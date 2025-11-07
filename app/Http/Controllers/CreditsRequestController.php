<?php

namespace App\Http\Controllers;
use App\Models\Customers;
use App\Models\DeliveryRequest;
use App\Models\DeliveryItemRequest;
use App\Models\Credits;
use App\Models\Payments;
use App\Models\PurchaseHistory;
use Illuminate\Support\Facades\DB;

use App\Models\PurchaseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreditsRequestController extends Controller
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
    public function list(Request $request)
    {
        $user = Auth::user();
        $customer = Customers::where('user_id',  $user->user_id)->firstOrFail();
        $credit = Credits::where('user_id', $user->user_id)->firstOrFail();
        $customerId = $customer->customer_id;

        //Credit calculations
            // Deliveries of customer that are delivered
            $deliveries = DeliveryRequest::where('customer_id', $customerId)
                ->where('status', 'Delivered')
                ->pluck('delivery_id');

            // UsedCredit from DeliveryItemRequests
            $deliveryUsedCredit = DeliveryItemRequest::whereIn('delivery_id', $deliveries)
                ->sum('balance');
            // UsedCredit from PurchaseHistories
            $purchaseUsedCredit = PurchaseHistory::where('customer_id', $customerId)
                ->sum('amount');
            // UsedCredit from DeliveryItemRequests (remaining balance)
            $UsedCredit = DeliveryItemRequest::whereIn('delivery_id', $deliveries)
                ->sum('balance');            
            // PaidCredit from Payments
            $PaidCredit = Payments::where('customer_id', $customerId)
                ->where('status', 'Verified')
                ->sum('total_amount'); 

        //transaction history
            $transactions = PurchaseHistory::where('customer_id', $customerId)
                ->orderBy('updated_at', 'desc')
                ->get();
            $purchasesWithBalance = PurchaseRequest::select(
                    'purchase_requests.po_id',
                    DB::raw('SUM(delivery_item_requests.balance) AS total_balance')
                )
                ->join('delivery_item_requests', 'purchase_requests.po_id', '=', 'delivery_item_requests.po_id')
                ->join('delivery_requests', 'delivery_item_requests.delivery_id', '=', 'delivery_requests.delivery_id')
                ->where('purchase_requests.user_id', $user->user_id)
                ->where('delivery_requests.status', 'Delivered')
                ->groupBy('purchase_requests.po_id')
                ->having('total_balance', '>', 0)
                ->get();

            return view('franken.crd.list', compact(
                'user',
                'customer',
                'credit',
                'UsedCredit',
                'PaidCredit',
                'transactions',
                'purchasesWithBalance'
            ));
    }



}
    

