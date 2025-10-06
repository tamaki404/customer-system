<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GlobalCeiling;
use App\Models\Logs;
use App\Models\Address;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
class GlobalCeilingController extends Controller
{
        
    public function setGlobalCeiling(Request $request){
        $user = Auth::user();
        $request->validate([
            'method_select' => 'required|in:Fixed,Percentage',
            'city_selected' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'staff_id' => 'required|integer',
            'fixed_price' => 'required_if:method,Fixed|nullable|numeric|min:0',
            'percentage_ceiling' => 'required_if:method,Percentage|nullable|numeric|min:0|max:100',
        ]);

        $address = Address::where('office_city', $request->city_selected)->first();

        $date = date('Ymd');
        $ceiling_id = 'CEILING-' . $date . '-' . strtoupper(Str::random(5));
        $log_id = 'LOG-' . $date . '-' . strtoupper(Str::random(5));

        $ceiling = GlobalCeiling::create([
            'ceiling_id'     => $ceiling_id,
            'method'             => $request->method_select,
            'fixed_price'        => $request->fixed_price,
            'percentage_ceiling' => $request->percentage_ceiling,
            'city_selected'      => strtolower($request->city_selected), 
            'start_date'         => $request->start_date,
            'end_date'           => $request->end_date,
            'staff_id'           => $user->user_id,
            'status'    => 'Active',

        ]);


        Logs::create([
            'user_id'     => $user->user_id,
            'action'      => 'Created ceiling price',
            'log_id'      => $log_id,
            'description' => "Staff ($user->user_id) set a {$request->method_select} ceiling price " .
                            ($request->method_select == 'Fixed'
                                ? "₱{$request->fixed_price}"
                                : "{$request->percentage_ceiling}%") .
                            " for city ({$request->city_selected}) from ({$request->start_date}) to ({$request->end_date}).",
            'entity'      => 'GlobalCeiling',
            'entity_id'   => $ceiling->id,
        ]);


        return back()->with('success', 'Ceiling price set successfully.');
     }

}
