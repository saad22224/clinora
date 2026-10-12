<?php

namespace App\Models;

use Database\Factories\ClinicFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Clinic extends Model
{
    /** @use HasFactory<ClinicFactory> */
    use HasFactory, SoftDeletes;

    /**
     * Generate a unique slug, including soft-deleted clinics that still occupy the unique index.
     */
    public static function uniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug !== '' ? $slug : 'clinic';
        $slug = $originalSlug;
        $count = 1;

        while (static::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $originalSlug.'-'.$count++;
        }

        return $slug;
    }

    protected $fillable = [
        'organization_id',
        'marketer_id',
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
        'latitude',
        'longitude',
        'working_hours',
        'hero_title',
        'hero_subtitle',
        'about_us',
        'social_links',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'working_hours' => 'array',
        'social_links' => 'array',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

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

    public function visitTypes(): HasMany
    {
        return $this->hasMany(VisitType::class);
    }

    public function marketer(): BelongsTo
    {
        return $this->belongsTo(Marketer::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(ClinicNotification::class);
    }
}
