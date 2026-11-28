<?php

namespace App\Models;

use App\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Visit extends Model
{
    use BelongsToClinic;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'visited_at' => 'datetime',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function diagnoses(): HasMany
    {
        return $this->hasMany(PatientDiagnosis::class)->latest('diagnosed_at');
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class)->latest();
    }

    public function medicalFiles(): HasMany
    {
        return $this->hasMany(MedicalFile::class)->latest();
    }

    public function labOrders(): HasMany
    {
        return $this->hasMany(LabOrder::class)->latest('ordered_at');
    }

    public function radiologyOrders(): HasMany
    {
        return $this->hasMany(RadiologyOrder::class)->latest('ordered_at');
    }

    public function followUpAppointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'follow_up_of_visit_id')->latest('scheduled_at');
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
