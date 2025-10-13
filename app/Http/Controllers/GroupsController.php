<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Representatives;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Suppliers;

class GroupsController extends Controller
{
    public function GroupsView(Request $request)
    {
        $user = Auth::user();
        $supplier = Suppliers::where('user_id', $user->user_id)->first(); 
        $reps = Representatives::where('user_id', $supplier->supplier_id)->get();

        $representatives = Representatives::where('user_id', $user->user_id)->first(); 


        return view('groups.group', [
        'user' => $user,
        'reps' => $reps,

        ]);
    }

}
