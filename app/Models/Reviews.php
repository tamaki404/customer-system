<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reviews extends Model
{

    protected $fillable = [
        'review_id',
        'head',
        'body',
        'user_id',
        'status',
        'raised_by',
        'raised_at',
        'review_feedback',
        'reviewed_by',
        'reviewed_at',
        'resolved_at',
        'resolved_by',

    ];

    public function staff()
    {
        return $this->belongsTo(Staffs::class, 'raised_by', 'user_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'raised_by', 'user_id');
    }
    public function raised()
    {
        return $this->belongsTo(Staffs::class, 'raised_by', 'user_id');
    }
    public function resolved()
    {
        return $this->belongsTo(Staffs::class, 'resolved_by', 'user_id');
    }
    public function reviewed()
    {
        return $this->belongsTo(Staffs::class, 'reviewed_by', 'user_id');
    }

}
