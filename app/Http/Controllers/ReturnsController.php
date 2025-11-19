<?php

namespace App\Http\Controllers;
use App\Models\Customers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

use App\Models\PurchaseRequest;
use App\Models\DeliveryItemRequest;
use App\Models\DeliveryRequest;
use App\Models\Logs;

class ReturnsController extends Controller
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
    public function schedule(Request $request)
    {
        // Debug: incoming request
        \Log::info('Schedule Request Received:', $request->all());

        $request->validate([
            'po_id' => 'required|exists:purchase_requests,po_id',
            'delivery_date' => 'required|date',
            'items' => 'required|array',
        ]);

        DB::beginTransaction();

        try {
            $user = Auth::user();
            $date = date('Ymd');

            // Fetch PurchaseRequest
            $po = PurchaseRequest::where('po_id', $request->po_id)->firstOrFail();
            $customer = Customers::where('user_id', $po->user_id)->first();

            // Generate Delivery ID
            $deliveryId = 'DLV-' . $date . '-' . $this->randomBase36String(5);

            // Create DeliveryRequest
            $delivery = DeliveryRequest::create([
                'po_id'         => $po->po_id,
                'delivery_id'   => $deliveryId,
                'customer_id'   => $customer->customer_id,
                'delivery_date' => $request->delivery_date,
                'label'         => 'Return',
                'status'        => 'Scheduled',
                'action_by'     => $user->user_id,
                'action_at'     => now(),
            ]);

            \Log::info('DeliveryRequest Created:', $delivery->toArray());

            // Loop through each submitted product
            foreach ($request->items as $product_id => $data) {

                $itemId = 'ITEM-' . $date . '-' . $this->randomBase36String(5);

                \Log::info('Processing Product Item:', [
                    'product_id' => $product_id,
                    'item_id'    => $itemId,
                    'data'       => $data
                ]);

                // Create DeliveryItemRequest
                DeliveryItemRequest::create([
                    'delivery_id'       => $deliveryId,
                    'delivery_item_id'  => $itemId,
                    'po_id'             => $po->po_id,
                    'customer_id'       => $customer->customer_id,
                    'product_id'        => $product_id,
                    'set_id'            => $data['set_id'],
                    'planned_heads'     => $data['planned_heads'] ?? 0,
                    'planned_kilos'     => $data['planned_kilos'] ?? 0,
                    'balance'           => '0'
                ]);
            }

            DB::commit();

            \Log::info('Schedule Completed Successfully:', [
                'delivery_id' => $deliveryId,
                'po_id'       => $po->po_id
            ]);

            return redirect()->back()->with('success', 'Delivery scheduled successfully!');

        } catch (\Exception $e) {

            DB::rollBack();

            \Log::error('Schedule Error:', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Error scheduling delivery.');
        }
    }




    public function list(Request $request)
    {
        $user = Auth::user();

        $varianceByDelivery = DeliveryItemRequest::select(
                'delivery_item_requests.delivery_id',
                DB::raw('SUM(planned_heads - received_heads) AS heads_variance'),
                DB::raw('SUM(planned_kilos - received_kilos) AS kilos_variance')
            )
            ->join('purchase_requests', 'purchase_requests.po_id', '=', 'delivery_item_requests.po_id')
            ->where('purchase_requests.status', 'Completed')
            ->whereRaw('planned_heads != received_heads OR planned_kilos != received_kilos')
            ->groupBy('delivery_item_requests.delivery_id')
            ->with(['varianceItems.product'])
            ->orderBy('delivery_item_requests.updated_at', 'desc')
            ->paginate(25);

        return view('franken.rtn.list', compact(
            'user',
            'varianceByDelivery',
        ));
    }

    private function generatePoId(): string
    {
        try {
            $prefix = 'PO';
            $date = date('Ymd');
            
            // Get the last PO ID for today
            $lastPo = PurchaseRequest::where('po_id', 'LIKE', $prefix . $date . '%')
                ->orderBy('po_id', 'desc')
                ->first();
            
            if ($lastPo) {
                $lastNumber = intval(substr($lastPo->po_id, -4));
                $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
                
                Log::debug('Generated PO ID from last PO', [
                    'last_po_id' => $lastPo->po_id,
                    'new_number' => $newNumber
                ]);
            } else {
                $newNumber = '0001';
                
                Log::debug('Generated first PO ID for today', [
                    'date' => $date
                ]);
            }
            
            $poId = $prefix . $date . $newNumber;
            
            Log::info('PO ID generated', ['po_id' => $poId]);
            
            return $poId;
            
        } catch (Exception $e) {
            Log::error('Failed to generate PO ID', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
    public function create(Request $request)
    {
        $user = Auth::user();

        try {
            // Validate the request
            $validated = $request->validate([
                'original_delivery_id' => 'required|exists:delivery_requests,delivery_id',
                'po_id' => 'required|exists:purchase_requests,po_id',
                'customer_id' => 'required',
                'scheduled_date' => 'required|date|after:now',
                'selected_items' => 'required|array|min:1',
                'selected_items.*' => 'exists:delivery_item_requests,delivery_item_id',
                'planned_heads' => 'required|array',
                'planned_kilos' => 'required|array',
                'product_ids' => 'required|array',
                'set_ids' => 'required|array',
            ]);

            // Generate new delivery ID
            $delivery_id = $this->generateDeliveryId();

            // Create the new delivery request
            $deliveryRequest = DeliveryRequest::create([
                'po_id' => $validated['po_id'],
                'delivery_id' => $delivery_id,
                'customer_id' => $validated['customer_id'],
                'delivery_date' => $validated['scheduled_date'],
                'status' => 'Scheduled',
                'action_by' => $user->user_id,
                'label' => 'Return',
                'action_at' => now(),
            ]);

            Log::info('New delivery scheduled', [
                'delivery_id' => $delivery_id,
                'original_delivery_id' => $validated['original_delivery_id'],
                'scheduled_date' => $validated['scheduled_date']
            ]);

            $originalDelivery = DeliveryRequest::where('delivery_id', $validated['original_delivery_id'])->first();
            $originalDelivery->return_status = 'Resolved';
            $originalDelivery->save();

            // Create delivery items for selected items
            $itemsCreated = 0;
            foreach ($validated['selected_items'] as $originalItemId) {
                $plannedHeads = $validated['planned_heads'][$originalItemId] ?? 0;
                $plannedKilos = $validated['planned_kilos'][$originalItemId] ?? 0;
                $productId = $validated['product_ids'][$originalItemId];
                $setId = $validated['set_ids'][$originalItemId];

                // Only create item if there's a quantity
                if ($plannedHeads > 0 || $plannedKilos > 0) {
                    $deliveryItemId = $this->generateDeliveryItemId($delivery_id, $productId);

                    DeliveryItemRequest::create([
                        'delivery_id' => $delivery_id,
                        'po_id' => $validated['po_id'],
                        'delivery_item_id' => $deliveryItemId,
                        'customer_id' => $validated['customer_id'],
                        'product_id' => $productId,
                        'set_id' => $setId,
                        'planned_kilos' => $plannedKilos > 0 ? $plannedKilos : null,
                        'planned_heads' => $plannedHeads > 0 ? $plannedHeads : null,
                        'balance' => "0.00"
                    ]);

                    $itemsCreated++;

                    Log::debug('Delivery item created', [
                        'delivery_item_id' => $deliveryItemId,
                        'product_id' => $productId,
                        'planned_heads' => $plannedHeads,
                        'planned_kilos' => $plannedKilos
                    ]);


                }


            }

                $date = date('Ymd');
                $log_id = 'LOG-' . $date . '-' . $this->randomBase36String(5);
                $user_ip = $_SERVER['REMOTE_ADDR'];
                Logs::create([
                    'log_id' =>  $log_id,
                    'user_id' => $user->user_id,
                    'role' => $user->role,
                    'action' => "Scheduled a return delivery",
                    'description' => $deliveryRequest->delivery_id,
                    'ip_address' => $user_ip,
                    'entity' => "DeliveryRequest",
                    'entity_id' => $deliveryRequest->id,
                ]);

            return redirect()->back()->with('success', "Delivery {$delivery_id} scheduled successfully with {$itemsCreated} items.");

        } catch (\Exception $e) {
            Log::error('Error scheduling delivery', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Failed to schedule delivery: ' . $e->getMessage());
        }
    }

    private function generateDeliveryId()
    {
        $lastDelivery = DeliveryRequest::orderBy('delivery_id', 'desc')->first();
        
        if (!$lastDelivery) {
            return 'DLV-000001';
        }

        $lastId = (int) substr($lastDelivery->delivery_id, 4);
        $newId = $lastId + 1;
        
        return 'DLV-' . str_pad($newId, 6, '0', STR_PAD_LEFT);
    }

    private function generateDeliveryItemId($deliveryId, $productId)
    {
        $lastItem = DeliveryItemRequest::where('delivery_id', $deliveryId)
            ->where('product_id', $productId)
            ->orderBy('delivery_item_id', 'desc')
            ->first();

        if (!$lastItem) {
            return $deliveryId . '-' . $productId . '-001';
        }

        $lastSequence = (int) substr($lastItem->delivery_item_id, -3);
        $newSequence = $lastSequence + 1;
        
        return $deliveryId . '-' . $productId . '-' . str_pad($newSequence, 3, '0', STR_PAD_LEFT);
    }


      

}
