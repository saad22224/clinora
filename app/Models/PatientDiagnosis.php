<?php

namespace App\Models;

use App\Traits\BelongsToClinic;
use Database\Factories\PatientDiagnosisFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientDiagnosis extends Model
{
    /** @use HasFactory<PatientDiagnosisFactory> */
    use BelongsToClinic, HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'diagnosed_at' => 'date',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }
}
