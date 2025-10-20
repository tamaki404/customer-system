<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Suppliers;
use App\Models\Documents;
use App\Models\Orders;
use App\Models\Receipts;
use App\Models\Credits;
use App\Models\Representatives;
use App\Models\Delivery;
use Carbon\Carbon;
use Session;

class DashboardController extends Controller
{
public function dashboardView(Request $request)
{
    $user = Auth::user();
    $supplier = $user ? Suppliers::where('user_id', $user->user_id)->first() : null;

    $purchasesCount = 0;
    $totalReceipts = 0;
    $totalOrders = 0;
    $remainingBalance = 0;
    $deliveryCount = 0;
    $documentCount = $supplier ? Documents::where('supplier_id', $supplier->supplier_id)->count() : 0;

    if ($user->role === 'Supplier') {
        $totalOrders = Orders::where('supplier_id', $supplier->supplier_id)->count();

                $deliveryCount = Delivery::where('supplier_id', $supplier->supplier_id)
                    ->where('Status', "Scheduled")
                    ->count();


                $credit = Credits::where('user_id', $user->user_id)->first();
                $usedCredit = Orders::where('supplier_id', $supplier->supplier_id)
                    ->whereIn('payment_status', ['Unpaid', 'Partially Settled'])
                    ->selectRaw('
                        SUM(
                            orders.total_amount - COALESCE(
                                (SELECT SUM(r.total_amount) 
                                FROM receipts r 
                                WHERE r.order_id = orders.order_id 
                                AND r.status = "Verified"), 0
                            )
                        ) as outstanding_balance
                    ')
                    ->value('outstanding_balance');


                $remainingBalance = $credit ? $credit->credit_limit - $usedCredit : 0;


        $purchasesCount = Orders::where('supplier_id', $supplier->supplier_id)->count();
        $totalReceipts = Receipts::where('supplier_id', $supplier->supplier_id)->count();

    



    // } elseif ($user->role === 'Staff' || $user->role === 'Admin') {
    //    $today = Carbon::today();

    //     $totalOrders = Orders::count();
    //     $pendingOrders = Orders::where('status', 'Pending')->count();
    //     $deliveryCount = Delivery::where('delivery_date', $today)->count();
    //     $totalReceipts = Receipts::count();
    //     $deliveryToday = Delivery::where('delivery_date', $today)->count();
    //     $pendingReceipts = Receipts::where('status', "Pending")->count();
    //     $verifiedReceipts = Receipts::where('status', "Verified")->count();
    //     $deliveries = Delivery::where('delivery_date', $today)->get();
    //     $orderCount = Orders::where('status', "Completed")->count();


    // }

    } elseif (in_array($user->role, ['Admin', 'Staff'])) {
        $today = Carbon::today();
        $totalOrders = Orders::count();
        $pendingOrders = Orders::where('status', 'Pending')->count();
        $deliveryCount = Delivery::where('delivery_date', $today)->count();
        $totalReceipts = Receipts::count();
        $deliveryToday = Delivery::where('delivery_date', $today)->count();
        $pendingReceipts = Receipts::where('status', "Pending")->count();
        $verifiedReceipts = Receipts::where('status', "Verified")->count();
        $deliveries = Delivery::where('delivery_date', $today)->get();
        $orderCount = Orders::where('status', "Completed")->count();
    }

    return view('dashboard', compact(
        'user',
        'supplier',
        'purchasesCount',
        'remainingBalance',
        'documentCount',
        'totalOrders',
        'totalReceipts',
        'deliveryCount'
    ));
}


public function layoutView(Request $request)
{
    $user = Auth::user();
    $rep = auth('representative')->user();

    $supplier = null;
    $documentCount = 0;
    $activeRepresentative = null;

    if ($user->role === 'Supplier') {
        $supplier = Suppliers::where('user_id', $user->user_id)->first();
        $documentCount = $supplier
            ? Documents::where('supplier_id', $supplier->supplier_id)->count()
            : 0;

        // Get active representative if one is signed in
        if (session()->has('active_representative_id')) {
            $activeRepresentative = Representatives::where('rep_id', session('active_representative_id'))
                ->first();
        }
    }

    return view('layouts.main', [
        'user' => $user,
        'rep' => $rep,

        'supplier' => $supplier,
        'documentCount' => $documentCount,
        'activeRepresentative' => $activeRepresentative,
    ]);
}


}
