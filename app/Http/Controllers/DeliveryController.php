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

class DeliveryController extends Controller
{


    public function orderProcess(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,order_id',
            'items' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            $order = Orders::with(['supplier.delivery', 'items'])->where('order_id', $request->order_id)->firstOrFail();
            $supplierDelivery = $order->supplier->delivery;

            if (!$supplierDelivery) {
                throw new \Exception('Supplier delivery requirements are missing.');
            }

            $deliveryDays = $supplierDelivery->delivery_days ?? [];
            $frequency = count($deliveryDays);

            if ($frequency === 0) {
                throw new \Exception('No delivery days defined for this supplier.');
            }

            $dateNow = now()->format('Ymd');

            // 🔹 Create a delivery for each day
            foreach ($deliveryDays as $dayName) {
                $deliveryDate = Carbon::parse("next $dayName");

                $deliveryId = 'DEL-' . $dateNow . '-' . strtoupper(Str::random(5));

                $delivery = Delivery::create([
                    'delivery_id'         => $deliveryId,
                    'order_id'            => $order->order_id,
                    'supplier_id'         => $order->supplier_id,
                    'delivery_date'       => $deliveryDate,
                    'status'              => 'Scheduled',
                    'notes'               => "Auto-generated for {$dayName} delivery",
                    'ppe_requirements'    => $supplierDelivery->ppe_requirements,
                    'delivery_frequency'  => $supplierDelivery->delivery_frequency,
                    'deliveries_per_week' => $supplierDelivery->deliveries_per_week,
                    'delivery_address_1'  => $supplierDelivery->delivery_address_1,
                    'delivery_address_2'  => $supplierDelivery->delivery_address_2,
                    'delivery_address_3'  => $supplierDelivery->delivery_address_3,
                    'receiving_time'      => $supplierDelivery->receiving_time,
                    'delivery_instructions' => $supplierDelivery->delivery_instructions,
                ]);

                // 🔹 For each order item, get the user-input per-day distribution
                foreach ($order->items as $item) {
                    $inputValue = $request->input("items.{$item->id}.{$dayName}");
                    $quantity = $inputValue ? floatval($inputValue) : 0;

                    if ($quantity <= 0) continue;

                    $deliveryItemId = 'DELI-' . $dateNow . '-' . strtoupper(Str::random(5));

                    DeliveryItems::create([
                        'delivery_item_id' => $deliveryItemId,
                        'delivery_id'      => $deliveryId,
                        'order_item_id'    => $item->order_item_id,
                        'product_id'       => $item->product_id,
                        'set_id'           => $item->set_id,
                        'planned_heads'    => $item->product->measurement_type === 'Heads' ? $quantity : null,
                        'planned_kilos'    => $item->product->measurement_type === 'Kilos' ? $quantity : null,
                        'received_heads'   => 0,
                        'received_kilos'   => 0,
                        'status'           => 'Pending',
                    ]);
                }
            }

            // Update order status
            $order->update(['status' => 'Processed']);

            DB::commit();

            return back()->with('success', 'Deliveries successfully scheduled for this order.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error processing order: ' . $e->getMessage());
            return back()->with('error', 'Failed to process order: ' . $e->getMessage());
        }
    }
}

