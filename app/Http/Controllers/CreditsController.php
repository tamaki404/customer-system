<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Credits;
use App\Models\Orders;
use App\Models\Suppliers;

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
                    ->where('status', 'pending')
                    ->sum('total_amount');

                $availableCredit = $credit->credit_limit - $usedCredit;
                $oustandingPayments = Orders::where('supplier_id', $supplier->supplier_id)
                    ->where('status', 'pending')
                    ->get();



            }
            return view('credits.list', [
                'user' => $user,
                'oustandingPayments' => $oustandingPayments,
                'credit' => $credit,
                'usedCredit' => $usedCredit,
                'availableCredit' => $availableCredit,

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

