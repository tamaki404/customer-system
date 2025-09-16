<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif !important; }</style>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('css/registration/signup.css') }}">
    <link rel="stylesheet" href="{{ asset('css/links/scroll-bar.css') }}">



</head>
<body>
    <div class="log-form-bg">
        <div class="header">
            <img src="{{ asset('assets/sunnyLogo1.png') }}" alt="Owner Image">
            <div class="header-texts">
                <h2>Create Account</h2>
                <p class="kindly-mess">Please fill out the form below to create your account</p>
                <span>Already have an account? <a class="href-link" href="/login" style="color: #f5922a">Sign In</a></span>
            </div>

        </div>

        <div class="form-container" style="display: flex; flex-direction: row; gap: 10px; align-items: center; justify-content: center;">
            <button class="prev-btn btn-transition" type="button"><span class="material-symbols-outlined">arrow_back_ios</span></button>
            <form method="POST" action="/register-company" class="log-form" id="registerForm" enctype="multipart/form-data">
                @csrf
                
                <div class="step1">
                    <p style="color: #333; font-size: 14px;">Fields with <span class="req-asterisk">*</span> are required</p>
                    <!-- Company Details -->
                    <section class="company-details">
                        <p class="form-name">Company details</p>
                        <div class="form-items">
                            <div class="form-group">
                                <label for="company-name"><span class="req-asterisk">*</span> Customer/Company name </label>
                                <input type="text" name="company_name" id="company-name" required maxlength="255">
                            </div>

                            <!-- Office Address -->
                            <div class="form-group">
                                <label><span class="req-asterisk">*</span> Office address </label>
                                <div id="office-address">
                                    <input type="text" name="street" id="street" required placeholder="Street" maxlength="255">
                                    <input type="text" name="subdivision" id="subdivision" required placeholder="Subdivision" maxlength="255">
                                    <input type="text" name="barangay" id="barangay" required placeholder="Barangay" maxlength="255">
                                    <input type="text" name="city" id="city" required placeholder="City" maxlength="10">
                                </div>
                            </div>

                            <!-- Contact & Personal Info -->
                            <div class="form-group">
                                <label for="mobile-no"><span class="req-asterisk">*</span> Mobile No.</label>
                                <input type="number" name="mobile_no" id="mobile-no" placeholder="ex. 09123456789" required maxlength="11">
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
                                    <option value="passport">Passport</option>
                                    <option value="driver_license">Driver's License</option>
                                    <option value="national_id">National ID</option>
                                    <option value="postal_id">Postal ID</option>
                                    <option value="philhealth_id">Philhealth ID</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="email"><span class="req-asterisk">*</span> Email address</label>
                                <input type="email" name="email" id="email" required maxlength="255">
                            </div>
                            <div class="form-group">
                                <label for="civil-status"><span class="req-asterisk">*</span> Civil status</label>
                                <select name="civil_status" id="civil-status">
                                    <option value="single">Single</option>
                                    <option value="married">Married</option>
                                    <option value="divorced">Divorced</option>
                                    <option value="widowed">Widowed</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="citizenship"><span class="req-asterisk">*</span> Citizenship</label>
                                <select name="citizenship" id="citizenship">
                                    <option value="filipino">Filipino</option>
                                    <option value="foreign">Foreign</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="payment_method"><span class="req-asterisk">*</span> Payment Method</label>
                                <select name="payment_method" id="payment_method" required>
                                    <option value="credit_card">EFT</option>
                                    <option value="paypal">PayPal</option>
                                    <option value="gcash">GCash</option>
                                </select>
                            </div>
                        </div>
                    </section>

                    <!-- Authorized Representative -->
                    <section class="authorized-representative">
                        <p class="form-name">Authorized representative</p>
                        <div class="form-items">
                            <div class="form-group">
                                <label><span class="req-asterisk">*</span> Name</label>
                                <div>
                                    <input type="text" name="rep_last_name" placeholder="Last name" required maxlength="50">
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
                                <label><span class="req-asterisk">*</span> Name</label>
                                <div>
                                    <input type="text" name="signatory_last_name" placeholder="Last name" required maxlength="50">
                                    <input type="text" name="signatory_first_name" placeholder="First name" required maxlength="50">
                                    <input type="text" name="signatory_middle_name" placeholder="Middle name" maxlength="50">
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
                                <input type="text" name="salesman_relationship" id="salesman-relationship" placeholder="ex. 2 years" required maxlength="50">
                            </div>
                            <div class="form-group">
                                <label for="weekly-volume">Weekly volume (trays and/or heads)</label>
                                <input type="text" name="weekly_volume" id="weekly-volume" placeholder="ex. 100 trays" required maxlength="50">
                            </div>
                            <div class="form-group">
                                <label for="other-products-interest">Other products interest in</label>
                                <input type="text" name="other_products_interest" id="other-products-interest" placeholder="ex. Eggs, Chicken, etc." maxlength="255">
                            </div>
                            <div class="form-group">
                                <label for="date-required">Date required</label>
                                <input type="date" name="date_required" id="date-required" required>
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
                        <label for="">Instruction</label>
                        <span>Upload necessary files here that are needed to confirm your identity.</span>
                        <span>Strictly 2MB max per file.</span>
                        <span>Upload scanned documents for clearer quality.</span>
                    </section>

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

                    </div>
 
                    <!-- Agreement -->
                    <section class="form-agreement" style="margin-top: 10px">
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
                                <input type="checkbox" name="agreement" id="agreement" required style="margin: 0">
                                <label for="agreement" style="margin: 0">
                                    I have read and understood the above agreement, and I hereby confirm my acceptance of the terms and conditions stated.
                                </label>
                            </div>
                        </div>
                    </section>

                    <section class="register-section" style="">
                    
                        <button type="submit" class="register-btn btn-transition" >Register account</button>
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li style="font-size: 14px;">{{ $error }}</li>
                                    @endforeach
                                </ul>
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
</body>
</html>
