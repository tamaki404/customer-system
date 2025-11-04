<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceHistory extends Model
{
    protected $fillable = [
        'phistory_id',
        'customer_id',
        'set_id',
        'new_price',
        'past_price',
        'action_by',

        'action',
        'sale_id'
    ];

    public function set()
    {
        return $this->belongsTo(ProductSetting::class, 'set_id', 'set_id');
    }
    public function staff()
    {
        return $this->belongsTo(Staffs::class, 'action_by', 'user_id');
    }


}
