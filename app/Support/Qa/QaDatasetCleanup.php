<?php

namespace App\Support\Qa;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\ClinicNotification;
use App\Models\Drug;
use App\Models\Finance;
use App\Models\MedicalFile;
use App\Models\Organization;
use App\Models\OrganizationUser;
use App\Models\Patient;
use App\Models\PatientDiagnosis;
use App\Models\Plan;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Scopes\TenantScope;
use App\Models\Subscription;
use App\Models\User;
use App\Models\VisitType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class QaDatasetCleanup
{
    public function __construct(
        private QaManifest $manifest,
        private QaAccountCatalog $catalog,
    ) {}

    /**
     * @return array{deleted: array<string, int>, files_deleted: int}
     */
    public function cleanupRun(string $runId, bool $dryRun = false): array
    {
        $data = $this->manifest->read($runId);
        $records = is_array($data['records'] ?? null) ? $data['records'] : [];
        $privateFiles = is_array($data['private_files'] ?? null) ? $data['private_files'] : [];
        $disk = (string) ($data['meta']['files_disk'] ?? config('clinora.qa.files_disk', 'local'));

        $counts = [
            'prescription_items' => 0,
            'prescriptions' => 0,
            'medical_files' => 0,
            'patient_diagnoses' => 0,
            'finances' => 0,
            'appointments' => 0,
            'clinic_notifications' => 0,
            'visit_types' => 0,
            'patients' => 0,
            'organization_users' => 0,
            'subscriptions' => 0,
            'users' => 0,
            'clinics' => 0,
            'organizations' => 0,
            'plans' => 0,
            'drugs' => 0,
        ];

        if ($dryRun) {
            foreach ($counts as $key => $_) {
                $ids = $records[$key] ?? [];
                $counts[$key] = is_array($ids) ? count($ids) : 0;
            }

            return [
                'deleted' => $counts,
                'files_deleted' => count($privateFiles),
            ];
        }

        return DB::transaction(function () use ($records, $privateFiles, $disk, $counts, $runId) {
            $counts['prescription_items'] = $this->deleteByIds(PrescriptionItem::class, $records['prescription_items'] ?? []);
            $counts['prescriptions'] = $this->deleteByIds(Prescription::class, $records['prescriptions'] ?? [], true);
            $counts['medical_files'] = $this->deleteByIds(MedicalFile::class, $records['medical_files'] ?? [], true);
            $counts['patient_diagnoses'] = $this->deleteByIds(PatientDiagnosis::class, $records['patient_diagnoses'] ?? [], true);
            $counts['finances'] = $this->deleteByIds(Finance::class, $records['finances'] ?? [], true);
            $counts['appointments'] = $this->deleteByIds(Appointment::class, $records['appointments'] ?? [], true);
            $counts['clinic_notifications'] = $this->deleteByIds(ClinicNotification::class, $records['clinic_notifications'] ?? []);
            $counts['visit_types'] = $this->deleteByIds(VisitType::class, $records['visit_types'] ?? []);
            $counts['patients'] = $this->deleteByIds(Patient::class, $records['patients'] ?? [], true);
            $counts['organization_users'] = $this->deleteByIds(OrganizationUser::class, $records['organization_users'] ?? []);
            $counts['subscriptions'] = $this->deleteByIds(Subscription::class, $records['subscriptions'] ?? []);
            $counts['users'] = $this->deleteUsers($records['users'] ?? []);
            $counts['clinics'] = $this->deleteClinics($records['clinics'] ?? []);
            $counts['organizations'] = $this->deleteByIds(Organization::class, $records['organizations'] ?? []);
            $counts['plans'] = $this->deletePlans($records['plans'] ?? []);
            $counts['drugs'] = $this->deleteByIds(Drug::class, $records['drugs'] ?? []);

            $filesDeleted = 0;
            foreach ($privateFiles as $path) {
                if (is_string($path) && Storage::disk($disk)->exists($path)) {
                    Storage::disk($disk)->delete($path);
                    $filesDeleted++;
                }
            }

            $prefix = trim((string) config('clinora.qa.files_prefix', 'medical-records/qa'), '/').'/'.$runId;
            if (Storage::disk($disk)->exists($prefix)) {
                Storage::disk($disk)->deleteDirectory($prefix);
            }

            $manifestPath = $this->manifest->pathFor($runId);
            if (is_file($manifestPath)) {
                unlink($manifestPath);
            }

            return [
                'deleted' => $counts,
                'files_deleted' => $filesDeleted,
            ];
        });
    }

    /**
     * Remove orphan QA users/clinics matching catalog emails / qa- slugs when no manifest is available.
     *
     * @return array{users: int, clinics: int}
     */
    public function cleanupOrphansByCatalog(bool $dryRun = false): array
    {
        $emails = $this->catalog->emails();
        $users = User::query()->whereIn('email', $emails)->get();
        $clinicIds = Clinic::withTrashed()
            ->where(function ($query) {
                $query->where('slug', 'like', QaAccountCatalog::SLUG_PREFIX.'%');
            })
            ->pluck('id');

        if ($dryRun) {
            return [
                'users' => $users->count(),
                'clinics' => $clinicIds->count(),
            ];
        }

        if ($users->isEmpty() && $clinicIds->isEmpty()) {
            return ['users' => 0, 'clinics' => 0];
        }

        throw new RuntimeException(
            'QA orphan records matching catalog emails/slugs exist without a usable manifest. '.
            'Provide --run=<run_id> from storage/app/qa-manifests, or delete the orphan QA rows manually in clinora_qa only.'
        );
    }

    /**
     * @param  class-string  $model
     * @param  list<int|string>  $ids
     */
    private function deleteByIds(string $model, array $ids, bool $tenantScoped = false): int
    {
        $ids = array_values(array_filter($ids, fn ($id) => is_numeric($id)));

        if ($ids === []) {
            return 0;
        }

        $query = $model::query()->whereIn('id', $ids);

        if ($tenantScoped && method_exists($model, 'withoutGlobalScope')) {
            $query->withoutGlobalScope(TenantScope::class);
        }

        return $query->delete();
    }

    /**
     * @param  list<int|string>  $ids
     */
    private function deleteUsers(array $ids): int
    {
        $ids = array_values(array_filter($ids, fn ($id) => is_numeric($id)));
        if ($ids === []) {
            return 0;
        }

        $users = User::query()->whereIn('id', $ids)->get();
        $deleted = 0;

        foreach ($users as $user) {
            // Never touch a non-catalog email even if an id leaked into the manifest.
            if (! in_array($user->email, $this->catalog->emails(), true)) {
                continue;
            }

            $user->roles()->detach();
            $user->permissions()->detach();
            $user->delete();
            $deleted++;
        }

        return $deleted;
    }

    /**
     * @param  list<int|string>  $ids
     */
    private function deleteClinics(array $ids): int
    {
        $ids = array_values(array_filter($ids, fn ($id) => is_numeric($id)));
        if ($ids === []) {
            return 0;
        }

        return Clinic::withTrashed()
            ->whereIn('id', $ids)
            ->where('slug', 'like', QaAccountCatalog::SLUG_PREFIX.'%')
            ->forceDelete();
    }

    /**
     * @param  list<int|string>  $ids
     */
    private function deletePlans(array $ids): int
    {
        $ids = array_values(array_filter($ids, fn ($id) => is_numeric($id)));
        if ($ids === []) {
            return 0;
        }

        return Plan::query()
            ->whereIn('id', $ids)
            ->where('name', 'like', 'QA %')
            ->delete();
    }
}
