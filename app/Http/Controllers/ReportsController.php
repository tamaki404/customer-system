<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Orders;

use Illuminate\Http\Request;

class ReportsController extends Controller
{
public function reports()
{
    $user = auth()->user();

     $totalSales = Orders::where('status', 'Completed')->sum('total_price');
    $completedOrdersCount = Orders::where('status', 'Completed')->count();
    $averageOrderValue = $completedOrdersCount > 0 
        ? $totalSales / $completedOrdersCount 
        : 0;

    $successfulPayments = Orders::where('status', 'Completed')->count();

    $totalPaymentAttempts = Orders::whereIn('status', [
        'Completed', 'Rejected', 'Pending', 'Processing'
    ])->count();

    $paymentSuccessRate = $totalPaymentAttempts > 0
        ? round(($successfulPayments / $totalPaymentAttempts) * 100, 2)
        : 0;

    

    return view('reports', compact(
        'user',
        'successfulPayments',
        'totalPaymentAttempts',
        'paymentSuccessRate',
        'totalSales',
        'completedOrdersCount',
        'averageOrderValue',

    ));
}


}
