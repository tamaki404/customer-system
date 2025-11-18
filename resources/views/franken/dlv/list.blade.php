@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{asset('css/staffs/list.css')}}">
    <link rel="stylesheet" href="{{asset('css/franken/pr/list.css')}}">
    <link rel="stylesheet" href="{{ asset('css/views/dropdown.css') }}">

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

    
        <div class="content-bg">
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

                </div>

                <div class="heading" style="display: flex; flex-direction: row; justify-content: space-between; margin-top: 10px;">
                    <p class="heading">Delivery summary ({{$delivery->count()}})</p>
                </div>
                @if ($user->role === "Customer")
                    <div class="content-body" style="background: #fff">
                        <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                            <thead style="background-color: #fff;">
                                <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                                    <th>#</th>
                                    <th>Updated on</th>
                                    <th>Heads/Kilos</th>
                                    <th>PO ID</th>
                                    <th>Scheduled</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>    
                                @if ($delivery)
                                    @foreach ($delivery as $del)
                                    <!-- Delivery receipt modal -->
                                        <div class="modal fade" id="pdfModal-{{ $del->delivery_id }}" tabindex="-1">
                                            <div class="modal-dialog modal-xl" > 
                                                <div class="modal-content" style="width: 50vw">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">#{{ $del->delivery_id }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body p-0" style="height: 80vh;">
                                                        <iframe src="{{ route('dlv.receipt', $del->delivery_id) }}"
                                                                style="width: 100%; height: 100%; border: none;"></iframe>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <!--Process delivery modal -->
                                        <div class="modal fade" id="process-{{ $del->delivery_id }}" tabindex="-1">
                                            <div class="modal-dialog" > 
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">

                                                            <p style="font-size: 14px; margin-bottom: 10px; flex-direction: column; display: flex;">
                                                                <span> Process delivery <strong>#{{ $del->delivery_id }}</strong></span>
                                                                <small> PO # <strong>{{ $del->delivery_id }}</strong></small>
                                                            </p>

                                                            <p class="info">
                                                                <span class="material-symbols-outlined icon">
                                                                info
                                                                </span>
                                                                <span>You may change the quantity of the items below lesser than the planned amount. Editing this will adjust the other pending deliveries.</span>
                                                            </p>

                                                            <div>
                                                                <p>
                                                                    <span>{{ $del->customer->company_name }}</span>
                                                                </p>
                                                                <p>
                                                                    <span>Created date</span>
                                                                    <span>{{ $del->created_at }}</span>
                                                                </p>
                                                                <p>
                                                                    <span>Delivery date</span>
                                                                    <span>{{ $del->delivery_date }}</span>
                                                                </p>
                                                            </div>

                                                            <p style="margin: 5px">This delivery has (<strong>{{ $del->Delitems->count() }}</strong>) item/s</p>

                                                            <div class="delivery-inputs" style="display: flex; flex-direction: row;  padding: 10px; box-shadow: rgba(0, 0, 0, 0.02) 0px 1px 3px 0px, rgba(27, 31, 35, 0.15) 0px 0px 0px 1px; border-radius: 10px;">
                                                                @foreach ($del->Delitems as $item)
                                                                    <p>
                                                                        <span>#{{ $loop->iteration }}</span>
                                                                        <strong>{{ $item->product->name }}</strong>
                                                                    </p>
                                                                    <div class="input-divs" style="display: flex; flex-direction: row; gap: 10px;">
                                                                        @if ($item->product->measurement_type === "Heads")
                                                                            <div>
                                                                                <input type="text" placeholder="{{ $item->planned_heads }}" name="planned_heads[{{ $item->delivery_item_id }}]" value="{{ $item->planned_heads }}" max="{{ $item->planned_heads }}">H
                                                                            </div>
                                                                        @elseif ($item->product->measurement_type === "Kilos")
                                                                            <div>
                                                                                <input type="text" placeholder="{{ $item->planned_kilos }}" name="planned_kilos[{{ $item->delivery_item_id }}]" value="{{ $item->planned_kilos }}" max="{{ $item->planned_kilos }}">K
                                                                            </div>
                                                                        @elseif ($item->product->measurement_type === "Heads&Kilos")
                                                                            <div>
                                                                                <input type="text" placeholder="{{ $item->planned_heads }}" name="planned_heads[{{ $item->delivery_item_id }}]" value="{{ $item->planned_heads }}" max="{{ $item->planned_heads }}">H
                                                                                <input type="text" placeholder="{{ $item->planned_kilos }}" name="planned_kilos[{{ $item->delivery_item_id }}]" value="{{ $item->planned_kilos }}" max="{{ $item->planned_kilos }}">K
                                                                            </div>
                                                                        @endif
                                                                    </div>

                                                                @endforeach

                                                            </div>
                                                        
                                                    </div>  
                                                    <div class="modal-footer">
                                                        <button type="button" class="yellow-btn" style="border-radius:5px;">Process order</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{ $del->updated_at->format('F j, y g:i a') }}</td>
                                            <td>
                                                @php
                                                    $sum_heads = $del->items->sum('planned_heads');
                                                    $sum_kilos = $del->items->sum('planned_kilos');
                                                @endphp
                            
                                                {{ $sum_heads}} - {{ $sum_kilos}}
                                            </td>
                                            <td>#{{ $del->po_id }}</td>
                                            <td>{{$del->delivery_date->format('F j, y')}}</td>
                                            <td>{{$del->status}}</td>
                                            
                                            <td style="display: flex; align-items: center; justify-content: center;">
                                                <div class="dropdown" style="display:flex; align-items: center; justify-content: center; width: auto;">
                                                    <button class="" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 13px; ">
                                                        <span class="material-symbols-outlined">
                                                        expand_circle_down
                                                        </span>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a class="dropdown-item"
                                                                data-bs-toggle="modal" data-bs-target="#pdfModal-{{ $del->delivery_id }}">
                                                                <span class="material-symbols-outlined">download</span>
                                                                 Delivery receipt
                                                            </a>
                                                        </li>
                                                        @if ($del->status === 'Scheduled')
                                                            <li>
                                                                <a class="dropdown-item"
                                                                    data-bs-toggle="modal" data-bs-target="#process-{{ $del->delivery_id }}">
                                                                    <span class="material-symbols-outlined">manufacturing</span>
                                                                    Process delivery
                                                                </a>
                                                            </li>
                                                        @endif


                                                        
                                                    </ul>
                                                </div>
                                            </td>
   
                                        </tr>
                                    @endforeach
                                @else
                                        <p>No data</p>
                                @endif                            

                            </tbody>
                        </table>
                        
                    </div>
                @elseif($user->role !== "Customer")
                    <div class="content-body" style="background: #fff;">
                        <table style="width:100%; border-collapse:collapse; border: 1px solid #fff; overflow: hidden;" >
                            <thead style="background-color: #fff;">
                                <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc; ">
                                    <th>#</th>
                                    <th>Updated on</th>
                                    <th>Customer</th>
                                    <th>PO ID</th>
                                    <th>Heads/Kilos</th>
                                    <th>Scheduled</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>    
                                @if ($delivery)
                                    @foreach ($delivery as $del)
                                        <!--Process delivery modal -->
                                        <div class="modal fade" id="process-{{ $del->delivery_id }}" tabindex="-1" >
                                            <div class="modal-dialog" > 
                                                <form class="modal-content" action="{{ route('dlv.update', $del->delivery_id) }}" method="POST" >
                                                    @csrf
                                                    <div class="modal-header">
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body" style="background: #fff;">
                                                            <p class="info">
                                                                <span class="material-symbols-outlined icon">
                                                                info
                                                                </span>
                                                                <span>You may change the quantity of the items below lesser than the planned amount. Editing this will adjust the other pending deliveries.</span>
                                                            </p>

                                                            <div class="details-info-del">
                                                                <p>
                                                                    <strong>{{ $del->customer->company_name }}</strong>
                                                                </p>
                                                                <p>
                                                                    <span>Delivery #</span>
                                                                    <strong>{{ $del->delivery_id }}</strong>
                                                                </p>
                                                                <p>
                                                                    <span>PO #</span>
                                                                    <strong>{{ $del->po_id }}</strong>
                                                                </p>

                                                                <p>
                                                                    <span>Created date</span>
                                                                    <strong>{{ $del->created_at->format('F j, y g:i a') }}</strong>
                                                                </p>
                                                                <p>
                                                                    <span>Delivery date</span>
                                                                    <strong>{{ $del->delivery_date->format('F j, y g:i a') }}</strong>
                                                                </p>
                                                            </div>

                                                            <p style="margin: 5px; margin-top: 10px;">This delivery has (<strong>{{ $del->Delitems->count() }}</strong>) item/s</p>

                                                            <div class="delivery-inputs" style="align-items: flex-start; display: flex; flex-direction: column;  padding: 10px; box-shadow: rgba(0, 0, 0, 0.02) 0px 1px 3px 0px, rgba(27, 31, 35, 0.15) 0px 0px 0px 1px; border-radius: 10px;">
                                                                @foreach ($del->Delitems as $item)
                                                                    <p>
                                                                        <span>#{{ $loop->iteration }}</span>
                                                                        <strong>{{ $item->product->name }}</strong>
                                                                    </p>
                                                                    <div class="input-divs" style="display: flex; flex-direction: row; gap: 10px; margin-bottom: 10px;">
                                                                        @if ($item->product->measurement_type === "Heads")
                                                                            <div>
                                                                                <input type="text" placeholder="{{ $item->planned_heads }}" name="planned_heads[{{ $item->delivery_item_id }}]" value="{{ $item->planned_heads }}" max="{{ $item->planned_heads }}"> H
                                                                            </div>
                                                                        @elseif ($item->product->measurement_type === "Kilos")
                                                                            <div>
                                                                                <input type="text" placeholder="{{ $item->planned_kilos }}" name="planned_kilos[{{ $item->delivery_item_id }}]" value="{{ $item->planned_kilos }}" max="{{ $item->planned_kilos }}"> K
                                                                            </div>
                                                                        @elseif ($item->product->measurement_type === "Heads&Kilos")
                                                                            <div>
                                                                                <input type="text" placeholder="{{ $item->planned_heads }}" name="planned_heads[{{ $item->delivery_item_id }}]" value="{{ $item->planned_heads }}" max="{{ $item->planned_heads }}"> H
                                                                                <input type="text" placeholder="{{ $item->planned_kilos }}" name="planned_kilos[{{ $item->delivery_item_id }}]" value="{{ $item->planned_kilos }}" max="{{ $item->planned_kilos }}"> K
                                                                            </div>
                                                                        @endif
                                                                    </div>

                                                                @endforeach

                                                            </div>
                                                        
                                                    </div>  
                                                    <div class="modal-footer" style="background: #fff;">
                                                        <button type="button" type="submit" class="yellow-btn" style="border-radius:5px;">Process order</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{ $del->updated_at->format('F j, y g:i a') }}</td>
                                            <td>{{ $del->customer->company_name}}</td>
                                            <td>#{{ $del->po_id }}</td>
                                            <td>
                                                @php
                                                    $sum_heads = $del->items->sum('planned_heads');
                                                    $sum_kilos = $del->items->sum('planned_kilos');
                                                @endphp
                            
                                                {{ $sum_heads}} - {{ $sum_kilos}}
                                            </td>
                                            <td>{{$del->delivery_date->format('F j, y')}}</td>
                                            <td>{{ $del->status}}</td>
                                            <td style="display: flex; align-items: center; justify-content: center;">
                                                <div class="dropdown" style="display:flex; align-items: center; justify-content: center; width: auto;">
                                                    <button class="" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 13px; ">
                                                        <span class="material-symbols-outlined">
                                                        expand_circle_down
                                                        </span>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a class="dropdown-item"
                                                                data-bs-toggle="modal" data-bs-target="#pdfModal-{{ $del->delivery_id }}">
                                                                <span class="material-symbols-outlined">download</span>
                                                                 Delivery receipt
                                                            </a>
                                                        </li>
                                                        @if ($del->status === 'Scheduled')
                                                            <li>
                                                                <a class="dropdown-item"
                                                                    data-bs-toggle="modal" data-bs-target="#process-{{ $del->delivery_id }}">
                                                                    <span class="material-symbols-outlined">manufacturing</span>
                                                                    Process delivery
                                                                </a>
                                                            </li>
                                                        @endif


                                                        
                                                    </ul>
                                                </div>
                                            </td>
                                            
                                        </tr>
                                    @endforeach
                                @else
                                        <p>No data</p>
                                @endif                            

                            </tbody>
                        </table>
                    </div>
                @endif
                    <div class="pagination-div" style="margin-top: 15px;">
                        <p>Showing {{ $delivery->firstItem() }} to {{ $delivery->lastItem() }} of {{ $delivery->total() }} entries</p>
                        {{ $delivery->links() }}
                    </div>

        </div>


@endsection

@push('scripts')

    <script src="{{ asset('js/pr/list.js') }}"></script>

@endpush
