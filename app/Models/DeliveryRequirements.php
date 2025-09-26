<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryRequirements extends Model
{

    protected $fillable = [
        'user_id',
        'ppe_requirements',
        'delivery_frequency',
        'delivery_address_1',
        'delivery_address_2',
        'delivery_address_3',
        'delivery_instructions'


    ];
}
