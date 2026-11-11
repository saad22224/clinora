<?php

namespace App\Services\OfflineLicense;

use App\Models\AuditLog;
use App\Models\IssuedOfflineLicense;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

final class OfflineLicenseIssuer
{
    public function __construct(private readonly LicenseEnvelopeFactory $envelopes) {}

    /**
     * @param  array{
     *     clinic_name: string,
     *     customer_name?: ?string,
     *     license_id?: ?string,
     *     starts_at: CarbonImmutable|string,
     *     expires_at: CarbonImmutable|string,
     *     grace_days?: int,
     *     max_devices?: int,
     *     max_doctors?: ?int,
     *     min_match?: int,
     *     transfer_seq?: int,
     *     features?: list<string>,
     *     require_production?: bool
     * }  $input
     * @param  array{
     *     v: int,
     *     license_id: ?string,
     *     clinic: ?string,
     *     fingerprint: list<string|null>,
     *     install_public_key: string,
     *     version: ?string,
     *     high_watermark: ?string,
     *     generated_at: ?string
     * }  $request
     */
    public function issue(array $input, array $request, ?User $admin = null, ?IssuedOfflineLicense $renewedFrom = null): IssuedOfflineLicense
    {
        $key = LicenseSigningKey::fromConfig();

        if (($input['require_production'] ?? true) && ! $key->isProduction) {
            throw new RuntimeException('لا يمكن إصدار ترخيص إنتاج بمفتاح اختبار. اضبط مفتاح الإنتاج أولًا.');
        }

        $startsAt = CarbonImmutable::parse($input['starts_at'])->utc();
        $expiresAt = CarbonImmutable::parse($input['expires_at'])->utc();
        $issuedAt = CarbonImmutable::now('UTC');
        $graceDays = (int) ($input['grace_days'] ?? 30);
        $maxDevices = (int) ($input['max_devices'] ?? 5);
        $maxDoctors = array_key_exists('max_doctors', $input) ? $input['max_doctors'] : null;
        $minMatch = (int) ($input['min_match'] ?? min(3, collect($request['fingerprint'])->filter()->count()));
        $transferSeq = (int) ($input['transfer_seq'] ?? 0);
        $features = $input['features'] ?? ['core'];
        $clinicName = trim((string) $input['clinic_name']);
        $customerName = isset($input['customer_name']) ? trim((string) $input['customer_name']) : null;
        $licenseId = trim((string) ($input['license_id'] ?? '')) ?: $this->generateLicenseId();

        if ($renewedFrom !== null && $transferSeq < $renewedFrom->transfer_seq) {
            throw new InvalidArgumentException('يجب ألا يقل transfer_seq عن الترخيص السابق.');
        }

        if ($renewedFrom !== null
            && $licenseId === $renewedFrom->license_id
            && $expiresAt->lessThan($renewedFrom->expires_at)) {
            throw new InvalidArgumentException('تجديد بنفس رقم الترخيص يتطلب تاريخ انتهاء أحدث (قاعدة older_license في Offline).');
        }

        $built = $this->envelopes->create(
            key: $key,
            licenseId: $licenseId,
            clinicName: $clinicName,
            issuedAt: $issuedAt,
            startsAt: $startsAt,
            expiresAt: $expiresAt,
            graceDays: $graceDays,
            maxDevices: $maxDevices,
            maxDoctors: $maxDoctors,
            fingerprint: $request['fingerprint'],
            minMatch: $minMatch,
            features: $features,
            transferSeq: $transferSeq,
        );

        return DB::transaction(function () use ($built, $key, $clinicName, $customerName, $issuedAt, $startsAt, $expiresAt, $graceDays, $maxDevices, $maxDoctors, $minMatch, $transferSeq, $features, $request, $admin, $renewedFrom, $licenseId) {
            if ($renewedFrom !== null) {
                $renewedFrom->update(['lifecycle_status' => 'replaced']);
            }

            $record = IssuedOfflineLicense::query()->create([
                'license_id' => $licenseId,
                'clinic_name' => $clinicName,
                'customer_name' => $customerName !== '' ? $customerName : null,
                'key_id' => $key->keyId,
                'fingerprint_components' => $request['fingerprint'],
                'fingerprint_digest' => hash('sha256', json_encode($request['fingerprint'], JSON_THROW_ON_ERROR)),
                'min_match' => $minMatch,
                'install_public_key' => $request['install_public_key'],
                'license_issued_at' => $issuedAt,
                'starts_at' => $startsAt,
                'expires_at' => $expiresAt,
                'grace_days' => $graceDays,
                'max_devices' => $maxDevices,
                'max_doctors' => $maxDoctors,
                'transfer_seq' => $transferSeq,
                'features' => $features,
                'envelope_json' => $built['envelope'],
                'envelope_sha256' => $built['sha256'],
                'lifecycle_status' => 'issued',
                'renewed_from_id' => $renewedFrom?->id,
                'issued_by' => $admin?->id,
                'request_meta' => [
                    'v' => $request['v'],
                    'request_license_id' => $request['license_id'],
                    'request_clinic' => $request['clinic'],
                    'version' => $request['version'],
                    'high_watermark' => $request['high_watermark'],
                    'generated_at' => $request['generated_at'],
                ],
            ]);

            AuditLog::record($renewedFrom ? 'admin.offline_license_renewed' : 'admin.offline_license_issued', $record, [
                'license_id' => $record->license_id,
                'key_id' => $record->key_id,
                'clinic_name' => $record->clinic_name,
                'expires_at' => $record->expires_at->toIso8601String(),
                'renewed_from' => $renewedFrom?->license_id,
                'fingerprint_digest' => $record->fingerprint_digest,
            ]);

            return $record;
        });
    }

    public function generateLicenseId(): string
    {
        do {
            $candidate = 'CLN-'.now('UTC')->format('Ymd').'-'.Str::upper(Str::random(10));
        } while (IssuedOfflineLicense::query()->where('license_id', $candidate)->exists());

        return $candidate;
    }
}
