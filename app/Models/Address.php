<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'home_street', 
        'home_subdivision',
        'home_barangay',
        'home_city', 
        'office_street',
        'office_subdivision',
        'office_barangay',
        'office_city',
        'supplier_id'
        
    ];
}
