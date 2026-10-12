<?php

namespace App\Models;

use Database\Factories\VisitTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VisitType extends Model
{
    /** @use HasFactory<VisitTypeFactory> */
    use HasFactory;

    protected $fillable = [
        'clinic_id',
        'name',
        'description',
        'price',
        'icon',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
