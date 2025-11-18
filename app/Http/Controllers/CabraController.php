<?php

namespace App\Http\Controllers;
use App\Models\Customers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Products;
use App\Models\ProductSetting;
use App\Models\Promos;

class CabraController extends Controller
{
    public function list(Request $request)
    {
        $user = Auth::user();
        $customer = Customers::where('user_id', $user->user_id)->first(); 
        $products = Products::orderBy('name', 'asc')->paginate(25); 

        $setProducts = $customer 
            ? ProductSetting::where('customer_id', $customer->customer_id)->get() 
            : collect(); 
        
        // Load active promos with product relationship and filter by quantity > 0
        $activePromos = Promos::with('product')
            ->where('status', "Active")
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->where('quantity', '>', 0) 
            ->orderBy('quantity', 'asc') 
            ->get();

        return view('franken.prd.list', [
            'user' => $user,
            'products' => $products,
            'setProducts' => $setProducts,
            'activePromos' => $activePromos,

        ]);
    }
    }
