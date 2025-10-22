<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/layout/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/info/style.css') }}">
        <link rel="stylesheet" href="{{ asset('css/info/style.css') }}">

    <link rel="stylesheet" href="{{ asset('css/error/declined.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/flash-message.css') }}">

    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <title>Modify Account</title>

</head>
<body>
    @if (session('success') || session('error'))
        @php
            $isSuccess = session('success') !== null;
            $message = $isSuccess ? session('success') : session('error');
        @endphp
        <div id="flash-message" class="flash-message alert {{ $isSuccess ? 'alert-success' : 'alert-danger' }}">
            {{ $message }}
        </div>
    @endif

    {{-- confirm supplier request --}}
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
                        <p>Are the modified data correct now?</p>
                        <select name="account_status" id="account_status" required>
                            <option value="">-- Select status --</option>
                            <option value="To confirm">Yes, all are good now</option>
                            <option value="Declined again">No, there's something wrong</option>
                        </select>
                    </div>
                    <!-- Reason to decline again (hidden by default) -->
                        <div class="modal-option-groups" id="feedback_group" style="display: none;">
                            <p>Kindly specify what needs to be changed and the reason, be specific and on point</p>
                            <textarea 
                                name="review_feedback" 
                                id="feedback" 
                                rows="4" 
                                class="form-control" 
                                placeholder="Provide detailed feedback here..."
                                maxlength="500"
                                style="width: 100%; resize: vertical; padding: 10px; border: 1px solid #ddd; border-radius: 4px;"
                            ></textarea>
                            <small class="text-muted" style="font-size: 11px;">Maximum 500 characters</small>
                        </div>

                   
                </div>
                <input type="hidden" name="supplier_id" value="{{$supplier->supplier_id }}">
                <input type="hidden" name="reviewed_by" value="{{ Auth()->user()->user_id }}">
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit action</button>
                </div>
            </form>
        </div>
    </div>


    <div class="container">
        @if ($reason === "ID image and details")
            <form action="{{ route('declined.update') }}" method="POST" enctype="multipart/form-data" class="decline-form">
                @csrf
                <div class="section-row">
                    <section class="group-details first_id">
                        <p class="group-name">ID details verification</p>

                        <div class="form-list">
                            <div class="input-forms">
                                <p for="id-image">
                                    <span class="req-asterisk">*</span> ID image
                                </p>

                                @php
                                    $mime = $supplier->id_image ? finfo_buffer(finfo_open(), $supplier->id_image, FILEINFO_MIME_TYPE) : null;
                                    $imgSrc = $supplier->id_image
                                        ? 'data:' . $mime . ';base64,' . base64_encode($supplier->id_image)
                                        : asset('assets/default-company-logo.png');
                                @endphp

                                <div class="image-preview" id="imagePreview">
                                    <p>Image preview</p>
                                    <img id="preview-img" src="{{ $imgSrc }}" alt="ID Preview">
                                    <span id="preview-text" style="display: none;">No image selected</span>
                                </div>

                                <input type="file" id="id-image" name="id_image" accept="image/*">
                            </div>

                            <div class="input-forms">
                                <label for="id-type"><span class="req-asterisk">*</span> Type of ID</label>
                                <select name="id_type" id="id-type" required>
                                    <option value="{{ $supplier->id_type ?? '' }}" selected>{{ $supplier->id_type ?? 'Select ID Type' }}</option>
                                    <option value="Passport">Passport</option>
                                    <option value="Driver's License">Driver's License</option>
                                    <option value="National ID">National ID</option>
                                    <option value="SSS ID">SSS ID</option>
                                    <option value="GSIS ID">GSIS ID</option>
                                    <option value="UMID">UMID</option>
                                    <option value="Postal ID">Postal ID</option>
                                    <option value="PhilHealth ID">PhilHealth ID</option>
                                    <option value="Voter's ID">Voter's ID</option>
                                    <option value="PRC ID">PRC ID</option>
                                </select>
                            </div>

                            <div class="input-forms">
                                <label for="id_number"><span class="req-asterisk">*</span> ID number</label>
                                <input type="text" name="id_number" id="id_number" maxlength="100" required value="{{ $supplier->id_number ?? '' }}">
                            </div>

                            <div class="input-forms">
                                <label for="birthdate"><span class="req-asterisk">*</span> Birthdate</label>
                                <input type="date" name="birthdate" id="birthdate" required value="{{ $supplier->birthdate ?? '' }}">
                            </div>
                        </div>
                    </section>

                    <section class="group-details second_id">
                        <p class="group-name">Valid IDs Documentation</p>

                        <div class="form-list">
                            <p><span class="req-asterisk">*</span> Documents (2) Valid IDs (PDF)</p>

                            <div class="pdf-documents-row">
                                @php
                                    $validOne = isset($documents) ? $documents->firstWhere('type', 'valid_one') : null;
                                    $validTwo = isset($documents) ? $documents->firstWhere('type', 'valid_two') : null;
                                @endphp

                                {{-- Valid ID (1) --}}
                                @php
                                    $pdfBlob = $validOne->file ?? null;
                                    $mime = $pdfBlob ? finfo_buffer(finfo_open(), $pdfBlob, FILEINFO_MIME_TYPE) : 'application/pdf';
                                    $pdfData = $pdfBlob ? 'data:' . $mime . ';base64,' . base64_encode($pdfBlob) : null;
                                @endphp

                                <div class="doc-preview-box" data-doc-type="valid_one">
                                    <p><strong>Valid ID (1)</strong></p>
                                    <div class="preview-container">
                                        @if ($pdfData)
                                            <iframe
                                                src="{{ $pdfData }}"
                                                width="100%"
                                                height="200"
                                                style="border: 1px solid #ccc; border-radius: 6px;">
                                            </iframe>
                                        @else
                                            <p class="no-preview">No document uploaded yet</p>
                                        @endif
                                    </div>
                                    <input type="file" id="valid_one" name="valid_one" accept="application/pdf" class="pdf-input">
                                    <p class="selected-file">No file selected</p>
                                </div>

                                {{-- Valid ID (2) --}}
                                @php
                                    $pdfBlob = $validTwo->file ?? null;
                                    $mime = $pdfBlob ? finfo_buffer(finfo_open(), $pdfBlob, FILEINFO_MIME_TYPE) : 'application/pdf';
                                    $pdfData = $pdfBlob ? 'data:' . $mime . ';base64,' . base64_encode($pdfBlob) : null;
                                @endphp

                                <div class="doc-preview-box" data-doc-type="valid_two">
                                    <p><strong>Valid ID (2)</strong></p>
                                    <div class="preview-container">
                                        @if ($pdfData)
                                            <iframe
                                                src="{{ $pdfData }}"
                                                width="100%"
                                                height="200"
                                                style="border: 1px solid #ccc; border-radius: 6px;">
                                            </iframe>
                                        @else
                                            <p class="no-preview">No document uploaded yet</p>
                                        @endif
                                    </div>
                                    <input type="file" id="valid_two" name="valid_two" accept="application/pdf" class="pdf-input">
                                    <p class="selected-file">No file selected</p>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="form-actions" style="margin-top: 15px;" >
                    <button type="submit" class="resubmit-btn">Resubmit for Review</button>
                </div>
            </form>
        @endif

        <div class="footer-section">
            <p>For support and issues, kindly contact Sunny and Scrambles support team<br>
            <strong>sunny&scramble@gmail.com</strong> or <strong>09123456789</strong></p>
        </div>
    </div>



    <script src="{{ asset('js/errors/file-size.js') }}"></script>
    <script src="{{ asset('js/errors/flash-message.js') }}"></script>


</body>
</html>