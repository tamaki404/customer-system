<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_id',
        'delivery_id',
        'customer_id',
        'delivery_date',
        'delivered_date',
        'status',
        'payment_status',
        'return_status',
        'label',
        'due_date',
        'reference_delivery',
        'sum_balance',
        'action_by',
        'action_at',
        'feedback',
        'pod_file',
    ];

    protected $casts = [
        'delivery_date' => 'date',

        'delivered_date' => 'datetime',
        'action_at' => 'datetime',
    ];
    public function deliveryItems()
    {
        return $this->hasMany(DeliveryItemRequest::class, 'delivery_id', 'delivery_id');
    }
    public function item()
    {
        return $this->belongsTo(DeliveryItemRequest::class, 'delivery_id', 'delivery_id');
    }
    public function payments()
    {
        return $this->hasMany(Payments::class, 'delivery_id', 'delivery_id');
    }
public function Delitems()
{
    return $this->hasMany(DeliveryItemRequest::class, 'delivery_id', 'delivery_id');
}

    /**
     * Get the purchase request for this delivery
     */
    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class, 'po_id', 'po_id');
    }
    // public function payment()
    // {
    //     return $this->belongsTo(Payments::class, 'delivery_id', 'delivery_id');
    // }
    /**
     * Get the delivery items for this delivery request
     */

    public function productSetting()
    {
        return $this->hasOne(ProductSetting::class, 'set_id', 'set_id');
    }
    public function items()
    {
        return $this->hasMany(DeliveryItemRequest::class, 'po_id', 'po_id');
    }
    public function scheduled_items()
    {
        return $this->hasMany(DeliveryItemRequest::class, 'delivery_id', 'delivery_id');
    }
    /**
     * Get the customer for this delivery
     */
    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id', 'customer_id');
    }

    /**
     * Scope for filtering by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for deliveries on a specific date
     */
    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('delivery_date', $date);
    }

    /**
     * Scope for upcoming deliveries
     */
    public function scopeUpcoming($query)
    {
        return $query->where('delivery_date', '>=', now()->toDateString())
            ->where('status', 'Scheduled');
    }
}
