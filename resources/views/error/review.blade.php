
@extends('layouts.main')


@push('styles')
    <link rel="stylesheet" href="{{ asset('css/error/review.css') }}">
@endpush


@section('content')
    @if (session('success') || session('error'))
        @php
            $isSuccess = session('success') !== null;
            $message = $isSuccess ? session('success') : session('error');
        @endphp
        <div id="flash-message" class="flash-message alert {{ $isSuccess ? 'alert-success' : 'alert-danger' }}">
            {{ $message }}
        </div>
    @endif

    <!-- Confirm supplier request modal -->
    <div class="modal fade" id="request-action" tabindex="-1" aria-labelledby="requestActionLabel" aria-hidden="true" >
        <div class="modal-dialog modal-dialog-centered" style="justify-content: start; display: flex; align-items: start;">
            <form class="modal-content" method="POST" action="{{ route('review.confirm') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" style="font-size: 15px; color: #333;font-weight: bold;">Supplier Request Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <div class="note-notify">
                        <span class="material-symbols-outlined">info</span>
                        <span>If you confirm this user, they'll be pending for approval and you have to confirm them</span>
                    </div>
                    
                    <!-- Status selection -->
                    <div class="modal-option-groups">
                        <p>Are the modified data correct now?</p>
                        <select name="account_status" id="status" required>
                            <option value="">-- Select status --</option>
                            <option value="Accepted">Yes, all are good now</option>
                            <option value="Declined">No, there's something wrong</option>
                        </select>
                    </div>
                    
                    <!-- Reason to decline (shown only when Declined is selected) -->
                    <div class="modal-option-groups" id="feedback-container" style="display: flex; flex-direction: column;">
                        <p>Kindly specify what needs to be changed and the reason, be specific and on point</p>
                        <textarea 
                            style="width: 100%;
                                    border-radius: 10px;
                                    padding: 10px;
                                    border: none;
                                    box-shadow: rgba(0, 0, 0, 0.02) 0px 1px 3px 0px, rgba(27, 31, 35, 0.15) 0px 0px 0px 1px;
                                    font-size: 14px;
                                    outline: none;"
                            name="review_feedback" 
                            id="feedback" 
                            rows="4" 
                            placeholder="Provide detailed feedback here..."
                            maxlength="500"
                        ></textarea>
                        <small class="text-muted">Maximum 500 characters</small>
                    </div>
                </div>
                
                <input type="hidden" name="supplier_id" value="{{ $supplier->supplier_id }}">
                <input type="hidden" name="reviewed_by" value="{{ Auth()->user()->user_id }}">
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit Action</button>
                </div>
            </form>
        </div>
    </div>
    

    <div class="content-bg">
        <div class="content-header">
            <div class="contents-display">
                <p>
                    <a href="{{ route('customers.list') }}">< Supplier profile</a>
                </p>

            </div>
            <div class="title-actions">
                <p class="heading">Review modified data</p>
                <button 
                    class="btn-transition"
                    type="button"
                    data-bs-toggle="modal" 
                    data-bs-target="#request-action">
                    <span class="material-symbols-outlined">approval_delegation</span>
                    File an Action
                </button>
            </div>
        </div>
        <div class="content-body" style="padding: 10px; border: none; height: auto;">
            <div>
                <p>Report details: {{ $review->head }}</p>

                @if ($review->review_feedback !== NULL)
                    <p>
                        <span>Raised</span>
                        <span>Declined by: {{ $review->raised_by }} at {{ $review->raised_at }}</span>
                        <span>Feedback: {{ $review->body }}</span>
                    </p>                    
                    <p>
                        <span>Reviewed (Declined again)</span>
                        <span>Reviewed by: {{ $review->reviewed_by }} at {{ $review->reviewed_at }}</span>
                        <span>Reviewed (feedback): {{ $review->review_feedback }}</span>
                    </p>
                @else

                @endif

            </div>
            @if ($reason === "ID image and details")
                <div>
                    <div class="section-row">
                        <section class="group-details first_id">
                            <p class="group-name">ID Details Verification</p>
                            <div class="form-list">
                                <div class="input-forms">
                                    <p>ID Image</p>
                                    @php
                                        $mime = $supplier->id_image ? finfo_buffer(finfo_open(), $supplier->id_image, FILEINFO_MIME_TYPE) : null;
                                        $imgSrc = $supplier->id_image
                                            ? 'data:' . $mime . ';base64,' . base64_encode($supplier->id_image)
                                            : asset('assets/default-company-logo.png');
                                    @endphp
                                    <div class="image-preview" id="imagePreview">
                                        <img style="height: 250px; width: auto;" id="preview-img" src="{{ $imgSrc }}" alt="ID Preview">
                                    </div>
                                </div>
                                <div class="details-display">
                                    <p>
                                        <strong>Type of ID </strong>
                                        <span>.................................</span>
                                        <span></span> {{ $supplier->id_type }}</span>
                                    </p>
                                    <p>
                                        <strong>ID Number </strong>
                                        <span>.................................</span>
                                        <span></span> {{ $supplier->id_number }}</span>
                                    </p>
                                    <p>
                                        <strong>Birthdate </strong>
                                        <span>.................................</span>
                                        <span></span> {{ $supplier->birthdate }}</span>
                                    </p>
                                </div>
                 
                            </div>
                        </section>

                        <section class="group-details second_id">
                            <p class="group-name">Valid IDs Documentation</p>

                            <div class="pdf-documents-row">
                                @php
                                    $validOne = isset($documents) ? $documents->firstWhere('type', 'valid_one') : null;
                                    $validTwo = isset($documents) ? $documents->firstWhere('type', 'valid_two') : null;
                                @endphp

                                {{-- VALID ID (1) --}}
                                @php
                                    $pdfBlob = $validOne->file ?? null;
                                    $mime = $pdfBlob ? finfo_buffer(finfo_open(), $pdfBlob, FILEINFO_MIME_TYPE) : 'application/pdf';
                                    $pdfData1 = $pdfBlob ? 'data:' . $mime . ';base64,' . base64_encode($pdfBlob) : null;
                                    $modalId1 = 'validOneModal';
                                @endphp

                                <div class="doc-preview-box text-center" style="width: 220px;">
                                    <p><strong>Valid ID (1)</strong></p>
                                    <div class="card shadow-sm border-0 rounded-3 overflow-hidden"
                                        style="cursor: pointer; height: 300px;"
                                        data-bs-toggle="modal" data-bs-target="#{{ $modalId1 }}">
                                        <div class="ratio ratio-4x3 bg-light" style="height: 80%">
                                            @if ($pdfData1)
                                                <iframe
                                                    src="{{ $pdfData1 }}#toolbar=0&navpanes=0&scrollbar=0&page=1&"
                                                    style="width: 100%; height: 100%; pointer-events: none; border: none;"
                                                    title="Valid ID (1) Preview"
                                                ></iframe>
                                            @else
                                                <p class="text-danger">No document uploaded yet</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Modal for VALID ID (1) --}}
                                <div class="modal fade" id="{{ $modalId1 }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-xl modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Valid ID (1)</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center" style="height: 80vh;">
                                                @if ($pdfData1)
                                                    <iframe
                                                        src="{{ $pdfData1 }}"
                                                        style="width: 100%; height: 100%; border: none;"
                                                        title="Valid ID (1) Full View"
                                                    ></iframe>
                                                @else
                                                    <p class="text-danger">Unable to load document.</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- VALID ID (2) --}}
                                @php
                                    $pdfBlob = $validTwo->file ?? null;
                                    $mime = $pdfBlob ? finfo_buffer(finfo_open(), $pdfBlob, FILEINFO_MIME_TYPE) : 'application/pdf';
                                    $pdfData2 = $pdfBlob ? 'data:' . $mime . ';base64,' . base64_encode($pdfBlob) : null;
                                    $modalId2 = 'validTwoModal';
                                @endphp

                                <div class="doc-preview-box text-center" style="width: 220px;">
                                    <p><strong>Valid ID (2)</strong></p>
                                    <div class="card shadow-sm border-0 rounded-3 overflow-hidden"
                                        style="cursor: pointer; height: 300px;"
                                        data-bs-toggle="modal" data-bs-target="#{{ $modalId2 }}">
                                        <div class="ratio ratio-4x3 bg-light" style="height: 80%">
                                            @if ($pdfData2)
                                                <iframe
                                                    src="{{ $pdfData2 }}#toolbar=0&navpanes=0&scrollbar=0&page=1&"
                                                    style="width: 100%; height: 100%; pointer-events: none; border: none;"
                                                    title="Valid ID (2) Preview"
                                                ></iframe>
                                            @else
                                                <p class="text-danger">No document uploaded yet</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Modal for VALID ID (2) --}}
                                <div class="modal fade" id="{{ $modalId2 }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-xl modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Valid ID (2)</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center" style="height: 80vh;">
                                                @if ($pdfData2)
                                                    <iframe
                                                        src="{{ $pdfData2 }}"
                                                        style="width: 100%; height: 100%; border: none;"
                                                        title="Valid ID (2) Full View"
                                                    ></iframe>
                                                @else
                                                    <p class="text-danger">Unable to load document.</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </section>
                    </div>
                </div>
            @elseif ($reason === "Bank details")
                <div>
                    <div class="section-row" style="width: 60%">
                        <section class="group-details first_id" style="margin-top: 5px">
                            <p class="group-name">Bank details</p>
                            <div class="form-list">

                                <style>
                                    .details-display p .label{
                                        color: #666;
                                        font-size: 13px;
                                    }
                                </style>
                                <div class="details-display">
                                    <p>
                                        <span class="label">Account name </span>
                                        <span>{{$bank->account_name}}</span>
                                    </p>
                                    <p>
                                        <span class="label">Account number</span>
                                        <span>{{$bank->account_number}}</span>
                                    </p>
                                    <p>
                                        <span class="label">Bank</span>
                                        <span>{{ $bank->bank }}</span>
                                    </p>

                                    <p>
                                        <span class="label">Branch </span>
                                        <span>{{$bank->branch}}</span>
                                    </p>
                
                                </div>


                            </div>
                        </section>
                    </div>


                </div>            
            @elseif ($reason === "Necessary documents")
                <div>
                    <div class="section-row">
                        <section class="group-details first_id">
                            <p class="group-name">ID Details Verification</p>
                            <div class="pdf-documents-grid d-flex flex-row flex-wrap gap-3 justify-content-around w-100">
                                @php
                                    $requiredDocs = [
                                        'SEC' => 'SEC Certificate',
                                        'BP' => 'Business Permit',
                                        'BIR' => 'BIR Certificate',
                                        'MP' => 'Mayor’s Permit',
                                        'BS' => 'Bank Statement',
                                        'PB' => 'Proof of Billing',
                                        'NCC' => 'Notarized Corporation Certificate',
                                        'AIB' => 'Articles of Incorporation and Bylaws',
                                    ];
                                @endphp

                                @foreach ($requiredDocs as $type => $label)
                                    @php
                                        $doc = isset($documents) ? $documents->firstWhere('type', $type) : null;
                                        $pdfBlob = $doc->file ?? null;
                                        $mime = $pdfBlob ? finfo_buffer(finfo_open(), $pdfBlob, FILEINFO_MIME_TYPE) : 'application/pdf';
                                        $pdfData = $pdfBlob ? 'data:' . $mime . ';base64,' . base64_encode($pdfBlob) : null;
                                        $modalId = 'docModal_' . $type;
                                    @endphp

                                    <div class="" style="padding: 5px; align-items: center; text-align: center; display: flex; flex-direction: row; flex-wrap: wrap; border-radius: 5px;  width: 300px;" data-doc-type="{{ $type }}" style="width: 300px;">
                                        <p style="margin: 5px"><strong>{{ $label }}</strong></p>

                                        <div class="preview-container"
                                            style="width: 100%; cursor: pointer;"
                                            data-bs-toggle="modal"
                                            data-bs-target="#{{ $modalId }}">
                                            @if ($pdfData)
                                                <iframe
                                                    src="{{ $pdfData }}#toolbar=0&navpanes=0&scrollbar=0&page=1"
                                                    width="100%"
                                                    height="200"
                                                    style="border: 1px solid #ccc; border-radius: 6px; pointer-events: none;">
                                                </iframe>
                                            @else
                                                <p class="text-muted">No document uploaded yet</p>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Modal -->
                                    <div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-xl modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">{{ $label }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-center" style="height: 80vh;">
                                                    @if ($pdfData)
                                                        <iframe
                                                            src="{{ $pdfData }}"
                                                            width="100%"
                                                            height="100%"
                                                            style="border: none;"
                                                            title="{{ $label }} Full View">
                                                        </iframe>
                                                    @else
                                                        <p class="text-danger">Unable to load PDF.</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </section>

                    </div>
                </div>
            @elseif ($reason === "Delivery requirements")
                <div>
                    <div class="section-row" style="width: 60%">
                        <section class="group-details first_id" >
                            <p class="group-name">Delivery requirements</p>
                            <div class="form-list">
                                <style>
                                    .details-display p .label{
                                        color: #666;
                                        font-size: 13px;
                                    }
                                </style>
                                <div class="details-display">
                                    <p>
                                        <span class="label">Frequency of delivery </span>
                                        <span>{{$del->delivery_frequency}}</span>
                                    </p>
                                    <p>
                                        <span class="label">Deliveries per week</span>
                                        <span>{{$del->deliveries_per_week}}</span>
                                    </p>
                                    <p>
                                        <span class="label">Days of delivery</span>
                                        <span>{{$del->delivery_days}}</span>
                                    </p>

                                    <hr>

                                    <p>
                                        <span class="label">(Monthly) Deliveries in a month </span>
                                        <span>{{$del->deliveries_per_month}}</span>
                                    </p>

                                    <hr>

                                    <p>
                                        <span class="label">Receiving time </span>
                                        <span>{{$del->receiving_time}}</span>
                                    </p>

                                    <hr>

                                    <p>
                                        <span class="label">Delivery address 1 </span>
                                        <span>{{$del->delivery_address_1}}</span>
                                    </p>
                                    <p>
                                        <span class="label">Delivery address 2 </span>
                                        <span>{{$del->delivery_address_2}}</span>
                                    </p>
                                    <p>
                                        <span class="label">Delivery address 3 </span>
                                        <span>{{$del->delivery_address_3}}</span>
                                    </p>
                            
                
                                </div>
                            </div>
                        </section>

                    </div>


                </div>
            @endif
        </div>
    </div>

@endsection


@push('scripts')


    <script src="{{ asset('js/error/modal') }}"></script>

</script>


@endpush