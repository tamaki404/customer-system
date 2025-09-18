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

            <script>
                setTimeout(function() {
                    const alerts = document.querySelectorAll('.alert');
                    alerts.forEach(function(alert) {
                        alert.style.transition = 'opacity 0.5s';
                        alert.style.opacity = '0';
                        setTimeout(function() {
                            alert.remove();
                        }, 500);
                    });
                }, 5000);
            </script>

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
                    <option value="Processing">Yes, confirm supplier's request</option>
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
        </div>

        <input type="hidden" name="supplier_id" value="{{ $supplier->supplier_id }}">
        <input type="hidden" name="user_id" value="{{ $supplier->user->user_id }}">

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Submit action</button>
        </div>
    </form>

        <script>
        document.addEventListener("DOMContentLoaded", function () {
            const accStatus = document.getElementById("acc_status");
            const reasonGroup = document.getElementById("reason_group");
            const reasonSelect = document.getElementById("reason_to_decline");

            function toggleReasonField() {
                if (accStatus.value === "Declined") {
                    reasonGroup.style.display = "block";
                    reasonSelect.setAttribute("required", "required");
                } else {
                    reasonGroup.style.display = "none";
                    reasonSelect.removeAttribute("required");
                    reasonSelect.value = ""; // reset selection
                }
            }

            // Run once on page load
            toggleReasonField();

            // Run every time status changes
            accStatus.addEventListener("change", toggleReasonField);
        });
        </script>

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

                <div>
                    <button data-bs-toggle="modal" data-bs-target="#request-action" class="btn-transition">File an action</button>
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
                            $imgSrc = auth()->user()->image 
                                ? ('data:' . auth()->user()->image_mime_type . ';base64,' . base64_encode(auth()->user()->image))
                                : asset('images/default-avatar.png');
                        @endphp
                        <img class="supplier-image" src="{{ $imgSrc }}" alt="Profile Image">  
                        <p class="name-title">
                            <span style="font-size: 13px;  color: #333;">Lastname, Firstname</span>
                            <span style=" font-size: 12px;">Sales agent</span>
                            <span style="margin: 0; font-size: 12px;">Approved at July 21, 2025</span>
                        </p>
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

@endpush