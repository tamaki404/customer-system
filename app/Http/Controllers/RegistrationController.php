<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function showSignupForm()
    {
        return view('registration.signup');
    }

    public function register(Request $request)
    {
        
    }
}
