<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountStatus extends Model
{
    use HasFactory;
 protected $table = 'account_status';
    protected $fillable = [
        'user_id',
        'status_id', 
        'account_status', 
        'reason_to_decline',
        'staff_id',
        'supplier_id',
        'approved_at',
        'approved_by',
    ];

    public function supplier()
    {
        return $this->belongsTo(Suppliers::class);
    }public function staff()
    {
        return $this->belongsTo(Staffs::class, 'approved_by', 'user_id');
    }

}

