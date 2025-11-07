<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\OrderHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Credits;
use App\Models\Orders;
use App\Models\Customers;
use App\Models\Receipts;

class CreditsController extends Controller
{
    public function creditsList(Request $request)
    {
        $user = Auth::user();
        $customer = Customers::where('user_id', $user->user_id)->first();
        $orders = collect(); 
        if ($user->role !== "Customer") {
            $orders = Credits::all();
            $receipts = Receipts::orderBy('created_at', 'desc')->get();
        } 
        elseif ($user->role === "Customer") {
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
            $availableCredit = $credit->credit_limit - $usedCredit;
            $oustandingPayments = Orders::where('orders.customer_id', $customer->customer_id)
                ->whereIn('orders.payment_status', ['Unpaid', 'Partially settled', 'Fully paid'])
                ->select('orders.*')
                ->selectSub(function ($query) {
                    $query->from('receipts')
                        ->selectRaw('COALESCE(SUM(total_amount), 0)')
                        ->whereColumn('receipts.order_id', 'orders.order_id')
                        ->where('receipts.status', 'Verified');
                }, 'verified_receipts_total')
                ->whereRaw('orders.total_amount > (
                    select COALESCE(SUM(total_amount), 0)
                    from receipts
                    where receipts.order_id = orders.order_id
                    and receipts.status = "Verified"
                )') 
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($order) {
                    $order->outstanding_balance = $order->total_amount - $order->verified_receipts_total;
                    return $order;
                });
            $unpaidOrders = Orders::where('customer_id', $customer->customer_id)
                    ->where('payment_status', '!=', 'Fully paid')
                    ->get();

                $orderIds = Orders::where('customer_id', $customer->customer_id)
                    ->pluck('order_id');

                $transactionHistory = OrderHistory::whereIn('order_id', $orderIds)
                    ->orderBy('created_at', 'desc')
                    ->get();

                $customer = Customers::where('user_id', $user->user_id)->first();
                $receipts = Receipts::where('customer_id', $customer->customer_id)->orderBy('created_at', 'desc')->get();

            }
            return view('credits.list', [
                'user' => $user,
                'oustandingPayments' => $oustandingPayments,
                'credit' => $credit,
                'usedCredit' => $usedCredit,
                'availableCredit' => $availableCredit,
                'transactionHistory' => $transactionHistory,
                'unpaidOrders' => $unpaidOrders,
                'receipts' => $receipts,
                'customer' => $customer,

            ]);
    }

}

