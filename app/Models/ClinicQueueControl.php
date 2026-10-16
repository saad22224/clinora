<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClinicQueueControl extends Model
{
    protected $fillable = [
        'clinic_id',
        'doctor_id',
        'queue_date',
        'is_paused',
    ];

    protected function casts(): array
    {
        return [
            'queue_date' => 'date',
            'is_paused' => 'boolean',
        ];
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
