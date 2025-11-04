<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Control;
use App\Models\Credits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Orders;
use App\Models\PurchaseOrders;
use App\Models\PurchaseOrderItem;
use App\Models\Customers;
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
            $customer = null;
            $pos = collect();
            $setProds = collect(); 

            if ($user->role === "Customer") {
                $customer = Customers::where('user_id', $user->user_id)->first();
                if ($customer) {
                    $query = PurchaseOrders::where('customer_id', $customer->customer_id)
                        ->with(['items', 'customer']);

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

                    // Get products with sale prices
                    // Get products with sale prices
                    $setProds = ProductSetting::where('customer_id', $customer->customer_id)
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
                            $setProduct->activeSale = NULL; // Initialize

                            if ($activeSale && $activeSale->quantity > 0) {
                                // Only apply sale if quantity is available
                                if ($activeSale->value_type === "Fixed") {
                                    $setProduct->nego_price = $activeSale->value;
                                    $setProduct->on_sale = true;
                                } elseif ($activeSale->value_type === "Percentage") {
                                    $discountAmount = ($setProduct->original_price * $activeSale->value) / 100;
                                    $setProduct->nego_price = $setProduct->original_price - $discountAmount;
                                    $setProduct->on_sale = true;
                                }
                                $setProduct->activeSale = $activeSale; // Attach the sale object
                            } elseif ($activeSale && $activeSale->quantity <= 0) {
                                // Sale exists but quantity depleted - use original price
                                $setProduct->nego_price = $setProduct->original_price;
                                $setProduct->on_sale = false;
                                $setProduct->activeSale = $activeSale; // Still attach for reference
                            }

                            return $setProduct;
                        });
                }
                $credit = Credits::where('user_id', $user->user_id)->first();
                $usedCredit = Orders::where('customer_id', $customer->customer_id)
                    ->whereIn('payment_status', ['Unpaid', 'Partially Settled'])
                    ->selectRaw('
                        SUM(
                            orders.total_amount - COALESCE(
                                (SELECT SUM(r.total_amount) 
                                FROM receipts r 
                                WHERE r.order_id = orders.order_id 
                                AND r.status = "Verified"), 0
                            )
                        ) as outstanding_balance
                    ')
                    ->value('outstanding_balance');

                $availableCredit = $credit->credit_limit - $usedCredit;
                $creditLimit      = $credit->credit_limit;
                $exceedAllowance  = $creditLimit * 0.20;
                $exceedLimit      = $creditLimit + $exceedAllowance;

            } 
            elseif ($user->role === "Staff" || $user->role === "Admin") {
                $query = PurchaseOrders::with(['items', 'customer', 'staff']);

                if ($request->filled('search')) {
                    $search = $request->search;
                    $query->where(function($q) use ($search) {
                        $q->where('po_id', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhereHas('customer', function($customerQuery) use ($search) {
                            $customerQuery->where('company_name', 'like', "%{$search}%");
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
                'customer' => $customer,
                'setProds' => $setProds, 
                'pos' => $pos,
                'availableCredit' => $availableCredit,
                'exceedLimit' => $exceedLimit,

            ]);
        }
        public function purchaseOrderView($po_id, Request $request)
        {
            $user = Auth::user();
            $po = PurchaseOrders::where('po_id', $po_id)->with(['items.product', 'customer'])->first(); 
            
            if ($user->role === "Customer") {
                $setProducts = ProductSetting::where('customer_id', $po->customer_id)
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
        
            if ($user->role !== "Customer") {
                return redirect()->back()->with('error', 'Only customers can create purchase orders.');
            }
        
            $customer = Customers::where('user_id', $user->user_id)->first();
        
            if (!$customer) {
                return redirect()->back()->with('error', 'Customer profile not found.');
            }
        
            // Fetch user credit
            $credit = Credits::where('user_id', $user->user_id)->first();
        
            if (!$credit) {
                return redirect()->back()->with('error', 'Credit profile not found.');
            }
        
            // Fetch outstanding unpaid balances
            $usedCredit = Orders::where('customer_id', $customer->customer_id)
                ->whereIn('payment_status', ['Unpaid', 'Partially Settled'])
                ->selectRaw('
                    SUM(
                        orders.total_amount - COALESCE(
                            (SELECT SUM(r.total_amount) 
                            FROM receipts r 
                            WHERE r.order_id = orders.order_id 
                            AND r.status = "Verified"), 0
                        )
                    ) as outstanding_balance
                ')
                ->value('outstanding_balance') ?? 0;

            $creditLimit     = $credit->credit_limit;
            $exceedAllowance = $creditLimit * 0.20;
            $maxAllowed      = $creditLimit + $exceedAllowance;
                
            try {
                $request->validate([
                    'selected_products'   => 'required|array|min:1',
                    'selected_products.*' => 'exists:product_settings,set_id',
                    'placed_kilos'        => 'nullable|array',
                    'placed_kilos.*'      => 'nullable|numeric|min:0',
                    'placed_heads'        => 'nullable|array',
                    'placed_heads.*'      => 'nullable|numeric|min:0',
                    'notes'               => 'nullable|string|max:1000',
                ]);
            
                DB::beginTransaction();
            
                $date  = date('Ymd');
                $po_id = 'PO-' . $date . '-' . Str::upper(Str::random(5));
            
                $purchaseOrder = PurchaseOrders::create([
                    'po_id'        => $po_id,
                    'customer_id'  => $customer->customer_id,
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

            $placedHeads = $request->placed_heads[$setId] ?? 0;
            $placedKilos = $request->placed_kilos[$setId] ?? 0;
            $measurementType = $productSetting->product->measurement_type;

            if ($measurementType === 'Heads&Kilos') {
                $quantityForCalculation = $placedKilos;
                if ($placedKilos <= 0 || $placedHeads <= 0) {
                    throw new \Exception("Both heads & kilos are required for {$productSetting->product->name}");
                }
            } elseif ($measurementType === 'Kilos') {
                $quantityForCalculation = $placedKilos;
                if ($quantityForCalculation <= 0) {
                    throw new \Exception("Kilos is required for {$productSetting->product->name}");
                }
                $placedHeads = 0;
            } elseif ($measurementType === 'Heads') {
                $quantityForCalculation = $placedHeads;
                if ($quantityForCalculation <= 0) {
                    throw new \Exception("Heads is required for {$productSetting->product->name}");
                }
                $placedKilos = 0;
            } else {
                throw new \Exception("Invalid measurement type for {$productSetting->product->name}");
            }

            $originalPrice = $productSetting->nego_price;
            $finalUnitPrice = $originalPrice;

            $activeSale = SaleDiscount::where('product_id', $productSetting->product_id)
                ->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
                ->first();

            if ($activeSale && $activeSale->quantity > 0) {
                $availableQuantity = $activeSale->quantity;

                if ($quantityForCalculation > $availableQuantity) {
                    throw new \Exception(
                        "Insufficient sale quantity for {$productSetting->product->name}. Only {$availableQuantity} left."
                    );
                }

                if ($activeSale->value_type === "Fixed") {
                    $finalUnitPrice = $activeSale->value;
                } elseif ($activeSale->value_type === "Percentage") {
                    $discountAmount = ($originalPrice * $activeSale->value) / 100;
                    $finalUnitPrice = $originalPrice - $discountAmount;
                }

                $activeSale->update([
                    'quantity' => $availableQuantity - $quantityForCalculation
                ]);
            }

            $itemTotal = $finalUnitPrice * $quantityForCalculation;
            $poItemId  = 'POI-' . $date . '-' . Str::upper(Str::random(5));

            PurchaseOrderItem::create([
                'po_item_id'      => $poItemId,
                'po_id'           => $po_id,
                'product_id'      => $productSetting->product_id,
                'set_id'          => $setId,
                'placed_heads'    => $placedHeads,
                'placed_kilos'    => $placedKilos,
                'alt_heads'       => $placedHeads,
                'alt_kilos'       => $placedKilos,
                'original_price'  => $originalPrice,
                'unit_price'      => $finalUnitPrice,
                'total_price'     => $itemTotal,
                'status'          => 'Pending',
            ]);

            $totalAmount += $itemTotal;
        }

        // ✅ CREDIT LIMIT VALIDATION HERE
        $newUsage = $usedCredit + $totalAmount;

        if ($newUsage > $maxAllowed) {
            throw new \Exception(
                "This purchase will exceed your available credit capacity. Maximum allowed: ₱"
                . number_format($maxAllowed, 2)
            );
        }

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
                    'customer_id' => $purchaseOrder->customer_id,
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
                ->with(['items.product', 'customer', 'staff'])
                ->firstOrFail();

            $pdf = Pdf::loadView('pdf.purchase-orders.purchase_order', compact('purchaseOrder'));
            return $pdf->stream("purchase-order-{$po_id}.pdf");
        }






}
