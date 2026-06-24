<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Clinic extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'logo',
        'description',
        'theme_color',
        'is_active',
        'doctor_bio',
        'doctor_title',
        'doctor_image',
        'phone',
        'address',
        'working_hours',
        'hero_title',
        'hero_subtitle',
        'about_us',
        'social_links',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'working_hours' => 'array',
        'social_links' => 'array',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
    }

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    public function medicalFiles(): HasMany
    {
        return $this->hasMany(MedicalFile::class);
    }

    public function finances(): HasMany
    {
        return $this->hasMany(Finance::class);
    }

    public function bookingRequests(): HasMany
    {
        return $this->hasMany(BookingRequest::class);
    }

    public function visitTypes(): HasMany
    {
        return $this->hasMany(VisitType::class);
    }
}
