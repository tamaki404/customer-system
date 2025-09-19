@extends('layouts.main')


@push('styles')
    <link rel="stylesheet" href="{{ asset('css/views/customer.css') }}">
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
            <div class="contents-display">
                <p>
                    <a href="{{ route('staffs.list') }}">< Orders list</a>
                </p>
            </div>

            <div class="title-actions">
                <p class="heading">Order</p>

                {{-- <div>
                    <button data-bs-toggle="modal" data-bs-target="#modify-action" class="btn-transition">Modify account</button>
                </div> --}}

            </div>


        </div>

        <div class="content-body" style="padding: 10px; border: none; height: auto;">
          
         
       
        </div>


   </div>
@endsection



@push('scripts')
    <script src="{{ asset('js/global/password.js') }}"></script>
    <script src="{{ asset('js/global/two_mb.js') }}"></script>



@endpush