<?php

namespace App\Repositories;

use App\Models\Patient;
use App\Repositories\Interfaces\PatientRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PatientRepository implements PatientRepositoryInterface
{
    public function create(array $data): Patient
    {
        return Patient::create($data);
    }

    public function getLatest(): Collection
    {
        return Patient::latest()->take(10)->get();
    }
}
