
@extends('layouts.main')

@push('styles')
@endpush

@section('content')

    {{-- Staff confirmation modal --}}
    @if (auth()->user()->role !== 'Supplier' && $po->status === 'Pending')
        <div class="modal fade" id="confirm-action" tabindex="-1" aria-labelledby="confirmActionLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <form class="modal-content" style="width: 800px" method="POST" action="{{ route('purchaseorders.confirm', $po->po_id) }}">
                    @csrf
                    
                    @if ($errors->any())
                        <div class="alert alert-danger" style="margin: 10px;">
                            <h6 style="margin-bottom: 10px; font-weight: bold;">Validation Errors:</h6>
                            <ul style="margin: 0; padding-left: 20px;">
                                @foreach ($errors->all() as $error)
                                    <li style="font-size: 14px;">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    @if (session('success'))
                        <div class="alert alert-success" style="margin: 10px;">{{ session('success') }}</div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" style="margin: 10px;">
                            <h6 style="margin-bottom: 10px; font-weight: bold;">Error:</h6>
                            <p style="margin: 0; font-size: 14px;">{{ session('error') }}</p>
                        </div>
                    @endif

                        <div class="modal-header">
                            <p class="modal-title" id="confirmActionLabel">Confirm Purchase Order</p>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    
                        <div class="modal-body">
                        <p class="note-notify">
                            <span class="material-symbols-outlined"> info </span>
                                <span>Review and modify quantities as needed. You can accept or reject this purchase order.</span>
                            </p>

                            <div class="form-group" style="margin-bottom: 20px; display: flex; flex-direction: column;">
                                <label for="staff_notes">Staff Notes (Optional)</label>
                                <textarea name="notes" id="staff_notes" class="" rows="3" style=" border-radius: 5px; outline: none; padding: 5px;" placeholder="Add any notes about this purchase order..."></textarea>
                            </div>

                            <div style="overflow-x: auto;">
                            <table style="width:100%; border-collapse:collapse; border: 1px solid #f7f7fa;">
                                <thead style="background-color: #f9f9f9;">
                                    <tr style="background:#f7f7fa; text-align: center; height: 30px">
                                        <td>#</td>
                                        <td>Product ID</td>
                                        <td>Name</td>
                                        <td>Measurement</td>
                                        <td>Unit price</td>
                                        <td>Quantity</td>
                                        <td>Total</td>
                                    </tr>
                                </thead>
                                <tbody>
                                        @foreach($po->items as $item)
                                        <tr>
                                        <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->product->product_id }}</td>
                                            <td>{{ $item->product->name }}</td>
                                            <td>{{ $item->product->measurement_type }}</td>
                                            <td>₱{{ number_format($item->unit_price, 2) }}</td>
                                            <td style="display: flex; justify-content: center;">
                                                @if ($item->product->measurement_type === "Kilos")
                                                    <input type="number" 
                                                            name="alt_kilos[{{ $item->po_item_id }}]" 
                                                            value="{{ $item->placed_kilos }}" 
                                                            min="0"
                                                            class="form-control staff-quantity-input"
                                                            onchange="calculateStaffTotal('{{ $item->po_item_id }}', {{ $item->unit_price }})" 
                                                            style="width:100px"
                                                            >
                                                @elseif ($item->product->measurement_type === "Heads")
                                                    <input type="number" 
                                                            name="alt_heads[{{ $item->po_item_id }}]" 
                                                            value="{{ $item->placed_heads }}" 
                                                            min="0"
                                                            class="form-control staff-quantity-input"
                                                            onchange="calculateStaffTotal('{{ $item->po_item_id }}', {{ $item->unit_price }})"
                                                            style="width:100px"
                                                            >
                                                @endif

                                            </td>
                                        <td>
                                            <span id="staff_total_{{ $item->po_item_id }}" class="staff-row-total">
                                                ₱{{ number_format($item->unit_price * $item->supplier_quantity, 2) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                            <div style="margin-top: 20px; padding: 15px; background-color: #f8f9fa; border-radius: 5px;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <strong>Total Items: <span id="totalItems">{{ $po->items->count() }}</span></strong>
                                    </div>
                                    <div>
                                        <strong>Grand Total: <span id="staffGrandTotal">₱{{ number_format($po->total_amount, 2) }}</span></strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="action" value="Reject" style="font-size: 14px" class="btn btn-danger">Reject Order</button>
                        <button type="submit" name="action" value="Accept" style="font-size: 14px" class="btn btn-success">Accept Order</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

   <div class="content-bg">
        <div class="content-header">
            <div class="contents-display">
                <p>
                    <a href="{{ route('purchaseorders.list') }}">< Purchase orders list</a>
                </p>
            </div>

            <div class="title-actions">
                <p class="heading">Purchase Order - {{ $po->po_id }}</p>
                <div style="display: flex; gap: 10px;">
                    @if (auth()->user()->role !== 'Supplier' && $po->status === 'Pending')
                        <button data-bs-toggle="modal" data-bs-target="#confirm-action" class="btn-transition">Confirm Order</button>
                    @endif
                    
                    @if ($po->status !== 'Pending' && auth()->user()->role !== 'Supplier')
                        <div style="display: flex; flex-direction: column; gap: 5px; margin: 5px;">
                            <p style="margin: 0"><span>Print</span></p>
                    <div>
                                <button type="button" 
                                        data-bs-toggle="modal" data-bs-target="#pdfModal" 
                                        data-url="{{ route('purchaseorders.pdf', $po->po_id) }}"
                                        class="btn-transition">
                                    Purchase Order
                                </button>
                                

                            </div>
                    </div>
                @endif
                </div>
            </div>
        </div>

        <div class="content-body" style="padding: 20px; border: none; height: auto;">
            <!-- Order Details -->
            <div style="background: #fff; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
                <h4>Order Details</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 15px;">
                    <div>
                        <p><strong>PO ID:</strong> {{ $po->po_id }}</p>
                        <p><strong>Supplier:</strong> {{ $po->supplier->company_name }}</p>
                        <p><strong>Status:</strong> 
                            <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px; 
                                @if($po->status === 'Pending') background-color: #fff3cd; color: #856404;
                                @elseif($po->status === 'Accepted') background-color: #d4edda; color: #155724;
                                @else background-color: #f8d7da; color: #721c24;
                                @endif">
                                {{ $po->status }}
                            </span>
                        </p>
                        <p><strong>Created:</strong> {{ $po->created_at->format('F j, Y g:i A') }}</p>
                    </div>
                    <div>
                        <p><strong>Total Amount:</strong> ₱{{ number_format($po->total_amount, 2) }}</p>
                        @if($po->staff)
                            <p><strong>Confirmed by:</strong> {{ $po->staff->first_name }} {{ $po->staff->last_name }}</p>
                        @endif
                        @if($po->confirmed_at)
                            <p><strong>Confirmed at:</strong> {{ $po->confirmed_at->format('F j, Y g:i A') }}</p>
                        @endif
                        @if($po->notes)
                            <p><strong>Notes:</strong> {{ $po->notes }}</p>
                        @endif
                        @if($po->status === 'Accepted')
                            @php
                                $relatedOrder = \App\Models\Orders::where('po_id', $po->po_id)->first();
                    @endphp
                            @if($relatedOrder)
                                <p><strong>Related Order:</strong> 
                                    <a href="{{ route('orders.order', $relatedOrder->order_id) }}" style="color: #007bff; text-decoration: none;">
                                        {{ $relatedOrder->order_id }}
                                    </a>
                                </p>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div style="background: #fff; padding: 20px; border-radius: 10px;">
                <h4>Order Items</h4>
                <div style="overflow-x: auto; margin-top: 15px;">
                    <table style="width:100%; border-collapse:collapse; border: 1px solid #ddd;">
                        <thead style="background-color: #f8f9fa;">
                            <tr style="text-align: center; height: 40px; border-bottom: 1px solid #ddd;">
                                <th>#</th>
                                <th>Product ID</th>
                                <th>Product Name</th>
                                <th>Condition</th>
                                <th>Measurement</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Status</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($po->items as $item)
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="text-align: center;">{{ $loop->iteration }}</td>
                                    <td style="text-align: center;">{{ $item->set_id }}</td>
                                    <td>{{ $item->product->name }}</td>
                                    <td>{{ optional($item->set->user->req($item->product_id)->first())->condition ?? 'N/A' }}</td>
                                    <td>{{ $item->product->measurement_type }}</td>
                                    <td>
                                        @if ($item->product->measurement_type === "Kilos" && $item->product->measurement_type !== "0")
                                            {{ $item->placed_kilos }}kg
                                        @elseif ($item->product->measurement_type === "Heads" && $item->product->measurement_type !== "0")
                                            {{ $item->placed_heads }}
                                        @else
                                            --
                                        @endif
                                    </td>
                                    <td style="text-align: center;">
                                        @if($item->original_price && $item->original_price > $item->unit_price)
                                            <span style="text-decoration: line-through; color: #999;">
                                                ₱{{ number_format($item->original_price, 2) }}
                                            </span><br>
                                            <span style="color: #fe8d29; font-weight: bold;">
                                                ₱{{ number_format($item->unit_price, 2) }}
                                            </span>
                                        @else
                                            ₱{{ number_format($item->unit_price, 2) }}
                                        @endif
                                    </td>


                                    <td style="text-align: center;">
                                        <span style="padding: 2px 6px; border-radius: 3px; font-size: 11px;
                                            @if($item->status === 'Pending') background-color: #fff3cd; color: #856404;
                                            @elseif($item->status === 'Accepted') background-color: #d4edda; color: #155724;
                                            @else background-color: #f8d7da; color: #721c24;
                                            @endif">
                                            {{ $item->status }}
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        @php
                                            $oldTotal = $item->original_price * $item->supplier_quantity;
                                            $newTotal = $item->total_price;
                                        @endphp

                                        @if($item->original_price > $item->unit_price)
                                            <span style="text-decoration: line-through; color: #999;">
                                                ₱{{ number_format($oldTotal, 2) }}
                                            </span><br>
                                            <span style="color: #fe8d29; font-weight: bold;">
                                                ₱{{ number_format($newTotal, 2) }}
                                            </span>
                                        @else
                                            ₱{{ number_format($newTotal, 2) }}
                                        @endif
                                    </td>                                
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </div>

   <!-- PDF Modal -->
   <div class="modal fade" id="pdfModal" tabindex="-1" aria-hidden="true">
       <div class="modal-dialog modal-xl">
           <div class="modal-content">
               <div class="modal-header">
                   <h5 class="modal-title">Purchase Order PDF</h5>
                   <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
               <div class="modal-body">
                   <iframe id="pdfFrame" src="" width="100%" height="600px" style="border: none;"></iframe>
               </div>
           </div>
       </div>
   </div>

@endsection



@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function calculateStaffTotal(poItemId, unitPrice) {
        const quantityInput = document.querySelector(`input[name="staff_quantities[${poItemId}]"]`);
        const totalSpan = document.getElementById(`staff_total_${poItemId}`);
        
        if (quantityInput && totalSpan) {
            const quantity = parseInt(quantityInput.value) || 0;
            const total = unitPrice * quantity;
            
            totalSpan.textContent = `₱${total.toFixed(2)}`;
            
            // Update grand total
            updateStaffGrandTotal();
        }
    }

    function updateStaffGrandTotal() {
        let grandTotal = 0;
        const totalSpans = document.querySelectorAll('.staff-row-total');
        
        totalSpans.forEach(span => {
            const amount = parseFloat(span.textContent.replace('₱', '').replace(',', '')) || 0;
            grandTotal += amount;
        });
        
        const grandTotalSpan = document.getElementById('staffGrandTotal');
        if (grandTotalSpan) {
            grandTotalSpan.textContent = `₱${grandTotal.toFixed(2)}`;
        }
    }

    // PDF Modal functionality
    document.addEventListener('DOMContentLoaded', function() {
        const pdfModal = document.getElementById('pdfModal');
        if (pdfModal) {
            pdfModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const pdfUrl = button.getAttribute('data-url');
                const pdfFrame = document.getElementById('pdfFrame');
                if (pdfFrame && pdfUrl) {
                    pdfFrame.src = pdfUrl;
                }
            });
        }
    });
</script>
@endpush