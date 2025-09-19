<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class ProductController extends Controller
{
        public function productList(Request $request)
        {
            $user = Auth::user();



            return view('products.list', [
                'user' => $user,
            ]);
        }
}
