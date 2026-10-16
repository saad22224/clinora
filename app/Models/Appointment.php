<?php

namespace App\Models;

use App\Traits\BelongsToClinic;
use Database\Factories\AppointmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Appointment extends Model
{
    /** @use HasFactory<AppointmentFactory> */
    use BelongsToClinic, HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'price' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'reminder_sent' => 'boolean',
            'queue_number' => 'integer',
            'queue_date' => 'date',
            'called_at' => 'datetime',
            'in_progress_at' => 'datetime',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function prescription(): HasOne
    {
        return $this->hasOne(Prescription::class);
    }

    public function visitType(): BelongsTo
    {
        return $this->belongsTo(VisitType::class);
    }
}
