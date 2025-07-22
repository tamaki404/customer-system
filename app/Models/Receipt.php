<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 

class Receipt extends Model
{
    use HasFactory;
    protected $table = 'receipts';
    protected $fillable = [
        'customer_id',
        'receipt_image',
        'purchase_date',
        'store_name',
        'total_amount',
        'invoice_number',
        'notes',
        'status',
        'verified_by',
        'verified_at',
        'receipt_number',
    ];

    protected $primaryKey = 'receipt_id';
    // Relationships
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}


?>
