<?php

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\ClinicNotification;
use App\Models\Marketer;
use App\Models\MedicalFile;
use App\Models\Organization;
use App\Models\OrganizationUser;
use App\Models\Patient;
use App\Models\Plan;
use App\Models\Scopes\TenantScope;
use App\Models\Subscription;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\VisitType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    foreach (['superadmin', 'clinic_owner', 'doctor', 'secretary'] as $role) {
        Role::findOrCreate($role, 'web');
    }

    foreach ([
        'manage_appointments',
        'use_staff_messages',
        'manage_patients',
        'manage_prescriptions',
        'manage_records',
        'manage_billing',
        'manage_settings',
    ] as $permission) {
        Permission::findOrCreate($permission, 'web');
    }
});

function createActiveClinicWithOwner(string $name = 'عيادة النور', string $slug = 'al-noor'): array
{
    $plan = Plan::factory()->single()->create();
    $clinic = Clinic::factory()->create([
        'name' => $name,
        'slug' => $slug,
        'is_active' => true,
    ]);
    Subscription::factory()->forClinic($clinic)->withPlan($plan)->active()->create();

    $owner = User::factory()->clinicOwner()->forClinic($clinic)->create();
    $owner->syncPermissions(Permission::where('guard_name', 'web')->pluck('name'));

    return [$clinic, $owner, $plan];
}

test('organization dashboard and reports count all branches accurately even after entering a single branch', function () {
    $chainPlan = Plan::factory()->chain(5)->create();
    $organization = Organization::factory()->create(['name' => 'سلسلة الشفاء']);
    Subscription::factory()->forOrganization($organization)->withPlan($chainPlan)->active()->create();

    $orgOwner = User::factory()->create(['role' => 'owner', 'clinic_id' => null]);
    OrganizationUser::factory()->owner()->create([
        'organization_id' => $organization->id,
        'user_id' => $orgOwner->id,
    ]);

    $branchA = Clinic::factory()->forOrganization($organization)->create(['name' => 'فرع أ', 'slug' => 'branch-a']);
    $branchB = Clinic::factory()->forOrganization($organization)->create(['name' => 'فرع ب', 'slug' => 'branch-b']);

    Patient::factory()->count(2)->forClinic($branchA)->create();
    Patient::factory()->count(3)->forClinic($branchB)->create();

    // Enter Branch A (sets session active_clinic_id = branchA->id)
    $this->actingAs($orgOwner)
        ->post(route('organization.clinics.enter', [$organization, $branchA]))
        ->assertRedirect(route('clinic.dashboard'));

    expect(session('active_clinic_id'))->toBe($branchA->id);

    // Now visit Organization Reports directly without explicitly calling leave-clinic
    $response = $this->actingAs($orgOwner)
        ->get(route('organization.reports.index', $organization))
        ->assertOk();

    $totals = $response->viewData('totals');
    $branches = $response->viewData('branches');
    expect($totals['patients_count'])->toBe(5);

    $branchBMetrics = collect($branches)->firstWhere('clinic_id', $branchB->id);
    expect($branchBMetrics['patients_count'])->toBe(3);
});

test('organization owner can use staff messages and view appointments in branch clinic without branch manager', function () {
    $chainPlan = Plan::factory()->chain(5)->create();
    $organization = Organization::factory()->create(['name' => 'سلسلة الأمل']);
    Subscription::factory()->forOrganization($organization)->withPlan($chainPlan)->active()->create();

    $orgOwner = User::factory()->create(['role' => 'owner', 'clinic_id' => null]);
    OrganizationUser::factory()->owner()->create([
        'organization_id' => $organization->id,
        'user_id' => $orgOwner->id,
    ]);

    $branch = Clinic::factory()->forOrganization($organization)->create([
        'name' => 'فرع بدون مدير',
        'slug' => 'branch-no-manager',
    ]);

    $this->actingAs($orgOwner)
        ->post(route('organization.clinics.enter', [$organization, $branch]))
        ->assertRedirect(route('clinic.dashboard'));

    // Appointments page does not crash with 500 when branch has no direct user rows
    $this->actingAs($orgOwner)
        ->get(route('clinic.appointments.index'))
        ->assertOk();

    // Staff messages index & store work for organization owner in branch context
    $this->actingAs($orgOwner)
        ->getJson(route('clinic.messages.index'))
        ->assertOk();

    $this->actingAs($orgOwner)
        ->postJson(route('clinic.messages.store'), ['body' => 'مرحباً فريق الفرع'])
        ->assertCreated()
        ->assertJsonPath('message.body', 'مرحباً فريق الفرع');
});

test('inactive clinic is redirected to subscription expired on clinic routes', function () {
    [$clinic, $owner] = createActiveClinicWithOwner('عيادة معطلة', 'inactive-clinic-test');

    $clinic->update(['is_active' => false]);

    $this->actingAs($owner)
        ->get(route('clinic.dashboard'))
        ->assertRedirect(route('subscription.expired'));
});

test('cross-clinic notification markRead is forbidden', function () {
    [$clinicA, $ownerA] = createActiveClinicWithOwner('عيادة أ', 'clinic-notif-a');
    [$clinicB] = createActiveClinicWithOwner('عيادة ب', 'clinic-notif-b');

    $notificationB = ClinicNotification::factory()->forClinic($clinicB)->unread()->create();

    $this->actingAs($ownerA)
        ->post(route('clinic.notifications.read', $notificationB))
        ->assertForbidden();

    expect($notificationB->fresh()->is_read)->toBeFalse();
});

test('doctor cannot modify or delete clinic owner account via staff controller', function () {
    [$clinic, $owner] = createActiveClinicWithOwner('عيادة الحماية', 'clinic-staff-guard');

    $doctor = User::factory()->doctor()->forClinic($clinic)->create();
    $doctor->assignRole('doctor');

    $this->actingAs($doctor)
        ->put(route('clinic.staff.update', $owner), [
            'name' => 'محاولة اختراق',
            'email' => 'hacked@example.com',
        ])
        ->assertForbidden();

    $this->actingAs($doctor)
        ->delete(route('clinic.staff.destroy', $owner))
        ->assertForbidden();

    expect($owner->fresh()->email)->not->toBe('hacked@example.com');
});

test('secretary creating appointment assigns clinic doctor and appears in day queue', function () {
    [$clinic, $owner] = createActiveClinicWithOwner('عيادة الدور', 'clinic-queue-sec');

    $doctor = User::factory()->doctor()->forClinic($clinic)->create();
    $doctor->assignRole('doctor');

    $secretary = User::factory()->secretary()->forClinic($clinic)->create();
    $secretary->assignRole('secretary');
    $secretary->givePermissionTo('manage_appointments');

    $patient = Patient::factory()->forClinic($clinic)->create();
    $visitType = VisitType::factory()->forClinic($clinic)->create(['price' => 250]);

    $this->actingAs($secretary)
        ->post(route('clinic.appointments.store'), [
            'patient_id' => $patient->id,
            'visit_type_id' => $visitType->id,
            'scheduled_at' => now()->toDateString(),
            'notes' => 'حجز من الاستقبال',
        ])
        ->assertRedirect(route('clinic.appointments.index'));

    $appointment = Appointment::withoutGlobalScope(TenantScope::class)
        ->where('clinic_id', $clinic->id)
        ->first();

    expect($appointment)->not->toBeNull()
        ->and($appointment->doctor_id)->toBe($doctor->id)
        ->and($appointment->queue_number)->toBe(1);
});

test('public booking while authenticated in another clinic reuses existing patient in target clinic', function () {
    [$clinicA, $ownerA] = createActiveClinicWithOwner('عيادة أ', 'public-book-a');
    [$clinicB] = createActiveClinicWithOwner('عيادة ب', 'public-book-b');

    $visitTypeB = VisitType::factory()->forClinic($clinicB)->create(['price' => 150]);
    $existingPatientB = Patient::withoutGlobalScope(TenantScope::class)->create([
        'clinic_id' => $clinicB->id,
        'name' => 'مريض مسجل مسبقاً',
        'phone' => '01099887766',
    ]);

    $this->actingAs($ownerA)
        ->post(route('clinic.public.book', $clinicB->slug), [
            'patient_name' => 'مريض مسجل مسبقاً',
            'patient_phone' => '01099887766',
            'visit_type_id' => $visitTypeB->id,
            'preferred_date' => now()->addDay()->toDateString(),
        ])
        ->assertRedirect();

    $count = Patient::withoutGlobalScope(TenantScope::class)
        ->where('clinic_id', $clinicB->id)
        ->where('phone', '01099887766')
        ->count();

    expect($count)->toBe(1)
        ->and($existingPatientB->fresh()->id)->toBe($existingPatientB->id);
});

test('patient AI chat validates history attaches private local medical files and handles mocked Gemini responses and errors', function () {
    Storage::fake('local');

    [$clinic, $owner] = createActiveClinicWithOwner('عيادة الذكاء', 'clinic-ai-test');
    [$otherClinic, $otherOwner] = createActiveClinicWithOwner('عيادة أخرى', 'clinic-ai-other');

    $patient = Patient::factory()->forClinic($clinic)->create([
        'name' => 'مريض اختباري للتلخيص',
        'medical_history' => 'ارتفاع ضغط الدم والسكري من النوع الثاني',
    ]);

    $pdfPath = 'medical-records/test-lab.pdf';
    Storage::disk('local')->put($pdfPath, "%PDF-1.4\n%Synthetic lab result\n");

    MedicalFile::factory()->forPatient($patient)->create([
        'clinic_id' => $clinic->id,
        'type' => 'تحليل صورة دم كاملة',
        'file_path' => $pdfPath,
    ]);

    // 1. When AI feature flag is disabled -> 404
    config(['clinora.features.ai_enabled' => false]);
    $this->actingAs($owner)
        ->postJson(route('clinic.patients.ai-chat', $patient), ['message' => 'لخص الحالة'])
        ->assertNotFound();

    // Enable AI feature flag + set mocked key in config
    config([
        'clinora.features.ai_enabled' => true,
        'clinora.gemini_api_keys' => ['fake-gemini-key-for-testing'],
    ]);
    SystemSetting::set('ai_enabled', '1');

    // 2. Cross-clinic user cannot access patient AI chat (404 due to TenantScope)
    $this->actingAs($otherOwner)
        ->postJson(route('clinic.patients.ai-chat', $patient), ['message' => 'لخص الحالة'])
        ->assertNotFound();

    // 3. Malformed history payload is rejected with 422
    $this->actingAs($owner)
        ->postJson(route('clinic.patients.ai-chat', $patient), [
            'message' => 'لخص الحالة',
            'history' => [['invalid_key' => 'bad']],
        ])
        ->assertStatus(422);

    // 4. Successful mocked Gemini response with private local file attached, then 429 on second call
    $capturedRequest = null;
    $callCount = 0;
    Http::fake(function ($request) use (&$capturedRequest, &$callCount) {
        $callCount++;
        $capturedRequest = $request;

        if ($callCount === 1) {
            return Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'ملخص طبي: المريض يعاني من ارتفاع ضغط الدم والسكري مع وجود تحليل مرفق.'],
                            ],
                        ],
                    ],
                ],
            ], 200);
        }

        return Http::response(['error' => 'quota exceeded'], 429);
    });

    $this->actingAs($owner)
        ->postJson(route('clinic.patients.ai-chat', $patient), [
            'message' => 'لخص حالة المريض والمرفقات',
            'history' => [
                ['role' => 'user', 'message' => 'مرحباً'],
                ['role' => 'model', 'message' => 'أهلاً دكتور'],
            ],
        ])
        ->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'ملخص طبي: المريض يعاني من ارتفاع ضغط الدم والسكري مع وجود تحليل مرفق.',
        ]);

    expect($capturedRequest)->not->toBeNull();
    $payload = $capturedRequest->data();
    $lastContentParts = $payload['contents'][2]['parts'] ?? [];
    expect(collect($lastContentParts)->contains(fn ($part) => isset($part['inlineData']['data'])))->toBeTrue();

    // 5. Upstream Gemini failure returns safe fallback message without leaking key
    $fallbackResponse = $this->actingAs($owner)
        ->postJson(route('clinic.patients.ai-chat', $patient), [
            'message' => 'لخص الحالة مجدداً',
        ])
        ->assertOk()
        ->json('message');

    expect($fallbackResponse)->toContain('المساعد الذكي يشهد ضغطاً عالياً')
        ->and($fallbackResponse)->not->toContain('fake-gemini-key-for-testing');
});

test('clinic settings rejects invalid theme color hex and billing rejects mismatched appointment patient', function () {
    [$clinic, $owner] = createActiveClinicWithOwner('عيادة الإعدادات', 'clinic-settings-test');

    $this->actingAs($owner)
        ->put(route('clinic.settings.update'), [
            'name' => 'عيادة الإعدادات',
            'theme_color' => 'red;}</style><script>',
        ])
        ->assertSessionHasErrors('theme_color');

    $this->actingAs($owner)
        ->put(route('clinic.settings.update'), [
            'name' => 'عيادة الإعدادات',
            'theme_color' => '#2563EB',
        ])
        ->assertSessionHasNoErrors();

    $patientA = Patient::factory()->forClinic($clinic)->create();
    $patientB = Patient::factory()->forClinic($clinic)->create();
    $appointmentB = Appointment::factory()->forClinic($clinic)->completed()->create([
        'patient_id' => $patientB->id,
        'doctor_id' => $owner->id,
    ]);

    $this->actingAs($owner)
        ->post(route('clinic.billing.store'), [
            'type' => 'income',
            'amount' => 150,
            'description' => 'كشف',
            'transaction_date' => now()->toDateString(),
            'patient_id' => $patientA->id,
            'appointment_id' => $appointmentB->id,
        ])
        ->assertStatus(422);
});

test('public landing login and registration views render cleanly without debug styles or fabricated testimonials', function () {
    $marketer = Marketer::factory()->create();
    expect($marketer->referral_code)->not->toBeEmpty();

    $landing = $this->get(route('home'))->assertOk()->getContent();
    expect($landing)
        ->toContain('id="about"')
        ->toContain('مصممة لتناسب كل دور داخل منشأتك الطبية')
        ->not->toContain('lh3.googleusercontent.com/aida-public')
        ->not->toContain('موثوق به من أكثر من 500');

    $login = $this->get(route('login'))->assertOk()->getContent();
    expect($login)
        ->not->toContain('background-color: red')
        ->toContain('id="toggle-login-password"');

    $register = $this->get(route('register', ['ref' => $marketer->referral_code]))->assertOk()->getContent();
    expect($register)
        ->not->toContain('background-color: red')
        ->toContain('id="toggle-register-password"')
        ->toContain($marketer->referral_code);
});
