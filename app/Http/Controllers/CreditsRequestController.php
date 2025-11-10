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
        $customer = Customers::where('user_id', $user->user_id)->firstOrFail();
        $credit = Credits::where('user_id', $user->user_id)->firstOrFail();
        $customerId = $customer->customer_id;


        // Total balance from delivered deliveries
        $balance = DeliveryItemRequest::join('delivery_requests', 'delivery_item_requests.delivery_id', '=', 'delivery_requests.delivery_id')
            ->where('delivery_requests.customer_id', $customerId)
            ->where('delivery_requests.status', 'Delivered')
            ->sum('delivery_item_requests.balance');

        // Already paid (verified payments)
        $alreadyPaid = Payments::where('customer_id', $customerId)
            ->where('status', 'Verified')
            ->sum('total_amount');

        // Current balance
        $currentBalance = $balance - $alreadyPaid;

        // --- TRANSACTION HISTORY ---
        $transactions = PurchaseHistory::where('customer_id', $customerId)
            ->orderBy('updated_at', 'desc')
            ->get();

        // Purchases that still have remaining balance
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

            $purchaseRequests = PurchaseRequest::where('user_id', $customer->user_id)->get();
            $deliveries = DeliveryItemRequest::with('deliveryRequest')
                ->whereIn('po_id', $purchaseRequests->pluck('po_id'))
                ->get();
            $payments = Payments::where('customer_id', $customer->customer_id)
                ->where('status', 'Verified')
                ->get();

            $purchaseData = $purchaseRequests->map(function ($po) use ($deliveries, $payments) {
                $deliveredBalance = $deliveries
                    ->filter(fn ($d) => $d->po_id === $po->po_id && $d->deliveryRequest->status === "Delivered")
                    ->sum('balance');

                $paidAmount = $payments
                    ->where('po_id', $po->po_id)
                    ->sum('total_amount');

                return [
                    'purchase_request'   => $po,                     
                    'po_id'              => $po->po_id,
                    'delivered_balance'  => $deliveredBalance,
                    'paid_amount'        => $paidAmount,
                    'remaining_balance'  => $deliveredBalance - $paidAmount,
                ];
            });

        return view('franken.crd.list', compact(
            'user',
            'customer',
            'credit',
            'balance',
            'alreadyPaid',
            'currentBalance',
            'transactions',
            'purchasesWithBalance',
            'purchaseData'

        ));
    }




}
    

