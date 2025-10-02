@extends('layouts.main')


@push('styles')
    <link rel="stylesheet" href="{{ asset('css/views/customer.css') }}">
    <link rel="stylesheet" href="{{ asset('css/views/customers/forms.css') }}">
    <link rel="stylesheet" href="{{ asset('css/views/customers/sales.css') }}">

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


            <div class="modal fade" id="request-action"tabindex="-1" aria-labelledby="requestActionLabel" aria-hidden="true">
                <div class="modal-dialog" >
                    <form class="modal-content" method="POST" action="{{ route('supplier.confirm') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <p class="modal-title">Supplier request action</p>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        
                        <div class="modal-body">
                            <p class="note-notify">
                                <span class="material-symbols-outlined"> info </span>
                                <span>Review the profile before taking any action on this request.</span>
                            </p>

                            <!-- Status selection -->
                            <div class="modal-option-groups">
                                <p>Do you want to accept this supplier's request to join the system?</p>
                                <select name="account_status" id="account_status" required>
                                    <option value="">-- Select status --</option>
                                    <option value="Accepted">Yes, confirm supplier's request</option>
                                    <option value="Declined">No, there's a problem with their request</option>
                                </select>
                            </div>

                            <!-- Reason to decline (hidden by default) -->
                            <div class="modal-option-groups" id="reason_group" style="display: none;">
                                <p>What seems to be the problem?</p>
                                <select name="reason_to_decline" id="reason_to_decline">
                                    <option value="">-- Select reason --</option>
                                    <option value="Wrong documents">Wrong documents, need to be changed</option>
                                    <option value="Contact support">Contact support to learn issue</option>
                                </select>
                            </div>



                            <!-- Assign staff -->
                            <div class="modal-option-groups">
                                <p>Assign a sales agent</p>
                                <select name="staff_id" class="form-control" required>
                                    <option value="">-- Select agent --</option>
                                    @foreach($staffs as $staff)
                                        <option value="{{ $staff->staff->staff_id }}">
                                            {{ $staff->staff->firstname }} {{ $staff->staff->lastname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                    
                            <div class="modal-option-groups">
                                <p>Add credit limit</p>
                                  <input 
                                    type="text" 
                                    name="credit_limit" 
                                    id="credit_limit" 
                                    placeholder="Enter credit limit"
                                    class="input-form"
                                    style="width:300px; font-size: 14px;"
                                    required
                                >
                            </div>

                            <div class="modal-option-groups">
                                <p>Product requiremnets</p>

                                <table>
                                    <thead>
                                        <tr>
                                            <td>Product ID</td>
                                            <td>Product name</td>
                                            <td>Base price</td>
                                            <td>Agreed price</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ( $productRequirements as $product)
                                            <tr>
                                                <td>{{$product->product_id}}</td>
                                                <td>{{$product->product->name}}</td>
                                                <td>₱{{$product->product->base_price}}</td>
                                                <td>₱
                                                    <input 
                                                        type="number" 
                                                        name="products[{{ $product->product_id }}][nego_price]" 
                                                        class="decimal-input"
                                                        style="width: 80px"
                                                        step="0.01"
                                                        placeholder="0.00"
                                                    />
                                                    <input type="hidden" name="products[{{ $product->product_id }}][product_id]" value="{{ $product->product_id }}">

                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>

                                </table>
                                    
                                
                               
                            </div>





                    
                   
                        </div>

                        <input type="hidden" name="supplier_id" value="{{ $supplier->supplier_id }}">
                        <input type="hidden" name="user_id" value="{{ $supplier->user->user_id }}">

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit action</button>
                        </div>
                    </form>





                </div>
            </div>

            <div class="modal fade" id="modify-action" tabindex="-1" aria-labelledby="requestActionLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form class="modal-content" method="POST" action="{{ route('supplier.confirm') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <p class="modal-title">Supplier confirm action</p>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body">
                        <p class="note-notify">
                            <span class="material-symbols-outlined"> warning </span>
                            <span>Any actiosn comitted would notify the suppleir</span>
                        </p>

                        <!-- Status selection -->
                        <div class="modal-option-groups">
                            <p>Freeze account</p>
                            <button>Freeze account</button>
                            <button>Unfreeze account</button>

                        </div>
                        <div class="modal-option-groups">
                            <p>Reason to freezing account</p>
                            <input type="text" name="freezing_note" >
                            
                        </div>
                        <!-- Assign staff -->
                        <div class="modal-option-groups">
                            <p>Assign a new sales agent</p>
                            <select name="staff_id" class="form-control" required>
                                <option value="">-- Select Sales Agent --</option>
                                @foreach($staffs as $staff)
                                    <option value="{{ $staff->staff->staff_id }}">
                                        {{ $staff->staff->firstname }} {{ $staff->staff->lastname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>


                    </div>

                    <input type="hidden" name="supplier_id" value="{{ $supplier->supplier_id }}">
                    <input type="hidden" name="user_id" value="{{ $supplier->user->user_id }}">

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit action</button>
                    </div>
                </form>



            </div>
            </div>

            {{-- add new product to user requirements --}}
            <div class="modal fade" id="add-product-modal" tabindex="-1" aria-labelledby="requestActionLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form class="modal-content" method="POST" action="{{ route('supplier.confirm') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <p class="modal-title">Add product to requirements</p>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <input type="hidden" name="set_id" id="modal-set-id">
                    <input type="hidden" name="supplier_id" id="modal-supplier-id">

                    <div class="modal-body">
                        <p class="note-notify">
                            <span class="material-symbols-outlined"> warning </span>
                            <span>Any action comitted would notify the supplier</span>
                        </p>

                        <!-- Status selection -->
                     

                    </div>

                    <input type="hidden" name="supplier_id" value="{{ $supplier->supplier_id }}">
                    <input type="hidden" name="user_id" value="{{ $supplier->user->user_id }}">

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit action</button>
                    </div>
                </form>



            </div>
            </div>

            {{-- edit products in each product requirements row --}}
            <div class="modal fade" id="edit-row-action" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <p class="modal-title">Product requirement</p>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <div class="modal-action-con">
                                
                                <div class="action-buttons" style="border-bottom: #f8912a30 2px solid">
                                    <button onclick="setActiveButton(this); showModifyForm()">Modify Product</button>
                                    <button onclick="setActiveButton(this); showSaleForm()">
                                        <span class="material-symbols-outlined">percent_discount</span>
                                        Set on Sale
                                    </button>
                                </div>

                            </div>


                        <form class="modify-product-form" id="modify-form" style="display:none;" method="POST" action="{{ route('productset.modify') }}">
                            @csrf
                            <input type="hidden" id="edit-modal-set-id" name="set_id">
                            <input type="hidden" id="edit-modal-supplier-id" name="supplier_id">

                            <div class="form-group">
                                <label class="form-label">Product</label>
                                <input type="text" id="modify-product-name" style="width: 300px; color: #333;" disabled>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Price</label>
                                <input type="number" step="0.01" placeholder="00.00" name="price" id="modify-price" style="width: 200px">
                            </div>
                            
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remove" value="1" id="modal-remove">
                                <label class="form-check-label" for="modal-remove">Remove this requirement</label>
                            </div>

                            <button type="submit" class="btn-transition">Update Product</button>
                        </form>

                    {{-- add price sale form --}}
                    <form class="modify-product-form product-form" id="sale-form" style="display:none;" method="POST" action="{{ route('productset.sale') }}">
                        @csrf
                        <input type="hidden" id="sale-form-set-id" name="set_id">
                        <input type="hidden" id="sale-form-supplier-id" name="supplier_id">

                        <p class="note-notify">
                            <span class="material-symbols-outlined">info</span>
                            <span>This promotion will automatically apply starting from the selected date.</span>
                        </p>

                        <div class="form-group">
                            <label class="form-label">Sale product</label>
                            <input type="text" id="sale-product-name" style="width: 300px; color: #333;" disabled>
                        </div>

                        <input type="hidden" id="sale-base-price">

                        <div class="form-group">
                            <label class="form-label" for="sale_price">Selling price (₱)</label>
                            <input type="number" step="0.01" placeholder="00.00" name="sale_price" id="sale_price" style="width: 200px" required>
                        </div>

                        <div class="form-group" style="box-shadow: #f8912a30 0px 0px 0px 3px; margin: 5px; border-radius: 5px; padding: 10px;">
                            <label class="form-label">Effectivity</label>
                            <div class="form-calendar" style="gap: 5px">
                                <div>
                                    <label for="start_date">Start date</label>
                                    <input type="datetime-local" id="start_date" style="font-size: 13px; color: #666;" name="start_date" required>
                                </div>
                                <div>
                                    <label for="end_date">End date</label>
                                    <input type="datetime-local" id="end_date" style="font-size: 13px; color: #666;" name="end_date" required>
                                </div>
                            </div>
                        </div>

                        {{-- Live promotion preview --}}
                        <div id="promo-preview" style="display: none; margin: 15px 0; padding: 15px; background: linear-gradient(135deg, #f8912a 0%, #ff6b35 100%); border-radius: 8px; color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                                <span class="material-symbols-outlined" style="font-size: 24px;">percent_discount</span>
                                <p style="margin: 0; font-weight: bold;">Promotion Preview</p>
                            </div>
                            <div id="promo-content" style="background: rgba(255,255,255,0.2); padding: 12px; border-radius: 5px; backdrop-filter: blur(10px);">
                                <p style="margin: 0; font-size: 14px; font-weight: bold;" id="promo-product-name">Product Name</p>
                                <p style="margin: 8px 0; font-size: 14px;">
                                    <span style="text-decoration: line-through; opacity: 0.8;">₱<span id="promo-old-price">0.00</span></span>
                                    <span style="font-size: 20px; font-weight: bold; margin-left: 10px;">₱<span id="promo-new-price">0.00</span></span>
                                    <span id="promo-discount-badge" style="background: rgba(255,255,255,0.3); padding: 4px 8px; border-radius: 4px; font-size: 12px; margin-left: 10px; font-weight: bold;">0% OFF</span>
                                </p>
                                <p style="margin: 0; font-size: 13px; opacity: 0.9;">
                                    <span id="promo-duration">0</span> day/s sale from 
                                    <span id="promo-dates">date range</span>
                                </p>
                            </div>
                        </div>

                        <button type="submit" class="btn-transition">Apply promotion</button>
                    </form>



                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                    </div>
                </div>
            </div>

            {{-- account control --}}
            <div class="modal fade" id="account-control" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <form class="modal-content" method="POST" action="{{ route('productset.modify') }}" style="width: 600px">
                    @csrf
                    <div class="modal-header">
                        <p class="modal-title">Account control</p>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <p class="note-notify">
                        <span class="material-symbols-outlined"> warning </span>
                        <span>Any action committed will notify the supplier</span>
                        </p>
                    
                        <input type="hidden" id="edit-modal-set-id" name="set_id">
                        <input type="hidden" name="supplier_id" id="edit-modal-supplier-id">
                        <div class="action-btn">
                            <button class="suspend" style="background-color: #e60b06 ">
                                Suspend
                            </button>
                            <button class="activate" style="background-color: #37e606 ">
                                Activate
                            </button>
                            <button class="edit" style="background-color: gray ">
                                Edit
                            </button>
                        </div>

                        <style>
                            .action-btn{
                                display: flex;
                                flex-direction: row;
                                 justify-content: space-evenly;
                            }
                            .action-btn button{
                                transition: background-color 0.3s ease, color 0.3s ease;
                                color: #fff;
                                border: none;
                                box-shadow: rgba(0, 0, 0, 0.15) 1.95px 1.95px 2.6px;
                                border-radius: 5px;
                                padding: 10px;
                                min-width: 150px;
                                display: flex;
                                flex-direction: row;
                                align-items: center;
                                justify-content: center;
                                gap: 10px

                            }
                        </style>

               

                   
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
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
                        <p>
                            <a href="{{ route('customers.list') }}">< Supplier list</a>
                        </p>
                    </div>

                    <div class="title-actions">
                        <p class="heading">Supplier's profile</p>

                        {{-- @if (Auth()->user()->role !== 'Staff')
                            <div>
                                <button data-bs-toggle="modal" data-bs-target="#account-control" class="btn-transition">
                                    <span class="material-symbols-outlined"> joystick</span>
                                    Account controls
                                </button>
                            </div>
                        @endif --}}

                        {{-- @if (optional($supplier->account_status)->account_status == null && $accStatus->account_status === "Pending")
                            <div>
                                <button data-bs-toggle="modal" data-bs-target="#request-action" class="btn-transition">File an action</button>
                            </div>
                        @endif --}}


                        @if ($accStatus->account_status === 'Declined')
                            <div>
                                <p>This user was declined due to: {{$accStatus->reason_to_decline}}</p>
                                waiting for supplier to modify their request
                            </div>
                        @elseif ($accStatus->account_status === 'Pending')
                            <button data-bs-toggle="modal" data-bs-target="#request-action" class="btn-transition">File an action</button>

                        @elseif ($accStatus->account_status === 'Accepted')
                            <div>
                                <button class="btn-transition" data-bs-toggle="modal" data-bs-target="#modify-action">File an action</button>
                            </div>
                  
                        @endif

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
                                </div>

                            </div>
                        </div>

                    </div>

                    <div class="sales-person-div">
                            <p class="sales-title">Sales person</p>
                            <div class="sales-person">
                                    @php
                                        $imgSrc = ($staffAgent && $staffAgent->user && $staffAgent->user->image)
                                            ? 'data:' . $staffAgent->user->image_mime_type . ';base64,' . base64_encode($staffAgent->user->image)
                                            : asset('images/default-avatar.png');
                                    @endphp

                                    <img class="supplier-image" src="{{ $imgSrc }}" alt="Staff Profile Image">



                                    @if ($staffAgent !== NULL)
                                        <p class="name-title">
                                            <span style="font-size: 13px;  color: #333;">
                                                {{$staffAgent->lastname}},
                                                {{$staffAgent->firstname}}
                                                {{$staffAgent->middlename}}

                                            </span>
                                            <span style=" font-size: 12px;">Sales agent</span>
                                            <span style="margin: 0; font-size: 12px;">Approved at July 21, 2025</span>
                                        </p>
                                    @else
                                            <span style="margin: 0; font-size: 12px;">No approved sales agent yet</span>

                                    @endif

                            </div>
                    
                    </div>

                    <div class="tab-div">

                        <div class="tabs" role="tablist">
                            <button class="tab-button active" data-tab="product" role="tab" aria-selected="true" aria-controls="product-content" id="product-tab">
                            Product requirements
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

                        {{-- product requirements --}}
                        <div id="product-content" class="tab-content active" role="tabpanel" aria-labelledby="product-tab">
                            <div class="profile-mid" >

                                <div class="authorized-staffs" style="box-shadow: none">
                                    <p style="margin-bottom: 5px">On sale</p>
                                    <div class="rep-sign-tables" style="width: 100%; display: flex; flex-direction: row; gap: 5px;">
                                        <div class="authorized-rep sale-div">
                                            @foreach ( $sales as $sale)
                                                <div>
                                                    <p style="display: flex; gap: 5px; align-items: center;">
                                                        <span class="material-symbols-outlined">
                                                            percent_discount
                                                        </span>
                                                        <span>{{$sale->set->product->name}}</span>
                                                    </p>
                                                    <p>
                                                        {{ \Carbon\Carbon::parse($sale->start_date)->format('M d, Y h:i A') }}
                                                        -
                                                        {{ \Carbon\Carbon::parse($sale->end_date)->format('M d, Y h:i A') }}
                                                    </p>
                                                    @php
                                                        $hours = \Carbon\Carbon::parse($sale->start_date)->diffInHours(\Carbon\Carbon::parse($sale->end_date));
                                                        $days = round($hours / 24, 1);
                                                    @endphp

                                                    <p>{{ $days }} day/s sale</p>

                                                    @php
                                                        $original = $sale->set->nego_price;
                                                        $salePrice = $sale->sale_price;
                                                        $discount = $original > 0 ? round((($original - $salePrice) / $original) * 100) : 0;
                                                    @endphp

                                                    <p>
                                                        <span style="text-decoration: line-through">₱{{ number_format($original, 2) }}</span>
                                                        <span>₱{{ number_format($salePrice, 2) }}</span>
                                                        <span>({{ $discount }}% off)</span>
                                                    </p>





                                                </div>
                                            
                                            @endforeach
                                            
                                        </div>

                                    </div>
                                </div>

                                <div class="authorized-staffs" style="box-shadow: none">
                                    <p style="margin-bottom: 5px">Price change</p>
                                    <div class="rep-sign-tables" style="width: 100%; display: flex; flex-direction: row; gap: 5px;">
                                        <div class="authorized-rep">
                                            <div>
                                                <a href="">See price history</a>
                                            </div>
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
                                                        <td>{{ $productRequirement->settings->nego_price }}</td>
                                                        <td>
                                                            <button  
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#edit-row-action" 
                                                                class="btn-span edit-product-btn"
                                                                data-set-id="{{ $productRequirement->settings->set_id }}"
                                                                data-price="{{ $productRequirement->settings->nego_price }}"
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

                                <div class="authorized-staffs" style="box-shadow: none">
                                    <p style="margin-bottom: 5px">Summary list</p>
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
                                                        <td>{{ $productRequirement->settings->nego_price }}</td>
                                                        <td>
                                                            <button  
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#edit-row-action" 
                                                                class="btn-span edit-product-btn"
                                                                data-set-id="{{ $productRequirement->set_id }}"
                                                                data-price="{{ $productRequirement->nego_price }}"
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
                                                <span>
                                                    {{ implode(', ', array_filter([
                                                        $account_status->staff->lastname,
                                                        $account_status->staff->firstname
                                                        ]))
                                                    }}
                                                    
                                                </span>

                                            </p>
                                            <p>
                                                <span style="color:#666">Approved at</span>
                                                <span>{{ \Carbon\Carbon::parse($account_status->approved_at)->format('F j, Y') }}</span>
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
    <script src="{{ asset('js/global/money-format.js') }}"></script>
    <script src="{{asset('js/global/decimal-input.js')}}"></script>
    <script src="{{ asset('js/global/x/switch-form.js') }}"></script>


    <script>
        const filterUrl = "{{ route('products.filter') }}";
    </script>
    <script src="{{ asset('js/global/edit-product.js') }}"></script>
    <script src="{{ asset('js/global/filter-products.js') }}"></script>
    <script src="{{ asset('js/global/modal-hide-input.js') }}"></script>
    <script src="{{ asset('js/global/alert-timeout.js') }}"></script>
    <script src="{{ asset('js/global/view-esignature.js') }}"></script>

    <script src="{{ asset('js/global/modal/add-product-user.js') }}"></script>
     
    <script src="{{ asset('js/global/format-currency.js') }}"></script>

</script>


@endpush
