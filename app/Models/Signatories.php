<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Signatories extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_id',
        'sign_lastname', 
        'sign_firstname', 
        'sign_middlename', 
        'sign_position', 
        'e_image',
        'e_size',
        'e_mime_type', 
        'e_filename',
    ];

    public function customer()
    {
        return $this->belongsTo(Customers::class);
    }
}
