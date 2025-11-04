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
use App\Models\ProductSetting;
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

                $order = Orders::with(['customer.delivery', 'items'])
                    ->where('order_id', $request->order_id)
                    ->firstOrFail();

                $customerDelivery = $order->customer->delivery;

                if (!$customerDelivery) {
                    throw new \Exception('Customer delivery requirements are missing.');
                }

                $rawDays = $customerDelivery->delivery_days ?? '';
                
                if (is_array($rawDays)) {
                    $deliveryDays = $rawDays;
                } elseif (is_string($rawDays) && !empty($rawDays)) {
                    if (strpos($rawDays, ',') !== false) {
                        $deliveryDays = array_map('trim', explode(',', $rawDays));
                    } else {
                        $deliveryDays = json_decode($rawDays, true) ?? [$rawDays];
                    }
                } else {
                    $deliveryDays = [];
                }

                if (empty($deliveryDays)) {
                    throw new \Exception('No delivery days defined for this customer.');
                }

                $dateNow = now()->format('Ymd');

                foreach ($deliveryDays as $dayName) {
                    $dayName = trim($dayName); 

                    $deliveryId = 'DEL-' . $dateNow . '-' . strtoupper(Str::random(5));
                    $deliveryDate = Carbon::parse($dayName)->toDateString();

                    Delivery::create([
                        'delivery_id'   => $deliveryId,
                        'order_id'      => $order->order_id,
                        'customer_id'   => $order->customer_id, 
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
                            'customer_id'      => $order->customer_id,
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
                'delivery_id' => 'required|string', 
                'status' => 'required|string|in:Delivered',
                'feedback' => 'nullable|string|max:200',
                'pod_file' => 'required|file|mimes:pdf|max:2048',
                'received_kilos' => 'array',
                'received_heads' => 'array',
            ]);

            $delivery = Delivery::where('delivery_id', $request->delivery_id)->firstOrFail();
            $order = Orders::where('order_id', $request->order_id)->firstOrFail();

            // Store PDF as binary
            $pdfContent = file_get_contents($request->file('pod_file')->getRealPath());

            //  Update this delivery
            $delivery->update([
                'status' => $request->status,
                'feedback' => $request->feedback,
                'delivered_at' => now(),
                'pod_file' => $pdfContent,
                'pod_mime' => 'application/pdf',
            ]);

            //  Update all delivery items for this delivery
            $receivedKilos = $request->input('received_kilos', []);
            $receivedHeads = $request->input('received_heads', []);
            $allItemIds = array_unique(array_merge(array_keys($receivedKilos), array_keys($receivedHeads)));

            foreach ($allItemIds as $deliveryItemId) {
                $deliveryItem = DeliveryItems::where('delivery_item_id', $deliveryItemId)->first();

                if ($deliveryItem) {
                    $updateData = ['status' => $request->status];

                    if (isset($receivedKilos[$deliveryItemId])) {
                        $plannedKilos = $deliveryItem->planned_kilos ?? $deliveryItem->placed_kilos ?? 0;
                        $updateData['received_kilos'] = $receivedKilos[$deliveryItemId];
                        $updateData['variance_kilos'] = $receivedKilos[$deliveryItemId] - $plannedKilos;
                    }

                    if (isset($receivedHeads[$deliveryItemId])) {
                        $plannedHeads = $deliveryItem->planned_heads ?? $deliveryItem->placed_heads ?? 0;
                        $updateData['received_heads'] = $receivedHeads[$deliveryItemId];
                        $updateData['variance_heads'] = $receivedHeads[$deliveryItemId] - $plannedHeads;
                    }

                    $deliveryItem->update($updateData);
                }
            }

            //  Check if ALL deliveries for this order are now "Delivered"
            $totalDeliveries = Delivery::where('order_id', $order->order_id)->count();
            $deliveredCount = Delivery::where('order_id', $order->order_id)
                                    ->where('status', 'Delivered')
                                    ->count();

            if ($totalDeliveries > 0 && $totalDeliveries === $deliveredCount) {
                $order->update(['status' => 'Completed']);
            }
            // after updating delivery items above

            // Get all DELIVERED items for this delivery
            $items = DeliveryItems::where('delivery_id', $delivery->delivery_id)
                ->where('status', 'Delivered')
                ->with('productSetting')
                ->orderBy('created_at', 'asc')
                ->get();

            $receivedTotal = 0;

            foreach ($items as $item) {
                if ($item->productSetting) {
                    $lineTotal = $item->received_kilos * $item->productSetting->nego_price;
                    $receivedTotal += $lineTotal;
                }
            }

            $date  = date('Ymd');

            $history_id = 'OH-' . $date . '-' . $this->randomBase36String(5);

            OrderHistory::create([
                'action_by' => Auth::user()->user_id,
                'order_id' => $delivery->order_id,
                'action_at' => now(),
                'history_id' => $history_id,
                'label' => 'Order',
                'amount' => $receivedTotal,
                'status' => $request->status,
            ]); 
                        

            return back()->with('success', 'Delivery successfully confirmed with variance recorded.');
        }

        public function deliveryView($delivery_id, Request $request)
        {
            $user = Auth::user();
            $delivery = Delivery::where('delivery_id', $delivery_id)->first();

            // Always show ALL delivery items in the view
            $items = DeliveryItems::where('delivery_id', $delivery_id)
                ->with('productSetting')
                ->orderBy('created_at', 'asc')
                ->get();

            // Only count DELIVERED items for total
            $deliveredItems = DeliveryItems::where('delivery_id', $delivery_id)
                ->where('status', 'Delivered')
                ->with('productSetting')
                ->get();

            $receivedTotal = 0;
            foreach ($deliveredItems as $item) {

                $productSetting = ProductSetting::where('product_id', $item->product_id)
                    ->where('customer_id', $item->customer_id)
                    ->first();

                if ($productSetting) {
                    $receivedTotal += $item->received_kilos * $productSetting->nego_price;
                }
            }


            $receivedTotal = 0;

            foreach ($items as $item) {

                // Fetch negotiated price by CUSTOMER + PRODUCT
                $productSetting = ProductSetting::where('product_id', $item->product_id)
                    ->where('customer_id', $item->customer_id)
                    ->first();

                if ($productSetting) {
                    // Received value = received kilos × negotiated unit price
                    $lineTotal = $item->received_kilos * $productSetting->nego_price;
                    $receivedTotal += $lineTotal;
                }
            }

            return view('deliveries.items', [
                'user' => $user,
                'items' => $items,
                'delivery' => $delivery,
                'receivedTotal' => $receivedTotal
            ]);
        }





        public function deliveryList(Request $request)
        {
            $today = Carbon::today();

            $deliveries = Delivery::with('requirement')
                ->get()
                ->sortBy(function ($delivery) use ($today) {
                    $date = Carbon::parse($delivery->delivery_date);

                    // Determine priority weight
                    if ($delivery->status === 'Delivered') {
                        $priority = 4; // Delivered last
                    } elseif ($date->isToday()) {
                        $priority = 1; // Delivery today first
                    } elseif ($date->isFuture()) {
                        $priority = 2; // Upcoming second
                    } elseif ($date->isPast() && $delivery->status !== 'Delivered') {
                        $priority = 3; // Late third
                    } else {
                        $priority = 5;
                    }

                    return [
                        $priority,
                        $date,
                        $delivery->requirement->receiving_time ?? '00:00:00'
                    ];
                })
                ->values();

            return view('delivery.list', compact('deliveries'));
        }







}

