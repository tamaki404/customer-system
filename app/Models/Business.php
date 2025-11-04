<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    protected $fillable = [
        'user_id',
        'customer_id',
        'years', 
        'referred_by', 
        'contacted_by', 
        
    ];
}
