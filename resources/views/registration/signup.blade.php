<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Signup</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif !important; }</style>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('css/registration/signup.css') }}">
    <link rel="stylesheet" href="{{ asset('css/links/scroll-bar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/displays/alerts.css') }}">



</head>
<body>
    <div class="log-form-bg">
        <div class="header">
            <img src="{{ asset('assets/sunnyLogo1.png') }}" alt="Owner Image">
            <div class="header-texts">
                <h2>Create Account</h2>
                <p class="kindly-mess">Please fill out the form below to create your account</p>
                <span>Already have an account? <a class="href-link" href="{{ route('signin') }}" style="color: #f5922a">Sign In</a></span>
            </div>

        </div>

        <div class="form-container" style="display: flex; flex-direction: row; gap: 10px; align-items: center; justify-content: center;">
            <button class="prev-btn btn-transition" type="button"><span class="material-symbols-outlined">arrow_back_ios</span></button>
            <form method="POST" action="{{ route('registration.supplier.register') }}" class="log-form" id="registerForm" enctype="multipart/form-data">
                @csrf
                
                <div class="step1">
                    <p style="color: #333; font-size: 14px;">Fields with <span class="req-asterisk">*</span> are required</p>
                    <!-- Company Details -->
                    <section class="company-details">
                        <p class="form-name">Company details</p>
                        <div class="form-items">
                            <div class="form-group">
                                <label for="company-name"><span class="req-asterisk">*</span> Customer/Company name </label>
                                <input type="text" style="width: 300px" name="company_name" id="company-name" required maxlength="255">
                            </div>
                            <div class="form-group">
                                <label for="company-image"><span class="req-asterisk">*</span> Company image/logo </label>
                                <input type="file" id="company-image" name="image" required accept="image/*">
                                <p class="error-message"></p>

                            </div>
                            <!-- Home Address -->
                            <div class="form-group">
                                <label for="home-street"><span class="req-asterisk">*</span> Home address </label>
                                <div id="home-address">
                                    <input type="text" name="home_street" id="home-street" required placeholder="Street" maxlength="255">
                                    <input type="text" name="home_subdivision" id="home-subdivision" required placeholder="Subdivision" maxlength="255">
                                    <input type="text" name="home_barangay" id="home-barangay" required placeholder="Barangay" maxlength="255">
                                    <input type="text" name="home_city" id="home-city" required placeholder="City" maxlength="100">
                                </div>
                            </div>

                            <!-- Office Address -->
                            <div class="form-group">
                                <label for="office-street"><span class="req-asterisk">*</span> Office address </label>
                                <div id="office-address">
                                    <input type="text" name="office_street" id="office-street" required placeholder="Street" maxlength="255">
                                    <input type="text" name="office_subdivision" id="office-subdivision" required placeholder="Subdivision" maxlength="255">
                                    <input type="text" name="office_barangay" id="office-barangay" required placeholder="Barangay" maxlength="255">
                                    <input type="text" name="office_city" id="office-city" required placeholder="City" maxlength="100">
                                </div>
                            </div>

                            <!-- Contact & Personal Info -->
                            <div class="form-group">
                                <label for="mobile-no"><span class="req-asterisk">*</span> Mobile No.</label>
                                <input type="number" name="mobile_no" id="mobile-no" placeholder="ex. 09123456789" required>
                            </div>
                            <div class="form-group">
                                <label for="telephone-no"><span class="req-asterisk">*</span> Telephone No.</label>
                                <input type="number" name="telephone_no" id="telephone-no" placeholder="ex. 0287654321" required maxlength="11">
                            </div>
                            <div class="form-group">
                                <label for="birthdate">Birthdate</label>
                                <input type="date" name="birthdate" id="birthdate">
                            </div>
                            <div class="form-group">
                                <label for="valid-id-no">Valid ID no.</label>
                                <input type="text" name="valid_id_no" id="valid-id-no" maxlength="255">
                            </div>
                            <div class="form-group">
                                <label for="id-type">ID Type</label>
                                <select name="id_type" id="id-type">
                                    <option value="Passport">Passport</option>
                                    <option value="Driver's license">Driver's License</option>
                                    <option value="National ID">National ID</option>
                                    <option value="Postal ID">Postal ID</option>
                                    <option value="Philhealth ID">Philhealth ID</option>
                                </select>
                            </div>
                    
                            <div class="form-group">
                                <label for="civil-status"><span class="req-asterisk">*</span> Civil status</label>
                                <select name="civil_status" id="civil-status">
                                    <option value="Single">Single</option>
                                    <option value="Married">Married</option>
                                    <option value="Divorced">Divorced</option>
                                    <option value="Widowed">Widowed</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="citizenship"><span class="req-asterisk">*</span> Citizenship</label>
                                <select name="citizenship" id="citizenship" required>
                                    <option value="Filipino">Filipino</option>
                                    <option value="Foreign">Foreign</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="payment_method"><span class="req-asterisk">*</span> Payment Method</label>
                                <select name="payment_method" id="payment_method" required>
                                    <option value="Credit card">EFT</option>
                                    <option value="Paypal">PayPal</option>
                                    <option value="Gcash">GCash</option>
                                </select>
                            </div>
                        </div>
                    </section>

                    <!-- Authorized Representative -->
                    <section class="authorized-representative">
                        <p class="form-name">Authorized representative</p>
                        <div class="form-items">
                            <div class="form-group">
                                <label for="rep-name"><span class="req-asterisk">*</span> Name</label>
                                <div >
                                    <input id="rep-name" type="text" name="rep_last_name" placeholder="Last name" required maxlength="50">
                                    <input type="text" name="rep_first_name" placeholder="First name" required maxlength="50">
                                    <input type="text" name="rep_middle_name" placeholder="Middle name" maxlength="50">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="rep-relationship"><span class="req-asterisk">*</span> Relationship/Position</label>
                                <input type="text" name="rep_relationship" id="rep-relationship" placeholder="ex. Procurement - Director" required maxlength="50">
                            </div>
                            <div class="form-group">
                                <label for="rep-contact-no"><span class="req-asterisk">*</span> Contact No.</label>
                                <input type="number" name="rep_contact_no" id="rep-contact-no" placeholder="ex. 09123456789" required maxlength="11">
                            </div>
                        </div>
                    </section>

                    <!-- Authorized Signatories -->
                    <section class="authorized-signatories">
                        <p class="form-name">Authorized signatories</p>
                        <div class="form-items">
                            <div class="form-group">
                                <label for="signatory-last-name"><span class="req-asterisk">*</span> Name</label>
                                <div>
                                    <input id="signatory-last-name" type="text" name="signatory_last_name" placeholder="Last name" required maxlength="50">
                                    <input id="signatory-first-name" type="text" name="signatory_first_name" placeholder="First name" required maxlength="50">
                                    <input id="signatory-middle-name" type="text" name="signatory_middle_name" placeholder="Middle name" maxlength="50">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="signatory-rep-relationship"><span class="req-asterisk">*</span> Relationship/Position</label>
                                <input type="text" name="signatory_relationship" id="signatory-rep-relationship" placeholder="ex. Procurement - Director" required maxlength="50">
                            </div>
                            <div class="form-group">
                                <label for="signatory-rep-contact-no"><span class="req-asterisk">*</span> Contact No.</label>
                                <input type="number" name="signatory_contact_no" id="signatory-rep-contact-no" placeholder="ex. 09123456789" required maxlength="11">
                            </div>
                        </div>
                    </section>

                    <!-- Bank Details -->
                    <section class="bank-details">
                        <p class="form-name">Bank details</p>
                        <div class="form-items">
                            <div class="form-group">
                                <label for="account-name">Account name</label>
                                <input type="text" name="account_name" id="account-name" maxlength="255">
                            </div>
                            <div class="form-group">
                                <label for="bank">Bank</label>
                                <input type="text" name="bank" id="bank" maxlength="255">
                            </div>
                            <div class="form-group">
                                <label for="branch">Branch</label>
                                <input type="text" name="branch" id="branch" maxlength="255">
                            </div>
                            <div class="form-group">
                                <label for="account-number">Account number</label>
                                <input type="text" name="account_number" id="account-number" maxlength="255">
                            </div>
                        </div>
                    </section>

                    <!-- Business Details -->
                    <section class="business-details">
                        <p class="form-name">Business details</p>
                        <div class="form-items">
                            <div class="form-group">
                                <label for="salesman-relationship">How long have you known salesman?</label>
                                <input type="text" name="salesman_relationship" id="salesman-relationship" placeholder="ex. 2 years"  maxlength="50">
                            </div>
                            <div class="form-group">
                                <label for="weekly-volume">Weekly volume (trays and/or heads)</label>
                                <input type="text" name="weekly_volume" id="weekly-volume" placeholder="ex. 100 trays"  maxlength="50">
                            </div>
                            <div class="form-group">
                                <label for="other-products-interest">Other products interest in</label>
                                <input type="text" name="other_products_interest" id="other-products-interest" placeholder="ex. Eggs, Chicken, etc." maxlength="255">
                            </div>
                            <div class="form-group">
                                <label for="date-required">Date required</label>
                                <input type="date" name="date_required" id="date-required" >
                            </div>
                            <div class="form-group">
                                <label for="referred-by">Referred by</label>
                                <input type="text" name="referred_by" id="referred-by" placeholder="ex. John Doe" maxlength="255">
                            </div>
                        </div>
                    </section>


                </div>
                <div class="step2">
                    <!-- Company Details -->
                    <section class="upload-instructions">
                        <p style="font-weight: bold;">Instructions</p>
                        <p>Upload necessary files here that are needed to confirm your identity.</p>
                        <p>Strictly 2MB max per file.</p>
                        <p>Upload scanned documents for clearer quality.</p>
                    </section>

                    <style>
                        .upload-instructions p {
                            color: #333;
                            font-size: 14px;
                            margin: 2px 0;
                        }
                    </style>


                    <div class="file-upload-container">
                        <div class="file-upload-section">
                            <label for="affidavit-of-loss">
                                <span class="req-asterisk">*</span> Affidavit of loss 
                                <span class="max-file">(max. 3 files)</span>
                            </label>
                            <input type="file" name="AOL[]" id="affidavit-of-loss" required multiple accept="image/*">
                            <div class="file-count">0 files selected</div>
                            <div class="image-preview-container"></div>
                            <p class="error-message"></p>
                        </div>
                        <div class="file-upload-section">
                            <label for="certificate-of-registration"> <span class="req-asterisk">*</span> Certificate of registration <span class="max-file">(max. 3 files)</span></label>
                            <input type="file" name="COR[]" id="certificate-of-registration" required multiple accept="image/*">
                            <div class="file-count">0 files selected</div>
                            <div class="image-preview-container"></div>
                            <p class="error-message"></p>
                        </div>
                        <div class="file-upload-section">
                            <label for="barangay-clearance"> <span class="req-asterisk">*</span> Barangay clearance <span class="max-file">(max. 3 files)</span></label>
                            <input type="file" name="BC[]" id="barangay-clearance" required multiple accept="image/*">
                            <div class="file-count">0 files selected</div>
                            <div class="image-preview-container"></div>
                            <p class="error-message"></p>
                        </div>
                        <div class="file-upload-section">
                            <label for="business-permit"> <span class="req-asterisk">*</span> Business permit <span class="max-file">(max. 3 files)</span></label>
                            <input type="file" name="BP[]" id="business-permit" required multiple accept="image/*">
                            <div class="file-count">0 files selected</div>
                            <div class="image-preview-container"></div>
                            <p class="error-message"></p>
                        </div>
                        <div class="file-upload-section">
                            <label for="sanitary-permit"> <span class="req-asterisk">*</span> Sanitary permit <span class="max-file">(max. 3 files)</span></label>
                            <input type="file" name="SP[]" id="sanitary-permit" required multiple accept="image/*">
                            <div class="file-count">0 files selected</div>
                            <div class="image-preview-container"></div>
                            <p class="error-message"></p>
                        </div>
                        <div class="file-upload-section">
                            <label for="environmental-management-permit"> <span class="req-asterisk">*</span> Environmental management permit <span class="max-file">(max. 3 files)</span></label>
                            <input type="file" name="EMP[]" id="environmental-management-permit" required multiple accept="image/*">
                            <div class="file-count">0 files selected</div>
                            <div class="image-preview-container"></div>
                            <p class="error-message"></p>
                        </div>
                        <div class="file-upload-section">
                            <label for="community-tax-certificate"> <span class="req-asterisk">*</span> Community tax certificate <span class="max-file">(max. 3 files)</span></label>
                            <input type="file" name="CTC[]" id="community-tax-certificate" required multiple accept="image/*">
                            <div class="file-count">0 files selected</div>
                            <div class="image-preview-container"></div>
                            <p class="error-message"></p>
                        </div>

                        <div class="file-upload-section">
                            <label for="product-requirements"> <span class="req-asterisk">*</span> Product requirements <span class="max-file">(max. 3 files)</span></label>
                            <input type="file" name="PR[]" id="product-requirements" required multiple accept="image/*">
                            <div class="file-count">0 files selected</div>
                            <div class="image-preview-container"></div>
                            <p class="error-message"></p>
                        </div>

                    </div>

                    <section class="acc-security" >
                        <p style="font-size: 15px; color: #333; font-weight: bold;">Account security</p>
                        <div class="form-items"style="display: flex; flex-direction: column; gap: 5px ">
                            <div class="form-group">
                                <label for="email_address"><span class="req-asterisk">*</span> Email address</label>
                                <input type="text" name="email_address" id="email_address" placeholder="@gmail.com" required maxlength="255">
                            </div>
                            <div class="form-group">
                                <label for="password"><span class="req-asterisk">*</span> Password</label>
                                <input type="password" name="password" id="password" required minlength="8" maxlength="255">
                     
                            </div>
                            <div class="form-group">
                                <label for="password_confirmation"><span class="req-asterisk">*</span> Confirm Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" required minlength="8" maxlength="255">
                                <p class="error-message" id="password-match-error" style="display: none; color: #e74c3c; font-size: 12px; margin-top: 5px;"></p>
                            </div>
                                       <div id="password-strength" style="margin-top: 5px; font-size: 12px;">
                                    <div style="display: flex;">
                                        <span id="length-check" style="color: #ccc;"> 8+ characters -</span>
                                        <span id="uppercase-check" style="color: #ccc;"> Uppercase -</span>
                                        <span id="lowercase-check" style="color: #ccc;"> Lowercase -</span>
                                        <span id="number-check" style="color: #ccc;"> Number -</span>
                                        <span id="special-check" style="color: #ccc;"> Special char -</span>
                                    </div>
                                </div>
                        </div>
                    </section>

                    <!-- Agreement -->
                    <section class="form-agreement" style="margin-top: 50px">
                        <p class="form-name" style="margin: 0">Agreement</p>
                        <div class="form-items" style="display: flex; flex-direction: column;">
                            <p class="agreement-text" style="margin: 0; color: #333; font-size: 14px;">
                                I/We agree that all information provided in this form is true and correct. 
                                <br> 
                                I/We agree that Sunny and Scramble Corporation may use this information for purpose of background check and marketing purposes.  
                                <br>
                                I agree that my/our information will be kept confidential.
                            </p>
                            <div class="form-group" style="display: flex; flex-direction: row; align-items: center;">
                                <input type="checkbox" name="agreement" id="agreement" required style="margin: 0" required>
                                <label for="agreement" style="margin: 0">
                                    I have read and understood the above agreement, and I hereby confirm my acceptance of the terms and conditions stated.
                                </label>
                            </div>
                        </div>
                    </section>

                    <section class="register-section" style="">
                    
                        <button 
                            type="submit" 
                            class="register-btn btn-transition"
                        >
                            Register account
                        </button>

                            @if ($errors->any())
                                <div class="alert alert-danger auto-hide">
                                    <ul style="margin: 0; padding-left: 20px;">
                                        @foreach ($errors->all() as $error)
                                            <li style="font-size: 14px;">{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if(session('success'))
                                <div class="alert alert-success auto-hide">
                                    {{ session('success') }}
                                </div>
                            @endif


                            

                    </section>

                </div>



            </form>
            <button class="next-btn btn-transition" type="button"><span class="material-symbols-outlined">arrow_forward_ios</span></button>
        </div>


    </div>


    <script src="{{ asset('js/registration/toggle-stepper.js') }}"></script>
    <script src="{{ asset('js/registration/preview-input-images.js') }}"></script>
    <script src="{{ asset('js/registration/prevent-double-submit.js') }}"></script>

</body>
</html>
