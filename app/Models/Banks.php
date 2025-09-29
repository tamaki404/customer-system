<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banks extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'supplier_id',
        'account_name', 
        'bank', 
        'branch', 
        'account_number',
    ];

    public function supplier()
    {
        return $this->belongsTo(Suppliers::class);
    }
}
