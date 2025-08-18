<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Municipality extends Model {
    protected $table = 'municipality';
    protected $primaryKey = 'municipality_id';
    public $timestamps = false;

    public function barangay() {
        return $this->hasMany(Barangay::class, 'municipality_id');
    }
}
