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


class PaymentsController extends Controller
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
    public function create( Request $request)
    {
        \Log::info('Uploading receipt file - Request Data:', $request->all());

        try {
            $request->validate([
                'delivery_id' => 'required|string|exists:delivery_requests,delivery_id',
                'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);
            $user = Auth::user();
            $customer=Customers::where('user_id', $user->user_id)->firstOrFail();

            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Uploading receipt submission failed:', $e->errors());
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $imageBlob = file_get_contents($request->file('image')->getRealPath());

            // Generate receipt id
            $date = date('Ymd');
            $del = DeliveryRequest::where('delivery_id', $request->delivery_id)->first();

            $payment_id = 'PAY-' . $date . '-' . $this->randomBase36String(5);
            $history_id = 'PH-' . $date . '-' . $this->randomBase36String(5);

            $receipt = Payments::create([
                'payment_id'  => $payment_id,
                'po_id'       => $del->po_id,
                'delivery_id' => $request->delivery_id,
                'customer_id' => $customer->customer_id,
                'status'      => "Pending",
                'label'       => "Payment",
                'image'       => $imageBlob,
            ]);


            PurchaseHistory::create([
                'po_id' =>  $del->po_id,
                'customer_id' => $customer->customer_id,
                'purchase_id' => $history_id,
                'payment_id' => $receipt->payment_id,
                'delivery_id' => $request->delivery_id,
                'label' => "Payment",
                'amount' => "0",
                'status' => "Pending"
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Receipt has been uploaded successfully! Receipt ID: ' . $receipt->payment_id);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Receipt upload failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);

            return redirect()->back()
                ->with('error', 'Receipt upload failed: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function list(Request $request)
    {
        $user = Auth::user();
        $customer = null;

        if ($user->role === "Customer") {        
            $customer = Customers::where('user_id',  $user->user_id)->firstOrFail();
            $payments = Payments::where('customer_id', $customer->customer_id)->get();
        } else {
            $payments = Payments::orderBy('updated_at', 'desc')->get();
        }

        return view('franken.pym.list', compact(
            'user',
            'customer',
            'payments',
        ));
    }
    public function payment($payment_id, Request $request)
    {
        $user = Auth::user();
        $customer = null;

        if ($user->role === "Customer") {        
            $payment = Payments::where('payment_id', $payment_id)->firstOrFail();
        } else {
            $payment = Payments::where('payment_id', $payment_id)->firstOrFail();
        }

        return view('franken.pym.payment', compact(
            'user',
            'payment',
        ));
    }

    public function collection($po_id, Request $request)
    {
        $user = Auth::user();
        $customer = null;
        $payments = Payments::where('po_id', $po_id)
            ->where('status', 'Verified')
            ->orderBy('delivery_id')
            ->orderByDesc('updated_at')
            ->get()
            ->groupBy('delivery_id');


        $purchase = PurchaseRequest::where('po_id', $po_id)->firstOrFail();

        return view('franken.pym.collection', compact(
            'payments',
            'purchase',

        ));
    }
public function verify(Request $request)
{
    $request->validate([
        'payment_id'   => 'required|exists:payments,payment_id',
        'status'       => 'required|in:Verified,Rejected',
        'total_amount' => 'required|numeric',
    ]);

    try {
        $payment = Payments::where('payment_id', $request->payment_id)->firstOrFail();
        $delivery = DeliveryRequest::where('delivery_id', $payment->delivery_id)->firstOrFail();

        // ✅ Update the payment record
        $payment->update([
            'status'       => $request->status,
            'action_by'    => Auth::user()->user_id,
            'action_at'    => now(),
            'total_amount' => $request->total_amount,
        ]);

        // ✅ Recalculate total paid for this delivery (AFTER updating the payment)
        $totalBalance = $delivery->items->sum('balance'); // total delivery cost
        $totalPaid = Payments::where('delivery_id', $delivery->delivery_id)
            ->where('status', 'Verified')
            ->sum('total_amount');

        // ✅ Determine correct payment status
        if ($totalBalance > 0) {
            if (bccomp($totalPaid, $totalBalance, 2) >= 0) {
                $newStatus = 'Fully paid';
            } elseif (bccomp($totalPaid, '0', 2) > 0) {
                $newStatus = 'Partially paid';
            } else {
                $newStatus = 'Unpaid';
            }
        } else {
            $newStatus = 'Unpaid'; // or handle zero balance case as needed
        }

        // ✅ Update delivery payment status only if changed
        if ($delivery->payment_status !== $newStatus) {
            $delivery->update([
                'payment_status' => $newStatus,
                'updated_at'     => now(),
            ]);
        }

        // ✅ Log to PurchaseHistory only if payment was verified
        if ($request->status === 'Verified') {
            $date = date('Ymd');
            $history_id = 'PH-' . $date . '-' . $this->randomBase36String(5);

            PurchaseHistory::create([
                'po_id'        => $payment->po_id,
                'customer_id'  => $payment->customer_id,
                'purchase_id'  => $history_id,
                'payment_id'   => $payment->payment_id,
                'delivery_id'  => $payment->delivery_id,
                'label'        => 'Payment',
                'amount'       => $request->total_amount,
                'status'       => 'Successful',
            ]);
        }

        return redirect()->back()->with('success', 'Receipt updated successfully!');
    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Failed to update receipt. Please try again.')
            ->withInput();
    }
}





}
