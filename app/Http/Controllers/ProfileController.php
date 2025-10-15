<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Suppliers;
use App\Models\Staffs;
use App\Models\Documents;
use App\Models\Address;
use App\Models\AccountStatus;
use App\Models\ProductSales;
use App\Models\PriceHistory;
use App\Models\DeliveryRequirements;
use App\Models\Products;
use App\Models\ProductSetting;
use App\Models\ProductRequirements;
use App\Models\Representatives;
use App\Models\Signatories;
use App\Models\Business;

class ProfileController extends Controller
{
        public function profileView(Request $request)
        {
            $user = Auth::user();
            $supplier = Suppliers::where('user_id', $user->user_id)->first(); 
            $address  = Address::where('supplier_id', $supplier->supplier_id)->first();
            $accStatus  = AccountStatus::where('supplier_id', $supplier->supplier_id)->first();
            $staffAgent = Staffs::where('staff_id', $accStatus->staff_id)->first();
            $documents  = Documents::where('supplier_id', $supplier->supplier_id)->get();
            $products   = Products::where('status', 'Listed')->get();
            $productRequirements   = ProductSetting::where('supplier_id', $supplier->supplier_id)->get();
            $salesHistos   = ProductSales::where('supplier_id', $supplier->supplier_id)
            ->orderBy('created_at', 'desc')
            ->get();
            $prices   = PriceHistory::where('supplier_id', $supplier->supplier_id)
            ->orderBy('created_at', 'desc')
            ->get();
            $activeSale   = ProductSales::where('supplier_id', $supplier->supplier_id)->first();
            $delivery = DeliveryRequirements::where('supplier_id', $supplier->supplier_id)->first();
            $prodSpecs   = ProductRequirements::where('supplier_id', $supplier->supplier_id)->get();
            $representatives   = Representatives::where('supplier_id', $supplier->supplier_id)->get();
            $signatories   = Signatories::where('supplier_id', $supplier->supplier_id)->get();
            $account_status = AccountStatus::where('supplier_id', $supplier->supplier_id)->first();
            $business   = Business::where('supplier_id', $supplier->supplier_id)->first();


            return view('profile.profile', [
                'user' => $user,
                'supplier' => $supplier,
                'address' => $address,
                'staffAgent' => $staffAgent,
                'documents' => $documents,
                'products' => $products,
                'productRequirements' => $productRequirements,
                'salesHistos' => $salesHistos,
                'prices' => $prices,
                'activeSale' => $activeSale,
                'delivery' => $delivery,
                'prodSpecs' => $prodSpecs,
                'representatives' => $representatives,
                'signatories' => $signatories,
                'account_status' => $account_status,
                'business' => $business,
            ]);
        }
}
