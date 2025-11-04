<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'email_address', 
        'password',
        'gate_password',
        'image',
        'image_mime_type', 
        'image_filename',
        'image_size',
        'status',
        'role',
        'role_type',
        'email_verified_at',
        'agreement'
        
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

        public function customer()
    {
        return $this->belongsTo(Customers::class, 'user_id', 'user_id');
    }

    public function rep()
    {
        return $this->belongsTo(Representatives::class, 'user_id', 'user_id');
    }
    public function acc_status()
    {
        return $this->belongsTo(AccountStatus::class, 'user_id', 'user_id');
    }

    public function staff()
    {
        return $this->hasOne(Staffs::class, 'user_id', 'user_id');
    }


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}












