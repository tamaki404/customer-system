<?php

namespace App\Http\Controllers;

<<<<<<< HEAD
use App\Models\Delivery;
use App\Models\DeliveryItems;
use App\Models\Staffs;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
=======
>>>>>>> parent of 54b5d0c3 (Add revised system code)
use Illuminate\Http\Request;
use App\Models\Orders;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
<<<<<<< HEAD
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
        public function orderList(Request $request)
        {
            $user = Auth::user();

            $supplier = null;
            $orders = collect(); 

            if ($user->role !== "Supplier") {
                $orders = Orders::withSum('receipts', 'total_amount')
                    ->get()
                    ->map(function ($order) {
                        $paid = $order->receipts_sum_total_amount ?? 0;
                        $balance = $order->total_amount - $paid;


                        if ($paid >= $order->total_amount) {
                            $order->payment_status = 'Fully paid';
                        } elseif ($paid > 0) {
                            $order->payment_status = 'Partially settled';
                        } else {
                            $order->payment_status = 'Unpaid';
                        }

                        $order->paid_amount = $paid;
                        $order->balance = max($balance, 0);

                        return $order;
                    });
            } 
            elseif ($user->role === "Supplier") {
                $supplier = Suppliers::where('user_id', $user->user_id)->first();

                $orders = Orders::where('supplier_id', $supplier->supplier_id)
                    ->with([
                        'receipts', 
                        'deliveries' => function ($q) {
                            $q->with('deliveryItems');
                        }
                    ])
                    ->withSum('receipts', 'total_amount')
                    ->get()
                    ->map(function ($order) {
                        // --- Payment Status ---
                        $paid = $order->receipts_sum_total_amount ?? 0;
                        $order->running_balance = max($order->total_amount - $paid, 0);

                        if ($paid >= $order->total_amount) {
                            $order->payment_status = 'Fully Paid';
                        } elseif ($paid > 0) {
                            $order->payment_status = 'Partially settled';
                        } else {
                            $order->payment_status = 'Unpaid';
                        }

                        // --- Delivery Completion ---
                        $total = $order->deliveries->count();
                        $completed = $order->deliveries->where('status', 'Completed')->count();
                        $completionRatio = $total > 0 ? "{$completed}/{$total}" : "0/0";



                        return $order;
                    });
            }



            return view('orders.list', [
                'user' => $user,
                'supplier' => $supplier,
                'orders' => $orders,
=======
{
    public function checkout(Request $request)
    {
        try {
            $user = auth()->user();
            $validated = $request->validate([
                'items' => 'required|array|min:1',
                'items.*.id' => 'required|exists:products,id',
                'items.*.qty' => 'required|integer|min:1|max:999',
                'total' => 'nullable|numeric|min:0',
>>>>>>> parent of 54b5d0c3 (Add revised system code)
            ]);
            $items = $validated['items'];
            $total = $validated['total'] ?? 0;

            // Validate user
            if (!$user || $user->user_type !== 'Customer') {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            // Validate cart
            if (empty($items)) {
                return response()->json(['success' => false, 'message' => 'Cart is empty.'], 400);
            }

            // Use database transaction for data consistency
            DB::beginTransaction();

            try {
<<<<<<< HEAD
                $imageBlob = null;
                $imageMimeType = null;
                $imageFilename = null;
                $imageSize = null;
                
                if ($request->hasFile('image')) {
                    $image = $request->file('image');
                    
                    $imageBlob = file_get_contents($image->getRealPath());
                    $imageMimeType = $image->getMimeType();
                    $imageFilename = $image->getClientOriginalName();
                    $imageSize = $image->getSize();
                }

                if ($request->hasFile('image')) {
                    $image = $request->file('image');
                    \Log::info('Uploaded file details:', [
                        'name' => $image->getClientOriginalName(),
                        'mime' => $image->getMimeType(),
                        'size' => $image->getSize(),
                        'path' => $image->getRealPath(),
                    ]);
                }


                $purchaseOrder = PurchaseOrders::create([
                    'po_id'       => $po_id,
                    'supplier_id' => $request->supplier_id,
                    'status' => $request->status,
                    'image'         => $imageBlob,
                    'image_mime_type' => $imageMimeType,
                    'image_filename' => $imageFilename,
                    'image_size'    => $imageSize,

                ]);

            

                DB::commit();

                return redirect()->route('purchaseorders.list')
                    ->with('success', 'Purchase order has been created!');

            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Purchase order submission error: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                    'request_data' => $request->all()
                ]);
                
                
                return redirect()->back()
                    ->with('error', 'Purchase order creating failed: ' . $e->getMessage() . '. Please check the logs for more details.')
                    ->withInput();
            }
            }

        public function orderView($order_id, Request $request)
        {
            $user = Auth::user();
         
            $order = Orders::with(['items.productSetting'])->where('order_id', $order_id)->first();
            $items = $order->items;
            $deliveries = Delivery::when($order_id, function ($query) use ($order_id) {
                    $query->where('order_id', $order_id);
                })
                ->orderBy('delivery_date', 'asc')
                ->get();

            return view('orders.order', [
                'user' => $user,
                'order' => $order,
                'items' => $items,
                'deliveries' => $deliveries,

            ]);
        }

        public function placeOrderItems(Request $request)
        {
            \Log::info('Placing purchase order items - Request Data:', $request->all());
            
            try {
                // Validate the request
                $request->validate([
                    'po_id' => 'required|exists:purchase_orders,po_id',
                    'supplier_id' => 'required|exists:suppliers,supplier_id',
                    'selected_products' => 'required|array|min:1',
                    'selected_products.*' => 'required|exists:product_settings,set_id', 
                    'quantities' => 'required|array',
                    'quantities.*' => 'required|numeric|min:1',
                    'product_ids' => 'required|array',
                    'unit_prices' => 'required|array',
                ]);
                
            } catch (\Illuminate\Validation\ValidationException $e) {
                \Log::error('Purchase order submission failed:', $e->errors());
                return redirect()->back()
                    ->withErrors($e->validator)
                    ->withInput();
            }
            
            try {
                DB::beginTransaction();
                
                // Get the purchase order
                $purchaseOrder = PurchaseOrders::where('po_id', $request->po_id)->firstOrFail();
                
                // Create the main order first
                $date = date('Ymd');
                $order_id = 'ORD-' . $date . '-' . $this->randomBase36String(5);
                
                $order = Orders::create([
                    'order_id' => $order_id,
                    'po_id' => $request->po_id,
                    'supplier_id' => $request->supplier_id,
                    'order_date' => now(),
                    'status' => 'Pending', 
                    'total_amount' => 0, 
                ]);
                
                $totalOrderAmount = 0;
                $orderItemsCreated = [];
                
                foreach (array_unique($request->selected_products) as $setId) {
                    if (!isset($request->quantities[$setId]) || 
                        !isset($request->product_ids[$setId]) || 
                        !isset($request->unit_prices[$setId])) {
                        continue;
=======
                // Validate stock availability
                foreach ($items as $item) {
                    $product = Product::find($item['id']);
                    if (!$product) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "Product {$item['name']} not found."
                        ], 400);
>>>>>>> parent of 54b5d0c3 (Add revised system code)
                    }
                    
                    if ($product->quantity < $item['qty']) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "{$item['name']} is out of stock or not enough quantity. Available: {$product->quantity}"
                        ], 400);
                    }
                }
                $date = date('Ymd');
                function randomBase36String(int $length): string {
                    $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    $str = '';
                    for ($i = 0; $i < $length; $i++) {
                        $str .= $chars[random_int(0, strlen($chars) - 1)];
                    }
                    return $str;
                }

                $order_id = 'ORD-' . $date . '-' . randomBase36String(5);


                // Process each item
                foreach ($items as $item) {
                    $product = Product::find($item['id']);
                    
                    // Update product quantity
                    $product->quantity -= $item['qty'];
                    $product->save();

                    // Create order record
                    Orders::create([
                        'order_id'    => $order_id,
                        'customer_id' => $user->id,
                        'product_id'  => $product->id,
                        'quantity'    => $item['qty'],
                        'unit_price'  => $product->price,
                        'total_price' => $product->price * $item['qty'],
                        'status'      => 'Pending',  

                        
                    ]);
                }

                DB::commit();
                
                return response()->json([
                    'success' => true, 
                    'message' => 'Order placed successfully!',
                    'order_id' => $order_id
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Checkout transaction failed: ' . $e->getMessage());
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Checkout error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your order. Please try again.'
            ], 500);
        }
    }
    public function customerOrders()
    {
        $user = auth()->user();
        $search = request('search');
        $status = request('status');
        $from = request('from_date', now()->startOfMonth()->format('Y-m-d'));
        $to = request('to_date', now()->endOfMonth()->format('Y-m-d'));

        $query = Orders::with('product')->where('customer_id', $user->id);

        // Status filter (tabs)
        if ($status && in_array($status, ['Pending', 'Processing', 'Completed', 'Cancelled', 'Rejected'])) {
            $query->where('status', $status);
        }

        // Date range filter aligned with status
        $dateColumn = ($status && in_array($status, ['Processing', 'Completed', 'Cancelled', 'Rejected']))
            ? 'action_at'
            : 'created_at';
        $query->whereBetween($dateColumn, [Carbon::parse($from)->startOfDay(), Carbon::parse($to)->endOfDay()]);

        // Search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', "%$search%")
                  ->orWhere('status', 'like', "%$search%")
                  ->orWhere('created_at', 'like', "%$search%")
                  ->orWhere('action_at', 'like', "%$search%");
            });
        }

        $allOrders = $query->orderByDesc($dateColumn)->get();

        $orders = $allOrders->groupBy('order_id')->map(function ($orderItems) {
            $firstItem = $orderItems->first();
            return (object) [
                'order_id' => $firstItem->order_id,
                'customer_id' => $firstItem->customer_id,
                'status' => $firstItem->status,
                'created_at' => $firstItem->created_at,
                'action_at' => $firstItem->action_at,
                'total_amount' => $orderItems->sum('total_price'),
                'item_count' => $orderItems->count(),
                'total_quantity' => $orderItems->sum('quantity'),
                'items' => $orderItems
            ];
        })->sortByDesc('created_at')->values();

        return view('customer_orders', compact('orders', 'user', 'search', 'status', 'from', 'to'));
    }


    public function viewOrder($id)
    {
        $orders = Orders::where('order_id', $id)->with('product')->get();

        if ($orders->isEmpty()) {
            return redirect()->back()->with('error', 'Order not found.');
        }

        $total = $orders->sum('total_price');
        $user = auth()->user();
        $ownerId = optional($orders->first())->customer_id;
        if (!in_array($user->user_type, ['Admin', 'Staff']) && $ownerId !== $user->id) {
            abort(403, 'Unauthorized access');
        }

        return view('view-order', compact('orders', 'total', 'user'));
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
            
            // Get all order items for this order
            $orderItems = Orders::where('order_id', $order_id)->get();
            
            // Restore product quantities
            foreach ($orderItems as $orderItem) {
                $product = Product::find($orderItem->product_id);
                if ($product) {
                    $product->quantity += $orderItem->quantity;
                    $product->save();
                }
            }
            
            // Update order status
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

<<<<<<< HEAD
        public function deliveryReceiptPdf($delivery_id)
        {
            $delivery = Delivery::where('delivery_id', $delivery_id)->firstOrFail();
            $items = DeliveryItems::where('delivery_id', $delivery_id)->get();
            $pdf = PDF::loadView('pdf.orders.delivery_receipt', compact('delivery', 'items'));
            return $pdf->stream("delivery-receipt-{$delivery_id}.pdf");
=======
    public function store() {
        $user = auth()->user();
        $search = request('search');
        $status = request('status');
        $filter = request('filter'); 
        
        $query = Product::query();
        
        $statuses = ['Available', 'Low stock', 'No stock', 'Unlisted'];
        $statusCounts = [];
        foreach ($statuses as $s) {
            $statusCounts[$s] = Product::where('status', $s)->count();
        }
        $statusCounts['All'] = Product::count();
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                ->orWhere('id', 'like', "%$search%")
                ->orWhere('status', 'like', "%$search%")
                ->orWhere('product_id', 'like', "%$search%")
                ->orWhere('category', 'like', "%$search%");
            });
        }
        
        if ($status && $status !== 'All') {
            $query->where('status', $status);
        }
        
        $products = $query
            ->select('products.*')
            ->selectSub(function ($q) {
                $q->from('orders')
                ->selectRaw('COALESCE(SUM(quantity), 0)')
                ->whereColumn('orders.product_id', 'products.id')
                ->where('status', 'Completed');
            }, 'sold_quantity');
        
        switch ($filter) {
            case 'asc':
                $products = $products->orderBy('name', 'asc');
                break;
            case 'desc':
                $products = $products->orderBy('name', 'desc');
                break;
            case 'new':
                $products = $products->orderBy('created_at', 'desc');
                break;
            case 'old':
                $products = $products->orderBy('created_at', 'asc');
                break;
            case 'sold-most':
                $products = $products->orderByDesc('sold_quantity');
                break;
            case 'sold-least':
                $products = $products->orderBy('sold_quantity', 'asc');
                break;
            default:
                $products = $products->orderByRaw("
                    CASE 
                        WHEN quantity > 0 AND status != 'Unlisted' THEN 1
                        WHEN quantity = 0 THEN 2
                        WHEN status = 'Unlisted' THEN 3
                        ELSE 4
                    END
                ")->orderByDesc('created_at');
                break;
        }
        
        $products = $products->paginate(15);
        
        $appendParams = [];
        if ($search) {
            $appendParams['search'] = $search;
        }
        if ($status) {
            $appendParams['status'] = $status;
        }
        if ($filter) {
            $appendParams['filter'] = $filter;
        }
        
        if (!empty($appendParams)) {
            $products->appends($appendParams);
        }
        
        return view('store', compact('user', 'products', 'search', 'statusCounts', 'filter'));
    }
    
    public function orders()
    {
        $user = auth()->user();
        $search = request('search');
        $status = request('status');
        $from = request('from_date', now()->startOfMonth()->format('Y-m-d'));
        $to = request('to_date', now()->endOfMonth()->format('Y-m-d'));

        $query = Orders::with(['product', 'user']);

        // Status filter (tabs)
        if ($status && in_array($status, ['Pending', 'Processing', 'Completed', 'Cancelled', 'Rejected'])) {
            $query->where('status', $status);
>>>>>>> parent of 54b5d0c3 (Add revised system code)
        }

        // Date range filter aligned with status state
        $dateColumn = ($status && in_array($status, ['Processing', 'Completed', 'Cancelled', 'Rejected']))
            ? 'action_at'
            : 'created_at';
        $query->whereBetween($dateColumn, [Carbon::parse($from)->startOfDay(), Carbon::parse($to)->endOfDay()]);

        // Search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', "%$search%")
                  ->orWhere('status', 'like', "%$search%")
                  ->orWhere('created_at', 'like', "%$search%")
                  ->orWhere('action_at', 'like', "%$search%");
            })
            ->orWhereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('store_name', 'like', "%$search%");
            });
        }

        $allOrders = $query->orderByDesc($dateColumn)->get();

        $orders = $allOrders->groupBy('order_id')->map(function ($orderItems) {
            $firstItem = $orderItems->first();
            return (object) [
                'order_id' => $firstItem->order_id,
                'customer_id' => $firstItem->customer_id,
                'status' => $firstItem->status,
                'created_at' => $firstItem->created_at,
                'action_at' => $firstItem->action_at,
                'total_amount' => $orderItems->sum('total_price'),
                'item_count' => $orderItems->count(),
                'total_quantity' => $orderItems->sum('quantity'),
                'user' => $firstItem->user,
                'items' => $orderItems
            ];
        })->sortByDesc('created_at')->values();

        return view('orders', compact('orders', 'user', 'search', 'status', 'from', 'to'));
    }

    public function orderView($id)
    {
        $orderItems = Orders::with(['product', 'user'])
            ->where('order_id', $id)
            ->get();

        if ($orderItems->isEmpty()) {
            return redirect()->back()->with('error', 'Order not found.');
        }

        $user = auth()->user();

        return view('order_view', compact('orderItems', 'user'));
    }

    public function acceptOrder($order_id)
    {
        $user = auth()->user();

        Orders::where('order_id', $order_id)->update([
            'status' => 'Processing',
            'action_at' => now(),
            'action_by' => $user->name,
        ]);

        return redirect()->route('order.view', $order_id)
            ->with('success', 'Order accepted successfully!');
    }

    public function markOrderDone($order_id)
    {
        $user = auth()->user();

        Orders::where('order_id', $order_id)->update([
            'status' => 'Completed',
            'action_at' => now(),
            'action_by' => $user->name,
        ]);

        return redirect()->route('order.view', $order_id)
            ->with('success', 'Order marked as done successfully!');
    }

    public function rejectOrder($order_id)
    {
        $user = auth()->user();
        $ownerId = Orders::where('order_id', $order_id)->value('customer_id');
        if (!in_array($user->user_type, ['Admin', 'Staff']) && $ownerId !== $user->id) {
            abort(403, 'Unauthorized action');
        }

        try {
            DB::beginTransaction();
            
            // Get all order items for this order
            $orderItems = Orders::where('order_id', $order_id)->get();
            
            // Restore product quantities (only for non-completed items)
            foreach ($orderItems->where('status', '!=', 'Completed') as $orderItem) {
                $product = Product::find($orderItem->product_id);
                if ($product) {
                    $product->quantity += $orderItem->quantity;
                    $product->save();
                }
            }
            
            // Update order status
            Orders::where('order_id', $order_id)->update([
                'status' => 'Rejected',
                'action_at' => now(),
                'action_by' => $user->name,
            ]);
            
            DB::commit();
            
            return redirect()->route('orders.view', $order_id)
                ->with('success', 'Order rejected successfully! Product quantities have been restored.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting order: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to reject order. Please try again.');
        }
    }


}