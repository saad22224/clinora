<?php

namespace App\Traits;

use App\Models\Clinic;
use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait BelongsToClinic
{
    protected static function bootBelongsToClinic(): void
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function ($model) {
            if (! blank($model->clinic_id)) {
                return;
            }

            if (! Auth::hasUser()) {
                return;
            }

            $clinicId = session('active_clinic_id') ?: Auth::user()->clinic_id;

            if ($clinicId) {
                $model->clinic_id = $clinicId;
            }
        });
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }
}
