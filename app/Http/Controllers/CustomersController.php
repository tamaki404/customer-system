<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Suppliers;
use App\Models\Documents;
use App\Models\User;
use App\Models\AccountStatus;
use App\Models\Staffs;
use App\Models\Logs;
use App\Models\Products;
use App\Models\ProductSetting;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomersController extends Controller
{

        public function customersList(Request $request)
        {
            $user = Auth::user();
            $supplier = Suppliers::where('user_id', $user->user_id)->first() ;
            $suppliers = Suppliers::with('user')
                ->whereRelation('user', 'role', 'Supplier')
                ->get();



            return view('customers.list', [
                'user' => $user,
                'supplier' => $supplier,
                'suppliers' => $suppliers,
            ]);
        }

        public function customerView($supplier_id, Request $request)
        {
            $user = Auth::user();

            $supplier   = Suppliers::where('supplier_id', $supplier_id)->firstOrFail();
            $accStatus  = AccountStatus::where('supplier_id', $supplier_id)->first();
            $staffs     = User::where('role', 'Staff')
                                ->where('role_type', 'sales_representative')
                                ->where('status', 'Active')
                                ->get();
            $staffAgent = Staffs::where('staff_id', $supplier->staff_id)->first();
            $documents  = Documents::where('supplier_id', $supplier_id)->get();
            $products   = Products::where('status', 'Listed')->get();
            $productRequirements   = ProductSetting::where('supplier_id', $supplier_id)->get();


            return view('customers.customer', [
                'user'       => $user,
                'supplier'   => $supplier,
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
            $user = Auth::user();
            \Log::info('SupplierConfirm started', $request->all());

            $request->validate([
                'supplier_id'       => 'required|exists:suppliers,supplier_id',
                'user_id'       => 'required|exists:users,user_id',
                'acc_status'        => 'required|string|max:100',
                'reason_to_decline' => 'nullable|string|max:200|required_if:acc_status,Declined',
                'staff_id'          => 'required|exists:staffs,staff_id',

                'products'    => 'nullable|array',
                'products.*.product_id' => 'required|string|exists:products,product_id',
                'products.*.price'      => 'required|numeric|min:0',

            ]);

            DB::beginTransaction();

            try {


                $user = User::where('user_id', $request->user_id)->firstOrFail();
                $acc_status = AccountStatus::firstOrNew(['supplier_id' => $request->supplier_id]);

                $acc_status->staff_id = $request->staff_id;
                $acc_status->acc_status = $request->acc_status; 
                $acc_status->reason_to_decline = $request->acc_status === 'Declined'
                    ? $request->reason_to_decline
                    : null;
                $acc_status->save();

                $user->status = $request->acc_status;
                $user->save();

                $supplier = Suppliers::where('supplier_id', $request->supplier_id)->firstOrFail();
                $supplier->staff_id = $request->staff_id;
                $supplier->save();


                // Save product settings only if accepted
                if ($request->acc_status === 'Accepted' && $request->has('products')) {
                    foreach ($request->products as $product) {
                        ProductSetting::create([
                            'product_id'  => $product['product_id'],
                            'supplier_id' => $request->supplier_id,
                            'price'       => $product['price'],
                            'added_by'    => $user->user_id, 
                        ]);
                    }

                }
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

            
                
                Logs::create([
                    'user_id' => Auth::user()->user_id,
                    'action' => 'Supplier registration request',
                    'log_id' => $log_id,
                    'description' => "Supplier {$request->supplier_id} confirmed with status '{$request->acc_status}' and assigned to staff {$request->staff_id}.",
                ]);


                DB::commit();
                return redirect()->back()
                    ->with('success', "Supplier confirmation saved successfully (status: {$request->acc_status}).");


                

                } catch (\Exception $e) {
                    \Log::error('SupplierConfirm failed: ' . $e->getMessage(), [
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
