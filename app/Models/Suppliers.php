<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suppliers extends Model
{
    protected $fillable = [
        'user_id',
        'supplier_id',
        'email_verified_at',
        'action_at',
        'company_name',
        'home_street',
        'home_subdivision',
        'home_barangay',
        'home_city',
        'office_street',
        'office_subdivision',
        'office_barangay',
        'office_city',
        'mobile_no',
        'telephone_no',
        'birthdate',
        'valid_id_no',
        'id_type',
        // 'email',
        'civil_status', 
        'citizenship', 
        'payment_method',

        'salesman_relationship', 
        'weekly_volume', 
        'other_products_interest',
        'date_required', 
        'referred_by', 

        'product_requirements',
        
        'agreement',

        'staff_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
    public function staff()
    {
        return $this->belongsTo(Staffs::class, 'staff_id', 'staff_id');
    }

    public function signatory()
    {
        return $this->belongsTo( Signatories::class, 'supplier_id', 'supplier_id');
    }

    public function representative()
    {
        return $this->belongsTo( Representatives::class, 'supplier_id', 'supplier_id');
    }
    public function account_status()
    {
        return $this->belongsTo( AccountStatus::class, 'supplier_id', 'supplier_id');
    }
    public function bank()
    {
        return $this->belongsTo( Banks::class, 'supplier_id', 'supplier_id');
    }
}


