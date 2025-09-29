<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Suppliers;
use App\Models\Documents;
use App\Models\Orders;
use App\Models\Receipts;
use App\Models\Credits;

class DashboardController extends Controller
{
public function dashboardView(Request $request)
{
    $user = Auth::user();
    $supplier = $user ? Suppliers::where('user_id', $user->user_id)->first() : null;
    $documentCount = $supplier ? Documents::where('supplier_id', $supplier->supplier_id)->count() : 0;

    $usedCredit = 0;
    $totalOrders = 0;
    $pendingOrders = 0;
    $totalReceipts = 0;

    if ($user->role === 'Supplier') {
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
            ->value('outstanding_balance') ?? 0; 

        $totalOrders = Orders::where('supplier_id', $supplier->supplier_id)->count();
        $pendingOrders = Orders::where('supplier_id', $supplier->supplier_id)
            ->where('status', 'Pending')
            ->count();
        $totalReceipts = Receipts::where('supplier_id', $supplier->supplier_id)->count();

    } elseif ($user->role === 'Staff' || $user->role === 'Admin') {
        $totalOrders = Orders::count();
        $pendingOrders = Orders::where('status', 'Pending')->count();
        $totalReceipts = Receipts::count();


    }

    return view('dashboard', [
        'user' => $user,
        'supplier' => $supplier,
        'documentCount' => $documentCount,
        'usedCredit' => $usedCredit,
        'totalOrders' => $totalOrders,
        'pendingOrders' => $pendingOrders,
        'totalReceipts' => $totalReceipts,
    ]);
}


    public function layoutView(Request $request)
    {
        $user = Auth::user();

        $supplier = null;
        $documentCount = 0;

        if ($user->role === 'Supplier') {
            $supplier = Suppliers::where('user_id', $user->user_id)->first();
            $documentCount = $supplier
                ? Documents::where('supplier_id', $supplier->supplier_id)->count()
                : 0;
        }

        return view('layouts.main', [
            'user' => $user,
            'supplier' => $supplier,
            'documentCount' => $documentCount,
        ]);
    }

}
