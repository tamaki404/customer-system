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
        $users = User::where('user_type', 'Customer')->get();
        return view('customers', compact('users'));
    }
    public function viewCustomer($customer_id)
    {
        $customer = User::findOrFail($customer_id);

        $receipts = Receipt::where('customer_id', $customer_id)->orderBy('created_at', 'desc')->get();
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
    public function acceptCustomer($customer_id)
    {
        $customer = User::findOrFail($customer_id);
        $customer->acc_status = 'accepted';
        $customer->save();
        return redirect()->route('customer.view', $customer_id)->with('success', 'Customer accepted successfully!');
    }

    public function suspendCustomer($customer_id)
    {
        $customer = User::findOrFail($customer_id);
        $customer->acc_status = 'suspended';
        $customer->save();
        return redirect()->route('customer.view', $customer_id)->with('success', 'Customer suspended successfully!');
    }


public function showDashboard()
{
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

    // Top Stores: get all customers, sum their total_amount from receipts IN THIS WEEK
    $weekStart = Carbon::now()->startOfWeek();
    $weekEnd = Carbon::now()->endOfWeek();
    $topStores = User::where('user_type', 'Customer')
        ->whereNotNull('store_name')
        ->get()
        ->map(function($user) use ($weekStart, $weekEnd) {
            $total = Receipt::where('customer_id', $user->id)
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->sum('total_amount');
            return [
                'name' => $user->store_name,
                'sales' => (float) $total,
            ];
        })
        ->sortByDesc('sales')
        ->take(10)
        ->values();

    return view('dashboard', compact('pendingWeekCount', 'pendingDayCount', 'activeUsers', 'pendingJoins', 'monthlyTotal', 'totalReceipts', 'topStores'));
}

}
