<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\MedicalFile;
use App\Models\Patient;
use App\Models\Scopes\TenantScope;
use App\Models\Subscription;
use App\Support\Qa\QaDatasetBuilder;
use App\Support\Qa\QaEnvironmentGuard;
use App\Support\Qa\QaManifest;
use App\Services\FinanceReportService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Throwable;

class QaValidateCommand extends Command
{
    protected $signature = 'clinora:qa-validate
                            {--run= : Manifest run id (defaults to latest)}
                            {--dry-run : Alias for read-only validation}';

    protected $description = 'Validate QA dataset tenant alignment, org inheritance, and finance expectations';

    public function handle(
        QaEnvironmentGuard $guard,
        QaManifest $manifest,
        FinanceReportService $financeReport,
    ): int {
        try {
            $guard->assertSafeForDryRun();
            $guard->assertIntegrationsRemainDisabled();

            $runId = $this->option('run') ?: $manifest->latestRunId();
            if (! is_string($runId) || $runId === '') {
                $this->error('No QA manifest run available. Generate a dataset first.');

                return self::FAILURE;
            }

            $data = $manifest->read($runId);
            $errors = [];

            $clinicIds = $data['records']['clinics'] ?? [];
            foreach ($clinicIds as $clinicId) {
                $clinic = Clinic::withTrashed()->find($clinicId);
                if (! $clinic) {
                    $errors[] = "Missing clinic id {$clinicId}";
                }
            }

            foreach ($data['records']['patients'] ?? [] as $patientId) {
                $patient = Patient::withoutGlobalScope(TenantScope::class)->find($patientId);
                if (! $patient) {
                    $errors[] = "Missing patient {$patientId}";

                    continue;
                }
                if (! in_array($patient->clinic_id, $clinicIds, true)) {
                    $errors[] = "Patient {$patientId} clinic_id not in QA clinics";
                }
            }

            foreach ($data['records']['appointments'] ?? [] as $appointmentId) {
                $appointment = Appointment::withoutGlobalScope(TenantScope::class)->find($appointmentId);
                if (! $appointment) {
                    $errors[] = "Missing appointment {$appointmentId}";

                    continue;
                }
                $patient = Patient::withoutGlobalScope(TenantScope::class)->find($appointment->patient_id);
                if ($patient && (int) $patient->clinic_id !== (int) $appointment->clinic_id) {
                    $errors[] = "Appointment {$appointmentId} patient/clinic mismatch";
                }
            }

            $orgIds = $data['records']['organizations'] ?? [];
            foreach ($orgIds as $orgId) {
                $branchIds = Clinic::query()->where('organization_id', $orgId)->pluck('id');
                if (Subscription::query()->whereIn('clinic_id', $branchIds)->exists()) {
                    $errors[] = "Organization {$orgId} branches have clinic-level subscriptions";
                }
                if (! Subscription::query()->where('organization_id', $orgId)->whereNull('clinic_id')->exists()) {
                    $errors[] = "Organization {$orgId} missing organization-scoped subscription";
                }
            }

            $populated = Clinic::query()->where('slug', 'like', 'qa-populated-clinic%')->first();
            if ($populated) {
                $expected = QaDatasetBuilder::EXPECTED_FINANCE_POPULATED;
                $metrics = $financeReport->forClinic(
                    $populated,
                    Carbon::now()->startOfMonth(),
                    Carbon::now()->endOfDay()
                );

                foreach (['collected_income', 'expenses', 'net_cash', 'completed_service_value', 'outstanding'] as $key) {
                    if (round((float) $metrics[$key], 2) !== round((float) $expected[$key], 2)) {
                        $errors[] = "Finance {$key} expected {$expected[$key]} got {$metrics[$key]}";
                    }
                }
            } else {
                $errors[] = 'Populated QA clinic not found';
            }

            foreach ($data['private_files'] ?? [] as $path) {
                $file = MedicalFile::withoutGlobalScope(TenantScope::class)
                    ->where('file_path', $path)
                    ->first();
                if (! $file) {
                    $errors[] = "Manifest private file path missing in DB: {$path}";
                }
            }

            if ($errors !== []) {
                foreach ($errors as $error) {
                    $this->error($error);
                }

                return self::FAILURE;
            }

            $this->info("QA validate passed for run [{$runId}]");

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}
