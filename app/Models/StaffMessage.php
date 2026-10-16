<?php

namespace App\Models;

use Database\Factories\StaffMessageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffMessage extends Model
{
    /** @use HasFactory<StaffMessageFactory> */
    use HasFactory;

    protected $fillable = [
        'clinic_id',
        'from_user_id',
        'body',
        'is_quick',
    ];

    protected function casts(): array
    {
        return [
            'is_quick' => 'boolean',
        ];
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }
}
