@extends('layouts.main')

@push('styles')

@endpush

@section('content')

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
    @if (session('success') || session('error'))
        <div 
            id="flash-message"
            class="flash-message 
                {{ session('success') ? 'alert-success' : 'alert-danger' }}">
            <strong>
                {{ session('success') ? 'Success:' : ' Error:' }}
            </strong>
            {{ session('success') ?? session('error') }}
        </div>


    @endif


    <div class="content-bg" >
        <div class="content-header">
            <div class="contents-display">
                <form action="{{ route('purchaseorders.list') }}" id="text-search" class="search-text-con" method="GET">
                    <input type="text" name="search" class="search-bar"
                        placeholder="Search by PO ID, Customer, Status"
                        value="{{ request('search') }}"
                        style="outline:none;"
                    >
                    <button type="submit" class="search-btn"><span class="material-symbols-outlined">search</span></button>
                </form>
                <form action="{{ route('purchaseorders.list') }}" class="date-search" id="from-to-date" method="GET">
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
            <div style="display: flex; flex-direction: row; justify-content: space-between; margin: 5px;">
                <div class="heading" style="display: flex; flex-direction: row; justify-content: space-between; margin-top: 10px;">
                    <p class="heading">Logs list</p>
                </div>
                <div>
            </div>

        </div>

        <div class="content-body" style="background: #fff">
            <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                <thead style="background-color: #fff;">
                    <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                        <th>#</th>
                        <th>Timestamp</th>
                        <th>User</th>
                        <th>Log ID</th>
                        <th>Action</th>
                        <th>ID</th>
                        <th>Entity</th>
                        <th>Row ID</th>
                    </tr>
                </thead>
                <tbody>                                
                    @foreach ($logs as $log)
                        <tr >
                            <td style="text-align: center; padding: 8px;">{{ $loop->iteration }}</td>
                            <td style="text-align: center; padding: 8px;">{{ $log->created_at->format('F j,y g:i a') }}</td>
                            <td style="text-align: center; padding: 8px;">{{ $log->staff->lastname }}, {{ $log->staff->firstname }} {{ $log->staff->middlename }}</td>
                            <td style="text-align: center; padding: 8px;">{{ $log->log_id }}</td>
                            <td style="text-align: center; padding: 8px;">{{ $log->action }}</td>
                            <td style="text-align: center; padding: 8px;">#{{ $log->description }}</td>
                            <td style="text-align: center; padding: 8px;">{{ $log->entity }}</td>
                            <td style="text-align: center; padding: 8px;">{{ $log->entity_id }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination-div" style="margin-top: 15px;">
            <p>Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} entries</p>
            {{ $logs->links() }}
       </div>
    </div>


@endsection

@push('scripts')


@endpush
