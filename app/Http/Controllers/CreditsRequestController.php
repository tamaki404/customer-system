<?php

namespace App\Http\Controllers;
use App\Models\Customers;
use App\Models\DeliveryRequest;
use App\Models\DeliveryItemRequest;
use App\Models\Credits;
use App\Models\Payments;
use App\Models\PurchaseHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\PurchaseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use finfo;
use Carbon\Carbon;

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

    public function download($payment_id)
    {
        $payment = Payments::where('payment_id', $payment_id)->firstOrFail();
        $binary = $payment->image;

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_buffer($finfo, $binary);
        finfo_close($finfo);

        $ext = explode('/', $mime)[1];
        $filename = "payment_{$payment_id}.$ext";

        return response($binary, 200)
            ->header('Content-Type', $mime)
            ->header('Content-Disposition', "attachment; filename=\"$filename\"");
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
            ->where('status' , '!=', 'Pending')
            ->orderBy('updated_at', 'desc')
            ->get();

        //due

        $now = Carbon::now();
        $endDate = $now->copy()->addDays(15);
        // Get user's purchase request PO IDs
        $poIds = PurchaseRequest::where('user_id', $customer->user_id)
            ->pluck('po_id');
        // Get delivery IDs due in next 15 days
        $deliveriesDueSoonIds = DeliveryRequest::whereIn('po_id', $poIds)
            ->whereBetween('due_date', [$now, $endDate])
            ->pluck('delivery_id');
        // Sum all balances from DeliveryItemRequest
        $totalBalance = DeliveryItemRequest::whereIn('delivery_id', $deliveriesDueSoonIds)
            ->sum('balance');
        // Sum of all verified payments for those same deliveries
        $totalPaid = Payments::whereIn('delivery_id', $deliveriesDueSoonIds)
            ->where('status', 'Verified')
            ->sum('total_amount');
        // Subtract payments from total balance to get the outstanding (due) amount
        $totalDue = $totalBalance - $totalPaid;



        // Purchases that still have remaining balance
        // $purchasesWithBalance = PurchaseRequest::select(
        //         'purchase_requests.po_id',
        //         DB::raw('SUM(delivery_item_requests.balance) AS total_balance')
        //     )
        //     ->join('delivery_item_requests', 'purchase_requests.po_id', '=', 'delivery_item_requests.po_id')
        //     ->join('delivery_requests', 'delivery_item_requests.delivery_id', '=', 'delivery_requests.delivery_id')
        //     ->where('purchase_requests.user_id', $user->user_id)
        //     ->where('delivery_requests.status', 'Delivered')
        //     ->groupBy('purchase_requests.po_id')
        //     ->having('total_balance', '>', 0)
        //     ->get();

            $deliveryWithBalance = DeliveryRequest::select(
                    'delivery_requests.delivery_id',
                    DB::raw('SUM(delivery_item_requests.balance) AS total_balance')
                )
                ->join('delivery_item_requests', 'delivery_requests.delivery_id', '=', 'delivery_item_requests.delivery_id')
                ->where('delivery_requests.customer_id', $customer->customer_id)
                ->where('delivery_requests.status', 'Delivered')
                ->where('payment_status', '!=', 'Fully paid')
                ->groupBy('delivery_requests.delivery_id')
                ->orderBy('due_date', 'desc')
                ->get();

            $purchaseRequests = PurchaseRequest::where('user_id', $customer->user_id)
                ->orderBy('updated_at', 'desc') 
                ->get();            $deliveries = DeliveryItemRequest::with('deliveryRequest')
                ->whereIn('po_id', $purchaseRequests->pluck('po_id'))
                ->get();
            $dels = DeliveryRequest::with(['items', 'payments' => function($q) {
                    $q->where('status', 'Verified');
                }])
                ->where('customer_id', $customer->customer_id)
                ->where('status', 'Delivered')
                ->orderBy('due_date', 'desc')
                ->get();

            // Prepare data with running balance
            $deliveriesWithBalance = $dels->map(function ($delivery) {
                $totalItemsBalance = $delivery->items->sum('balance'); // total balance of delivery items
                $totalPaid = $delivery->payments->sum('total_amount'); // total verified payments

                $runningBalance = $totalItemsBalance - $totalPaid;

                return [
                    'delivery_id'     => $delivery->delivery_id,
                    'total_items'     => $totalItemsBalance,
                    'total_paid'      => $totalPaid,
                    'running_balance' => $runningBalance,
                    'due_date'        => $delivery->due_date,
                ];
            });


            //get sum(balance) of delivery->items->status===Partially paid of same delivery_id and subtarct
                
            $payments = Payments::where('customer_id', $customer->customer_id)
                ->orderBy('updated_at', 'desc')
                ->get();

            $purchaseData = $purchaseRequests->map(function ($po) use ($deliveries, $payments) {

                // total delivered balance
                $deliveredBalance = $deliveries
                    ->filter(fn ($d) => $d->po_id === $po->po_id && $d->deliveryRequest->status === "Delivered")
                    ->sum('balance');

                // total paid amount
                $paidAmount = $payments
                    ->where('po_id', $po->po_id)
                    ->sum('total_amount');

                $remainingBalance = $deliveredBalance - $paidAmount;

                // find nearest unpaid delivery due_date
                $unpaidDeliveries = $deliveries
                    ->filter(fn ($d) =>
                        $d->po_id === $po->po_id &&
                        $d->deliveryRequest &&
                        $d->deliveryRequest->due_date &&
                        $d->deliveryRequest->status === "Delivered"
                    )
                    ->map(fn ($d) => $d->deliveryRequest)
                    ->unique('delivery_id');

                // Select due_dates that are not past due
                $upcomingDueDates = $unpaidDeliveries
                    ->filter(fn ($delivery) => Carbon::parse($delivery->due_date)->isFuture())
                    ->pluck('due_date')
                    ->sort()
                    ->values();


                $nearestDueDate = $upcomingDueDates->first(); // the soonest unpaid one

                return [
                    'purchase_request'   => $po,
                    'po_id'              => $po->po_id,
                    'delivered_balance'  => $deliveredBalance,
                    'paid_amount'        => $paidAmount,
                    'remaining_balance'  => $remainingBalance,
                    'nearest_due_date'   => $nearestDueDate,

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
            'deliveryWithBalance',
            'purchaseData',
            'payments',
            'totalDue',
            'dels'

        ));
    }




}
    

