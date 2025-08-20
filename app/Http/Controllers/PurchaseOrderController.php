<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Orders;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Region;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseOrderController extends Controller {
    public function purchaseOrder()
    {
        $user = auth()->user();
        $search = request('search');
        $from = request('from_date', now()->startOfMonth()->format('Y-m-d'));
        $to = request('to_date', now()->endOfMonth()->format('Y-m-d'));
        $status = request('status');

        $query = PurchaseOrder::query();

        if ($user->user_type !== 'Staff' && $user->user_type !== 'Admin') {
            $query->where('user_id', $user->id);
        }

        if ($status && in_array($status, ['Draft', 'Pending', 'Processing', 'Partial', 'Completed', 'Cancelled'])) {
            $query->where('status', $status);
        }

        if ($from && $to) {
            $query->whereBetween('order_date', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay()
            ]);
        }


        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('po_number', 'like', "%$search%")
                ->orWhere('company_name', 'like', "%$search%")
                ->orWhere('subtotal', 'like', "%$search%")
                ->orWhere('order_date', 'like', "%$search%");
            });
        }

        $purchaseOrders = $query->orderBy('order_date', 'desc')->paginate(50);

        return view('purchase_order', compact('user', 'purchaseOrders', 'search', 'from', 'to', 'status'));
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


    public function downloadPDF($po_number)
    {
        $user = auth()->user();
        $order = PurchaseOrder::where('po_number', $po_number)->firstOrFail();

        $ordersItem = PurchaseOrderItem::where('po_id', $po_number)
            ->orderBy('created_at', 'desc')
            ->get();

        $pdf = Pdf::loadView('purchase_orders.purchase_order_form', compact('user', 'order', 'ordersItem'));

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

            $subtotal = collect($cart)->sum(fn($item) => floatval($item['price']) * intval($item['quantity']));
            $tax = 0; 
            $grandTotal = $subtotal + $tax;

            $attachmentPath = null;
            if ($request->hasFile('po_attachment')) {
                $attachmentPath = $request->file('po_attachment')->store('attachments', 'public');
            }

            $date = date('Ymd');
            $po_number = 'PO-' . $date . '-' . $this->randomBase36String(5);

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
                'status'          => $request->input('status', 'Pending'),
                'order_date'      => Carbon::now(),
            ]);

            $order_id = 'ORD-' . $date . '-' . $this->randomBase36String(5);

            foreach ($cart as $item) {
                $product = Product::find($item['id']);

                if (!$product) {
                    throw new \Exception("Product with ID {$item['id']} not found.");
                }

                Orders::create([
                    'po_id'       => $po->po_number,
                    'order_id'    => $order_id,
                    'customer_id' => $user->id,
                    'product_id'  => $product->id,
                    'quantity'    => $item['quantity'],
                    'unit_price'  => $product->price,
                    'total_price' => $product->price * $item['quantity'],
                    'status'     => $request->input('status', 'Pending'),
                ]);

                PurchaseOrderItem::create([
                    'po_id'       => $po->po_number,
                    'product_id'  => $item['id'],
                    'order_id'    => $order_id,
                    'quantity'    => intval($item['quantity']),
                    'unit_price'  => floatval($item['price']),
                    'total_price' => floatval($item['price']) * intval($item['quantity']),
                ]);
            }

            DB::commit(); 

            return redirect()->route('purchase_order')->with('success', 'Purchase order placed successfully!');

        } catch (\Throwable $e) {
            DB::rollBack(); 
            return back()->withErrors(['error' => 'Failed to place order: ' . $e->getMessage()])->withInput();
        }
    }

    

    public function purchaseOrderView($po_number)
    {   
        $order = PurchaseOrder::where('po_number', $po_number)->firstOrFail();

        return view('purchase_orders.purchase_order_view', compact('order', ));
    }


     
}