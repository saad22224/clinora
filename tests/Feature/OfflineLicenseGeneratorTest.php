<?php

use App\Models\IssuedOfflineLicense;
use App\Models\User;
use App\Services\OfflineLicense\LicenseEnvelopeFactory;
use App\Services\OfflineLicense\LicenseRequestCodec;
use App\Services\OfflineLicense\LicenseSigningKey;
use App\Services\OfflineLicense\OfflineLicenseIssuer;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Process;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

function offlineLicenseTestKey(string $directory, string $keyId = 'lic-test-online'): LicenseSigningKey
{
    if (! is_dir($directory)) {
        mkdir($directory, 0700, true);
    }

    $path = $directory.DIRECTORY_SEPARATOR.$keyId.'.seed';
    LicenseSigningKey::generateToFile($path, $keyId);

    config([
        'clinora.offline_license.key_id' => $keyId,
        'clinora.offline_license.private_key_path' => $path,
        'clinora.offline_license.production' => false,
    ]);

    return LicenseSigningKey::fromConfig();
}

function sampleActivationRequest(array $overrides = []): array
{
    return array_replace([
        'v' => 1,
        'license_id' => null,
        'clinic' => 'عيادة الاختبار',
        'fingerprint' => [
            'afb20b607e5cd61c470796e30df15305',
            'b5a1308666beef1b103a80921e997b90',
            '76ec5401438b9de311ef5f6c3103512d',
            '04c8178f67eb94912e3ec6dc28f4780a',
            'e8f00b7e8502f9b924f2b9829b21567d',
        ],
        'install_public_key' => base64_encode(str_repeat("\1", SODIUM_CRYPTO_SIGN_PUBLICKEYBYTES)),
        'version' => '1.0.0',
        'high_watermark' => null,
        'generated_at' => now()->toIso8601String(),
    ], $overrides);
}

beforeEach(function () {
    $this->keyDir = storage_path('framework/testing/offline-license-'.getmypid());
    $this->signingKey = offlineLicenseTestKey($this->keyDir);
});

afterEach(function () {
    if (isset($this->keyDir) && is_dir($this->keyDir)) {
        foreach (glob($this->keyDir.DIRECTORY_SEPARATOR.'*') ?: [] as $file) {
            @unlink($file);
        }
        @rmdir($this->keyDir);
    }
});

test('valid CLNREQ1 request decodes and invalid checksum is refused', function () {
    $code = LicenseRequestCodec::encode(sampleActivationRequest());
    $decoded = LicenseRequestCodec::decodeActivation($code);

    expect($decoded['v'])->toBe(1)
        ->and($decoded['fingerprint'])->toHaveCount(5)
        ->and($decoded['install_public_key'])->not->toBeEmpty();

    $tampered = substr($code, 0, -2).'AA';

    expect(fn () => LicenseRequestCodec::decodeActivation($tampered))
        ->toThrow(InvalidArgumentException::class);
});

test('malformed and unsupported requests are refused', function (string $code) {
    expect(fn () => LicenseRequestCodec::decodeActivation($code))
        ->toThrow(InvalidArgumentException::class);
})->with([
    'wrong prefix' => 'CLNXXX-AAAAA',
    'invalid base32' => 'CLNREQ1-!!!!!',
    'empty fingerprint' => fn () => LicenseRequestCodec::encode(sampleActivationRequest([
        'fingerprint' => [null, null, null, null, null],
    ])),
]);

test('issuer signs a license that verifies and stores history', function () {
    $issuer = app(OfflineLicenseIssuer::class);
    $admin = User::factory()->create(['role' => 'superadmin']);

    $record = $issuer->issue([
        'clinic_name' => 'عيادة الاختبار',
        'customer_name' => 'عميل تجريبي',
        'starts_at' => CarbonImmutable::now('UTC')->subDay(),
        'expires_at' => CarbonImmutable::now('UTC')->addYear(),
        'grace_days' => 30,
        'max_devices' => 5,
        'min_match' => 3,
        'transfer_seq' => 0,
        'require_production' => false,
    ], sampleActivationRequest(), $admin);

    expect($record->license_id)->toStartWith('CLN-')
        ->and($record->envelope_json)->toContain('"format":1')
        ->and($record->key_id)->toBe($this->signingKey->keyId);

    $payload = app(LicenseEnvelopeFactory::class)->assertValid(
        $record->envelope_json,
        [$this->signingKey->keyId => $this->signingKey->publicKeyBase64()]
    );

    expect($payload['edition'])->toBe('offline')
        ->and($payload['server_fingerprint']['components'])->toBe(sampleActivationRequest()['fingerprint']);

    $tampered = json_decode($record->envelope_json, true);
    $bytes = base64_decode($tampered['payload']);
    $bytes[10] = chr(ord($bytes[10]) ^ 0x01);
    $tampered['payload'] = base64_encode($bytes);

    expect(fn () => app(LicenseEnvelopeFactory::class)->assertValid(
        json_encode($tampered),
        [$this->signingKey->keyId => $this->signingKey->publicKeyBase64()]
    ))->toThrow(InvalidArgumentException::class);
});

test('missing production key blocks production issuance', function () {
    config([
        'clinora.offline_license.key_id' => null,
        'clinora.offline_license.private_key_path' => null,
        'clinora.offline_license.production' => true,
    ]);

    expect(fn () => app(OfflineLicenseIssuer::class)->issue([
        'clinic_name' => 'عيادة',
        'starts_at' => now(),
        'expires_at' => now()->addYear(),
        'require_production' => true,
    ], sampleActivationRequest()))->toThrow(RuntimeException::class);
});

test('renewal keeps previous record and refuses older expiry for same license id', function () {
    $issuer = app(OfflineLicenseIssuer::class);
    $request = sampleActivationRequest();

    $first = $issuer->issue([
        'clinic_name' => 'عيادة',
        'license_id' => 'CLN-RENEW-001',
        'starts_at' => CarbonImmutable::parse('2026-01-01'),
        'expires_at' => CarbonImmutable::parse('2027-01-01'),
        'require_production' => false,
    ], $request);

    expect(fn () => $issuer->issue([
        'clinic_name' => 'عيادة',
        'license_id' => 'CLN-RENEW-001',
        'starts_at' => CarbonImmutable::parse('2026-01-01'),
        'expires_at' => CarbonImmutable::parse('2026-06-01'),
        'require_production' => false,
    ], $request, null, $first))->toThrow(InvalidArgumentException::class);

    $second = $issuer->issue([
        'clinic_name' => 'عيادة',
        'license_id' => 'CLN-RENEW-001',
        'starts_at' => CarbonImmutable::parse('2027-01-01'),
        'expires_at' => CarbonImmutable::parse('2028-01-01'),
        'require_production' => false,
    ], $request, null, $first);

    expect($first->fresh()->lifecycle_status)->toBe('replaced')
        ->and($second->renewed_from_id)->toBe($first->id)
        ->and(IssuedOfflineLicense::query()->count())->toBe(2);
});

test('generated envelope is accepted by Clinora Offline LicenseVerifier', function () {
    $offlineRoot = 'C:\\laragon\\www\\clinora_offline';

    if (! is_file($offlineRoot.'\\vendor\\autoload.php')) {
        $this->markTestSkipped('Offline project vendor not available for interop.');
    }

    $issuer = app(OfflineLicenseIssuer::class);
    $record = $issuer->issue([
        'clinic_name' => 'عيادة التوافق',
        'license_id' => 'CLN-INTEROP-ONLINE-1',
        'starts_at' => CarbonImmutable::now('UTC')->subDay(),
        'expires_at' => CarbonImmutable::now('UTC')->addYear(),
        'grace_days' => 30,
        'max_devices' => 5,
        'min_match' => 3,
        'require_production' => false,
    ], sampleActivationRequest());

    $licenseFile = $this->keyDir.DIRECTORY_SEPARATOR.'interop.json';
    file_put_contents($licenseFile, $record->envelope_json);

    $script = <<<'PHP'
<?php
require $argv[1] . '/vendor/autoload.php';
use App\Services\License\LicenseVerifier;
$verifier = new LicenseVerifier([$argv[2] => $argv[3]]);
$license = $verifier->verify(file_get_contents($argv[4]));
echo $license->licenseId . '|' . $license->clinicName;
PHP;

    $scriptFile = $this->keyDir.DIRECTORY_SEPARATOR.'offline-verify.php';
    file_put_contents($scriptFile, $script);

    $result = Process::run([
        PHP_BINARY,
        $scriptFile,
        $offlineRoot,
        $this->signingKey->keyId,
        $this->signingKey->publicKeyBase64(),
        $licenseFile,
    ]);

    expect($result->successful())->toBeTrue($result->errorOutput().$result->output())
        ->and($result->output())->toContain('CLN-INTEROP-ONLINE-1')
        ->and($result->output())->toContain('عيادة التوافق');
});
