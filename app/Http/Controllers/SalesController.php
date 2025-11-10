<?php

namespace App\Http\Controllers;
use App\Models\SaleDiscount;
use Illuminate\Http\Request;
use App\Models\Customers;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
    public function list(Request $request){
        $user = Auth::user();
        if ($user->role === 'Customer') {
            $customer = Customers::where('user_id', $user->user_id)->firstOrFail();
            $sales = SaleDiscount::where('category', $customer->category);
        }
        elseif ($user->role !== 'Customer') {
            $sales = SaleDiscount::orderBy('updated_at', 'desc')->get();
        }
        return view('franken.prm.list', compact('sales'));
    }
    
}
