<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;

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

        $receipts = \App\Models\Receipt::where('customer_id', $customer_id)->orderBy('created_at', 'desc')->get();
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
}
