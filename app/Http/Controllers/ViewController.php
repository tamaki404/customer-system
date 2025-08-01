<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Receipt;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;


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
        $users = $query->paginate(25);
        $verifiedCustomersCount = User::where('user_type', 'Customer')->where('acc_status', 'accepted')->count();
        
        // Append search parameter to pagination links
        if ($search) {
            $users->appends(['search' => $search]);
        }
        
        return view('customers', compact('users', 'verifiedCustomersCount', 'search'));
    }
    public function viewCustomer($id)
    {
        $customer = User::findOrFail($id);

        $receipts = Receipt::where('id', $id)->orderBy('created_at', 'desc')->get();
        return view('customer_view', compact('customer', 'receipts'));
    }

    public function viewStaff($id)
    {
        $staff = User::findOrFail($id);
        
        // Check if user has permission to view this staff
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
        $customer->acc_status = 'suspended';
        $customer->save();
        return redirect()->route('customer.view', $id)->with('success', 'Customer suspended successfully!');
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
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->file('image')->extension();
            $request->file('image')->move(public_path('images'), $imageName);
            
            $staff->update(['image' => $imageName]);
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
            'acc_status' => 'required|in:active,pending,suspended',
        ]);
        
        $staff->update(['acc_status' => $request->acc_status]);
        
        $statusMessages = [
            'active' => 'Staff account activated successfully!',
            'pending' => 'Staff account set to pending status!',
            'suspended' => 'Staff account suspended successfully!'
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
        
        $staff->update(['acc_status' => 'suspended']);
        
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
