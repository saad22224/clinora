<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (! Auth::hasUser()) {
            return;
        }

        $clinicId = session('active_clinic_id') ?: Auth::user()->clinic_id;

        if ($clinicId) {
            $builder->where($model->getTable().'.clinic_id', $clinicId);
        }
    }
}
