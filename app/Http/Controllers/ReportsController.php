<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Orders;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    
    public function reports(Request $request)
    {
        $user = auth()->user();
        
        // Get date filters from request or set defaults
        $dateRange = $request->get('date_range', 'last_30_days');
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');
        
        // Always set startDate and endDate - determine the actual date range based on selection
        switch ($dateRange) {
            case 'last_7_days':
                $startDate = Carbon::now()->subDays(7);
                $endDate = Carbon::now();
                break;
            case 'last_30_days':
                $startDate = Carbon::now()->subDays(30);
                $endDate = Carbon::now();
                break;
            case 'last_3_months':
                $startDate = Carbon::now()->subMonths(3);
                $endDate = Carbon::now();
                break;
            case 'custom':
                if ($fromDate && $toDate) {
                    $startDate = Carbon::parse($fromDate);
                    $endDate = Carbon::parse($toDate);
                } else {
                    // Default to last 30 days if custom range is incomplete
                    $startDate = Carbon::now()->subDays(30);
                    $endDate = Carbon::now();
                }
                break;
            default:
                $startDate = Carbon::now()->subDays(30);
                $endDate = Carbon::now();
        }
        
        // Apply date filtering to queries
        $ordersQuery = Orders::whereBetween('created_at', [$startDate, $endDate]);
        
        $totalSales = $ordersQuery->where('status', 'Completed')->sum('total_price');
        $completedOrdersCount = $ordersQuery->where('status', 'Completed')->count();
        
        $averageOrderValue = $completedOrdersCount > 0 
            ? $totalSales / $completedOrdersCount 
            : 0;
        
        $successfulPayments = $ordersQuery->where('status', 'Completed')->count();
        
        $totalPaymentAttempts = $ordersQuery->whereIn('status', [
            'Completed', 'Rejected', 'Pending', 'Processing'
        ])->count();
        
        $paymentSuccessRate = $totalPaymentAttempts > 0
            ? round(($successfulPayments / $totalPaymentAttempts) * 100, 2)
            : 0;
        
        // Monthly sales data with date filtering
        $monthlySales = Orders::selectRaw('MONTH(created_at) as month, SUM(total_price) as total')
            ->where('status', 'Completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->pluck('total', 'month');
        
        return view('reports', compact(
            'user',
            'successfulPayments',
            'totalPaymentAttempts',
            'paymentSuccessRate',
            'totalSales',
            'completedOrdersCount',
            'averageOrderValue',
            'monthlySales',
            'dateRange',
            'fromDate',
            'toDate',
            'startDate',
            'endDate'
        ));
    }

    public function exportReports(Request $request) {
        $type = $request->get('type', 'excel');
        
        // Get the same filtered data
        $dateRange = $request->get('date_range', 'last_30_days');
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');
        
        // Determine date range (same logic as reports method)
        switch ($dateRange) {
            case 'last_7_days':
                $startDate = Carbon::now()->subDays(7);
                $endDate = Carbon::now();
                break;
            case 'last_30_days':
                $startDate = Carbon::now()->subDays(30);
                $endDate = Carbon::now();
                break;
            case 'last_3_months':
                $startDate = Carbon::now()->subMonths(3);
                $endDate = Carbon::now();
                break;
            case 'custom':
                if ($fromDate && $toDate) {
                    $startDate = Carbon::parse($fromDate);
                    $endDate = Carbon::parse($toDate);
                } else {
                    $startDate = Carbon::now()->subDays(30);
                    $endDate = Carbon::now();
                }
                break;
            default:
                $startDate = Carbon::now()->subDays(30);
                $endDate = Carbon::now();
        }
        
        // Get filtered orders data
        $orders = Orders::whereBetween('created_at', [$startDate, $endDate])
                       ->where('status', 'Completed')
                       ->get();
        
        if ($type === 'excel') {
            // For Excel export, you'd use Laravel Excel package
            // return Excel::download(new OrdersExport($orders), 'sales_report_' . date('Y-m-d') . '.xlsx');
            return response()->json(['message' => 'Excel export would be implemented here', 'data' => $orders]);
        } elseif ($type === 'pdf') {
            // For PDF export, you'd use DomPDF or similar
            // $pdf = PDF::loadView('reports.pdf', compact('orders', 'startDate', 'endDate'));
            // return $pdf->download('sales_report_' . date('Y-m-d') . '.pdf');
            return response()->json(['message' => 'PDF export would be implemented here', 'data' => $orders]);
        }
    }
}