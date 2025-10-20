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

            $receipt   = Receipts::where('receipt_id', $receipt_id)->first();


            return view('receipts.receipt', [
                'user'       => $user,
                'receipt'   => $receipt,

            ]);
        }

public function receiptAction(Request $request, $receipt_id)
{
    $request->validate([
        'order_id' => 'required|exists:orders,order_id',
        'amount'   => 'required|numeric|min:1',
        'status'   => 'required|in:Verified,Rejected', 
        'remarks'  => 'nullable|string|max:200',
    ]);

    try {
        DB::beginTransaction();

        $receipt = Receipts::where('receipt_id', $receipt_id)->firstOrFail();

        $receipt->update([
            'total_amount' => $request->amount,
            'status'       => $request->status,
        ]);

        $updatedAmount = $receipt->total_amount;

        $date = now()->format('Ymd');
        $log_id = 'LOG-' . $date . '-' . $this->randomBase36String(5);
        $history_id = 'OH-' . $date . '-' . $this->randomBase36String(5);
        $user_id = Auth::user()->user_id;




        Logs::create([
            'user_id'     => $user_id,
            'action'      => "($request->status) receipt",
            'log_id'      => $log_id,
            'description' => "Staff ($user_id), ($request->status) receipt '{$receipt_id}' with amount {$updatedAmount}",
            'entity'      => 'Receipts',
            'entity_id'   => $receipt->id,
        ]);

        OrderHistory::create([
            'action_by'  => $user_id,
            'order_id'   => $request->order_id,
            'action_at'  => now(),
            'history_id' => $history_id,
            'label'      => 'Receipt',
            'amount'     => $request->amount,
            'status'     => $request->status,
            'remarks'    => $request->remarks,
        ]);

        DB::commit();

        return redirect()->back()->with(
            'success',
            "Receipt {$receipt->receipt_id} updated successfully. Amount paid: {$updatedAmount}"
        );
    } catch (\Exception $e) {
        DB::rollBack();

        \Log::error('Receipt update failed:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'request_data' => $request->all(),
        ]);

        return redirect()->back()->with('error', 'Failed to update receipt: '.$e->getMessage());
    }
}

  

}