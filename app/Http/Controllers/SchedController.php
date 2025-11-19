<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Models\DeliveryRequest;
use App\Models\DeliveryItemRequest;
use App\Models\ProductSetting;
use App\Models\Customers;
use App\Models\Receipts;
use App\Models\Payments;
use App\Models\Products;
use App\Models\Logs;
use Illuminate\Support\Facades\Auth;



class SchedController extends Controller
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
    public function create(Request $request)
    {
        $po = PurchaseRequest::findOrFail($request->po_id);
        $user = Auth::user();
        $date = date('Ymd');
        $deliveryId = 'DLV-' . $date . '-' . $this->randomBase36String(5);
        $itemId = 'ITEM' . $date . '-' . $this->randomBase36String(5);

        // Create DeliveryRequest
        $delivery = DeliveryRequest::create([
            'po_id' => $po->id,
            'delivery_id' => $deliveryId,
            'customer_id' => $po->customer_id,
            'delivery_date' => $request->delivery_date,
            'label' => 'Return',
            'status' => 'Scheduled',
            'action_by' => $user->user_id,
            'action_at' => now(),
        ]);

        // Loop through items submitted
        foreach ($request->items as $product_id => $data) {
            DeliveryItemRequest::create([
                'delivery_id' => $deliveryId,
                'delivery_item_id' => $itemId,
                'po_id' => $po->id,
                'customer_id' => $po->customer_id,
                'product_id' => $product_id,
                'planned_heads' => $data['planned_heads'] ?? 0,
                'planned_kilos' => $data['planned_kilos'] ?? 0,
                'balance' => ($data['planned_heads'] ?? 0) + ($data['planned_kilos'] ?? 0),
            ]);


        }

        return redirect()->back()->with('success', 'Delivery scheduled successfully!');
    }

}
