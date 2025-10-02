<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductRequirements extends Model
{
    protected $fillable = [
        'user_id',
        'supplier_id',
        'product_id',
        'condition',
        'weight_requirement',
        'primary_packaging',
        'secondary_packaging',
        'labeling_requiremen',
        'rejection_parameter'


    ];

        public function product()
    {
        return $this->belongsTo(Products::class, 'product_id', 'product_id');
    }
        public function settings()
    {
        return $this->belongsTo(ProductSetting::class, 'supplier_id', 'supplier_id');
    }
    
}
