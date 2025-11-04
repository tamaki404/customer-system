<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documents extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_id',
        'type',
        'file_name',
        'file_mime',
        'file_size',
        'file',
    ];


    public function customer()
    {
        return $this->belongsTo(Customers::class);
    }
}
