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
}
