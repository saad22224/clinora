<?php

namespace App\Repositories\Interfaces;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Collection;

interface PatientRepositoryInterface
{
    public function create(array $data): Patient;

    public function getLatest(): Collection;
}
