<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Representatives extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'rep_lastname', 
        'rep_firstname', 
        'rep_middlename', 
        'auth_position', 
        'rep_contact',
    ];

    public function supplier()
    {
        return $this->belongsTo(Suppliers::class);
    }
}
