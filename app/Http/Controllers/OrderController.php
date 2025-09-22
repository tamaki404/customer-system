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
                $orders = Orders::all();
            } 
            elseif ($user->role === "Supplier") {
                $supplier = Suppliers::where('user_id', $user->user_id)->first();
                $orders = Orders::where('supplier_id', $supplier->supplier_id)->get();
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

        public function placeOrderItems(Request $request)
        {
            \Log::info('Placing purchase order items - Request Data:', $request->all());
            
            try {
                // Validate the request
                $request->validate([
                    'po_id' => 'required|exists:purchase_orders,po_id',
                    'supplier_id' => 'required|exists:suppliers,supplier_id',
                    'selected_products' => 'required|array|min:1',
                    'selected_products.*' => 'required|exists:product_settings,set_id', 
                    'quantities' => 'required|array',
                    'quantities.*' => 'required|numeric|min:1',
                    'product_ids' => 'required|array',
                    'unit_prices' => 'required|array',
                ]);
                
            } catch (\Illuminate\Validation\ValidationException $e) {
                \Log::error('Purchase order submission failed:', $e->errors());
                return redirect()->back()
                    ->withErrors($e->validator)
                    ->withInput();
            }
            
            try {
                DB::beginTransaction();
                
                // Get the purchase order
                $purchaseOrder = PurchaseOrders::where('po_id', $request->po_id)->firstOrFail();
                
                // Create the main order first
                $date = date('Ymd');
                $order_id = 'ORD-' . $date . '-' . $this->randomBase36String(5);
                
                $order = Orders::create([
                    'order_id' => $order_id,
                    'po_id' => $request->po_id,
                    'supplier_id' => $request->supplier_id,
                    'order_date' => now(),
                    'status' => 'Pending', 
                    'total_amount' => 0, 
                ]);
                
                $totalOrderAmount = 0;
                $orderItemsCreated = [];
                
                foreach (array_unique($request->selected_products) as $setId) {
                    if (!isset($request->quantities[$setId]) || 
                        !isset($request->product_ids[$setId]) || 
                        !isset($request->unit_prices[$setId])) {
                        continue;
                    }

                    $quantity = (int) $request->quantities[$setId];
                    $unitPrice = (float) $request->unit_prices[$setId];
                    $productId = $request->product_ids[$setId];
                    $totalPrice = $quantity * $unitPrice;

                    $orderItemId = 'ORDR_ITEM-' . $date . '-' . $this->randomBase36String(5);

                    OrderItem::create([
                        'order_item_id' => $orderItemId,
                        'order_id' => $order_id,
                        'product_id' => $productId,
                        'set_id' => $setId,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'total_price' => $totalPrice,
                        'status' => 'Pending',
                    ]);

                    $totalOrderAmount += $totalPrice;
                }

                
                $order->update(['total_amount' => $totalOrderAmount]);
                
                // Handle file upload if present
                if ($request->hasFile('order_document')) {
                    $file = $request->file('order_document');
                    $filename = 'order_doc_' . $order_id . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('order_documents', $filename, 'public');
                    
                    // Update order with document path
                    $order->update(['document_path' => $filePath]);
                }
                
                $purchaseOrder->update([
                    'status' => 'Placed',
                    'placed_at' => now(),
                ]);
            

                \Log::info('Processing order items:', [
                    'selected_products' => $request->selected_products,
                    'quantities' => $request->quantities,
                    'product_ids' => $request->product_ids,
                    'unit_prices' => $request->unit_prices,
                ]);

                
                DB::commit();
                
                return redirect()->back()->with('success', 
                    'Purchase order has been placed successfully! Order ID: ' . $order_id . 
                    '. Total items: ' . count($orderItemsCreated));
                    
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Purchase order placement failed:', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'request_data' => $request->all(),
                ]);
                
                return redirect()->back()
                    ->with('error', 'Purchase order placement failed: ' . $e->getMessage() . 
                        '. Please check the logs for more details.')
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
