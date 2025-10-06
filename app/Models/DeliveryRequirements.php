<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryRequirements extends Model
{
    protected $fillable = [
        'delivery_frequency',
        'deliveries_per_week',
        'delivery_days',
        'deliveries_per_month',
        'receiving_time',
        'delivery_address_1',
        'delivery_address_2',
        'delivery_address_3',
    ];

}
