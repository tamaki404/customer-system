<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;

class ViewController extends Controller
{
 public function profile()
{
    $user = auth()->user();
    return view('profile', compact('user'));
}

}
