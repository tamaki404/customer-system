<?php

namespace App\Http\Controllers;
use App\Models\SaleDiscount;
use Illuminate\Http\Request;
use App\Models\Customers;
use App\Models\Promos;
use App\Models\Products;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Auth;

class PromosController extends Controller
{
    public function list(Request $request){
        $user = Auth::user();
        if ($user->role === 'Customer') {
            $customer = Customers::where('user_id', $user->user_id)->firstOrFail();
            $promos = Promos::with('product')
                ->where('category', $customer->category)
                ->where('status', '==', 'Active', ) 
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->where('quantity', '>', value: 0) 
                ->orderBy('quantity', 'asc')
                ->get();      
        }
        elseif ($user->role !== 'Customer') {
            $promos = Promos::with('product')
                ->orderBy('quantity', 'desc')
                ->orderBy('start_date', 'asc')
                ->orderBy('end_date', 'asc')
                ->where('status', 'Active', ) 
                ->get();            
            $products = Products::orderBy('name', 'desc')->get();

        }
        return view('franken.prm.list', compact('promos', 'products'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'value' => 'required|numeric|min:0',
            'value_type' => 'required|in:percentage,fixed',
            'category' => 'required|in:Wholesale,Distributor,HRI,Dealer',
            'product' => 'required|string|exists:products,product_id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'user_id' => 'required|string|exists:users,user_id',
            'quantity' => 'required|integer|min:0',
        ]);

        $promo = Promos::create([
            'promo_id' => 'PROMO-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5)),
            'name' => $request->name,
            'description' => $request->description,
            'value' => $request->value,
            'value_type' => $request->value_type,
            'category' => $request->category,
            'product_id' => $request->product,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'user_id' => $request->user_id,
            'quantity' => $request->quantity,
            'status' => "Active",
        ]);

        $product = $request->product;
        $date = date('Ymd');
        function randomBase36String(int $length): string {
            $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $str = '';
            for ($i = 0; $i < $length; $i++) {
                $str .= $chars[random_int(0, strlen($chars) - 1)];
            }
            return $str;
        }
                
        return redirect()->back()->with('success', 'Promo successfully listed!');
    }
    
}
