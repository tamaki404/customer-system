<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PurchaseOrderController extends Controller
    {
    public function purchaseOrder()
    {
        $user = auth()->user();

        $purchaseOrders = PurchaseOrder::orderBy('order_date', 'desc')
            ->paginate(10);

        return view('purchase_order', compact('user', 'purchaseOrders'));
    }


    public function purchaseOrderForm(){
        $user = auth()->user();
        return view('purchase_orders/purchase_order_form', compact('user'));
    }

    public function storeOrderView(){
        $user = auth()->user();
        $search = request('search');
        $query = Product::query();
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                ->orWhere('id', 'like', "%$search%")
                ->orWhere('status', 'like', "%$search%")
                ->orWhere('product_id', 'like', "%$search%");
            });
        }
        
        $products = $query
            ->select('products.*')
            ->selectSub(function ($q) {
                $q->from('orders')
                ->selectRaw('COUNT(*)')
                ->whereColumn('orders.product_id', 'products.id');
            }, 'sold_quantity')
            ->orderByDesc('created_at')
            ->paginate(15);
            
        if ($search) {
            $products->appends(['search' => $search]);
        }
        
        return view('purchase_orders/store_create_order', compact('user', 'products', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_name' => 'required',
            'shipping_address' => 'required',
            'billing_address' => 'required',
            'contact_phone' => 'required',
            'contact_email' => 'required|email',
            'cart_data' => 'required',
        ]);

        $cart = json_decode($request->cart_data, true);
        
        if (empty($cart)) {
            return back()->withErrors(['cart_data' => 'Cart cannot be empty.'])->withInput();
        }

        $subtotal = collect($cart)->sum(fn($item) => floatval($item['price']) * intval($item['quantity']));
        $tax = 0; // compute if needed
        $grandTotal = $subtotal + $tax;

        // Handle file upload
        $attachmentPath = null;
        if ($request->hasFile('po_attachment')) {
            $attachmentPath = $request->file('po_attachment')->store('attachments', 'public');
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

                $po_number= 'PO-' . $date . '-' . randomBase36String(5);

        $po = PurchaseOrder::create([
            'user_id' => auth()->id(),
            'po_number' => $po_number,
            'receiver_name' => $request->receiver_name,
            'shipping_address' => $request->shipping_address,
            'billing_address' => $request->billing_address,
            'contact_phone' => $request->contact_phone,
            'contact_email' => $request->contact_email,
            'order_notes' => $request->order_notes,
            'po_attachment' => $attachmentPath,
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'grand_total' => $grandTotal,
            'status' => 'pending',
            'order_date' => Carbon::now(),
        ]);

        // Store purchase order items using product_id
        foreach ($cart as $item) {
            PurchaseOrderItem::create([
                'purchase_order_id' => $po->id,
                'product_id' => $item['id'],
                'quantity' => intval($item['quantity']),
                'unit_price' => floatval($item['price']),
                'total_price' => floatval($item['price']) * intval($item['quantity']),
            ]);
        }

        return redirect()->route('purchase_order')->with('success', 'Purchase order placed successfully!');
    }
}