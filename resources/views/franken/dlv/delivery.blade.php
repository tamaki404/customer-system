@extends('layouts.main')


@push('styles')
    <link rel="stylesheet" href="{{ asset('css/views/customer.css') }}">
    
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

    <div class="modal fade" id="mark-as-delivered" tabindex="-1" aria-labelledby="requestActionLabel" aria-hidden="true" >
        <div class="modal-dialog">
            <form action="{{ route('dlv.receive') }}" class="modal-content" style="width: 700px; height: 80%" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="delivery_id" value="{{ $delivery->delivery_id }}">
                <div class="modal-header">
                    <p class="modal-title" id="">
                        <span class="material-symbols-outlined" >package_2</span>  
                        <span>Delivery action</span>
                    </p>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="gap: 5px; height: 500px; overflow: auto; display: flex; flex-direction: column;">
                    <p class="note-notify">
                        <span class="material-symbols-outlined"> info </span>
                        <span>For any variance, make sure everything is written on the feedback input.</span>
                        <span>Inputs with (*) are required</span>
                    </p>
                    {{-- STATUS --}}
                    <div class="modal-option-groups"  >
                        <p style="font-size: 13px"><span class="req-asterisk">*</span> What action would you like to do with this order?</p>
                        <select name="status" required>
                            <option value="">-- Select order status --</option>
                            <option value="Delivered">Mark as delivered</option>
                        </select>
                    </div>
                    {{-- FILE UPLOAD --}}
                    <div class="form-group" >
                        <p style="margin: 0" style="font-size: 13px"><span class="req-asterisk">*</span>Upload signed POD (PDF only)</p>
                        <input type="file" name="pod_file" required accept="application/pdf" style="border-radius: 10px;
                            padding: 10px;
                            border: none;
                            box-shadow: rgba(0, 0, 0, 0.02) 0px 1px 3px 0px, rgba(27, 31, 35, 0.15) 0px 0px 0px 1px;
                            font-size: 14px;
                            width: 80%;
                        ">
                        <div id="file-error" style="color:#dc3545; font-size:13px; margin-top:5px;"></div>
                    </div>
                    {{-- RECEIVED QUANTITIES --}}
                    <p style="margin: 0"><span class="req-asterisk">*</span>Delivery items table</p>
                    <table style="width:100%; height: 100%; padding: 3px;   border-radius: 10px; box-shadow: rgba(0, 0, 0, 0.05) 0px 6px 24px 0px, rgba(0, 0, 0, 0.08) 0px 0px 0px 1px; border-radius: 5px;" >
                        <thead style="background-color: #fff;">
                                <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                                    <th>Product ID</th>
                                    <th>Product</th>
                                    <th>Planned</th>
                                    <th>Received</th>
                                    <th>
                                        Promo
                                    </th> 
                                    <th>Variance</th>
                                </tr>
                        </thead>
                        <tbody>
                                @foreach($items as $item)
                                    @php
                                            $measurement = $item->product->measurement_type;
                                            $plannedHeads = $item->planned_heads ?? $item->placed_heads ?? 0;
                                            $plannedKilos = $item->planned_kilos ?? $item->placed_kilos ?? 0;
                                    @endphp
                                    <tr>
                                        <td>{{ $item->product_id }}</td>
                                        <td class="text-start">
                                            <strong>{{ $item->product->name }}</strong><br>
                                            <small class="text-muted">{{ $measurement }}</small>
                                        </td>
                                        {{-- PLANNED --}}
                                        <td>
                                            @if ($measurement === 'Kilos')
                                                <div><strong>{{ $plannedKilos }}</strong> <small>kg</small></div>
                                            @elseif ($measurement === 'Heads')
                                                <div><strong>{{ $plannedHeads }}</strong> <small>heads</small></div>
                                            @elseif ($measurement === 'Heads&Kilos')
                                                <div><strong>{{ $plannedHeads }}</strong> <small>heads</small></div>
                                                <div><strong>{{ $plannedKilos }}</strong> <small>kg</small></div>
                                            @endif
                                        </td>

                                        {{-- RECEIVED --}}
                                        <td>
                                            @if ($measurement === 'Kilos')
                                                <input type="number"
                                                    name="received_kilos[{{ $item->delivery_item_id ?? '' }}]"
                                                    class="form-control received-input mb-1"
                                                    data-planned="{{ $plannedKilos }}"
                                                        style="font-size: 13px"

                                                    step="0.01"
                                                    placeholder="Enter kilos">
                                            @elseif ($measurement === 'Heads')
                                                <input type="number"
                                                    name="received_heads[{{ $item->delivery_item_id ?? '' }}]"
                                                    class="form-control received-input mb-1"
                                                    data-planned="{{ $plannedHeads }}"
                                                        style="font-size: 13px"
                                                    step="1"
                                                    placeholder="Enter heads">
                                            @elseif ($measurement === 'Heads&Kilos')
                                                <div class="d-flex flex-column gap-2">
                                                    <input type="number"
                                                        name="received_heads[{{ $item->delivery_item_id ?? '' }}]"
                                                        class="form-control received-input"
                                                        data-planned="{{ $plannedHeads }}"
                                                        style="font-size: 13px"
                                                        step="1"
                                                        placeholder="Enter heads">
                                                    <input type="number"
                                                        name="received_kilos[{{ $item->delivery_item_id ?? '' }}]"
                                                        class="form-control received-input"
                                                        data-planned="{{ $plannedKilos }}"
                                                        step="0.01"
                                                        style="font-size: 13px"
                                                        placeholder="Enter kilos">
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->promo && $item->promo->value_type === "Fixed")
                                                <span style="color: #f8912a">₱{{$item->promo->value}} </span>
                                          @elseif ($item->promo && $item->promo->value_type === "Percentage")
                                                @php
                                                    $decimal = $item->promo->value / 100;
                                                    $discount = $decimal * $item->productSetting->nego_price;
                                                    $discountedPrice = $item->productSetting->nego_price - $discount;
                                                @endphp
                                                <span style="color: #f8912a">₱{{ number_format($discountedPrice, 2) }}</span>
                                                <span style="text-decoration: line-through">₱{{ number_format($item->productSetting->nego_price, 2) }}</span>
                                            @else
                                                ₱{{ $item->productSetting->nego_price }}
                                            @endif
                                        </td>
                                        {{-- VARIANCE --}}
                                        <td>
                                            @if ($measurement === 'Heads&Kilos')
                                                <div class="variance-text-heads text-muted mb-1">--</div>
                                                <div class="variance-text-kilos text-muted">--</div>
                                            @else
                                                <p class="variance-text text-muted m-0">--</p>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                        </tbody>
                    </table>
                    {{-- FEEDBACK --}}
                    <div class="modal-option-groups" style="margin-top: 10px">
                        <p style="font-size: 13px; display: flex; flex-direction: column;">
                            <span>Feedback (Optional)</span>
                            <span style="color: #666">If there’s a variance, please provide an explanation below.</span>
                        </p>
                        <textarea type="text" name="feedback" maxlength="200" style="box-shadow: rgba(0, 0, 0, 0.05) 0px 6px 24px 0px, rgba(0, 0, 0, 0.08) 0px 0px 0px 1px; border: none; padding: 5px; border-radius: 5px; height: 50px; outline: none;">
                        </textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>

            </form>
        </div>
    </div>

   <div class="content-bg" style="display: flex; flex-direction: row; overflow: hidden;">
        <div class="left" style="width: 75%">
            <div class="content-header">
                <div class="contents-display">
                    <p>
                        <a href="{{ route('pr.request', $delivery->po_id) }}">< Purchase request</a>
                    </p>
                </div>
                <div class="title-actions">
                    <p class="heading" >
                        <span>Delivery #{{ $delivery->delivery_id }}</span>
                        <span class="order-status"> {{ $delivery->status }} </span>
                    </p>
                    <div class="upper-con" style="display: flex; flex-direction: column; gap: 5px; margin: 5px;">
                        <div class="buttons">
                            <!-- Buttons -->
                            @if ($delivery->status === 'Scheduled' && Auth()->user()->role === 'Customer')
                                <button type="button" 
                                    data-bs-toggle="modal" data-bs-target="#mark-as-delivered" 
                                    class="yellow-btn"
                                    style="transform:none; transition:none"
                                    >
                                    <span class="material-symbols-outlined" >
                                    package_2
                                    </span>
                                    Mark as delivered
                                </button>
                            @endif
                        </div>
                    </div>
                </div>  
                <div class="details-box">
                    <div class="first" style="justify-content: space-between">
                        <div class="un-named" style="display: flex; flex-direction: row;  width: 100%; align-items: center; gap: 15px;">
                            <div>
                                <span class="material-symbols-outlined" style="color: #666; font-size: 35px;">
                                delivery_truck_speed
                                </span>                                    
                                <p class="time">
                                    <span class="label">Scheduled day</span>
                                    <span>{{$delivery->delivery_date->format('F j, y')}}</span>
                                </p>
                            </div>
                            @if ($delivery->status === "Delivered")
                                <div style="display: flex; flex-direction: row; align-items: center;">
                                    ---
                                    <span class="material-symbols-outlined" style="color: #666; font-size: 35px;">
                                    orders
                                    </span>                                    
                                    <p class="time">
                                        <span class="label">Received at</span>
                                        <span>{{$delivery->delivered_date->format('F j, y')}}</span>
                                    </p>
                                </div>
                            @endif
                        </div>
                  
                        <button class="collection-btn" style="min-width: 130px; background-color: #dc3545; border-radius: 5px;box-shadow: rgba(0, 0, 0, 0.15) 1.95px 1.95px 2.6px;">
                            <span class="material-symbols-outlined" >assignment_returned</span> Return slip
                        </button>   
                    </div>

                </div>
                
            </div>
            <div class="delivery-body " style="background-color: #fff; border: none; height: auto; overflow-x: auto; height: 450px;  border-radius: 0; border-radius: 5px;box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;">
                <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                    <thead style="background-color: #fff;">
                        <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                            <th>#</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Received</th>
                            <th>Variance</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody style="background-color: #fff; ">
                        @foreach ( $items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{$item->product->name}}</td>
                                <td>
                                    @if ($item->product->measurement_type === "Heads")
                                        {{$item->planned_heads}}pcs
                                    @elseif ($item->product->measurement_type === "Kilos")
                                        {{$item->planned_kilos}}kg
                                    @elseif ($item->product->measurement_type === "Heads&Kilos")
                                        {{$item->planned_heads}}
                                        {{$item->planned_kilos}}kg
                                    @endif
                                </td>
                                <td>
                                    @if ($delivery->status === "Delivered")
                                        @if ($item->product->measurement_type === "Heads")
                                            {{$item->received_heads}}pcs
                                        @elseif ($item->product->measurement_type === "Kilos")
                                            {{$item->received_kilos}}kg
                                        @elseif ($item->product->measurement_type === "Heads&Kilos")
                                            {{$item->received_heads}}
                                            {{$item->received_kilos}}kg
                                        @endif
                                    @else
                                        --
                                    @endif
                                </td>
                                <td>
                                    @if ($delivery->status === "Delivered")
                                        @php
                                            $varianceHeads = $item->received_heads - $item->planned_heads;
                                            $varianceKilos = $item->received_kilos - $item->planned_kilos;
                                        @endphp
                                        @if ($varianceHeads && $varianceKilos )
                                            @if ($item->product->measurement_type === "Heads")
                                                {{ $varianceHeads }} heads
                                            @elseif ($item->product->measurement_type === "Kilos")
                                                {{ number_format($varianceKilos, 2) }} kg
                                            @elseif ($item->product->measurement_type === "Heads&Kilos")
                                                {{ $varianceHeads }} heads /
                                                {{ number_format($varianceKilos, 2) }} kg
                                            @endif
                                        @else
                                            --
                                        @endif

                                    @else
                                        --
                                    @endif
                                </td>

                                <td>₱{{number_format($item->balance, 2)}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
        <div class="right" style="width: 25%; height: 100%; background-color: #fff">
            <p style="box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;">
                <span class="material-symbols-outlined">
                picture_as_pdf
                </span>
                <span>Proof of delivery</span>
            </p>
            <div class="right-bg" >
                <button class="collection-btn" style="width: 180px; border-radius: 5px;box-shadow: rgba(0, 0, 0, 0.15) 1.95px 1.95px 2.6px;">
                    <span class="material-symbols-outlined" >order_play</span> View delivery receipt
                </button>  
                <div class="payment-con" style="overflow:hidden; box-shadow: rgba(0, 0, 0, 0.05) 0px 0px 0px 1px, rgb(209, 213, 219) 0px 0px 0px 1px inset; border-radius: 5px; background-color: #f2f2f26f;" >
                    @php
                        $pdfData = $delivery->pod_file
                            ? 'data:application/pdf;base64,' . base64_encode($delivery->pod_file)
                            : null;
                    @endphp
                    <div style="height: 100%; width: 100%; cursor: pointer; " data-bs-toggle="modal" data-bs-target="#podModal">
                        @if ($delivery->status === "Delivered")                       
                            {{-- Preview Card --}}
                            @if($pdfData)
                                <iframe
                                    src="{!! $pdfData !!}#toolbar=0&navpanes=0&scrollbar=0&page=1"
                                    style="width: 100%; height: 100%; pointer-events: none; border: none;">
                                </iframe>
                            @else
                                <p class="text-danger">No document</p>
                            @endif
                            {{-- Modal --}}
                                <div class="modal fade" id="podModal" tabindex="-1"  aria-hidden="true" >
                                    <div class="modal-dialog">
                                    <div  class="modal-content" style="width: 600px; height: 80%">
                                        <div class="modal-header">
                                            <p class="modal-title" style=" background-color:transparent; display: flex; flex-direction: row; justify-content: flex-start;">
                                                <span style="font-size: 13px; font-weight:bold;" class="material-symbols-outlined" >picture_as_pdf</span>  
                                                <span style="font-size: 14px; font-weight:bold;">Proof of delivery</span>
                                            </p>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="width: 30px"></button>
                                        </div>
                                        <div class="modal-body text-center" style="height: 80vh;">
                                            @if ($pdfData)
                                                <iframe src="{!! $pdfData !!}" width="100%" height="100%" style="border: none;"></iframe>
                                            @else
                                                <p class="text-danger">Unable to load PDF.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                   
                        @else
                            <p style="font-size:12px; color:#888; font-weight:normal; text-align:center;">
                                Delivery hasn't been received yet.
                            </p>
                        @endif

                    </div>
                  
                </div>

            </div>
        </div>
    </div>

@endsection



@push('scripts')


@endpush