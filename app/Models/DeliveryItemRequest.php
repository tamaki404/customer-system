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
        'customer_id',
        'product_id',
        'set_id',
        'planned_kilos',
        'received_kilos',
        'planned_heads',
        'received_heads',
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
    public function deliveryRequest()
    {
        return $this->belongsTo(DeliveryRequest::class, 'delivery_id', 'delivery_id');
    }

    /**
     * Get the product for this item
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

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
    public function productSetting()
    {
        return $this->belongsTo(ProductSetting::class, 'set_id', 'set_id');
    }

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