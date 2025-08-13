<style>
    .receipt-table th, .receipt-table td {
        width: 120px;
        max-width: 120px;
        min-width: 120px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>


@extends('layout')

@section('content')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/receipts.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fadein.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Document</title>
    <link rel="stylesheet" href="{{ asset('css/open-modal.css') }}">
</head>
<body>
<script src="{{ asset('js/fadein.js') }}"></script>


<div id="myModal" class="modal">
  <div class="modal-content">
    <span class="close-btn">&times;</span>
     @if(auth()->user()->user_type === 'Customer')
        <div class="form-section">
            <h3 class="form-title" style="margin: 1px">Submit New Receipt</h3>
            <p>Please upload your receipt below. Ensure all information is accurate before submission.</p>
            <form action="/submit-receipt" class="receipt-form" method="POST" enctype="multipart/form-data">
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
                                // Remove all commas before submitting
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

                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            const paymentInput = document.getElementById('payment_method');
                            const invoiceInput = document.getElementById('invoice_number');

                            function toggleInvoiceField() {
                                const method = paymentInput.value.trim().toLowerCase();
                                if (method === 'gcash' || method === 'card') {
                                    invoiceInput.disabled = false;
                                } else {
                                    invoiceInput.disabled = true;
                                }
                            }

                            toggleInvoiceField(); 
                            paymentInput.addEventListener('input', toggleInvoiceField);
                        });
                    </script>




                    <div class="full">
                        <label>Orders</label>
                        <textarea name="notes" rows="3" placeholder="You can list your orders here"></textarea>
                    </div>

                </div>

                <button type="submit" class="submit-btn" id="submitBtn" style="color: #333; font-size: 15px;">Submit Receipt</button>
            </form>
        </div>
    @endif

    
  </div>
</div>



    <div class="receipt-wrapper">
        <div class="wrapper-title">
            <form action="/date-search" id="searchCon" style="margin-left: 10px" class="date-search" method="GET">
                <input type="text" style="    width: 390px; border: none;" name="search" class="search-bar" placeholder="Search receipt #, customer, amount, or date" value="{{ request('search') }}">
                <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
            </form>
            <form action="/date-search" class="date-search" id="from-to-date" method="GET">
                <span>From</span>
                <input type="date" name="from_date" class="input-date" value="{{ request('from_date', now()->startOfMonth()->format('Y-m-d')) }}" onchange="this.form.submit()">
                <span >To</span>
                <input type="date" name="to_date" class="input-date" value="{{ request('to_date', now()->endOfMonth()->format('Y-m-d')) }}" onchange="this.form.submit()">
            </form>
        </div>
        <div class="receipt-container">

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

        {{-- <div class="status-tabs">
            @foreach($tabStatuses as $label => $value)
                @php
                    $isActive = ($value === null && empty($currentStatus)) || ($value !== null && $currentStatus === $value);
                    $params = $value ? array_merge($baseParams, ['status' => $value]) : $baseParams;
                @endphp
                <a href="{{ route('date.search', $params) }}" class="status-tab{{ $isActive ? ' active' : '' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div> --}}

        @if($receipts->where('status', 'Verified')->count())
            <div style="margin-bottom: 1rem; font-size: 1.1rem; color: #1976d2; font-weight: 600;">
                Total Amount for Verified Receipts: ₱{{ number_format($receipts->where('status', 'Verified')->sum('total_amount'), 2) }}
            </div>
        @endif

        <!-- Pagination Info -->
        <div style="margin-bottom: 1rem; font-size: 0.9rem; color: #666;">
            Page {{ $receipts->currentPage() }} of {{ $receipts->lastPage() }} ({{ $receipts->total() }} total receipts)
        </div>


            @isset($receipts)
               @if(auth()->user()->user_type === 'Staff' || auth()->user()->user_type === 'Admin')
                    <div class="status-tabs">
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
                    @if($receipts->isEmpty())
                        <div style="text-align:center; margin:2rem 0; color:#888; font-size:1.1rem;">No receipts found.</div>
                    @else
                        <div class="table-wrapper" style="overflow: scroll">
                            <table class="receipt-table">
                                <thead>
                                    <tr>
                                        <th>Receipt #</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Purchase date</th>
                                        <th>Status</th>
                                        <th>Verified by</th>
                                        <th>Image</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($receipts as $receipt)
                                        <tr onclick="window.location='{{ url('/receipts_view/' . $receipt->receipt_id) }}'">
                                            <td>{{ $receipt->receipt_number }}</td>
                                            <td>{{ $receipt->store_name }}</td>
                                            <td>₱{{ number_format($receipt->total_amount, 2) }}</td>
                                            <td>{{ \Carbon\Carbon::parse($receipt->purchase_date)->format('F j, Y') }}</td>
                                            {{-- <td><span class="status {{ strtolower($receipt->status) }}"
                                                >{{ $receipt->status }}</span>
                                            </td> --}}
                                            <td style="color:
                                                @if($receipt->status === 'Verified') green
                                                @elseif($receipt->status === 'Pending') #333
                                                @elseif($receipt->status === 'Cancelled') orange
                                                @elseif($receipt->status === 'Rejected') red
                                                @else #333
                                                @endif
                                            ;">{{ $receipt->status }}</td>
                                            <td>{{ $receipt->verified_by }}</td>
                                            <td>
                                                @if($receipt->receipt_image)
                                                    @php
                                                        $isBase64 = !empty($receipt->receipt_image_mime);
                                                        $dataUri = $isBase64 ? ('data:' . $receipt->receipt_image_mime . ';base64,' . $receipt->receipt_image) : null;
                                                    @endphp
                                                    <img src="{{ $dataUri ? $dataUri : asset('images/' . $receipt->receipt_image) }}" class="receipt-thumb" alt="Receipt Image">
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        
                        <!-- Pagination Controls -->
                        <div class="pagination-wrapper">
                            @if($receipts->hasPages())
                                <div class="pagination-info">
                                    Page {{ $receipts->currentPage() }} of {{ $receipts->lastPage() }}
                                </div>
                                
                                <div class="pagination-controls">
                                    @if($receipts->onFirstPage())
                                        <span>Previous</span>
                                    @else
                                        <a href="{{ $receipts->previousPageUrl() }}">
                                            Previous
                                        </a>
                                    @endif
                                    
                                    @if($receipts->hasMorePages())
                                        <a href="{{ $receipts->nextPageUrl() }}">
                                            Next
                                        </a>
                                    @else
                                        <span>Next</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif
                @elseif(auth()->user()->user_type === 'Customer')
                    <div class="title-wrapper">
                        <h2 class="title">Your Receipts</h2>  
                        <button id="openModalBtn">Submit a Receipt</button>
                    </div>
                    <div class="status-tabs">
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
                    @if($receipts->isEmpty())
                        <div style="text-align:center; margin:2rem 0; color:#888; font-size:1.1rem;">No receipts found.</div>
                    @else
                        <div class="table-wrapper">
                            <table class="receipt-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Receipt #</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Verified by</th>
                                        <th>Image</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($receipts as $receipt)
                                        <tr onclick="window.location='{{ url('/receipts_view/' . $receipt->receipt_id) }}'">
                                            <td>{{ \Carbon\Carbon::parse($receipt->created_at)->format('F j, Y') }}</td>
                                            <td>{{ $receipt->receipt_number }}</td>
                                            <td>₱{{ number_format($receipt->total_amount, 2) }}</td>
                                            <td><span class="status {{ strtolower($receipt->status) }}">{{ $receipt->status }}</span></td>
                                            <td>{{ $receipt->verified_by }}</td>
                                            <td>
                                                @if($receipt->receipt_image)
                                                    @php
                                                        $isBase64 = !empty($receipt->receipt_image_mime);
                                                        $dataUri = $isBase64 ? ('data:' . $receipt->receipt_image_mime . ';base64,' . $receipt->receipt_image) : null;
                                                    @endphp
                                                    <img src="{{ $dataUri ? $dataUri : asset('images/' . $receipt->receipt_image) }}" class="receipt-thumb" alt="Receipt Image">
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination Controls for Customers -->
                        <div class="pagination-wrapper">
                            @if($receipts->hasPages())
                                <div class="pagination-info">
                                    Page {{ $receipts->currentPage() }} of {{ $receipts->lastPage() }}
                                </div>
                                
                                <div class="pagination-controls">
                                    @if($receipts->onFirstPage())
                                        <span>Previous</span>
                                    @else
                                        <a href="{{ $receipts->previousPageUrl() }}">
                                            Previous
                                        </a>
                                    @endif
                                    
                                    @if($receipts->hasMorePages())
                                        <a href="{{ $receipts->nextPageUrl() }}">
                                            Next
                                        </a>
                                    @else
                                        <span>Next</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif
                @endif
            @else
                <div style="text-align:center; margin:2rem 0; color:#888; font-size:1.1rem;">No receipts for this month.</div>
            @endisset

            @if(session('success'))
                <div class="alert success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert error">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

        
        </div>
    </div>

<script src="{{ asset('scripts/open-modal.js') }}"></script>




</body>
</html>




@endsection
