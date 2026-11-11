<?php

namespace App\Services\OfflineLicense;

use InvalidArgumentException;

/**
 * Compatible with Clinora Offline LicenseRequestCode (CLNREQ1 / CLNREL1).
 * Base32 of JSON + first 4 bytes of SHA-256(JSON); no schema inventing.
 */
final class LicenseRequestCodec
{
    public const ActivationPrefix = 'CLNREQ1';

    public const ReleasePrefix = 'CLNREL1';

    private const Alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * @param  array<string, mixed>  $request
     */
    public static function encode(array $request, string $prefix = self::ActivationPrefix): string
    {
        $json = json_encode($request, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);

        return $prefix.'-'.implode('-', str_split(self::base32($json.substr(hash('sha256', $json, true), 0, 4)), 5));
    }

    /**
     * @return array<string, mixed>
     */
    public static function decode(string $code, string $prefix = self::ActivationPrefix): array
    {
        $code = strtoupper((string) preg_replace('/[\s-]+/', '', $code));

        if (! str_starts_with($code, $prefix)) {
            throw new InvalidArgumentException(
                $prefix === self::ActivationPrefix
                    ? 'رمز الطلب يجب أن يبدأ بـ CLNREQ1.'
                    : 'رمز النقل يجب أن يبدأ بـ CLNREL1.'
            );
        }

        $bytes = self::unbase32(substr($code, strlen($prefix)));
        $json = substr($bytes, 0, -4);

        if (strlen($bytes) < 5 || ! hash_equals(substr(hash('sha256', $json, true), 0, 4), substr($bytes, -4))) {
            throw new InvalidArgumentException('رمز الطلب غير مكتمل أو نُسخ بخطأ (فشل التحقق من المجموع).');
        }

        $request = json_decode($json, true);

        if (! is_array($request)) {
            throw new InvalidArgumentException('محتوى رمز الطلب تالف.');
        }

        return $request;
    }

    /**
     * @return array{
     *     v: int,
     *     license_id: ?string,
     *     clinic: ?string,
     *     fingerprint: list<string|null>,
     *     install_public_key: string,
     *     version: ?string,
     *     high_watermark: ?string,
     *     generated_at: ?string
     * }
     */
    public static function decodeActivation(string $code): array
    {
        $request = self::decode($code, self::ActivationPrefix);

        if (($request['v'] ?? null) !== 1) {
            throw new InvalidArgumentException('إصدار رمز الطلب غير مدعوم.');
        }

        $fingerprint = $request['fingerprint'] ?? null;

        if (! is_array($fingerprint) || ! array_is_list($fingerprint) || count($fingerprint) !== 5) {
            throw new InvalidArgumentException('بصمة الجهاز ناقصة أو غير صالحة في رمز الطلب.');
        }

        foreach ($fingerprint as $component) {
            if ($component !== null && (! is_string($component) || preg_match('/^[0-9a-f]{32}$/', $component) !== 1)) {
                throw new InvalidArgumentException('أحد مكوّنات بصمة الجهاز غير صالح.');
            }
        }

        if (collect($fingerprint)->filter()->isEmpty()) {
            throw new InvalidArgumentException('بصمة الجهاز فارغة بالكامل؛ لا يمكن إصدار ترخيص مرتبط.');
        }

        $installPublicKey = $request['install_public_key'] ?? null;

        if (! is_string($installPublicKey) || base64_decode($installPublicKey, true) === false
            || strlen((string) base64_decode($installPublicKey, true)) !== SODIUM_CRYPTO_SIGN_PUBLICKEYBYTES) {
            throw new InvalidArgumentException('مفتاح التثبيت العام ناقص أو غير صالح في رمز الطلب.');
        }

        return [
            'v' => 1,
            'license_id' => is_string($request['license_id'] ?? null) ? $request['license_id'] : null,
            'clinic' => is_string($request['clinic'] ?? null) ? $request['clinic'] : null,
            'fingerprint' => array_values($fingerprint),
            'install_public_key' => $installPublicKey,
            'version' => is_string($request['version'] ?? null) ? $request['version'] : null,
            'high_watermark' => is_string($request['high_watermark'] ?? null) ? $request['high_watermark'] : null,
            'generated_at' => is_string($request['generated_at'] ?? null) ? $request['generated_at'] : null,
        ];
    }

    private static function base32(string $bytes): string
    {
        $bits = '';

        foreach (str_split($bytes) as $byte) {
            $bits .= str_pad(decbin(ord($byte)), 8, '0', STR_PAD_LEFT);
        }

        $encoded = '';

        foreach (str_split($bits, 5) as $chunk) {
            $encoded .= self::Alphabet[bindec(str_pad($chunk, 5, '0'))];
        }

        return $encoded;
    }

    private static function unbase32(string $text): string
    {
        $bits = '';

        foreach (str_split($text) as $character) {
            $value = strpos(self::Alphabet, $character);

            if ($value === false) {
                throw new InvalidArgumentException('رمز الطلب يحتوي أحرفًا غير صالحة.');
            }

            $bits .= str_pad(decbin($value), 5, '0', STR_PAD_LEFT);
        }

        $padding = strlen($bits) % 8;

        if ($padding >= 5 || ($padding > 0 && substr($bits, -$padding) !== str_repeat('0', $padding))) {
            throw new InvalidArgumentException('رمز الطلب غير مكتمل أو نُسخ بخطأ.');
        }

        $bytes = '';

        foreach (str_split($bits, 8) as $chunk) {
            if (strlen($chunk) === 8) {
                $bytes .= chr(bindec($chunk));
            }
        }

        return $bytes;
    }
}
