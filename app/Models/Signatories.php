<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Signatories extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'signatory_last_name',
        'signatory_first_name',
        'signatory_middle_name',
        'signatory_relationship',
        'signatory_contact_no',
    ];

    public function supplier()
    {
        return $this->belongsTo(Suppliers::class);
    }
}
