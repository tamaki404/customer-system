<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function purchaseOrder(){
        $user = auth()->user();
        return view('purchase_order', compact('user'));
    }
}
