<?php

namespace App\Models;

use Database\Factories\DrugFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Drug extends Model
{
    /** @use HasFactory<DrugFactory> */
    use HasFactory;

    protected $guarded = ['id'];
}
