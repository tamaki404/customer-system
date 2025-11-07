
@extends('layouts.main')


@push('styles')
    <link rel="stylesheet" href="{{ asset('css/receipt.css') }}">

@endpush
@section('content')

    {{-- payment verify --}}
    @if(Auth()->user()->role !== "Customer" && $payment->status === 'Pending')
        <div class="modal fade" id="verify-payment" tabindex="-1" aria-labelledby="requestActionLabel" aria-hidden="true" >
            <div class="modal-dialog" style="width: auto">
                <form class="modal-content" method="POST" action="{{ route('pym.verify') }}" style="width: 800px">
                    @csrf


                    <div class="modal-header">
                        <p class="modal-title">Verify payment</p>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <p class="note-notify">
                            <span class="material-symbols-outlined"> info </span>
                            <span>Verified receipts will deduct from the total amount of the tagged order.</span>
                        </p>

                        <div class="mt-2">
                            <label class="form-label">Set status for this receipt <span class="text-danger">*</span></label>
                            <select name="status" id="statusSelect"  required>
                                <option value="">-- Select status --</option>
                                <option value="Verified">Verify receipt</option>
                                <option value="Rejected">Reject receipt</option>
                            </select>
                        </div>

                        {{-- Verified Section --}}
                        <div id="verifiedSection" style="border-radius: 5px;">

                            <div class="form-group mt-2">
                                <label class="form-label">Amount Paying <span class="text-danger">*</span></label>
                              <input type="number" 
                                name="total_amount" 
                                id="amountInput"
                                step="0.01"
                                min="0.01"
                                placeholder="Enter amount">

                            </div>
                        </div>



                        <input type="hidden" name="payment_id" value="{{ $payment->payment_id }}">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="submitBtn" class="btn btn-primary" >Confirm Action</button>
                    </div>


                </form>
            </div>
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
                <p class="heading">Payment #{{ $payment->payment_id }} </p> <p>{{ $payment->status }}</p>
                @if(Auth()->user()->role !== "Customer" && $payment->status === 'Pending')
                    <div>
                        <button data-bs-toggle="modal" data-bs-target="#verify-payment" class="btn-transition">Verify payment</button>
                    </div>
                @endif
            </div>

        </div>




    </div>



   </div>

@endsection



@push('scripts')




@endpush