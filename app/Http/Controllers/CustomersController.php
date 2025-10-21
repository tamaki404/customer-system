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
use App\Models\Suppliers;
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
            $supplier = Suppliers::where('user_id', $user->user_id)->first() ;
            $products = Products::where('status', 'Listed')->get();

            $suppliers = Suppliers::select(
            'suppliers.*',
                    DB::raw("CONCAT_WS(' ', staffs.firstname, staffs.middlename, staffs.lastname) as staff_name"))
                    ->join('account_status', 'account_status.supplier_id', '=', 'suppliers.supplier_id')
                    ->leftJoin('staffs', 'staffs.staff_id', '=', 'account_status.staff_id')
                    ->with('user')
                    ->whereRelation('user', 'role', 'Supplier')
                    ->orderBy('created_at', 'desc')
                    ->get();




            return view('customers.list', [
                'user' => $user,
                'supplier' => $supplier,
                'suppliers' => $suppliers,
                'products' => $products,
            ]);
        }

        public function customerView($supplier_id, Request $request)
        {
            $user = Auth::user();

            $supplier   = Suppliers::where('supplier_id', $supplier_id)->firstOrFail();
            $accStatus  = AccountStatus::where('supplier_id', $supplier_id)->first();
            $staffs     = User::where('role', 'Staff')
                                ->where('role_type', 'sales_representative')
                                ->get();
            $delivery = DeliveryRequirements::where('supplier_id', $supplier->supplier_id)->first();

            $address = Address::where('supplier_id', $supplier->supplier_id)->first();
            $ceilingPrice = GlobalCeiling::whereRaw('LOWER(city_selected) = ?', [strtolower($address->office_city)])->first();

            $address = Address::where('supplier_id', $supplier->supplier_id)->first();
            $staffAgent = Staffs::where('staff_id', $accStatus->staff_id)->first();
            $documents  = Documents::where('supplier_id', $supplier_id)->get();
            $products   = Products::where('status', 'Listed')->get();
            $productRequirements = ProductRequirements::with([
                'product',
                'settings' => function($query) use ($supplier_id) {
                    $query->where('supplier_id', $supplier_id);
                }
            ])->where('supplier_id', $supplier_id)->get();

            $prodSpecs   = ProductRequirements::where('supplier_id', $supplier_id)->get();
            $representatives   = Representatives::where('supplier_id', $supplier_id)->get();
            $signatories   = Signatories::where('supplier_id', $supplier_id)->get();
            $business   = Business::where('supplier_id', operator: $supplier_id)->first();
            $account_status = AccountStatus::where('supplier_id', $supplier_id )->first();
            $sales   = ProductSales::where('supplier_id', $supplier_id)->get();
            $activeSale   = ProductSales::where('supplier_id', $supplier_id)->first();
            $prices   = PriceHistory::where('supplier_id', $supplier_id)
            ->orderBy('created_at', 'desc')
            ->get();
            $salesHistos   = ProductSales::where('supplier_id', $supplier_id)
            ->orderBy('created_at', 'desc')
            ->get();

            return view('customers.customer', [
                'user'       => $user,
                'supplier'   => $supplier,
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

            ]);
        }

        public function supplierConfirm(Request $request)
        {
            $staff = Auth::user();
            Log::info('SupplierConfirm started', $request->all());
            $input = $request->all();
            $input['credit_limit'] = str_replace(',', '', $input['credit_limit']);

            $request->merge($input);


$request->validate([
    'supplier_id'       => 'required|exists:suppliers,supplier_id',
    'user_id'           => 'required|exists:users,user_id',
    'account_status'    => 'required|string|max:100',
    'reason_to_decline' => 'nullable|string|max:200|required_if:account_status,Declined',
    'to_change'         => 'nullable|string|max:200|required_if:account_status,Declined',
    'feedback'          => 'nullable|string|max:500|required_if:account_status,Declined',
    'staff_id'          => 'required|exists:staffs,staff_id',
    'credit_limit'      => 'required|numeric|min:0',
    'products'          => 'nullable|array',
    'products.*.product_id' => 'required|string|exists:products,product_id',
    'products.*.nego_price' => 'required|numeric|min:0',
]);

            DB::beginTransaction();

            try {


                $user = User::where('user_id', $request->user_id)->firstOrFail();
                $account_status = AccountStatus::firstOrNew(['supplier_id' => $request->supplier_id]);

$account_status->staff_id = $request->staff_id;
$account_status->account_status = $request->account_status; 

if ($request->account_status === 'Declined') {
    $account_status->reason_to_decline = $request->reason_to_decline;
    $account_status->to_change = $request->to_change;
    $account_status->feedback = $request->feedback;
} else {
    $account_status->reason_to_decline = null;
    $account_status->to_change = null;
    $account_status->feedback = null;
}

$account_status->save();

                $user->status = $request->account_status;
                $user->save();

                $supplier = AccountStatus::where('supplier_id', $request->supplier_id)->firstOrFail();
                $supplier->staff_id = $request->staff_id;
                $supplier->approved_by = $request->user_id;
                $supplier->approved_at = now();

                $supplier->save();

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
                    $set_id = 'SET-' . $date . '-' . randomBase36String(5);


                // Save product settings only if accepted
                    if ($request->account_status === 'Accepted' && $request->has('products')) {
                        foreach ($request->products as $productData) {
                            ProductSetting::create([
                                'product_id'  => $productData['product_id'],
                                'set_id'      => 'SET-' . date('Ymd') . '-' . randomBase36String(5),
                                'supplier_id' => $request->supplier_id,
                                'nego_price'  => $productData['nego_price'],
                                'added_by'    => $user->user_id,
                            ]);
                        }
                    }


                $credit_id = 'CRDT-' . $date . '-' . randomBase36String(5);

                Credits::updateOrCreate(
                    ['user_id' => $user->user_id],
                    [
                        'credit_id'   => $credit_id,
                        'status'      => 'Active',
                        'balance'     => 0,
                        'credit_limit'=> $input['credit_limit'],
                    ]
                );


                Logs::create([
                    'user_id' => $request->user_id,
                    'action' => 'Supplier registration request',
                    'log_id' => $log_id,
                    'description' => "Supplier {$request->supplier_id} confirmed with status 'Accepted', assigned to staff {$request->staff_id} and set negotiated price.",
                    'entity' => 'Supplier', 
                    'entity_id' => $supplier->id,
                ]);


                DB::commit();
                return redirect()->back()
                    ->with('success', "Supplier confirmation saved successfully (status: {$request->account_status}).");


                

                } catch (\Exception $e) {
                    Log::error('SupplierConfirm failed: ' . $e->getMessage(), [
                        'trace' => $e->getTraceAsString(),
                        'request' => $request->all(),
                        'line' => $e->getLine(),
                        'file' => $e->getFile(),
                    ]);
                    
                    return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
                }
        }
        public function afas($supplier_id, Request $request)
        {
            $user = Auth::user();

            $supplier = Suppliers::where($supplier_id, 'supplier_id');

            $accStatus= AccountStatus::where('supplier_id', $supplier_id)->firstOrFail();
            // Only fetch staff if user is staff
            $staff = Staffs::where('user_id', $user->user_id)->firstOrFail();

            // Get assigned staff
            $staffAgent = null;
            if ($supplier->staff_id) {
                $staffAgent = Staffs::where('staff_id', $supplier->staff_id)->first();
            }

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

            $staffs = User::where('role', 'Staff')
                ->where('role_type', 'sales_representative')
                ->where('status', 'Active')
                ->get();

            return view('customers.customer', [
                'user' => $user,
                'supplier' => $supplier,
                'documentCount' => $documentCount,
                'customer' => $customer,
                'documents' => $documents,
                'staffs' => $staffs,
                'staffAgent' => $staffAgent,
                'accStatus' => $accStatus,

            ]);
        }

}
