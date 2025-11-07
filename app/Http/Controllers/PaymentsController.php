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
                'po_id' => 'required|string|exists:purchase_requests,po_id',
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

            $payment_id = 'PAY-' . $date . '-' . $this->randomBase36String(5);
            $history_id = 'PH-' . $date . '-' . $this->randomBase36String(5);

            $receipt = Payments::create([
                'payment_id'  => $payment_id,
                'po_id'       => $request->po_id,
                'customer_id' => $customer->customer_id,
                'delivery_id' => "0",
                'status'      => "Pending",
                'label'       => "Payment",
                'image'       => $imageBlob,
            ]);


            PurchaseHistory::create([
                'po_id' => $request->po_id,
                'customer_id' => $customer->customer_id,
                'purchase_id' => $history_id,
                'payment_id' => $receipt->payment_id,
                'delivery_id' => "0",
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
            $payments = Payments::all();
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

public function verify(Request $request)
{
    $request->validate([
        'payment_id' => 'required|exists:payments,payment_id',
        'status'   => 'required|in:Verified,Rejected',
        'total_amount'   => 'required|numeric',
    ]);

    try {
        $payment = Payments::where('payment_id', $request->payment_id)->firstOrFail();

        $payment->update([
            'status'     => $request->status,
            'action_by'  => Auth::user()->user_id,
            'action_at'  => now(),
            'total_amount' => $request->total_amount,
        ]);

        $date = date('Ymd');
        $history_id = 'PH-' . $date . '-' . $this->randomBase36String(5);

        PurchaseHistory::create([
            'po_id' => $payment->po_id,
            'customer_id' => $payment->customer_id,
            'purchase_id' => $history_id,
            'payment_id' => $payment->payment_id,
            'delivery_id' => "0",
            'label' => "Payment",
            'amount' => $request->total_amount,
            'status' => "Successful"
        ]);

        return redirect()->back()
            ->with('success', 'Receipt updated successfully!');

    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Failed to update receipt. Please try again.')
            ->withInput();
    }
}


}
