<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Orders;
use App\Models\Delivery;
use App\Models\DeliveryItems;
use App\Models\Logs;
use Illuminate\Support\Facades\Auth;
use App\Models\OrderHistory;

class DeliveryController extends Controller
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
        public function orderProcess(Request $request)
        {
            $user = Auth()->user();

            $request->validate([
                'order_id' => 'required|exists:orders,order_id',
                'items'    => 'required|array',
            ]);

            try {
                DB::beginTransaction();

                $order = Orders::with(['supplier.delivery', 'items'])
                    ->where('order_id', $request->order_id)
                    ->firstOrFail();

                $supplierDelivery = $order->supplier->delivery;

                if (!$supplierDelivery) {
                    throw new \Exception('Supplier delivery requirements are missing.');
                }

                $deliveryDays = $supplierDelivery->delivery_days ?? [];
                if (empty($deliveryDays)) {
                    throw new \Exception('No delivery days defined for this supplier.');
                }

                $dateNow = now()->format('Ymd');

                foreach ($deliveryDays as $dayName) {

                    $deliveryId = 'DEL-' . $dateNow . '-' . strtoupper(Str::random(5));
                    $deliveryDate = \Carbon\Carbon::parse($dayName)->toDateString();

                    Delivery::create([
                        'delivery_id'   => $deliveryId,
                        'order_id'      => $order->order_id,
                        'supplier_id'   => $order->supplier_id, 
                        'delivery_date' => $deliveryDate,
                        'status'        => 'Scheduled',
                    ]);


                    foreach ($order->items as $item) {

                        $inputHeads = $request->input("items.{$item->id}.{$dayName}.heads");
                        $inputKilos = $request->input("items.{$item->id}.{$dayName}.kilos");

                        $plannedHeads = 0;
                        $plannedKilos = 0;

                        if ($item->product->measurement_type === 'Heads') {
                            $plannedHeads = floatval($inputHeads ?? 0);
                        } elseif ($item->product->measurement_type === 'Kilos') {
                            $plannedKilos = floatval($inputKilos ?? 0);
                        } elseif ($item->product->measurement_type === 'Heads&Kilos') {
                            $plannedHeads = floatval($inputHeads ?? 0);
                            $plannedKilos = floatval($inputKilos ?? 0);
                        }

                        // Skip if both are zero
                        if ($plannedHeads <= 0 && $plannedKilos <= 0) {
                            continue;
                        }

                        $deliveryItemId = 'DELI-' . $dateNow . '-' . strtoupper(Str::random(5));

                        DeliveryItems::create([
                            'delivery_item_id' => $deliveryItemId,
                            'delivery_id'      => $deliveryId,
                            'order_item_id'    => $item->order_item_id,
                            'product_id'       => $item->product_id,
                            'set_id'           => $item->set_id,
                            'planned_heads'    => $plannedHeads,
                            'planned_kilos'    => $plannedKilos,
                            'received_heads'   => 0,
                            'received_kilos'   => 0,
                            'status'           => 'Pending',
                        ]);
                    }
                }

                $order->update(['status' => 'Processed']);

                DB::commit();

                return back()->with('success', 'Deliveries successfully scheduled for this order.');
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Error processing order: ' . $e->getMessage());
                return back()->with('error', 'Failed to process order: ' . $e->getMessage());
            }
        }

        public function confirmDelivery(Request $request)
        {
            $request->validate([
                'order_id' => 'required|string',
                'status' => 'required|string|in:Delivered',
                'feedback' => 'nullable|string|max:200',
                'pod_file' => 'required|file|mimes:pdf|max:2048',
                'received_kilos' => 'array',
                'received_heads' => 'array',
            ]);

            $delivery = Delivery::where('order_id', $request->order_id)->firstOrFail();

            $pdfContent = file_get_contents($request->file('pod_file')->getRealPath());

            $delivery->update([
                'status' => $request->status,
                'feedback' => $request->feedback,
                'delivered_at' => now(),
                'pod_file' => $pdfContent,
                'pod_mime' => 'application/pdf',
            ]);

            // Get all unique delivery item IDs from both arrays
            $receivedKilos = $request->input('received_kilos', []);
            $receivedHeads = $request->input('received_heads', []);
            $allItemIds = array_unique(array_merge(array_keys($receivedKilos), array_keys($receivedHeads)));

            // Process all items in a single loop
            foreach ($allItemIds as $deliveryItemId) {
                $deliveryItem = DeliveryItems::where('delivery_item_id', $deliveryItemId)->first();
                
                if ($deliveryItem) {
                    $updateData = ['status' => $request->status];

                    // Process kilos if provided
                    if (isset($receivedKilos[$deliveryItemId])) {
                        $plannedKilos = $deliveryItem->planned_kilos ?? $deliveryItem->placed_kilos ?? 0;
                        $updateData['received_kilos'] = $receivedKilos[$deliveryItemId];
                        $updateData['variance_kilos'] = $receivedKilos[$deliveryItemId] - $plannedKilos;
                    }

                    // Process heads if provided
                    if (isset($receivedHeads[$deliveryItemId])) {
                        $plannedHeads = $deliveryItem->planned_heads ?? $deliveryItem->placed_heads ?? 0;
                        $updateData['received_heads'] = $receivedHeads[$deliveryItemId];
                        $updateData['variance_heads'] = $receivedHeads[$deliveryItemId] - $plannedHeads;
                    }

                    $deliveryItem->update($updateData);
                }
            }

            return back()->with('success', 'Delivery successfully confirmed with variance recorded.');
        }
        public function deliveryView($delivery_id, Request $request)
        {
            $user = Auth::user();
            $delivery = Delivery::where('delivery_id', $delivery_id)->first();
            $items = DeliveryItems::where('delivery_id', $delivery_id) 
                ->orderBy('created_at', 'asc')
                ->get();

            return view('deliveries.items', [
                'user' => $user,
                'items' => $items,
                'delivery' => $delivery,

            ]);
        }




}

