@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{asset('css/staffs/list.css')}}">
@endpush

@section('content')

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

            @if (session('success') || session('error'))
                <div 
                    id="flash-message"
                    class="flash-message 
                        {{ session('success') ? 'alert-success' : 'alert-danger' }}">
                    <strong>
                        {{ session('success') ? 'Success:' : ' Error:' }}
                    </strong>
                    {{ session('success') ?? session('error') }}
                </div>
            @endif
    {{-- create order --}}
    @if (auth()->user()->role === 'Customer')
        <div class="modal fade" id="create-order-modal" style="overflow: hidden;"  tabindex="-1" aria-labelledby="requestActionLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl " style="overflow: hidden; height: 90%; overflow: auto;">
                <form class="modal-content" method="POST"  style="width: 800px;overflow: auto;" action="{{ route('purchaseorders.create') }}">
                    @csrf

                
                    <div class="modal-header">
                        <p class="modal-title" id="requestActionLabel">Create purchase order</p>
                    </div>
                    
                    <div class="modal-body">
                        <p class="note-notify">
                            <span class="material-symbols-outlined"> info </span>
                            <span>Select products from your available inventory and specify quantities.</span>
                        </p>
                        <div id="creditInfo" style="margin-bottom: 20px;">
                        </div>

                        <script>
                            // Initialize credit data when page loads
                            document.addEventListener('DOMContentLoaded', function() {
                                const creditLimit = {{ $creditLimit }};
                                const usedCredit = {{ $usedCredit }};
                                const maxAllowed = {{ $maxAllowedCredit }};
                                
                                // Initialize the credit display
                                if (typeof initializeCreditData === 'function') {
                                    initializeCreditData(creditLimit, usedCredit, maxAllowed);
                                }
                            });
                        </script>


                        <div class="form-group" style="margin-bottom: 20px; flex-direction: column; display: flex;">
                            <label for="notes">Notes (Optional)</label>
                            <input name="notes" style="font-size: 14px" id="notes" rows="3" placeholder="Add any additional notes for this purchase order...">
                        </div>

                        <div style="height: 400px; overflow-y: scroll; display: flex; ">
                           <table style="width:100%; border-collapse:collapse;  border: 1px solid #f7f7fa;">
                                <thead style="background-color: #f9f9f9; position: sticky; z-index: 1; top: 0;">
                                    <tr style="background:#f7f7fa; text-align: center; height: 30px">
                                        <td>Select</td>
                                        <td>#</td>
                                        <td>Product Name</td>
                                        <td>Category</td>
                                        <td>Measurements</td>
                                        <td>Price</td>
                                        <td>Heads</td>
                                        <td>Kilos</td>
                                        <td>Total</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $customer = auth()->user()->customer;
                                        $setProducts = \App\Models\ProductSetting::where('customer_id', $customer->customer_id)
                                            ->with('product')
                                            ->get();
                                    @endphp
                                    @foreach($setProds as $setProd)
                                        @php
                                            // Check if sale is active and has quantity
                                            $hasActiveSale = $setProd->on_sale && isset($setProd->activeSale) && $setProd->activeSale->quantity > 0;
                                            $saleQuantity = $hasActiveSale ? $setProd->activeSale->quantity : null;
                                            
                                            // Determine display price
                                            $displayPrice = $hasActiveSale ? $setProd->nego_price : $setProd->original_price;
                                        @endphp
                                        
                                        <tr class="product-row" 
                                            data-set-id="{{ $setProd->set_id }}" 
                                            data-product-id="{{ $setProd->product->product_id }}" 
                                            data-price="{{ $displayPrice }}"
                                            data-original-price="{{ $setProd->original_price }}"
                                            data-on-sale="{{ $hasActiveSale ? 'true' : 'false' }}"
                                            data-sale-quantity="{{ $saleQuantity ?? 'null' }}"
                                            data-measurement-type="{{ $setProd->product->measurement_type }}">                                   
                                            
                                            <td class="checkbox-cell">
                                                <input type="checkbox" 
                                                    name="selected_products[]" 
                                                    value="{{ $setProd->set_id }}"
                                                    class="product-checkbox"
                                                    onchange="toggleProductRow(this, '{{ $setProd->set_id }}')">
                                            </td>
                                            
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $setProd->product->name }}</td>
                                            <td>{{ $setProd->product->category }}</td>
                                            <td>{{ $setProd->product->measurement_type }}</td>
                                            
                                            <td>
                                                <div style="display: flex; flex-direction: column;">
                                                    @if($hasActiveSale)
                                                        <span style="text-decoration: line-through; color: #888;">
                                                            ₱{{ number_format($setProd->original_price, 2) }}
                                                        </span>
                                                        <span style="color: #fe8d29; font-weight: bold;">
                                                            ₱{{ number_format($setProd->nego_price, 2) }}
                                                        </span>
                                                        <span style="color: #666; font-size: 12px;">
                                                            ({{ $saleQuantity }} {{ $setProd->product->measurement_type === 'Heads' ? 'pcs' : 'kg' }} left!)
                                                        </span>
                                                    @elseif($setProd->on_sale && (!isset($setProd->activeSale) || $setProd->activeSale->quantity <= 0))
                                                        {{-- Sale ended, show original price --}}
                                                        <span style="color: #333;">
                                                            ₱{{ number_format($setProd->original_price, 2) }}
                                                        </span>
                                                        <span style="color: #999; font-size: 11px; font-style: italic;">
                                                            (Sale ended)
                                                        </span>
                                                    @else
                                                        ₱{{ number_format($setProd->nego_price, 2) }}
                                                    @endif
                                                </div>
                                            </td>
                                            
                                            <td>
                                                <input type="number" 
                                                    name="placed_heads[{{ $setProd->set_id }}]" 
                                                    value="0" 
                                                    min="1"
                                                    @if($hasActiveSale && $setProd->product->measurement_type === 'Heads')
                                                        max="{{ $saleQuantity }}"
                                                    @endif
                                                    class="form-control heads-input"
                                                    onchange="calculateRowTotal('{{ $setProd->set_id }}')"
                                                    disabled>
                                            </td>                                        
                                            
                                            <td>
                                                <input type="number" 
                                                    name="placed_kilos[{{ $setProd->set_id }}]" 
                                                    value="0" 
                                                    min="0.01"
                                                    step="0.01"
                                                    @if($hasActiveSale && in_array($setProd->product->measurement_type, ['Kilos', 'Heads&Kilos']))
                                                        max="{{ $saleQuantity }}"
                                                    @endif
                                                    class="form-control kilos-input"
                                                    onchange="calculateRowTotal('{{ $setProd->set_id }}')"
                                                    disabled>
                                            </td>
                                            
                                            <td>
                                                <span id="total_{{ $setProd->set_id }}" class="row-total">
                                                    ₱0.00
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
                                    <strong><span id="selectedCount">0</span> selected</strong>
                                </div>
                                <div>
                                    <p style="color: #888">Total: <span id="grandTotal" style="font-size: 15px; color: #333; font-weight: bold;">₱0.00</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn" disabled>Create Purchase Order</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

        <div class="content-bg">
                <div class="content-header">
                    <div class="contents-display">
                        <form action="{{ route('purchaseorders.list') }}" id="text-search" class="search-text-con" method="GET">
                            <input type="text" name="search" class="search-bar"
                                placeholder="Search by PO ID, Customer, Status"
                                value="{{ request('search') }}"
                                style="outline:none;"
                            >
                            <button type="submit" class="search-btn"><span class="material-symbols-outlined">search</span></button>
                        </form>


                        <form action="{{ route('purchaseorders.list') }}" class="date-search" id="from-to-date" method="GET">
                            <p>Date range</p>
                            <div class="from-to-picker">
                                <div class="month-div">
                                    <span>From</span>
                                    <input type="date" name="from_date" class="input-date"
                                        value="{{ request('from_date', now()->startOfMonth()->format('Y-m-d')) }}"
                                        onchange="this.form.submit()">
                                </div>
                                <div class="month-div">
                                    <span>To</span>
                                    <input type="date" name="to_date" class="input-date"
                                        value="{{ request('to_date', now()->endOfMonth()->format('Y-m-d')) }}"
                                        onchange="this.form.submit()">
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="heading" style="display: flex; flex-direction: row; justify-content: space-between; margin-top: 10px;">
                        <p class="heading">Purchase order list</p>
                        @if ( auth()->user()->role === 'Customer')
                            <button class="add-staff-btn btn-transition" data-bs-toggle="modal" data-bs-target="#create-order-modal">
                                <span style="font-size: 15px; margin: 0" class="material-symbols-outlined">add</span>
                                Create order
                            </button>
                        @endif

                    </div>

                </div>


                @if (auth()->user()->role !== 'Customer')

                    <div class="content-body" style="background: #fff">

                        <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                            <thead style="background-color: #fff;">
                                <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>PO ID</th>
                                    <th>Customer</th>
                                    <th>Items</th>
                                    <th>Heads/Kilos</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th>Confirmed By</th>
                                </tr>
                            </thead>
                            <tbody>                                
                                @foreach ($pos as $po)
                                    <tr onclick="window.location.href='{{ route('purchaseorders.purchaseorder', ['po_id' => $po->po_id]) }}'" style="cursor: pointer;">
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{ $po->created_at->format('F j, Y') }}</td>
                                        <td>{{$po->po_id}}</td>
                                        <td>{{ $po->customer->company_name ?? 'N/A' }}</td>
                                        <td>{{ $po->items->count() }}</td>
                                                @php
                                                    $totalHeads = 0;
                                                    $totalKilos = 0;

                                                    foreach ($po->items as $item) {
                                                        if ($item->product->measurement_type === 'Kilos') {
                                                            $totalKilos += $item->placed_kilos ?? 0;
                                                        } elseif ($item->product->measurement_type === 'Heads') {
                                                            $totalHeads += $item->placed_heads ?? 0;
                                                        } elseif ($item->product->measurement_type === 'Heads&Kilos') {
                                                            $totalHeads += $item->placed_heads ?? 0;
                                                            $totalKilos += $item->placed_kilos ?? 0;
                                                        }
                                                    }
                                                @endphp


                                                    <td>
                                                        @if ($totalHeads > 0 && $totalKilos > 0)
                                                            {{ $totalHeads }} pcs | {{ $totalKilos }} kg
                                                        @elseif ($totalHeads > 0)
                                                            {{ $totalHeads }} pcs
                                                        @elseif ($totalKilos > 0)
                                                            {{ $totalKilos }} kg
                                                        @else
                                                            --
                                                        @endif
                                                    </td>




                                        <td>₱{{ number_format($po->total_amount, 2) }}</td>
                                        <td>
                                            <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px; 
                                                @if($po->status === 'Pending') background-color: #fff3cd; color: #856404;
                                                @elseif($po->status === 'Accepted') background-color: #d4edda; color: #155724;
                                                @else background-color: #f8d7da; color: #721c24;
                                                @endif">
                                                {{$po->status}}
                                            </span>
                                        </td>
                                        <td>{{ $po->staff ? $po->staff->first_name . ' ' . $po->staff->last_name : '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                 
                    </div>
                @elseif (auth()->user()->role === 'Customer')

                    <div class="content-body" style="background: #fff">

                        <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                            <thead style="background-color: #fff;">
                                <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>PO ID</th>
                                    <th>Items</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th>Confirmed By</th>
                                </tr>
                            </thead>
                            <tbody>                                
                                @foreach ($pos as $po)
                                    <tr onclick="window.location.href='{{ route('purchaseorders.purchaseorder', ['po_id' => $po->po_id]) }}'" style="cursor: pointer;">
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{ $po->created_at->format('F j, Y') }}</td>
                                        <td>{{$po->po_id}}</td>
                                        <td>{{ $po->items->count() }}</td>
                                        <td>₱{{ number_format($po->total_amount, 2) }}</td>
                                        <td>
                                            <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px; 
                                                @if($po->status === 'Pending') background-color: #fff3cd; color: #856404;
                                                @elseif($po->status === 'Accepted') background-color: #d4edda; color: #155724;
                                                @else background-color: #f8d7da; color: #721c24;
                                                @endif">
                                                {{$po->status}}
                                            </span>
                                        </td>
                                        <td>{{ $po->staff ? $po->staff->first_name . ' ' . $po->staff->last_name : '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                
                    </div>

                @endif

        </div>


@endsection

@push('scripts')
    <script src="{{ asset('js/purchase-order/create-po.js') }}"></script>



@endpush
