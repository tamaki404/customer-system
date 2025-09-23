<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Suppliers;
use App\Models\Staffs;
use App\Models\Documents;
use App\Models\Products;
use App\Models\ProductSetting;
class ProfileController extends Controller
{
        public function profileView(Request $request)
        {
            $user = Auth::user();
            $supplier = Suppliers::where('user_id', $user->user_id)->first(); 
            $staffAgent = Staffs::where('staff_id', $supplier->staff_id)->first();
            $documents  = Documents::where('supplier_id', $supplier->supplier_id)->get();
            $products   = Products::where('status', 'Listed')->get();
            $productRequirements   = ProductSetting::where('supplier_id', $supplier->supplier_id)->get();

            return view('profile.profile', [
                'user' => $user,
                'supplier' => $supplier,
                'staffAgent' => $staffAgent,
                'documents' => $documents,
                'products' => $products,
                'productRequirements' => $productRequirements,

            ]);
        }
}
