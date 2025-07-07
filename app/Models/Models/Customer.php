<?php

namespace App\Models\Models;
use App\Models\Models\Transaction;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'full_name', 'phone_number', 'email', 'gender','pin_code','points','tier'
    ];   
     
    public function transactions()
{
    return $this->hasMany(Transaction::class);
}

}
