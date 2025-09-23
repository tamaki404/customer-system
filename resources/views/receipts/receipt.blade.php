
@extends('layouts.main')


@push('styles')
@endpush



@section('content')


    {{-- purchase order placing --}}
    <div class="modal fade" id="modify-action" tabindex="-1" aria-labelledby="requestActionLabel" aria-hidden="true" >
        <div class="modal-dialog" style="width: auto">
            <form class="modal-content" method="POST" enctype="multipart/form-data" style="width: 800px" action="{{ route('receipts.action', $receipt->receipt_id) }}">
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

                <!-- Hidden field for PO ID -->                
                <div class="modal-header">
                    <p class="modal-title" id="requestActionLabel">Receipt actions</p>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body" style="width: auto">
                    <p class="note-notify">
                        <span class="material-symbols-outlined"> info </span>
                        <span>Any changes will be logged.</span>
                    </p>

                    <div style="width: auto">
                        <table style="width:100%; border-collapse:collapse; border: 1px solid #f7f7fa;">
                            <thead style="background-color: #f9f9f9;">
                                <tr style="background:#f7f7fa; text-align: center; height: 30px">
                                    <td>Receipt ID</td>
                                    <td>Order ID</td>
                                    <td>Order amount</td>
                                    <td>Amount paying</td>

                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $receipt->receipt_id }}</td>
                                    <td><button>View order</button></td>
                                    <td>
                                        {{ number_format($receipt->order->total_amount) }}
                                    </td>
                                    <td><input type="number" name="amount"></td>

                                </tr>
                                <input type="hidden" name="order_id" value="{{$receipt->order_id}}">
                                <input type="hidden" name="receipt_id" value="{{$receipt->receipt_id}}">

                            </tbody>
                        </table>
                    </div>

                    <select name="status" id="" required>
                        <option value="Verified">Verify receipt</option>
                        <option value="Rejected">Reject receipt</option>
                    </select>
                    
                    <div class="form-group">
                        <input type="text" name="remarks" maxlength="200">
                    </div>
      
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit"  class="btn btn-primary">Confirm receipt</button>
                </div>
            </form>
        </div>
    </div>

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

   <div class="content-bg" >
        <div class="content-header">
            <div class="contents-display">
                <p>
                    <a href="{{ route('receipts.list') }}">< Receipts list</a>
                </p>
            </div>

            <div class="title-actions">
                <p class="heading">Receipt</p>

                <div>
                    <button data-bs-toggle="modal" data-bs-target="#modify-action" class="btn-transition">File an action</button>
                </div>
            </div>


        </div>

        <div class="content-body" style="padding: 10px; border: none; height: auto;">
            <div class="purchase-order-div" style="display: flex; flex-direction: row; gap: 20px">
                <div class="po-image-div">
                    @php
                        $imgSrc = $receipt->image 
                            ? ('data:' . $receipt->image_mime_type . ';base64,' . base64_encode($receipt->image))
                            : asset('assets/default-image.jpg');
                    @endphp
                    <img class="supplier-image" src="{{ $imgSrc }}" alt="Profile Image"> 
                </div>


            </div>

       
        </div>


   </div>

@endsection



@push('scripts')



@endpush