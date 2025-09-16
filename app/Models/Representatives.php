<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Representatives extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'rep_last_name', 
        'rep_first_name', 
        'rep_middle_name', 
        'rep_relationship', 
        'rep_contact_no',
    ];

    public function supplier()
    {
        return $this->belongsTo(Suppliers::class);
    }
}
