<?php

namespace App\Models;

use Database\Factories\ClinicNotificationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClinicNotification extends Model
{
    /** @use HasFactory<ClinicNotificationFactory> */
    use HasFactory;

    protected $fillable = [
        'clinic_id',
        'title',
        'message',
        'type',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }
}
