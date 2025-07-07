<?php

namespace App\Models\Models;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Model
{
    //  use HasFactory;

    protected $fillable = [
        'username', 'password', 'last_login', 'status'
    ];

    protected $hidden = ['password'];
}
