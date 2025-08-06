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
            $items = $request->input('items', []);
            $total = $request->input('total', 0);

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

                // Generate unique order ID
                $order_id = 'ORD-' . time() . '-' . $user->id;

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
    public function specOrders($id)
    {
        $user = auth()->user();
        $search = request('search');

        $allOrders = Orders::with('product')
            ->where('customer_id', $id)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('order_id', 'like', "%$search%")
                    ->orWhere('status', 'like', "%$search%")
                    ->orWhere('created_at', 'like', "%$search%");
                });
            })
            ->orderByDesc('created_at')
            ->get();

        $orders = $allOrders->groupBy('order_id')->map(function ($orderItems) {
            $firstItem = $orderItems->first();
            return (object) [
                'order_id' => $firstItem->order_id,
                'customer_id' => $firstItem->customer_id,
                'status' => $firstItem->status,
                'created_at' => $firstItem->created_at,
                'total_amount' => $orderItems->sum('total_price'),
                'item_count' => $orderItems->count(),
                'total_quantity' => $orderItems->sum('quantity'),
                'items' => $orderItems
            ];
        })->sortByDesc('created_at')->values();

        return view('orders', compact('orders', 'user', 'search'));
    }


    public function viewOrder($id)
    {
        $order = Orders::where('order_id', $id)->with('product')->first();

        if (!$order) {
            return redirect()->back()->with('error', 'Order not found.');
        }

        // Get all items for the order
        $items = Orders::getOrderItems($id);
        $total = Orders::getOrderTotal($id);

        return view('view-order', compact('order', 'items', 'total'));
    }

    

}