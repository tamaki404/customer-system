<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\DeliveryRequirements;
use App\Models\ProductSales;
use App\Models\Representatives;
use App\Models\Signatories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Customers;
use App\Models\Documents;
use App\Models\User;
use App\Models\AccountStatus;
use App\Models\Staffs;
use App\Models\Logs;
use App\Models\Products;
use App\Models\Address;
use App\Models\ProductRequirements;
use App\Models\PriceHistory;
use App\Models\GlobalCeiling;
use App\Models\Reviews;
use App\Models\SaleDiscount;
use App\Models\ProductSetting;
use App\Models\Credits;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Users;

class CustomersController extends Controller
{
        public function customersList(Request $request)
        {
            $user = Auth::user();
            $customer = Customers::where('user_id', $user->user_id)->first() ;
            $products = Products::where('status', 'Listed')->get();

            $Customers = Customers::select(
            'Customers.*',
                    DB::raw("CONCAT_WS(' ', staffs.firstname, staffs.middlename, staffs.lastname) as staff_name"))
                    ->join('account_status', 'account_status.customer_id', '=', 'Customers.customer_id')
                    ->leftJoin('staffs', 'staffs.staff_id', '=', 'account_status.staff_id')
                    ->with('user')
                    ->whereRelation('user', 'role', 'customer')
                    ->orderBy('created_at', 'desc')
                    ->paginate(50);

            return view('customers.list', [
                'user' => $user,
                'customer' => $customer,
                'customers' => $Customers, 
                'products' => $products,
            ]);
        }

        public function customerView($customer_id, Request $request)
        {
            $user = Auth::user();

            $customer   = Customers::where('customer_id', $customer_id)->firstOrFail();

            $review = Reviews::where('user_id', $customer->user_id)->first();

            $accStatus  = AccountStatus::where('customer_id', $customer_id)->first();
            $staffs = User::where('role', 'Staff')
                ->where('role_type', 'sales_representative')
                ->with('staff')
                ->get();
            $delivery = DeliveryRequirements::where('customer_id', $customer->customer_id)->first();
            $address = Address::where('customer_id', $customer->customer_id)->first();
            $ceilingPrice = GlobalCeiling::whereRaw('LOWER(city_selected) = ?', [strtolower($address->office_city)])->first();

            $address = Address::where('customer_id', $customer->customer_id)->first();
            $staffAgent = Staffs::where('staff_id', $accStatus->staff_id)->first();
            $documents  = Documents::where('customer_id', $customer_id)->get();
            $products   = Products::where('status', 'Listed')->get();
            $productRequirements = ProductRequirements::with([
                'product',
                'settings' => function($query) use ($customer_id) {
                    $query->where('customer_id', $customer_id);
                }
            ])->where('customer_id', $customer_id)->get();
            $prodSpecs   = ProductRequirements::where('customer_id', $customer_id)->get();
            $representatives   = Representatives::where('customer_id', $customer_id)->get();
            $signatories   = Signatories::where('customer_id', $customer_id)->get();
            $business   = Business::where('customer_id', operator: $customer_id)->first();
            $account_status = AccountStatus::where('customer_id', $customer_id )->first();
            $sales   = ProductSales::where('customer_id', $customer_id)->get();
            $activeSale   = ProductSales::where('customer_id', $customer_id)->first();
            $prices   = PriceHistory::where('customer_id', $customer_id)
            ->orderBy('created_at', 'desc')
            ->get();


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

            return view('customers.customer', [
                'user'       => $user,
                'customer'   => $customer,
                'address'   => $address,
                'prodSpecs'   => $prodSpecs,
                'delivery'   => $delivery,
                'representatives'   => $representatives,
                'signatories'   => $signatories,
                'business'   => $business,
                'account_status' => $account_status,
                'sales' => $sales,
                'activeSale' => $activeSale,
                'prices' => $prices,
                'salesHistos' => $salesHistos,
                'ceilingPrice' => $ceilingPrice,

                'staffs'     => $staffs,
                'accStatus'  => $accStatus,
                'staffAgent' => $staffAgent,
                'documents'  => $documents,
                'products'   => $products,
                'productRequirements'   => $productRequirements,
                'review' => $review,

            ]);
        }

        public function customerConfirm(Request $request)
        {
            $staff = Auth::user();
            Log::info('customerConfirm started', $request->all());
            $input = $request->all();
            
            // Only process credit_limit if it exists (when Accepted)
            if (isset($input['credit_limit'])) {
                $input['credit_limit'] = str_replace(',', '', $input['credit_limit']);
                $request->merge($input);
            }

            $request->validate([
                'customer_id'       => 'required|exists:Customers,customer_id',
                'user_id'           => 'required|exists:users,user_id',
                'staff_id'          => 'nullable|required_if:account_status,Accepted|exists:staffs,staff_id', 
                'credit_limit'      => 'required_if:account_status,Accepted|numeric|min:0',
                'account_status'    => 'required|string|max:100',

                'products'          => 'sometimes|required_if:account_status,Accepted|array',
                'products.*.product_id' => 'sometimes|required_if:account_status,Accepted|string|exists:products,product_id',
                'products.*.nego_price' => 'sometimes|required_if:account_status,Accepted|numeric|min:0',

                // declined, to save to reviews table
                'reason_to_decline' => 'nullable|string|max:200|required_if:account_status,Declined',
                'to_change'         => 'nullable|string|max:200|required_if:account_status,Declined',
                'feedback'          => 'nullable|string|max:500|required_if:account_status,Declined',
            ]);

            if ($request->account_status !== 'Accepted') {
                $request->merge(['products' => []]);
            }

            DB::beginTransaction();

            try {
                $date = date('Ymd');
                function randomBase36String(int $length): string {
                    $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    $str = '';
                    for ($i = 0; $i < $length; $i++) {
                        $str .= $chars[random_int(0, strlen($chars) - 1)];
                    }
                    return $str;
                }

                $log_id = 'LOG-' . $date . '-' . randomBase36String(5);



                $user = User::where('user_id', $request->user_id)->firstOrFail();
                $account_status = AccountStatus::firstOrNew(['customer_id' => $request->customer_id]);

                $account_status->staff_id = $request->staff_id;
                $account_status->account_status = $request->account_status; 

                if ($request->account_status === 'Declined') {
                    Reviews::create([
                        'review_id' => 'REVIEW-' . $date . '-' . randomBase36String(5),
                        'head' => $request->to_change,
                        'body' => $request->feedback,
                        'user_id' => $request->user_id,
                        'status' => "Active",
                        'raised_by' => $staff->user_id,
                        'raised_at' => now()
                    ]);
                    $account_status->reason_to_decline = $request->reason_to_decline; 

                }

                $account_status->save();

                $user->status = $request->account_status;
                $user->save();

                $customer = AccountStatus::where('customer_id', $request->customer_id)->firstOrFail();
                $customer->staff_id = $request->staff_id;
                $customer->save();



                // Save product settings only if accepted
                if ($request->account_status === 'Accepted' && $request->has('products')) {
                    foreach ($request->products as $productData) {
                        ProductSetting::create([
                            'product_id'  => $productData['product_id'],
                            'set_id'      => 'SET-' . date('Ymd') . '-' . randomBase36String(5),
                            'customer_id' => $request->customer_id,
                            'nego_price'  => $productData['nego_price'],
                            'added_by'    => $user->user_id,
                        ]);
                    }
                }

                // Only create/update credits if accepted
                if ($request->account_status === 'Accepted') {
                    $credit_id = 'CRDT-' . $date . '-' . randomBase36String(5);
                    
                    Credits::updateOrCreate(
                        ['user_id' => $customer->user_id],
                        [
                            'credit_id'   => $credit_id,
                            'status'      => 'Active',
                            'balance'     => 0,
                            'credit_limit'=> $request->credit_limit,
                        ]
                    );
                }

                Logs::create([
                    'user_id' => $request->user_id,
                    'action' => 'customer registration request',
                    'log_id' => $log_id,
                    'description' => "customer {$request->customer_id} confirmed with status '{$request->account_status}'" . 
                                ($request->account_status === 'Accepted' ? ", assigned to staff {$request->staff_id} and set negotiated price." : "."),
                    'entity' => 'customer', 
                    'entity_id' => $customer->id,
                ]);

                DB::commit();
                return redirect()->back()
                    ->with('success', "customer confirmation saved successfully (status: {$request->account_status}).");

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('customerConfirm failed: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                    'request' => $request->all(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile(),
                ]);
                
                return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
            }
        }

        public function afas($customer_id, Request $request)
        {
            $user = Auth::user();

            $customer = Customers::where($customer_id, 'customer_id');

            $accStatus= AccountStatus::where('customer_id', $customer_id)->firstOrFail();
            // Only fetch staff if user is staff
            $staff = Staffs::where('user_id', $user->user_id)->firstOrFail();

            // Get assigned staff
            $staffAgent = null;
            if ($customer->staff_id) {
                $staffAgent = Staffs::where('staff_id', $customer->staff_id)->first();
            }

            $documentCount = $customer
                ? Documents::where('customer_id', $customer->customer_id)->count()
                : 0;
            $documents = Documents::where('customer_id', $customer_id)->get();

            // optional: get specific customer
            $customerId = $request->query('id');
            $customer = null;

            if ($customerId) {
                $customer = Customers::with('user')
                    ->where('customer_id', $customerId)
                    ->whereRelation('user', 'role', 'customer')
                    ->first();
            }

            $staffs = User::where('role', 'Staff')
                ->where('role_type', 'sales_representative')
                ->where('status', 'Active')
                ->get();

            return view('customers.customer', [
                'user' => $user,
                'customer' => $customer,
                'documentCount' => $documentCount,
                'documents' => $documents,
                'staffs' => $staffs,
                'staffAgent' => $staffAgent,
                'accStatus' => $accStatus,

            ]);
        }

}