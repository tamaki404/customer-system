<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalCeiling extends Model
{
protected $fillable = [
    'fixed_price',
    'percentage_ceiling',
    'method',
    'city_selected',
    'start_date',
    'end_date',
    'staff_id',
    'ceiling_id'
];

}
