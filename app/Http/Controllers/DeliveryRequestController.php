<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Models\Customers;
use App\Models\DeliveryItemRequest;
use App\Models\PurchaseRequest;
use App\Models\DeliveryRequest;
use App\Models\Credits;
use App\Models\PurchaseHistory;
use App\Models\Promos;
use App\Models\Logs;

use Carbon\Carbon;

class DeliveryRequestController extends Controller
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
    public function delivery($delivery_id, Request $request)
    {
        $user = Auth::user();

        if ($user->role === "Customer") {
            $user = Auth::user();
            $customer = Customers::where('user_id', $user->user_id)->first();
            $credit = Credits::where('user_id', $user->user_id)->first();
            $delivery = DeliveryRequest::where('delivery_id', $delivery_id)->first();
            $items = DeliveryItemRequest::where('delivery_id', $delivery_id)->get();
            if ($delivery->customer_id !==  $user->customer->customer_id) {
                return redirect()->back()->with('error', 'Purchase order does not exist');
            }
            if ($delivery->status === "Pending") {
                return redirect()->back()->with('error', 'Purchase order not confirmed yet');
            }
        }elseif ($user->role !== "Customer") {
            $delivery = DeliveryRequest::where('delivery_id', $delivery_id)->first();
            $customer = Customers::where('user_id', $delivery->user_id)->first();
            $credit = Credits::where('user_id', $user->user_id)->first();
            $items = DeliveryItemRequest::where('delivery_id', $delivery_id)->get();

        }

            $activePromos = Promos::with('product')
                ->where('status', "Active")
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->where('quantity', '>', 0) 
                ->orderBy('quantity', 'asc') 
                ->get();

        return view('franken.dlv.delivery', compact(
            'user',
            'customer',
            'credit',
            'delivery',
            'items',
            'activePromos'
        ));
    }

public function receive(Request $request)
{
    $request->validate([
        'delivery_id'   => 'required|string',
        'feedback'      => 'nullable|string|max:200',
        'pod_file'      => 'required|file|mimes:pdf|max:2048',
        'received_kilos'=> 'array',
        'received_heads'=> 'array',
    ]);

    try {
        $user     = Auth::user();
        $delivery = DeliveryRequest::where('delivery_id', $request->delivery_id)->first();
        $credit   = Credits::where('user_id', $user->user_id)->first();
        $purchaseRequest = PurchaseRequest::where('po_id', $delivery->po_id)->first();

        // Store PDF binary
        $pdfContent = file_get_contents($request->file('pod_file')->getRealPath());

        // Set due date
        $due_date = now()->addDays((int)$credit->credit_term);

        // Update delivery main record
        $delivery->update([
            'status'        => "Delivered",
            'feedback'      => $request->feedback,
            'delivered_date'=> now(),
            'updated_at'    => now(),
            'return_status' => '',          // will fill later if variance exists
            'pod_file'      => $pdfContent,
            'pod_mime'      => 'application/pdf',
            'due_date'      => $due_date,
        ]);

        // ---------------------------
        // UPDATE DELIVERY ITEM VALUES
        // ---------------------------
        $receivedKilos = $request->input('received_kilos', []);
        $receivedHeads = $request->input('received_heads', []);
        $allItemIds = array_unique(array_merge(array_keys($receivedKilos), array_keys($receivedHeads)));

        foreach ($allItemIds as $deliveryItemId) {

            $deliveryItem = DeliveryItemRequest::where('delivery_item_id', $deliveryItemId)
                ->with('product')
                ->first();

            if (!$deliveryItem || !$deliveryItem->product) continue;

            $measurement = strtolower($deliveryItem->product->measurement_type);

            if (($measurement === 'heads' || $measurement === 'head') &&
                isset($receivedHeads[$deliveryItemId])) {
                $deliveryItem->update(['received_heads' => $receivedHeads[$deliveryItemId]]);
            }

            if (($measurement === 'kilos' || $measurement === 'kg') &&
                isset($receivedKilos[$deliveryItemId])) {
                $deliveryItem->update(['received_kilos' => $receivedKilos[$deliveryItemId]]);
            }

            if ($measurement === 'heads&kilos') {
                if (isset($receivedHeads[$deliveryItemId])) {
                    $deliveryItem->update(['received_heads' => $receivedHeads[$deliveryItemId]]);
                }
                if (isset($receivedKilos[$deliveryItemId])) {
                    $deliveryItem->update(['received_kilos' => $receivedKilos[$deliveryItemId]]);
                }
            }
        }

        // ---------------------------------------------------
        // CHECK FOR VARIANCE (APPLY return_status = Unresolved)
        // ---------------------------------------------------
        $hasVariance = DeliveryItemRequest::where('delivery_id', $delivery->delivery_id)
            ->with('product')
            ->get()
            ->contains(function ($item) {
                $measurement = strtolower($item->product->measurement_type);

                if (($measurement === 'heads' || $measurement === 'head') &&
                    ($item->received_heads != $item->expected_heads)) {
                    return true;
                }

                if (($measurement === 'kilos' || $measurement === 'kg') &&
                    ($item->received_kilos != $item->expected_kilos)) {
                    return true;
                }

                if ($measurement === 'heads&kilos' &&
                    (
                        $item->received_heads != $item->expected_heads ||
                        $item->received_kilos != $item->expected_kilos
                    )) {
                    return true;
                }

                return false;
            });

        if ($hasVariance) {
            $delivery->update([
                'return_status' => 'Unresolved'
            ]);
        }

        // ---------------------------
        // UPDATE PO STATUS
        // ---------------------------
        $totalDeliveries   = DeliveryRequest::where('po_id', $delivery->po_id)->count();
        $deliveredCount    = DeliveryRequest::where('po_id', $delivery->po_id)
                                ->where('status', 'Delivered')
                                ->count();

        $purchaseRequest->update([
            'status' => $totalDeliveries === $deliveredCount ? 'Completed' : 'Progressing'
        ]);

        // ---------------------------
        // BALANCE CALCULATION
        // ---------------------------
        $items = DeliveryItemRequest::where('delivery_id', $delivery->delivery_id)
            ->with('productSetting')
            ->orderBy('created_at', 'asc')
            ->get();

        $total_amount = 0;

        foreach ($items as $item) {
            if ($item->productSetting && $item->product) {

                $qty = (strtolower($item->product->measurement_type) === 'heads' ||
                        strtolower($item->product->measurement_type) === 'head')
                        ? ($item->received_heads ?? 0)
                        : ($item->received_kilos ?? 0);

                $unitPrice = $item->productSetting->nego_price;
                $promoPrice = $unitPrice;

                // Apply promo if valid
                if (
                    $item->promo &&
                    $item->promo->status === 'Active' &&
                    $item->promo->quantity > 0 &&
                    $item->promo->start_date <= now() &&
                    $item->promo->end_date >= now()
                ) {
                    // Compute promo pricing
                    if ($item->promo->value_type === "Fixed") {
                        $promoPrice = max(0, $unitPrice - $item->promo->value);
                    } else {
                        $discount = ($item->promo->value / 100) * $unitPrice;
                        $promoPrice = max(0, $unitPrice - $discount);
                    }

                    $discountedQty = min($qty, $item->promo->quantity);
                    $promoTotal = $discountedQty * $promoPrice;
                    $regularQty = $qty - $discountedQty;
                    $regularTotal = $regularQty * $unitPrice;

                    // Update promo
                    $newQty = $item->promo->quantity - $discountedQty;
                    $item->promo->update([
                        'quantity' => max(0, $newQty),
                        'status'   => $newQty <= 0 ? 'Sold out' : $item->promo->status
                    ]);

                    $balance = $promoTotal + $regularTotal;

                } else {
                    $balance = $qty * $unitPrice;
                }

                $item->update(['balance' => $balance]);
                $total_amount += $balance;
            }
        }

        // ---------------------------
        // PURCHASE HISTORY
        // ---------------------------
        $date = date('Ymd');
        $history_id = 'OH-' . $date . '-' . $this->randomBase36String(5);

        PurchaseHistory::create([
            'po_id'       => $delivery->po_id,
            'customer_id' => $delivery->customer_id,
            'purchase_id' => $history_id,
            'delivery_id' => $delivery->delivery_id,
            'label'       => "Delivery",
            'amount'      => $total_amount,
            'status'      => "Successful"
        ]);

        // ---------------------------
        // LOGS
        // ---------------------------
        $log_id = 'LOG-' . $date . '-' . $this->randomBase36String(5);

        Logs::create([
            'log_id'    => $log_id,
            'user_id'   => $user->user_id,
            'role'      => $user->role,
            'action'    => "Received a delivery",
            'description' => $delivery->delivery_id,
            'ip_address'=> request()->ip(),
            'entity'    => "DeliveryRequest",
            'entity_id' => $delivery->id,
        ]);

        return back()->with('success', 'Delivery successfully confirmed with variance recorded.');

    } catch (\Throwable $e) {
        dd($e->getMessage(), $e->getFile(), $e->getLine());
    }
}



    public function list(Request $request)
    {
        $user = Auth::user();
        if($user->role === "Customer"){
            $customer = Customers::where('user_id', $user->user_id)->first();
            $delivery = DeliveryRequest::join('purchase_requests as pr', 'delivery_requests.po_id', '=', 'pr.po_id')
                ->where('delivery_requests.customer_id', $customer->customer_id)
                ->whereNotIn('pr.status', ['Rejected', 'Cancelled', 'Pending'])
                ->orderBy('delivery_requests.delivery_date', 'asc')
                ->select('delivery_requests.*') 
                ->paginate(25);

        }elseif($user->role !== "Customer"){
            $delivery = DeliveryRequest::join('purchase_requests as pr', 'delivery_requests.po_id', '=', 'pr.po_id')
                ->whereNotIn('pr.status', ['Rejected', 'Cancelled', 'Pending'])
                ->orderBy('delivery_requests.delivery_date', 'asc')
                ->select('delivery_requests.*') 
                ->paginate(25);
        }        
        return view('franken.dlv.list', compact(
            'delivery',
            'user'
        ));
    }

    public function receipt($delivery_id)
    {
        $delivery = DeliveryRequest::where('delivery_id', $delivery_id)->firstOrFail();

        return view('franken.pdf.receipt', [
            'delivery' => $delivery
        ]);
    }

    public function update(Request $request, $delivery_id)
    {
        $request->validate([
            'planned_heads' => 'array',
            'planned_kilos' => 'array',
        ]);

        $delivery = DeliveryRequest::where('delivery_id', $delivery_id)->firstOrFail();

        $plannedHeads = $request->input('planned_heads', []);
        $plannedKilos = $request->input('planned_kilos', []);

        foreach ($delivery->Delitems as $item) {

            $newHeads = $plannedHeads[$item->delivery_item_id] ?? null;
            $newKilos = $plannedKilos[$item->delivery_item_id] ?? null;

            // ---- HEADS ONLY ----
            if ($item->product->measurement_type === "Heads") {
                if ($newHeads !== null) {
                    $item->planned_heads = min($newHeads, $item->planned_heads); // cannot exceed original planned value
                }
            }

            // ---- KILOS ONLY ----
            elseif ($item->product->measurement_type === "Kilos") {
                if ($newKilos !== null) {
                    $item->planned_kilos = min($newKilos, $item->planned_kilos);
                }
            }

            // ---- HEADS & KILOS ----
            elseif ($item->product->measurement_type === "Heads&Kilos") {
                if ($newHeads !== null) {
                    $item->planned_heads = min($newHeads, $item->planned_heads);
                }
                if ($newKilos !== null) {
                    $item->planned_kilos = min($newKilos, $item->planned_kilos);
                }
            }

            $item->save();
        }

        return back()->with('success', 'Delivery quantities updated successfully.');
    }


}
