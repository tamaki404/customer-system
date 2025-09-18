<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountStatus extends Model
{
    use HasFactory;
 protected $table = 'account_status';
    protected $fillable = [
        'supplier_id',
        'status_id', 
        'acc_status', 
        'reason_to_decline',
        'staff_id',
    ];

    public function supplier()
    {
        return $this->belongsTo(Suppliers::class);
    }

}

