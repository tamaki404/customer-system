<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Logs;
class LogsController extends Controller
{
    public function list(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        if ($user->role === 'Customer') {
            $logs = Logs::where('user_id', $user->user_id)->orderBy('created_at', 'desc')->get();

        }
        elseif ($user->role === 'Admin' || $user->role === 'Staff') {
            $logs = Logs::where('role', "Admin")->orWhere('role', "Staff")->orderBy('created_at', 'desc')->get();

        }
        return view('franken.lg.list', [
            'logs' => $logs,
            'user' => $user,
        ]);
    }
}
