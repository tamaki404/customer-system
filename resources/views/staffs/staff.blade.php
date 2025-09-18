@extends('layouts.main')


@push('styles')
    <link rel="stylesheet" href="{{ asset('css/views/customer.css') }}">
@endpush



@section('content')


    <div class="modal fade" id="modify-action" tabindex="-1" aria-labelledby="requestActionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content">
        
            <div class="modal-header">
                <p class="modal-title" id="requestActionLabel">Staff modify action</p>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
         
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" >Submit action</button>
            </div>
        
        </form>
    </div>
    </div>



   <div class="content-bg" >
        <div class="content-header">
            <div class="contents-display">
                <p>
                    <a href="{{ route('staffs.list') }}">< Staffs list</a>
                </p>
            </div>

            <div class="title-actions">
                <p class="heading">Staff's profile</p>

                <div>
                    <button data-bs-toggle="modal" data-bs-target="#modify-action" class="btn-transition">Modify account</button>
                </div>

            </div>


        </div>

        <div class="content-body" style="padding: 10px; border: none; height: auto;">
            <div class="profile-upper">
                @php
                    $imgSrc = auth()->user()->image 
                        ? ('data:' . auth()->user()->image_mime_type . ';base64,' . base64_encode(auth()->user()->image))
                        : asset('images/default-avatar.png');
                @endphp
                <img class="supplier-image" src="{{ $imgSrc }}" alt="Profile Image">   
            

            </div>

            <div class="profile-mid">

                <div class="authorized-staffs" style="margin-top: 10px">
                    <p style="margin-bottom: 5px">Handled suppliers</p>
                    <di class="rep-sign-tables" style="width: 100%; display: flex; flex-direction: row; gap: 5px;">
                        <div class="authorized-rep">
                            <table style="width:100%; border-collapse:collapse; border: 1px solid #f7f7fa;">
                                <thead style="background-color: #f9f9f9;">
                                    <tr style="background:#f7f7fa; text-align: center; height: 30px">
                                        <th>#</th>
                                        <th>Company name</th>
                                        <th>Supplier ID</th>
                                        <th>Order</th>
                                        <th>Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($staff as $s)
                                        <tr>
                                            <td>--</td>
                                            <td>{{ optional($s->supplier)->company_name ?? '--' }}</td>
                                            <td>{{ optional($s->supplier)->supplier_id ?? '--' }}</td>
                                            <td>--</td>
                                            <td>--</td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>

                    </di>


                </div>

            </div>

         
       
        </div>


   </div>
@endsection



@push('scripts')

@endpush