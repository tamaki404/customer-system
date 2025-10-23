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
<body style="background-color: #ffde59; overflow: hidden; padding: 5px;">
    @if (session('success') || session('error'))
        @php
            $isSuccess = session('success') !== null;
            $message = $isSuccess ? session('success') : session('error');
        @endphp
        <div id="flash-message" class="flash-message alert {{ $isSuccess ? 'alert-success' : 'alert-danger' }}">
            {{ $message }}
        </div>
    @endif

    <div class="container" style="width: 70%; overflow-y: auto; height: 800px; padding: 0px 5px;">
        {{-- <div class="header-section">
            @if ($review->review_feedback !== NULL)
                <p>Your request has been declined again</p>
                <p>Declined at {{ $review->reviewed_at }}</p>
            @else
                <p>Your request to join was declined</p>
                <p>Declined at {{ $review->raised_at }}</p>
            @endif

        </div> --}}
        <div class="head-container">
            <div class="header" style="display: flex; flex: 1; flex-direction: row; justify-content: space-between;">
                <img src="{{ asset(path: 'assets/sunnyLogo1.png') }}" alt="Owner Image" width="150" class="ownerImage">
                <a href="" style="">< Go back to signup</a>
            </div>
                <p class="info-display" style="width: 90%; max-width: 90%;">
                    <span class="material-symbols-outlined">info</span>
                    <span>
                        Our team has reviewed your registration details and identified specific issues that need correction.  
                        Please review the feedback below, make the necessary modifications, and resubmit your account for verification.
                    </span>
                </p>
            <div class="report-section">

                <div class="report-details">
                    @if ($review->review_feedback !== NULL)

                        <div class="report-item">
                            <span class="report-label">Feedback from reviewer:</span>
                            <span class="report-value">{{ $review->review_feedback }}</span>
                        </div>
                        <div class="report-item">
                            <span class="report-label">Date reviewed:</span>
                            <span class="report-value">{{ \Carbon\Carbon::parse($review->reviewed_at)->format('F j, Y g:i A') }}</span>
                        </div>
                    @else

                        <div class="report-item">
                            <span class="report-label">Required action:</span>
                            <span class="report-value">{{ $review->head }}</span>
                        </div>
                        <div class="report-item">
                            <span class="report-label">Feedback from reviewer:</span>
                            <span class="report-value">{{ $review->body }}</span>
                        </div>
                        <div class="report-item">
                            <span class="report-label">Date declined:</span>
                            <span class="report-value">{{ \Carbon\Carbon::parse($review->raised_at)->format('F j, Y g:i A') }}</span>
                        </div>
                    @endif
                </div>

                <p class="report-note">
                    ⚠️ Make sure that all uploaded documents are clear, valid, and correctly labeled before resubmitting.  
                    Once resubmitted, your application will go through another review process.
                </p>
            </div>
        </div>



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
                    {{-- <button type="submit" class="resubmit-btn">Report issueAsk to resubmit</button> --}}
                    <button type="submit" class="resubmit-btn">Submit</button>

                </div>
            </form>
        @elseif ($reason === "Bank details")
            <form action="{{ route('declined.update') }}" method="POST" enctype="multipart/form-data" class="decline-form">
                @csrf
                <div class="section-row">
                    <section class="group-details first_id">
                        <p class="group-name">Bank details</p>

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

                <div class="form-actions" style="margin-top: 15px; width: 100%; display: flex; align-items: center; justify-content: center;">
                    {{-- <button type="submit" class="resubmit-btn">Report issueAsk to resubmit</button> --}}
                    <button type="submit" class="resubmit-btn" style="width: 300px">Submit</button>

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