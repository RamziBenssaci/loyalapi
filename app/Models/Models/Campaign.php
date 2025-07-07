<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Campaign extends Model
{
     use HasFactory;

    protected $fillable = [
        'name', 'description', 'start_date', 'end_date', 'earn_rate', 'redeem_rate', 'status'
    ];
}
