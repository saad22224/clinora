<?php

namespace App\Models;

use App\Traits\BelongsToClinic;
use Database\Factories\MedicalFileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalFile extends Model
{
    /** @use HasFactory<MedicalFileFactory> */
    use BelongsToClinic, HasFactory;

    protected $guarded = ['id'];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
