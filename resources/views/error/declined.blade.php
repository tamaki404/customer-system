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

    <div class="container" style="width: 70%; height: 800px; padding: 0px 5px;">

        <div class="head-container">
            <div class="header" style="display: flex; flex: 1; flex-direction: row; justify-content: space-between;">
                <img src="{{ asset(path: 'assets/sunnyLogo1.png') }}" alt="Owner Image" width="150" class="ownerImage">
                <form action="{{ route('logout') }}" method="POST" style="display:inline; width: auto;">
                    @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
            </div>

        </div>

        <div style=" overflow-y: auto;">
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
                    Once resubmitted, your application will go through another review process.d
                </p>
            </div>
            @if ($reason === "ID image and details")
                <form action="{{ route('declined.update') }}" method="POST" enctype="multipart/form-data" class="decline-form">
                    @csrf
                    <input type="hidden" value="ids" name="key">
                    <div class="section-row">
                        <section class="group-details first_id">
                            <p class="group-name">ID details verification</p>

                            <div class="form-list">
                                <div class="input-forms">
                                    <p for="id-image">
                                        <span class="req-asterisk">*</span> ID image
                                    </p>

                                    @php
                                        $mime = $customer->id_image ? finfo_buffer(finfo_open(), $customer->id_image, FILEINFO_MIME_TYPE) : null;
                                        $imgSrc = $customer->id_image
                                            ? 'data:' . $mime . ';base64,' . base64_encode($customer->id_image)
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
                                        <option value="{{ $customer->id_type ?? '' }}" selected>{{ $customer->id_type ?? 'Select ID Type' }}</option>
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
                                    <input type="text" name="id_number" id="id_number" maxlength="100" required value="{{ $customer->id_number ?? '' }}">
                                </div>

                                <div class="input-forms">
                                    <label for="birthdate"><span class="req-asterisk">*</span> Birthdate</label>
                                    <input type="date" name="birthdate" id="birthdate" required value="{{ $customer->birthdate ?? '' }}">
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
                        <button type="submit" class="resubmit-btn" style="width: 300px">Submit</button>

                    </div>
                </form>
            @elseif ($reason === "Bank details")
                <form action="{{ route('declined.bank.update') }}" method="POST" enctype="multipart/form-data" class="decline-form">
                    @csrf
                    <input type="hidden" value="banks" name="key">
                    <div class="section-row" style="gap: 5px">
                        <section class="group-details first_id" style="margin-top: 5px">
                            <p class="group-name">Bank details</p>
                            <div class="form-list">
                                <div style="display: flex; flex-direction: row; flex-wrap: wrap; gap:10px">
                                    <div class="input-forms">
                                        <label for="account_name"> Account name</label>
                                        <input type="text" name="account_name" id="account_name" style="width: 300px"  maxlength="255" value="{{$bank->account_name}}">
                                        <p class="error-text" style="display: none"></p>
                                    </div>
                                    <div class="input-forms">
                                        <label for="account_number"> Account number</label>
                                        <input type="text" name="account_number" id="account_number" style="width: 150px"  maxlength="50" value="{{$bank->account_number}}">
                                        <p class="error-text" style="display: none"></p>
                                    </div> 
                                    <div class="input-forms">
                                        <label for="bank"> Bank</label>
                                        <input type="text" name="bank" id="bank" value="{{ $bank->bank }}">
                                        <p class="error-text" style="display: none"></p>
                                    </div>
                                    <div class="input-forms">
                                        <label for="branch"> Branch</label>
                                        <input type="text" name="branch" id="branch" style="width: 150px"  maxlength="200" value="{{$bank->branch}}">
                                        <p class="error-text" style="display: none"></p>
                                    </div>   
                                </div>  
                            </div>
                        </section>
                    </div>
                    <div class="form-actions" style="margin-top: 15px; width: 100%; display: flex; align-items: center; justify-content: center;">
                        <button type="submit" class="resubmit-btn" style="width: 300px">Submit</button>
                    </div>
                </form>
            @elseif ($reason === "Necessary documents")
                <form action="{{ route('declined.docx.update') }}" method="POST" enctype="multipart/form-data" class="decline-form">
                    @csrf
                    <input type="hidden" value="docx" name="key">
                    <section class="group-details necessary-docs" style="width: 100%">
                        <p class="group-name">Necessary Documents</p>

                        <div class="form-list">
                            <p><span class="req-asterisk">*</span> Upload or preview your 8 required PDF documents</p>

                            <div class="pdf-documents-grid" style="display: flex; flex-direction: row; flex: 1; flex-wrap: wrap; gap: 10px;">
                                @php
                                    // Map of document type => display name
                                    $requiredDocs = [
                                        'SEC' => 'SEC certificate',
                                        'BP' => 'Business Permit',
                                        'BIR' => 'BIR Certificate',
                                        'MP' => 'Mayor’s Permit',
                                        'BS' => 'Bank Statement',
                                        'PB' => 'Proof of Billing',
                                        'NCC' => 'Notarized corporation certificate',
                                        'AIB' => 'Articles of incorporation and bylaws',
                                    ];
                                @endphp

                                @foreach ($requiredDocs as $type => $label)
                                    @php
                                        $doc = isset($documents) ? $documents->firstWhere('type', $type) : null;
                                        $pdfBlob = $doc->file ?? null;
                                        $mime = $pdfBlob ? finfo_buffer(finfo_open(), $pdfBlob, FILEINFO_MIME_TYPE) : 'application/pdf';
                                        $pdfData = $pdfBlob ? 'data:' . $mime . ';base64,' . base64_encode($pdfBlob) : null;
                                    @endphp

                                    <div class="doc-preview-box" data-doc-type="{{ $type }}">
                                        <p><strong>{{ $label }}</strong></p>
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
                                        <input type="file"
                                            id="{{ strtolower($type) }}"
                                            name="{{ strtolower($type) }}"
                                            accept="application/pdf"
                                            class="pdf-input">
                                        <p class="selected-file">No file selected</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                    <div class="form-actions" style="margin-top: 15px; width: 100%; display: flex; align-items: center; justify-content: center;">
                        <button type="submit" class="resubmit-btn" style="width: 300px">Submit</button>

                    </div>

                 
                </form>
            @elseif ($reason === "Delivery requirements")
                <form action="{{ route('declined.bank.update') }}" method="POST" enctype="multipart/form-data" class="decline-form">
                    @csrf
                    <input type="hidden" value="delreq" name="key">
                    <section class="group-details" style="margin-top: 5px; width: 90%">
                        <p class="group-name">Delivery requirements</p>
                        <div class="form-list" style="display: flex; flex-direction: row; flex-wrap: wrap;">

                            <!-- Frequency type -->
                            <div class="input-forms">
                                <label for="delivery_frequency">
                                <span class="req-asterisk">*</span> Frequency of delivery
                                </label>
                                <select id="delivery_frequency" name="delivery_frequency" required>
                                <option value="{{$del->delivery_frequency}}">{{$del->delivery_frequency}}</option>
                                <option value="weekly">Weekly</option>
                                <option value="biweekly">Bi-weekly</option>
                                <option value="monthly">Monthly</option>
                                <option value="custom">Custom</option>
                                </select>
                                <p class="error-message"></p>
                            </div>
                            <!-- Number of deliveries -->
                            <div class="input-forms" >
                                <label for="deliveries_per_week">
                                <span class="req-asterisk">*</span> How many times per week?
                                </label>
                                <input type="number" id="deliveries_per_week" name="deliveries_per_week" min="1" max="7" placeholder="e.g. 2" required value="{{$del->deliveries_per_week}}">
                                <p class="error-message"></p>
                            </div>
                            <!-- Select delivery days -->
                            <div class="input-forms" style="width: auto">
                                <a for="delivery_days" style="    font-size: 13px; color: #666;">
                                    <span class="req-asterisk">*</span> Select days of the week for delivery
                                </a>
                                @php
                                    $selectedDays = is_array($del->delivery_days)
                                        ? $del->delivery_days
                                        : explode(',', $del->delivery_days);
                                @endphp

                                <div id="delivery_days" class="checkbox-group" style="display: flex; width: auto; flex-direction: row; flex-wrap: wrap;">
                                    @foreach (['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)
                                        <label>
                                            <input
                                                type="checkbox"
                                                name="delivery_days[]"
                                                value="{{ $day }}"
                                                {{ in_array($day, $selectedDays ?? []) ? 'checked' : '' }}>
                                            {{ $day }}
                                        </label>
                                    @endforeach
                                </div>

                                <p class="error-message"></p>
                            </div>
                            <!-- If monthly -->
                            <div class="input-forms" id="monthly_frequency_group" style="display:none;">
                                <label for="deliveries_per_month">
                                <span class="req-asterisk">*</span> If monthly, how many times per month?
                                </label>
                                <input type="number" id="deliveries_per_month" name="deliveries_per_month" min="1" max="31" placeholder="e.g. 4">
                                <p class="error-message"></p>
                            </div>
                            <!-- Receiving time -->
                            <div class="input-forms">
                                <label for="receiving_time">
                                <span class="req-asterisk">*</span> Preferred receiving time
                                </label>
                                <input 
                                    type="time" 
                                    id="receiving_time" 
                                    name="receiving_time" 
                                    required 
                                    value="{{ old('receiving_time', \Carbon\Carbon::parse($del->receiving_time)->format('H:i')) }}"
                                >
                                <p class="error-message"></p>
                            </div>
                            
                            <div class="input-forms">
                                <label for="del-add-1"><span class="req-asterisk">*</span> Delivery address 1</label>
                                <input type="text" id="del-add-1" style="width: 500px" name="delivery_address_1" maxlength="255" required value="{{ $del->delivery_address_1}}">
                                <p class="error-text" style="display: none"></p>
                            </div>
                            
                            <div class="input-forms">
                                <label for="del-add-2">Delivery address 2</label>
                                <input type="text" id="del-add-2" style="width: 500px" name="delivery_address_2" maxlength="255" required value="{{ $del->delivery_address_2}}">
                                <p class="error-text" style="display: none"></p>
                            </div>
                            
                            <div class="input-forms">
                                <label for="del-add-3">Delivery address 3</label>
                                <input type="text" id="del-add-3" style="width: 500px" name="delivery_address_3" maxlength="255" required value="{{ $del->delivery_address_3}}">
                                <p class="error-text" style="display: none"></p>
                            </div>
                        </div>
                    </section>
                    <div class="form-actions" style="margin-top: 15px; width: 100%; display: flex; align-items: center; justify-content: center;">
                        <button type="submit" class="resubmit-btn" style="width: 300px">Submit</button>
                    </div>
                 
                </form>
           
            @endif            
        </div>



        <div class="footer-section">
            <p>For support and issues, kindly contact Sunny and Scrambles support team<br>
            <strong>sunny&scramble@gmail.com</strong> or <strong>09123456789</strong></p>
        </div>
    </div>



    <script src="{{ asset('js/errors/file-size.js') }}"></script>
    <script src="{{ asset('js/errors/flash-message.js') }}"></script>



</body>
</html>