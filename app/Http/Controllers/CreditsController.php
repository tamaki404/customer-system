<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\OrderHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Credits;
use App\Models\Orders;
use App\Models\Suppliers;
use App\Models\Receipts;
use Illuminate\Support\Facades\DB;

class CreditsController extends Controller
{
        public function creditsList(Request $request)
        {
            $user = Auth::user();
            $supplier = Suppliers::where('user_id', $user->user_id)->first();
            $orders = collect(); 

            if ($user->role !== "Supplier") {
                $orders = Credits::all();
            } 
            elseif ($user->role === "Supplier") {
                $credit = Credits::where('user_id', $user->user_id)->first();
                $usedCredit = Orders::where('supplier_id', $supplier->supplier_id)
                    ->where('status', 'Accepted')
                    ->sum('total_amount');

                $availableCredit = $credit->credit_limit - $usedCredit;
                $receipts = Receipts::where('supplier_id', $supplier->supplier_id)->get();
                $oustandingPayments = Orders::where('orders.supplier_id', $supplier->supplier_id)
                    ->whereIn('orders.payment_status', ['Unpaid', 'Partially settled'])
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
                    ->get()
                    ->map(function ($order) {
                        $order->outstanding_balance = $order->total_amount - $order->verified_receipts_total;
                        return $order;
                    });


                $unpaidOrders = Orders::where('supplier_id', $supplier->supplier_id)
                    ->where('payment_status', '!=', 'Fully paid')
                    ->get();

                $orderIds = Orders::where('supplier_id', $supplier->supplier_id)
                    ->pluck('order_id');

                $transactionHistory = OrderHistory::whereIn('order_id', $orderIds)
                    ->whereIn('status', ['Verified', 'Accepted'])
                    ->orderBy('created_at', 'desc')
                    ->get();




            }
            return view('credits.list', [
                'user' => $user,
                'oustandingPayments' => $oustandingPayments,
                'credit' => $credit,
                'usedCredit' => $usedCredit,
                'availableCredit' => $availableCredit,
                'receipts' => $receipts,
                'transactionHistory' => $transactionHistory,
                'unpaidOrders' => $unpaidOrders

            ]);
        }

        // public function creditsView($credit_id, Request $request)
        // {
        //     $user = Auth::user();
        //     $credits = Credits::all(); 

        //     return view('credits.credit', [
        //         'user' => $user,
        //         'credits' => $credits,

        //     ]);
        
        // }
    

}

