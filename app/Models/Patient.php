<?php

namespace App\Models;

use App\Traits\BelongsToClinic;
use Carbon\Carbon;
use Database\Factories\PatientFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    /** @use HasFactory<PatientFactory> */
    use BelongsToClinic, HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'dob' => 'date',
    ];

    /**
     * Age for display: always derived from DOB when present so birthdays update
     * automatically; otherwise falls back to the stored age column.
     */
    public function displayAge(): ?int
    {
        if ($this->dob) {
            return $this->dob->age;
        }

        return $this->age !== null ? (int) $this->age : null;
    }

    /**
     * When DOB is provided and age is empty, fill age from DOB. Does not invent fields.
     *
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    public static function fillAgeFromDobWhenEmpty(array $validated): array
    {
        if (filled($validated['dob'] ?? null) && ! filled($validated['age'] ?? null)) {
            $validated['age'] = Carbon::parse($validated['dob'])->age;
        }

        return $validated;
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function medicalFiles()
    {
        return $this->hasMany(MedicalFile::class);
    }

    public function diagnoses()
    {
        return $this->hasMany(PatientDiagnosis::class)->latest('diagnosed_at');
    }

    public function visits()
    {
        return $this->hasMany(Visit::class)->latest('visited_at');
    }
}
