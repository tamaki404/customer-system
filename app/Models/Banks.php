<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banks extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_id',
        'account_name', 
        'bank', 
        'branch', 
        'account_number',
    ];

    public function customer()
    {
        return $this->belongsTo(Customers::class);
    }
}
