<?php

namespace App\Http\Controllers;

use App\Models\Staffs;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Orders;
use App\Models\PurchaseOrders;
use App\Models\Suppliers;
use App\Models\OrderItem;

class OrderController extends Controller
{   
    
    public static function randomBase36String(int $length): string
    {
        $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $str;
    }
        public function orderList(Request $request)
        {
            $user = Auth::user();

            $supplier = null;
            $orders = collect(); 

            if ($user->role !== "Supplier") {
                $orders = Orders::withSum('receipts', 'total_amount')
                    ->get()
                    ->map(function ($order) {
                        $paid = $order->receipts_sum_total_amount ?? 0;
                        $balance = $order->total_amount - $paid;


                        if ($paid >= $order->total_amount) {
                            $order->payment_status = 'Fully Paid';
                        } elseif ($paid > 0) {
                            $order->payment_status = 'Partially Paid';
                        } else {
                            $order->payment_status = 'Unpaid';
                        }

                        $order->paid_amount = $paid;
                        $order->balance = max($balance, 0);

                        return $order;
                    });
            } 
            elseif ($user->role === "Supplier") {
                $supplier = Suppliers::where('user_id', $user->user_id)->first();
                $orders = Orders::where('supplier_id', $supplier->supplier_id)
                    ->withSum('receipts', 'total_amount') 
                    ->get()
                    ->map(function ($order) {
                        $paid = $order->receipts_sum_total_amount ?? 0;

                        if ($paid >= $order->total_amount) {
                            $order->payment_status = 'Fully Paid';
                        } elseif ($paid > 0) {
                            $order->payment_status = 'Partially Paid';
                        } else {
                            $order->payment_status = 'Unpaid';
                        }

                        return $order;
                    });
            }

            return view('orders.list', [
                'user' => $user,
                'supplier' => $supplier,
                'orders' => $orders,
            ]);
        }

        public function createorder(Request $request){
        
            \Log::info('Staff Registration Request Data:', $request->all());

            try {
                $request->validate([
                    'supplier_id' => 'required|exists:suppliers,supplier_id',
                    'status'    => 'required|string',
                    'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
                ]);

            } catch (\Illuminate\Validation\ValidationException $e) {
                \Log::error('Puchase order submission failed:', $e->errors());
                return redirect()->back()
                    ->withErrors($e->validator)
                    ->withInput();
            }


            DB::beginTransaction();

            $date = date('Ymd');


            $po_id = 'PO-' . $date . '-' . $this->randomBase36String(5);

            try {
                $imageBlob = null;
                $imageMimeType = null;
                $imageFilename = null;
                $imageSize = null;
                
                if ($request->hasFile('image')) {
                    $image = $request->file('image');
                    
                    $imageBlob = file_get_contents($image->getRealPath());
                    $imageMimeType = $image->getMimeType();
                    $imageFilename = $image->getClientOriginalName();
                    $imageSize = $image->getSize();
                }

                if ($request->hasFile('image')) {
                    $image = $request->file('image');
                    \Log::info('Uploaded file details:', [
                        'name' => $image->getClientOriginalName(),
                        'mime' => $image->getMimeType(),
                        'size' => $image->getSize(),
                        'path' => $image->getRealPath(),
                    ]);
                }


                $purchaseOrder = PurchaseOrders::create([
                    'po_id'       => $po_id,
                    'supplier_id' => $request->supplier_id,
                    'status' => $request->status,
                    'image'         => $imageBlob,
                    'image_mime_type' => $imageMimeType,
                    'image_filename' => $imageFilename,
                    'image_size'    => $imageSize,

                ]);

            

                DB::commit();

                return redirect()->route('orders.list')
                    ->with('success', 'Purchase order has been created!');

            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Purchase order submission error: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                    'request_data' => $request->all()
                ]);
                
                
                return redirect()->back()
                    ->with('error', 'Purchase order creating failed: ' . $e->getMessage() . '. Please check the logs for more details.')
                    ->withInput();
            }
            }



        public function orderView($order_id, Request $request)
        {
            $user = Auth::user();
            $order = Orders::where('order_id', $order_id)->first(); 

            return view('orders.order', [
                'user' => $user,
                'order' => $order,

            ]);
        }

}
