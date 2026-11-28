<?php

use App\Models\Clinic;
use App\Models\Patient;
use App\Models\Plan;
use App\Models\Prescription;
use App\Models\PrescriptionTemplate;
use App\Models\Scopes\TenantScope;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Visit;
use App\Support\PrescriptionPrint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

function rxClinic(): array
{
    Role::firstOrCreate(['name' => 'clinic_owner', 'guard_name' => 'web']);

    $clinic = Clinic::create([
        'name' => 'عيادة الروشتات',
        'slug' => 'rx-clinic-'.uniqid(),
        'is_active' => true,
        'doctor_title' => 'د.',
    ]);

    $plan = Plan::create([
        'name' => 'single',
        'type' => 'single',
        'price' => 0,
        'duration_in_days' => 30,
        'max_clinics' => 1,
        'is_active' => true,
    ]);

    Subscription::create([
        'clinic_id' => $clinic->id,
        'plan_id' => $plan->id,
        'plan' => $plan->name,
        'starts_at' => now(),
        'ends_at' => now()->addDays(30),
        'is_active' => true,
    ]);

    $owner = User::factory()->create([
        'name' => 'د. أحمد المالك',
        'role' => 'owner',
        'clinic_id' => $clinic->id,
    ]);
    $owner->assignRole('clinic_owner');

    $patient = Patient::create([
        'clinic_id' => $clinic->id,
        'name' => 'مريض روشتة',
        'phone' => '01099998888',
    ]);

    $visit = Visit::create([
        'clinic_id' => $clinic->id,
        'patient_id' => $patient->id,
        'visited_at' => now(),
        'status' => 'open',
    ]);

    return compact('clinic', 'owner', 'patient', 'visit');
}

test('clinic medication templates are created and copied into prescriptions without live linkage', function () {
    ['clinic' => $clinic, 'owner' => $owner, 'patient' => $patient, 'visit' => $visit] = rxClinic();

    $this->actingAs($owner)
        ->postJson(route('clinic.prescription-templates.store'), [
            'name' => 'قالب ضغط',
            'items' => [
                ['medicine_name' => 'Amlodipine 5mg', 'dosage' => '1 tablet daily', 'instructions' => 'Morning'],
            ],
        ])
        ->assertCreated();

    $template = PrescriptionTemplate::withoutGlobalScope(TenantScope::class)->sole();

    $response = $this->actingAs($owner)
        ->post(route('clinic.prescriptions.store'), [
            'patient_id' => $patient->id,
            'visit_id' => $visit->id,
            'notes' => 'ملاحظات خاصة بالروشتة',
            'items' => [
                ['medicine_name' => 'Amlodipine 5mg', 'dosage' => '1 tablet daily', 'instructions' => 'Morning'],
            ],
        ]);

    $prescription = Prescription::withoutGlobalScope(TenantScope::class)->sole();
    $response->assertRedirect(route('clinic.prescriptions.print', $prescription));

    expect($prescription->visit_id)->toBe($visit->id)
        ->and($prescription->items()->sole()->medicine_name)->toBe('Amlodipine 5mg');

    $template->items()->first()->update(['medicine_name' => 'CHANGED TEMPLATE']);
    expect($prescription->fresh()->items()->sole()->medicine_name)->toBe('Amlodipine 5mg');
});

test('prescription cannot be created without a visit', function () {
    ['owner' => $owner, 'patient' => $patient] = rxClinic();

    $this->actingAs($owner)
        ->post(route('clinic.prescriptions.store'), [
            'patient_id' => $patient->id,
            'notes' => 'بدون زيارة',
            'items' => [
                ['medicine_name' => 'Augmentin 1g', 'dosage' => '1 tab', 'instructions' => null],
            ],
        ])
        ->assertSessionHasErrors('visit_id');

    expect(Prescription::withoutGlobalScope(TenantScope::class)->count())->toBe(0);
});

test('templates are isolated between clinics', function () {
    ['clinic' => $clinicA, 'owner' => $ownerA] = rxClinic();
    ['clinic' => $clinicB, 'owner' => $ownerB] = rxClinic();

    $this->actingAs($ownerA)
        ->postJson(route('clinic.prescription-templates.store'), [
            'name' => 'قالب أ',
            'items' => [['medicine_name' => 'Drug A', 'dosage' => null, 'instructions' => null]],
        ])
        ->assertCreated();

    $template = PrescriptionTemplate::withoutGlobalScope(TenantScope::class)
        ->where('clinic_id', $clinicA->id)
        ->sole();

    $this->actingAs($ownerB)
        ->delete(route('clinic.prescription-templates.destroy', $template))
        ->assertNotFound();

    expect(PrescriptionTemplate::withoutGlobalScope(TenantScope::class)->whereKey($template->id)->exists())->toBeTrue();
});

test('prescription print layouts and locales render and use clinic owner doctor name', function () {
    ['clinic' => $clinic, 'owner' => $owner, 'patient' => $patient, 'visit' => $visit] = rxClinic();

    $clinic->update([
        'prescription_layout' => 'modern',
        'prescription_print_locale' => 'en',
    ]);

    $this->actingAs($owner)
        ->post(route('clinic.prescriptions.store'), [
            'patient_id' => $patient->id,
            'visit_id' => $visit->id,
            'items' => [['medicine_name' => 'Paracetamol', 'dosage' => '500mg', 'instructions' => 'PRN']],
        ])
        ->assertRedirect();

    $prescription = Prescription::withoutGlobalScope(TenantScope::class)->sole();

    $this->actingAs($owner)
        ->get(route('clinic.prescriptions.print', $prescription))
        ->assertOk()
        ->assertSee('Patient', false)
        ->assertSee('د. أحمد المالك', false)
        ->assertSee('Paracetamol', false);

    foreach (PrescriptionPrint::layouts() as $layout) {
        $this->actingAs($owner)
            ->get(route('clinic.prescriptions.print-preview', ['layout' => $layout, 'locale' => 'ar']))
            ->assertOk()
            ->assertSee('معاينة تجريبية', false)
            ->assertSee('Amoxicillin 500mg', false)
            ->assertDontSee('أموكسيسيلين', false);
    }
});

test('clinic settings can update prescription layout and print locale', function () {
    ['clinic' => $clinic, 'owner' => $owner] = rxClinic();

    $this->actingAs($owner)
        ->put(route('clinic.settings.update'), [
            'name' => $clinic->name,
            'prescription_layout' => 'elegant',
            'prescription_print_locale' => 'en',
        ])
        ->assertRedirect(route('clinic.settings.index'));

    expect($clinic->fresh()->prescription_layout)->toBe('elegant')
        ->and($clinic->fresh()->prescription_print_locale)->toBe('en');
});

test('prescriptions sidebar remains browseable and lists visit linked prescriptions', function () {
    ['owner' => $owner, 'patient' => $patient, 'visit' => $visit] = rxClinic();

    $this->actingAs($owner)
        ->post(route('clinic.prescriptions.store'), [
            'patient_id' => $patient->id,
            'visit_id' => $visit->id,
            'items' => [['medicine_name' => 'Augmentin 1g', 'dosage' => '1g BID', 'instructions' => '7 days']],
        ])
        ->assertRedirect();

    $this->actingAs($owner)
        ->get(route('clinic.prescriptions.index'))
        ->assertOk()
        ->assertSee('Augmentin 1g', false)
        ->assertSee('قوالب الأدوية', false)
        ->assertSee('إضافة قالب', false)
        ->assertDontSee('كتابة وصفة', false);
});
