<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Signatories extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'lastname', 
        'firstname', 
        'middlename', 
        'sign_position', 
        'e_signature',
    ];

    public function supplier()
    {
        return $this->belongsTo(Suppliers::class);
    }
}
