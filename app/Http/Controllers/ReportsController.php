<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Orders;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\User;
use function PHPUnit\Framework\assertContainsOnlyInstancesOf;
use App\Models\Product;
use Illuminate\Support\Facades\DB;


class ReportsController extends Controller
{
    
    public function reports(Request $request)
    {
        $user = auth()->user();

        $startDate = Carbon::now()->subDays(7);
        $endDate = Carbon::now();


         $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

        // customer analytics
        $totalUsers = User::where('acc_status', 'Active')
            ->where('user_type', 'Customer')
            ->count();

        $newThisMonth = User::where('acc_status', 'Active')
            ->where('user_type', 'Customer')
            ->whereBetween('created_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])
            ->count();
        $pendingUsers = User::where('acc_status', 'Pending')->count();
        $customers = User::where('user_type', 'Customer')->get();

        //order management
        $completedOrders = Orders::where('status', 'Completed')->count();
        $processingOrders = Orders::where('status', 'Processing')->count();
        $pendingOrders = Orders::where('status', 'Pending')->count();
        $cancelledOrders = Orders::where('status', 'Cancelled')->count();
        $rejectedOrders = Orders::where('status', 'Rejected')->count();
        $ordersCount = Orders::All()->count();
        $topStores = Orders::select(
                'customer_id',
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_price) as total_revenue')
            )
                ->where('status', 'Completed')
                ->groupBy('customer_id')
                ->orderByDesc('total_orders') 
                ->with(['customer' => function($query) {
                $query->select('id', 'store_name');
            }])
                ->take(10)
            ->get();

        //product performance
        $completedOrders = Product::where('status', 'Completed')->count();
        $processingOrders = Product::where('status', 'Processing')->count();
        $pendingOrders = Product::where('status', 'Pending')->count();
        $cancelledOrders = Product::where('status', 'Cancelled')->count();
        $rejectedOrders = Product::where('status', 'Rejected')->count();
        $ordersCount = Product::All()->count();


        $bestSellingProducts = Orders::select(
                'products.id as product_id',
                'products.name as product_name',
                DB::raw('SUM(orders.quantity) as total_quantity'),
                DB::raw('SUM(orders.quantity * orders.unit_price) as total_revenue')
            )
            ->join('products', 'orders.product_id', '=', 'products.id')
            ->where('orders.status', 'Completed')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->get();

        
        $productsCount = Product::count();

            $bestSellers = Product::select('products.id', 'products.name', DB::raw('SUM(orders.quantity) as total_sold'))
                ->join('orders', 'orders.product_id', '=', 'products.id')
                ->where('orders.status', 'Completed')
                ->groupBy('products.id', 'products.name')
                ->orderByDesc('total_sold')
                ->take(5) 
                ->get()->count();

            $lowStock = Product::where('quantity', '<=', 10)
                ->where('quantity', '>', 0)
                ->count();

            $outOfStock = Product::where('quantity', 0)->count();





        
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
            'endDate',
            'totalUsers',
            'newThisMonth',
            'pendingUsers',
            'customers',
            'completedOrders',
            'processingOrders',
            'pendingOrders',
            'cancelledOrders',
            'rejectedOrders',
            'ordersCount',
            'bestSellingProducts',
            'productsCount' ,
            'bestSellers',   
            'lowStock' ,   
            'outOfStock'   ,
            'topStores'

        ));
    }

    public function exportReports(Request $request) {
            $type = $request->get('type', 'excel');
            $dateRange = $request->get('date_range', 'last_30_days');
            $fromDate = $request->get('from_date');
            $toDate = $request->get('to_date');

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

            $orders = Orders::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'Completed')
            ->get()
            ->groupBy('order_id');


            if ($type === 'excel') {
                // Use Laravel Excel to export
                return Excel::download(new \App\Exports\OrdersExport($orders, $startDate, $endDate), 'sales_report_' . date('Y-m-d') . '.xlsx');
            } elseif ($type === 'pdf') {
                // Use DomPDF to export
                $pdf = \PDF::loadView('reports.pdf', [
                    'orders' => $orders,
                    'startDate' => $startDate,
                    'endDate' => $endDate
                ]);
                return $pdf->download('sales_report_' . date('Y-m-d') . '.pdf');
            }
        }

    public function exportCustomers(Request $request) {
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

            $customers = User::whereBetween('created_at', [$startDate, $endDate])
            ->where('user_type', 'Customer')
            ->get();


            if ($type === 'excel') {
                // Use Laravel Excel to export
                return Excel::download(new \App\Exports\OrdersExport($customers, $startDate, $endDate), 'sales_report_' . date('Y-m-d') . '.xlsx');
            } elseif ($type === 'pdf') {
                // Use DomPDF to export
                $pdf = \PDF::loadView('reports.costumers', [
                    'customers' => $customers,
                    'startDate' => $startDate,
                    'endDate' => $endDate
                ]);
                return $pdf->download('customers_list' . date('Y-m-d') . '.pdf');
            }
        }

    public function exportProducts(Request $request)
    {
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');

        $startDate = $fromDate ? Carbon::parse($fromDate) : Carbon::now()->subDays(30);
        $endDate = $toDate ? Carbon::parse($toDate) : Carbon::now();

        $products = Product::select(
                'products.id as product_id',
                'products.name as product_name',
                'products.status as product_status',
                'products.quantity as current_stock', 
                'products.price as unit_price',
                DB::raw('COALESCE(SUM(orders.quantity), 0) as total_quantity'),
                DB::raw('COALESCE(SUM(orders.quantity * orders.unit_price), 0) as total_revenue')
            )
            ->leftJoin('orders', function($join) use ($startDate, $endDate) {
                $join->on('orders.product_id', '=', 'products.id')
                    ->whereBetween('orders.created_at', [$startDate, $endDate])
                    ->where('orders.status', 'Completed');
            })
            ->groupBy(
                'products.id',
                'products.name',
                'products.status',
                'products.price',
                'products.quantity' 
            )
            ->orderByDesc('total_quantity') 
            ->get();

        $pdf = \PDF::loadView('reports.product_performance_pdf', [
            'products' => $products,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);

        return $pdf->download('product_performance_' . date('Y-m-d') . '.pdf');
    }

    



}