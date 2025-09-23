<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    protected $fillable = [
        'product_id',
        'parent_product_id',
        'name',
        'srp',
        'category',
        'category_id',
        'unit',
        'weight',
        'added_by',
        'status',

    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function parent()
    {
        return $this->belongsTo(Products::class, 'parent_product_id', 'product_id');
    }

    public function children()
    {
        return $this->hasMany(Products::class, 'parent_product_id', 'product_id');
    }
}
