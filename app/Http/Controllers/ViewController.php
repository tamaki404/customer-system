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
        $users = User::whereIn('user_type', ['admin', 'staff'])->get();
        return view('staffs', compact('users'));
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
}
