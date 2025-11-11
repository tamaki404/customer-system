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
        if($user->role === "Customer"){

            $customer = Customers::where('user_id', $user->user_id)->firstOrFail();
            $credit = Credits::where('user_id', $user->user_id)->firstOrFail();
            $delivery = DeliveryRequest::where('delivery_id', $delivery_id)->firstOrFail();
            $items = DeliveryItemRequest::where('delivery_id', $delivery_id)->get();

        }
        elseif($user->role !== "Customer"){
            $delivery = DeliveryRequest::where('delivery_id', $delivery_id)->first();
            $customer = Customers::where('user_id', $delivery->user_id)->first();
            $credit = Credits::where('user_id', $user->user_id)->first();
            $items = DeliveryItemRequest::where('delivery_id', $delivery_id)->get();

        }


        return view('franken.dlv.delivery', compact(
            'user',
            'customer',
            'credit',
            'delivery',
            'items',

        ));
    
    }
    public function receive(Request $request)
        {
            $request->validate([
                'delivery_id' => 'required|string', 
                'feedback' => 'nullable|string|max:200',
                'pod_file' => 'required|file|mimes:pdf|max:2048',
                'received_kilos' => 'array',
                'received_heads' => 'array',
            ]);

            $user = Auth::user();

            $delivery = DeliveryRequest::where('delivery_id', $request->delivery_id)->firstOrFail();
            $purchaseRequest = PurchaseRequest::where('po_id', $delivery->po_id)->firstOrFail();

            // Store PDF as binary
            $pdfContent = file_get_contents($request->file('pod_file')->getRealPath());

            //  Update this delivery
            $delivery->update([
                'status' => "Delivered",
                'feedback' => $request->feedback,
                'delivered_date' => now(),
                'pod_file' => $pdfContent,
                'pod_mime' => 'application/pdf',
            ]);

            //  Update all delivery items for this delivery
            $receivedKilos = $request->input('received_kilos', []);
            $receivedHeads = $request->input('received_heads', []);
            $allItemIds = array_unique(array_merge(array_keys($receivedKilos), array_keys($receivedHeads)));

            foreach ($allItemIds as $deliveryItemId) {
                $deliveryItem = DeliveryItemRequest::where('delivery_item_id', $deliveryItemId)->first();

                if ($deliveryItem) {

                    if (isset($receivedKilos[$deliveryItemId])) {
                        $plannedKilos = $deliveryItem->planned_kilos ?? $deliveryItem->placed_kilos ?? 0;
                        $updateData['received_kilos'] = $receivedKilos[$deliveryItemId];
                    }

                    if (isset($receivedHeads[$deliveryItemId])) {
                        $plannedHeads = $deliveryItem->planned_heads ?? $deliveryItem->placed_heads ?? 0;
                        $updateData['received_heads'] = $receivedHeads[$deliveryItemId];
                    }

                    $deliveryItem->update($updateData);
                }
            }

            //  Check if ALL deliveries for this order are now "Delivered"
            $totalDeliveries = DeliveryRequest::where('po_id', $delivery->po_id)->count();
            $deliveredCount = DeliveryRequest::where('po_id', $delivery->po_id)
                                    ->where('status', 'Delivered')
                                    ->count();

            if ($totalDeliveries > 0 && $totalDeliveries === $deliveredCount) {
                $purchaseRequest->update(['status' => 'Completed']);
            }
            elseif ($totalDeliveries < 0 && $totalDeliveries === $deliveredCount) {
                $purchaseRequest->update(['status' => 'Progressing']);
            }
            // after updating delivery items above

            $items = DeliveryItemRequest::where('delivery_id', $delivery->delivery_id)
                ->with('productSetting')
                ->orderBy('created_at', 'asc')
                ->get();

            $total_amount = 0;

foreach ($items as $item) {
    if ($item->productSetting && $item->product) {

        // default value
        $qty = 0;

        // check measurement type
        if (strtolower($item->product->measurement_type) === 'heads' 
            || strtolower($item->product->measurement_type) === 'head') {

            // use received_heads
            $qty = $item->received_heads ?? 0;

        } else {
            // use kilos
            $qty = $item->received_kilos ?? 0;
        }

        $balance = $qty * $item->productSetting->nego_price;
        $item->update(['balance' => $balance]);
        $total_amount += $balance;
    }
}


            $date  = date('Ymd');
            $history_id = 'OH-' . $date . '-' . $this->randomBase36String(5);

            PurchaseHistory::create([
                'po_id' => $delivery->po_id,
                'customer_id' => $delivery->customer_id,
                'purchase_id' => $history_id,
                'delivery_id' => $delivery->delivery_id,
                'label' => "Delivery",
                'amount' => $total_amount,
                'status' => "Successful"
            ]);

            return back()->with('success', 'Delivery successfully confirmed with variance recorded.');
    }

    public function list(Request $request)
    {
        $user = Auth::user();
        if ($user->role === 'Customer') {
            $deliveries = DeliveryRequest::where('customer_id', $user->ucustomer_idser_id)
                ->orderBy('created_at', 'desc')
                ->get();

        } elseif ($user->role !== 'Customer') {
            $deliveries = DeliveryRequest::all()->orderBy('created_at', 'desc')->get();
            
        }

        return view('franken.dlv.list', compact(
            'deliveries',


        ));
    
    }

}
