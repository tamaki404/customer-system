<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Suppliers;
use App\Models\Documents;

class CustomersController extends Controller
{

        public function customersList(Request $request)
        {
            $user = Auth::user();
            $supplier = $user ? Suppliers::where('user_id', $user->user_id)->first() : null;
            $documentCount = $supplier ? Documents::where('supplier_id', $supplier->supplier_id)->count() : 0;
            $suppliers = Suppliers::with('user')
                ->whereRelation('user', 'role', 'Supplier')
                ->get();



            return view('customers.list', [
                'user' => $user,
                'supplier' => $supplier,
                'documentCount' => $documentCount,
                'suppliers' => $suppliers,
            ]);
        }

}
