<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model {
    protected $table = 'province';
    protected $primaryKey = 'province_id';
    public $timestamps = false;

    public function municipality() {
        return $this->hasMany(Municipality::class, 'province_id');
    }
}