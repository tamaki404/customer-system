<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staffs extends Model
{
    protected $fillable = [
        'staff_id',
        'supplier_id',
        'user_id',
        'email_verified_at',
        'action_at',
        'action_by',
        'lastname',
        'firstname',
        'middlename',
        'mobile_no',
        'telephone_no',
        'log_id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
    public function supplier()
    {
        return $this->belongsTo(Suppliers::class, 'supplier_id', 'supplier_id');
    }

}
