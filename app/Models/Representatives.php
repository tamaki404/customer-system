<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Representatives extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'lastname', 
        'firstname', 
        'middlename', 
        'auth_position', 
        'contact',
    ];

    public function supplier()
    {
        return $this->belongsTo(Suppliers::class);
    }
}
