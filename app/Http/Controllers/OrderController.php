<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
        public function orderList(Request $request)
        {
            $user = Auth::user();
           

            return view('orders.list', [
                'user' => $user,

            ]);
        }
}
