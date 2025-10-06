<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Logs;
class LogsController extends Controller
{
        public function logsList(Request $request)
        {
            $user = Auth::user();
            $logs = Logs::orderBy('created_at', 'desc')->get();

            return view('logs.list', [
                'logs' => $logs,
                'user' => $user,

            ]);
        
        }
}
