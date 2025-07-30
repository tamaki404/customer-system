<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Receipt;
use Carbon\Carbon;


class ViewController extends Controller
{
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
                  ->orWhere('user_type', 'like', "%$search%");
            });
        }
        $users = $query->get();
        return view('staffs', compact('users', 'user'));
    }

    public function showCustomers()
    {
        $query = User::where('user_type', 'Customer');
        $search = request('search');
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                  ->orWhere('store_name', 'like', "%$search%")
                  ->orWhere('acc_status', 'like', "%$search%")
                  ->orWhere('username', 'like', "%$search%");
            });
        }
        $users = $query->get();
        $verifiedCustomersCount = User::where('user_type', 'Customer')->where('acc_status', 'accepted')->count();
        return view('customers', compact('users', 'verifiedCustomersCount'));
    }
    public function viewCustomer($id)
    {
        $customer = User::findOrFail($id);

        $receipts = Receipt::where('id', $id)->orderBy('created_at', 'desc')->get();
        return view('customer_view', compact('customer', 'receipts'));
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

    public function suspendCustomer($id)
    {
        $customer = User::findOrFail($id);
        $customer->acc_status = 'suspended';
        $customer->save();
        return redirect()->route('customer.view', $id)->with('success', 'Customer suspended successfully!');
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


    $oneWeekAgo = Carbon::now()->subWeek();

    $pendingWeekCount = Receipt::where('status', 'Verified')
        ->where('created_at', '>=', $oneWeekAgo)
        ->count();

    $pendingDayCount = Receipt::where('status', 'Pending')->count();
    $activeUsers = User::where('last_seen_at', '>=', now()->subMinutes(15))->count();
    $pendingJoins = User::where('acc_status', 'pending')->count();
    $monthlyTotal = Receipt::whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->sum('total_amount');
    $totalReceipts = Receipt::where('created_at', '>=', now()->subDays(7))->count();

    $userPendingReceipts = Receipt::where('status', 'Pending') 
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
    'userVerifiedReceiptsWeek'


));

}




}
