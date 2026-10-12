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
use App\Models\Subscription;
use App\Models\User;
use App\Models\VisitType;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class QaDatasetBuilder
{
    /**
     * Independently specified expected finance for the populated standalone clinic.
     * Values are literals chosen at insert time — not derived from FinanceReportService.
     *
     * @var array{
     *     clinic_slug: string,
     *     collected_income: float,
     *     expenses: float,
     *     net_cash: float,
     *     completed_service_value: float,
     *     outstanding: float,
     *     notes: list<string>
     * }
     */
    public const EXPECTED_FINANCE_POPULATED = [
        'clinic_slug' => 'qa-populated-clinic',
        'collected_income' => 690.0,
        'expenses' => 75.0,
        'net_cash' => 615.0,
        'completed_service_value' => 900.0,
        'outstanding' => 60.0,
        'notes' => [
            'Five completed appointments priced 150+200+100+150+200=800 with finance incomes 100+200+80+150+120=650 (amounts intentionally differ from price where noted).',
            'One additional completed partial appointment priced 100 with amount_paid 40 and finance income 40 → outstanding 60.',
            'One expense of 75.',
            'Pending/cancelled/rejected appointments do not add completed_service_value.',
        ],
    ];

    /** @var array<string, list<int>> */
    private array $records = [
        'prescription_items' => [],
        'prescriptions' => [],
        'medical_files' => [],
        'patient_diagnoses' => [],
        'finances' => [],
        'appointments' => [],
        'clinic_notifications' => [],
        'visit_types' => [],
        'patients' => [],
        'organization_users' => [],
        'subscriptions' => [],
        'users' => [],
        'clinics' => [],
        'organizations' => [],
        'plans' => [],
        'drugs' => [],
    ];

    /** @var list<array<string, mixed>> */
    private array $accounts = [];

    /** @var list<string> */
    private array $privateFiles = [];

    /** @var list<string> */
    private array $scenarios = [];

    /** @var array<string, array<string, mixed>> */
    private array $expectedFinance = [];

    public function __construct(
        private QaAccountCatalog $catalog,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function build(string $runId, string $profile = 'standard', bool $writeFiles = true): array
    {
        $this->reset();
        $this->seedRolesAndPermissions();

        $accounts = $this->catalog->keyedAccounts();
        $filesDisk = (string) config('clinora.qa.files_disk', 'local');
        $filesPrefix = trim((string) config('clinora.qa.files_prefix', 'medical-records/qa'), '/').'/'.$runId;

        // Populated clinic always needs ≥5 patients for the fixed finance scenario.
        $patientCount = match ($profile) {
            'small' => 5,
            'edge' => 16,
            default => 10,
        };

        foreach (range(1, 8) as $i) {
            $drug = Drug::factory()->create([
                'commercial_name_en' => 'QA Drug '.$i,
            ]);
            $this->track('drugs', $drug->id);
        }

        $singlePlan = Plan::factory()->single()->create([
            'name' => 'QA Single',
            'price' => 499,
            'duration_in_days' => 365,
        ]);
        $this->track('plans', $singlePlan->id);

        $chainPlan = Plan::factory()->chain(5)->create([
            'name' => 'QA Chain',
            'price' => 1999,
            'duration_in_days' => 365,
        ]);
        $this->track('plans', $chainPlan->id);

        $qaSuperadmin = $this->createUser($accounts['qa_superadmin'], null);
        $qaSuperadmin->assignRole('superadmin');

        // --- Standalone populated clinic ---
        $populated = $this->createClinic('عيادة QA المأهولة', 'qa-populated-clinic', null, true);
        $this->createClinicSubscription($populated, $singlePlan, active: true);
        $this->populateClinicDetailed(
            clinic: $populated,
            ownerAccount: $accounts['populated_owner'],
            doctorAccount: $accounts['populated_doctor'],
            secretaryAccount: $accounts['populated_secretary'],
            restrictedSecretary: true,
            patientCount: $patientCount,
            writeFiles: $writeFiles,
            filesDisk: $filesDisk,
            filesPrefix: $filesPrefix,
            withFinanceScenario: true,
        );
        $this->scenarios[] = 'standalone_populated_active_subscription';

        // --- Empty standalone ---
        $empty = $this->createClinic('عيادة QA الفارغة', 'qa-empty-clinic', null, true);
        $this->createClinicSubscription($empty, $singlePlan, active: true);
        $this->createUser($accounts['empty_owner'], $empty->id)->assignRole('clinic_owner');
        $this->grantClinicPermissions('clinic_owner');
        $this->scenarios[] = 'standalone_empty_clinic';

        // --- Expired subscription ---
        $expired = $this->createClinic('عيادة QA منتهية', 'qa-expired-clinic', null, true);
        $this->createClinicSubscription($expired, $singlePlan, active: false, expired: true);
        $this->populateClinicSimple(
            $expired,
            $accounts['expired_owner'],
            $accounts['expired_doctor'],
            patientCount: 2,
            writeFiles: $writeFiles,
            filesDisk: $filesDisk,
            filesPrefix: $filesPrefix,
        );
        $this->scenarios[] = 'standalone_expired_subscription';

        // --- Inactive clinic (is_active=false) ---
        $inactive = $this->createClinic('عيادة QA غير نشطة', 'qa-inactive-clinic', null, false);
        $this->createClinicSubscription($inactive, $singlePlan, active: true);
        $this->createUser($accounts['inactive_owner'], $inactive->id)->assignRole('clinic_owner');
        $this->grantClinicPermissions('clinic_owner');
        $this->scenarios[] = 'standalone_inactive_clinic';

        // --- Cross-tenant peer ---
        $peer = $this->createClinic('عيادة QA النظيرة', 'qa-peer-clinic', null, true);
        $this->createClinicSubscription($peer, $singlePlan, active: true);
        $this->populateClinicSimple(
            $peer,
            $accounts['peer_owner'],
            $accounts['peer_doctor'],
            patientCount: 3,
            writeFiles: $writeFiles,
            filesDisk: $filesDisk,
            filesPrefix: $filesPrefix,
        );
        $this->scenarios[] = 'cross_tenant_peer_clinic';

        // --- Organization with inherited subscription ---
        $organization = Organization::factory()->create(['name' => 'سلسلة QA']);
        $this->track('organizations', $organization->id);

        $orgSub = Subscription::factory()
            ->forOrganization($organization)
            ->withPlan($chainPlan)
            ->active()
            ->create();
        $this->track('subscriptions', $orgSub->id);

        $orgOwner = $this->createUser($accounts['org_owner_a'], null);
        $membership = OrganizationUser::factory()->owner()->create([
            'organization_id' => $organization->id,
            'user_id' => $orgOwner->id,
        ]);
        $this->track('organization_users', $membership->id);

        $orgRestricted = $this->createUser($accounts['org_restricted'], null);
        $restrictedMembership = OrganizationUser::factory()->restricted()->create([
            'organization_id' => $organization->id,
            'user_id' => $orgRestricted->id,
        ]);
        $this->track('organization_users', $restrictedMembership->id);

        $branchA = $this->createClinic('فرع QA أ', 'qa-org-branch-a', $organization->id, true);
        $branchB = $this->createClinic('فرع QA ب', 'qa-org-branch-b', $organization->id, true);
        $branchEmpty = $this->createClinic('فرع QA فارغ', 'qa-org-branch-empty', $organization->id, true);

        $this->populateClinicSimple(
            $branchA,
            $accounts['branch_a_owner'],
            $accounts['branch_a_doctor'],
            patientCount: 4,
            writeFiles: $writeFiles,
            filesDisk: $filesDisk,
            filesPrefix: $filesPrefix,
        );
        $this->populateClinicSimple(
            $branchB,
            $accounts['branch_b_owner'],
            $accounts['branch_b_doctor'],
            patientCount: 3,
            writeFiles: $writeFiles,
            filesDisk: $filesDisk,
            filesPrefix: $filesPrefix,
        );

        $this->createUser($accounts['branch_empty_owner'], $branchEmpty->id)->assignRole('clinic_owner');
        $this->grantClinicPermissions('clinic_owner');

        $this->scenarios[] = 'organization_chain_inherited_subscription';
        $this->scenarios[] = 'organization_empty_branch';
        $this->scenarios[] = 'organization_restricted_admin';

        $this->expectedFinance['qa-populated-clinic'] = self::EXPECTED_FINANCE_POPULATED;

        return [
            'accounts' => $this->accounts,
            'records' => $this->records,
            'scenarios' => $this->scenarios,
            'expected_finance' => [
                'note' => 'Independently specified literals from QaDatasetBuilder::EXPECTED_FINANCE_POPULATED.',
                'totals' => $this->expectedFinance,
            ],
            'private_files' => $this->privateFiles,
            'meta' => [
                'phase' => '2C',
                'generator_version' => 2,
                'files_disk' => $filesDisk,
                'files_prefix' => $filesPrefix,
                'password' => QaAccountCatalog::PASSWORD,
            ],
        ];
    }

    private function reset(): void
    {
        foreach ($this->records as $key => $_) {
            $this->records[$key] = [];
        }
        $this->accounts = [];
        $this->privateFiles = [];
        $this->scenarios = [];
        $this->expectedFinance = [];
    }

    private function createClinic(string $name, string $slugBase, ?int $organizationId, bool $isActive): Clinic
    {
        $clinic = Clinic::factory()->create([
            'organization_id' => $organizationId,
            'name' => $name,
            'slug' => Clinic::uniqueSlug($slugBase),
            'is_active' => $isActive,
        ]);
        $this->track('clinics', $clinic->id);

        return $clinic;
    }

    private function createClinicSubscription(Clinic $clinic, Plan $plan, bool $active, bool $expired = false): void
    {
        $factory = Subscription::factory()->forClinic($clinic)->withPlan($plan);

        if ($expired) {
            $factory = $factory->expired();
        } elseif ($active) {
            $factory = $factory->active();
        } else {
            $factory = $factory->inactive();
        }

        $subscription = $factory->create();
        $this->track('subscriptions', $subscription->id);
    }

    /**
     * @param  array<string, mixed>  $account
     */
    private function createUser(array $account, ?int $clinicId): User
    {
        $user = User::factory()->create([
            'name' => $account['name'],
            'email' => $account['email'],
            'password' => QaAccountCatalog::PASSWORD,
            'role' => $account['role'],
            'clinic_id' => $clinicId,
        ]);
        $this->track('users', $user->id);

        if (! empty($account['spatie_role'])) {
            Role::findOrCreate($account['spatie_role'], 'web');
            $user->assignRole($account['spatie_role']);
        }

        $this->accounts[] = [
            'key' => $account['key'],
            'email' => $account['email'],
            'password' => QaAccountCatalog::PASSWORD,
            'name' => $account['name'],
            'role' => $account['role'],
            'spatie_role' => $account['spatie_role'],
            'clinic_id' => $clinicId,
            'context' => $account['context'],
            'notes' => $account['notes'],
            'user_id' => $user->id,
        ];

        return $user;
    }

    /**
     * @param  array<string, mixed>  $ownerAccount
     * @param  array<string, mixed>  $doctorAccount
     * @param  array<string, mixed>|null  $secretaryAccount
     */
    private function populateClinicDetailed(
        Clinic $clinic,
        array $ownerAccount,
        array $doctorAccount,
        ?array $secretaryAccount,
        bool $restrictedSecretary,
        int $patientCount,
        bool $writeFiles,
        string $filesDisk,
        string $filesPrefix,
        bool $withFinanceScenario,
    ): void {
        $owner = $this->createUser($ownerAccount, $clinic->id);
        $owner->assignRole('clinic_owner');
        $this->grantClinicPermissions('clinic_owner');

        $doctor = $this->createUser($doctorAccount, $clinic->id);
        $doctor->assignRole('doctor');
        $doctor->syncPermissions(Permission::where('guard_name', 'web')->pluck('name'));

        if ($secretaryAccount) {
            $secretary = $this->createUser($secretaryAccount, $clinic->id);
            $secretary->assignRole('secretary');
            if ($restrictedSecretary) {
                $secretary->syncPermissions([Permission::findOrCreate('manage_appointments', 'web')]);
            }
        }

        $visitTypes = $this->createVisitTypes($clinic);

        $patients = collect();
        for ($i = 0; $i < $patientCount; $i++) {
            $patient = Patient::factory()->forClinic($clinic)->create([
                'name' => 'مريض QA '.$clinic->slug.' #'.($i + 1),
                'phone' => '010'.str_pad((string) ($clinic->id * 100 + $i), 8, '0', STR_PAD_LEFT),
            ]);
            $this->track('patients', $patient->id);
            $patients->push($patient);

            $diagnosis = PatientDiagnosis::factory()->forPatient($patient)->create();
            $this->track('patient_diagnoses', $diagnosis->id);
        }

        if ($withFinanceScenario) {
            $this->seedPopulatedFinanceScenario($clinic, $doctor, $visitTypes, $patients->take(5)->values(), $writeFiles, $filesDisk, $filesPrefix);
        }

        // Extra patients beyond finance scenario get lighter clinical rows for list/pagination.
        foreach ($patients->slice(5) as $index => $patient) {
            $visitType = $visitTypes[$index % $visitTypes->count()];
            $appointment = Appointment::factory()
                ->forClinic($clinic)
                ->confirmed()
                ->fromReception()
                ->unpaid()
                ->create([
                    'patient_id' => $patient->id,
                    'doctor_id' => $doctor->id,
                    'visit_type_id' => $visitType->id,
                    'price' => $visitType->price,
                    'scheduled_at' => now()->addDays($index + 1),
                ]);
            $this->track('appointments', $appointment->id);
        }

        $notification = ClinicNotification::factory()->forClinic($clinic)->unread()->create([
            'title' => 'تنبيه QA',
            'message' => 'إشعار تجريبي للعيادة '.$clinic->name,
        ]);
        $this->track('clinic_notifications', $notification->id);
    }

    /**
     * @param  array<string, mixed>  $ownerAccount
     * @param  array<string, mixed>  $doctorAccount
     */
    private function populateClinicSimple(
        Clinic $clinic,
        array $ownerAccount,
        array $doctorAccount,
        int $patientCount,
        bool $writeFiles,
        string $filesDisk,
        string $filesPrefix,
    ): void {
        $owner = $this->createUser($ownerAccount, $clinic->id);
        $owner->assignRole('clinic_owner');
        $this->grantClinicPermissions('clinic_owner');

        $doctor = $this->createUser($doctorAccount, $clinic->id);
        $doctor->assignRole('doctor');
        $doctor->syncPermissions(Permission::where('guard_name', 'web')->pluck('name'));

        $visitTypes = $this->createVisitTypes($clinic);

        for ($i = 0; $i < $patientCount; $i++) {
            $patient = Patient::factory()->forClinic($clinic)->create([
                'name' => 'مريض QA '.$clinic->slug.' #'.($i + 1),
            ]);
            $this->track('patients', $patient->id);

            $diagnosis = PatientDiagnosis::factory()->forPatient($patient)->create();
            $this->track('patient_diagnoses', $diagnosis->id);

            $visitType = $visitTypes[$i % $visitTypes->count()];
            $appointment = Appointment::factory()
                ->forClinic($clinic)
                ->completed()
                ->fromReception()
                ->paid()
                ->create([
                    'patient_id' => $patient->id,
                    'doctor_id' => $doctor->id,
                    'visit_type_id' => $visitType->id,
                    'price' => $visitType->price,
                    'scheduled_at' => now()->subDays($i + 1),
                ]);
            $this->track('appointments', $appointment->id);

            $prescription = Prescription::factory()->forAppointment($appointment)->create();
            $this->track('prescriptions', $prescription->id);
            $items = PrescriptionItem::factory()->count(2)->forPrescription($prescription)->create();
            foreach ($items as $item) {
                $this->track('prescription_items', $item->id);
            }

            $file = $this->createMedicalFile($patient, $writeFiles, $filesDisk, $filesPrefix);
            $this->track('medical_files', $file->id);

            $finance = Finance::factory()
                ->forAppointment($appointment, (float) $appointment->amount_paid)
                ->create(['payment_method' => 'cash', 'transaction_date' => now()->toDateString()]);
            $this->track('finances', $finance->id);
        }

        $notification = ClinicNotification::factory()->forClinic($clinic)->create();
        $this->track('clinic_notifications', $notification->id);
    }

    /**
     * Fixed finance scenario matching EXPECTED_FINANCE_POPULATED.
     *
     * @param  \Illuminate\Support\Collection<int, Patient>  $patients
     * @param  \Illuminate\Support\Collection<int, VisitType>  $visitTypes
     */
    private function seedPopulatedFinanceScenario(
        Clinic $clinic,
        User $doctor,
        $visitTypes,
        $patients,
        bool $writeFiles,
        string $filesDisk,
        string $filesPrefix,
    ): void {
        $prices = [150.0, 200.0, 100.0, 150.0, 200.0];
        $collections = [100.0, 200.0, 80.0, 150.0, 120.0];

        foreach ($patients as $index => $patient) {
            $price = $prices[$index];
            $collected = $collections[$index];
            $visitType = $visitTypes->first(fn (VisitType $type) => (float) $type->price === $price)
                ?? $visitTypes->first();

            $appointment = Appointment::factory()
                ->forClinic($clinic)
                ->completed()
                ->fromReception()
                ->paid()
                ->create([
                    'patient_id' => $patient->id,
                    'doctor_id' => $doctor->id,
                    'visit_type_id' => $visitType->id,
                    'price' => $price,
                    'amount_paid' => $price,
                    'payment_status' => 'paid',
                    'scheduled_at' => now()->subDays(5 - $index)->setTime(10, 0),
                ]);
            $this->track('appointments', $appointment->id);

            $prescription = Prescription::factory()->forAppointment($appointment)->create();
            $this->track('prescriptions', $prescription->id);
            foreach (PrescriptionItem::factory()->count(2)->forPrescription($prescription)->create() as $item) {
                $this->track('prescription_items', $item->id);
            }

            $finance = Finance::factory()
                ->forAppointment($appointment, $collected)
                ->create([
                    'payment_method' => 'cash',
                    'transaction_date' => now()->toDateString(),
                    'description' => 'QA collection differs from price for apt #'.$appointment->id,
                ]);
            $this->track('finances', $finance->id);

            $file = $this->createMedicalFile($patient, $writeFiles, $filesDisk, $filesPrefix);
            $this->track('medical_files', $file->id);
        }

        $partialPatient = $patients->last();
        $partialVisit = $visitTypes->first(fn (VisitType $type) => (float) $type->price === 100.0)
            ?? $visitTypes->last();
        $partial = Appointment::factory()
            ->forClinic($clinic)
            ->completed()
            ->create([
                'patient_id' => $partialPatient->id,
                'doctor_id' => $doctor->id,
                'visit_type_id' => $partialVisit->id,
                'price' => 100,
                'amount_paid' => 40,
                'payment_status' => 'partial',
                'scheduled_at' => now()->subDay()->setTime(11, 0),
                'source' => 'reception_manual_entry',
            ]);
        $this->track('appointments', $partial->id);

        $partialFinance = Finance::factory()
            ->forAppointment($partial, 40.0)
            ->create([
                'payment_method' => 'card',
                'transaction_date' => now()->toDateString(),
            ]);
        $this->track('finances', $partialFinance->id);

        $pending = Appointment::factory()
            ->forClinic($clinic)
            ->pending()
            ->fromWebsite()
            ->unpaid()
            ->create([
                'patient_id' => $patients->first()->id,
                'doctor_id' => $doctor->id,
                'visit_type_id' => $visitTypes->first()->id,
                'price' => 150,
                'scheduled_at' => now()->addDays(2),
            ]);
        $this->track('appointments', $pending->id);

        $cancelled = Appointment::factory()
            ->forClinic($clinic)
            ->cancelled()
            ->fromReception()
            ->create([
                'patient_id' => $patients->get(1)->id,
                'doctor_id' => $doctor->id,
                'visit_type_id' => $visitTypes->get(1)->id,
                'price' => 200,
                'scheduled_at' => now()->subDays(2),
            ]);
        $this->track('appointments', $cancelled->id);

        $rejected = Appointment::factory()
            ->forClinic($clinic)
            ->rejected()
            ->fromWebsite()
            ->create([
                'patient_id' => $patients->get(2)->id,
                'doctor_id' => $doctor->id,
                'visit_type_id' => $visitTypes->get(2)->id,
                'price' => 100,
                'scheduled_at' => now()->subDays(3),
            ]);
        $this->track('appointments', $rejected->id);

        $expense = Finance::factory()
            ->forClinic($clinic)
            ->expense()
            ->create([
                'amount' => 75,
                'description' => 'مستلزمات عيادة QA',
                'transaction_date' => now()->toDateString(),
            ]);
        $this->track('finances', $expense->id);
    }

    /**
     * @return \Illuminate\Support\Collection<int, VisitType>
     */
    private function createVisitTypes(Clinic $clinic)
    {
        $definitions = [
            ['name' => 'كشف', 'price' => 150],
            ['name' => 'استشارة', 'price' => 200],
            ['name' => 'متابعة', 'price' => 100],
        ];

        $types = collect();
        foreach ($definitions as $definition) {
            $type = VisitType::factory()->forClinic($clinic)->create($definition);
            $this->track('visit_types', $type->id);
            $types->push($type);
        }

        return $types;
    }

    private function createMedicalFile(Patient $patient, bool $writeFiles, string $filesDisk, string $filesPrefix): MedicalFile
    {
        $relative = $filesPrefix.'/'.Str::uuid().'.pdf';

        if ($writeFiles) {
            Storage::disk($filesDisk)->put($relative, "%PDF-1.4\n%QA synthetic medical file\n");
            $this->privateFiles[] = $relative;
        }

        return MedicalFile::factory()->forPatient($patient)->document()->create([
            'file_path' => $relative,
        ]);
    }

    private function grantClinicPermissions(string $roleName): void
    {
        $role = Role::findByName($roleName, 'web');
        $role->syncPermissions(Permission::where('guard_name', 'web')->pluck('name'));
    }

    private function track(string $type, int $id): void
    {
        $this->records[$type][] = $id;
    }

    private function seedRolesAndPermissions(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (['superadmin', 'clinic_owner', 'doctor', 'secretary'] as $role) {
            Role::findOrCreate($role, 'web');
        }

        foreach ([
            'manage_appointments',
            'manage_patients',
            'manage_prescriptions',
            'manage_records',
            'manage_billing',
            'manage_settings',
        ] as $permission) {
            Permission::findOrCreate($permission, 'web');
        }
    }
}
