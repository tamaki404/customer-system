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
            $search = request('search');
            $query = Product::query();
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                    ->orWhere('id', 'like', "%$search%")
                    ->orWhere('status', 'like', "%$search%")
                    ->orWhere('product_id', 'like', "%$search%")
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



        return view('purchase_orders/store_create_order', compact('user', 'products', 'search'));
    }


}
