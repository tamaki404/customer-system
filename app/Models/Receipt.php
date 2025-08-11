<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 

class Receipt extends Model
    // If you ever use Receipt::find($id), and $id is a string ULID, you may want to set these:
    // public $incrementing = false;
    // protected $keyType = 'string';
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
    ];

    protected $primaryKey = 'receipt_id';
    // Relationships
    public function customer()
    {
    return $this->belongsTo(User::class, 'id', 'id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}


?>
