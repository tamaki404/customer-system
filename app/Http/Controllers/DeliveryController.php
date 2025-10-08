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
                'items' => 'required|array',
            ]);

            try {
                DB::beginTransaction();

                $order = Orders::with(['supplier.delivery', 'items'])->where('order_id', $request->order_id)->firstOrFail();
                $supplier_id = $order->supplier_id;
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

                    foreach ($order->items as $item) {
                        $inputValue = $request->input("items.{$item->id}.{$dayName}");
                        $quantity = $inputValue ? floatval($inputValue) : 0;

                        if ($quantity <= 0) continue;

                        $deliveryItemId = 'DELI-' . $dateNow . '-' . strtoupper(Str::random(5));
                        $log_id = 'LOG-' . $dateNow . '-' . strtoupper(Str::random(5));
                        $history_id = 'OH-' . $dateNow . '-' . strtoupper(Str::random(5));

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
                        Logs::create([
                            'user_id'     => $user->user_id,
                            'action'      => 'Proccesed and save schedule for a PO',
                            'log_id'      => $log_id,
                            'description' => " Staff ($user->user_id) processed ('$order->order_id') for '($supplier_id)' ",
                            'entity'      => 'Delivery',
                            'entity_id'   => $delivery->id,
                        ]);

                        OrderHistory::create([
                            'action_by' => Auth::user()->user_id,
                            'order_id' => $request->order_id,
                            'action_at' => now(),
                            'history_id' => $history_id,
                            'label' => 'Order',
                            'amount' => $order->total_amount,
                            'status' => $delivery->status,
                        ]);
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
        'pod_file' => 'required|file|mimes:pdf|max:2048', // 2MB max
        'received_kilos' => 'array',
        'received_heads' => 'array',
    ]);

    $delivery = Delivery::where('order_id', $request->order_id)->firstOrFail();

    // 📄 Read PDF file as binary
    $pdfContent = file_get_contents($request->file('pod_file')->getRealPath());

    // 🆙 Update Delivery table
    $delivery->update([
        'status' => $request->status,
        'feedback' => $request->feedback,
        'delivered_at' => now(),
        'pod_file' => $pdfContent,   // MEDIUMBLOB column in DB
        'pod_mime' => 'application/pdf',
    ]);

    // 🐄 Update each delivery item
    foreach ($request->input('received_kilos', []) as $deliveryItemId => $kilos) {
        DeliveryItems::where('delivery_item_id', $deliveryItemId)
            ->update([
                'received_kilos' => $kilos,
                'status' => $request->status,
            ]);
    }

    foreach ($request->input('received_heads', []) as $deliveryItemId => $heads) {
        DeliveryItems::where('delivery_item_id', $deliveryItemId)
            ->update([
                'received_heads' => $heads,
                'status' => $request->status,
            ]);
    }

    return back()->with('success', 'Delivery successfully confirmed.');
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

