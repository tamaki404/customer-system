<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use App\Models\DeliveryRequest;
use App\Models\DeliveryItemRequest;
use App\Models\ProductSetting;
use App\Models\Customers;
use App\Models\Receipts;
use App\Models\Payments;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class PurchaseRequestController extends Controller
{
    public function list(Request $request)
    {
        try {
            $user = Auth::user();

            if ($user->role === 'Customer') {
                $customer = Customers::where('user_id', $user->user_id)->firstOrFail();
                $requests = PurchaseRequest::where('user_id', $user->user_id)
                    ->orderBy('created_at', 'desc')
                    ->get();
                $products = ProductSetting::where('customer_id', $customer->customer_id)
                    ->with('product')
                    ->get();
                
                Log::info('Customer purchase requests loaded', [
                    'customer_id' => $customer->customer_id,
                    'requests_count' => $requests->count(),
                    'products_count' => $products->count()
                ]);
                
            } elseif ($user->role !== 'Customer') {
                $requests = PurchaseRequest::orderBy('created_at', 'desc')->get();
                $products = collect();
                
                Log::info('Staff purchase requests loaded', [
                    'requests_count' => $requests->count()
                ]);
            }
            
            return view('franken.pr.list', compact(
                'user',
                'requests',
                'products'
            ));
            
        } catch (Exception $e) {
            Log::error('Error loading purchase request list', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);
            
            return redirect()->back()
                ->with('error', 'Failed to load purchase requests. Please try again.');
        }
    }
    public function request($po_id, Request $request)
    {
        $user = Auth::user();
        $customer = Customers::where('user_id', $user->user_id)->firstOrFail();
        $request = PurchaseRequest::where('po_id', $po_id)->first();
        $del = DeliveryRequest::where('po_id', $po_id)->first();
        $itemCount = DeliveryItemRequest::where('po_id', $po_id)->count();
        $delCount = DeliveryRequest::where('po_id', $po_id)->count();
        $orderDeets = PurchaseRequest::where('po_id', $po_id)->first();

        $items = DeliveryItemRequest::where('delivery_id', $del->delivery_id)
            ->where('po_id', $po_id)
            ->with(['product', 'productSetting'])
            ->get()
            ->groupBy('product_id')
            ->map(function($group) {
                $first = $group->first();
                // Sum up quantities for the same product
                $first->total_planned_kilos = $group->sum('planned_kilos');
                $first->total_planned_heads = $group->sum('planned_heads');

                return $first;
            });
        $poBalance = DeliveryItemRequest::join('delivery_requests', 'delivery_item_requests.delivery_id', '=', 'delivery_requests.delivery_id')
            ->where('delivery_item_requests.po_id', $po_id)
            ->where('delivery_requests.status', 'Delivered')
            ->sum('delivery_item_requests.balance');

        $poPaid = Payments::where('po_id', $po_id)
            ->where('status', 'Verified')
            ->sum('total_amount');

        $remainingBalance = $poBalance - $poPaid;

        // Get delivery data
        $deliveries = DeliveryRequest::when($po_id, function ($query) use ($po_id) {
            $query->where('po_id', $po_id);
        })->orderBy('delivery_date', 'asc')->get();
        $verifiedPaidAmount = Receipts::where('po_id', $po_id)
            ->where('status', 'Verified')
            ->sum('total_amount');
            //  Determine payment status
            $paymentStatus = 'Not Paid';
            if ($verifiedPaidAmount >= $request->total_amount) {
                $paymentStatus = 'Paid';
            } elseif ($verifiedPaidAmount > 0 && $verifiedPaidAmount < $request->total_amount) {
                $paymentStatus = 'Partially settled';
            }
        
        $payments =Receipts::where('po_id', $po_id)
        ->orderBy('created_at', 'desc')
        ->where('status', "Verified")->get();
        $receivedPaymentCount =Receipts::where('po_id', $po_id)
        ->where('status', "Verified")->count();

        return view('franken.pr.request', [
                'user' => $user,
                'request' => $request,
                'items' => $items,
                'deliveries' => $deliveries,
                'activeDelivery' => $deliveries->count(),
                'itemCount' => $itemCount,
                'delCount' => $delCount,
                'orderDeets' => $orderDeets,
                'verifiedPaidAmount' => $verifiedPaidAmount,
                'paymentStatus' => $paymentStatus,
                'payments' => $payments,
                'receivedPaymentCount' => $receivedPaymentCount,
                'poBalance' => $poBalance,
                'remainingBalance' => $remainingBalance,
                'poPaid' => $poPaid,

        ]);


        
    }
    public function create(Request $request)
    {
        $user = Auth::user();
        
        Log::info('Purchase Request creation started', [
            'user_id' => $user->user_id,
            'request_data' => $request->except(['_token'])
        ]);

        try {
            // Validate the request
            $validated = $request->validate([
                'notes' => 'nullable|string|max:255',
                'preferred_days' => 'required|array|min:1',
                'preferred_days.*' => 'required|date|after:today',
                'selected_products' => 'required|array|min:1',
                'selected_products.*' => 'required|string',
                'planned_heads' => 'nullable|array',
                'planned_heads.*' => 'nullable|integer|min:0',
                'planned_kilos' => 'nullable|array',
                'planned_kilos.*' => 'nullable|numeric|min:0',
            ]);

            Log::info('Validation passed', ['validated_data' => $validated]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation failed for purchase request', [
                'user_id' => $user->user_id,
                'errors' => $e->errors(),
                'input' => $request->except(['_token'])
            ]);
            throw $e;
        }

        try {
            $customer = Customers::where('user_id', $user->user_id)->firstOrFail();
            
            Log::info('Customer found', [
                'customer_id' => $customer->customer_id,
                'user_id' => $user->user_id
            ]);

        } catch (Exception $e) {
            Log::error('Customer not found', [
                'user_id' => $user->user_id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Customer profile not found. Please contact support.');
        }

        DB::beginTransaction();
        
        try {
            // Generate unique PO ID
            $poId = $this->generatePoId();
            Log::info('Generated PO ID', ['po_id' => $poId]);
            
            // Calculate total amount
            // $totalAmount = $this->calculateTotalAmount(
            //     $validated['selected_products'],
            //     $validated['planned_heads'] ?? [],
            //     $validated['planned_kilos'] ?? []
            // );
            

            // Create Purchase Request
            $purchaseRequest = PurchaseRequest::create([
                'po_id' => $poId,
                'status' => 'Pending',
                'user_id' => $user->user_id,
                'total_amount' => "0.00",
                'notes' => $validated['notes'],
                'action_by' => $user->user_id,
                'action_at' => now(),
            ]);
            

            // Create Delivery Requests for each preferred day
            $preferredDays = $validated['preferred_days'];
            $numberOfDays = count($preferredDays);
            
            Log::info('Creating delivery requests', [
                'po_id' => $poId,
                'number_of_days' => $numberOfDays,
                'preferred_days' => $preferredDays
            ]);
            
            foreach ($preferredDays as $index => $deliveryDate) {
                $deliveryId = $this->generateDeliveryId($poId, $index + 1);
                
                Log::info('Creating delivery request', [
                    'delivery_id' => $deliveryId,
                    'delivery_date' => $deliveryDate,
                    'index' => $index + 1
                ]);
                
                try {
                    $deliveryRequest = DeliveryRequest::create([
                        'po_id' => $poId,
                        'delivery_id' => $deliveryId,
                        'customer_id' => $customer->customer_id,
                        'delivery_date' => $deliveryDate,
                        'status' => 'Scheduled',
                        'action_by' => $user->user_id,
                        'action_at' => now(),
                    ]);
                    
                    Log::info('Delivery request created', [
                        'delivery_id' => $deliveryId,
                        'id' => $deliveryRequest->id
                    ]);
                    
                } catch (Exception $e) {
                    Log::error('Failed to create delivery request', [
                        'delivery_id' => $deliveryId,
                        'delivery_date' => $deliveryDate,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }
                
                // Create Delivery Item Requests for each selected product
                $itemsCreated = 0;
                foreach ($validated['selected_products'] as $productId) {
                    try {
                        $productSetting = ProductSetting::where('product_id', $productId)
                            ->where('customer_id', $customer->customer_id)
                            ->firstOrFail();
                        
                        $plannedHeads = $validated['planned_heads'][$productId] ?? 0;
                        $plannedKilos = $validated['planned_kilos'][$productId] ?? 0;
                        
                        // Divide quantities across delivery days
                        $dividedHeads = $numberOfDays > 0 ? floor($plannedHeads / $numberOfDays) : 0;
                        $dividedKilos = $numberOfDays > 0 ? round($plannedKilos / $numberOfDays, 2) : 0;
                        
                        // For the last delivery, add any remainder
                        if ($index === $numberOfDays - 1) {
                            $remainderHeads = $plannedHeads - ($dividedHeads * ($numberOfDays - 1));
                            $remainderKilos = $plannedKilos - ($dividedKilos * ($numberOfDays - 1));
                            $dividedHeads = $remainderHeads;
                            $dividedKilos = $remainderKilos;
                        }
                        
                        // Only create item if there's a quantity
                        if ($dividedHeads > 0 || $dividedKilos > 0) {
                            $deliveryItemId = $this->generateDeliveryItemId($deliveryId, $productId);
                            
                            DeliveryItemRequest::create([
                                'delivery_id' => $deliveryId,
                                'po_id' => $poId,
                                'delivery_item_id' => $deliveryItemId,
                                'customer_id' => $customer->customer_id,
                                'product_id' => $productId,
                                'set_id' => $productSetting->set_id,
                                'planned_kilos' => $dividedKilos > 0 ? $dividedKilos : null,
                                'planned_heads' => $dividedHeads > 0 ? $dividedHeads : null,
                                'balance' => "0.00" 
                            ]);
                            
                            $itemsCreated++;
                            
                            Log::debug('Delivery item created', [
                                'delivery_item_id' => $deliveryItemId,
                                'product_id' => $productId,
                                'planned_heads' => $dividedHeads,
                                'planned_kilos' => $dividedKilos
                            ]);
                        }
                        
                    } catch (Exception $e) {
                        Log::error('Failed to create delivery item', [
                            'delivery_id' => $deliveryId,
                            'product_id' => $productId,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                        throw $e;
                    }
                }
                
                Log::info('Delivery items created', [
                    'delivery_id' => $deliveryId,
                    'items_created' => $itemsCreated
                ]);
            }
            
            DB::commit();
            

            
            return redirect()->route('pr.list')
                ->with('success', 'Purchase request created successfully! PO ID: ' . $poId);
                
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Purchase request creation failed - Transaction rolled back', [
                'user_id' => $user->user_id,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['_token'])
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create purchase request: ' . $e->getMessage());
        }
    }
    public function collection($po_id, Request $request)
    {
        $user = Auth::user();
        $customer = Customers::where('user_id', $user->user_id)->firstOrFail();
        $receipts = Receipts::where('po_id', $po_id)->get();
        $po = PurchaseRequest::where('po_id', $po_id)->firstOrFail();
        return view('franken.pr.collection', compact(
            'customer',
            'receipts',
            'po'
        ));
    }

    /**
     * Generate unique PO ID
     */
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
    
    /**
     * Generate unique Delivery ID
     */
    private function generateDeliveryId(string $poId, int $sequence): string
    {
        $deliveryId = $poId . '-D' . str_pad($sequence, 2, '0', STR_PAD_LEFT);
        
        Log::debug('Delivery ID generated', [
            'po_id' => $poId,
            'sequence' => $sequence,
            'delivery_id' => $deliveryId
        ]);
        
        return $deliveryId;
    }
    
    /**
     * Generate unique Delivery Item ID
     */
    private function generateDeliveryItemId(string $deliveryId, string $productId): string
    {
        $deliveryItemId = $deliveryId . '-P' . $productId;
        
        Log::debug('Delivery Item ID generated', [
            'delivery_id' => $deliveryId,
            'product_id' => $productId,
            'delivery_item_id' => $deliveryItemId
        ]);
        
        return $deliveryItemId;
    }
    
    /**
     * Calculate total amount based on selected products and quantities
     */
    private function calculateTotalAmount(array $productIds, array $plannedHeads, array $plannedKilos): float
    {
        try {
            $total = 0;
            $user = Auth::user();
            $customer = Customers::where('user_id', $user->user_id)->firstOrFail();
            
            Log::info('Calculating total amount', [
                'customer_id' => $customer->customer_id,
                'products_count' => count($productIds)
            ]);
            
            foreach ($productIds as $productId) {
                $productSetting = ProductSetting::where('product_id', $productId)
                    ->where('customer_id', $customer->customer_id)
                    ->with('product')
                    ->firstOrFail();
                
                $heads = $plannedHeads[$productId] ?? 0;
                $kilos = $plannedKilos[$productId] ?? 0;
                $price = $productSetting->nego_price;
                $measurementType = $productSetting->product->measurement_type;
                
                $productTotal = 0;
                
                // Calculate based on measurement type
                if ($measurementType === 'Heads') {
                    $productTotal = $heads * $price;
                } elseif ($measurementType === 'Kilos') {
                    $productTotal = $kilos * $price;
                }
                
                $total += $productTotal;
                
                Log::debug('Product total calculated', [
                    'product_id' => $productId,
                    'measurement_type' => $measurementType,
                    'heads' => $heads,
                    'kilos' => $kilos,
                    'price' => $price,
                    'product_total' => $productTotal
                ]);
            }
            
            $roundedTotal = round($total, 2);
            
            Log::info('Total amount calculation completed', [
                'raw_total' => $total,
                'rounded_total' => $roundedTotal
            ]);
            
            return $roundedTotal;
            
        } catch (Exception $e) {
            Log::error('Failed to calculate total amount', [
                'error' => $e->getMessage(),
                'product_ids' => $productIds,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}