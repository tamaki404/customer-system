<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Orders;
use App\Models\PurchaseOrders;
use App\Models\Suppliers;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderController extends Controller
{
        public function purchaseOrderlist(Request $request)
        {
            $user = Auth::user();
            $supplier = Suppliers::where('user_id', $user->user_id)->first();
            $pos = PurchaseOrders::where('supplier_id', $supplier->supplier_id)->get(); 


            return view('purchase-orders.list', [
                'user' => $user,
                'pos' => $pos,

            ]);
        }

        
        public function purchaseOrderView($po_id, Request $request)
        {
            $user = Auth::user();
            $po = PurchaseOrders::where('po_id', $po_id)->first(); 

            return view('purchase-orders.purchaseorder', [
                'user' => $user,
                'po' => $po,

            ]);
        }

}
