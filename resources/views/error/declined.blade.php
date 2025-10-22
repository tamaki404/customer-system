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

    <div class="container">
        <div class="header-section">
            <p>Your request to join was declined</p>
            <p>Declined at {{ $accStats->approved_at }}</p>
        </div>

        <div class="report-section">
            <p class="info-display" style="width: 90%; max-width: 90%;">
                <span class="material-symbols-outlined">info</span>
                <span>
                    Our team has reviewed your registration details and identified specific issues that need correction.  
                    Please review the feedback below, make the necessary modifications, and resubmit your account for verification.
                </span>
            </p>

            <div class="report-details">
                <div class="report-item">
                    <span class="report-label">Required Action:</span>
                    <span class="report-value">{{ $accStats->to_change }}</span>
                </div>
                <div class="report-item">
                    <span class="report-label">Feedback from Reviewer:</span>
                    <span class="report-value">{{ $accStats->feedback }}</span>
                </div>
                <div class="report-item">
                    <span class="report-label">Date of Decline:</span>
                    <span class="report-value">{{ \Carbon\Carbon::parse($accStats->approved_at)->format('F j, Y g:i A') }}</span>
                </div>
            </div>

            <p class="report-note">
                ⚠️ Make sure that all uploaded documents are clear, valid, and correctly labeled before resubmitting.  
                Once resubmitted, your application will go through another review process.
            </p>
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
                    <button type="submit" class="resubmit-btn">Resubmit for Review</button>
                </div>
            </form>
        @endif

        <div class="footer-section">
            <p>For support and issues, kindly contact Sunny and Scrambles support team<br>
            <strong>sunny&scramble@gmail.com</strong> or <strong>09123456789</strong></p>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const flashMessage = document.getElementById('flash-message');
            if (flashMessage) {
                flashMessage.style.opacity = 1;
                setTimeout(() => {
                    flashMessage.style.opacity = 0;
                    setTimeout(() => {
                        flashMessage.remove();
                    }, 400);
                }, 3000);
            }
        });
    </script>

</body>
</html>