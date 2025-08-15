@extends('layout')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/customers.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <title>Customers</title>
</head>
<body>
<script src="{{ asset('js/fadein.js') }}"></script>


<div class="customersFrame" style="">


    <form method="GET" action="" class="date-search">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by ID, Store Name, Status, Username">
        <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
    </form>

    <div class="titleCount"> 
        <h2>Customers</h2> 
    </div>
    
    <!-- Pagination Info -->
    <div style="margin-bottom: 1rem; font-size: 15px; color: #666;" class="page-count">
        Page {{ $users->currentPage() }} of {{ $users->lastPage() }} ({{ $users->total() }} total customers)
    </div>
    <div class="customerList" style="padding: 15px;">
        @if(isset($users) && count($users) > 0)
        {{-- <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#f7f7fa;">
                    <th style="padding:10px 8px;text-align:left;">Customer ID</th>
                    <th style="padding:10px 8px;text-align:left;">Image</th>
                    <th style="padding:10px 8px;text-align:left;">Store Name</th>
                    <th style="padding:10px 8px;text-align:left;">Account Status</th>
                    <th style="padding:10px 8px;text-align:left;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $i => $user)
                <tr style="border-bottom:1px solid #eee; align-items: center;" onclick="window.location='{{ url('/customer_view/' . $user->id) }}'">
                    <td style="padding:10px 8px; font-size: 14px;">{{  $user->id }}</td>
                    <td style="padding:10px 8px;">
                        @if($user->image)
                            @php
                                $isBase64 = !empty($user->image_mime);
                                $imgSrc = $isBase64 ? ('data:' . $user->image_mime . ';base64,' . $user->image) : asset('images/' . $user->image);
                            @endphp
                            <img src="{{ $imgSrc }}" alt="User Image" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                        @else
                            <span style="color:#aaa;">N/A</span>
                        @endif
                    </td>
                    <td style="padding:10px 8px; font-size: 14px;">{{ $user->store_name ?? 'N/A' }}</td>
                    <td style="padding:10px 8px;">
                        <span class="status-badge" style="
                            padding: 4px 8px;
                            border-radius: 12px;
                            font-weight: bold;
                             font-size: 14px;
                            letter-spacing: 0.5px;
                            @if($user->acc_status === 'Active')
                                background: #d4edda;
                                color: #155724;
                                border: 1px solid #c3e6cb;
                            @elseif($user->acc_status === 'accepted')
                                background: #d1ecf1;
                                color: #0c5460;
                                border: 1px solid #bee5eb;
                            @elseif($user->acc_status === 'suspended')
                                background: #f8d7da;
                                color: #721c24;
                                border: 1px solid #f5c6cb;
                            @else
                                background: #fff3cd;
                                color: #856404;
                                border: 1px solid #ffeaa7;
                            @endif
                        ">
                            @if($user->acc_status === 'Active')
                                {{ $user->acc_status }}
                            @elseif($user->acc_status === 'accepted')
                                 {{ $user->acc_status }}
                            @elseif($user->acc_status === 'suspended')
                                {{ $user->acc_status }}
                            @else
                                {{ $user->acc_status ?? 'Pending' }}
                            @endif
                        </span>
                    </td>
                    <td style="padding:10px 8px;">
                        @if($user->acc_status !== 'Active')
                            <form action="{{ url('/customer/activate/' . $user->id) }}" method="POST" style="display: inline-block;">
                                @csrf
                                <button type="submit" class="quick-activate-btn" style="
                                    background: #28a745;
                                    color: white;
                                    padding: 6px 12px;
                                    border: none;
                                    border-radius: 4px;
                                    font-size: 14px;
                                    font-weight: bold;
                                    cursor: pointer;
                                    transition: all 0.3s ease;
                                    box-shadow: rgba(0, 0, 0, 0.15) 1.95px 1.95px 2.6px;
                                " onmouseover="this.style.background='#218838'" onmouseout="this.style.background='#28a745'" onclick="event.stopPropagation();">
                                    Activate
                                </button>
                            </form>
                        @else
                            <span style="color: #28a745; font-size: 14px; font-weight: bold;"></span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table> --}}

        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#f7f7fa; text-align: center;">
                    <th style="width: 50px; padding: 10px;">#</th> 
                    <th style="width: 100px;"></th>
                    <th style="width: 200px;">Store</th>
                    <th style="width: 80px;">No. of Orders</th>
                    <th style="width: 150px;">Total Orders</th>
                    <th style="width: 120px;">Status</th>
                    <th style="width: 140px;">Last Order</th>
                </tr>
                
            </thead>
            <tbody>
                @foreach($users as $i => $user)
                    @php 
                        $statusClasses = [
                            'Active' => 'status-active',
                            'Pending' => 'status-pending',
                            'Suspended' => 'status-suspended',
                        ];
                    @endphp
                    <tr style="height: 50px; text-align: center; cursor:pointer;" onclick="window.location='{{ url('/customer_view/' . $user->id) }}'">
                        <td style="padding:10px 8px; font-size: 13px;">
                            {{ $loop->iteration }}
                        </td>
                        <td>
                             @if($user->image)
                            @php
                                $isBase64 = !empty($user->image_mime);
                                $imgSrc = $isBase64 ? ('data:' . $user->image_mime . ';base64,' . $user->image) : asset('images/' . $user->image);
                            @endphp
                            <img src="{{ $imgSrc }}" alt="User Image" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                        @else
                            <span style="color:#aaa;">N/A</span>
                        @endif

                        </td>
                        <td style="padding:10px 8px; font-size: 13px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
                            {{$user->store_name}}
                        </td>
                        <td style="padding:10px 8px; font-size: 13px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
                            {{ $user->orderCount }}
                        </td>
                        <td style="padding:10px 8px; font-size: 13px;">
                            ₱{{ number_format($user->totalOrders, 2) }}
                        </td>
                        <td>
                           <div style="display: flex; align-items: center; justify-content: center;" class="{{ $statusClasses[$user->acc_status] ?? 'status-default' }}">
                            ● {{ $user->acc_status }}
                           </div>
                        </td>
                        <td style="padding:10px 8px; font-size: 13px;">
                            {{ $user->lastOrder ? \Carbon\Carbon::parse($user->lastOrder)->format('F j, Y') : 'No orders' }}
                        </td>
                                         
                    </tr>


                
                @endforeach
            </tbody>
        </table>
        
        <!-- Pagination Controls -->
        <div class="pagination-wrapper" style="margin-top: 2rem; text-align: center;">
            @if($users->hasPages())
                <div class="pagination-controls" style="display: flex; justify-content: center; align-items: center; gap: 1rem;">
                    @if($users->onFirstPage())
                        <span style="color: #ccc; cursor: not-allowed;">Previous</span>
                    @else
                        <a href="{{ $users->previousPageUrl() }}" 
                           style="color: #1976d2; text-decoration: none; padding: 0.5rem 1rem; border: 1px solid #1976d2; border-radius: 4px;">
                            Previous
                        </a>
                    @endif
                    
                    @if($users->hasMorePages())
                        <a href="{{ $users->nextPageUrl() }}" 
                           style="color: #1976d2; text-decoration: none; padding: 0.5rem 1rem; border: 1px solid #1976d2; border-radius: 4px;">
                            Next
                        </a>
                    @else
                        <span style="color: #ccc; cursor: not-allowed;">Next</span>
                    @endif
                </div>
            @endif
        </div>
        @else
            <p>No customers found.</p>
        @endif
    </div>

</div>

</body>
</html>
@endsection
