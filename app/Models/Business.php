<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    protected $fillable = [
        'user_id',
        'years', 
        'reffered_by', 
        'contacted_by', 
        
    ];
}
