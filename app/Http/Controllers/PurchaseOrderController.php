<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function purchaseOrder(){
        $user = auth()->user();
        return view('purchase_order', compact('user'));
    }
    public function purchaseOrderForm(){
        $user = auth()->user();
        return view('purchase_orders/purchase_order_form', compact('user'));
    }
    public function storeOrderView(){
        $user = auth()->user();
        $products = Product::all();
        return view('purchase_orders/store_order_view', compact('products'));
    }


}
