<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tickets extends Model
{
    use HasFactory;

    protected $primaryKey = 'ticketID'; 
    public $incrementing = true; // ticketID is still int
    protected $keyType = 'int';

    // If you want to reference users by ULID string:
    // public $incrementing = false;
    // protected $keyType = 'string';

    protected $fillable = [
        'title',
        'body',
        'image',
        'startDate',
        'endDate',
        'id',
        'status',
        'received_by',
        'resolved_at',
    ];
}

class Ticket extends Model
{
    protected $primaryKey = 'ticketID';
    public $incrementing = true;
    protected $keyType = 'int';
}

?>