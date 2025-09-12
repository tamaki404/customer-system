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
public function changeQuantity(Request $request)
{
    $request->validate([
        'new_quantity' => 'required|array|min:1',
        'new_quantity.*' => 'required|integer|min:1'
    ]);

    $poi_ids = array_keys($request->new_quantity);
    
    $orderItems = PurchaseOrderItem::whereIn('poi_id', $poi_ids)->get()->keyBy('poi_id');
    
    $missingItems = array_diff($poi_ids, $orderItems->keys()->toArray());
    if (!empty($missingItems)) {
        return back()->withErrors([
            'error' => 'Some purchase order items were not found: ' . implode(', ', $missingItems)
        ]);
    }
    
    $rules = [];
    $hasChanges = false;
    
    foreach ($request->new_quantity as $poi_id => $new_quantity) {
        $currentItem = $orderItems[$poi_id];
        $rules["new_quantity.$poi_id"] = "required|integer|min:1|max:{$currentItem->quantity}";
        
        if ($new_quantity != $currentItem->quantity) {
            $hasChanges = true;
        }
    }

    if (!$hasChanges) {
        return back()->with('info', 'No changes were made to quantities.');
    }

    $validated = $request->validate($rules);

    DB::beginTransaction();
    
    try {
        $updatedCount = 0;
        
        foreach ($validated['new_quantity'] as $poi_id => $new_quantity) {
            $affected = PurchaseOrderItem::where('poi_id', $poi_id)
                ->update(['new_quantity' => $new_quantity, 'updated_at' => now()]);
            
            if ($affected > 0) {
                $updatedCount++;
            }
        }
        
        DB::commit();
        
        if ($updatedCount > 0) {
            return back()->with('success', "Successfully updated {$updatedCount} item quantities.");
        } else {
            return back()->with('warning', 'No items were updated. Please try again.');
        }
        
    } catch (\Exception $e) {
        DB::rollback();

        \Log::error('Failed to update quantities', [
            'error' => $e->getMessage(),
            'poi_ids' => $poi_ids,
            'quantities' => $request->new_quantity
        ]);
        
        return back()->with('error', 'Failed to update quantities. Please try again.');
    }
}
}



