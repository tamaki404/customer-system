<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model {
    protected $table = 'region';
    protected $primaryKey = 'region_id';
    public $timestamps = false;

    public function province() {
        return $this->hasMany(Province::class, 'region_id');
    }
}
