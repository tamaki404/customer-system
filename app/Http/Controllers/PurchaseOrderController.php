<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Control;
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
use App\Models\SaleDiscount;

use Illuminate\Support\Str;
class PurchaseOrderController extends Controller
{

            public static function randomBase36String(int $length): string
        {
            $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $str = '';
            for ($i = 0; $i < $length; $i++) {
                $str .= $chars[random_int(0, strlen($chars) - 1)];
            }
            return $str;
        }
        public function purchaseOrderlist(Request $request)
        {
            $user = Auth::user();
            $supplier = null;
            $pos = collect();

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



                    $setProds = ProductSetting::where('supplier_id', $supplier->supplier_id)
                    ->with('product')
                    ->get() 
                    ->unique('product_id')
                    ->values()
                    ->map(function($setProduct) {
                        $activeSale = SaleDiscount::where('product_id', $setProduct->product_id)
                            ->whereDate('start_date', '<=', now())
                            ->whereDate('end_date', '>=', now())
                            ->first();

                        $setProduct->original_price = $setProduct->nego_price;
                        $setProduct->on_sale = false;

                        if ($activeSale) {
                            if ($activeSale->value_type === "Fixed") {
                                $setProduct->nego_price = $activeSale->value;
                                $setProduct->on_sale = true;
                            } elseif ($activeSale->value_type === "Percentage") {
                                $discountAmount = ($setProduct->original_price * $activeSale->value) / 100;
                                $setProduct->nego_price = $setProduct->original_price - $discountAmount;
                                $setProduct->on_sale = true;
                            }
                        }

                        return $setProduct;
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

        public function createPurchaseOrder(Request $request) {
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
                    'selected_products'   => 'required|array|min:1',
                    'selected_products.*' => 'exists:product_settings,set_id',
                    'placed_kilos'        => 'nullable|array',
                    'placed_kilos.*'      => 'nullable|numeric|min:0',
                    'notes'               => 'nullable|string|max:1000',
                ]);

                DB::beginTransaction();

                $date  = date('Ymd');
                $po_id = 'PO-' . $date . '-' . Str::upper(Str::random(5));

                // Create purchase order
                $purchaseOrder = PurchaseOrders::create([
                    'po_id'        => $po_id,
                    'supplier_id'  => $supplier->supplier_id,
                    'status'       => 'Pending',
                    'notes'        => $request->notes,
                    'total_amount' => 0,
                    'placed_at'    => now(),
                ]);

                $totalAmount = 0;

                foreach ($request->selected_products as $setId) {
                    $productSetting = ProductSetting::with('product')->where('set_id', $setId)->first();

                    if (!$productSetting) {
                        throw new \Exception("Invalid product setting for set_id: $setId");
                    }

                    // Get placed_heads and placed_kilos from request
                    $placedHeads = $request->placed_heads[$setId] ?? 0;
                    $placedKilos = $request->placed_kilos[$setId] ?? 0;

                    // Get the measurement type to determine which value to use for calculation
                    $measurementType = $productSetting->product->measurement_type;
                    
                    // Determine quantity for price calculation based on measurement type
                    if ($measurementType === 'Kilos') {
                        $quantityForCalculation = $placedKilos;
                        
                        if ($quantityForCalculation <= 0) {
                            throw new \Exception("Invalid kilos for product: {$productSetting->product->name}");
                        }
                    } else {
                        $quantityForCalculation = $placedHeads;
                        
                        if ($quantityForCalculation <= 0) {
                            throw new \Exception("Invalid heads for product: {$productSetting->product->name}");
                        }
                    }

                    $originalPrice  = $productSetting->nego_price;
                    $finalUnitPrice = $originalPrice;

                    // Check if there's an active sale
                    $activeSale = ProductSales::where('set_id', $setId)
                        ->whereDate('start_date', '<=', now())
                        ->whereDate('end_date', '>=', now())
                        ->first();

                    if ($activeSale) {
                        $finalUnitPrice = $activeSale->sale_price;
                    }

                    $itemTotal = $finalUnitPrice * $quantityForCalculation;

                    $poItemId = 'POI-' . $date . '-' . Str::upper(Str::random(5));

                    PurchaseOrderItem::create([
                        'po_item_id'        => $poItemId,
                        'po_id'             => $po_id,
                        'product_id'        => $productSetting->product_id,
                        'set_id'            => $setId,
                        'placed_heads'      => $placedHeads,
                        'placed_kilos'      => $placedKilos,
                        'alt_heads'      => $placedHeads,
                        'alt_kilos'      => $placedKilos,
                        'original_price'    => $originalPrice,
                        'unit_price'        => $finalUnitPrice,
                        'total_price'       => $itemTotal,
                        'status'            => 'Pending',
                    ]);

                    $totalAmount += $itemTotal;
                }

                // Update total
                $purchaseOrder->update(['total_amount' => $totalAmount]);

                DB::commit();

                return redirect()
                    ->route('purchaseorders.purchaseorder', ['po_id' => $po_id])
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

                $date = date('Ymd');
                $log_id = 'LOG-' . $date . '-' . $this->randomBase36String(5);


            try {
                $request->validate([
                    'action' => 'required|in:Accept,Reject',
                    'notes' => 'nullable|string|max:1000',
                ]);

                DB::beginTransaction();

                $purchaseOrder = PurchaseOrders::where('po_id', $po_id)->firstOrFail();
                
                if ($purchaseOrder->status !== 'Pending') {
                    return redirect()->back()->with('error', 'Purchase order is not in pending status.');
                }

                $orderId = null;

                if ($request->action === 'Accept') {
                    // Get all items for this purchase order
                    $poItems = PurchaseOrderItem::where('po_id', $po_id)->get();
                    
                    // Calculate total amount from existing items
                    $totalAmount = $poItems->sum('total_price');
                    
                    // Update all items status to Accepted
                    PurchaseOrderItem::where('po_id', $po_id)->update(['status' => 'Accepted']);

                    // Update purchase order
                    $purchaseOrder->update([
                        'status' => 'Accepted',
                        'total_amount' => $totalAmount,
                        'staff_id' => $user->user_id,
                        'confirmed_at' => now(),
                        'notes' => $request->notes,
                    ]);

                    // Create order from all items
                    $orderId = $this->createOrderFromPurchaseOrder($purchaseOrder, $poItems);

                    Logs::create([
                        'user_id'     => $user->user_id,
                        'action'      => 'Accepted a PO',
                        'log_id'      => $log_id,
                        'description' => " Staff ($user->user_id)
                        accepted '($po_id)'",
                        'entity'      => 'PurchaseOrders',
                        'entity_id'   => $purchaseOrder->id,
                    ]);
                    
                } else {
                    // Reject all items
                    PurchaseOrderItem::where('po_id', $po_id)->update(['status' => 'Rejected']);
                    
                    $purchaseOrder->update([
                        'status' => 'Rejected',
                        'staff_id' => $user->user_id,
                        'confirmed_at' => now(),
                        'notes' => $request->notes,
                    ]);

                    Logs::create([
                        'user_id'     => $user->user_id,
                        'action'      => 'Rejected a PO',
                        'log_id'      => $log_id,
                        'description' => " Staff ($user->user_id)
                        rejected '($po_id)'",
                        'entity'      => 'PurchaseOrders',
                        'entity_id'   => $purchaseOrder->id,
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

        private function createOrderFromPurchaseOrder($purchaseOrder, $poItems)
        {
            try {
                $date = date('Ymd');
                $order_id = 'ORD-' . $date . '-' . $this->randomBase36String(5);
                
                // Calculate total amount from items
                $totalAmount = $poItems->sum('total_price');

                // Create the main order
                $order = Orders::create([
                    'order_id' => $order_id,
                    'po_id' => $purchaseOrder->po_id,
                    'supplier_id' => $purchaseOrder->supplier_id,
                    'order_date' => now(),
                    'status' => 'Accepted', 
                    'total_amount' => $totalAmount, 
                    'payment_status' => 'Unpaid', 
                ]);
                
                // Create order items from purchase order items
                foreach ($poItems as $poItem) {
                    $orderItemId = 'ORDR_ITEM-' . $date . '-' . $this->randomBase36String(5);

                    OrderItem::create([
                        'order_item_id' => $orderItemId,
                        'order_id' => $order_id,
                        'product_id' => $poItem->product_id,
                        'set_id' => $poItem->set_id,
                        'placed_heads' => $poItem->placed_heads,
                        'placed_kilos' => $poItem->placed_kilos,
                        'unit_price' => $poItem->unit_price,
                        'total_price' => $poItem->total_price,
                        'status' => 'Accepted',
                    ]);
                }
                // Create order history
                $history_id = 'OH-' . $date . '-' . $this->randomBase36String(5);
                
                OrderHistory::create([
                    'action_by' => Auth::user()->user_id,
                    'order_id' => $order_id,
                    'action_at' => now(),
                    'history_id' => $history_id,
                    'label' => 'Order',
                    'amount' => $totalAmount,
                    'status' => 'Accepted',
                ]);

                \Log::info('Order created from purchase order', [
                    'po_id' => $purchaseOrder->po_id,
                    'order_id' => $order_id,
                    'items_count' => $poItems->count(),
                    'total_amount' => $totalAmount
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

        // public function confirmPurchaseOrder(Request $request, $po_id)
        // {
        //     $user = Auth::user();

        //     $user_id = Auth::user()->user_id;

        //     if (!in_array($user->role, ['Staff', 'Admin'])) {
        //         return redirect()->back()->with('error', 'Only staff can confirm purchase orders.');
        //     }

        //     try {
        //         $request->validate([
        //             'action' => 'required|in:Accept,Reject',
        //             'notes' => 'nullable|string|max:1000',
        //         ]);

        //         DB::beginTransaction();

        //         $purchaseOrder = PurchaseOrders::where('po_id', $po_id)->firstOrFail();

        //         if ($purchaseOrder->status !== 'Pending') {
        //             return redirect()->back()->with('error', 'Purchase order is not in pending status.');
        //         }

        //         $itemStatus = $request->action === 'Accept' ? 'Accepted' : 'Rejected';
        //         PurchaseOrderItem::where('po_id', $po_id)->update(['status' => $itemStatus]);

        //         $purchaseOrder->update([
        //             'status'        => $request->action === 'Accept' ? 'Accepted' : 'Rejected',
        //             'staff_id'      => $user->user_id,
        //             'confirmed_at'  => now(),
        //             'notes'         => $request->notes,
        //         ]);

        //             $date = date('Ymd');
        //             function randomBase36String(int $length): string {
        //                 $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        //                 $str = '';
        //                 for ($i = 0; $i < $length; $i++) {
        //                     $str .= $chars[random_int(0, strlen($chars) - 1)];
        //                 }
        //                 return $str;
        //             }
        //         $log_id = 'LOG-' . $date . '-' . randomBase36String(5);
        //         Logs::create([
        //             'user_id'     => $user_id,
        //             'action'      => 'Confirmed a PO',
        //             'log_id'      => $log_id,
        //             'description' => "Staff( $user_id) confirmed" .$po_id,
        //             'entity'      => 'PurchaseOrders',
        //             'entity_id'   => $purchaseOrder->id,
        //         ]);

        //         DB::commit();

        //         $message = $request->action === 'Accept'
        //             ? 'Purchase order has been accepted successfully!'
        //             : 'Purchase order has been rejected successfully!';

        //         return redirect()->back()->with('success', $message);

        //     } catch (\Exception $e) {
        //         DB::rollBack();

        //         \Log::error('Purchase order confirmation failed:', [
        //             'error' => $e->getMessage(),
        //             'trace' => $e->getTraceAsString(),
        //             'request_data' => $request->all(),
        //         ]);

        //         return redirect()->back()
        //             ->with('error', 'Purchase order confirmation failed: ' . $e->getMessage());
        //     }
        // }


        public function purchaseOrderPdf($po_id)
        {
            $purchaseOrder = PurchaseOrders::where('po_id', $po_id)
                ->with(['items.product', 'supplier', 'staff'])
                ->firstOrFail();

            $pdf = Pdf::loadView('pdf.purchase-orders.purchase_order', compact('purchaseOrder'));
            return $pdf->stream("purchase-order-{$po_id}.pdf");
        }






}
