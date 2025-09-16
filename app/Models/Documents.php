<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documents extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'type',
        'file_name',
        'file_mime',
        'file_size',
        'file',
    ];


    public function supplier()
    {
        return $this->belongsTo(Suppliers::class);
    }
}
