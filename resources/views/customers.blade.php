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

        <div class="title-search" style="align-content: center">
            <h2 style="margin: 0;">Customers</h2> 

            <form method="GET" action="" class="date-search">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by ID, Store Name, Status, Username">
                <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
            </form>

        </div>

    <section class="status-cards-list" style="margin: 5px">
        <a class="status-card {{ $status === 'Active' ? 'active' : '' }}" 
        href="{{ route('customers', ['status' => 'Active']) }}">
            <p class="card-title">Activated</p>
            <div class="card-content">
                <h1 class="card-count">{{$activatedCustomers}}</h1>
                @if ($newCustomers > 0)
                    <span class="card-add-count">{{$newCustomers}}</span>
                @endif
            </div>
        </a>

        <a class="status-card {{ $status === 'Pending' ? 'active' : '' }}" 
        href="{{ route('customers', ['status' => 'Pending']) }}">
            <p class="card-title">Pending</p>
            <div class="card-content">
                <h1 class="card-count">{{$pendingCustomers}}</h1>
                @if ($pendingCustomers > 0)
                    <span class="card-add-count">{{$newPending}}</span>
                @endif
            </div>
        </a>

        <a class="status-card {{ $status === 'Suspended' ? 'active' : '' }}" 
        href="{{ route('customers', ['status' => 'Suspended']) }}">
            <p class="card-title">Suspended</p>
            <div class="card-content">
                <h1 class="card-count">{{$suspendedCustomers}}</h1>
            </div>
        </a>
    </section>


    
    <!-- Pagination Info -->
    <div style="font-size: 14px; color: #666;" class="page-count">
        Page {{ $users->currentPage() }} of {{ $users->lastPage() }} ({{ $users->total() }} total customers)
    </div>
    

    <div class="customerList" style="padding: 15px; display: flex; justify-content: center; align-items: start;">
        @if(isset($users) && count($users) > 0)

        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#f7f7fa; text-align: center;">
                    <th style="width: 50px; padding: 10px;">#</th> 
                    <th style="width: 100px;"></th>
                    <th style="width: 200px;">Store</th>
                    <th style="width: 80px;">No. of Orders</th>
                    <th style="width: 150px;">Total Orders</th>
                    <th style="width: 120px; ">Status</th>
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

                        <td style="width: 150px; text-align: center;">
                            <span class="{{ $statusClasses[$user->acc_status] ?? 'status-default' }}">
                                {{ $user->acc_status }}
                            </span>
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
            <p style="font-size:14px; margin-top: 50px; color: #666;">No customers found.</p>
        @endif
    </div>

</div>

</body>
</html>
@endsection
