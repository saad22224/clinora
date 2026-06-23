<?php

namespace App\Models;

use App\Traits\BelongsToClinic;
use Database\Factories\FinanceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Finance extends Model
{
    /** @use HasFactory<FinanceFactory> */
    use BelongsToClinic, HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
    ];
}
