<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;

class Representatives extends Model implements Authenticatable
{
    use \Illuminate\Auth\Authenticatable;
    use HasFactory;

    protected $fillable = [
        'user_id',
        'rep_lastname', 
        'rep_firstname', 
        'rep_middlename', 
        'auth_position', 
        'rep_contact',
        'supplier_id',
        'rep_id',
        'permissions',
        'cid'
    
    ];
protected $casts = [
    'permissions' => 'array',
];


    public function supplier()
    {
        return $this->belongsTo(Suppliers::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }


    public function can($permission)
    {
        return $this->permissions[$permission] ?? false;
    }

    public function getAuthIdentifierName()
    {
        return 'rep_id';
    }

}
