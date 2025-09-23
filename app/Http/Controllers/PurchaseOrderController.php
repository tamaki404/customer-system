<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Orders;
use App\Models\PurchaseOrders;
use App\Models\Suppliers;
use Illuminate\Support\Facades\Auth;
use App\Models\Products;
use App\Models\Orderitem;
use App\Models\ProductSetting;

class PurchaseOrderController extends Controller
{
        public function purchaseOrderlist(Request $request)
        {
            $user = Auth::user();

            $supplier = null;
            $pos = collect();


            if ($user->role === "Supplier") {
                $supplier = Suppliers::where('user_id', $user->user_id)->first();

                if ($supplier) {
                    $pos = PurchaseOrders::where('supplier_id', $supplier->supplier_id)
                        ->orderBy('created_at', 'desc')
                        ->get();
                }
            } 
            elseif ($user->role === "Staff" ) {
                $pos = PurchaseOrders::all();
            }
            elseif ($user->role === "Admin" ) {
                $pos = PurchaseOrders::all();
            }
            return view('purchase-orders.list', [
                'user' => $user,
                'supplier' => $supplier,
                'pos' => $pos,
            ]);
        }

        
        public function purchaseOrderView($po_id, Request $request)
        {
            $user = Auth::user();
            $po = PurchaseOrders::where('po_id', $po_id)->first(); 
            $setProducts = ProductSetting::where('supplier_id', $po->supplier_id)->get();

            return view('purchase-orders.purchaseorder', [
                'user' => $user,
                'po' => $po,
                'setProducts' => $setProducts,

            ]);

        }


        // public function storeOrderItems(Request $request, $po_id)
        // {
        //     $po = PurchaseOrders::findOrFail($po_id);

        //     // Create a new Order (if not already created)
        //     $order = Orders::create([
        //         'purchase_order_id' => $po->po_id,
        //         'user_id' => auth()->id(),
        //         // ...other fields as needed
        //     ]);

        //     foreach ($request->input('products', []) as $product_id => $data) {
        //         if (!empty($data['selected']) && $data['quantity'] > 0) {
        //             OrderItem::create([
        //                 'order_id' => $order->id,
        //                 'product_id' => $product_id,
        //                 'quantity' => $data['quantity'],
        //                 'price' => $data['price'] ?? null,
        //             ]);
        //         }
        //     }

        //     return redirect()->route('purchaseorders.purchaseorder', $po_id)->with('success', 'Order items saved!');
        // }

}
