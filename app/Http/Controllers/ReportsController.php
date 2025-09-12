<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Orders;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\User;
use function PHPUnit\Framework\assertContainsOnlyInstancesOf;
use App\Models\Product;
use App\Models\Receipt;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CustomersExport;
use App\Exports\OrdersExport as SalesOrdersExport;
use App\Exports\OrdersSummaryExport;
use App\Exports\ProductsExport;
use App\Exports\ReceiptsExport;
use App\Models\PurchaseOrder;
use DatePeriod;
use DateInterval;
use DateTime;


class ReportsController extends Controller
{

    public function customerReports(Request $request)
{
    $user = auth()->user();
    
    // Only allow customers to access their own reports
    if (in_array($user->user_type, ['Admin', 'Staff'])) {
        // If admin/staff, redirect to admin reports
        return redirect()->route('reports');
    }

    // Date range logic
    $dateRange = $request->get('date_range', 'last_30_days');
    $fromDate = $request->get('from_date');
    $toDate = $request->get('to_date');

    switch ($dateRange) {
        case 'last_7_days':
            $startDate = now()->subDays(7);
            $endDate = now();
            break;
        case 'last_3_months':
            $startDate = now()->subMonths(3);
            $endDate = now();
            break;
        case 'custom':
            $startDate = $fromDate ? Carbon::parse($fromDate) : now()->subDays(30);
            $endDate = $toDate ? Carbon::parse($toDate) : now();
            break;
        default: // last_30_days
            $startDate = now()->subDays(30);
            $endDate = now();
            break;
    }

    // Customer's Orders Data
    $myOrders = DB::table('orders')
        ->select('order_id', 'status', 
            DB::raw('SUM(quantity) as total_quantity'), 
            DB::raw('SUM(total_price) as total_price'),
            DB::raw('MAX(action_at) as action_at'),
            DB::raw('MAX(created_at) as created_at'))
        ->where('customer_id', $user->id)
        ->whereBetween('created_at', [$startDate, $endDate])
        ->groupBy('order_id', 'status')
        ->orderBy('created_at', 'desc')
        ->get();

    $myOrdersCount = $myOrders->count();
    $myCompletedOrders = $myOrders->where('status', 'Completed')->count();
    $myTotalSpent = $myOrders->where('status', 'Completed')->sum('total_price');
    $myAverageOrderValue = $myCompletedOrders > 0 ? $myTotalSpent / $myCompletedOrders : 0;

    // Monthly spend data
    $myMonthlySpend = DB::table('orders')
        ->select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(total_price) as total'))
        ->where('customer_id', $user->id)
        ->where('status', 'Completed')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->groupBy(DB::raw('MONTH(created_at)'))
        ->pluck('total', 'month');

    // Top products for customer
    $myTopProducts = DB::table('orders')
        ->join('products', 'orders.product_id', '=', 'products.id')
        ->select('products.name as product_name', DB::raw('SUM(orders.quantity) as total_quantity'))
        ->where('orders.customer_id', $user->id)
        ->whereBetween('orders.created_at', [$startDate, $endDate])
        ->groupBy('products.id', 'products.name')
        ->orderBy('total_quantity', 'desc')
        ->limit(10)
        ->get();

    // Customer's Purchase Orders Data
    $myPurchaseOrders = PurchaseOrder::where('user_id', $user->id)
        ->whereBetween('order_date', [$startDate, $endDate])
        ->orderBy('order_date', 'desc')
        ->limit(5)

        ->get();

    $myPurchaseOrdersCount = $myPurchaseOrders->count();
    $myPurchaseOrdersTotal = $myPurchaseOrders->where('status', 'Delivered')->sum('grand_total');
    
    // Purchase order statistics by status
    $myPOPending = $myPurchaseOrders->where('status', 'Pending')->count();
    $myPOProcessing = $myPurchaseOrders->where('status', 'Processing')->count();
    $myPOCompleted = $myPurchaseOrders->where('status', 'Delivered')->count();
    $myPOCancelled = $myPurchaseOrders->where('status', 'Cancelled')->count();
    $myPORejected = $myPurchaseOrders->where('status', 'Cancelled')->count();

    // Monthly PO data
    $myPOMonthlyData = collect();
    if ($myPurchaseOrders->count() > 0) {
        $myPOMonthlyData = $myPurchaseOrders->groupBy(function($po) {
            return $po->order_date->format('Y-m');
        })->map(function($pos, $month) {
            return [
                'count' => $pos->count(),
                'value' => $pos->sum('grand_total')
            ];
        });
    }

 
    $myReceipts = DB::table('receipts')
        ->where('id', $user->id)
        ->whereBetween('created_at', [$startDate, $endDate])
        ->get();
    
    $myReceiptsCount = $myReceipts->count();
    $myReceiptsPending = $myReceipts->where('status', 'Pending')->count();
    $myReceiptsVerified = $myReceipts->where('status', 'Verified')->count();
    $myReceiptsCancelled = $myReceipts->where('status', 'Cancelled')->count();
    $myReceiptsRejected = $myReceipts->where('status', 'Rejected')->count();
    $myReceiptsVerifiedAmount = $myReceipts
        ->where('status', 'Verified')
        ->where('id', $user->id)
        ->sum('total_amount');

  

    return view('reports/customer', compact(
        'startDate',
        'endDate',
        'myOrders',
        'myOrdersCount',
        'myCompletedOrders',
        'myTotalSpent',
        'myAverageOrderValue',
        'myMonthlySpend',
        'myTopProducts',
        'myPurchaseOrders',
        'myPurchaseOrdersCount',
        'myPurchaseOrdersTotal',
        'myPOPending',
        'myPOProcessing',
        'myPOCompleted',
        'myPOCancelled',
        'myPOMonthlyData',
        'myReceiptsCount',
        'myReceiptsPending',
        'myReceiptsVerified',
        'myReceiptsCancelled',
        'myReceiptsRejected',
        'myReceiptsVerifiedAmount'
    ));
}
private function getCustomerAnalytics($startDate, $endDate)
{
    // Customer analytics with date filtering
    $totalUsers = User::where('acc_status', 'Active')
        ->where('user_type', 'Customer')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->count();

    // New users in selected date range
    $newThisMonth = User::where('acc_status', 'Active')
        ->where('user_type', 'Customer')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->count();

    // Pending users created in date range
    $pendingUsers = User::where('acc_status', 'Pending')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->count();

    // Customer activity metrics from POs and Receipts
    $customerActivity = DB::table('users as u')
        ->leftJoin('purchase_orders as po', function($join) use ($startDate, $endDate) {
            $join->on('po.user_id', '=', 'u.id')
                 ->whereBetween('po.order_date', [$startDate, $endDate]);
        })
        ->leftJoin('receipts as r', function($join) use ($startDate, $endDate) {
            $join->on('r.po_id', '=', 'po.po_id')
                 ->whereBetween('r.created_at', [$startDate, $endDate]);
        })
        ->where('u.user_type', 'Customer')
        ->groupBy('u.id', 'u.store_name', 'u.email', 'u.mobile', 'u.telephone', 'u.address', 'u.name', 'u.created_at', 'u.acc_status')
        ->select(
            'u.id', 'u.store_name', 'u.email', 'u.mobile', 'u.telephone', 'u.address', 'u.name', 'u.created_at', 'u.acc_status',
            DB::raw('COUNT(DISTINCT po.po_id) as po_count'),
            DB::raw('COALESCE(SUM(po.grand_total), 0) as po_value'),
            DB::raw("COALESCE(SUM(CASE WHEN r.status = 'Verified' THEN r.total_amount ELSE 0 END), 0) as collections"),
            DB::raw("COALESCE(MAX(po.order_date), NULL) as last_po_date"),
            DB::raw("COALESCE(MAX(r.created_at), NULL) as last_receipt_date")
        )
        ->get();

    // Outstanding per customer
    $customerActivity = $customerActivity->map(function($c) {
        $c->outstanding = max(($c->po_value ?? 0) - ($c->collections ?? 0), 0);
        return $c;
    });

    // Top customers by PO value in range
    $topCustomersByPO = DB::table('purchase_orders as po')
        ->join('users as u', 'u.id', '=', 'po.user_id')
        ->whereBetween('po.order_date', [$startDate, $endDate])
        ->select('u.id as customer_id', 'u.store_name', DB::raw('SUM(po.grand_total) as total_po_value'))
        ->groupBy('u.id', 'u.store_name')
        ->orderByDesc('total_po_value')
        ->limit(10)
        ->get();

    // Most-bought product per customer (by quantity) within range
    $customerTopProductsRaw = DB::table('purchase_order_items as poi')
        ->join('purchase_orders as po', 'po.po_id', '=', 'poi.po_id')
        ->join('users as u', 'u.id', '=', 'po.user_id')
        ->join('products as p', 'p.id', '=', 'poi.product_id')
        ->whereBetween('po.order_date', [$startDate, $endDate])
        ->where('po.status', 'Delivered')
        ->select('u.id as customer_id', 'p.id as product_id', 'p.name as product_name',
            DB::raw('SUM(poi.quantity) as total_quantity'),
            DB::raw('SUM(poi.total_price) as total_revenue'))
        ->groupBy('u.id', 'p.id', 'p.name')
        ->get()
        ->groupBy('customer_id')
        ->map(function($rows) {
            return $rows->sortByDesc('total_quantity')->first();
        });

    return [
        'totalUsers' => $totalUsers,
        'newThisMonth' => $newThisMonth,
        'pendingUsers' => $pendingUsers,
        'customers' => $customerActivity,
        'topCustomersByPO' => $topCustomersByPO,
        'customerTopProducts' => $customerTopProductsRaw
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
    // Product performance based on Purchase Order Items
    $poiQuery = DB::table('purchase_order_items as poi')
        ->join('purchase_orders as po', 'po.po_id', '=', 'poi.po_id')
        ->join('products as p', 'p.id', '=', 'poi.product_id')
        ->whereBetween('po.order_date', [$startDate, $endDate])
        ->where('po.status', 'Delivered');

    $bestSellingProducts = (clone $poiQuery)
        ->select('p.id as product_id', 'p.name as product_name',
            DB::raw('SUM(poi.quantity) as total_quantity'),
            DB::raw('SUM(poi.total_price) as total_revenue'))
        ->groupBy('p.id', 'p.name')
        ->orderByDesc('total_quantity')
        ->get();

    $topByRevenue = (clone $poiQuery)
        ->select('p.id as product_id', 'p.name as product_name',
            DB::raw('SUM(poi.total_price) as total_revenue'),
            DB::raw('SUM(poi.quantity) as total_quantity'))
        ->groupBy('p.id', 'p.name')
        ->orderByDesc('total_revenue')
        ->limit(10)
        ->get();

    $topByQuantity = (clone $poiQuery)
        ->select('p.id as product_id', 'p.name as product_name',
            DB::raw('SUM(poi.quantity) as total_quantity'),
            DB::raw('SUM(poi.total_price) as total_revenue'))
        ->groupBy('p.id', 'p.name')
        ->orderByDesc('total_quantity')
        ->limit(10)
        ->get();

    // Monthly product revenue (aggregate)
    $monthlyProductRevenue = DB::table('purchase_order_items as poi')
        ->join('purchase_orders as po', 'po.po_id', '=', 'poi.po_id')
        ->whereBetween('po.order_date', [$startDate, $endDate])
        ->where('po.status', 'Delivered')
        ->selectRaw('DATE_FORMAT(po.order_date, "%Y-%m") as ym, SUM(poi.total_price) as total')
        ->groupBy('ym')
        ->pluck('total', 'ym');

    // Fill month labels
    $productMonthlyLabels = [];
    $productMonthlyValues = [];
    $period = new DatePeriod(
        new DateTime(Carbon::parse($startDate)->format('Y-m-01')),
        new DateInterval('P1M'),
        (new DateTime(Carbon::parse($endDate)->format('Y-m-01')))->modify('+1 month')
    );
    foreach ($period as $dt) {
        $ym = $dt->format('Y-m');
        $productMonthlyLabels[] = $dt->format('M Y');
        $productMonthlyValues[] = (float) ($monthlyProductRevenue[$ym] ?? 0);
    }

    // Inventory KPIs
    $productsCount = Product::count();
    $lowStock = Product::where('quantity', '<=', 10)->where('quantity', '>', 0)->count();
    $outOfStock = Product::where('quantity', 0)->count();
    $inventoryValuation = Product::select(DB::raw('SUM(quantity * price) as valuation'))->value('valuation') ?? 0;

    // Slow movers (lowest quantity shipped in range)
    $slowMovers = (clone $poiQuery)
        ->select('p.id as product_id', 'p.name as product_name', DB::raw('SUM(poi.quantity) as total_quantity'))
        ->groupBy('p.id', 'p.name')
        ->orderBy('total_quantity', 'asc')
        ->limit(10)
        ->get();

    // Zero sales products in range
    $productsWithSalesIds = (clone $poiQuery)
        ->distinct()->pluck('p.id');
    $zeroSalesProducts = Product::whereNotIn('id', $productsWithSalesIds)->select('id', 'name', 'quantity')->limit(20)->get();

    return [
        'bestSellingProducts' => $bestSellingProducts,
        'topByRevenue' => $topByRevenue,
        'topByQuantity' => $topByQuantity,
        'productsCount' => $productsCount,
        'inventoryValuation' => $inventoryValuation,
        'productMonthlyLabels' => $productMonthlyLabels,
        'productMonthlyValues' => $productMonthlyValues,
        'slowMovers' => $slowMovers,
        'zeroSalesProducts' => $zeroSalesProducts,
        'lowStock' => $lowStock,
        'outOfStock' => $outOfStock
    ];
}


private function getSalesAnalytics($startDate = null, $endDate = null)
{
    // Default range if not provided
    if (!$startDate || !$endDate) {
        $endDate = Carbon::now()->endOfMonth();
        $startDate = (clone $endDate)->subMonths(1)->startOfMonth();
    }

    $startDate = Carbon::parse($startDate)->startOfDay();
    $endDate = Carbon::parse($endDate)->endOfDay();

    // Sales KPIs from Purchase Orders and Verified Receipts
    $poQuery = PurchaseOrder::whereBetween('order_date', [$startDate, $endDate]);
    $totalPOValue = (clone $poQuery)->sum('grand_total');
    $deliveredPOs = (clone $poQuery)->where('status', 'Delivered')->count();
    $poCountAll = (clone $poQuery)->count();
    $averagePOValue = $poCountAll > 0 ? $totalPOValue / $poCountAll : 0;

    $verifiedCollections = Receipt::where('status', 'Verified')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->sum('total_amount');

    $collectionRate = $totalPOValue > 0 ? round(($verifiedCollections / $totalPOValue) * 100, 2) : 0;

    // Monthly series: PO value vs Collections
    $monthlyPO = DB::table('purchase_orders')
        ->selectRaw('DATE_FORMAT(order_date, "%Y-%m") as ym, SUM(grand_total) as total')
        ->whereBetween('order_date', [$startDate, $endDate])
        ->groupBy('ym')
        ->pluck('total', 'ym');

    $monthlyCollections = DB::table('receipts')
        ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as ym, SUM(total_amount) as total')
        ->where('status', 'Verified')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->groupBy('ym')
        ->pluck('total', 'ym');

    $labels = [];
    $monthlyPOValues = [];
    $monthlyCollectionValues = [];
    $period = new DatePeriod(
        new DateTime(Carbon::parse($startDate)->format('Y-m-01')),
        new DateInterval('P1M'),
        (new DateTime(Carbon::parse($endDate)->format('Y-m-01')))->modify('+1 month')
    );
    foreach ($period as $dt) {
        $ym = $dt->format('Y-m');
        $labels[] = $dt->format('M Y');
        $monthlyPOValues[] = (float) ($monthlyPO[$ym] ?? 0);
        $monthlyCollectionValues[] = (float) ($monthlyCollections[$ym] ?? 0);
    }

    // Top customers
    $topCustomersByPO = DB::table('purchase_orders as po')
        ->join('users as u', 'u.id', '=', 'po.user_id')
        ->whereBetween('po.order_date', [$startDate, $endDate])
        ->select('u.id', 'u.store_name', DB::raw('SUM(po.grand_total) as total_value'), DB::raw('COUNT(*) as pos'))
        ->groupBy('u.id', 'u.store_name')
        ->orderByDesc('total_value')
        ->limit(10)
        ->get();

    $topCustomersByCollections = DB::table('receipts as r')
        ->join('purchase_orders as po', 'po.po_id', '=', 'r.po_id')
        ->join('users as u', 'u.id', '=', 'po.user_id')
        ->where('r.status', 'Verified')
        ->whereBetween('r.created_at', [$startDate, $endDate])
        ->select('u.id', 'u.store_name', DB::raw('SUM(r.total_amount) as collected'))
        ->groupBy('u.id', 'u.store_name')
        ->orderByDesc('collected')
        ->limit(10)
        ->get();

    return [
        'totalSales' => $totalPOValue,
        'completedOrdersCount' => $deliveredPOs,
        'averageOrderValue' => $averagePOValue,
        'successfulPayments' => 0,
        'totalPaymentAttempts' => 0,
        'paymentSuccessRate' => $collectionRate,
        'monthlySales' => array_combine($labels, $monthlyCollectionValues),
        'monthlyPOValues' => $monthlyPOValues,
        'monthlyCollectionValues' => $monthlyCollectionValues,
        'monthlyLabels' => $labels,
        'verifiedCollections' => $verifiedCollections,
        'topCustomersByPO' => $topCustomersByPO,
        'topCustomersByCollections' => $topCustomersByCollections,
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

    private function getReceiptAnalytics($startDate, $endDate)
    {
        // Receipt status counts within date range
        $receiptStatusCounts = Receipt::select('status', DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('status')
            ->pluck('count', 'status');

        $ReceiptsverifiedCount  = $receiptStatusCounts['Verified'] ?? 0;
        $ReceiptspendingCount   = $receiptStatusCounts['Pending'] ?? 0;
        $ReceiptscancelledCount = $receiptStatusCounts['Cancelled'] ?? 0;
        $ReceiptsrejectedCount  = $receiptStatusCounts['Rejected'] ?? 0;

        // Total receipts in range
        $Receiptscount = Receipt::whereBetween('created_at', [$startDate, $endDate])->count();

        // Monetary KPIs
        $ReceiptsverifiedAmount = Receipt::where('status', 'Verified')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_amount');

        return [
            'Receiptscount' => $Receiptscount,
            'ReceiptsverifiedCount' => $ReceiptsverifiedCount,
            'ReceiptspendingCount' => $ReceiptspendingCount,
            'ReceiptscancelledCount' => $ReceiptscancelledCount,
            'ReceiptsrejectedCount' => $ReceiptsrejectedCount,
            'ReceiptsverifiedAmount' => $ReceiptsverifiedAmount,
        ];
    }

    private function getPoReceiptSummary($startDate, $endDate)
    {
        // Totals based solely on POs and Verified receipts
        $poQuery = PurchaseOrder::whereBetween('order_date', [$startDate, $endDate]);
        $poCount = (clone $poQuery)->count();
        $poDeliveredCount = (clone $poQuery)->where('status', 'Delivered')->count();
        $poGrandTotal = (clone $poQuery)->sum('grand_total');

        $verifiedReceiptsAmount = Receipt::where('status', 'Verified')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_amount');

        $outstandingBalance = max($poGrandTotal - $verifiedReceiptsAmount, 0);

        // Payment status breakdown on POs
        $paymentStatusCounts = PurchaseOrder::select('payment_status', DB::raw('COUNT(*) as count'))
            ->whereBetween('order_date', [$startDate, $endDate])
            ->groupBy('payment_status')
            ->pluck('count', 'payment_status');

        $fullyPaidCount = $paymentStatusCounts['Fully Paid'] ?? 0;
        $partiallySettledCount = $paymentStatusCounts['Partially Settled'] ?? 0;
        $processingOrUnpaidCount = ($paymentStatusCounts['Processing'] ?? 0) + ($paymentStatusCounts['Unpaid'] ?? 0);
        $overpaidCount = $paymentStatusCounts['Overpaid'] ?? 0;

        // Top products and customers from POs
        $topPOProducts = DB::table('purchase_order_items as poi')
            ->join('purchase_orders as po', 'po.po_id', '=', 'poi.po_id')
            ->join('products as p', 'p.id', '=', 'poi.product_id')
            ->whereBetween('po.order_date', [$startDate, $endDate])
            ->where('po.status', 'Delivered')
            ->select('p.name as product_name',
                DB::raw('SUM(poi.quantity) as total_quantity'),
                DB::raw('SUM(poi.total_price) as total_revenue'))
            ->groupBy('p.id', 'p.name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        $topPOCustomers = DB::table('purchase_orders as po')
            ->join('users as u', 'u.id', '=', 'po.user_id')
            ->whereBetween('po.order_date', [$startDate, $endDate])
            ->select('u.id', 'u.store_name', DB::raw('SUM(po.grand_total) as total_po_value'), DB::raw('COUNT(*) as total_pos'))
            ->groupBy('u.id', 'u.store_name')
            ->orderByDesc('total_po_value')
            ->limit(10)
            ->get();

        return [
            'poCount' => $poCount,
            'poDeliveredCount' => $poDeliveredCount,
            'poGrandTotal' => $poGrandTotal,
            'verifiedReceiptsAmount' => $verifiedReceiptsAmount,
            'outstandingBalance' => $outstandingBalance,
            'fullyPaidCount' => $fullyPaidCount,
            'partiallySettledCount' => $partiallySettledCount,
            'processingOrUnpaidCount' => $processingOrUnpaidCount,
            'overpaidCount' => $overpaidCount,
            'topPOProducts' => $topPOProducts,
            'topPOCustomers' => $topPOCustomers,
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

    // Customer-specific reports
    // if (strtolower($user->user_type) === 'customer') {
    //     // Spending analytics
    //     $customerOrdersQuery = Orders::where('customer_id', $user->id)
    //         ->whereBetween('created_at', [$startDate, $endDate]);

    //     $myTotalSpent = (clone $customerOrdersQuery)->where('status', 'Completed')->sum('total_price');
    //     $myCompletedOrders = (clone $customerOrdersQuery)->where('status', 'Completed')->distinct('order_id')->count('order_id');
    //     $myAverageOrderValue = $myCompletedOrders > 0 ? $myTotalSpent / $myCompletedOrders : 0;

    //     $myMonthlySpend = Orders::selectRaw('MONTH(created_at) as month, SUM(total_price) as total')
    //         ->where('customer_id', $user->id)
    //         ->where('status', 'Completed')
    //         ->whereBetween('created_at', [$startDate, $endDate])
    //         ->groupBy('month')
    //         ->pluck('total', 'month');

    //     // Orders status breakdown
    //     $myOrderStatusCounts = Orders::select('status', DB::raw('COUNT(DISTINCT order_id) as count'))
    //         ->where('customer_id', $user->id)
    //         ->whereBetween('created_at', [$startDate, $endDate])
    //         ->groupBy('status')
    //         ->pluck('count', 'status');

    //     $myOrdersCount = Orders::where('customer_id', $user->id)
    //         ->whereBetween('created_at', [$startDate, $endDate])
    //         ->distinct('order_id')
    //         ->count('order_id');

    //     // Orders list grouped by order_id
    //     $myOrders = Orders::select(
    //             'orders.order_id',
    //             'orders.status',
    //             DB::raw('SUM(orders.quantity) as total_quantity'),
    //             DB::raw('SUM(orders.total_price) as total_price'),
    //             DB::raw('MAX(orders.action_at) as action_at'),
    //             DB::raw('MAX(orders.created_at) as created_at')
    //         )
    //         ->where('orders.customer_id', $user->id)
    //         ->whereBetween('orders.created_at', [$startDate, $endDate])
    //         ->groupBy('orders.order_id', 'orders.status')
    //         ->orderBy('orders.order_id', 'desc')
    //         ->get();

    //     // Top products I bought in range
    //     $myTopProducts = Orders::select(
    //             'products.id as product_id',
    //             'products.name as product_name',
    //             DB::raw('SUM(orders.quantity) as total_quantity'),
    //             DB::raw('SUM(orders.quantity * orders.unit_price) as total_revenue')
    //         )
    //         ->join('products', 'orders.product_id', '=', 'products.id')
    //         ->where('orders.customer_id', $user->id)
    //         ->where('orders.status', 'Completed')
    //         ->whereBetween('orders.created_at', [$startDate, $endDate])
    //         ->groupBy('products.id', 'products.name')
    //         ->orderByDesc('total_quantity')
    //         ->get();

    //     // My receipts
    //     $myReceiptsQuery = Receipt::where('id', $user->id)
    //         ->whereBetween('created_at', [$startDate, $endDate]);

    //     $myReceiptsCount = (clone $myReceiptsQuery)->count();
    //     $myReceiptsPending = (clone $myReceiptsQuery)->where('status', 'Pending')->count();
    //     $myReceiptsVerified = (clone $myReceiptsQuery)->where('status', 'Verified')->count();
    //     $myReceiptsCancelled = (clone $myReceiptsQuery)->where('status', 'Cancelled')->count();
    //     $myReceiptsRejected = (clone $myReceiptsQuery)->where('status', 'Rejected')->count();
    //     $myReceiptsVerifiedAmount = (clone $myReceiptsQuery)->where('status', 'Verified')->sum('total_amount');

    //     return view('reports.customer', compact(
    //         'user',
    //         'dateRange', 'fromDate', 'toDate', 'startDate', 'endDate',
    //         // spending
    //         'myTotalSpent', 'myCompletedOrders', 'myAverageOrderValue', 'myMonthlySpend',
    //         // orders
    //         'myOrderStatusCounts', 'myOrdersCount', 'myTopProducts', 'myOrders',
    //         // receipts
    //         'myReceiptsCount', 'myReceiptsPending', 'myReceiptsVerified', 'myReceiptsCancelled', 'myReceiptsRejected', 'myReceiptsVerifiedAmount'
    //     ));
    // }

    $weekStart = Carbon::now()->startOfWeek();
    $weekEnd = Carbon::now()->endOfWeek();

    // Get all analytics with date filtering
    $customerAnalytics = $this->getCustomerAnalytics($startDate, $endDate);
    $orderAnalytics = $this->getOrderAnalytics($startDate, $endDate);
    $productAnalytics = $this->getProductAnalytics($startDate, $endDate);
    $salesAnalytics = $this->getSalesAnalytics($startDate, $endDate);
        $receiptAnalytics = $this->getReceiptAnalytics($startDate, $endDate);
    $poReceiptSummary = $this->getPoReceiptSummary($startDate, $endDate);

    // Extract customer analytics
    $totalUsers = $customerAnalytics['totalUsers'];
    $newThisMonth = $customerAnalytics['newThisMonth'];
    $pendingUsers = $customerAnalytics['pendingUsers'];
    $customers = $customerAnalytics['customers'];
    $topCustomersByPO_CA = $customerAnalytics['topCustomersByPO'];
    $customerTopProducts = $customerAnalytics['customerTopProducts'];

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
    $topByRevenue = $productAnalytics['topByRevenue'];
    $topByQuantity = $productAnalytics['topByQuantity'];
    $productsCount = $productAnalytics['productsCount'];
    $inventoryValuation = $productAnalytics['inventoryValuation'];
    $productMonthlyLabels = $productAnalytics['productMonthlyLabels'];
    $productMonthlyValues = $productAnalytics['productMonthlyValues'];
    $slowMovers = $productAnalytics['slowMovers'];
    $zeroSalesProducts = $productAnalytics['zeroSalesProducts'];
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

        // Extract receipt analytics
        $Receiptscount = $receiptAnalytics['Receiptscount'];
        $ReceiptsverifiedCount = $receiptAnalytics['ReceiptsverifiedCount'];
        $ReceiptspendingCount = $receiptAnalytics['ReceiptspendingCount'];
        $ReceiptscancelledCount = $receiptAnalytics['ReceiptscancelledCount'];
        $ReceiptsrejectedCount = $receiptAnalytics['ReceiptsrejectedCount'];
        $ReceiptsverifiedAmount = $receiptAnalytics['ReceiptsverifiedAmount'];

    // Extract PO/Receipt summary
    $poCount = $poReceiptSummary['poCount'];
    $poDeliveredCount = $poReceiptSummary['poDeliveredCount'];
    $poGrandTotal = $poReceiptSummary['poGrandTotal'];
    $verifiedReceiptsAmount = $poReceiptSummary['verifiedReceiptsAmount'];
    $outstandingBalance = $poReceiptSummary['outstandingBalance'];
    $fullyPaidCount = $poReceiptSummary['fullyPaidCount'];
    $partiallySettledCount = $poReceiptSummary['partiallySettledCount'];
    $processingOrUnpaidCount = $poReceiptSummary['processingOrUnpaidCount'];
    $overpaidCount = $poReceiptSummary['overpaidCount'];
    $topPOProducts = $poReceiptSummary['topPOProducts'];
    $topPOCustomers = $poReceiptSummary['topPOCustomers'];

    //purchase order
    // Purchase Orders Statistics
    $POpurchaseOrdersCount = PurchaseOrder::whereBetween('order_date', [$startDate, $endDate])->count();
    $POpendingPOs = PurchaseOrder::whereBetween('order_date', [$startDate, $endDate])->where('status', 'Pending')->count();
    $POprocessingPOs = PurchaseOrder::whereBetween('order_date', [$startDate, $endDate])->where('status', 'Processing')->count();
    $POcompletedPOs = PurchaseOrder::whereBetween('order_date', [$startDate, $endDate])->where('status', 'Delivered')->count();
    $POcancelledPOs = PurchaseOrder::whereBetween('order_date', [$startDate, $endDate])->where('status', 'Cancelled')->count();
    $POrejectedPOs = PurchaseOrder::whereBetween('order_date', [$startDate, $endDate])->where('status', 'Rejected')->count();
    $POtotalRevenue = PurchaseOrder::whereBetween('order_date', [$startDate, $endDate])->where('status', 'Delivered')->sum('grand_total');

    // Top Purchase Orders
    $topPurchaseOrders = PurchaseOrder::whereBetween('order_date', [$startDate, $endDate])
        ->where('status', '!=', 'Draft')  
        ->orderBy('grand_total', 'desc')
        ->limit(10)
        ->get();

    // Monthly data for chart
    $POmonthlyData = PurchaseOrder::selectRaw('DATE_FORMAT(order_date, "%Y-%m") as month, COUNT(*) as count, SUM(grand_total) as value')
        ->where('status', 'Delivered')  
        ->whereBetween('order_date', [$startDate, $endDate])
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->keyBy('month');

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
        'topCustomersByPO_CA',
        'customerTopProducts',
        'OrderscompletedOrders',
        'OrdersprocessingOrders',
        'OrderspendingOrders',
        'OrderscancelledOrders',
        'OrdersrejectedOrders',
        'OrdersordersCount',
        'bestSellingProducts',
        'topByRevenue',
        'topByQuantity',
        'productsCount',
        'inventoryValuation',
        'productMonthlyLabels',
        'productMonthlyValues',
        'slowMovers',
        'zeroSalesProducts',
        'lowStock',
        'outOfStock',
        'topStores',
        'Receiptscount',
        'ReceiptsverifiedCount',
        'ReceiptspendingCount',
        'ReceiptscancelledCount',
        'ReceiptsrejectedCount',
        'ReceiptsverifiedAmount',
        'POpurchaseOrdersCount',
        'POpendingPOs',
        'POprocessingPOs',
        'POcompletedPOs',
        'POcancelledPOs',
        'POtotalRevenue',
        'topPurchaseOrders',
        'POmonthlyData',
        'POrejectedPOs',
        // Summary (POs + Receipts)
        'poCount',
        'poDeliveredCount',
        'poGrandTotal',
        'verifiedReceiptsAmount',
        'outstandingBalance',
        'fullyPaidCount',
        'partiallySettledCount',
        'processingOrUnpaidCount',
        'overpaidCount',
        'topPOProducts',
        'topPOCustomers',
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

    // Orders (flat list) for Excel export view
    $orders = Orders::whereBetween('created_at', [$startDate, $endDate])
        ->orderBy('order_id')
        ->get();

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
            new SalesOrdersExport($orders, $startDate, $endDate),
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

        if ($request->get('type', 'pdf') === 'excel') {
            return Excel::download(
                new ProductsExport($products, $startDate, $endDate),
                'product_performance_' . date('Y-m-d') . '.xlsx'
            );
        }

        $pdf = \PDF::loadView('reports.product_performance_pdf', [
            'products' => $products,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);

        return $pdf->download('product_performance_' . date('Y-m-d') . '.pdf');
    }

    public function exportReceipts(Request $request)
    {
        $type = $request->get('type', 'excel');
        $dateInfo = $this->parseDateRange($request);
        $startDate = $dateInfo['startDate'];
        $endDate = $dateInfo['endDate'];

        $receipts = Receipt::select(
                'receipt_id',
                'receipt_number',
                'store_name',
                'total_amount',
                'purchase_date',
                'status',
                'verified_by',
                'created_at'
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($type === 'excel') {
            return Excel::download(
                new ReceiptsExport($receipts, $startDate, $endDate),
                'receipts_report_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.xlsx'
            );
        }

        $pdf = \PDF::loadView('reports.receipts_pdf', [
            'receipts' => $receipts,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
        return $pdf->download('receipts_report_' . now()->format('Y-m-d') . '.pdf');
    }
    // public function exportOrders(Request $request)
    // {
    //     $startDate = $request->from_date ? Carbon::parse($request->from_date) : Carbon::now()->subDays(30);
    //     $endDate = $request->to_date ? Carbon::parse($request->to_date) : Carbon::now();

    //     $statusOrder = ['Completed', 'Pending', 'Processing', 'Cancelled', 'Rejected'];

    //     $orders = Orders::select(
    //             'orders.order_id',
    //             'users.store_name',
    //             'orders.status',
    //             DB::raw('SUM(orders.quantity) as total_quantity'),
    //             DB::raw('SUM(orders.total_price) as total_price'),
    //             DB::raw('MAX(orders.updated_at) as action_at') 
    //         )
    //         ->join('users', 'users.id', '=', 'orders.customer_id')
    //         ->whereBetween('orders.created_at', [$startDate, $endDate])
    //         ->groupBy('orders.order_id', 'users.store_name', 'orders.status')
    //         ->orderByRaw("
    //             FIELD(orders.status, 'Completed', 'Pending', 'Processing', 'Cancelled', 'Rejected')
    //         ")
    //         ->orderBy('orders.order_id', 'desc')
    //         ->get();

    //     $ordersCount = Orders::distinct('order_id')->count('order_id');

    //     $completedOrders = Orders::where('status', 'Completed')
    //         ->distinct('order_id')
    //         ->count('order_id');

    //     $processingOrders = Orders::where('status', 'Processing')
    //         ->distinct('order_id')
    //         ->count('order_id');

    //     $pendingOrders = Orders::where('status', 'Pending')
    //         ->distinct('order_id')
    //         ->count('order_id');

    //     $cancelledOrders = Orders::where('status', 'Cancelled')
    //         ->distinct('order_id')
    //         ->count('order_id');

    //     $rejectedOrders = Orders::where('status', 'Rejected')
    //         ->distinct('order_id')
    //         ->count('order_id');


    //         if ($request->get('type', 'pdf') === 'excel') {
    //             return Excel::download(
    //                 new OrdersSummaryExport($orders, $startDate, $endDate),
    //                 'orders_report_' . now()->format('Y-m-d') . '.xlsx'
    //             );
    //         }

    //         $pdf = \PDF::loadView('reports.orders_pdf', [
    //             'startDate' => $startDate,
    //             'endDate' => $endDate,
    //             'ordersCount' => $ordersCount,
    //             'completedOrders' => $completedOrders,
    //             'processingOrders' => $processingOrders,
    //             'pendingOrders' => $pendingOrders,
    //             'cancelledOrders' => $cancelledOrders,
    //             'rejectedOrders' => $rejectedOrders,
    //             'orders' => $orders
    //         ]);

    //         return $pdf->download('orders_report_' . now()->format('Y-m-d') . '.pdf');
    //     }



    public function exportOrders(Request $request) 
{
    $startDate = $request->from_date ? Carbon::parse($request->from_date) : Carbon::now()->subDays(30);
    $endDate = $request->to_date ? Carbon::parse($request->to_date) : Carbon::now();

    $statusOrder = ['Completed', 'Pending', 'Processing', 'Cancelled', 'Rejected'];

    // Summary data (existing - for overview)
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
    ->orderBy('action_at', 'desc')
    ->get();




    // KPIs (with date range filter)
    $ordersCount = Orders::whereBetween('created_at', [$startDate, $endDate])
        ->distinct('order_id')->count('order_id');

    $completedOrders = Orders::whereBetween('created_at', [$startDate, $endDate])
        ->where('status', 'Completed')
        ->distinct('order_id')
        ->count('order_id');

    $processingOrders = Orders::whereBetween('created_at', [$startDate, $endDate])
        ->where('status', 'Processing')
        ->distinct('order_id')
        ->count('order_id');

    $pendingOrders = Orders::whereBetween('created_at', [$startDate, $endDate])
        ->where('status', 'Pending')
        ->distinct('order_id')
        ->count('order_id');

    $cancelledOrders = Orders::whereBetween('created_at', [$startDate, $endDate])
        ->where('status', 'Cancelled')
        ->distinct('order_id')
        ->count('order_id');

    $rejectedOrders = Orders::whereBetween('created_at', [$startDate, $endDate])
        ->where('status', 'Rejected')
        ->distinct('order_id')
        ->count('order_id');

    // Calculate total revenue
    $totalRevenue = Orders::whereBetween('created_at', [$startDate, $endDate])
        ->sum('total_price');

    $averageOrderValue = $ordersCount > 0 ? $totalRevenue / $ordersCount : 0;

    if ($request->get('type', 'pdf') === 'excel') {
        return Excel::download(
            new OrdersSummaryExport($orders, $startDate, $endDate),
            'orders_report_' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    $pdf = \PDF::loadView('reports.orders_pdf', [
        'startDate' => $startDate,
        'endDate' => $endDate,
        'ordersCount' => $ordersCount,
        'completedOrders' => $completedOrders,
        'processingOrders' => $processingOrders,
        'pendingOrders' => $pendingOrders,
        'cancelledOrders' => $cancelledOrders,
        'rejectedOrders' => $rejectedOrders,
        'totalRevenue' => $totalRevenue,
        'averageOrderValue' => $averageOrderValue,
        'orders' => $orders, // Summary data
    ]);

    return $pdf->download('orders_report_' . now()->format('Y-m-d') . '.pdf');
}

}