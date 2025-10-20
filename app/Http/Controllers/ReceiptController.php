<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Receipts;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Suppliers;
use App\Models\Logs;
use App\Models\OrderHistory;


class ReceiptController extends Controller
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

        public function receiptUpload(Request $request)
        {
            \Log::info('Uploading receipt file - Request Data:', $request->all());

            try {
                $request->validate([
                    'order_id' => 'required|exists:orders,order_id',
                    'supplier_id' => 'required|exists:suppliers,supplier_id',
                    'status' => 'required|in:Pending,Verified,Denied',
                    'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                \Log::error('Uploading receipt submission failed:', $e->errors());
                return redirect()->back()
                    ->withErrors($e->validator)
                    ->withInput();
            }

            try {
                DB::beginTransaction();

                $imageBlob = null;
                $imageMimeType = null;
                $imageFilename = null;
                $imageSize = null;

                if ($request->hasFile('image')) {
                    $image = $request->file('image');

                    $imageBlob = file_get_contents($image->getRealPath());
                    $imageMimeType = $image->getMimeType();
                    $imageFilename = $image->getClientOriginalName();
                    $imageSize = $image->getSize();

                    \Log::info('Uploaded file details:', [
                        'name' => $imageFilename,
                        'mime' => $imageMimeType,
                        'size' => $imageSize,
                        'path' => $image->getRealPath(),
                    ]);
                }

                // Generate receipt id
                $date = date('Ymd');
                $receipt_id = 'RCPT-' . $date . '-' . $this->randomBase36String(5);

                $receipt = Receipts::create([
                    'receipt_id' => $receipt_id,
                    'order_id' => $request->order_id,
                    'supplier_id' => $request->supplier_id,
                    'status' => $request->status, // use status from request
                    'image' => $imageBlob,
                    'image_mime_type' => $imageMimeType,
                    'image_filename' => $imageFilename,
                    'image_size' => $imageSize,
                ]);

                DB::commit();

                return redirect()->back()->with(
                    'success',
                    'Receipt has been uploaded successfully! Receipt ID: ' . $receipt_id
                );

            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Receipt upload failed:', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'request_data' => $request->all(),
                ]);

                return redirect()->back()
                    ->with('error', 'Receipt upload failed: ' . $e->getMessage() . 
                        '. Please check the logs for more details.')
                    ->withInput();
            }
        }

        public function receiptList(Request $request)
        {
            $user = Auth::user();

            if ($user->role !== "Supplier") {
                $receipts = Receipts::all();
            } 
            elseif ($user->role === "Supplier") {
                $supplier = Suppliers::where('user_id', $user->user_id)->first();
                $receipts = Receipts::where('supplier_id', $supplier->supplier_id)->get();
    


            }
            return view('receipts.list', [
                'user' => $user,
                'receipts' => $receipts,


            ]);
        }

        public function receiptView($receipt_id, Request $request)
        {
            $user = Auth::user();
            $receipt = Receipts::where('receipt_id', $receipt_id)->firstOrFail();

            // Calculate remaining amount for this order
            $order = $receipt->order;
            
            // Sum all verified receipts for this order
            $totalPaid = Receipts::where('order_id', $receipt->order_id)
                ->where('status', 'Verified')
                ->sum('total_amount');
            
            $remainingAmount = $order->total_amount - $totalPaid;

            return view('receipts.receipt', [
                'user'            => $user,
                'receipt'         => $receipt,
                'remainingAmount' => $remainingAmount,
                'totalPaid'       => $totalPaid,
            ]);
        }

public function receiptAction(Request $request, $receipt_id)
{
    // Conditional validation based on status
    $rules = [
        'order_id' => 'required|exists:orders,order_id',
        'status'   => 'required|in:Verified,Rejected',
    ];

    if ($request->status === 'Verified') {
        $rules['amount'] = [
            'required',
            'numeric',
            'min:0.01',
            function ($attribute, $value, $fail) use ($request) {
                $order = Order::where('order_id', $request->order_id)->first();
                
                if (!$order) {
                    $fail('Order not found.');
                    return;
                }
                
                // Calculate total already paid from verified receipts
                $totalPaid = Receipts::where('order_id', $request->order_id)
                    ->where('status', 'Verified')
                    ->sum('total_amount');
                
                $remainingAmount = $order->total_amount - $totalPaid;
                
                if ($value > $remainingAmount) {
                    $fail('The amount cannot exceed the remaining balance of ₱' . number_format($remainingAmount, 2) . 
                          ' (Order total: ₱' . number_format($order->total_amount, 2) . 
                          ', Already paid: ₱' . number_format($totalPaid, 2) . ')');
                }
            }
        ];
        $rules['remarks'] = 'nullable|string|max:200';
    } elseif ($request->status === 'Rejected') {
        $rules['reason'] = 'required|string|max:200';
    }

    $request->validate($rules);

    try {
        DB::beginTransaction();

        $receipt = Receipts::where('receipt_id', $receipt_id)->firstOrFail();

        // Check if receipt is already processed
        if (in_array($receipt->status, ['Verified', 'Rejected'])) {
            return redirect()->back()->with('error', 'This receipt has already been processed.');
        }

        // For verification, check if order is already fully paid
        if ($request->status === 'Verified') {
            $order = Order::where('order_id', $request->order_id)->first();
            $totalPaid = Receipts::where('order_id', $request->order_id)
                ->where('status', 'Verified')
                ->sum('total_amount');
            
            $remainingAmount = $order->total_amount - $totalPaid;
            
            if ($remainingAmount <= 0) {
                return redirect()->back()->with('error', 'This order has already been fully paid.');
            }
            
            if ($request->amount > $remainingAmount) {
                return redirect()->back()->with('error', 
                    'Amount exceeds remaining balance of ₱' . number_format($remainingAmount, 2));
            }
        }

        // Update receipt based on status
        $updateData = ['status' => $request->status];
        
        if ($request->status === 'Verified') {
            $updateData['total_amount'] = $request->amount;
        }

        $receipt->update($updateData);

        $date = now()->format('Ymd');
        $log_id = 'LOG-' . $date . '-' . $this->randomBase36String(5);
        $history_id = 'OH-' . $date . '-' . $this->randomBase36String(5);
        $user_id = Auth::user()->user_id;

        // Create log entry
        $logDescription = $request->status === 'Verified'
            ? "Staff ($user_id) verified receipt '{$receipt_id}' with amount ₱" . number_format($request->amount, 2)
            : "Staff ($user_id) rejected receipt '{$receipt_id}'. Reason: {$request->reason}";

        Logs::create([
            'user_id'     => $user_id,
            'action'      => "{$request->status} receipt",
            'log_id'      => $log_id,
            'description' => $logDescription,
            'entity'      => 'Receipts',
            'entity_id'   => $receipt->id,
        ]);

        // Create order history entry
        OrderHistory::create([
            'action_by'  => $user_id,
            'order_id'   => $request->order_id,
            'action_at'  => now(),
            'history_id' => $history_id,
            'label'      => 'Receipt',
            'amount'     => $request->status === 'Verified' ? $request->amount : null,
            'status'     => $request->status,
            'remarks'    => $request->status === 'Verified' 
                ? $request->remarks 
                : $request->reason,
        ]);

        DB::commit();

        $successMessage = $request->status === 'Verified'
            ? "Receipt {$receipt->receipt_id} verified successfully. Amount paid: ₱" . number_format($request->amount, 2)
            : "Receipt {$receipt->receipt_id} has been rejected.";

        return redirect()->back()->with('success', $successMessage);

    } catch (\Exception $e) {
        DB::rollBack();

        \Log::error('Receipt update failed:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'receipt_id' => $receipt_id,
            'request_data' => $request->except(['_token']),
        ]);

        return redirect()->back()
            ->with('error', 'Failed to update receipt. Please try again.')
            ->withInput();
    }
}

  

}