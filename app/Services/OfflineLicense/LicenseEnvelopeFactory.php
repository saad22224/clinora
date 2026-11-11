<?php

namespace App\Services\OfflineLicense;

use Carbon\CarbonImmutable;
use InvalidArgumentException;

/**
 * Builds and verifies Clinora Offline license envelopes (format 1 / Ed25519).
 */
final class LicenseEnvelopeFactory
{
    public const SignatureContext = "CLINORA-LICENSE-V1\n";

    public const MaxFileBytes = 16_384;

    /**
     * @param  list<string|null>  $fingerprint
     * @param  list<string>  $features
     * @return array{envelope: string, payload: array<string, mixed>, sha256: string}
     */
    public function create(
        LicenseSigningKey $key,
        string $licenseId,
        string $clinicName,
        CarbonImmutable $issuedAt,
        CarbonImmutable $startsAt,
        CarbonImmutable $expiresAt,
        int $graceDays,
        int $maxDevices,
        ?int $maxDoctors,
        array $fingerprint,
        int $minMatch,
        array $features,
        int $transferSeq,
    ): array {
        if (! preg_match('/^[A-Za-z0-9._-]{1,64}$/', $licenseId)) {
            throw new InvalidArgumentException('رقم الترخيص غير صالح.');
        }

        if (mb_strlen($clinicName) < 1 || mb_strlen($clinicName) > 255) {
            throw new InvalidArgumentException('اسم العيادة غير صالح.');
        }

        if ($expiresAt->lessThanOrEqualTo($startsAt)) {
            throw new InvalidArgumentException('تاريخ الانتهاء يجب أن يكون بعد تاريخ البداية.');
        }

        if ($graceDays < 0 || $graceDays > 365) {
            throw new InvalidArgumentException('فترة السماح غير صالحة.');
        }

        if ($maxDevices < 0 || $maxDevices > 1000) {
            throw new InvalidArgumentException('حد الأجهزة غير صالح.');
        }

        if (count($fingerprint) !== 5) {
            throw new InvalidArgumentException('بصمة الجهاز يجب أن تحتوي 5 مكوّنات.');
        }

        $bound = collect($fingerprint)->filter()->count();

        if ($minMatch < 1 || $minMatch > $bound) {
            throw new InvalidArgumentException('قيمة min_match غير صالحة لبصمة الجهاز.');
        }

        $payload = [
            'format' => 1,
            'key_id' => $key->keyId,
            'license_id' => $licenseId,
            'clinic_name' => $clinicName,
            'edition' => 'offline',
            'issued_at' => $issuedAt->utc()->format('Y-m-d\TH:i:s\Z'),
            'starts_at' => $startsAt->utc()->format('Y-m-d\TH:i:s\Z'),
            'expires_at' => $expiresAt->utc()->format('Y-m-d\TH:i:s\Z'),
            'grace_days' => $graceDays,
            'max_devices' => $maxDevices,
            'max_doctors' => $maxDoctors,
            'server_fingerprint' => [
                'components' => array_values($fingerprint),
                'min_match' => $minMatch,
            ],
            'features' => array_values($features),
            'transfer_seq' => $transferSeq,
        ];

        $payloadJson = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        $signature = sodium_crypto_sign_detached(self::SignatureContext.$payloadJson, $key->secretKey);

        $envelope = json_encode([
            'format' => 1,
            'key_id' => $key->keyId,
            'payload' => base64_encode($payloadJson),
            'signature' => base64_encode($signature),
        ], JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);

        if (strlen($envelope) > self::MaxFileBytes) {
            throw new InvalidArgumentException('ملف الترخيص أكبر من الحد المسموح Offline (16KB).');
        }

        $this->assertValid($envelope, [$key->keyId => $key->publicKeyBase64()]);

        return [
            'envelope' => $envelope,
            'payload' => $payload,
            'sha256' => hash('sha256', $envelope),
        ];
    }

    /**
     * @param  array<string, string>  $publicKeys  key_id => base64 public key
     * @return array<string, mixed>
     */
    public function assertValid(string $contents, array $publicKeys): array
    {
        if (strlen($contents) > self::MaxFileBytes) {
            throw new InvalidArgumentException('ملف الترخيص أكبر من المتوقع.');
        }

        $envelope = json_decode($contents, true);

        if (! is_array($envelope) || ($envelope['format'] ?? null) !== 1
            || ! is_string($envelope['key_id'] ?? null)
            || ! is_string($envelope['payload'] ?? null)
            || ! is_string($envelope['signature'] ?? null)) {
            throw new InvalidArgumentException('غلاف الترخيص تالف.');
        }

        $encodedKey = $publicKeys[$envelope['key_id']] ?? null;
        $publicKey = is_string($encodedKey) ? base64_decode($encodedKey, true) : false;
        $payload = base64_decode($envelope['payload'], true);
        $signature = base64_decode($envelope['signature'], true);

        if ($publicKey === false || strlen($publicKey) !== SODIUM_CRYPTO_SIGN_PUBLICKEYBYTES) {
            throw new InvalidArgumentException('مفتاح التحقق غير معروف (key_id).');
        }

        if ($payload === false || $signature === false || strlen($signature) !== SODIUM_CRYPTO_SIGN_BYTES
            || ! sodium_crypto_sign_verify_detached($signature, self::SignatureContext.$payload, $publicKey)) {
            throw new InvalidArgumentException('فشل التحقق من توقيع الترخيص.');
        }

        $data = json_decode($payload, true);

        if (! is_array($data) || ($data['key_id'] ?? null) !== $envelope['key_id'] || ($data['edition'] ?? null) !== 'offline') {
            throw new InvalidArgumentException('حمولة الترخيص غير مكتملة.');
        }

        return $data;
    }
}
