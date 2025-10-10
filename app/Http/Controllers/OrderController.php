<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\DeliveryItems;
use App\Models\Staffs;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Orders;
use App\Models\PurchaseOrders;
use App\Models\Suppliers;
use App\Models\OrderItem;
use App\Models\Logs;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\OrderHistory;

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

            $orders = Orders::with(['deliveries.deliveryItems'])
                ->withSum('receipts', 'total_amount')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($order) {
                    $paid = $order->receipts_sum_total_amount ?? 0;
                    $order->balance = max($order->total_amount - $paid, 0);
                    $order->payment_status = $paid >= $order->total_amount
                        ? 'Fully paid'
                        : ($paid > 0 ? 'Partially settled' : 'Unpaid');

                    $plannedHeads = $order->deliveries->flatMap->deliveryItems->sum('planned_heads');
                    $plannedKilos = $order->deliveries->flatMap->deliveryItems->sum('planned_kilos');

                    $deliveredDeliveries = $order->deliveries
                        ->where('status', 'Delivered')
                        ->sortBy('delivery_date');

                    $runningHeads = $plannedHeads;
                    $runningKilos = $plannedKilos;

                    $runningBalance = [];

                    // Start with planned at the end
                    $runningBalance[] = [
                        'heads' => $runningHeads,
                        'kilos' => $runningKilos,
                        'label' => 'Planned'
                    ];

                    foreach ($deliveredDeliveries as $index => $delivery) {
                        $deliveredHeads = $delivery->deliveryItems->sum('received_heads');
                        $deliveredKilos = $delivery->deliveryItems->sum('received_kilos');

                        $runningHeads -= $deliveredHeads;
                        $runningKilos -= $deliveredKilos;

                        $runningBalance[] = [
                            'heads' => $runningHeads,
                            'kilos' => $runningKilos,
                            'label' => 'After Delivery #' . ($index + 1)
                        ];
                    }

                    $runningBalance = array_reverse($runningBalance);

                    $order->setAttribute('running_balance', $runningBalance);

                    $order->all_scheduled = $order->deliveries->count() > 0
                        && $order->deliveries->every(fn($d) => $d->status === 'Scheduled');

                    return $order;
                });

            return view('orders.list', compact('user', 'supplier', 'orders'));
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

                return redirect()->route('purchaseorders.list')
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
         
            $order = Orders::with(['items.productSetting'])->where('order_id', $order_id)->first();
            $items = $order->items;
            $deliveries = Delivery::when($order_id, function ($query) use ($order_id) {
                    $query->where('order_id', $order_id);
                })
                ->orderBy('delivery_date', 'asc')
                ->get();

            

            return view('orders.order', [
                'user' => $user,
                'order' => $order,
                'items' => $items,
                'deliveries' => $deliveries,

            ]);
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
    
        public function orderAction(Request $request)
        {
            \Log::info('Placing purchase order items - Request Data:', $request->all());
            
            try {
                $validated = $request->validate([
                    'order_id' => 'required|exists:orders,order_id',
                    'status'   => 'required|in:Accepted,Rejected',
                ]);
                
            } catch (\Illuminate\Validation\ValidationException $e) {
                \Log::error('Purchase order submission failed:', $e->errors());
                return redirect()->back()
                    ->withErrors($e->validator)
                    ->withInput();
            }
            
            try {
                DB::beginTransaction();

                $date = date('Ymd');
                $log_id = 'LOG-' . $date . '-' . $this->randomBase36String(5);
                $history_id = 'OH-' . $date . '-' . $this->randomBase36String(5);

                $order = Orders::where('order_id', $validated['order_id'])->firstOrFail();
                $po = PurchaseOrders::where('po_id', $order->po_id)->firstOrFail();

                $data = [
                    'status'     => $validated['status'],
                    'updated_at' =>now(),
                ];

                $order->update($data);
                $po->update($data);


                $user_id = Auth::user()->user_id;


                Logs::create([
                    'user_id' => Auth::user()->user_id,
                    'action' => 'Commited on an order',
                    'log_id' => $log_id,
                    'description' => "Staff '{$user_id}' {$request->status} order '{$request->order_id}'",
                ]);

                OrderHistory::create([
                    'action_by' => Auth::user()->user_id,
                    'order_id' => $request->order_id,
                    'action_at' => now(),
                    'history_id' => $history_id,
                    'label' => 'Order',
                    'amount' => $order->total_amount,
                    'status' => $request->status,
                ]);
                
                DB::commit();
                
                return back()->with('success', 'Order status updated successfully.');

                    
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Order update failed:', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'request_data' => $request->all(),
                ]);
                
                return redirect()->back()
                    ->with('error', 'Order update failed: ' . $e->getMessage() . 
                        '. Please check the logs for more details.')
                    ->withInput();
            }
        }
    
        public function customerOrderPdf($order_id)
        {
            $order = Orders::where('order_id', $order_id)->firstOrFail();
            $items = $order->items;

            $pdf = Pdf::loadView('pdf.orders.customer_order', compact('order', 'items'));
            return $pdf->stream("customer-order-{$order_id}.pdf");
        }


        public function deliveryReceiptPdf($order_id)
        {
            $order = Orders::where('order_id', $order_id)->firstOrFail();
            $delivery = Delivery::where('order_id', $order_id)->firstOrFail();
            $delivery_id = $delivery->delivery_id;
            $items = DeliveryItems::where('delivery_id', $delivery_id)->get();
            $pdf = PDF::loadView('pdf.orders.delivery_receipt', compact('delivery', 'items'));
            return $pdf->stream("delivery-receipt-{$delivery_id}.pdf");
        }

        public function salesInvoicePdf($order_id)
        {
            $order = Orders::with([
                'supplier.user',
                'items.product'
            ])->where('order_id', $order_id)->firstOrFail();
            $items = $order->items;
            $pdf = PDF::loadView('pdf.orders.sales_invoice', compact('order', 'items'));
            return $pdf->stream("sales-invoice-{$order_id}.pdf");
        }


}
