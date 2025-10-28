<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SaleDiscount;
use Illuminate\Support\Str;
use App\Models\Logs;
class SaleDiscountController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:Sale,Discount',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'value' => 'required|numeric|min:0',
            'value_type' => 'required|in:percentage,fixed',
            'category' => 'required|in:Wholesale,Distributor,HRI,Dealer',
            'product' => 'required|string|exists:products,product_id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'user_id' => 'required|string|exists:users,user_id',
            'quantity' => 'required|integer|min:0',
        ]);

        $promo = SaleDiscount::create([
            'promo_id' => 'PROMO-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5)),
            'type' => $request->type,
            'name' => $request->name,
            'description' => $request->description,
            'value' => $request->value,
            'value_type' => $request->value_type,
            'category' => $request->category,
            'product_id' => $request->product,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'user_id' => $request->user_id,
            'quantity' => $request->quantity,
        ]);

        $product = $request->product;

        // Create log entry
        $date = date('Ymd');
        function randomBase36String(int $length): string {
            $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $str = '';
            for ($i = 0; $i < $length; $i++) {
                $str .= $chars[random_int(0, strlen($chars) - 1)];
            }
            return $str;
        }
        $log_id = 'LOG-' . $date . '-' . randomBase36String(5);
        Logs::create([
            'user_id' => $request->user_id,
            'action' => "Added a new {$request->type}",
            'log_id' => $log_id,
            'description' => "User ({$request->user_id}) added a new {$request->type} named '{$request->name}' with value " . ($request->value ?? 'N/A'). " for product ID {$product}, category {$request->category}.",
            'entity' => 'SalesDiscount',
            'entity_id' => $promo->id,
        ]);
                

        return redirect()->back()->with('success', 'Sale or discount successfully applied!');
    }
}
