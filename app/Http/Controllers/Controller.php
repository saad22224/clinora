<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
    use AuthorizesRequests;

    protected function currentClinic(): Clinic
    {
        $clinic = app()->bound('tenant') ? app('tenant') : auth()->user()?->clinic;

        abort_unless($clinic instanceof Clinic, 403, 'لم يتم تحديد العيادة الحالية.');

        return $clinic;
    }

    protected function currentClinicId(): int
    {
        return (int) $this->currentClinic()->id;
    }
}
