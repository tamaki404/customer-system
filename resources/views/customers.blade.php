@extends('layout')

@section('content')
<div class="customersFrame" style="max-width:900px;margin:40px auto;padding:30px;background:#fff;border-radius:10px;box-shadow:0 2px 12px #0001;">
    <h2 style="margin-bottom:24px;">Customer List</h2>
    @if(isset($users) && count($users) > 0)
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="background:#f7f7fa;">
                <th style="padding:10px 8px;text-align:left;">Customer ID</th>
                <th style="padding:10px 8px;text-align:left;">Image</th>
                <th style="padding:10px 8px;text-align:left;">Username</th>
                <th style="padding:10px 8px;text-align:left;">Store Name</th>
                <th style="padding:10px 8px;text-align:left;">Account Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $i => $user)
            <tr style="border-bottom:1px solid #eee; align-items: center;w" onclick="window.location='{{ url('/customer_view/' . $user->id) }}'">
                <td style="padding:10px 8px;">{{ $i+1 }}</td>
                <td style="padding:10px 8px;">
                    @if($user->image)
                        <img src="{{ asset('images/' . $user->image) }}" alt="User Image" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                    @else
                        <span style="color:#aaa;">N/A</span>
                    @endif
                </td>
                <td style="padding:10px 8px;">{{ $user->username }}</td>
                <td style="padding:10px 8px;">{{ $user->store_name ?? 'N/A' }}</td>
                <td style="padding:10px 8px;">{{ $user->acc_status ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
        <p>No customers found.</p>
    @endif
</div>
@endsection
