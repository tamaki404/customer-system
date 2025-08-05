<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderItem;
use App\Models\Product;
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

    foreach ($items as $item) {
        $product = Product::find($item['id']);
        if (!$product || $product->quantity < $item['qty']) {
            return response()->json([
                'success' => false,
                'message' => "{$item['name']} is out of stock or not enough quantity."
            ], 400);
        }
    }

    // Optionally, generate a unique order_id for this batch
    $order_id = time() . $user->id;

    foreach ($items as $item) {
        $product = Product::find($item['id']);
        $product->quantity -= $item['qty'];
        $product->save();

        OrderItem::create([
            'order_id'    => $order_id,
            'product_id'  => $product->id,
            'quantity'    => $item['qty'],
            'unit_price'  => $product->price,
            'total_price' => $product->price * $item['qty'],
        ]);
    }

    return response()->json(['success' => true, 'message' => 'Order placed successfully!']);
}
}
