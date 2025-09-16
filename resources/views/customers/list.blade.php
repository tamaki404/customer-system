@extends('layouts.main')

@section('content')
   <div class="content-bg">
        <div class="content-header">
            <p class="heading">Customer List</p>
            <div>

            </div>
        </div>
        <div class="content-body">
            <table style="width:100%; border-collapse:collapse; border: 1px solid #f7f7fa;">
                <thead style="background-color: #f9f9f9;">
                    <tr style="background:#f7f7fa; text-align: center; ">
                        <th>#</th>
                        <th>SUP ID.</th>
                        <th>Supplier</th>
                        <th>Representative</th>
                        <th>Mail</th>
                        <th>Status</th>
                        <th>Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($suppliers as $supplier)
                        <tr>
                            <th>{{ $loop->iteration }}</th>
                            <td>{{ $supplier->supplier_id }}</td>
                            <td>{{ $supplier->company_name }}</td>
                            <td>{{ $supplier->representative->rep_last_name }}sd</td>
                            <td>{{ $supplier->user->email_address }}</td>
                            <td>{{ $supplier->user->status }}</td>
                            <td>0.00</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
       
        </div>
   </div>
@endsection
