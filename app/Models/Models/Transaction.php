<?php

namespace App\Models\Models;

use App\Models\Models\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id', 'type', 'points', 'amount', 'description', 'reason', 'date'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
