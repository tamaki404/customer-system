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

        // Skip returns for planned quantity
        if ($dr->label === 'Return') {
            continue;
        }

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

        // Only count items if delivery status is Delivered
        if ($dr->status !== 'Delivered') {
            continue;
        }

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
    $total_planned_heads = 0;
    $total_planned_kilos = 0;
    $total_received_heads = 0;
    $total_received_kilos = 0;

    foreach ($this->deliveryRequests as $deliveryRequest) {

        // Only count received quantities for delivered items
        if ($deliveryRequest->status === 'Delivered') {
            foreach ($deliveryRequest->Delitems as $item) {
                $product = $item->product;

                if ($product->measurement_type === 'Heads' || $product->measurement_type === 'Heads&Kilos') {
                    $total_received_heads += $item->received_heads;
                }

                if ($product->measurement_type === 'Kilos' || $product->measurement_type === 'Heads&Kilos') {
                    $total_received_kilos += $item->received_kilos;
                }
            }
        }

        // Count planned quantities, but skip deliveries labeled 'Return'
        if ($deliveryRequest->label !== 'Return') {
            foreach ($deliveryRequest->Delitems as $item) {
                $product = $item->product;

                if ($product->measurement_type === 'Heads' || $product->measurement_type === 'Heads&Kilos') {
                    $total_planned_heads += $item->planned_heads;
                }

                if ($product->measurement_type === 'Kilos' || $product->measurement_type === 'Heads&Kilos') {
                    $total_planned_kilos += $item->planned_kilos;
                }
            }
        }
    }

    // Check if any variance exists
    return ($total_planned_heads != $total_received_heads) || ($total_planned_kilos != $total_received_kilos);
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
