<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Suppliers;
use App\Models\Documents;

class DashboardController extends Controller
{
    public function dashboardView(Request $request)
    {
        $user = Auth::user();
        $supplier = $user ? Suppliers::where('user_id', $user->user_id)->first() : null;
        $documentCount = $supplier ? Documents::where('supplier_id', $supplier->supplier_id)->count() : 0;

        return view('dashboard', [
            'user' => $user,
            'supplier' => $supplier,
            'documentCount' => $documentCount,
        ]);
    }

    public function layoutView(Request $request)
    {
        $user = Auth::user();
        $supplier = $user ? Suppliers::where('user_id', $user->user_id)->first() : null;
        $documentCount = $supplier ? Documents::where('supplier_id', $supplier->supplier_id)->count() : 0;

        return view('layouts.main', [
            'user' => $user,
            'supplier' => $supplier,
            'documentCount' => $documentCount,
        ]);
    }
}
