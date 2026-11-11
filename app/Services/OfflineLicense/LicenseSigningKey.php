<?php

namespace App\Services\OfflineLicense;

use RuntimeException;

/**
 * Loads the company Ed25519 private key for Offline license signing.
 * Private material never enters the database, views, or API responses.
 */
final class LicenseSigningKey
{
    public function __construct(
        public readonly string $keyId,
        public readonly string $secretKey,
        public readonly string $publicKey,
        public readonly bool $isProduction,
    ) {}

    public function publicKeyBase64(): string
    {
        return base64_encode($this->publicKey);
    }

    public static function fromConfig(): self
    {
        $keyId = (string) config('clinora.offline_license.key_id', '');
        $path = (string) config('clinora.offline_license.private_key_path', '');
        $isProduction = (bool) config('clinora.offline_license.production', true);

        if ($keyId === '' || $path === '') {
            throw new RuntimeException('signing_key_missing');
        }

        if (! is_file($path) || ! is_readable($path)) {
            throw new RuntimeException('signing_key_unreadable');
        }

        $raw = trim((string) file_get_contents($path));

        if ($raw === '') {
            throw new RuntimeException('signing_key_empty');
        }

        $decoded = base64_decode($raw, true);

        if ($decoded === false) {
            throw new RuntimeException('signing_key_invalid');
        }

        // File stores a 32-byte Ed25519 seed (base64). A 64-byte secret key is also accepted.
        $seed = match (strlen($decoded)) {
            SODIUM_CRYPTO_SIGN_SEEDBYTES => $decoded,
            SODIUM_CRYPTO_SIGN_SECRETKEYBYTES => substr($decoded, 0, SODIUM_CRYPTO_SIGN_SEEDBYTES),
            default => null,
        };

        if ($seed === null) {
            throw new RuntimeException('signing_key_invalid');
        }

        $keypair = sodium_crypto_sign_seed_keypair($seed);

        return new self(
            keyId: $keyId,
            secretKey: sodium_crypto_sign_secretkey($keypair),
            publicKey: sodium_crypto_sign_publickey($keypair),
            isProduction: $isProduction && $keyId !== 'interop-test',
        );
    }

    /**
     * @return array{configured: bool, key_id: ?string, public_key: ?string, is_production: bool, message: string}
     */
    public static function status(): array
    {
        try {
            $key = self::fromConfig();

            return [
                'configured' => true,
                'key_id' => $key->keyId,
                'public_key' => $key->publicKeyBase64(),
                'is_production' => $key->isProduction,
                'message' => $key->isProduction
                    ? 'مفتاح توقيع الإنتاج جاهز.'
                    : 'مفتاح اختبار فقط — لا تصدر به تراخيص عيادات حقيقية.',
            ];
        } catch (RuntimeException $exception) {
            return [
                'configured' => false,
                'key_id' => config('clinora.offline_license.key_id') ?: null,
                'public_key' => null,
                'is_production' => false,
                'message' => match ($exception->getMessage()) {
                    'signing_key_missing' => 'لم يُضبط مفتاح التوقيع. عيّن CLINORA_OFFLINE_LICENSE_KEY_ID ومسار المفتاح الخاص.',
                    'signing_key_unreadable' => 'ملف المفتاح الخاص غير موجود أو غير قابل للقراءة.',
                    'signing_key_empty', 'signing_key_invalid' => 'ملف المفتاح الخاص تالف أو بصيغة غير متوقعة.',
                    default => 'تعذر تحميل مفتاح التوقيع.',
                },
            ];
        }
    }

    /**
     * @return array{key_id: string, public_key: string, private_key_path: string}
     */
    public static function generateToFile(string $path, string $keyId): array
    {
        if (is_file($path)) {
            throw new RuntimeException('لن يتم الكتابة فوق مفتاح موجود: '.$path);
        }

        $directory = dirname($path);

        if (! is_dir($directory) && ! mkdir($directory, 0700, true) && ! is_dir($directory)) {
            throw new RuntimeException('تعذر إنشاء مجلد المفتاح.');
        }

        $keypair = sodium_crypto_sign_keypair();
        $seed = sodium_crypto_sign_secretkey($keypair);
        $seed = substr($seed, 0, SODIUM_CRYPTO_SIGN_SEEDBYTES);
        $public = sodium_crypto_sign_publickey($keypair);

        if (file_put_contents($path, base64_encode($seed)."\n", LOCK_EX) === false) {
            throw new RuntimeException('تعذر حفظ المفتاح الخاص.');
        }

        chmod($path, 0600);

        return [
            'key_id' => $keyId,
            'public_key' => base64_encode($public),
            'private_key_path' => $path,
        ];
    }
}
