<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IssuedOfflineLicense extends Model
{
    protected $fillable = [
        'license_id',
        'clinic_name',
        'customer_name',
        'key_id',
        'fingerprint_components',
        'fingerprint_digest',
        'min_match',
        'install_public_key',
        'license_issued_at',
        'starts_at',
        'expires_at',
        'grace_days',
        'max_devices',
        'max_doctors',
        'transfer_seq',
        'features',
        'envelope_json',
        'envelope_sha256',
        'lifecycle_status',
        'renewed_from_id',
        'issued_by',
        'request_meta',
    ];

    protected function casts(): array
    {
        return [
            'fingerprint_components' => 'array',
            'features' => 'array',
            'request_meta' => 'array',
            'license_issued_at' => 'datetime',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'grace_days' => 'integer',
            'max_devices' => 'integer',
            'max_doctors' => 'integer',
            'transfer_seq' => 'integer',
            'min_match' => 'integer',
        ];
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function renewedFrom(): BelongsTo
    {
        return $this->belongsTo(self::class, 'renewed_from_id');
    }

    public function renewals(): HasMany
    {
        return $this->hasMany(self::class, 'renewed_from_id');
    }

    public function computedStatus(?CarbonImmutable $now = null): string
    {
        if ($this->lifecycle_status === 'replaced') {
            return 'replaced';
        }

        $now ??= CarbonImmutable::now('UTC');
        $starts = CarbonImmutable::instance($this->starts_at)->utc();
        $expires = CarbonImmutable::instance($this->expires_at)->utc();
        $graceEnds = $expires->addDays((int) $this->grace_days);

        if ($now->lessThan($starts)) {
            return 'not_started';
        }

        if ($now->lessThan($expires)) {
            return 'active';
        }

        if ($now->lessThan($graceEnds)) {
            return 'grace';
        }

        return 'expired';
    }

    public function statusLabel(): string
    {
        return match ($this->computedStatus()) {
            'active' => 'ساري',
            'not_started' => 'لم يبدأ بعد',
            'grace' => 'فترة سماح',
            'expired' => 'منتهٍ',
            'replaced' => 'استُبدل / جُدّد',
            default => $this->lifecycle_status,
        };
    }

    public function fingerprintSummary(): string
    {
        $parts = collect($this->fingerprint_components ?? [])
            ->map(fn ($component, $index) => $component ? substr((string) $component, 0, 8) : '----')
            ->implode(' · ');

        return $parts !== '' ? $parts : '—';
    }

    public function downloadFilename(): string
    {
        return 'Clinora-License-'.$this->license_id.'.json';
    }
}
