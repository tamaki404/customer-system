<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Representatives;
use Illuminate\Http\Request;
use App\Models\Products;

class RegistrationController extends Controller
{
    public function showSignupForm()
    {
        $products = Products::all();
    return view('registration.signup', compact('products'));
    }

    public function chooseAccount(Request $request)
    {
        $user = Auth()->user();
        $reps = Representatives::where('user_id', $user->user_id)->get();
        return view('registration.choose_account', compact('reps', 'user'));
    }
}
