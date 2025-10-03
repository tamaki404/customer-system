<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GlobalCeiling;
use App\Models\Logs;
use App\Models\Products;
use App\Models\ProductSales;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
class GlobalCeilingController extends Controller
{
        
    public function setGlobalCeiling(Request $request){
        $user = Auth::user();
        $request->validate([
            'method' => 'required|in:Fixed,Percentage',
            'city_selected' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'staff_id' => 'required|integer',
            'fixed_price' => 'required_if:method,Fixed|nullable|numeric|min:0',
            'percentage_ceiling' => 'required_if:method,Percentage|nullable|numeric|min:0|max:100',
        ]);

        




        


        return back()->with('success', 'Ceiling price set successfully.');
     }

}
