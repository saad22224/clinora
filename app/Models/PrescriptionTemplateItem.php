<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrescriptionTemplateItem extends Model
{
    protected $guarded = ['id'];

    public function template(): BelongsTo
    {
        return $this->belongsTo(PrescriptionTemplate::class, 'prescription_template_id');
    }
}
