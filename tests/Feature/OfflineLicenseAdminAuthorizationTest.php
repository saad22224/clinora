<?php

use App\Models\IssuedOfflineLicense;
use App\Models\User;
use App\Services\OfflineLicense\LicenseRequestCodec;
use App\Services\OfflineLicense\LicenseSigningKey;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

function adminUser(): User
{
    Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
    $admin = User::factory()->create(['role' => 'superadmin']);
    $admin->assignRole('superadmin');

    return $admin;
}

function prepareSigningKey(): void
{
    $dir = storage_path('framework/testing/offline-admin-key-'.getmypid());
    if (! is_dir($dir)) {
        mkdir($dir, 0700, true);
    }
    $path = $dir.'/lic-test-admin.seed';
    if (! is_file($path)) {
        LicenseSigningKey::generateToFile($path, 'lic-test-admin');
    }
    config([
        'clinora.offline_license.key_id' => 'lic-test-admin',
        'clinora.offline_license.private_key_path' => $path,
        'clinora.offline_license.production' => false,
    ]);
}

test('super admin can open offline license pages', function () {
    $this->actingAs(adminUser())
        ->get(route('admin.offline-licenses.index'))
        ->assertOk();

    $this->actingAs(adminUser())
        ->get(route('admin.offline-licenses.create'))
        ->assertOk();

    $this->actingAs(adminUser())
        ->get(route('admin.offline-licenses.keys'))
        ->assertOk();
});

test('clinic owner cannot access offline license generator', function () {
    $owner = User::factory()->create(['role' => 'owner']);

    $this->actingAs($owner)->get(route('admin.offline-licenses.index'))->assertForbidden();
    $this->actingAs($owner)->get(route('admin.offline-licenses.create'))->assertForbidden();
    $this->actingAs($owner)->post(route('admin.offline-licenses.preview'), [
        'request_code' => 'CLNREQ1-AAAAA',
    ])->assertForbidden();
});

test('guest cannot access offline license routes', function () {
    $this->get(route('admin.offline-licenses.index'))->assertRedirect();
});

test('super admin can decode a request and issue a downloadable license', function () {
    prepareSigningKey();
    $admin = adminUser();

    $installKeyPair = sodium_crypto_sign_keypair();
    $request = [
        'v' => 1,
        'license_id' => null,
        'clinic' => 'عيادة لوحة الإدارة',
        'fingerprint' => [
            'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
            'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb',
            'cccccccccccccccccccccccccccccccc',
            null,
            'dddddddddddddddddddddddddddddddd',
        ],
        'install_public_key' => base64_encode(sodium_crypto_sign_publickey($installKeyPair)),
        'version' => '1.0.0',
        'high_watermark' => null,
        'generated_at' => now()->toIso8601String(),
    ];
    $code = LicenseRequestCodec::encode($request);

    $this->actingAs($admin)
        ->post(route('admin.offline-licenses.preview'), ['request_code' => $code])
        ->assertOk()
        ->assertSee('بيانات من رمز الطلب', false);

    $this->actingAs($admin)
        ->withSession(['offline_license_request' => LicenseRequestCodec::decodeActivation($code)])
        ->post(route('admin.offline-licenses.store'), [
            'clinic_name' => 'عيادة لوحة الإدارة',
            'customer_name' => 'عميل',
            'license_id' => 'CLN-ADMIN-UI-1',
            'starts_at' => now()->toDateString(),
            'expires_at' => now()->addYear()->toDateString(),
            'grace_days' => 30,
            'max_devices' => 5,
            'min_match' => 3,
            'transfer_seq' => 0,
            'confirm' => '1',
        ])
        ->assertRedirect();

    $license = IssuedOfflineLicense::query()->where('license_id', 'CLN-ADMIN-UI-1')->first();
    expect($license)->not->toBeNull();

    $this->actingAs($admin)
        ->get(route('admin.offline-licenses.download', $license))
        ->assertOk()
        ->assertHeader('content-disposition');
});
