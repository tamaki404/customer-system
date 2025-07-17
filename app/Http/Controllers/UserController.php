<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function register(Request $request){
        $incomingFields = $request -> validate([
            'username'=>'required',
            'password'=>'required',

        ]);

        User::create($incomingFields);
        return redirect('/home');

    }


}
