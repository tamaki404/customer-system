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
        @if (session('success'))
                <div class="alert alert-success" style="margin: 10px;">{{ session('success') }}</div>
        @endif
        @if (session('error'))
                <div class="alert alert-danger" style="margin: 10px;">
                    <h6 style="margin-bottom: 10px; font-weight: bold;">Error:</h6>
                    <p style="margin: 0; font-size: 14px;">{{ session('error') }}</p>
                </div>
        @endif
        
        <div class="modal fade" id="profile-modify" tabindex="-1" aria-labelledby="requestActionLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form class="modal-content" method="POST" enctype="multipart/form-data">
                     
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

                        <!-- Hidden field for staff ID -->

                        
                        <div class="modal-header">
                            <p class="modal-title" id="requestActionLabel">Profile modify</p>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        
                        <div class="modal-body">
                            <p class="note-notify">
                                <span class="material-symbols-outlined"> info </span>
                                <span>Changes require confirmation before they take effect.</span>
                            </p>
                            
                            <div class="modal-option-groups">
                                <p>Profile picture</p>
                                
                                
                            </div>
                            
                       
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit action</button>
                        </div>
                    </form>
                </div>
        </div>
            {{-- view e-signature --}}
            <div class="modal fade" id="view-sign-action" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <p class="modal-title" id="modal-signatory-name">E-signature</p>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body text-center">
                            <img id="view-sign-image" src="" alt="E-signature" style="max-width: 100%; height: auto;"/>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        <div class="content-bg" >
            <div class="content-header">
                    <div class="contents-display">
                       
                    </div>

                    <div class="title-actions">
                        <p class="heading">Your profile</p>
                        <button class="editprofile btn-transition" data-bs-toggle="modal" data-bs-target="#profile-modify" style="display: flex; flex-direction: row; align-items: center; justify-content: center;">
                            <span style="font-size: 16px" class="material-symbols-outlined">edit</span>
                            <span style="font-size: 14px; color: #333;">Edit profile</span>
                        </button>
                    </div>


            </div>
            <div class="content-body" style="padding: 10px; border: none; height: auto;">
                    <div class="profile-upper">
                        @php
                            $imgSrc =  $supplier->user->image 
                                ? ('data:' . $supplier->user->image_mime_type . ';base64,' . base64_encode($supplier->user->image))
                                : asset('images/default-avatar.png');
                        @endphp
                        <img class="supplier-image" src="{{ $imgSrc }}" alt="Profile Image">   
                        
                        <div class="profile-details-div" style="margin-left: 10px">
                            <div>
                                <div class="company-status" style="margin: 0; display: flex; align-items: center;">
                                    <p class="company-name">{{  $supplier->company_name }} <span style="font-size: 14px; font-weight: normal;">({{$supplier->category}})</span></p>
                                    <p style="margin: 0; cursor: pointer;">
                                        <span class="material-icons" style="font-size: 14px; color:
                                            @if($supplier->account_status->account_status === 'Pending') orange
                                            @elseif($supplier->account_status->account_status === 'Accepted') green
                                            @elseif($supplier->account_status->account_status === 'Suspended') red
                                            @else gray
                                            @endif
                                        "
                                        title="{{$supplier->account_status->account_status}}"
                                        
                                        >circle</span>
                                    </p>
                                </div>
                                <div class="details-address">
                                    <p class="address-div">
                                        <span class="material-symbols-outlined icon" title="Home/ Head office address">home_work</span>
                                            <span class="div-text">
                                                {{ implode(', ', array_filter([
                                                    $address->home_street,
                                                    $address->home_subdivision,
                                                    $address->home_barangay,
                                                    $address->home_city,
                                                ])) }}
                                            </span>

                                    </p>
                                    <p class="address-div">
                                        <span class="material-symbols-outlined icon" title="Office address">domain</span>
                                        <span class="div-text">
                                            {{ implode(', ', array_filter([
                                                $address->office_street,
                                                $address->office_subdivision,
                                                $address->office_barangay,
                                                $address->office_city, 
                                            ])) }}
                                        </span>
                                    </p>
                                </div>
                                <div class="details-contact" style="margin-top: 5px; margin-left: ;">

                                    <p>
                                        <span class="material-symbols-outlined icon" title="Mobile number">mobile</span>
                                        <span class="div-text">{{$supplier->mobile}}</span>
                                    </p>
                                    <span>|</span>
                                    @if ($supplier->tele !== NULL)
                                        <p>
                                            <span class="material-symbols-outlined icon" title="Telephone number">call</span>
                                            <span class="div-text">{{$supplier->tele}}</span>

                                        </p>
                                        <span>|</span>
                                    @endif


                                    <p>
                                        <span class="material-symbols-outlined icon" title="Email address">mail</span>
                                        <span class="div-text">{{$supplier->user->email_address}}</span>

                                    </p>

                                    <span>|</span>
                                    <p>
                                        <span class="material-symbols-outlined icon" title="Payment method">paid</span>
                                        <span class="div-text" style="color: green">{{$supplier->payment_method}}</span>

                                    </p>
                                    <span>|</span>
                                    <p>
                                        <span class="div-text" style="color: #666">{{$supplier->user_id}}</span>

                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="tab-div">

                        <div class="tabs" role="tablist">
                            <button class="tab-button active" data-tab="product" role="tab" aria-selected="false" aria-controls="product-content" id="product-tab">
                            Product requirements
                            </button>
                            <button class="tab-button" data-tab="prices" role="tab" aria-selected="true" aria-controls="prices-content" id="prices-tab">
                            Prices history
                            </button>
                            <button class="tab-button " data-tab="sales" role="tab" aria-selected="true" aria-controls="sales-content" id="sales-tab">
                            Sales record
                            </button>
                            <button class="tab-button" data-tab="staff" role="tab" aria-selected="false" aria-controls="staff-content" id="staff-tab">
                            Authorized staffs
                            </button>
                            <button class="tab-button" data-tab="details" role="tab" aria-selected="false" aria-controls="details-content" id="details-tab">
                            Banks & other details
                            </button>
                            <button class="tab-button" data-tab="documents" role="tab" aria-selected="false" aria-controls="documents-content" id="documents-tab">
                            Documents
                            </button>
                        </div>

                        {{-- sale history --}}
                        <div id="sales-content" class="tab-content" role="tabpanel" aria-labelledby="sales-tab">
                            <div class="profile-mid" >
                                <div class="authorized-staffs" style="box-shadow: none">
                                    <p style="margin-bottom: 5px">Sales table</p>
                                    <div class="rep-sign-tables" style="width: 100%; display: flex; flex-direction: row; gap: 5px;">
                                        <div class="authorized-rep">
                                            <table style="width:100%; border-collapse:collapse; border: 1px solid #f7f7fa;">
                                                <thead style="background-color: #f9f9f9;">
                                                    <tr style="background:#f7f7fa; text-align: center; height: 30px">
                                                        <th>#</th>
                                                        <th>Created At</th>
                                                        <th>Product</th>
                                                        <th>Sale ID</th>
                                                        <th>Sale Price</th>
                                                        <th>Discount (%)</th>
                                                        <th>Duration (Days)</th>
                                                        <th>Start Date</th>
                                                        <th>End Date</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    
                                                    @foreach ($salesHistos as $salesHisto)
                                                        @php
                                                            $hours = \Carbon\Carbon::parse($salesHisto->start_date)->diffInHours(\Carbon\Carbon::parse($salesHisto->end_date));
                                                            $days = round($hours / 24, 1);
                                                            $original = $salesHisto->set->nego_price;
                                                            $salePrice = $salesHisto->sale_price;
                                                            $discount = $original > 0 ? round((($original - $salePrice) / $original) * 100) : 0;
                                                        @endphp

                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ \Carbon\Carbon::parse($salesHisto->created_at)->format('M d, Y h:i A') }}</td>
                                                            <td>{{ $salesHisto->set->product->name }}</td>
                                                            <td>{{ $salesHisto->set_id }}</td>
                                                            <td>₱{{ $salesHisto->sale_price }}</td>
                                                            <td>{{ $discount }}% OFF</td>
                                                            <td>{{ $days }} day/s sale</td>
                                                            <td>{{ \Carbon\Carbon::parse($salesHisto->start_date)->format('M d, Y h:i A') }}</td>
                                                            <td>{{ \Carbon\Carbon::parse($salesHisto->end_date)->format('M d, Y h:i A') }}</td>
                                                                @php
                                                                    $now = \Carbon\Carbon::now();
                                                                    $start = \Carbon\Carbon::parse($salesHisto->start_date);
                                                                    $end = \Carbon\Carbon::parse($salesHisto->end_date);

                                                                    if ($now->lt($start)) {
                                                                        // Before start date
                                                                        if ($now->diffInDays($start) <= 3) {
                                                                            $status = 'Starting Soon';
                                                                        } else {
                                                                            $status = 'Upcoming';
                                                                        }
                                                                    } elseif ($now->between($start, $end)) {
                                                                        // Active now
                                                                        if ($now->diffInDays($end) <= 3) {
                                                                            $status = 'Ending Soon';
                                                                        } else {
                                                                            $status = 'Active';
                                                                        }
                                                                    } else {
                                                                        // Past end date
                                                                        $status = 'Expired';
                                                                    }
                                                                @endphp

                                                                <td>
                                                                    <span style="font-size: 13px; font-weight: normal;" class="badge 
                                                                        @if($status === 'Active') bg-success
                                                                        @elseif($status === 'Ending Soon') bg-warning
                                                                        @elseif($status === 'Starting Soon') bg-info
                                                                        @elseif($status === 'Upcoming') bg-primary
                                                                        @else bg-secondary @endif">
                                                                        {{ $status }}
                                                                    </span>
                                                                </td>


                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>

                        {{-- prices history --}}
                        <div id="prices-content" class="tab-content" role="tabpanel" aria-labelledby="prices-tab">
                            <div class="profile-mid" >
                                <div class="authorized-staffs" style="box-shadow: none">
                                    <p style="margin-bottom: 5px">Prices table</p>
                                    <div class="rep-sign-tables" style="width: 100%; display: flex; flex-direction: row; gap: 5px;">
                                        <div class="authorized-rep">
                                            <table style="width:100%; border-collapse:collapse; border: 1px solid #f7f7fa;">
                                                <thead style="background-color: #f9f9f9;">
                                                    <tr style="background:#f7f7fa; text-align: center; height: 30px">
                                                        <th>#</th>
                                                        <th>Product</th>
                                                        <th>Product ID</th>
                                                        <th>New price</th>
                                                        <th>Past price</th>
                                                        <th>Percentage</th>
                                                        <th>Updated by</th>
                                                        <th>Updated at</th>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($prices as $price)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ $price->set->product->name }}</td>
                                                            <td>{{ $price->set->product->product_id }}</td>
                                                            <td>₱{{ $price->new_price }}</td>
                                                            <td>₱{{ $price->past_price }}</td>
                                                            <td>
                                                                @php
                                                                    $new = $price->new_price;
                                                                    $old = $price->past_price;
                                                                    $percent = $old > 0 ? round((($new - $old) / $old) * 100, 2) : 0;
                                                                @endphp
                                                                {{ $percent }}%
                                                            </td>

                                                            
                                                            <td>{{ $price->staff->lastname}}, {{ $price->staff->firstname}} {{ $price->staff->middlename}}</td>
                                                            <td>{{ \Carbon\Carbon::parse($price->created_at)->format('M d, Y h:i A') }}</td>

                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>
                        {{-- product requirements --}}
                        <div id="product-content" class="tab-content active" role="tabpanel" aria-labelledby="product-tab">
                            <div class="profile-mid" >
                                @if ($activeSale > '0')
                                    <div class="authorized-staffs">
                                        <div class="sale-title-wrapper">
                                            <span class="material-symbols-outlined" style="font-size: 20px">local_fire_department</span>
                                            <p>On sale</p>
                                        </div>
                                        
                                        <div class="sale-list">
                                            @foreach ($sales as $sale)
                                                @php
                                                    $hours = \Carbon\Carbon::parse($sale->start_date)->diffInHours(\Carbon\Carbon::parse($sale->end_date));
                                                    $days = round($hours / 24, 1);
                                                    $original = $sale->set->nego_price;
                                                    $salePrice = $sale->sale_price;
                                                    $discount = $original > 0 ? round((($original - $salePrice) / $original) * 100) : 0;
                                                @endphp

                                                <div class="sale-item" >
                                                    <div class="sale-badge">{{ $discount }}% OFF</div>
                                                    
                                                    <div class="sale-header">
                                                        <span class="material-symbols-outlined">percent_discount</span>
                                                        <span class="product-name">{{ $sale->set->product->name }}</span>
                                                    </div>

                                                    <div class="sale-info">
                                                        <p class="sale-dates">
                                                            <span class="material-symbols-outlined">schedule</span>
                                                            {{ \Carbon\Carbon::parse($sale->start_date)->format('M d, Y h:i A') }}
                                                            →
                                                            {{ \Carbon\Carbon::parse($sale->end_date)->format('M d, Y h:i A') }}
                                                        </p>

                                                        <p class="sale-duration">{{ $days }} day/s sale</p>
                                                    </div>

                                                    <div class="sale-pricing">
                                                        <div class="price-wrapper">
                                                            <span class="original-price">₱{{ number_format($original, 2) }}</span>
                                                            <span class="sale-price">₱{{ number_format($salePrice, 2) }}</span>
                                                        </div>
                                                    </div>

                                                    <div class="countdown-box">
                                                        <p class="countdown"
                                                        data-start="{{ \Carbon\Carbon::parse($sale->start_date)->timezone('Asia/Manila')->timestamp * 1000 }}"
                                                        data-end="{{ \Carbon\Carbon::parse($sale->end_date)->timezone('Asia/Manila')->timestamp * 1000 }}">
                                                        </p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                @endif


                                <div class="authorized-staffs" style="box-shadow: none">
                                    <p style="margin-bottom: 5px">Price change</p>
                                    <div class="rep-sign-tables" style="width: 100%; display: flex; flex-direction: row; gap: 5px;">
                                        <div class="authorized-rep">
                                            <button  data-bs-toggle="modal" data-bs-target="#add-product-modal" class="btn-transition btn-span" style="width: 100px;     background-color: #f8912a; color: #fff; font-weight: normal;"> <span class="material-symbols-outlined ">add</span>Add new</button>

                                            <table style="width:100%; border-collapse:collapse; border: 1px solid #f7f7fa;">
                                                <thead style="background-color: #f9f9f9;">
                                                    <tr style="background:#f7f7fa; text-align: center; height: 30px">
                                                        <th>#</th>
                                                        <th>Category</th>
                                                        <th>Name</th>
                                                        <th>Unit</th>
                                                        <th>Measurement</th>
                                                        <th>Price</th>
                                                        <th></th>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($productRequirements as $productRequirement)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $productRequirement->condition }}</td>
                                                        <td>{{ $productRequirement->product->name }}</td>
                                                        <td>{{ $productRequirement->product->unit }}</td>
                                                        <td>{{ $productRequirement->product->measurement }}</td>
                                                 
                                                        @if ($productRequirement->settings?->sale)
                                                            {{-- Product is on sale --}}
                                                            <td style="display: flex; flex-direction: column;">
                                                                <span style="text-decoration: line-through; font-size: 10px; color: #888;">
                                                                    ₱{{ number_format($productRequirement->settings->nego_price, 2) }}
                                                                </span>
                                                                <span style="font-weight: bold; color: #f8912a;">
                                                                    ₱{{ number_format($productRequirement->settings->sale->sale_price, 2) }}
                                                                </span>
                                                            </td>
                                                        @else
                                                            {{-- Normal price --}}
                                                            @if ($productRequirement->settings && $productRequirement->settings->nego_price !== null)
                                                                <td>₱{{ number_format($productRequirement->settings->nego_price, 2) }}</td>
                                                            @else
                                                                <td>₱0.00</td>
                                                            @endif


                                                        @endif


                                                    
                                                        <td>
                                                            <button  
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#edit-row-action" 
                                                                class="btn-span edit-product-btn"
                                                                data-set-id="{{ ($productRequirement->settings->set_id) ?? "" }}"
                                                                data-price="{{ ($productRequirement->settings->nego_price) ?? "" }}"
                                                                data-name="{{ $productRequirement->product->name }}"
                                                                data-supplier-id="{{ $productRequirement->supplier_id }}"
                                                            >
                                                                <span class="material-symbols-outlined">edit</span>
                                                            </button>


                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                </div>

        

                                @if ($delivery->delivery_instructions !== NULL)
                                    <div class="authorized-staffs" style="width: 600px">
                                        <p style="margin-bottom: 5px">Remarks/Special instructions</p>
                                        <div class="authorized-rep delivery-req">
                                            <p class="spec-weight">
                                                <span style="font-weight: normal; font-size: 13px;">{{ $delivery->delivery_instructions }}</span>
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                <div class="authorized-staffs" style="width: 700px">
                                    <p style="margin-bottom: 5px">Delivery requirements</p>
                                        <div class="authorized-rep delivery-req">
                                                    <div class="spec-card" style="100%">
                                                        <p class="spec-weight">
                                                            <strong>Delivery frequency:</strong> 
                                                            <span style="font-weight: bold">{{ $delivery->delivery_frequency }}</span>
                                                        </p>
                                                        <p class="spec-weight">
                                                            <strong>PPE requirements:</strong> 
                                                            <span style="font-weight: bold">{{ $delivery->ppe_requirements }}</span>
                                                        </p>
                                                        <p class="spec-weight">
                                                            <strong>Address 1:</strong> 
                                                            <span style="font-weight: bold">{{ $delivery->delivery_address_1 }}</span>
                                                        </p>
                                                        @if ($delivery->delivery_address_2 !== NULL)
                                                            <p class="spec-weight">
                                                                <strong>Address 2:</strong> 
                                                                <span style="font-weight: bold">{{ $delivery->delivery_address_2 }}</span>
                                                            </p>
                                                        @elseif ($delivery->delivery_address_3 !== NULL)
                                                            <p class="spec-weight">
                                                                <strong>Address 3:</strong> 
                                                                <span style="font-weight: bold">{{ $delivery->delivery_address_3 }}</span>
                                                            </p>
                                                        @endif
                                                    </div>
                       
                                        </div>

                                                

                                </div>
                                
                                <div class="authorized-staffs" style="">
                                    <p style="margin-bottom: 5px">Specifications</p>
                                    <div class="rep-sign-tables" >
                                        <div class="authorized-rep" style="width: 100%; display: flex; flex-direction: row; gap: 5px; flex-wrap: wrap; justify-content:flex-start; background-color: transparent; border: none; box-shadow: none;">
                                                {{-- @for ($i = 0; $i <div 5; $i++) --}}
                                                @foreach ($prodSpecs as $prodSpec)
                                                    <div class="spec-card" style="width: 49%">
                                                        <p class="spec-title" style="display: flex; justify-content: space-between; font-size: 15px; margin-bottom: 10px;">
                                                            <span class="spec-name" style="font-weight: bold; color: #f8912a;">  {{ $prodSpec->product->name }} </span>
                                                            @if ($prodSpec->condition === 'frozen')
                                                                <span class="material-symbols-outlined" title="Frozen">mode_cool</span>
                                                            @elseif ($prodSpec->condition === 'fresh')
                                                                <span class="material-symbols-outlined" title="Fresh">air</span>
                                                            @endif
                                                        </p>

                                                        <p class="spec-weight">
                                                            <strong>Weight Requirement:</strong> 
                                                            <span style="font-weight: bold">{{ $prodSpec->weight_requirement }}</span>
                                                        </p>

                                                        <p class="spec-packaging">
                                                            <strong>Packaging Requirement:</strong>
                                                            <span class="pack-primary" style="font-weight: bold">Prim. {{ $prodSpec->primary_packaging }}</span>
                                                            <span class="pack-secondary" style="font-weight: bold">Sec. {{ $prodSpec->secondary_packaging }}</span>
                                                        </p>

                                                        <p class="spec-label-reject">
                                                        <strong>Labeling:</strong> <span style="font-weight: bold"> {{ $prodSpec->labeling_requirement}}</span> 
                                                        </p>
                                                        <p class="spec-label-reject">
                                                            <strong>Rejection:</strong> <span style="font-weight: bold"> {{ $prodSpec->rejection_parameter }}</span>
                                                        </p>
                                                    </div>
                                                @endforeach
                                                {{-- @endfor --}}
                                        </div>

                                    </div>


                                </div>

                                                

                            </div>
                        </div>

                        {{-- authorized staffs requirements --}}
                        <div id="staff-content" class="tab-content" role="tabpanel" aria-labelledby="staff-tab">
                            <div class="profile-mid">
                                <div class="authorized-staffs" >
                                    <p style="margin-bottom: 5px">Authorized staffs</p>
                                    <di class="rep-sign-tables" style="width: 100%; display: flex; flex-direction: row; gap: 5px;">
                                        <div class="authorized-rep">
                                            <p style="font-weight: normal; font-size: 13px;">Representatives </p>
                                            <table style="width:100%; border-collapse:collapse; border: 1px solid #f7f7fa;">
                                                <thead style="background-color: #f9f9f9;">
                                                    <tr style="background:#f7f7fa; text-align: center; height: 30px">
                                                        <th>#</th>
                                                        <th>Name</th>
                                                        <th>Relationship/position</th>
                                                        <th>Contact number</th>
                                                    </tr>
                                                </thead>
                                                <tbody>


                                                    @foreach ( $representatives as $rep)
                                                        <tr>
                                                            <td>{{$loop->iteration}}</td>
                                                            <td>
                                                                {{ implode(', ', array_filter([
                                                                    $rep->rep_lastname,
                                                                    $rep->rep_firstname,
                                                                    $rep->rep_middlename,
                                                                ])) }}
                                                            </td>
                                                            <td>{{ $rep->auth_position}}</td>
                                                            <td>{{ $rep->rep_contact}}</td>

                                                        </tr>
                                                    @endforeach

                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="signatory-rep">
                                            <p style="font-weight: normal; font-size: 13px;">Signatories to accept deliveries and sign invoices </p>
                                            <table style="width:100%; border-collapse:collapse; border: 1px solid #f7f7fa;">
                                                <thead style="background-color: #f9f9f9;">
                                                    <tr style="background:#f7f7fa; text-align: center; height: 30px">
                                                        <th>#</th>
                                                        <th>Name</th>
                                                        <th>Relationship/position</th>
                                                        <th>E-signature</th>
                                                        <th></th>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($signatories as $sign)
                                                        <tr>

                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>
                                                                {{ implode(', ', array_filter([
                                                                    $sign->sign_lastname,
                                                                    $sign->sign_firstname,
                                                                    $sign->sign_middlename,
                                                                ])) }}
                                                            </td>
                                                            <td>{{ $sign->sign_position }}</td>
                                                            <td>
                                                                @php
                                                                    $sign_image =  $sign->e_image
                                                                        ? ('data:' . $sign->e_mime_type . ';base64,' . base64_encode($sign->e_image))
                                                                        : asset('images/default-avatar.png');
                                                                @endphp
                                                                <img src="{{ $sign_image }}" width="60" height="60" class="rounded">
                                                            </td>
                                                            <td style="">
                                                                <button  
                                                                    class="viewSignBtn edit-product-btn"
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#view-sign-action"
                                                                    data-image="{{ $sign_image }}"
                                                                    data-name="{{ implode(', ', array_filter([
                                                                        $sign->sign_lastname,
                                                                        $sign->sign_firstname,
                                                                        $sign->sign_middlename,
                                                                    ])) }}"
                                                                    style="height: 40px">
                                                                    <span class="material-symbols-outlined" style="font-size: 17px">visibility</span>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>                        

                                    </di>
                                </div>

                            </div>
                        </div>

                        {{-- docs --}}
                        <div id="documents-content" class="tab-content" role="tabpanel" aria-labelledby="documents-tab">
                            <div class="documents-div">
                                <div>
                                    <p class="title-data">Documents</p>
                                    <div class="documents-list d-flex flex-wrap gap-4">
                                        @foreach ($documents as $document)
                                            @php
                                                $pdfData = $document->file
                                                    ? 'data:' . $document->file_mime . ';base64,' . base64_encode($document->file)
                                                    : null;

                                                $modalId = 'documentModal' . ($document->id ?? $loop->index);
                                            @endphp

                                            <div class="document-card text-center" style="width: 220px;">
                                                <div class="card shadow-sm border-0 rounded-3 overflow-hidden" style="cursor: pointer; height: 300px;"
                                                    data-bs-toggle="modal" data-bs-target="#{{ $modalId }}">
                                                    
                                                    <div class="ratio ratio-4x3 bg-light" style="height: 80%">
                                                        @if ($pdfData)
                                                            <iframe
                                                                src="{{ $pdfData }}#toolbar=0&navpanes=0&scrollbar=0&page=1&"
                                                                style="width: 100%; height: 100%; pointer-events: none; border: none;"
                                                                title="PDF Preview"
                                                            ></iframe>
                                                        @else
                                                            <p class="text-danger">No document</p>
                                                        @endif
                                                    </div>

                                                    <div class="card-body p-3">
                                                        <p class="fw-semibold mb-1" style="font-size: 0.9rem;">{{ $document->type }}</p>
                                                        <small class="text-muted">
                                                            {{ \Carbon\Carbon::parse($document->updated_at)->format('M d, Y') }} <br>
                                                            {{-- Opened {{ \Carbon\Carbon::parse($document->updated_at)->format('g:i A') }} --}}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-xl modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">{{ $document->type }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body text-center" style="height: 80vh;">
                                                            @if ($pdfData)
                                                                <iframe
                                                                    src="{{ $pdfData }}"
                                                                    width="100%"
                                                                    height="100%"
                                                                    style="border: none;"
                                                                    title="{{ $document->type }} Full View"
                                                                ></iframe>
                                                            @else
                                                                <p class="text-danger">Unable to load PDF.</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                </div>
                            </div>

                        </div>

                        {{-- banks and other details --}}
                        <div id="details-content" class="tab-content" role="tabpanel" aria-labelledby="details-tab">
                            <div class="profile-mid">

                                <div class="authorized-staffs" >
                                    <p style="margin-bottom: 5px">Account details</p>
                                    <di class="rep-sign-tables" style="width: 100%; display: flex; flex-direction: row; gap: 5px; ">
                                        <div class="questions-list">
                                            <p>
                                                <span style="color:#666">Joined at</span>
                                                <span>{{ \Carbon\Carbon::parse($account_status->created_at)->format('F j, Y') }}</span>
                                                
                                            </p>
                                            <p>
                                                <span style="color:#666">Verified by</span>
                                                @if (@$account_status->user_id === $supplier->user_id > 0 && @$account_status->staff_id !== NULL)
                                                    <span>
                                                        {{ implode(', ', array_filter([
                                                            $account_status->staff->lastname,
                                                            $account_status->staff->firstname
                                                            ]))
                                                        }}
                                                        
                                                    </span>
                                                @else
                                                <span>--</span>
                                                    
                                                @endif
                                   

                                            </p>
                                            <p>
                                                <span style="color:#666">Approved at</span>
                                                @if ($account_status->approved_at !== NULL)
                                                    <span>{{ \Carbon\Carbon::parse($account_status->approved_at)->format('F j, Y') }}</span>
                                                @else
                                                    <span>--</span>

                                                @endif
                                            </p>
                           
                                    
                                        </div>

                                    </di>


                                </div>

                                <div class="authorized-staffs" >                                                                        
                                    <p style="margin-bottom: 5px">Bank details</p>
                                    <div class="rep-sign-tables" style="width: 100%; display: flex; flex-direction: row; gap: 5px;">
                                        <div class="authorized-rep">
                                            <table style="width:100%; border-collapse:collapse; border: 1px solid #f7f7fa;">
                                                <thead style="background-color: #f9f9f9;">
                                                    <tr style="background:#f7f7fa; text-align: center; height: 30px">
                                                        <th>#</th>
                                                        <th>Account name</th>
                                                        <th>Bank</th>
                                                        <th>Branch</th>
                                                        <th>Account number</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>1</td>
                                                        <td>{{ $supplier->bank->account_number}}</td>
                                                        <td>{{ $supplier->bank->bank}}</td>
                                                        <td>{{ $supplier->bank->branch}}</td>
                                                        <td>{{ $supplier->bank->account_number}}</td>

                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>


                                </div>

                                <div class="authorized-staffs" >
                                    <p style="margin-bottom: 5px">Business & contacts</p>
                                    <di class="rep-sign-tables" style="width: 100%; display: flex; flex-direction: row; gap: 5px; ">
                                        <div class="questions-list">
                                            <p>
                                                <span style="color:#666">How long have you been in the industry? </span>
                                                <span>{{$business->years}}</span>
                                            </p>
                                            <p>
                                                <span style="color:#666">Referred by</span>
                                                <span>{{$business->referred_by}}</span>
                                            </p>
                                            <p>
                                                <span style="color:#666">Contacted by</span>
                                                <span>{{$business->contacted_by}}</span>
                                            </p>
                           
                                    
                                        </div>

                                    </di>


                                </div>

                                <div class="authorized-staffs" >
                                    <p style="margin-bottom: 5px">Valid ID</p>
                                    <di class="rep-sign-tables" style="width: 100%; display: flex; flex-direction: row; gap: 5px; ">
                                        <div class="questions-list" style="width: auto; gap: 2px">
                                            <div>
                                                @php
                                                    $idImg =  $supplier->id_image 
                                                        ? ('data:' . $supplier->id_mime_type . ';base64,' . base64_encode($supplier->id_image))
                                                        : asset('images/default-avatar.png');
                                                @endphp
                                                <img class="id-image" style="height: 200px" src="{{ $idImg }}" alt="ID Image"> 
                                            </div>
                                            <p>
                                                <span style="color:#666">Type of ID </span>
                                                <span>{{$supplier->id_type}}</span>
                                            </p>

                                            <p>
                                                <span style="color:#666">Valid ID no.</span>
                                                <span>{{$supplier->id_number}}</span>
                                            </p>
                                            <p>
                                                <span style="color:#666">Civil status</span>
                                                <span>{{$supplier->civil_status}}</span>
                                            </p>
                                            <p>
                                                <span style="color:#666">Citizenship</span>
                                                <span>{{$supplier->citizenship}}</span>
                                            </p>
                                    
                                        </div>

                                    </di>


                                </div>
                            
                            </div>
                        </div>



                    </div>

                
            
            </div>
        </div>

@endsection



@push('scripts')

    <script src="{{ asset('js/global/x/profile-tab.js') }}"></script>
    <script src="{{ asset('js/global/x/edit-product-modal.js') }}"></script>
    <script src="{{ asset('js/global/x/view-sign-modal.js') }}">

</script>


@endpush
