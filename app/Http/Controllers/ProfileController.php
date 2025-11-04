<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SaleDiscount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Customers;
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
            $customer = Customers::where('user_id', $user->user_id)->first(); 
            $address  = Address::where('customer_id', $customer->customer_id)->first();
            $accStatus  = AccountStatus::where('customer_id', $customer->customer_id)->first();
            $staffAgent = Staffs::where('staff_id', $accStatus->staff_id)->first();
            $documents  = Documents::where('customer_id', $customer->customer_id)->get();
            $products   = Products::where('status', 'Listed')->get();
            $productRequirements   = ProductRequirements::where('customer_id', $customer->customer_id)->get();
  
            $prices   = PriceHistory::where('customer_id', $customer->customer_id)
            ->orderBy('created_at', 'desc')
            ->get();
            $activeSale   = ProductSales::where('customer_id', $customer->customer_id)->first();
            $delivery = DeliveryRequirements::where('customer_id', $customer->customer_id)->first();
            $prodSpecs   = ProductRequirements::where('customer_id', $customer->customer_id)->get();
            $representatives   = Representatives::where('customer_id', $customer->customer_id)->get();
            $signatories   = Signatories::where('customer_id', $customer->customer_id)->get();
            $account_status = AccountStatus::where('customer_id', $customer->customer_id)->first();
            $business   = Business::where('customer_id', $customer->customer_id)->first();

            $isTherePriceHistory = PriceHistory::where('customer_id', $customer->customer_id)->exists();

            $prodSpecs   = ProductRequirements::where('customer_id', $customer->customer_id)->get();
            $sales = SaleDiscount::where('category', $customer->category)->get();
            // Get all product_ids for this customer
            $productIds = ProductRequirements::where('customer_id', $customer->customer_id)
                ->pluck('product_id');

            // Query all sale discounts that match category AND product_id
            $salesHistos = SaleDiscount::where('category', $customer->category)
                ->whereIn('product_id', $productIds)
                ->orderBy('created_at', 'desc')
            ->get();
     
            // Check if any matching sales exist
            $isThereSalesHistory = $sales->isNotEmpty();

            return view('profile.profile', [
                'user' => $user,
                'customer' => $customer,
                'isTherePriceHistory' => $isTherePriceHistory,
                'isThereSalesHistory' => $isThereSalesHistory,
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
                'sales' => $sales,
                'business' => $business,
            ]);
        }
}
