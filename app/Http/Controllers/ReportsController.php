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
use App\Http\Controllers\CustomersExport;
use App\Http\Controllers\Excel;

class ReportsController extends Controller
{

private function getCustomerAnalytics($startDate, $endDate)
{
    // Customer analytics with date filtering
    $totalUsers = User::where('acc_status', 'Active')
        ->where('user_type', 'Customer')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->count();

    // New users in selected date range (changed from "this month" to selected range)
    $newThisMonth = User::where('acc_status', 'Active')
        ->where('user_type', 'Customer')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->count();

    // Pending users created in date range
    $pendingUsers = User::where('acc_status', 'Pending')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->count();

    // Customers list with date filtering
    $customers = User::where('user_type', 'Customer')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->get();

    return [
        'totalUsers' => $totalUsers,
        'newThisMonth' => $newThisMonth,
        'pendingUsers' => $pendingUsers,
        'customers' => $customers
    ];
}

private function getOrderAnalytics($startDate, $endDate)
{
    // Order status counts with date filtering
    $orderStatusCounts = Orders::select('status', DB::raw('COUNT(DISTINCT order_id) as count'))
        ->whereBetween('created_at', [$startDate, $endDate])
        ->groupBy('status')
        ->pluck('count', 'status');

    $OrderscompletedOrders = $orderStatusCounts['Completed'] ?? 0;
    $OrdersprocessingOrders = $orderStatusCounts['Processing'] ?? 0;
    $OrderspendingOrders    = $orderStatusCounts['Pending'] ?? 0;
    $OrderscancelledOrders  = $orderStatusCounts['Cancelled'] ?? 0;
    $OrdersrejectedOrders   = $orderStatusCounts['Rejected'] ?? 0;

    // Total orders count with date filtering
    $OrdersordersCount = Orders::distinct('order_id')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->count('order_id');

    // Top stores with date filtering
    $topStores = Orders::select(
            'customer_id',
            DB::raw('COUNT(*) as total_orders'),
            DB::raw('SUM(total_price) as total_revenue')
        )
        ->where('status', 'Completed')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->groupBy('customer_id')
        ->orderByDesc('total_orders') 
        ->with(['customer' => function($query) {
            $query->select('id', 'store_name');
        }])
        ->take(10)
        ->get();

    return [
        'OrderscompletedOrders' => $OrderscompletedOrders,
        'OrdersprocessingOrders' => $OrdersprocessingOrders,
        'OrderspendingOrders' => $OrderspendingOrders,
        'OrderscancelledOrders' => $OrderscancelledOrders,
        'OrdersrejectedOrders' => $OrdersrejectedOrders,
        'OrdersordersCount' => $OrdersordersCount,
        'topStores' => $topStores
    ];
}

private function getProductAnalytics($startDate, $endDate)
{
    // Best selling products with date filtering
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

    // Total products count (this might stay as total products, not date-filtered)
    $productsCount = Product::count();

    // Best sellers count with date filtering
    $bestSellers = Product::select('products.id', 'products.name', DB::raw('SUM(orders.quantity) as total_sold'))
        ->join('orders', 'orders.product_id', '=', 'products.id')
        ->where('orders.status', 'Completed')
        ->whereBetween('orders.created_at', [$startDate, $endDate])
        ->groupBy('products.id', 'products.name')
        ->orderByDesc('total_sold')
        ->take(5) 
        ->get()
        ->count();

    // Low stock and out of stock (these are current inventory status, not date-dependent)
    $lowStock = Product::where('quantity', '<=', 10)
        ->where('quantity', '>', 0)
        ->count();

    $outOfStock = Product::where('quantity', 0)->count();

    return [
        'bestSellingProducts' => $bestSellingProducts,
        'productsCount' => $productsCount,
        'bestSellers' => $bestSellers,
        'lowStock' => $lowStock,
        'outOfStock' => $outOfStock
    ];
}

private function getSalesAnalytics($startDate, $endDate)
{
    // Apply date filtering to sales queries
    $ordersQuery = Orders::whereBetween('created_at', [$startDate, $endDate]);
    
    $totalSales = $ordersQuery->where('status', 'Completed')->sum('total_price');
    $completedOrdersCount = $ordersQuery->where('status', 'Completed')->count();
    
    $averageOrderValue = $completedOrdersCount > 0 
        ? $totalSales / $completedOrdersCount 
        : 0;

    // Payment success rate with date filtering
    $successfulPayments = Orders::whereBetween('created_at', [$startDate, $endDate])
        ->where('status', 'Completed')
        ->distinct('order_id')
        ->count('order_id');

    $totalPaymentAttempts = Orders::whereBetween('created_at', [$startDate, $endDate])
        ->whereIn('status', ['Completed', 'Rejected', 'Pending', 'Processing'])
        ->distinct('order_id')
        ->count('order_id');

    $paymentSuccessRate = $totalPaymentAttempts > 0
        ? round(($successfulPayments / $totalPaymentAttempts) * 100, 2)
        : 0;
    
    // Monthly sales data with date filtering
    $monthlySales = Orders::selectRaw('MONTH(created_at) as month, SUM(total_price) as total')
        ->where('status', 'Completed')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->groupBy('month')
        ->pluck('total', 'month');

    return [
        'totalSales' => $totalSales,
        'completedOrdersCount' => $completedOrdersCount,
        'averageOrderValue' => $averageOrderValue,
        'successfulPayments' => $successfulPayments,
        'totalPaymentAttempts' => $totalPaymentAttempts,
        'paymentSuccessRate' => $paymentSuccessRate,
        'monthlySales' => $monthlySales
    ];
}

private function parseDateRange(Request $request)
{
    $dateRange = $request->get('date_range', 'last_30_days');
    $fromDate = $request->get('from_date');
    $toDate = $request->get('to_date');
    
    // Determine the actual date range based on selection
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

    return [
        'startDate' => $startDate,
        'endDate' => $endDate,
        'dateRange' => $dateRange,
        'fromDate' => $fromDate,
        'toDate' => $toDate
    ];
}

public function reports(Request $request)
{
    $user = auth()->user();

    // Parse date range
    $dateInfo = $this->parseDateRange($request);
    $startDate = $dateInfo['startDate'];
    $endDate = $dateInfo['endDate'];
    $dateRange = $dateInfo['dateRange'];
    $fromDate = $dateInfo['fromDate'];
    $toDate = $dateInfo['toDate'];

    $weekStart = Carbon::now()->startOfWeek();
    $weekEnd = Carbon::now()->endOfWeek();

    // Get all analytics with date filtering
    $customerAnalytics = $this->getCustomerAnalytics($startDate, $endDate);
    $orderAnalytics = $this->getOrderAnalytics($startDate, $endDate);
    $productAnalytics = $this->getProductAnalytics($startDate, $endDate);
    $salesAnalytics = $this->getSalesAnalytics($startDate, $endDate);

    // Extract customer analytics
    $totalUsers = $customerAnalytics['totalUsers'];
    $newThisMonth = $customerAnalytics['newThisMonth'];
    $pendingUsers = $customerAnalytics['pendingUsers'];
    $customers = $customerAnalytics['customers'];

    // Extract order analytics
    $OrderscompletedOrders = $orderAnalytics['OrderscompletedOrders'];
    $OrdersprocessingOrders = $orderAnalytics['OrdersprocessingOrders'];
    $OrderspendingOrders = $orderAnalytics['OrderspendingOrders'];
    $OrderscancelledOrders = $orderAnalytics['OrderscancelledOrders'];
    $OrdersrejectedOrders = $orderAnalytics['OrdersrejectedOrders'];
    $OrdersordersCount = $orderAnalytics['OrdersordersCount'];
    $topStores = $orderAnalytics['topStores'];

    // Extract product analytics
    $bestSellingProducts = $productAnalytics['bestSellingProducts'];
    $productsCount = $productAnalytics['productsCount'];
    $bestSellers = $productAnalytics['bestSellers'];
    $lowStock = $productAnalytics['lowStock'];
    $outOfStock = $productAnalytics['outOfStock'];

    // Extract sales analytics
    $totalSales = $salesAnalytics['totalSales'];
    $completedOrdersCount = $salesAnalytics['completedOrdersCount'];
    $averageOrderValue = $salesAnalytics['averageOrderValue'];
    $successfulPayments = $salesAnalytics['successfulPayments'];
    $totalPaymentAttempts = $salesAnalytics['totalPaymentAttempts'];
    $paymentSuccessRate = $salesAnalytics['paymentSuccessRate'];
    $monthlySales = $salesAnalytics['monthlySales'];

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
        'OrderscompletedOrders',
        'OrdersprocessingOrders',
        'OrderspendingOrders',
        'OrderscancelledOrders',
        'OrdersrejectedOrders',
        'OrdersordersCount',
        'bestSellingProducts',
        'productsCount',
        'bestSellers',   
        'lowStock',   
        'outOfStock',
        'topStores'
    ));
}

public function exportCustomers(Request $request) 
    {
        $type = $request->get('type', 'excel');
        
        $dateInfo = $this->parseDateRange($request);
        $startDate = $dateInfo['startDate'];
        $endDate = $dateInfo['endDate'];
        
        $customerAnalytics = $this->getCustomerAnalytics($startDate, $endDate);
        $customers = $customerAnalytics['customers'];
           
        if ($type === 'excel') {
            return Excel::download(
                new CustomersExport($customers, $startDate, $endDate), 
                'customers_report_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.xlsx'
            );
        } elseif ($type === 'pdf') {
            $pdf = \PDF::loadView('reports.customers', [
                'customers' => $customers,
                'startDate' => $startDate,
                'endDate' => $endDate
            ]);
            return $pdf->download('customers_list_' . date('Y-m-d') . '.pdf');
        }
    }
    
    // public function reports(Request $request)
    // {
    //     $user = auth()->user();

    //     $startDate = Carbon::now()->subDays(7);
    //     $endDate = Carbon::now();


    //      $weekStart = Carbon::now()->startOfWeek();
    //     $weekEnd = Carbon::now()->endOfWeek();

    //     // customer analytics
    //     $totalUsers = User::where('acc_status', 'Active')
    //         ->where('user_type', 'Customer')
    //         ->count();

    //     $newThisMonth = User::where('acc_status', 'Active')
    //         ->where('user_type', 'Customer')
    //         ->whereBetween('created_at', [
    //             Carbon::now()->startOfMonth(),
    //             Carbon::now()->endOfMonth()
    //         ])
    //         ->count();
    //     $pendingUsers = User::where('acc_status', 'Pending')->count();
    //     $customers = User::where('user_type', 'Customer')->get();

    //     //order management
    //     $orderStatusCounts = Orders::select('status', DB::raw('COUNT(DISTINCT order_id) as count'))
    //         ->groupBy('status')
    //         ->pluck('count', 'status');

    //     $OrderscompletedOrders = $orderStatusCounts['Completed'] ?? 0;
    //     $OrdersprocessingOrders = $orderStatusCounts['Processing'] ?? 0;
    //     $OrderspendingOrders    = $orderStatusCounts['Pending'] ?? 0;
    //     $OrderscancelledOrders  = $orderStatusCounts['Cancelled'] ?? 0;
    //     $OrdersrejectedOrders   = $orderStatusCounts['Rejected'] ?? 0;

    //     $OrdersordersCount = Orders::distinct('order_id')->count('order_id');

        
    //     $topStores = Orders::select(
    //             'customer_id',
    //             DB::raw('COUNT(*) as total_orders'),
    //             DB::raw('SUM(total_price) as total_revenue')
    //         )
    //             ->where('status', 'Completed')
    //             ->groupBy('customer_id')
    //             ->orderByDesc('total_orders') 
    //             ->with(['customer' => function($query) {
    //             $query->select('id', 'store_name');
    //         }])
    //             ->take(10)
    //         ->get();

    //     //product performance
    //     $completedOrders = Product::where('status', 'Completed')->count();
    //     $processingOrders = Product::where('status', 'Processing')->count();
    //     $pendingOrders = Product::where('status', 'Pending')->count();
    //     $cancelledOrders = Product::where('status', 'Cancelled')->count();
    //     $rejectedOrders = Product::where('status', 'Rejected')->count();
    //     $ordersCount = Product::All()->count();


    //     $bestSellingProducts = Orders::select(
    //             'products.id as product_id',
    //             'products.name as product_name',
    //             DB::raw('SUM(orders.quantity) as total_quantity'),
    //             DB::raw('SUM(orders.quantity * orders.unit_price) as total_revenue')
    //         )
    //         ->join('products', 'orders.product_id', '=', 'products.id')
    //         ->where('orders.status', 'Completed')
    //         ->whereBetween('orders.created_at', [$startDate, $endDate])
    //         ->groupBy('products.id', 'products.name')
    //         ->orderByDesc('total_quantity')
    //         ->get();

        
    //     $productsCount = Product::count();

    //         $bestSellers = Product::select('products.id', 'products.name', DB::raw('SUM(orders.quantity) as total_sold'))
    //             ->join('orders', 'orders.product_id', '=', 'products.id')
    //             ->where('orders.status', 'Completed')
    //             ->groupBy('products.id', 'products.name')
    //             ->orderByDesc('total_sold')
    //             ->take(5) 
    //             ->get()->count();

    //         $lowStock = Product::where('quantity', '<=', 10)
    //             ->where('quantity', '>', 0)
    //             ->count();

    //         $outOfStock = Product::where('quantity', 0)->count();





        
    //     $dateRange = $request->get('date_range', 'last_30_days');
    //     $fromDate = $request->get('from_date');
    //     $toDate = $request->get('to_date');
        
    //     // Always set startDate and endDate - determine the actual date range based on selection
    //     switch ($dateRange) {
    //         case 'last_7_days':
    //             $startDate = Carbon::now()->subDays(7);
    //             $endDate = Carbon::now();
    //             break;
    //         case 'last_30_days':
    //             $startDate = Carbon::now()->subDays(30);
    //             $endDate = Carbon::now();
    //             break;
    //         case 'last_3_months':
    //             $startDate = Carbon::now()->subMonths(3);
    //             $endDate = Carbon::now();
    //             break;
    //         case 'custom':
    //             if ($fromDate && $toDate) {
    //                 $startDate = Carbon::parse($fromDate);
    //                 $endDate = Carbon::parse($toDate);
    //             } else {
    //                 // Default to last 30 days if custom range is incomplete
    //                 $startDate = Carbon::now()->subDays(30);
    //                 $endDate = Carbon::now();
    //             }
    //             break;
    //         default:
    //             $startDate = Carbon::now()->subDays(30);
    //             $endDate = Carbon::now();
    //     }
        
    //     // Apply date filtering to queries
    //     $ordersQuery = Orders::whereBetween('created_at', [$startDate, $endDate]);
        
    //     $totalSales = $ordersQuery->where('status', 'Completed')->sum('total_price');
    //     $completedOrdersCount = $ordersQuery->where('status', 'Completed')->count();
        
    //     $averageOrderValue = $completedOrdersCount > 0 
    //         ? $totalSales / $completedOrdersCount 
    //         : 0;

        
        
    //     $successfulPayments = $ordersQuery->where('status', 'Completed')->count();


    //     $totalPaymentAttempts = $ordersQuery->whereIn('status', [
    //         'Completed', 'Rejected', 'Pending', 'Processing'
    //     ])->count();




    //     //payment success rate
    //     $successfulPayments = Orders::whereBetween('created_at', [$startDate, $endDate])
    //         ->where('status', 'Completed')
    //         ->distinct('order_id')
    //         ->count('order_id');

    //     $totalPaymentAttempts = Orders::whereBetween('created_at', [$startDate, $endDate])
    //         ->whereIn('status', ['Completed', 'Rejected', 'Pending', 'Processing'])
    //         ->distinct('order_id')
    //         ->count('order_id');

    //     $paymentSuccessRate = $totalPaymentAttempts > 0
    //         ? round(($successfulPayments / $totalPaymentAttempts) * 100, 2)
    //         : 0;
        
        
    //     // Monthly sales data with date filtering
    //     $monthlySales = Orders::selectRaw('MONTH(created_at) as month, SUM(total_price) as total')
    //         ->where('status', 'Completed')
    //         ->whereBetween('created_at', [$startDate, $endDate])
    //         ->groupBy('month')
    //         ->pluck('total', 'month');



        
    //     return view('reports', compact(
    //         'user',
    //         'successfulPayments',
    //         'totalPaymentAttempts',
    //         'paymentSuccessRate',
    //         'totalSales',
    //         'completedOrdersCount',
    //         'averageOrderValue',
    //         'monthlySales',
    //         'dateRange',
    //         'fromDate',
    //         'toDate',
    //         'startDate',
    //         'endDate',
    //         'totalUsers',
    //         'newThisMonth',
    //         'pendingUsers',
    //         'customers',
    //         'OrderscompletedOrders',
    //         'OrdersprocessingOrders',
    //         'OrderspendingOrders',
    //         'OrderscancelledOrders',
    //         'OrdersrejectedOrders',
    //         'OrdersordersCount',
    //         'bestSellingProducts',
    //         'productsCount' ,
    //         'bestSellers',   
    //         'lowStock' ,   
    //         'outOfStock'   ,
    //         'topStores'

    //     ));
    // }

public function exportReports(Request $request) {
    $type = $request->get('type', 'excel');
    $dateRange = $request->get('date_range', 'last_30_days');
    $fromDate = $request->get('from_date');
    $toDate = $request->get('to_date');

    // Date range setup
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

    // Orders query grouped by order_id and ordered
    $orders = Orders::whereBetween('created_at', [$startDate, $endDate])
        ->orderBy('order_id')
        ->get()
        ->groupBy('order_id');

    // Summary KPIs
    $totalOrders = $orders->count();
    $completedOrders = $orders->filter(fn($o) => $o->first()->status === 'Completed')->count();
    $totalSales = $orders->flatMap->all()->where('status', 'Completed')->sum('total_price');
    $averageOrderValue = $completedOrders > 0 ? $totalSales / $completedOrders : 0;

    $totalPaymentAttempts = $orders->count();
    $successfulPayments = $completedOrders;
    $paymentSuccessRate = $totalPaymentAttempts > 0
        ? round(($successfulPayments / $totalPaymentAttempts) * 100, 2)
        : 0;

    // Sales by status (count + revenue)
    $statuses = ['Completed', 'Processing', 'Pending', 'Cancelled', 'Rejected'];
    $salesByStatus = [];
    foreach ($statuses as $status) {
        $filtered = $orders->filter(fn($o) => $o->first()->status === $status);
        $salesByStatus[$status] = [
            'count' => $filtered->count(),
            'revenue' => $filtered->flatMap->all()->sum('total_price')
        ];
    }

    if ($type === 'excel') {
        return Excel::download(
            new \App\Exports\OrdersExport($orders, $startDate, $endDate),
            'sales_report_' . date('Y-m-d') . '.xlsx'
        );
    } elseif ($type === 'pdf') {
        $pdf = \PDF::loadView('reports.pdf', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalOrders' => $totalOrders,
            'completedOrders' => $completedOrders,
            'totalSales' => $totalSales,
            'averageOrderValue' => $averageOrderValue,
            'paymentSuccessRate' => $paymentSuccessRate,
            'salesByStatus' => $salesByStatus
        ]);
        return $pdf->download('sales_report_' . date('Y-m-d') . '.pdf');
    }
}

    // public function exportCustomers(Request $request) {
    //         $type = $request->get('type', 'excel');
    //         // Get the same filtered data
    //         $dateRange = $request->get('date_range', 'last_30_days');
    //         $fromDate = $request->get('from_date');
    //         $toDate = $request->get('to_date');

    //         // Determine date range (same logic as reports method)
    //         switch ($dateRange) {
    //             case 'last_7_days':
    //                 $startDate = Carbon::now()->subDays(7);
    //                 $endDate = Carbon::now();
    //                 break;
    //             case 'last_30_days':
    //                 $startDate = Carbon::now()->subDays(30);
    //                 $endDate = Carbon::now();
    //                 break;
    //             case 'last_3_months':
    //                 $startDate = Carbon::now()->subMonths(3);
    //                 $endDate = Carbon::now();
    //                 break;
    //             case 'custom':
    //                 if ($fromDate && $toDate) {
    //                     $startDate = Carbon::parse($fromDate);
    //                     $endDate = Carbon::parse($toDate);
    //                 } else {
    //                     $startDate = Carbon::now()->subDays(30);
    //                     $endDate = Carbon::now();
    //                 }
    //                 break;
    //             default:
    //                 $startDate = Carbon::now()->subDays(30);
    //                 $endDate = Carbon::now();
    //         }

    //         $customers = User::whereBetween('created_at', [$startDate, $endDate])
    //         ->where('user_type', 'Customer')
    //         ->get();


    //         if ($type === 'excel') {
    //             // Use Laravel Excel to export
    //             return Excel::download(new \App\Exports\OrdersExport($customers, $startDate, $endDate), 'sales_report_' . date('Y-m-d') . '.xlsx');
    //         } elseif ($type === 'pdf') {
    //             // Use DomPDF to export
    //             $pdf = \PDF::loadView('reports.costumers', [
    //                 'customers' => $customers,
    //                 'startDate' => $startDate,
    //                 'endDate' => $endDate
    //             ]);
    //             return $pdf->download('customers_list' . date('Y-m-d') . '.pdf');
    //         }
    //     }

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
    public function exportOrders(Request $request)
    {
        $startDate = $request->from_date ? Carbon::parse($request->from_date) : Carbon::now()->subDays(30);
        $endDate = $request->to_date ? Carbon::parse($request->to_date) : Carbon::now();

        $statusOrder = ['Completed', 'Pending', 'Processing', 'Cancelled', 'Rejected'];

        $orders = Orders::select(
                'orders.order_id',
                'users.store_name',
                'orders.status',
                DB::raw('SUM(orders.quantity) as total_quantity'),
                DB::raw('SUM(orders.total_price) as total_price'),
                DB::raw('MAX(orders.updated_at) as action_at') 
            )
            ->join('users', 'users.id', '=', 'orders.customer_id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('orders.order_id', 'users.store_name', 'orders.status')
            ->orderByRaw("
                FIELD(orders.status, 'Completed', 'Pending', 'Processing', 'Cancelled', 'Rejected')
            ")
            ->orderBy('orders.order_id', 'desc')
            ->get();

        $ordersCount = Orders::distinct('order_id')->count('order_id');

        $completedOrders = Orders::where('status', 'Completed')
            ->distinct('order_id')
            ->count('order_id');

        $processingOrders = Orders::where('status', 'Processing')
            ->distinct('order_id')
            ->count('order_id');

        $pendingOrders = Orders::where('status', 'Pending')
            ->distinct('order_id')
            ->count('order_id');

        $cancelledOrders = Orders::where('status', 'Cancelled')
            ->distinct('order_id')
            ->count('order_id');

        $rejectedOrders = Orders::where('status', 'Rejected')
            ->distinct('order_id')
            ->count('order_id');


            $pdf = \PDF::loadView('reports.orders_pdf', [
                'startDate' => $startDate,
                'endDate' => $endDate,
                'ordersCount' => $ordersCount,
                'completedOrders' => $completedOrders,
                'processingOrders' => $processingOrders,
                'pendingOrders' => $pendingOrders,
                'cancelledOrders' => $cancelledOrders,
                'rejectedOrders' => $rejectedOrders,
                'orders' => $orders
            ]);

            return $pdf->download('orders_report_' . now()->format('Y-m-d') . '.pdf');
        }



}