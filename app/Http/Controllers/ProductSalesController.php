<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PriceHistory;
use App\Models\ProductSetting;
use Illuminate\Http\Request;
use App\Models\Logs;
use App\Models\ProductSales;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProductSalesController extends Controller
{
    public function addSale(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'set_id' => 'required|exists:product_settings,set_id',
            'supplier_id' => 'required|exists:suppliers,supplier_id',
            'sale_price' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'start_date' => 'required|date' ,
            'end_date'  => 'required|date|after:start_date'
        ]);

        $product = ProductSetting::where('set_id', $request->set_id)->firstOrFail();



        $date = date('Ymd');
        $log_id = 'LOG-' . $date . '-' . strtoupper(Str::random(5));
        $sale_id = 'SALE-' . $date . '-' . strtoupper(Str::random(5));
        $price = 'PRICE-' . $date . '-' . strtoupper(Str::random(5));

        $description = '';

        //  1: product sales
        $sale = ProductSales::create([
            'sale_id'     => $sale_id,
            'supplier_id'      => $request->supplier_id,
            'set_id'      => $request->set_id,
            'sale_price' => $request->sale_price,
            'status'    => 'Active',
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'action_by' => $user->user_id
        ]);
        
        //  2: price history
        // PriceHistory::create([
        //     'phistory_id'     => $price,
        //     'supplier_id'      => $request->supplier_id,
        //     'action' => 'Sale',
        //     'sale_id' => $sale_id,
        //     'set_id'      => $request->set_id,
        //     'new_price' => $request->sale_price,
        //     'past_price' => $product->nego_price,
        //     'action_by' => $user->user_id,
        // ]);

        //  3: price history
        Logs::create([
            'user_id'     => $user->user_id,
            'action'      => 'Created promo',
            'log_id'      => $log_id,
            'description' => " Staff ($user->user_id)
                created promo for ($request->supplier_id)' ($request->set_id). sale price 
                ( $request->sale_price) from ($request->start_date) to ($request->end_date) ",
            'entity'      => 'ProductSales',
            'entity_id'   => $sale->id,
        ]);

        return back()->with('success', 'Product requirement updated successfully.');
    }}
