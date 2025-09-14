<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
class PurchaseOrderItemController extends Controller{
public function changeQuantity(Request $request) {
    $request->validate([
        'new_quantity' => 'required|array|min:1',
        'new_quantity.*' => 'nullable|integer|min:0'
    ]);

    // Filter out empty/null values - only process items where user entered a value
    $quantities = array_filter($request->new_quantity, function($value) {
        return $value !== null && $value !== '' && is_numeric($value);
    });

    if (empty($quantities)) {
        return back()->with('info', 'No quantities were specified for update.');
    }

    $updatedCount = 0;
    $errors = [];
    $po_id = null; // Track the purchase order ID

    try {
        DB::transaction(function () use ($quantities, &$updatedCount, &$errors, &$po_id) {
            foreach ($quantities as $poi_id => $new_quantity) {
                $currentItem = PurchaseOrderItem::where('poi_id', $poi_id)->first();

                if (!$currentItem) {
                    $errors[] = "Item with POI {$poi_id} not found.";
                    continue;
                }

                // Store po_id for later use
                if (!$po_id) {
                    $po_id = $currentItem->po_id;
                }

                // Convert to integer for comparison
                $new_quantity = (int) $new_quantity;

                // Validate that new_quantity doesn't exceed original quantity
                if ($new_quantity > $currentItem->quantity) {
                    $errors[] = "New quantity ({$new_quantity}) for '{$currentItem->product->name}' cannot exceed original quantity ({$currentItem->quantity}).";
                    continue;
                }

                // Update the new_quantity column
                $oldNewQuantity = $currentItem->new_quantity;
                $currentItem->update(['new_quantity' => $new_quantity]);

                // Only count as updated if the value actually changed
                if ($oldNewQuantity != $new_quantity) {
                    $updatedCount++;
                }
            }

            // If there are validation errors, throw an exception to rollback
            if (!empty($errors)) {
                throw new \Exception('Validation failed: ' . implode(' | ', $errors));
            }

            // Calculate and update the new grand total
            if ($po_id) {
                $this->updatePurchaseOrderGrandTotal($po_id);
            }
        });

        return back()->with(
            $updatedCount > 0 ? 'success' : 'info',
            $updatedCount > 0 
                ? "Successfully updated {$updatedCount} item(s) with new quantities and recalculated total."
                : 'Values processed but no changes were made (same quantities were entered).'
        );

    } catch (\Exception $e) {
        return back()->withErrors([
            'quantity_errors' => $errors ?: [$e->getMessage()]
        ])->withInput();
    }
}

/**
 * Calculate and update the grand total for a purchase order based on new quantities
 */
private function updatePurchaseOrderGrandTotal($po_id) {
    // Get all items for this purchase order
    $orderItems = PurchaseOrderItem::where('po_id', $po_id)->get();
    
    $newGrandTotal = 0;
    
    foreach ($orderItems as $item) {
        // Use new_quantity if it exists, otherwise use original quantity
        $quantity = $item->new_quantity ?? $item->quantity;
        $unitPrice = $item->unit_price;
        
        // Calculate subtotal for this item
        $subtotal = $quantity * $unitPrice;
        $newGrandTotal += $subtotal;
    }
    
    // Update the purchase order's grand_total
    PurchaseOrder::where('po_id', $po_id)->update([
        'grand_total' => $newGrandTotal
    ]);
    
    return $newGrandTotal;
}
}



