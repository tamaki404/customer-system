<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\OrderReceipt;

class OrderReceiptController extends Controller
{

    private function randomBase36String($length = 5)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
    public function receivedOrder(Request $request){
        $request->validate([
            'feedback'   => 'nullable|string|max:255',
            'po_id'    => 'required|string|max:255|exists:purchase_orders,po_id',
            'status'   => 'nullable',
            'label'   => 'nullable',
        ]);

        $date = date('Ymd');
        $or_id = 'OR-' . $date . '-' . $this->randomBase36String(5);

        $po = OrderReceipt::create([
            'feedback'        => $request->feedback,
            'po_id'           => $request->po_id,
            'status'          => $request->status,
            'label'           => $request->label,
            'or_id'           => $or_id,
            'received_at'     => Carbon::now()
        ]);

        $po->create();
        return back()->with('success', 'Order has been set received successfully');
    }



    
}
