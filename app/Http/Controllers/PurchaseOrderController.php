<?php

namespace App\Http\Controllers;

<<<<<<< HEAD
use App\Http\Controllers\Control;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Orders;
use App\Models\PurchaseOrders;
=======
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\PurchaseOrder;
>>>>>>> parent of 54b5d0c3 (Add revised system code)
use App\Models\PurchaseOrderItem;
use App\Models\Receipt;
use App\Models\Invoice;
use App\Models\Orders;
use Carbon\Traits\Timestamp;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Region;
use Barryvdh\DomPDF\Facade\Pdf;
<<<<<<< HEAD
use App\Models\Logs;
use App\Models\OrderHistory;
use App\Models\ProductSales;
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

                $setProds = ProductSetting::where('supplier_id', $supplier->supplier_id)
                    ->with('product')
                    ->get()
                    ->map(function($setProduct) {
                        $activeSale = ProductSales::where('set_id', $setProduct->set_id)
                            ->whereDate('start_date', '<=', now())
                            ->whereDate('end_date', '>=', now())
                            ->first();

                        // Store both prices
                        $setProduct->original_price = $setProduct->nego_price;
                        if ($activeSale) {
                            $setProduct->nego_price = $activeSale->sale_price;
                            $setProduct->on_sale = true;
                        } else {
                            $setProduct->on_sale = false;
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
=======
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class PurchaseOrderController extends Controller {

    public function purchaseOrder()
    {
        $user = auth()->user();
        $search = request('search');
        $from = request('from_date', now()->startOfMonth()->format('Y-m-d'));
        $to = request('to_date', now()->endOfMonth()->format('Y-m-d'));
        $filterStatus = request('status');

        $baseQuery = PurchaseOrder::query();

        if ($search) {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('po_id', 'like', "%{$search}%")
                ->orWhere('receiver_name', 'like', "%{$search}%")
                ->orWhere('company_name', 'like', "%{$search}%")
                ->orWhere('order_date', 'like', "%{$search}%")
                ->orWhere('payment_status', 'like', "%{$search}%");
            });
        }

        if (!in_array($user->user_type, ['Staff', 'Admin'])) {
            $baseQuery->where('user_id', $user->id);
        }

        if ($from && $to) {
            $baseQuery->whereBetween('order_date', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay()
>>>>>>> parent of 54b5d0c3 (Add revised system code)
            ]);
        }
        

<<<<<<< HEAD
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
=======
        $statusCounts = [];
        $tabStatuses = [
            'All' => null,
            'Draft' => 'Draft',
            'Pending' => 'Pending',
            'Processing' => 'Processing',
            'Delivered' => 'Delivered',
            'Cancelled' => 'Cancelled',
            'Rejected' => 'Rejected',
            'Unpaid' => 'Unpaid',
            'Partially Settled' => 'Partially Settled',
            'Fully Paid' => 'Fully Paid',
        ];

        $statusCounts['All'] = (clone $baseQuery)->where('status', '!=', 'Draft')->count();

        foreach ($tabStatuses as $label => $value) {
            if ($value !== null) {
                $countQuery = clone $baseQuery;
                $countQuery->where(function ($q) use ($value) {
                    $q->where('status', $value)->orWhere('payment_status', $value);
                });
                
                if ($value !== 'Draft') {
                    $countQuery->where('status', '!=', 'Draft');
                }
                
                $statusCounts[$label] = $countQuery->count();
            }
        }

        $query = clone $baseQuery;

        if ($filterStatus) {
            $query->where(function ($q) use ($filterStatus) {
                $q->where('status', $filterStatus)
                ->orWhere('payment_status', $filterStatus);
            });
        }

        if ($filterStatus !== 'Draft') {
            $query->where('status', '!=', 'Draft');
        }

        $purchaseOrders = $query
            ->select('purchase_orders.*')
            ->selectRaw('
                (
                    SELECT SUM(CASE 
                        WHEN poi.new_quantity IS NOT NULL THEN poi.new_quantity 
                        ELSE poi.quantity 
                    END)
                    FROM purchase_order_items poi
                    WHERE poi.po_id = purchase_orders.po_id
                ) as total_quantity
            ')
            ->orderBy('order_date', 'desc')
            ->paginate(50);

        // calculate remaining balance for each PO (only Verified receipts count)
        foreach ($purchaseOrders as $po) {
            $paidAmount = Receipt::where('po_id', $po->po_id)
                ->where('status', 'Verified')
                ->sum('total_amount') ?? 0;
            $po->remaining_balance = max($po->grand_total - $paidAmount, 0);
        }

        return view('purchase_order', compact(
            'user',
            'purchaseOrders',
            'search',
            'from',
            'to',
            'filterStatus',
            'statusCounts' 
        ));
    }
            
    public function purchaseReceipts($po_id)
    {
        $user = auth()->user();

        $receipts = Receipt::where('po_id', $po_id)->get();

        return view('purchase_orders/receipts_purchase_order', compact('user', 'receipts'));
    }


    
    public function cancelOrder($order_id)
    {
        $user = auth()->user();
        $ownerId = Orders::where('order_id', $order_id)->value('customer_id');
        if (!in_array($user->user_type, ['Admin', 'Staff']) && $ownerId !== $user->id) {
            abort(403, 'Unauthorized action');
        }

        try {
            DB::beginTransaction();
            
            $orderItems = Orders::where('order_id', $order_id)->get();
            
            foreach ($orderItems as $orderItem) {
                $product = Product::find($orderItem->product_id);
                if ($product) {
                    $product->quantity += $orderItem->quantity;
                    $product->save();
                }
            }
            
            Orders::where('order_id', $order_id)->update([
                'status' => 'Cancelled',
                'action_at' => now(),
                'action_by' => $user->name,
            ]);
            
            DB::commit();
            
            return redirect()->route('orders.view', $order_id)
                ->with('success', 'Order cancelled successfully! Product quantities have been restored.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error cancelling order: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to cancel order. Please try again.');
        }
    }

    public function productSearch()
    {
        $search = request('search');
        $query = Product::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('product_id', 'like', "%{$search}%")
                ->orWhere('status', 'like', "%{$search}%"); 
            });
        }

        $products = $query->orderBy('created_at', 'desc');

        return view('store_create_order', compact('search', 'products'));
    }



    public function purchaseOrderForm($po_id)
    {
        $user = auth()->user();
        $order = PurchaseOrder::where('po_id', $po_id)->firstOrFail();

        $ordersItem = PurchaseOrderItem::where('po_id', $po_id)
            ->orderBy('created_at', 'desc') 
            ->get();

        return view('purchase_orders.purchase_order_form', compact('user', 'order', 'ordersItem'));
    }

    public function invoiceView($po_id)
    {
        $user = auth()->user();
        $order = PurchaseOrder::where('po_id', $po_id)->firstOrFail();
        $invoice = Invoice::where('po_id', $po_id)->firstOrFail();
        $invoiceItems = PurchaseOrderItem::where('po_id', $po_id)
            ->orderBy('created_at', 'desc') 
            ->get();

        return view('purchase_orders.invoice-view', compact('user', 'order', 'invoiceItems', 'invoice'));
    }

    public function downloadPDF($po_id)
    {
        $user = auth()->user();
        $order = PurchaseOrder::where('po_id', $po_id)->firstOrFail();

        $ordersItem = PurchaseOrderItem::where('po_id', $po_id)
            ->orderBy('created_at', 'desc')
            ->get();

        $pdf = Pdf::loadView('purchase_orders.purchase_order_pdf', compact('user', 'order', 'ordersItem'));
        return $pdf->download("PurchaseOrder-{$order->po_id}.pdf");
    }


    public function storeOrderView()
    {
        $user = auth()->user();
        $search = request('search');

        $query = Product::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('product_id', 'like', "%{$search}%");
            });
        }

        $products = $query
            ->select('products.*')
            ->selectSub(function ($q) {
                $q->from('orders')
                ->selectRaw('COALESCE(SUM(quantity), 0)') 
                ->whereColumn('orders.product_id', 'products.id');
            }, 'sold_quantity')
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString(); 
        $regions = Region::orderBy('region_name')
            ->get(['region_id','region_name']);

        return view('purchase_orders.store_create_order', compact('user', 'products', 'search', 'regions'));
    }

    private function randomBase36String($length = 5)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $randomString;
    }

    public function purchaseOrderView($po_id)
    {   
        $po = PurchaseOrder::where('po_id', $po_id)->firstOrFail();
        $order = PurchaseOrder::where('po_id', $po_id)->firstOrFail();

        $ordersItem = PurchaseOrderItem::where('po_id', $po_id)
            ->orderBy('created_at', 'desc')
            ->get();

        $orderCount = PurchaseOrderItem::where('po_id', $po_id)->count();

        return view('purchase_orders.purchase_order_view', compact('po','order', 'ordersItem', 'orderCount' ));
    }


    public function store(Request $request){
    $user = auth()->user();

    $request->validate([
        'receiver_name'   => 'required',
        'company_name'    => 'required|string|max:255',
        'postal_code'     => 'nullable|string|max:10',
        'region'          => 'required|exists:region,region_id',
        'province'        => 'required|exists:province,province_id',
        'municipality'    => 'required|exists:municipality,municipality_id',
        'barangay'        => 'required|exists:barangay,barangay_id',
        'street'          => 'required|string|max:255',
        'billing_address' => 'required',
        'cart_data'       => 'required',
        'order_notes'     => 'nullable|string|max:500',
        'receiver_mobile' => 'required|string|max:15',
        
    ]);


    $cart = json_decode($request->cart_data, true);

    if (empty($cart)) {
        return back()->withErrors(['cart_data' => 'Cart cannot be empty.'])->withInput();
    }

    try {
        DB::beginTransaction();

        foreach ($cart as $item) {
            $product = Product::find($item['id']);
            
            if (!$product) {
>>>>>>> parent of 54b5d0c3 (Add revised system code)
                DB::rollBack();
                return back()->withErrors(['error' => "Product with ID {$item['id']} not found."])->withInput();
            }
            
            if ($product->quantity < $item['quantity']) {
                DB::rollBack();
                return back()->withErrors([
                    'error' => "Product '{$product->name}' is out of stock or insufficient quantity. Available: {$product->quantity}, Requested: {$item['quantity']}"
                ])->withInput();
            }
        }

<<<<<<< HEAD
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
=======
        $subtotal = collect($cart)->sum(fn($item) => floatval($item['price']) * intval($item['quantity']));
        $tax = 0; 
        $grandTotal = $subtotal + $tax;

        $attachmentPath = null;
        if ($request->hasFile('po_attachment')) {
            $attachmentPath = $request->file('po_attachment')->store('attachments', 'public');
        }

        $date = date('Ymd');
        $po_id = 'PO-' . $date . '-' . $this->randomBase36String(5);
        $status = $request->input('status', 'Pending');

        $po = PurchaseOrder::create([
            'user_id'         => $user->id,
            'po_id'       => $po_id,
            'receiver_name'   => $request->receiver_name,
            'receiver_mobile' => $request->receiver_mobile,
            'postal_code'     => $request->postal_code,
            'region_id'       => $request->region,
            'province_id'     => $request->province,
            'municipality_id' => $request->municipality,
            'barangay_id'     => $request->barangay,
            'street'          => $request->street,
            'company_name'    => $request->company_name,
            'billing_address' => $request->billing_address,

            'order_notes'     => $request->order_notes,
            'subtotal'        => $subtotal,
            'tax_amount'      => $tax,
            'grand_total'     => $grandTotal,
            'status'          => $status,
            'order_date'      => Carbon::now(),
            'payment_status' => 'Unpaid'
        ]);

    
        

            $order_id = 'ORD-' . $date . '-' . $this->randomBase36String(5);

            if ($status !== 'Draft') {
            $order_id = 'ORD-' . $date . '-' . $this->randomBase36String(5);

            foreach ($cart as $item) {
                $product = Product::find($item['id']);

                if (!$product) {
                    throw new \Exception("Product with ID {$item['id']} not found.");
                }

                // Create order record
                Orders::create([
                    'po_id'       => $po->po_id,
                    'order_id'    => $order_id,
                    'customer_id' => $user->id,
                    'product_id'  => $product->id,
                    'quantity'    => $item['quantity'],
                    'unit_price'  => $product->price,
                    'total_price' => $product->price * $item['quantity'],
                    'status'      => $status,
                ]);

            $poi_id = 'POI-' . $date . '-' . $this->randomBase36String(5). '-' .$this->randomBase36String(5);

                // Create purchase order item
                PurchaseOrderItem::create([
                    'po_id'       => $po->po_id,
                    'product_id'  => $item['id'],
                    'poi_id'      => $poi_id,
                    'order_id'    => $order_id,
                    'quantity'    => intval($item['quantity']),
                    'unit_price'  => floatval($item['price']),
                    'total_price' => floatval($item['price']) * intval($item['quantity']),
                ]);

>>>>>>> parent of 54b5d0c3 (Add revised system code)

                $product->quantity -= $item['quantity'];

                // Update status based on new quantity
                if ($product->quantity === '0') {
                    $product->status = "No stock";
                } elseif ($product->quantity < 20) {
                    $product->status = "Low stock";
                } else {
                    $product->status = "Available";
                }

                $product->save();




            }
        }

<<<<<<< HEAD
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
=======
        DB::commit(); 

        return redirect()->route('purchase_order')->with('success', 'Purchase order placed successfully!');

    } catch (\Throwable $e) {
        DB::rollBack(); 
        Log::error('Purchase Order creation error: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        
        return back()->withErrors(['error' => 'Failed to place order: ' . $e->getMessage()])->withInput();
    }
    }

    public function modifyQuantity(){
        
    }


    // public function cancelPurchaseOrder(Request $request, $id)
    // {
    //     try {
    //         $purchaseOrder = PurchaseOrder::findOrFail($id);
            
    //         // Only allow cancellation if order is still pending/processing
    //         if (!in_array($purchaseOrder->status, ['Pending', 'Processing'])) {
    //             return back()->withErrors(['error' => 'Cannot cancel order with status: ' . $purchaseOrder->status]);
    //         }

    //         DB::beginTransaction();

    //         $purchaseOrderItems = PurchaseOrderItem::where('po_id', $purchaseOrder->po_id)->get();
            
    //         foreach ($purchaseOrderItems as $item) {
    //             $product = Product::find($item->product_id);
    //             if ($product) {
    //                 // Restore quantity back to inventory
    //                 $product->quantity += $item->quantity;
    //                 $product->save();
    //             }
    //         }

    //         $purchaseOrder->status = 'Cancelled';
    //         $purchaseOrder->save();

    //         Orders::where('po_id', $purchaseOrder->po_id)->update(['status' => 'Cancelled']);

    //         DB::commit();

    //         return redirect()->route('purchase_order')->with('success', 'Purchase order cancelled and inventory restored successfully!');

    //     } catch (\Throwable $e) {
    //         DB::rollBack();
    //         Log::error('Purchase Order cancellation error: ' . $e->getMessage());
            
    //         return back()->withErrors(['error' => 'Failed to cancel purchase order: ' . $e->getMessage()]);
    //     }
    // }

    public function changeStatus(Request $request)
    {
        $po = PurchaseOrder::findOrFail($request->po_id);
        $status = $request->input('status'); 
        $user = $request->input('user_id');

        $po->status = $status;

        if (in_array($status, ['Cancelled', 'Rejected'])) {
            $purchaseOrderItems = PurchaseOrderItem::where('po_id', $po->id)->get();

            foreach ($purchaseOrderItems as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->quantity += $item->quantity;
                    $product->save();
                }
            }
>>>>>>> parent of 54b5d0c3 (Add revised system code)
        }

        if ($status === "Accepted") {
            $po->approved_by = $user; 
            $po->approved_at = now(); 
            
        } 
        elseif ($status === "Delivered") {
            $po->delivered_at = now(); 

<<<<<<< HEAD



=======
            $date = date('Ymd');
            $invoice_number = 'INV-' . $date . '-' . $this->randomBase36String(5);

            $invoice = new Invoice();
            $invoice->po_id = $po->po_id; 
            $invoice->user_id = $po->user_id; 

            $invoice->invoice_number = $invoice_number;
            $invoice->delivered_at = $po->delivered_at; 
            $invoice->billing_address = $po->billing_address; 
            $invoice->subtotal = $po->subtotal; 
            $invoice->tax_amount = $po->tax_amount;
            $invoice->grand_total = $po->grand_total;
            $invoice->status = 'unpaid';
            $invoice->save();
        } 
        elseif ($status === "Cancelled") {
            $po->cancelled_at = now(); 
            $po->cancelled_by = $user;
        } 
        elseif ($status === "Rejected") {
            $po->rejected_at = now(); 
            $po->rejected_by = $user; 

        }
>>>>>>> parent of 54b5d0c3 (Add revised system code)

        $po->save();

        return back()->with('success', "Purchase order has been {$status}.");
    }

public function cancelPOStatus(Request $request)
{
    $po = PurchaseOrder::where('po_id', $request->po_id)->firstOrFail();
    $status = $request->input('status');
    
    $user = $request->input('user_id');
    $user_type = $request->input('user_type');
    
    if (in_array($po->status, ['Cancelled', 'Rejected'])) {
        return back()->with('error', 'Purchase order is already cancelled/rejected.');
    }
    
    $po->status = $status;
    
    if (in_array($status, ['Cancelled', 'Rejected'])) {
        $purchaseOrderItems = PurchaseOrderItem::where('po_id', $po->po_id)->get();
        
        foreach ($purchaseOrderItems as $item) {
            $product = Product::find($item->product_id);
            if ($product) {
                $product->quantity += $item->quantity;
                $product->save();
            }
        }
    }
    
    // Set cancelled metadata
    if ($status === "Cancelled") {
        $po->cancelled_at = now();
        $po->cancelled_by = $user;
        $po->cancelled_user_type = $user_type;
    }
    
    $po->save();
    return back()->with('success', 'Purchase order saved successfully.');
}
     
}