<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SaleDiscount;
use Illuminate\Support\Str;

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
            'staff_id' => 'required|string',
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
            'staff_id' => $request->staff_id,
        ]);

        return redirect()->back()->with('success', 'Sale or discount successfully applied!');
    }
}
