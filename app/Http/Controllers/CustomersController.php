<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Suppliers;
use App\Models\Documents;
use App\Models\User;
use App\Models\AccountStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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


            ]);
        }

        public function supplierConfirm(Request $request)
        {
                   \Log::info('SupplierConfirm started', $request->all());

            $request->validate([
                'supplier_id'       => 'required|exists:suppliers,supplier_id',
                'user_id'       => 'required|exists:users,user_id',
                'acc_status'        => 'required|string|max:100',
                'reason_to_decline' => 'nullable|string|max:200|required_if:acc_status,Declined',
                'staff_id'          => 'required|exists:staffs,staff_id',
            ]);

            DB::beginTransaction();

            try {
                // make sure user has supplier_id column
                $user = User::where('user_id', $request->user_id)->firstOrFail();

                $date = now()->format('Ymd');
                $status_id = 'STATUS-' . $date . '-' . strtoupper(Str::random(5));

                AccountStatus::create([
                    'supplier_id'       => $request->supplier_id,
                    'status_id'         => $status_id,
                    'acc_status'        => $request->acc_status,
                    'reason_to_decline' => $request->acc_status === 'Declined' ? $request->reason_to_decline : null,
                    'staff_id'          => $request->staff_id,
                ]);

                $user->status = $request->acc_status;
                $user->save();

                DB::commit();

                return redirect()->route('staffs.list')
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



}
