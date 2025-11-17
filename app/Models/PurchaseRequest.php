<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_id',
        'status',
        'user_id',
        'total_amount',
        'notes',
        'action_by',
        'action_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'action_at' => 'datetime',
    ];

    /**
     * Get the delivery requests for this purchase request
     */
    public function items()
    {
        return $this->hasMany(DeliveryItemRequest::class, 'po_id', 'po_id');
    }
    public function deliveryRequests()
    {
        return $this->hasMany(DeliveryRequest::class, 'po_id', 'po_id');
    }

public function totalPlanned()
{
    $planned_heads = 0;
    $planned_kilos = 0;

    foreach ($this->deliveryRequests as $dr) {
        foreach ($dr->Delitems as $item) {
            $product = $item->product;

            if ($product->measurement_type == 'Heads' || $product->measurement_type == 'Heads&Kilos') {
                $planned_heads += $item->planned_heads;
            }

            if ($product->measurement_type == 'Kilos' || $product->measurement_type == 'Heads&Kilos') {
                $planned_kilos += $item->planned_kilos;
            }
        }
    }

    return [
        'heads' => $planned_heads,
        'kilos' => $planned_kilos,
    ];
}

public function totalDelivered()
{
    $del_heads = 0;
    $del_kilos = 0;

    foreach ($this->deliveryRequests as $dr) {
        foreach ($dr->Delitems as $item) {
            $product = $item->product;

            if ($product->measurement_type == 'Heads' || $product->measurement_type == 'Heads&Kilos') {
                $del_heads += $item->received_heads;
            }

            if ($product->measurement_type == 'Kilos' || $product->measurement_type == 'Heads&Kilos') {
                $del_kilos += $item->received_kilos;
            }
        }
    }

    return [
        'heads' => $del_heads,
        'kilos' => $del_kilos,
    ];
}


public function hasVariance()
{
    foreach ($this->deliveryRequests as $deliveryRequest) {

        foreach ($deliveryRequest->Delitems as $item) {
            $product = $item->product;

            if ($product->measurement_type === 'Heads' || $product->measurement_type === 'Heads&Kilos') {
                if ($item->planned_heads != $item->received_heads) {
                    return true;
                }
            }

            if ($product->measurement_type === 'Kilos' || $product->measurement_type === 'Heads&Kilos') {
                if ($item->planned_kilos != $item->received_kilos) {
                    return true;
                }
            }
        }
    }

    return false;
}


    /**
     * Get the user who created this purchase request
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
    public function credit()
    {
        return $this->belongsTo(Credits::class, 'user_id', 'user_id');
    }
    /**
     * Get the customer for this purchase request
     */
    public function customer()
    {
        return $this->belongsTo(Customers::class, 'user_id', 'user_id');
    }

    /**
     * Scope for filtering by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by customer
     */
    public function scopeForCustomer($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
    public function requirements()
    {
        return $this->belongsTo(DeliveryRequirements::class, 'user_id', 'user_id');
    }
}
