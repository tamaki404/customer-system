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

    <title>Document</title>
</head>
<body>
<script src="{{ asset('js/fadein.js') }}"></script>


<div class="customersFrame" style="">


    <form method="GET" action="" class="date-search">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by ID, Store Name, Status, Username">
                <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
    </form>

    

    <div class="titleCount"> 
        <h2>Customer List</h2> 
        <span style=" width: auto; display: flex; flex-direction: row; gap: 5px;">
            <p style="font-weight: bold;">Verified Customers: </p>{{ $verifiedCustomersCount }}
        </span>
    </div>
    
    <!-- Pagination Info -->
    <div style="margin-bottom: 1rem; font-size: 0.9rem; color: #666;">
        Page {{ $users->currentPage() }} of {{ $users->lastPage() }} ({{ $users->total() }} total customers)
    </div>
    <div class="customerList" style="padding: 15px;">
        @if(isset($users) && count($users) > 0)
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#f7f7fa;">
                    <th style="padding:10px 8px;text-align:left;">Customer ID</th>
                    <th style="padding:10px 8px;text-align:left;">Image</th>
                    <th style="padding:10px 8px;text-align:left;">Store Name</th>
                    <th style="padding:10px 8px;text-align:left;">Account Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $i => $user)
                <tr style="border-bottom:1px solid #eee; align-items: center; font-size: 15px;" onclick="window.location='{{ url('/customer_view/' . $user->id) }}'">
                    <td style="padding:10px 8px;; font-size: 15px;">{{  $user->id }}</td>
                    <td style="padding:10px 8px;; font-size: 15px;">
                        @if($user->image)
                            <img src="{{ asset('images/' . $user->image) }}" alt="User Image" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                        @else
                            <span style="color:#aaa;">N/A</span>
                        @endif
                    </td>
                    <td style="padding:10px 8px;; font-size: 15px;">{{ $user->store_name ?? 'N/A' }}</td>
                    <td style="padding:10px 8px;; font-size: 15px;">{{ $user->acc_status ?? 'N/A' }}</td>
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
