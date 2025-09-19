<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Orders;

class OrderController extends Controller
{
        public function orderList(Request $request)
        {
            $user = Auth::user();
           

            return view('orders.list', [
                'user' => $user,

            ]);
        }

    public function createorder(Request $request){
       
        \Log::info('Staff Registration Request Data:', $request->all());

        try {
            $request->validate([
                'supplier_id' => 'required|exists:suppliers,supplier_id',
                'status'    => 'required|string',
                'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Puchase order submission failed:', $e->errors());
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        }


        DB::beginTransaction();

        $date = date('Ymd');
        function randomBase36String(int $length): string {
            $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $str = '';
            for ($i = 0; $i < $length; $i++) {
                $str .= $chars[random_int(0, strlen($chars) - 1)];
            }
            return $str;
        }

        $order_id = 'ORDR-' . $date . '-' . randomBase36String(5);

        try {
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
            }

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                \Log::info('Uploaded file details:', [
                    'name' => $image->getClientOriginalName(),
                    'mime' => $image->getMimeType(),
                    'size' => $image->getSize(),
                    'path' => $image->getRealPath(),
                ]);
            }


            // 1. Create order
            $order = Orders::create([
                'order_id'       => $order_id,
                'supplier_id' => $request->supplier_id,
                'status' => $request->status,
                'image'         => $imageBlob,
                'image_mime_type' => $imageMimeType,
                'image_filename' => $imageFilename,
                'image_size'    => $imageSize,

            ]);

          

            DB::commit();

            return redirect()->route('orders.list')
                ->with('success', 'Purchase order has been created!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Purchase order submission error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            
            return redirect()->back()
                ->with('error', 'Purchase order creating failed: ' . $e->getMessage() . '. Please check the logs for more details.')
                ->withInput();
        }
        }

}
