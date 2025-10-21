<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    protected $fillable = [
        // 'product_id',
        // 'parent_product_id',
        // 'name',
        // 'base_price',
        // 'category',
        // 'category_id',
        // 'unit',
        // 'weight',
        // 'added_by',
        // 'status',
        // 'measurement_type'

        'product_id',
        'name',
        'base_price',
        'category',
        'measurement_type',
        'added_by',
        'status',

    ];





    public function set()
    {
        return $this->belongsTo(ProductSetting::class, 'product_id', 'product_id');
    }

    public function req()
    {
        return $this->belongsTo(ProductRequirements::class, 'product_id', 'product_id');
    }

    
}
