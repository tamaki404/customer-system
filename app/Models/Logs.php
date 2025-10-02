<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logs extends Model
{
    use HasFactory;

    protected $table = 'logs';

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'log_id',     
        'user_id',     
        'action',      
        'description',
        'ip_address',
        'entity',
        'entity_id',
    ];

    // Relationship back to user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
