@extends('layouts.main')


@push('styles')
    <link rel="stylesheet" href="{{ asset('css/views/heads/head.css') }}">
@endpush



@section('content')


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
                <div class="title-actions" style="display: flex; flex-direction: column;">
                    <p class="heading">Groups</p>
                    <p class="head-info" style="width: 500px">
                        <span class="material-symbols-outlined">info</span>
                        <span >Here is a list of the authorized representatives you added during signup. You can create accounts for them with selected privileges.</span>
                    </p>
                </div>
            </div>
            <div class="table-con">
                <p>
                    <span class="material-symbols-outlined"> person_apron </span>
                    Authorized Representatives
                </p>      
                    <div class="content-body" style="background: #fff; height: auto;">

                        <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                            <thead style="background-color: #fff;">
                                <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                                    <th>#</th>
                                    <th>Rep. ID</th>
                                    <th>Name</th>
                                    <th>Role type</th>
                                    <th>Permissions</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reps as $rep)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>--</td>
                                        <td>
                                            {{$rep->rep_lastname}}
                                            {{$rep->rep_firstname}}
                                            {{$rep->rep_middlename}}
                                        </td>
                                        <td>{{ $rep->auth_position }}</td>
                                        <td>--</td>
                                        <td style="display: flex; align-items: center; justify-content: center;">
                                            <button class="btn-transition" data-bs-toggle="modal" data-bs-target="#modify-modal">
                                                <span style="font-size: 15px; margin: 0" class="material-symbols-outlined">manage_accounts</span>
                                                Modify account
                                            </button>                                        
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                </div>
            </div>

            <div class="table-con">
                    <p>
                        <span class="material-symbols-outlined"> stylus_note </span>
                        Signatories to accept deliveries ad sign invoices
                    </p>
                    <div class="content-body" style="background: #fff; height: auto;">

                        <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                            <thead style="background-color: #fff;">
                                <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                                    <th>#</th>
                                    <th>Rep. ID</th>
                                    <th>Name</th>
                                    <th>Role type</th>
                                    <th>Permissions</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reps as $rep)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>--</td>
                                        <td>
                                            {{$rep->rep_lastname}}
                                            {{$rep->rep_firstname}}
                                            {{$rep->rep_middlename}}
                                        </td>
                                        <td>{{ $rep->auth_position }}</td>
                                        <td></td>
                                        <td></td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                
            </div>
        </div>
@endsection



@push('scripts')

@endpush