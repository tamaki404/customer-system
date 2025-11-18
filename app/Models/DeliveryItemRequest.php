<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeliveryItemRequest extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'delivery_id',
        'delivery_item_id',
        'po_id',
        'customer_id',
        'product_id',
        'set_id',
        'planned_kilos',
        'received_kilos',
        'planned_heads',
        'received_heads',
        'balance',
    ];

    protected $casts = [
        'planned_kilos' => 'decimal:2',
        'received_kilos' => 'decimal:2',
        'planned_heads' => 'integer',
        'received_heads' => 'integer',
    ];

    /**
     * Get the delivery request for this item
     */

public function delivery()
{
    return $this->belongsTo(DeliveryRequest::class, 'delivery_id', 'delivery_id');
}



    public function deliveryRequest()
    {
        return $this->belongsTo(DeliveryRequest::class, 'delivery_id', 'delivery_id');
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id', 'product_id');
    }
    public function promo()
    {
        return $this->belongsTo(Promos::class, 'product_id', 'product_id');
    }
    public function productSetting()
    {
        return $this->hasOne(ProductSetting::class, 'set_id', 'set_id');
    }


public function varianceItems()
{
    return $this->hasMany(DeliveryItemRequest::class, 'delivery_id', 'delivery_id')
        ->whereRaw('planned_heads != received_heads OR planned_kilos != received_kilos');
}

    protected static function booted()
        {
            static::creating(function ($item) {
                if (!$item->product) return; // in case product not loaded yet

                switch ($item->product->measurement_type) {
                    case 'Heads':
                        // Only heads should be filled; clear kilos
                        $item->planned_kilos = null;
                        if (empty($item->planned_heads)) {
                            $item->planned_heads = 0;
                        }
                        break;

                    case 'Kilos':
                        // Only kilos should be filled; clear heads
                        $item->planned_heads = null;
                        if (empty($item->planned_kilos)) {
                            $item->planned_kilos = 0.0;
                        }
                        break;

                    case 'Heads&Kilos':
                        // Both can exist; ensure at least 0 defaults
                        if (empty($item->planned_heads)) {
                            $item->planned_heads = 0;
                        }
                        if (empty($item->planned_kilos)) {
                            $item->planned_kilos = 0.0;
                        }
                        break;
                }
            });
        }
    /**
     * Get the product for this item
     */

    /**
     * Get the customer for this item
     */
    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id', 'customer_id');
    }

    /**
     * Get the product setting for this item
     */


    /**
     * Check if item has been fully received
     */
    public function isFullyReceived(): bool
    {
        if ($this->planned_heads > 0) {
            return $this->received_heads >= $this->planned_heads;
        }
        
        if ($this->planned_kilos > 0) {
            return $this->received_kilos >= $this->planned_kilos;
        }
        
        return false;
    }

    /**
     * Get the variance between planned and received
     */
    public function getVariance(): array
    {
        return [
            'heads_variance' => $this->received_heads - $this->planned_heads,
            'kilos_variance' => $this->received_kilos - $this->planned_kilos,
        ];
    }
}