<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class OrderController extends Controller
{
public function checkout(Request $request)
{
    $user = auth()->user();
    $items = $request->input('items', []);
    $total = $request->input('total', 0);

    if (!$user || $user->user_type !== 'Customer') {
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
    }

    if (empty($items)) {
        return response()->json(['success' => false, 'message' => 'Cart is empty.'], 400);
    }

    // Validate stock
    foreach ($items as $item) {
        $product = \App\Models\Product::find($item['id']);
        if (!$product || $product->quantity < $item['qty']) {
            return response()->json([
                'success' => false,
                'message' => "{$item['name']} is out of stock or not enough quantity."
            ], 400);
        }
    }

    // Create order (or receipt, depending on your models)
    $order = new \App\Models\Order();
    $order->user_id = $user->id;
    $order->total = $total;
    $order->save();

    // Attach products and update stock
    foreach ($items as $item) {
        $product = \App\Models\Product::find($item['id']);
        $product->quantity -= $item['qty'];
        $product->save();

        // You may want to create an order item/receipt row here
        // Example:
        // \App\Models\OrderItem::create([
        //     'order_id' => $order->id,
        //     'product_id' => $product->id,
        //     'quantity' => $item['qty'],
        //     'price' => $item['price'],
        // ]);
    }

    return response()->json(['success' => true, 'message' => 'Order placed successfully!']);
}

    
}
