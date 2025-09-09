<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Receipt;
use App\Models\Invoice;
use App\Models\Orders;
use Carbon\Traits\Timestamp;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Region;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class PurchaseOrderController extends Controller {
public function purchaseOrder()
{
    $user = auth()->user();
    $search = request('search');
    $from = request('from_date', now()->startOfMonth()->format('Y-m-d'));
    $to = request('to_date', now()->endOfMonth()->format('Y-m-d'));
    $status = request('status');

    $query = PurchaseOrder::query();

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('po_number', 'like', "%{$search}%")
                ->orWhere('receiver_name', 'like', "%{$search}%")
                ->orWhere('company_name', 'like', "%{$search}%")
                ->orWhere('order_date', 'like', "%{$search}%"); 
        });
    }

    if ($user->user_type !== 'Staff' && $user->user_type !== 'Admin') {
        $query->where('user_id', $user->id);
    }

    if ($status && in_array($status, ['Draft', 'Pending', 'Processing', 'Delivered', 'Cancelled', 'Rejected'])) {
        $query->where('status', $status);
    }

    if ($from && $to) {
        $query->whereBetween('order_date', [
            Carbon::parse($from)->startOfDay(),
            Carbon::parse($to)->endOfDay()
        ]);
    }

    $purchaseOrders = $query
        ->where('status', '!=', 'Draft')  
        ->orderBy('order_date', 'desc')
        ->paginate(50);

    foreach ($purchaseOrders as $po) {
        $paidAmount = Receipt::where('po_number', $po->po_number)
            ->where('status', 'Verified') 
            ->sum('total_amount');

        $po->remaining_balance = max($po->grand_total - $paidAmount, 0); 
    }


    return view('purchase_order', compact('user', 'purchaseOrders', 'search', 'from', 'to', 'status'));
}


        
    public function purchaseReceipts($po_number)
    {
        $user = auth()->user();

        $receipts = Receipt::where('po_number', $po_number)->get();

        return view('purchase_orders/receipts_purchase_order', compact('user', 'receipts'));
    }


    
    public function cancelOrder($order_id)
    {
        $user = auth()->user();
        $ownerId = Orders::where('order_id', $order_id)->value('customer_id');
        if (!in_array($user->user_type, ['Admin', 'Staff']) && $ownerId !== $user->id) {
            abort(403, 'Unauthorized action');
        }

        try {
            DB::beginTransaction();
            
            // Get all order items for this order
            $orderItems = Orders::where('order_id', $order_id)->get();
            
            foreach ($orderItems as $orderItem) {
                $product = Product::find($orderItem->product_id);
                if ($product) {
                    $product->quantity += $orderItem->quantity;
                    $product->save();
                }
            }
            
            // Update order status
            Orders::where('order_id', $order_id)->update([
                'status' => 'Cancelled',
                'action_at' => now(),
                'action_by' => $user->name,
            ]);
            
            DB::commit();
            
            return redirect()->route('orders.view', $order_id)
                ->with('success', 'Order cancelled successfully! Product quantities have been restored.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error cancelling order: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to cancel order. Please try again.');
        }
    }

    public function productSearch()
    {
        $search = request('search');
        $query = Product::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('product_id', 'like', "%{$search}%")
                ->orWhere('status', 'like', "%{$search}%"); 
            });
        }

        $products = $query->orderBy('created_at', 'desc');

        return view('store_create_order', compact('search', 'products'));
    }



    public function purchaseOrderForm($po_number)
    {
        $user = auth()->user();
        $order = PurchaseOrder::where('po_number', $po_number)->firstOrFail();

        $ordersItem = PurchaseOrderItem::where('po_id', $po_number)
            ->orderBy('created_at', 'desc') 
            ->get();

        return view('purchase_orders.purchase_order_form', compact('user', 'order', 'ordersItem'));
    }

    public function invoiceView($po_number)
    {
        $user = auth()->user();
        $order = PurchaseOrder::where('po_number', $po_number)->firstOrFail();
        $invoice = Invoice::where('po_number', $po_number)->firstOrFail();
        $invoiceItems = PurchaseOrderItem::where('po_id', $po_number)
            ->orderBy('created_at', 'desc') 
            ->get();

        return view('purchase_orders.invoice-view', compact('user', 'order', 'invoiceItems', 'invoice'));
    }

    public function downloadPDF($po_number)
    {
        $user = auth()->user();
        $order = PurchaseOrder::where('po_number', $po_number)->firstOrFail();

        $ordersItem = PurchaseOrderItem::where('po_id', $po_number)
            ->orderBy('created_at', 'desc')
            ->get();

        $pdf = Pdf::loadView('purchase_orders.purchase_order_pdf', compact('user', 'order', 'ordersItem'));
        return $pdf->download("PurchaseOrder-{$order->po_number}.pdf");
    }


    public function storeOrderView()
    {
        $user = auth()->user();
        $search = request('search');

        $query = Product::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('product_id', 'like', "%{$search}%");
            });
        }

        $products = $query
            ->select('products.*')
            ->selectSub(function ($q) {
                $q->from('orders')
                ->selectRaw('COALESCE(SUM(quantity), 0)') 
                ->whereColumn('orders.product_id', 'products.id');
            }, 'sold_quantity')
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString(); 
        $regions = Region::orderBy('region_name')
            ->get(['region_id','region_name']);

        return view('purchase_orders.store_create_order', compact('user', 'products', 'search', 'regions'));
    }

    private function randomBase36String($length = 5)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $randomString;
    }

    // public function store(Request $request)
    // {
    //     $user = auth()->user();

    //     $request->validate([
    //         'receiver_name'   => 'required',
    //         'company_name'    => 'required|string|max:255',
    //         'postal_code'     => 'nullable|string|max:10',
    //         'region'          => 'required|exists:region,region_id',
    //         'province'        => 'required|exists:province,province_id',
    //         'municipality'    => 'required|exists:municipality,municipality_id',
    //         'barangay'        => 'required|exists:barangay,barangay_id',
    //         'street'          => 'required|string|max:255',
    //         'billing_address' => 'required',
    //         'contact_phone'   => 'required',
    //         'contact_email'   => 'required|email',
    //         'cart_data'       => 'required',
    //         'order_notes'     => 'nullable|string|max:500',
    //         'receiver_mobile' => 'required|string|max:15',
    //     ]);

    //     $cart = json_decode($request->cart_data, true);

    //     if (empty($cart)) {
    //         return back()->withErrors(['cart_data' => 'Cart cannot be empty.'])->withInput();
    //     }

    //     try {
    //         DB::beginTransaction();

    //         $subtotal = collect($cart)->sum(fn($item) => floatval($item['price']) * intval($item['quantity']));
    //         $tax = 0; 
    //         $grandTotal = $subtotal + $tax;

    //         $attachmentPath = null;
    //         if ($request->hasFile('po_attachment')) {
    //             $attachmentPath = $request->file('po_attachment')->store('attachments', 'public');
    //         }

            
    //         $date = date('Ymd');
    //         $po_number = 'PO-' . $date . '-' . $this->randomBase36String(5);

    //         $po = PurchaseOrder::create([
    //             'user_id'         => $user->id,
    //             'po_number'       => $po_number,
    //             'receiver_name'   => $request->receiver_name,
    //             'receiver_mobile' => $request->receiver_mobile,
    //             'postal_code'     => $request->postal_code,
    //             'region_id'       => $request->region,
    //             'province_id'     => $request->province,
    //             'municipality_id' => $request->municipality,
    //             'barangay_id'     => $request->barangay,
    //             'street'          => $request->street,
    //             'company_name'    => $request->company_name,
    //             'billing_address' => $request->billing_address,
    //             'contact_phone'   => $request->contact_phone,
    //             'contact_email'   => $request->contact_email,
    //             'order_notes'     => $request->order_notes,
    //             'subtotal'        => $subtotal,
    //             'tax_amount'      => $tax,
    //             'grand_total'     => $grandTotal,
    //             'status'          => $request->input('status', 'Pending'),
    //             'order_date'      => Carbon::now(),
    //         ]);

    //         $order_id = 'ORD-' . $date . '-' . $this->randomBase36String(5);

    //         foreach ($cart as $item) {
    //             $product = Product::find($item['id']);

    //             if (!$product) {
    //                 throw new \Exception("Product with ID {$item['id']} not found.");
    //             }

    //             Orders::create([
    //                 'po_id'       => $po->po_number,
    //                 'order_id'    => $order_id,
    //                 'customer_id' => $user->id,
    //                 'product_id'  => $product->id,
    //                 'quantity'    => $item['quantity'],
    //                 'unit_price'  => $product->price,
    //                 'total_price' => $product->price * $item['quantity'],
    //                 'status'     => $request->input('status', 'Pending'),
    //             ]);

    //             PurchaseOrderItem::create([
    //                 'po_id'       => $po->po_number,
    //                 'product_id'  => $item['id'],
    //                 'order_id'    => $order_id,
    //                 'quantity'    => intval($item['quantity']),
    //                 'unit_price'  => floatval($item['price']),
    //                 'total_price' => floatval($item['price']) * intval($item['quantity']),
    //             ]);
    //         }

    //         DB::commit(); 

    //         return redirect()->route('purchase_order')->with('success', 'Purchase order placed successfully!');

    //     } catch (\Throwable $e) {
    //         DB::rollBack(); 
    //         return back()->withErrors(['error' => 'Failed to place order: ' . $e->getMessage()])->withInput();
    //     }
    // }

    

    public function purchaseOrderView($po_number)
    {   
        $po = PurchaseOrder::where('po_number', $po_number)->firstOrFail();
        $order = PurchaseOrder::where('po_number', $po_number)->firstOrFail();

        $ordersItem = PurchaseOrderItem::where('po_id', $po_number)
            ->orderBy('created_at', 'desc')
            ->get();

        $orderCount = PurchaseOrderItem::where('po_id', $po_number)->count();

        return view('purchase_orders.purchase_order_view', compact('po','order', 'ordersItem', 'orderCount' ));
    }


    public function store(Request $request)
    {
    $user = auth()->user();

    $request->validate([
        'receiver_name'   => 'required',
        'company_name'    => 'required|string|max:255',
        'postal_code'     => 'nullable|string|max:10',
        'region'          => 'required|exists:region,region_id',
        'province'        => 'required|exists:province,province_id',
        'municipality'    => 'required|exists:municipality,municipality_id',
        'barangay'        => 'required|exists:barangay,barangay_id',
        'street'          => 'required|string|max:255',
        'billing_address' => 'required',
        'contact_phone'   => 'required',
        'contact_email'   => 'required|email',
        'cart_data'       => 'required',
        'order_notes'     => 'nullable|string|max:500',
        'receiver_mobile' => 'required|string|max:15',
        
    ]);


    $cart = json_decode($request->cart_data, true);

    if (empty($cart)) {
        return back()->withErrors(['cart_data' => 'Cart cannot be empty.'])->withInput();
    }

    try {
        DB::beginTransaction();

        foreach ($cart as $item) {
            $product = Product::find($item['id']);
            
            if (!$product) {
                DB::rollBack();
                return back()->withErrors(['error' => "Product with ID {$item['id']} not found."])->withInput();
            }
            
            if ($product->quantity < $item['quantity']) {
                DB::rollBack();
                return back()->withErrors([
                    'error' => "Product '{$product->name}' is out of stock or insufficient quantity. Available: {$product->quantity}, Requested: {$item['quantity']}"
                ])->withInput();
            }
        }

        $subtotal = collect($cart)->sum(fn($item) => floatval($item['price']) * intval($item['quantity']));
        $tax = 0; 
        $grandTotal = $subtotal + $tax;

        $attachmentPath = null;
        if ($request->hasFile('po_attachment')) {
            $attachmentPath = $request->file('po_attachment')->store('attachments', 'public');
        }

        $date = date('Ymd');
        $po_number = 'PO-' . $date . '-' . $this->randomBase36String(5);
        $status = $request->input('status', 'Pending');

        $po = PurchaseOrder::create([
            'user_id'         => $user->id,
            'po_number'       => $po_number,
            'receiver_name'   => $request->receiver_name,
            'receiver_mobile' => $request->receiver_mobile,
            'postal_code'     => $request->postal_code,
            'region_id'       => $request->region,
            'province_id'     => $request->province,
            'municipality_id' => $request->municipality,
            'barangay_id'     => $request->barangay,
            'street'          => $request->street,
            'company_name'    => $request->company_name,
            'billing_address' => $request->billing_address,
            'contact_phone'   => $request->contact_phone,
            'contact_email'   => $request->contact_email,
            'order_notes'     => $request->order_notes,
            'subtotal'        => $subtotal,
            'tax_amount'      => $tax,
            'grand_total'     => $grandTotal,
            'status'          => $status,
            'order_date'      => Carbon::now(),
            'payment_status' => 'Unpaid'
        ]);

    
        

        $order_id = 'ORD-' . $date . '-' . $this->randomBase36String(5);

            if ($status !== 'Draft') {
            $order_id = 'ORD-' . $date . '-' . $this->randomBase36String(5);

            foreach ($cart as $item) {
                $product = Product::find($item['id']);

                if (!$product) {
                    throw new \Exception("Product with ID {$item['id']} not found.");
                }

                // Create order record
                Orders::create([
                    'po_id'       => $po->po_number,
                    'order_id'    => $order_id,
                    'customer_id' => $user->id,
                    'product_id'  => $product->id,
                    'quantity'    => $item['quantity'],
                    'unit_price'  => $product->price,
                    'total_price' => $product->price * $item['quantity'],
                    'status'      => $status,
                ]);

                // Create purchase order item
                PurchaseOrderItem::create([
                    'po_id'       => $po->po_number,
                    'product_id'  => $item['id'],
                    'order_id'    => $order_id,
                    'quantity'    => intval($item['quantity']),
                    'unit_price'  => floatval($item['price']),
                    'total_price' => floatval($item['price']) * intval($item['quantity']),
                ]);


                $product->quantity -= $item['quantity'];

                // Update status based on new quantity
                if ($product->quantity === '0') {
                    $product->status = "No stock";
                } elseif ($product->quantity < 20) {
                    $product->status = "Low stock";
                } else {
                    $product->status = "Available";
                }

                $product->save();




            }
        }

        DB::commit(); 

        return redirect()->route('purchase_order')->with('success', 'Purchase order placed successfully!');

    } catch (\Throwable $e) {
        DB::rollBack(); 
        Log::error('Purchase Order creation error: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        
        return back()->withErrors(['error' => 'Failed to place order: ' . $e->getMessage()])->withInput();
    }
    }


    // public function cancelPurchaseOrder(Request $request, $id)
    // {
    //     try {
    //         $purchaseOrder = PurchaseOrder::findOrFail($id);
            
    //         // Only allow cancellation if order is still pending/processing
    //         if (!in_array($purchaseOrder->status, ['Pending', 'Processing'])) {
    //             return back()->withErrors(['error' => 'Cannot cancel order with status: ' . $purchaseOrder->status]);
    //         }

    //         DB::beginTransaction();

    //         $purchaseOrderItems = PurchaseOrderItem::where('po_id', $purchaseOrder->po_number)->get();
            
    //         foreach ($purchaseOrderItems as $item) {
    //             $product = Product::find($item->product_id);
    //             if ($product) {
    //                 // Restore quantity back to inventory
    //                 $product->quantity += $item->quantity;
    //                 $product->save();
    //             }
    //         }

    //         $purchaseOrder->status = 'Cancelled';
    //         $purchaseOrder->save();

    //         Orders::where('po_id', $purchaseOrder->po_number)->update(['status' => 'Cancelled']);

    //         DB::commit();

    //         return redirect()->route('purchase_order')->with('success', 'Purchase order cancelled and inventory restored successfully!');

    //     } catch (\Throwable $e) {
    //         DB::rollBack();
    //         Log::error('Purchase Order cancellation error: ' . $e->getMessage());
            
    //         return back()->withErrors(['error' => 'Failed to cancel purchase order: ' . $e->getMessage()]);
    //     }
    // }

    public function changeStatus(Request $request)
    {
        $po = PurchaseOrder::findOrFail($request->po_id);
        $status = $request->input('status'); 
        $user = $request->input('user_id');

        $po->status = $status;

        if (in_array($status, ['Cancelled', 'Rejected'])) {
            $purchaseOrderItems = PurchaseOrderItem::where('po_id', $po->id)->get();

            foreach ($purchaseOrderItems as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->quantity += $item->quantity;
                    $product->save();
                }
            }
        }

        if ($status === "Accepted") {
            $po->approved_by = $user; 
            $po->approved_at = now(); 
            
        } 
        elseif ($status === "Delivered") {
            $po->delivered_at = now(); 

            $date = date('Ymd');
            $invoice_number = 'INV-' . $date . '-' . $this->randomBase36String(5);

            $invoice = new Invoice();
            $invoice->po_number = $po->po_number; 
            $invoice->user_id = $po->user_id; 

            $invoice->invoice_number = $invoice_number;
            $invoice->delivered_at = $po->delivered_at; 
            $invoice->billing_address = $po->billing_address; 
            $invoice->subtotal = $po->subtotal; 
            $invoice->tax_amount = $po->tax_amount;
            $invoice->grand_total = $po->grand_total;
            $invoice->status = 'unpaid';
            $invoice->save();
        } 
        elseif ($status === "Cancelled") {
            $po->cancelled_at = now(); 
            $po->cancelled_by = $user;
        } 
        elseif ($status === "Rejected") {
            $po->rejected_at = now(); 
            $po->rejected_by = $user; 

        }

        $po->save();

        return back()->with('success', "Purchase order has been {$status}.");
    }

    public function cancelPOStatus(Request $request)
    {
        $po = PurchaseOrder::findOrFail($request->po_id);
        $status = $request->input('status'); 
        $user = $request->input('user_id');
        $user_type = $request->input('user_type');

        $po->status = $status;

        if (in_array($status, ['Cancelled', 'Rejected'])) {
            $purchaseOrderItems = PurchaseOrderItem::where('po_id', $po->id)->get();

            foreach ($purchaseOrderItems as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->quantity += $item->quantity;
                    $product->save();
                }
            }
        }

      
        if ($status === "Cancelled") {
            $po->cancelled_at = now(); 
            $po->cancelled_by = $user;
            $po->cancelled_user_type= $user_type;
        } 


        $po->save();
        return back()->with('success', 'Purchase order saved successfully.');

    }
     
}