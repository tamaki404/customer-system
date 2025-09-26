<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Signatories extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'lastname', 
        'firstname', 
        'middlename', 
        'sign_position', 
        'e_image',
        'e_size',
        'e_mime_type', 
        'e_filename',
    ];

    public function supplier()
    {
        return $this->belongsTo(Suppliers::class);
    }
}
