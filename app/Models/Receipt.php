<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 

class Receipt extends Model

{
    use HasFactory;
    protected $table = 'receipts';
    protected $fillable = [
        'receipt_image',
        'receipt_image_mime',
        'purchase_date',
        'store_name',
        'total_amount',
        'invoice_number',
        'notes',
        'receipt_number',
        'status',
        'id',
        'po_number',
        'payment_at',
        'rejected_note',
        'additional_note',
        'action_by',
        'action_at'
    ];

    protected $primaryKey = 'receipt_id';
    // Relationships
    public function customer()
    {
    return $this->belongsTo(User::class, 'id', 'id');
    }

    public function purchaseOrder()
    {
    return $this->belongsTo(PurchaseOrder::class, 'po_number', 'po_number');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}


?>
