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
    <style>
        .modal-option-groups {
            margin-bottom: 15px;
        }
        .modal-option-groups p {
            margin-bottom: 8px;
            font-weight: 500;
        }
        .modal-option-groups select,
        .modal-option-groups textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .modal-option-groups textarea {
            resize: vertical;
            font-family: inherit;
        }
        .note-notify {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            padding: 12px;
            background: #e3f2fd;
            border-left: 4px solid #2196F3;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .text-muted {
            display: block;
            margin-top: 4px;
            font-size: 11px;
            color: #6c757d;
        }
        #feedback-container {
            display: none;
        }
        #feedback-container.show {
            display: block;
        }
    </style>
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



<!-- Confirm supplier request modal -->
<div class="modal fade" id="request-action" tabindex="-1" aria-labelledby="requestActionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="{{ route('review.confirm') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
                <p class="modal-title">Supplier request action</p>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <p class="note-notify">
                    <span class="material-symbols-outlined">info</span>
                    <span>If you confirm this user, they'll be pending for approval and you have to confirm them</span>
                </p>
                
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
                <div class="modal-option-groups" id="feedback-container">
                    <p>Kindly specify what needs to be changed and the reason, be specific and on point</p>
                    <textarea 
                        name="review_feedback" 
                        id="feedback" 
                        rows="4" 
                        placeholder="Provide detailed feedback here..."
                        maxlength="500"
                    ></textarea>
                    <small class="text-muted">Maximum 500 characters</small>
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

<script>
    // Toggle feedback textarea based on status selection
    document.getElementById('status').addEventListener('change', function() {
        const feedbackContainer = document.getElementById('feedback-container');
        const feedbackTextarea = document.getElementById('feedback');
        
        if (this.value === 'Declined') {
            feedbackContainer.classList.add('show');
            feedbackTextarea.required = true;
        } else {
            feedbackContainer.classList.remove('show');
            feedbackTextarea.required = false;
            feedbackTextarea.value = ''; // Clear feedback when not declining
        }
    });

    // Reset form when modal is closed
    document.getElementById('request-action').addEventListener('hidden.bs.modal', function () {
        const form = this.querySelector('form');
        form.reset();
        document.getElementById('feedback-container').classList.remove('show');
        document.getElementById('feedback').required = false;
    });
</script>




    <div class="container">
        @if ($reason === "ID image and details")
            <div>
                @csrf
                <div class="section-row">
                    <section class="group-details first_id">
                        <p class="group-name">ID details verification</p>

                        <div class="form-list">
                            <div class="input-forms">
                                <p for="id-image">
                                     ID image
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

                            </div>

                            <div class="input-forms">
                                <label for="id-type"> Type of ID</label>
                                <input name="id_type" id="id-type" type="text" value="{{ $supplier->id_type }}" readonly>
                            </div>

                            <div class="input-forms">
                                <label for="id_number"> ID number</label>
                                <input name="id_number" id="id_number" type="text" value="{{ $supplier->id_number }}" readonly>

                            </div>

                            <div class="input-forms">
                                <label for="birthdate"> Birthdate</label>
                                <input type="date" name="birthdate" id="birthdate" required value="{{ $supplier->birthdate ?? '' }}" readonly>
                            </div>
                        </div>
                    </section>

                    <section class="group-details second_id">
                        <p class="group-name">Valid IDs Documentation</p>

                        <div class="form-list">
                            <p> Documents (2) Valid IDs (PDF)</p>

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
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

           
            </div>
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