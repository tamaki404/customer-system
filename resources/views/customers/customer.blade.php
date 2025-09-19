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


            <div class="modal fade" id="request-action" tabindex="-1" aria-labelledby="requestActionLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form class="modal-content" method="POST" action="{{ route('supplier.confirm') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <p class="modal-title">Supplier request action</p>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body">
                        <p class="note-notify">
                            <span class="material-symbols-outlined"> warning </span>
                            <span>Review the profile before taking any action on this request.</span>
                        </p>

                        <!-- Status selection -->
                        <div class="modal-option-groups">
                            <p>Do you want to accept this supplier's request to join the system?</p>
                            <select name="acc_status" id="acc_status" required>
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
                                <option value="">-- Select Sales Agent --</option>
                                @foreach($staffs as $staff)
                                    <option value="{{ $staff->staff->staff_id }}">
                                        {{ $staff->staff->firstname }} {{ $staff->staff->lastname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Product requirement section -->
                        <div class="modal-option-groups">
                            <p>Select required products for this supplier:</p>

                            <div class="row mb-2">
                                <div class="col">
                                    <select id="filter-category" class="form-control">
                                        <option value="">-- Category --</option>
                                        <option value="Frozen">Frozen</option>
                                        <option value="Cuts">Cuts</option>
                                        <option value="Eggs">Eggs</option>
                                        <option value="Processed">Processed</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <select id="filter-unit" class="form-control">
                                        <option value="">-- Unit --</option>
                                        <option value="Pack">Pack</option>
                                        <option value="Box">Box</option>
                                        <option value="Bag">Bag</option>
                                        <option value="Piece">Piece</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <select id="filter-weight" class="form-control">
                                        <option value="">-- Weight --</option>
                                        <option value="Kilogram">Kilogram</option>
                                        <option value="Gram">Gram</option>
                                        <option value="Piece">Piece</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <button type="button" class="btn btn-secondary" onclick="filterProducts()">Search</button>
                                </div>
                            </div>

                            <div id="product-results">
                                <!-- Filtered products will appear here -->
                            </div>

                            <hr>

                            <h6>Selected Products</h6>
                            <table class="table" id="selected-products">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Unit</th>
                                        <th>Weight</th>
                                        <th>Price</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Rows added dynamically -->
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
                <form class="modal-content" method="POST" action="{{ route('productset.modify') }}">
                @csrf
                <div class="modal-header">
                    <p class="modal-title">Modify Product Requirement</p>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <p class="note-notify">
                    <span class="material-symbols-outlined"> warning </span>
                    <span>Any action committed will notify the supplier</span>
                    </p>

                    <input type="hidden" name="set_id" id="modal-set-id">
                    <input type="hidden" name="supplier_id" id="modal-supplier-id">

                    <div class="mb-3">
                    <label class="form-label">Product</label>
                    <input type="text" class="form-control" id="modal-product-name" disabled>
                    </div>

                    <div class="mb-3">
                    <label class="form-label">Price</label>
                    <input type="number" step="0.01" class="form-control" name="price" id="modal-price">
                    </div>

                    <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remove" value="1" id="modal-remove">
                    <label class="form-check-label" for="modal-remove">
                        Remove this requirement
                    </label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
                </form>
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

                        {{-- @if (optional($supplier->account_status)->acc_status == null && $accStatus->acc_status === "Pending")
                            <div>
                                <button data-bs-toggle="modal" data-bs-target="#request-action" class="btn-transition">File an action</button>
                            </div>
                        @endif --}}


                        @if ($accStatus->acc_status === 'Declined')
                            <div>
                                <p>This user was declined due to: {{$accStatus->reason_to_decline}}</p>
                                waiting for supplier to modify their request
                            </div>
                        @elseif ($accStatus->acc_status === 'Pending')
                            <button data-bs-toggle="modal" data-bs-target="#request-action" class="btn-transition">File an action</button>

                        @elseif ($accStatus->acc_status === 'Accepted')
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
                                <p class="company-status" style="margin: 0">
                                    <span class="company-name">{{  $supplier->company_name }}</span>
                                    <span class="user-status">{{$supplier->user->status}}</span>
                                </p>
                                <div class="details-address">
                                    <p class="address-div">
                                        <span class="material-symbols-outlined icon" title="Home/ Head office address">home_work</span>
                                        <span class="div-text">
                                            {{ implode(', ', array_filter([
                                                $supplier->home_street,
                                                $supplier->home_subdivision,
                                                $supplier->home_barangay,
                                                $supplier->home_city,
                                            ])) }}
                                        </span>
                                    </p>
                                    <p class="address-div">
                                        <span class="material-symbols-outlined icon" title="Office address">domain</span>
                                        <span class="div-text">
                                            {{ implode(', ', array_filter([
                                                $supplier->office_street,
                                                $supplier->office_subdivision,
                                                $supplier->office_barangay,
                                                $supplier->office_city, 
                                            ])) }}
                                        </span>
                                    </p>
                                </div>
                                <div class="details-contact" style="margin-top: 5px; margin-left: ;">
                                    <p>
                                        <span class="material-symbols-outlined icon" title="Mobile number">mobile</span>
                                        <span class="div-text">{{$supplier->mobile_no}}</span>
                                    </p>
                                    <span>|</span>
                                    <p>
                                        <span class="material-symbols-outlined icon" title="Telephone number">call</span>
                                        <span class="div-text">{{$supplier->telephone_no}}</span>

                                    </p>
                                    <span>|</span>
                                    <p>
                                        <span class="material-symbols-outlined icon" title="Email address">mail</span>
                                        <span class="div-text">{{$supplier->user->email_address}}</span>

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
                                                {{$staffAgent->lastname}}
                                                {{$staffAgent->firstname}},
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


                    <div class="profile-mid">
                        <div class="authorized-staffs" >
                            <p style="margin-bottom: 5px">Product requirements</p>
                            <di class="rep-sign-tables" style="width: 100%; display: flex; flex-direction: row; gap: 5px;">
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
                                                <td>{{ $productRequirement->product->category }}</td>
                                                <td>{{ $productRequirement->product->name }}</td>
                                                <td>{{ $productRequirement->product->unit }}</td>
                                                <td>{{ $productRequirement->product->measurement }}</td>
                                                <td>{{ $productRequirement->price }}</td>
                                                <td>
                                                    <button  
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#edit-row-action" 
                                                        class="btn-span edit-product-btn"
                                                        data-set-id="{{ $productRequirement->set_id }}"
                                                        data-price="{{ $productRequirement->price }}"
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
                                        

                            </di>
                        </div>
                    </div>

                    <div class="profile-mid">

                        
                        <div class="authorized-staffs" >
                            <p style="margin-bottom: 5px">Authorized staffs</p>
                            <di class="rep-sign-tables" style="width: 100%; display: flex; flex-direction: row; gap: 5px;">
                                <div class="authorized-rep">
                                    <p style="font-weight: normal; font-size: 13px;">Representative/s </p>
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
                                            <tr>
                                                <td>1</td>
                                                <td>
                                                    {{ implode(', ', array_filter([
                                                        $supplier->representative->rep_last_name,
                                                        $supplier->representative->rep_first_name,
                                                        $supplier->representative->rep_middle_name,
                                                    ])) }}
                                                </td>
                                                <td>{{ $supplier->representative->rep_relationship}}</td>
                                                <td>{{ $supplier->representative->rep_contact_no}}</td>

                                            </tr>
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
                                                <th>Contact number</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                        <td>1</td>
                                        <td>
                                            {{ implode(', ', array_filter([
                                                $supplier->signatory->signatory_last_name,
                                                $supplier->signatory->signatory_first_name,
                                                $supplier->signatory->signatory_middle_name,
                                            ])) }}
                                        </td>
                                        <td>{{ $supplier->signatory->signatory_relationship}}</td>
                                        <td>{{ $supplier->signatory->signatory_contact_no}}</td>

                                        </tr>
                                    </tbody>
                                    </table>
                                </div>                        

                            </di>
                        </div>
                        <div class="authorized-staffs" style="margin-top: 10px">
                            <p style="margin-bottom: 5px">Bank details</p>
                            <di class="rep-sign-tables" style="width: 100%; display: flex; flex-direction: row; gap: 5px;">
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

                            </di>


                        </div>
                        <div class="authorized-staffs" style="margin-top: 10px">
                            <p style="margin-bottom: 5px">Referral</p>
                            <di class="rep-sign-tables" style="width: 100%; display: flex; flex-direction: row; gap: 5px; ">
                                <div class="questions-list">
                                    <p>
                                        <span style="color:#666">How long have you known salesman? </span>
                                        <span>{{$supplier->salesman_relationship}}</span>
                                    </p>
                                    <p>
                                        <span style="color:#666">Weekly volume (tray/head)</span>
                                        <span>{{$supplier->weekly_volume}}</span>
                                    </p>
                                    <p>
                                        <span style="color:#666">Date acquired</span>
                                        <span>{{$supplier->date_required}}</span>
                                    </p>
                                    <p>
                                        <span style="color:#666">Other products interested in</span>
                                        <span>{{$supplier->other_products_interest}}</span>
                                    </p>
                                    <p>
                                        <span style="color:#666">Referred by</span>
                                        <span>{{$supplier->referred_by}}</span>
                                    </p>
                            
                                </div>

                            </di>


                        </div>
                    </div>

                    <div class="documents-div">
                        <div>
                            <p class="title-data">Documents</p>
                            <div class="documents-list">
                                @foreach ($documents as $document)
                                    @php
                                        $imgSrc = $document->file
                                            ? 'data:' . $document->file_mime . ';base64,' . base64_encode($document->file)
                                            : asset('images/default-avatar.png');

                                        $modalId = 'documentModal' . ($document->id ?? $loop->index);
                                    @endphp

                                    <div class="document-group">
                                        <p>{{ $document->type }}</p>
                                        <img class="supplier-image"
                                            src="{{ $imgSrc }}"
                                            alt="Document"
                                            data-bs-toggle="modal"
                                            data-bs-target="#{{ $modalId }}">
                                    </div>

                                    <!-- Modal -->
                                    <div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered" style="height: 100%">
                                            <div class="modal-content" >
                                                <div class="modal-header">
                                                    <p class="modal-title" >{{ $document->type }}</p>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <img src="{{ $imgSrc }}" class="img-fluid" alt="Document Preview">
                                                </div>
                                                <div class="modal-footer">
                                                    {{-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Download</button> --}}
                                                    <button type="button" class="btn btn-primary" style="align-content: center; justify-content: center; display: flex; gap: 5px">
                                                        <span class="material-symbols-outlined" style="font-size: 17px">
                                                            download
                                                        </span>
                                                        <span>Download</span>
                                                    </button>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                            </div>

                        </div>
                    </div>

                
            
                </div>


        </div>

@endsection



@push('scripts')

  
    <script src="{{ asset('js/global/edit-product.js') }}"></script>
    <script src="{{ asset('js/global/filter-products.js') }}"></script>
    <script src="{{ asset('js/global/modal-hide-input.js') }}"></script>
    <script src="{{ asset('js/global/alert-timeout.js') }}"></script>
    <script src="{{ asset('js/global/modal/add-product-user.js') }}"></script>


@endpush
