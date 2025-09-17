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
            $supplier = Suppliers::where('user_id', $user->user_id)->first() ;
            $documentCount = Documents::where('supplier_id', $supplier->supplier_id)->count();
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

        public function customerView($supplier_id, Request $request)
        {
            $user = Auth::user();

            $supplier = Suppliers::where('supplier_id', $supplier_id)->first();

            $documentCount = $supplier
                ? Documents::where('supplier_id', $supplier->supplier_id)->count()
                : 0;
            $documents = Documents::where('supplier_id', $supplier_id)->get();

            // optional: get specific customer
            $customerId = $request->query('id');
            $customer = null;

            if ($customerId) {
                $customer = Suppliers::with('user')
                    ->where('supplier_id', $customerId)
                    ->whereRelation('user', 'role', 'Supplier')
                    ->first();
            }

            return view('customers.customer', [
                'user' => $user,
                'supplier' => $supplier,
                'documentCount' => $documentCount,
                'customer' => $customer,
                'documents' => $documents,

            ]);
        }


}
