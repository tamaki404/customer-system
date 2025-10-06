<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suppliers extends Model
{

    protected $fillable = [
        'user_id',
        'supplier_id',
        'staff_id',
        'company_name',
        'category',
        'mobile',
        'citizenship',
        'payment_method',
        'tele',
        'civil_status',
        'id_image',
        'id_mime_type', 
        'id_filename',
        'id_size',
        'id_type',
        'id_number',
        'birthdate',

    ];


    public function set()
    {
        return $this->belongsTo( ProductSetting::class, 'supplier_id', 'supplier_id');
    }
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
    public function delivery()
    {
        return $this->belongsTo(DeliveryRequirements::class, 'supplier_id', 'supplier_id');
    }
    public function req($productId)
    {
        return $this->hasOne(ProductRequirements::class, 'user_id', 'user_id')
            ->where('product_id', $productId);
    }
}


