
@extends('layout')

@section( 'content')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/fadein.css') }}">
    <link rel="stylesheet" href="{{ asset('css/receipts_now.css') }}">
@endpush
@push('scripts')
    <link rel="stylesheet" href="{{ asset('css/open-modal.css') }}">
    <script src="{{ asset('scripts/open-modal.js') }}"></script>
    <script src="{{ asset('js/disableBtn.js') }}"></script>
    <script src="{{ asset('js/receipts.js') }}"></script>
@endpush
@push('cdn')
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
@endpush

@extends('layout')

@section('content')

<script src="{{ asset('js/fadein.js') }}"></script>

    {{-- modal --}}
    <div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        @if(auth()->user()->user_type === 'Customer')
            <div class="form-section">
                <h3 class="form-title" style="margin: 1px">Submit New Receipt</h3>
                <p>Please upload your receipt below. Ensure all information is accurate before submission.</p>
                <form action="/submit-receipt" id="submitForm" class="receipt-form" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-grid">
                        <input type="hidden" name="id" value="{{ auth()->user()->id }}">
                        <input type="hidden" name="status" value="Pending">
                        <input type="hidden" name="verified_by">
                        <input type="hidden" name="verified_at">

                        <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            const form = document.querySelector('.receipt-form');
                            const totalAmountInput = document.getElementById('total_amount');
                            if (form && totalAmountInput) {
                                form.addEventListener('submit', function(e) {
                                    totalAmountInput.value = totalAmountInput.value.replace(/,/g, '');
                                });
                            }
                        });
                        </script>

                        <div style="display: none">
                            <label>Store Name</label>
                            <input type="text" name="store_name" value="{{ auth()->user()->store_name }}" placeholder="Store name" hidden>
                        </div>

                        <div style="display: none">
                            <label>Username</label>
                            <input type="text" name="username" value="{{ auth()->user()->username }}" hidden>
                        </div>

                        <div>
                            <label>Receipt Number</label>
                            <input type="text" name="receipt_number" required>
                        </div>

                        <div>
                            <label>Upload Receipt</label>
                            <input type="file" name="receipt_image" accept="image/*" required>
                        </div>

                        <div>
                            <label>Purchase Date</label>
                            <input type="date" name="purchase_date" placeholder="e.g. 2025-07-22" required>
                        </div>

                        <div>
                            <label>Total Amount</label>
                            <div style="position: relative;">
                                <span style="
                                    position: absolute;
                                    top: 50%;
                                    left: 10px;
                                    transform: translateY(-50%);
                                    font-weight: bold;
                                    color: #444;
                                ">₱</span>
                                <input 
                                    type="text" 
                                    name="total_amount" 
                                    id="total_amount"
                                    placeholder="0.00"
                                    required
                                    style="padding-left: 25px;"
                                >
                            </div>
                        </div>

                        <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            const input = document.getElementById('total_amount');

                            input.addEventListener('input', () => {
                                let rawValue = input.value.replace(/[^0-9.]/g, '');
                                const parts = rawValue.split('.');
                                
                                let formatted = Number(parts[0]).toLocaleString('en-US');
                                
                                if (parts.length > 1) {
                                    formatted += '.' + parts[1].slice(0, 2);
                                }

                                input.value = formatted;
                            });
                        });
                        </script>

                        <div>
                            <label>Payment Method</label>
                            <input type="text" name="payment_method" id="payment_method" placeholder="Cash / GCash / Card" required>
                        </div>

                        <div id="invoice_section">
                            <label>Invoice Number</label>
                            <input type="text" name="invoice_number" id="invoice_number" disabled>
                        </div>



                        <div class="full">
                            <label>Orders</label>
                            <textarea name="notes" rows="10"  rows="5" cols="30"placeholder="You can list your orders here"></textarea>
                        </div>

                    </div>

                    <button type="submit" class="submit-btn" id="submitBtn" style="color: #333; font-size: 15px;">Submit Receipt</button>
                </form>
            </div>
        @endif

        
    </div>
    </div>


    <div class="receipts-frame">
        <div class="search-container">
            <form action="{{ route('purchase_order') }}" id="text-search" class="date-search" method="GET">
                <input type="text" name="search" class="search-bar"
                    placeholder="Search receipt #, customer, amount, or date"
                    value="{{ request('search') }}"
                    style="outline:none;"
                >
                <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
            </form>

            <form action="{{ route('purchase_order') }}" class="date-search" id="from-to-date" method="GET">
                <span>From</span>
                <input type="date" name="from_date" class="input-date"
                    value="{{ request('from_date', now()->startOfMonth()->format('Y-m-d')) }}"
                    onchange="this.form.submit()">
                <span>To</span>
                <input type="date" name="to_date" class="input-date"
                    value="{{ request('to_date', now()->endOfMonth()->format('Y-m-d')) }}"
                    onchange="this.form.submit()">
            </form>
        </div>

        <div class="receipt-title" >
            <div class="receipt-title-button">
                <h2>Receipts</h2>
                @if(auth()->user()->user_type === 'Customer')
                    <button id="openModalBtn" class="submit-ticket-btn">Submit a Receipt</button>
                @endif
     
            </div>     

        </div>
            @if(session('success'))
                <div class="alert success" id="alert-notify"  style="padding: 10px 15px; background: #d4edda; color: #155724; border-radius: 5px; margin-bottom: 10px;" >
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert error" id="alert-notify" style=" display: flex; padding: 10px 15px; background: #f8d7da; color: #721c24; border-radius: 5px; margin-bottom: 10px;" >
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif



        <div class="status-pagination">
            <div class="tab-statuses">
                @php
                    $currentStatus = $status ?? request('status');
                    $baseParams = [
                        'from_date' => request('from_date', now()->startOfMonth()->format('Y-m-d')),
                        'to_date' => request('to_date', now()->endOfMonth()->format('Y-m-d')),
                        'search' => request('search', '')
                    ];
                    $tabStatuses = [
                        'All' => null,
                        'Pending' => 'Pending',
                        'Verified' => 'Verified',
                        'Cancelled' => 'Cancelled',
                        'Rejected' => 'Rejected'
                    ];
                @endphp
                @foreach($tabStatuses as $label => $value)
                    @php
                        $isActive = ($value === null && empty($currentStatus)) || ($value !== null && $currentStatus === $value);
                        $params = $value ? array_merge($baseParams, ['status' => $value]) : $baseParams;
                    @endphp
                        <a href="{{ route('date.search', $params) }}" class="status-tab{{ $isActive ? ' active' : '' }}">
                            {{ $label }}
                        </a>
                @endforeach
            </div>
            {{-- <div style="margin-bottom: 1rem; font-size: 0.9rem; color: #666;">
                Page {{ $receipts->currentPage() }} of {{ $receipts->lastPage() }} ({{ $receipts->total() }} total receipts)
            </div> --}}

            @if($receipts->where('status', 'Verified')->count())
                <div class="sum-receipts" style="margin: 0">
                    Total Amount for Verified Receipts: <p class="sum-receipts-total" style="margin: 0">₱{{ number_format($receipts->where('status', 'Verified')->sum('total_amount'), 2) }}</p>
                </div> 
            @endif 
        </div>

        <div class="receipts-box">
            {{-- admin & staff --}}
            @if(auth()->user()->user_type !== 'Customer')
                <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="background:#f7f7fa; text-align: center;">
                        <th style="width:5%;">#</th>
                        <th style="width:15%;">Date</th>
                        <th style="width:30%;">Customer</th>
                        <th style="width:15%;">Amount</th>
                        <th style="width:10%;">Status</th>
                        <th style="width:15%;">Action by</th>
                        <th style="width:10%;">Receipt</th>
                    </tr>
                </thead>

                    <tbody>
                        @forelse($receipts as $receipt)
                            <tr style="height: 50px; text-align: center; cursor:pointer; overflow: hidden;" onclick="window.location='{{ url('/receipts_view/' . $receipt->receipt_id) }}'">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ \Carbon\Carbon::parse($receipt->purchase_date)->format('j F, Y') }}</td>
                                <td>{{ $receipt->store_name }}</td>
                                <td>₱{{ number_format($receipt->total_amount, 2) }}</td>
                                <td>
                                    @php 
                                        $statusClasses = [
                                        'Pending' => 'status-pending',
                                        'Verified' => 'status-verified',
                                        'Cancelled' => 'status-cancelled',
                                        'Rejected' => 'status-rejected',
                                        ];
                                    @endphp

                                    <div class="{{ $statusClasses[$receipt->status] ?? 'status-default' }}">
                                        {{ $receipt->status }}
                                    </div>
                                </td>
                                <td style="font-size: 14px">{{ $receipt->verified_by }}</td>
                                <td>
                                    @if($receipt->receipt_image)
                                        @php
                                            $isBase64 = !empty($receipt->receipt_image_mime);
                                            $dataUri = $isBase64 ? ('data:' . $receipt->receipt_image_mime . ';base64,' . $receipt->receipt_image) : null;
                                        @endphp
                                        <img style="height: 50px" src="{{ $dataUri ? $dataUri : asset('images/' . $receipt->receipt_image) }}" class="receipt-thumb" alt="Receipt Image">
                                        @else
                                            N/A
                                    @endif
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" style="color: #888">No purchase orders found.</td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>


            {{-- customer --}}
            @elseif(auth()->user()->user_type === 'Customer')
                <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="background:#f7f7fa; text-align: center;">
                        <th style="width:5%;">#</th>
                        <th style="width:15%;">Date</th>
                        <th style="width:15%;">Amount</th>
                        <th style="width:10%;">Status</th>
                        <th style="width:10%;">Receipt</th>
                    </tr>
                </thead>

                    <tbody>
                        @forelse($receipts as $receipt)
                            <tr style="height: 50px; text-align: center; cursor:pointer; overflow: hidden;" onclick="window.location='{{ url('/receipts_view/' . $receipt->receipt_id) }}'">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ \Carbon\Carbon::parse($receipt->purchase_date)->format('j F, Y') }}</td>
                                <td>₱{{ number_format($receipt->total_amount, 2) }}</td>
                                <td>
                                    @php 
                                        $statusClasses = [
                                        'Pending' => 'status-pending',
                                        'Verified' => 'status-verified',
                                        'Cancelled' => 'status-cancelled',
                                        'Rejected' => 'status-rejected',
                                        ];
                                    @endphp

                                    <div class="{{ $statusClasses[$receipt->status] ?? 'status-default' }}">
                                        {{ $receipt->status }}
                                    </div>
                                </td>
                                <td>
                                    @if($receipt->receipt_image)
                                        @php
                                            $isBase64 = !empty($receipt->receipt_image_mime);
                                            $dataUri = $isBase64 ? ('data:' . $receipt->receipt_image_mime . ';base64,' . $receipt->receipt_image) : null;
                                        @endphp
                                        <img style="height: 50px" src="{{ $dataUri ? $dataUri : asset('images/' . $receipt->receipt_image) }}" class="receipt-thumb" alt="Receipt Image">
                                        @else
                                            N/A
                                    @endif
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" style="color: #888">No purchase orders found.</td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            @endif
        </div>
            {{-- page count --}}
        <div class="pagination-wrapper" style="margin-top: 10px; text-align: center; display: flex; flex-direction: row; justify-content: space-between;">
            @if ($receipts->total() > 0)
                    <div style="text-align: center; font-size:14px; color: #555;">
                        Page {{ $receipts->currentPage() }} of {{ $receipts->lastPage() }}
                    </div>
            @endif

            @if ($receipts->hasPages())
                    <div class="pagination-controls" style="display: flex; justify-content: center; align-items: center; gap: 0.5rem; ">

                        {{-- previous --}}
                        @if ($receipts->onFirstPage())
                            <span style="color: #fb8e24; font-size: 14px; padding: 0.5rem 1rem; border: 1px solid #fb8e24; border-radius: 10px;">Previous</span>
                        @else
                            <a href="{{ $receipts->previousPageUrl() }}" 
                            style="color: #fb8e24; text-decoration: none; font-size: 14px; padding: 0.5rem 1rem; border: 1px solid #fb8e24; border-radius: 10px;">
                                Previous
                            </a>
                        @endif

                        {{-- next --}}
                        @if ($receipts->hasMorePages())
                            <a href="{{ $receipts->nextPageUrl() }}" 
                            style="color: #fb8e24; text-decoration: none; font-size: 14px; padding: 0.5rem 1rem; border: 1px solid #fb8e24; border-radius: 10px;">
                                Next
                            </a>
                        @else
                            <span style="color: #ccc; padding: 0.5rem 1rem; font-size: 14px; border: 1px solid #ddd; border-radius: 10px;">Next</span>
                        @endif

                    </div>
            @endif
        </div>
    </div>





@endsection
