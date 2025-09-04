<?php

namespace App\Http\Controllers;
use App\Models\PurchaseOrder;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Receipt;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Models\Product;
use App\Models\Orders;
use Illuminate\Support\Facades\DB;
use Users;



class ViewController extends Controller{


    public function profile()
    {
        $user = auth()->user();
        return view('profile', compact('user'));
    }

    public function showStaffs()
    {
        $user = auth()->user();
        $query = User::query();
        $query->whereIn('user_type', ['admin', 'staff']);

        $search = request('search');
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                  ->orWhere('username', 'like', "%$search%")
                  ->orWhere('user_type', 'like', value: "%$search%")
                  ->orWhere('email', 'like', value: "%$search%")
                  ->orWhere('name', 'like', "%$search%");

            });
        }
        $users = $query->paginate(10);
        
        // Append search parameter to pagination links
        if ($search) {
            $users->appends(['search' => $search]);
        }
        
        return view('staffs', compact('users', 'user', 'search'));
    }

    public function showCustomers()
    {
        $search = request('search');

        $users = User::where('user_type', 'Customer')
            ->when($search, function ($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('id', 'like', "%$search%")
                    ->orWhere('store_name', 'like', "%$search%")
                    ->orWhere('acc_status', 'like', "%$search%")
                    ->orWhere('username', 'like', "%$search%");
                });
            })
            ->withCount([
                'orders as orderCount' => function ($q) {
                    $q->where('status', 'Completed');
                }
            ])
            ->withSum([
                'orders as totalOrders' => function ($q) {
                    $q->where('status', 'Completed');
                }
            ], 'total_price') 
            ->withMax('orders as lastOrder', 'created_at')
            ->paginate(25);

        $verifiedCustomersCount = User::where('user_type', 'Customer')
            ->where('acc_status', 'accepted')
            ->count();

        return view('customers', compact('users', 'verifiedCustomersCount', 'search'));
    }


    public function viewCustomer($id)
    {
        $customer = User::findOrFail($id);

        $receipts = Receipt::where('id', $id)
        ->orderBy('created_at', 'desc')
        ->take(10)                               
        ->paginate(2, ['*'], 'receipts_page');


        $orders = Orders::select(
                'orders.order_id',
                'orders.status',
                DB::raw('SUM(orders.quantity) as total_quantity'),
                DB::raw('SUM(orders.total_price) as total_price'),
                DB::raw('MAX(orders.updated_at) as action_at')
            )
            ->where('orders.customer_id', $id)
            ->groupBy('orders.order_id', 'orders.status')
            ->orderBy('action_at', 'desc')
            ->paginate(7, ['*'], 'orders_page');

        $purchaseOrders = PurchaseOrder::where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(7, ['*'], 'purchaseOrder_page');


        return view('customer_view', compact('customer', 'receipts', 'orders', 'purchaseOrders'));
    }


    public function viewStaff($id)
    {
        $staff = User::findOrFail($id);
        
        if (auth()->user()->user_type !== 'Admin' && auth()->user()->id !== $staff->id) {
            abort(403, 'Unauthorized access');
        }
        
        return view('staff_view', compact('staff'));
    }
    public function dashboard()
    {
        $user = auth()->user();
        return view('dashboard', compact('user'));
    }

    public function staffs()
    {

        $user = auth()->user();
        return view('staffs', compact('user'));
    }
    public function acceptCustomer($id)
    {
        $customer = User::findOrFail($id);
        $customer->acc_status = 'accepted';
        $customer->save();
        return redirect()->route('customer.view', $id)->with('success', 'Customer accepted successfully!');
    }

    public function activateCustomer($id)
    {
        $customer = User::findOrFail($id);
        $customer->acc_status = 'Active';
        $customer->save();
        return redirect()->route('customer.view', $id)->with('success', 'Customer activated successfully! They can now access the system.');
    }

    public function suspendCustomer($id)
    {
        $customer = User::findOrFail($id);
        $customer->acc_status = 'Suspended';
        $customer->save();
        return redirect()->route('customer.view', $id)->with('success', 'Customer Suspended successfully!');
    }

    // Staff Management Methods
    public function updateStaffProfile(Request $request, $id)
    {
        $staff = User::findOrFail($id);
        
        // Check permissions
        if (auth()->user()->user_type !== 'Admin' && auth()->user()->id !== $staff->id) {
            abort(403, 'Unauthorized access');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
        ]);
        
        $staff->update([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
        ]);
        
        return redirect()->route('staff.view', $id)->with('success', 'Profile updated successfully!');
    }

    public function changeStaffPassword(Request $request, $id)
    {
        $staff = User::findOrFail($id);
        
        // Check permissions
        if (auth()->user()->user_type !== 'Admin' && auth()->user()->id !== $staff->id) {
            abort(403, 'Unauthorized access');
        }
        
        // If admin is changing someone else's password, don't require current password
        if (auth()->user()->user_type === 'Admin' && auth()->user()->id !== $staff->id) {
            $request->validate([
                'new_password' => 'required|string|min:8|confirmed',
            ]);
        } else {
            // For self-password change, require current password
            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|string|min:8|confirmed',
            ]);
            
            // Verify current password
            if (!Hash::check($request->current_password, $staff->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect']);
            }
        }
        
        $staff->update([
            'password' => Hash::make($request->new_password),
        ]);
        
        return redirect()->route('staff.view', $id)->with('success', 'Password changed successfully!');
    }

    public function uploadStaffImage(Request $request, $id)
    {
        $staff = User::findOrFail($id);
        
        // Check permissions
        if (auth()->user()->user_type !== 'Admin' && auth()->user()->id !== $staff->id) {
            abort(403, 'Unauthorized access');
        }
        
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);
        
        if ($request->hasFile('image')) {
            $imageData = file_get_contents($request->file('image')->getRealPath());
            $base64 = base64_encode($imageData);
            $mime = $request->file('image')->getMimeType();
            
            $staff->update([
                'image' => $base64,
                'image_mime' => $mime,
            ]);
        }
        
        return redirect()->route('staff.view', $id)->with('success', 'Image uploaded successfully!');
    }

    public function updateStaffStatus(Request $request, $id)
    {
        $staff = User::findOrFail($id);
        
        // Only admins can update staff status
        if (auth()->user()->user_type !== 'Admin') {
            abort(403, 'Unauthorized access');
        }
        
        $request->validate([
            'acc_status' => 'required|in:active,pending,Suspended',
        ]);
        
        $staff->update(['acc_status' => $request->acc_status]);
        
        $statusMessages = [
            'active' => 'Staff account activated successfully!',
            'pending' => 'Staff account set to pending status!',
            'Suspended' => 'Staff account Suspended successfully!'
        ];
        
        return redirect()->route('staff.view', $id)->with('success', $statusMessages[$request->acc_status]);
    }

    public function deactivateStaff($id)
    {
        $staff = User::findOrFail($id);
        
        // Only admins can deactivate staff
        if (auth()->user()->user_type !== 'Admin') {
            abort(403, 'Unauthorized access');
        }
        
        $staff->update(['acc_status' => 'Suspended']);
        
        return redirect()->route('staff.view', $id)->with('success', 'Staff account deactivated successfully!');
    }

    public function deleteStaff($id)
    {
        $staff = User::findOrFail($id);
        
        // Only admins can delete staff
        if (auth()->user()->user_type !== 'Admin') {
            abort(403, 'Unauthorized access');
        }
        
        // Prevent admin from deleting themselves
        if (auth()->user()->id === $staff->id) {
            return back()->withErrors(['error' => 'You cannot delete your own account']);
        }
        
        $staff->delete();
        
        return redirect()->route('staffs')->with('success', 'Staff account deleted successfully!');
    }


    public function showDashboard()
        {

            $user = auth()->user();
            $id = $user->id;


            // Recent Activities: Receipts verified today
            $weekStart = Carbon::now()->startOfWeek();
            $weekEnd = Carbon::now()->endOfWeek();
            $today = Carbon::today();
            $verifiedReceiptsToday = Receipt::where(function ($query) use ($today) {
                $query->whereNotNull('verified_by')
                    ->whereDate('verified_at', $today);
            })
            ->orWhereIn('status', ['Cancelled', 'Rejected'])
            ->orderByDesc('verified_at')
            ->limit(5)
            ->get(['receipt_id', 'verified_by', 'receipt_number', 'verified_at', 'status']);

            $verifiedOrdersToday = Orders::where(function ($query) use ($today) {
                $query->whereNotNull('action_by')
                    ->whereDate('action_at', $today);
            })
            ->orWhereIn('status', ['Cancelled', 'Rejected', 'Processing'])
            ->orderByDesc('action_at')
            ->limit(5)
            ->get(['order_id', 'action_by', 'customer_id', 'action_at', 'status']);



            $oneWeekAgo = Carbon::now()->subWeek();

            $pendingWeekCount = Receipt::where('status', 'Verified')
                ->where('created_at', '>=', $oneWeekAgo)
                ->count();

            $pendingWeekOrder = Orders::where('status', 'Completed')
                ->where('created_at', '>=', $oneWeekAgo)
                ->count();
            $pendingOrders = Orders::where('status', 'Pending')
                ->count();                
            $pendingPOs = PurchaseOrder::where('status', 'Pending')
                ->count();    
            $pendingDayCount = Receipt::where('status', 'Pending')->count();
            $activeUsers = User::where('last_seen_at', '>=', now()->subMinutes(15))->count();
            $pendingJoins = User::where('acc_status', 'pending')
                ->where('user_type', 'Customer')
                ->count();
            $monthlyTotal = Receipt::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_amount');
            $totalReceipts = Receipt::where('created_at', '>=', now()->subDays(7))->count();


            $userPendingReceipts = Receipt::where('status', 'Pending') 
                ->where('id', $id)
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->get();
            $userPendingOrders = Orders::where('status', 'Pending') 
                ->where('id', $id)
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->get();

            $userApprovedReceipts = Receipt::where('status', 'Verified')
                ->where('id', $id)
                ->where('created_at', '>=', $oneWeekAgo)
                ->get();

            $userVerifiedReceiptsWeek = Receipt::whereNotNull('verified_by')
                ->whereNotNull('verified_at')
                ->whereBetween('verified_at', [$weekStart, $weekEnd])
                ->where('id', $id)
                ->orderByDesc('verified_at')
                ->limit(5)
                ->get(['receipt_id', 'verified_by', 'receipt_number', 'verified_at']);

            $userVerifiedReceiptsWeek = Receipt::where('id', $id)
                ->where(function ($query) use ($weekStart, $weekEnd) {
                    $query->where(function ($q) use ($weekStart, $weekEnd) {
                        $q->whereNotNull('verified_by')
                        ->whereBetween('verified_at', [$weekStart, $weekEnd]);
                    })
                    ->orWhereIn('status', ['Cancelled', 'Rejected']);
                })
                ->orderByDesc('verified_at')
                ->limit(10)
                ->get(['receipt_id', 'verified_by', 'receipt_number', 'verified_at', 'status']);


        // Top Stores: get all customers, sum their total_amount from receipts IN THIS WEEK

            $topStores = User::where('user_type', 'Customer')
                ->whereNotNull('store_name')
                ->get()
                ->map(function($user) use ($weekStart, $weekEnd) {
                    $total = Receipt::where('id', $user->id)
                        ->whereBetween('created_at', [$weekStart, $weekEnd])
                        ->sum('total_amount');
                    return [
                        'name' => $user->store_name,
                        'sales' => (float) $total,
                    ];
                })
                ->sortByDesc('sales')
                ->take(5)
                ->values();

            $hour = now()->format('H');

            if ($hour >= 5 && $hour < 12) {
                $greeting = 'Good morning';
            } elseif ($hour >= 12 && $hour < 17) {
                $greeting = 'Good afternoon';
            } elseif ($hour >= 17 && $hour < 21) {
                $greeting = 'Good evening';
            } else {
                $greeting = 'Good night';
            }


            // Customer top products (this month)
            $monthStart = Carbon::now()->startOfMonth();
            $monthEnd = Carbon::now()->endOfMonth();
            $topProducts = Orders::select(
                    'products.name as product_name',
                    DB::raw('SUM(orders.quantity) as total_qty')
                )
                ->join('products', 'orders.product_id', '=', 'products.id')
                ->where('orders.customer_id', $id)
                ->whereBetween('orders.created_at', [$monthStart, $monthEnd])
                ->groupBy('products.name')
                ->orderByDesc('total_qty')
                ->take(10)
                ->get();

            $customerTopProductLabels = $topProducts->pluck('product_name');
            $customerTopProductQuantities = $topProducts->pluck('total_qty')->map(fn($q) => (int) $q);






        return view('dashboard', compact(
            'pendingWeekCount',
            'pendingDayCount',
            'activeUsers',
            'pendingJoins',
            'monthlyTotal',
            'totalReceipts',
            'topStores',
            'verifiedReceiptsToday',
            'greeting',
            'userApprovedReceipts',
            'userVerifiedReceiptsWeek',
            'userPendingReceipts',
            'userVerifiedReceiptsWeek',
            'pendingOrders',
            'userPendingOrders',
            'customerTopProductLabels',
            'customerTopProductQuantities',
            'pendingWeekOrder',
            'verifiedOrdersToday',
            'pendingPOs',
          
        ));

        }

        public function dashboardData(Request $request)
        {
            // Greeting
            $hour = Carbon::now()->format('H');
            $greeting = $hour < 12 ? "Good Morning" : ($hour < 18 ? "Good Afternoon" : "Good Evening");

            $user = auth()->user();
            $id = $user->id;
            // Get date range (default = this month)
            $from = $request->input('from_date', Carbon::now()->startOfMonth()->toDateString());
            $to   = $request->input('to_date', Carbon::now()->endOfMonth()->toDateString());



            // Card counts (except today)
            $purchaseOrdersCount = PurchaseOrder::where('status', 'Pending')
                ->whereDate('created_at', '!=', Carbon::today())
                ->whereBetween('order_date', [$from, $to])
                ->count();

            $ordersCount = Orders::where('status', 'Pending')
                ->whereDate('created_at', '!=', Carbon::today())
                ->groupBy('orders.order_id')
                ->whereBetween('created_at', [$from, $to])
                ->count();

            $receiptsCount = Receipt::where('status', 'Pending')
                ->whereDate('created_at', '!=', Carbon::today())
                ->whereBetween('created_at', [$from, $to])
                ->count();

            $usersCount = User::where('acc_status', 'Pending')
                ->whereDate('created_at', '!=', Carbon::today())
                ->whereBetween('created_at', [$from, $to])
                ->count();

            // New data today
            $newPurchaseOrdersCount = PurchaseOrder::where('status', 'Pending')
                ->whereDate('created_at', Carbon::today())
                ->whereBetween('created_at', [$from, $to])
                ->count();

            $newOrdersCount = Orders::where('status', 'Pending')
                ->whereDate('created_at', Carbon::today())
                ->groupBy('orders.order_id')
                ->whereBetween('created_at', [$from, $to])
                ->count();

            $newReceiptsCount = Receipt::where('status', 'Pending')
                ->whereDate('created_at', Carbon::today())
                ->whereBetween('created_at', [$from, $to])
                ->count();

            $newUsersCount = User::where('acc_status', 'Pending')
                ->whereDate('created_at', Carbon::today())
                ->whereBetween('created_at', [$from, $to])
                ->count();

            // Purchase orders per day in selected range
            $ordersPerDay = PurchaseOrder::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as total')
                )
                ->whereBetween('created_at', [$from, $to])
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            $labels = $ordersPerDay->pluck('date');
            $data   = $ordersPerDay->pluck('total');

            // Recent purchase orders
            $recentPurchaseOrders = PurchaseOrder::whereBetween('created_at', [$from, $to])
                ->orderBy('created_at', 'desc')
                ->where('status', '!=', 'Draft')
                ->take(10)
                ->get();

            // Status percentages
            $totalOrders = PurchaseOrder::whereBetween('created_at', [$from, $to])->count();

            $statusCounts = PurchaseOrder::select('status', DB::raw('COUNT(*) as total'))
                ->whereBetween('created_at', [$from, $to])
                ->groupBy('status')
                ->pluck('total', 'status');

            $statusPercents = $statusCounts->map(function ($count) use ($totalOrders) {
                return $totalOrders > 0 ? round(($count / $totalOrders) * 100, 2) : 0;
            });

            // Fulfilled orders
            $completedOrders = PurchaseOrder::where('status', 'Delivered')
                ->whereBetween('created_at', [$from, $to])
                ->count();



   

            $customerPendings = PurchaseOrder::where('status', 'Delivered')
                ->where('user_id', $id)
                ->whereDate('delivered_at', '!=', Carbon::today())
                ->whereBetween('created_at', [$from, $to])
                ->count();

            $customerOrders = Orders::where('status', 'Completed')
                ->where('customer_id', $id)
                ->groupBy('orders.order_id')
                ->whereDate('action_at', '!=', Carbon::today())
                ->whereBetween('created_at', [$from, $to])
                ->count();

            $customerReceipts = Receipt::where('status', 'Verified')
                ->where('id', $id)
                ->whereDate('verified_at', '!=', Carbon::today())
                ->whereBetween('created_at', [$from, $to])
                ->count();

            // New data today
            $newCustomerPendings = PurchaseOrder::where('status', 'Delivered')
                ->where('user_id', $id)
                ->whereDate('delivered_at', Carbon::today())
                ->whereBetween('created_at', [$from, $to])
                ->count();

            $newCustomerOrders = Orders::where('status', 'Completed')
                ->where('customer_id', $id)
                ->whereDate('action_at', Carbon::today())
                ->whereBetween('created_at', [$from, $to])
                ->count();

            $newCustomerReceipts = Receipt::where('status', 'Verified')
                ->where('id', $id)
                ->whereDate('verified_at', Carbon::today())
                ->whereBetween('created_at', [$from, $to])
                ->count();

            $recentOrder = PurchaseOrder::whereBetween('created_at', [$from, $to])
                ->where('user_id', $id)
                ->orderBy('created_at', 'desc')
                ->first();


            $spendingSummary = PurchaseOrder::where('user_id', $id)
                ->selectRaw("
                    YEARWEEK(order_date, 1) as week,
                    MIN(DATE(order_date)) as week_start,
                    SUM(grand_total) as total_spent
                ")
                ->where('status', 'Delivered')
                ->whereBetween('order_date', [$from, $to])
                ->groupBy(DB::raw("YEARWEEK(order_date, 1)"))
                ->orderBy('week')
                ->get();

            $spendingLabels = $spendingSummary->map(function ($row) {
                $start = Carbon::parse($row->week_start);
                return $start->format('M d') . ' - ' . $start->copy()->addDays(6)->format('M d');
            });

            $spendingData = $spendingSummary->pluck('total_spent');







            return view('dashboard', compact(
                'greeting',
                'purchaseOrdersCount',
                'ordersCount',
                'receiptsCount',
                'usersCount',
                'newPurchaseOrdersCount',
                'newOrdersCount',
                'newReceiptsCount',
                'newUsersCount',
                'labels',
                'data',
                'recentPurchaseOrders',
                'statusPercents',
                'completedOrders',
                'customerPendings',
                'customerOrders',
                'customerReceipts',
                'newCustomerPendings',
                'newCustomerOrders',
                'newCustomerReceipts',
                'recentOrder',
                'spendingSummary',
                'spendingLabels',
                'spendingData',

            ));
        }



        public function ordersDetails($order_id)
        {
            $user = auth()->user();
            $order = Receipt::findOrFail($order_id);
            if ($order->order_id !== $user->id && $user->user_type !== 'Admin' && $user->user_type !== 'Staff') {
                abort(403, 'Unauthorized access');
            }
            return view('order_details', compact('order', 'user'));
        }
        


}