
@extends('layouts.main')
@section('content')



   <div class="content-bg">
        <div class="content-header">
            <div class="contents-display">
                <form action="{{ route('logs.list') }}" id="text-search" class="search-text-con" method="GET">
                    <input type="text" name="search" class="search-bar"
                        placeholder="Search by SUP ID. , Supplier, Representative and status"
                        value="{{ request('search') }}"
                        style="outline:none;"
                    >
                    <button type="submit" class="search-btn"><span class="material-symbols-outlined">search</span></button>
                </form>


                <form action="{{ route('logs.list') }}" class="date-search" id="from-to-date" method="GET">
                    <p>Date range</p>
                    <div class="from-to-picker">
                        <div class="month-div">
                            <span>From</span>
                            <input type="date" name="from_date" class="input-date"
                                value="{{ request('from_date', now()->startOfMonth()->format('Y-m-d')) }}"
                                onchange="this.form.submit()">
                        </div>
                        <div class="month-div">
                            <span>To</span>
                            <input type="date" name="to_date" class="input-date"
                                value="{{ request('to_date', now()->endOfMonth()->format('Y-m-d')) }}"
                                onchange="this.form.submit()">
                        </div>
                    </div>
                </form>
            </div>



        </div>

        <div class="content-body" style="background: #fff">
            <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                <thead style="background-color: #fff;">
                    <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                        <th>#</th>
                        <th>Date</th>
                        <th>Performer</th>
                        <th>Action</th>
                        <th>Description</th>
                        <th>Log ID</th>



                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                        {{-- <tr onclick="window.location.href='{{ route('logs.log', ['staff_id' => $staff->staff_id]) }}'"> --}}
                        <tr >
                            <th>{{ $loop->iteration }}</th>
                            <th>{{$log->created_at}}</th>
                            <th>{{$log->user->user_id}}</th>
                            <th>{{$log->action}}</th>
                            <th style="width: 40%; white-space: normal; word-wrap: break-word;">
                                {{ $log->description }}
                            </th>

                            <th>{{$log->log_id}}</th>

                        </tr>
                  

                    @endforeach
                </tbody>
            </table>
       
        </div>

        <div class="pagination-div">
            <p>50 out of 100 <span>2/3</span></p>
            <div>
                <button>Previous</button>
                <button>Next</button>
            </div>
        </div>

   </div>

@endsection

@push('scripts')


@endpush

