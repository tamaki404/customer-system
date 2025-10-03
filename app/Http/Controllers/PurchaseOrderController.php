<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Orders;
use App\Models\PurchaseOrders;
use App\Models\PurchaseOrderItem;
use App\Models\Suppliers;
use Illuminate\Support\Facades\Auth;
use App\Models\Products;
use App\Models\OrderItem;
use App\Models\ProductSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Logs;
use App\Models\OrderHistory;
use App\Models\ProductSales;

class PurchaseOrderController extends Controller
{
public function purchaseOrderlist(Request $request)
{
    $user = Auth::user();
    $supplier = null;
    $pos = collect();
    $setProds = collect();

    if ($user->role === "Supplier") {
        $supplier = Suppliers::where('user_id', $user->user_id)->first();

        if ($supplier) {
            $query = PurchaseOrders::where('supplier_id', $supplier->supplier_id)
                ->with(['items', 'supplier']);

            // Apply search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('po_id', 'like', "%{$search}%")
                      ->orWhere('status', 'like', "%{$search}%");
                });
            }

            // Apply date filter
            if ($request->filled('from_date')) {
                $query->whereDate('created_at', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $query->whereDate('created_at', '<=', $request->to_date);
            }

            $pos = $query->orderBy('created_at', 'desc')->get();

            // Only load setProds if supplier exists
            $setProds = ProductSetting::where('supplier_id', $supplier->supplier_id)
                ->with('product')
                ->get()
                ->map(function($setProds) {
                    $activeSale = ProductSales::where('set_id', $setProds->set_id)
                        ->whereDate('start_date', '<=', now())
                        ->whereDate('end_date', '>=', now())
                        ->first();

                    if ($activeSale) {
                        $setProds->nego_price = $activeSale->sale_price;
                    }

                    return $setProds;
                });
        }
    } 
    elseif ($user->role === "Staff" || $user->role === "Admin") {
        $query = PurchaseOrders::with(['items', 'supplier', 'staff']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('po_id', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function($supplierQuery) use ($search) {
                      $supplierQuery->where('company_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $pos = $query->orderBy('created_at', 'desc')->get();
    }

    return view('purchase-orders.list', [
        'user' => $user,
        'supplier' => $supplier,
        'setProds' => $setProds,
        'pos' => $pos,
    ]);
}


        
        public function purchaseOrderView($po_id, Request $request)
        {
            $user = Auth::user();
            $po = PurchaseOrders::where('po_id', $po_id)->with(['items.product', 'supplier'])->first(); 
            
            if ($user->role === "Supplier") {
                $setProducts = ProductSetting::where('supplier_id', $po->supplier_id)
                    ->with('product')
                    ->get();
            } else {
                $setProducts = collect();
            }

            return view('purchase-orders.purchaseorder', [
                'user' => $user,
                'po' => $po,
                'setProducts' => $setProducts,
            ]);
        }

        public function createPurchaseOrder(Request $request)
        {
            $user = Auth::user();
            
            if ($user->role !== "Supplier") {
                return redirect()->back()->with('error', 'Only suppliers can create purchase orders.');
            }

            $supplier = Suppliers::where('user_id', $user->user_id)->first();
            
            if (!$supplier) {
                return redirect()->back()->with('error', 'Supplier profile not found.');
            }

            try {
                $request->validate([
                    'selected_products' => 'required|array|min:1',
                    'selected_products.*' => 'exists:product_settings,set_id',
                    'quantities' => 'required|array',
                    'quantities.*' => 'required|integer|min:1',
                    'notes' => 'nullable|string|max:1000',
                ]);

                DB::beginTransaction();

                $date = date('Ymd');
                $po_id = 'PO-' . $date . '-' . $this->randomBase36String(5);

                // Create purchase order
                $purchaseOrder = PurchaseOrders::create([
                    'po_id' => $po_id,
                    'supplier_id' => $supplier->supplier_id,
                    'status' => 'Pending',
                    'notes' => $request->notes,
                    'total_amount' => 0,
                    'placed_at' => now(),
                ]);

                $totalAmount = 0;

                // Create purchase order items
                foreach ($request->selected_products as $setId) {
                    $productSetting = ProductSetting::where('set_id', $setId)->first();
                    $quantity = $request->quantities[$setId] ?? 1;
                    $unitPrice = $productSetting->price;
                    $itemTotal = $unitPrice * $quantity;

                    $poItemId = 'POI-' . $date . '-' . $this->randomBase36String(5);

                    PurchaseOrderItem::create([
                        'po_item_id' => $poItemId,
                        'po_id' => $po_id,
                        'product_id' => $productSetting->product_id,
                        'set_id' => $setId,
                        'supplier_quantity' => $quantity,
                        'staff_quantity' => $quantity, // Initially same as supplier quantity
                        'unit_price' => $unitPrice,
                        'total_price' => $itemTotal,
                        'status' => 'Pending',
                    ]);

                    $totalAmount += $itemTotal;
                }

                // Update total amount
                $purchaseOrder->update(['total_amount' => $totalAmount]);

                DB::commit();

                return redirect()->route('purchaseorders.purchaseorder', $po_id)
                    ->with('success', 'Purchase order created successfully! PO ID: ' . $po_id);

            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Purchase order creation failed:', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'request_data' => $request->all(),
                ]);

                return redirect()->back()
                    ->with('error', 'Purchase order creation failed: ' . $e->getMessage())
                    ->withInput();
            }
        }

        public function confirmPurchaseOrder(Request $request, $po_id)
        {
            $user = Auth::user();
            
            if (!in_array($user->role, ['Staff', 'Admin'])) {
                return redirect()->back()->with('error', 'Only staff can confirm purchase orders.');
            }

            try {
                $request->validate([
                    'staff_quantities' => 'required|array',
                    'staff_quantities.*' => 'required|integer|min:0',
                    'action' => 'required|in:Accept,Reject',
                    'notes' => 'nullable|string|max:1000',
                ]);

                DB::beginTransaction();

                $purchaseOrder = PurchaseOrders::where('po_id', $po_id)->firstOrFail();
                
                if ($purchaseOrder->status !== 'Pending') {
                    return redirect()->back()->with('error', 'Purchase order is not in pending status.');
                }

                $totalAmount = 0;

                if ($request->action === 'Accept') {
                    // Update quantities and calculate new total
                    $acceptedItems = [];
                    foreach ($request->staff_quantities as $poItemId => $quantity) {
                        $item = PurchaseOrderItem::where('po_item_id', $poItemId)->first();
                        if ($item) {
                            $item->update([
                                'staff_quantity' => $quantity,
                                'total_price' => $item->unit_price * $quantity,
                                'status' => $quantity > 0 ? 'Accepted' : 'Rejected',
                            ]);
                            
                            if ($quantity > 0) {
                                $totalAmount += $item->total_price;
                                $acceptedItems[] = $item;
                            }
                        }
                    }

                    $purchaseOrder->update([
                        'status' => 'Accepted',
                        'total_amount' => $totalAmount,
                        'staff_id' => $user->user_id,
                        'confirmed_at' => now(),
                        'notes' => $request->notes,
                    ]);

                    // Create an order from the accepted purchase order
                    $orderId = $this->createOrderFromPurchaseOrder($purchaseOrder, $acceptedItems);
                } else {
                    // Reject the entire order
                    PurchaseOrderItem::where('po_id', $po_id)->update(['status' => 'Rejected']);
                    
                    $purchaseOrder->update([
                        'status' => 'Rejected',
                        'staff_id' => $user->user_id,
                        'confirmed_at' => now(),
                        'notes' => $request->notes,
                    ]);
                }

                DB::commit();

                if ($request->action === 'Accept') {
                    return redirect()->back()->with('success', "Purchase order has been accepted successfully! Order ID: {$orderId}");
                } else {
                    return redirect()->back()->with('success', "Purchase order has been rejected successfully!");
                }

            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Purchase order confirmation failed:', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'request_data' => $request->all(),
                ]);

                return redirect()->back()
                    ->with('error', 'Purchase order confirmation failed: ' . $e->getMessage());
            }
        }

        public function purchaseOrderPdf($po_id)
        {
            $purchaseOrder = PurchaseOrders::where('po_id', $po_id)
                ->with(['items.product', 'supplier', 'staff'])
                ->firstOrFail();

            $pdf = Pdf::loadView('pdf.purchase-orders.purchase_order', compact('purchaseOrder'));
            return $pdf->stream("purchase-order-{$po_id}.pdf");
        }

        private function createOrderFromPurchaseOrder($purchaseOrder, $acceptedItems)
        {
            try {
                $date = date('Ymd');
                $order_id = 'ORD-' . $date . '-' . $this->randomBase36String(5);
                
                // Create the main order
                $order = Orders::create([
                    'order_id' => $order_id,
                    'po_id' => $purchaseOrder->po_id,
                    'supplier_id' => $purchaseOrder->supplier_id,
                    'order_date' => now(),
                    'status' => 'Accepted', 
                    'total_amount' => $purchaseOrder->total_amount, 
                    'payment_status' => 'Unpaid', 

                ]);
                
                // Create order items from accepted purchase order items
                foreach ($acceptedItems as $poItem) {
                    $orderItemId = 'ORDR_ITEM-' . $date . '-' . $this->randomBase36String(5);

                    OrderItem::create([
                        'order_item_id' => $orderItemId,
                        'order_id' => $order_id,
                        'product_id' => $poItem->product_id,
                        'set_id' => $poItem->set_id,
                        'quantity' => $poItem->staff_quantity,
                        'unit_price' => $poItem->unit_price,
                        'total_price' => $poItem->total_price,
                        'status' => 'Accepted',
                    ]);
                }

                $date = date('Ymd');
                $log_id = 'LOG-' . $date . '-' . $this->randomBase36String(5);
                $history_id = 'OH-' . $date . '-' . $this->randomBase36String(5);

                $order = Orders::where('order_id', $order_id)->firstOrFail();
                $po = PurchaseOrders::where('po_id', $order->po_id)->firstOrFail();

                $data = [
                    'status'     => 'Accepted',
                    'updated_at' =>now(),
                ];

                $order->update($data);
                $po->update($data);


                $user_id = Auth::user()->user_id;


                Logs::create([
                    'user_id' => Auth::user()->user_id,
                    'action' => 'Commited on an order',
                    'log_id' => $log_id,
                    'description' => "Staff '{$user_id}' Accepted order '$order_id'",
                ]);

                OrderHistory::create([
                    'action_by' => Auth::user()->user_id,
                    'order_id' => $order_id,
                    'action_at' => now(),
                    'history_id' => $history_id,
                    'label' => 'Order',
                    'amount' => $order->total_amount,
                    'status' => 'Accepted',
                ]);

                \Log::info('Order created from purchase order', [
                    'po_id' => $purchaseOrder->po_id,
                    'order_id' => $order_id,
                    'items_count' => count($acceptedItems),
                    'total_amount' => $purchaseOrder->total_amount
                ]);

                return $order_id;

            } catch (\Exception $e) {
                \Log::error('Failed to create order from purchase order', [
                    'po_id' => $purchaseOrder->po_id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        }

        private function randomBase36String($length)
        {
            $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $result = '';
            for ($i = 0; $i < $length; $i++) {
                $result .= $characters[random_int(0, strlen($characters) - 1)];
            }
            return $result;
        }


        // public function storeOrderItems(Request $request, $po_id)
        // {
        //     $po = PurchaseOrders::findOrFail($po_id);

        //     // Create a new Order (if not already created)
        //     $order = Orders::create([
        //         'purchase_order_id' => $po->po_id,
        //         'user_id' => auth()->id(),
        //         // ...other fields as needed
        //     ]);

        //     foreach ($request->input('products', []) as $product_id => $data) {
        //         if (!empty($data['selected']) && $data['quantity'] > 0) {
        //             OrderItem::create([
        //                 'order_id' => $order->id,
        //                 'product_id' => $product_id,
        //                 'quantity' => $data['quantity'],
        //                 'price' => $data['price'] ?? null,
        //             ]);
        //         }
        //     }

        //     return redirect()->route('purchaseorders.purchaseorder', $po_id)->with('success', 'Order items saved!');
        // }

}
