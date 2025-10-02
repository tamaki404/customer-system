<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceHistory extends Model
{
    protected $fillable = [
        'phistory_id',
        'supplier_id',
        'set_id',
        'new_price',
        'past_price',
        'action_by',

        'action',
        'sale_id'


    ];
}
