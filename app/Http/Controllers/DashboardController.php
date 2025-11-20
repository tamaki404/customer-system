<?php

namespace App\Http\Controllers;

use App\Models\DeliveryItemRequest;
use App\Models\DeliveryRequest;
use App\Models\PurchaseHistory;
use App\Models\PurchaseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Customers;
use App\Models\Documents;
use App\Models\Orders;
use App\Models\Receipts;
use App\Models\Credits;
use App\Models\Representatives;
use App\Models\Delivery;
use App\Models\Payments;

use Carbon\Carbon;
use App\Models\User;

use Session;

class DashboardController extends Controller
{

    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $purchaseCount = PurchaseRequest::count();
        $deliveryCount = DeliveryRequest::count();
        $receiptCount = Receipts::count();
        $payments = Payments::limit(5)->get();
        
        return view('dashboard', compact(
            'user',
            'purchaseCount',
            'deliveryCount',
            'receiptCount',
            'payments'
        ));
    }

    public function dashboardView(Request $request)
    {
        $user = Auth::user();
        $customer = $user ? Customers::where('user_id', $user->user_id)->first() : null;

        $purchasesCount = 0;
        $totalReceipts = 0;
        $totalOrders = 0;
        $remainingBalance = 0;
        $deliveryCount = 0;
        $documentCount = $customer ? Documents::where('customer_id', $customer->customer_id)->count() : 0;

        if ($user->role === 'Customer') {
            $totalOrders = Orders::where('customer_id', $customer->customer_id)->count();

                    $deliveryCount = Delivery::where('customer_id', $customer->customer_id)
                        ->where('Status', "Scheduled")
                        ->count();


                    $credit = Credits::where('user_id', $user->user_id)->first();
                    $usedCredit = Orders::where('customer_id', $customer->customer_id)
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


            $purchasesCount = Orders::where('customer_id', $customer->customer_id)->count();
            $totalReceipts = Receipts::where('customer_id', $customer->customer_id)->count();

        



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
        $user = Auth::user();
        $purchaseCount = PurchaseRequest::count();
        $deliveryCount = DeliveryRequest::count();
        $receiptCount = Receipts::count();
        $payments = PurchaseHistory::limit(5)->get();
        

        }

        return view('dashboard', compact(
            'user',
            'customer',
            'purchasesCount',
            'remainingBalance',
            'documentCount',
            'totalOrders',
            'totalReceipts',
            'deliveryCount',

            'purchaseCount',
            'deliveryCount',
            'receiptCount',
            'payments'
        ));
    }

    public function layoutView(Request $request)
    {
        $user = Auth::user();
        $rep = auth('representative')->user();

        $customer = null;
        $documentCount = 0;
        $activeRepresentative = null;

        if ($user->role === 'Customer') {
            $customer = Customers::where('user_id', $user->user_id)->first();
            $documentCount = $customer
                ? Documents::where('customer_id', $customer->customer_id)->count()
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

            'customer' => $customer,
            'documentCount' => $documentCount,
            'activeRepresentative' => $activeRepresentative,
        ]);
    }


}
