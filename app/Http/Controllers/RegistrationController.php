<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Products;

class RegistrationController extends Controller
{
    public function showSignupForm()
    {
        $products = Products::all();
    return view('registration.signup', compact('products'));
    }

    public function register(Request $request)
    {
        
    }
}
