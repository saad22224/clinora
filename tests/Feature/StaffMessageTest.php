<?php

use App\Models\Clinic;
use App\Models\Plan;
use App\Models\StaffMessage;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

function seedMessagingClinic(): array
{
    Role::firstOrCreate(['name' => 'clinic_owner', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'doctor', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'secretary', 'guard_name' => 'web']);
    Permission::findOrCreate('use_staff_messages', 'web');

    $clinic = Clinic::factory()->create(['is_active' => true]);
    $plan = Plan::factory()->single()->create();
    Subscription::factory()->forClinic($clinic)->withPlan($plan)->active()->create();

    $doctor = User::factory()->doctor()->forClinic($clinic)->create();
    $secretary = User::factory()->secretary()->forClinic($clinic)->create();
    $secretary->givePermissionTo('use_staff_messages');

    $otherClinic = Clinic::factory()->create(['is_active' => true]);
    Subscription::factory()->forClinic($otherClinic)->withPlan($plan)->active()->create();
    $outsider = User::factory()->doctor()->forClinic($otherClinic)->create();

    return compact('clinic', 'doctor', 'secretary', 'otherClinic', 'outsider');
}

test('authorized doctor can send and retrieve clinic messages', function () {
    ['clinic' => $clinic, 'doctor' => $doctor, 'secretary' => $secretary] = seedMessagingClinic();

    $this->actingAs($doctor)->postJson(route('clinic.messages.store'), [
        'body' => 'أدخل المريض التالي.',
    ])->assertCreated()
        ->assertJsonPath('message.body', 'أدخل المريض التالي.');

    $this->actingAs($secretary)->getJson(route('clinic.messages.index'))
        ->assertOk()
        ->assertJsonPath('messages.0.body', 'أدخل المريض التالي.');

    expect(StaffMessage::where('clinic_id', $clinic->id)->count())->toBe(1);
});

test('secretary without staff messages permission cannot access messaging', function () {
    ['clinic' => $clinic] = seedMessagingClinic();

    $restricted = User::factory()->secretary()->forClinic($clinic)->create();
    $restricted->assignRole('secretary');

    $this->actingAs($restricted)->getJson(route('clinic.messages.index'))->assertForbidden();
    $this->actingAs($restricted)->postJson(route('clinic.messages.store'), [
        'body' => 'رسالة محظورة',
    ])->assertForbidden();
});

test('unrelated clinic staff cannot read another clinic messages', function () {
    ['doctor' => $doctor, 'outsider' => $outsider] = seedMessagingClinic();

    $this->actingAs($doctor)->postJson(route('clinic.messages.store'), [
        'body' => 'رسالة داخلية',
    ])->assertCreated();

    $this->actingAs($outsider)->getJson(route('clinic.messages.index'))
        ->assertOk()
        ->assertJsonPath('messages', []);
});

test('invalid message content is rejected', function () {
    ['doctor' => $doctor] = seedMessagingClinic();

    $this->actingAs($doctor)
        ->postJson(route('clinic.messages.store'), ['body' => str_repeat('x', 501)])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['body']);

    $this->actingAs($doctor)
        ->postJson(route('clinic.messages.store'), [])
        ->assertUnprocessable()
        ->assertJsonPath('errors.body.0', 'نص الرسالة مطلوب.');
});

test('polling with since_id returns only newer messages', function () {
    ['doctor' => $doctor, 'secretary' => $secretary] = seedMessagingClinic();

    $this->actingAs($doctor)->postJson(route('clinic.messages.store'), ['body' => 'الأولى'])->assertCreated();
    $firstId = StaffMessage::query()->value('id');

    $this->actingAs($doctor)->postJson(route('clinic.messages.store'), ['body' => 'الثانية'])->assertCreated();

    $this->actingAs($secretary)->getJson(route('clinic.messages.index', ['since_id' => $firstId]))
        ->assertOk()
        ->assertJsonCount(1, 'messages')
        ->assertJsonPath('messages.0.body', 'الثانية');
});
