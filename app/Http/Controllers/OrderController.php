<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Orders;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
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
                // Validate stock availability
                foreach ($items as $item) {
                    $product = Product::find($item['id']);
                    if (!$product) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "Product {$item['name']} not found."
                        ], 400);
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

        public function store()
        {
            $user = auth()->user();
            $search = request('search');
            $query = Product::query();
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                    ->orWhere('id', 'like', "%$search%")
                    ->orWhere('status', 'like', "%$search%")
                    ;
                });
            }
            $products = $query
                ->select('products.*')
                ->selectSub(function ($q) {
                    $q->from('orders')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('orders.product_id', 'products.id');
                }, 'sold_quantity')
                ->orderByDesc('created_at')
                ->paginate(15);
            if ($search) {
                $products->appends(['search' => $search]);
            }
            return view('store', compact('user', 'products', 'search'));
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