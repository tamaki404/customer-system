<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Receipts;
use Illuminate\Support\Facades\DB;


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

}