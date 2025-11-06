<?php

namespace App\Http\Controllers;
use App\Models\Customers;
use App\Models\PurchaseRequest;
use App\Models\Receipts;
use App\Models\Credits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreditsRequestController extends Controller
{
    public function list(Request $request)
    {
        $user = Auth::user();
        $customer = Customers::where('user_id', $user->user_id)->firstOrFail();
        $credit = Credits::where('user_id', $user->user_id)->firstOrFail();

        // $usedCredit = PurchaseRequest::where('customer_id', $customer->customer_id)
        //     ->selectRaw('
        //         SUM(
        //             orders.total_amount - COALESCE(
        //                 (SELECT SUM(r.total_amount) 
        //                 FROM receipts r 
        //                 WHERE r.order_id = orders.order_id 
        //                 AND r.status = "Verified"), 0
        //             )
        //         ) as outstanding_balance
        //     ')
        //     ->value('outstanding_balance');



        return view('franken.crd.list', compact(
            'user',
            'customer',
        ));
    
    }
}
