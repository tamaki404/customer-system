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
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer|exists:products,id',
            'items.*.name' => 'required|string',
            'items.*.price' => 'required|numeric',
            'items.*.qty' => 'required|integer|min:1',
            'total' => 'required|numeric|min:0.01',
        ]);

        $order = Order::create([
            'user_id' => Auth::id(),
            'items' => $request->items,
            'total' => $request->total,
            'expires_at' => Carbon::now()->addHours(24),
        ]);

        return response()->json(['success' => true, 'order_id' => $order->id]);
    }
}
