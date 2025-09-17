<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Suppliers;
use App\Models\Documents;
use App\Models\Staffs;

class StaffsController extends Controller
{
        public function staffsList(Request $request)
        {
            $user = Auth::user();
            $staffs = Suppliers::with('user')
                ->whereRelation('user', 'role', 'Staff')
                ->get();



            return view('staffs.list', [
                'user' => $user,
                'staffs' => $staffs,

            ]);
        }

        
}
