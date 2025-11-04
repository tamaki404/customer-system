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

    <link rel="stylesheet" href="{{ asset('css/signup/style.css') }}">

    <style>
        .form-group label,
        .file-upload-section label{
            font-size: 13px;
        }
        
    </style>


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
            <form method="POST" action="{{ route('registration.customer.register') }}" class="log-form" id="registerForm" enctype="multipart/form-data">
                @csrf


                <div class="step1" style=" gap: 20px;">
                    <h3 class="step-title">1/3 Customer information</h3>
                    <p style="color: #333; font-size: 14px;">Fields with <span class="req-asterisk">*</span> are required</p>
                    
                    <!-- Company Details -->
                    <div class="company-details-div">
                        <section class="company-details">
                            <p class="form-name">1.1 Company details</p>

                            <div class="form-items">

                                <div class="form-group" >
                                    <label for="company-name"><span class="req-asterisk">*</span> Category </label>
                                    <div style="display: flex; flex-direction: row; box-shadow: rgba(0, 0, 0, 0.02) 0px 1px 3px 0px, rgba(27, 31, 35, 0.15) 0px 0px 0px 1px; border-radius: 10px; padding: 5px; height: 50px;">
                                        <div>
                                            <input type="checkbox" id="option1" name="customer_category" value="Wholesale"> 
                                            <p style="font-size: 13px; color: #333; margin: 0;">Wholesale</p>
                                        </div>
                                        <div>
                                            <input type="checkbox" id="option1" name="customer_category" value="Distributor"> 
                                            <p style="font-size: 13px; color: #333; margin: 0;">Distributor</p>
                                        </div>
                                        <div>
                                            <input type="checkbox" id="option1" name="customer_category" value="HIR"> 
                                            <p style="font-size: 13px; color: #333; margin: 0;">HIR</p>
                                        </div>
                                        <div>
                                            <input type="checkbox" id="option1" name="customer_category" value="Dealer"> 
                                            <p style="font-size: 13px; color: #333; margin: 0;">Dealer</p>
                                        </div>
                                    </div>

                                </div>
                            </div>
        
                        </section>
    
                        <section class="company-details">

                            <div class="form-items">


                                <div class="form-group">
                                    <label for="company-name"><span class="req-asterisk">*</span> Customer/Company name </label>
                                    <input type="text" style="width: 300px" name="company_name" id="company-name" required maxlength="255">
                                </div>
                                <div class="form-group">
                                    <label for="company-image"><span class="req-asterisk">*</span> Company image/logo </label>
                                    <input type="file" id="company-image" name="image" accept="image/*">
                                    <div>
                                        <input type="checkbox" id="use-default-logo" name="use_default_logo" value="use_default"> 
                                        <p style="font-size: 13px; color: #333; margin: 0;">Use default image</p>
                                    </div>

                                    <p class="error-message"></p>

                                </div>
                            </div>
                    
        
                        </section>

                        <section class="company-details">
                            <div class="form-items">
                        

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
                                        <option value="Gcash">Gcash</option>
                                        <option value="Cash">Cash</option>
                                        <option value="Bank transfer">Bank transfer</option>
                                    </select>
                                </div>
                            </div>
                        </section>
                    </div>

                    <!-- ID details -->
                    <div class="company-details-div">
                        <section class="company-details">
                            <p class="form-name">1.2 Verify details</p>
                            <div class="form-items">
                                <div class="form-group">
                                    <label for="id-image"><span class="req-asterisk">*</span> ID image </label>
                                    <input type="file" id="id-image" name="id_image" accept="image/*">
                                    <p class="error-message"></p>
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
                                    <label for="birthdate">Birthdate</label>
                                    <input type="date" name="birthdate" id="birthdate">
                                </div>
                            
                            </div>
                        </section>
                    </div>

                    <!-- Representatives accounts -->
                    <div class="company-details-div">
                        <section class="company-details">
                            <p class="form-name">1.3 Auhtorized representative</p>
                            <div class="form-items rep-group-div">
                                <div class="rep-form-group">
                                    <div class="form-group">
                                        <label for="rep-name"><span class="req-asterisk">*</span> Name</label>
                                        <div>
                                            <input id="rep-name" type="text" name="lastname" placeholder="Last name" required maxlength="50">
                                            <input type="text" name="firstname" placeholder="First name" required maxlength="50">
                                            <input type="text" name="middlename" placeholder="Middle name" maxlength="50">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="rep-position"><span class="req-asterisk">*</span> Position</label>
                                        <input type="text" name="position" value="Admin" placeholder="Admin" disabled required>
                                    </div>
                                    <div class="form-group">
                                        <label for="rep-contact-no"><span class="req-asterisk">*</span> Contact No.</label>
                                        <input type="text" name="mobile" id="rep-contact-no" placeholder="ex. 09123456789" required maxlength="11">
                                    </div>
                                </div>
                            </div>
                            <div class="form-items rep-group-div">
                                <div class="rep-form-group">
                                    <div class="form-group">
                                        <div>
                                            <input id="rep-name" type="text" name="lastname" placeholder="Last name" required maxlength="50">
                                            <input type="text" name="firstname" placeholder="First name" required maxlength="50">
                                            <input type="text" name="middlename" placeholder="Middle name" maxlength="50">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="position" value="" placeholder="Purchasing assistant" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="mobile" id="rep-contact-no" placeholder="ex. 09123456789" required maxlength="11">
                                    </div>
                                </div>
                            </div>          
                        </section>
                    </div>

                    <!-- Signatories -->
                    <div class="company-details-div">
                        <section class="company-details">
                            <p class="form-name">1.4 Signatories</p>
                            <div class="form-items rep-group-div">
                                <div class="rep-form-group">
                                    <div class="form-group">
                                        <label for="rep-name"><span class="req-asterisk">*</span> Name</label>
                                        <div >
                                            <input id="rep-name" type="text" name="lastname" placeholder="Last name" required maxlength="50">
                                            <input type="text" name="firstname" placeholder="First name" required maxlength="50">
                                            <input type="text" name="middlename" placeholder="Middle name" maxlength="50">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="rep-position"><span class="req-asterisk">*</span> Position</label>
                                        <input type="text" name="position" maxlength="50" placeholder="Warehouse supervisor" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="rep-contact-no"><span class="req-asterisk">*</span> E-signature</label>
                                        <input type="file" id="id-image" name="e-signature" accept="image/*" required>
                                    </div>
                                </div>
                            </div>

                        </section>
                    </div>

                    <!-- Bank Details -->
                    <div class="company-details-div">
                        <section class="company-details">
                            <p class="form-name">1.5 Bank details</p>
                            <div class="form-items rep-group-div">
                                <div class="rep-form-group">
                                    <div class="form-group">
                                        <label for=""> Account name</label>
                                        <div>
                                            <input id="" type="text" name="account" placeholder=""  maxlength="50">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="">Account number</label>
                                        <input type="text" name="number" id=""  maxlength="100">
                                    </div>
                                    <div class="form-group">
                                        <label for=""> Bank</label>
                                        <input type="text" name="bank" maxlength="100"  >
                                    </div>
                                    <div class="form-group">
                                        <label for="">Branch</label>
                                        <input type="text" name="branch" id="" >
                                    </div>
                                    
                                </div>
                                <div class="rep-form-group">
                                    <div class="form-group">
                                        <label for=""> Account name</label>
                                        <div>
                                            <input id="" type="text" name="account" placeholder=""  maxlength="50">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="">Account number</label>
                                        <input type="text" name="number" id=""  maxlength="100">
                                    </div>
                                    <div class="form-group">
                                        <label for=""> Bank</label>
                                        <input type="text" name="bank" maxlength="100"  >
                                    </div>
                                    <div class="form-group">
                                        <label for="">Branch</label>
                                        <input type="text" name="branch" id="" >
                                    </div>
                                    
                                </div>
                            </div>

                        </section>
                    </div>

                    <!-- Business Details -->
                    <div class="company-details-div">
                        <section class="company-details">
                            <p class="form-name">1.6 Business details</p>
                            <div class="form-items rep-group-div">
                                <div class="rep-form-group">
                                    <div class="form-group">
                                        <label for=""> How long have you been in the industry?</label>
                                        <div>
                                            <input id="" type="text" name="years_active" placeholder=""  maxlength="20">
                                        </div>
                                    </div>
                          
                                    
                                </div>

                            </div>

                        </section>
                    </div>
             


                </div>
                <div class="step2" style="gap: 20px; width: 100%">
                    <h3 class="step-title">2/3 Documents filing</h3>
                    <section class="upload-instructions" style="width: 100%; position: absolue;">
                        <p style="font-weight: bold;">Instructions</p>
                        <p>All documents listed below are required</p>
                        <p>Strictly 2MB max per file.</p>
                        <p>Upload PDF files for multiple copies.</p>
                    </section>

                    <style>
                        .upload-instructions p {
                            color: #333;
                            font-size: 14px;
                            margin: 2px 0;
                        }
                    </style>   

                    <!-- documents -->
                    <div class="file-upload-container" style="display: flex; flex-direction: column; width: 1500px;">

                        <div class="file-upload-section">
                            <label for="sec"> <span class="req-asterisk">*</span> Securities and Exchange Commission (SEC) </label>
                            <input type="file" name="SEC" id="sec" style="width:300px" required accept="application/pdf">
                            <p class="error-message"></p>
                        </div>
                        <div class="file-upload-section">
                            <label for="bp"> <span class="req-asterisk">*</span> Business permit </label>
                            <input type="file" name="BP"  style="width:300px" id="bp" required accept="application/pdf">
                            <p class="error-message"></p>
                        </div>
                        <div class="file-upload-section">
                            <label for="bir"> <span class="req-asterisk">*</span> BIR form 2303 </label>
                            <input type="file" name="BIR"  style="width:300px" id="bir" required accept="application/pdf">
                            <p class="error-message"></p>
                        </div>
                        <div class="file-upload-section">
                            <label for="mp"> <span class="req-asterisk">*</span> Mayor's permit </label>
                            <input type="file" name="MP"  style="width:300px" id="bir" required accept="application/pdf">
                            <p class="error-message"></p>
                        </div>
                        <div class="file-upload-section">
                            <label for="id"> <span class="req-asterisk">*</span> Valid ID (2)</label>
                            <input type="file" name="valid_one"  style="width:300px" id="id" required accept="application/pdf">
                            <input type="file" name="valid_two"  style="width:300px" id="bir" required accept="application/pdf">
                            <p class="error-message"></p>
                        </div>
                        <div class="file-upload-section">
                            <label for="bs"> <span class="req-asterisk">*</span> Bank statement (min. 6 months) </label>
                            <input type="file" name="BS" id="bs"  style="width:300px" required accept="application/pdf">
                            <p class="error-message"></p>
                        </div>          
                        <div class="file-upload-section">
                            <label for="pb"> <span class="req-asterisk">*</span> Proof of billing </label>
                            <input type="file" name="PB" id="pb"  style="width:300px" required accept="application/pdf">
                            <p class="error-message"></p>
                        </div>         
                        <div class="file-upload-section">
                            <label for="ncc"> <span class="req-asterisk">*</span> Notarized corporation certificate (CORP) </label>
                            <input type="file" name="NCC" id="ncc"  style="width:300px" required accept="application/pdf">
                            <p class="error-message"></p>
                        </div>        
                        <div class="file-upload-section">
                            <label for="aib"> <span class="req-asterisk">*</span> Articles of incorporation and bylaws (CORP) </label>
                            <input type="file" name="AIB" id="aib"  style="width:300px" required accept="application/pdf">
                            <p class="error-message"></p>
                        </div>
                    </div>

                

             


                </div>
                <div class="step3" style=" gap: 20px;">
                    <h3 class="step-title">3/3 Customer product requirements</h3>

                    <!-- Product search and select -->
                    <div class="company-details-div">
                        <section class="company-details">
                            <p class="form-name">3.1 Product search and add</p>
                            <div class="form-items">
                                <div class="form-group">
                                    <label for="company-name"><span class="req-asterisk">*</span> Product search </label>
                                    <div>
                                        <input type="text" placeholder="Search by name" maxlength="100" style="width: 300px">
                                        <button class="search-prod-btn"><span class="material-symbols-outlined" style="font-size: 17px">search</span></button>
                                    </div>
                                    <p class="error-message"></p>
                                </div>
                                {{-- product display --}}
                                <div class="form-group">
                                    
                                </div>

                            
                            </div>
                        </section>
                    </div>
                    <!-- Product fill form -->
                    <div class="company-details-div">
                        <section class="company-details">
                            <p class="form-name">3.2 Verify details</p>
                            <div class="form-items" style="display: flex; flex-direction: row; flex: 1; flex-wrap: wrap; width: 100%; gap: 10px; justify-content: space-evenly;">
                                {{-- each products selected --}}
                                <div class="product-form-main" style="gap: 5px; width: 45%; padding: 10px; border-radius: 5px; box-shadow: rgba(14, 63, 126, 0.06) 0px 0px 0px 1px, rgba(42, 51, 70, 0.03) 0px 1px 1px -0.5px, rgba(42, 51, 70, 0.04) 0px 2px 2px -1px, rgba(42, 51, 70, 0.04) 0px 3px 3px -1.5px, rgba(42, 51, 70, 0.03) 0px 5px 5px -2.5px, rgba(42, 51, 70, 0.03) 0px 10px 10px -5px, rgba(42, 51, 70, 0.03) 0px 24px 24px -8px;" >
                                    <label for="product-div-fill"> Product 1 </label>
                                    <div class="product-form-div" id="product-div-fill" style="gap: 10px; display: flex; flex-direction: row; width: 100%;">
                                        <div class="product-input"  style="display: flex; flex-direction: column; gap: 2px;">
                                            <label for="id-image"><span class="req-asterisk">*</span> Name </label>
                                            <input type="text" name="name" value="" required>
                                            <p class="error-message"></p>
                                        </div>
                                        <div class="product-input-group">
                                            <label for="id-image"><span class="req-asterisk">*</span> Product condition </label>
                                            <div>
                                                <div>
                                                    <label for="">Fresh</label>
                                                    <input type="checkbox" name="condition" id="" value="fresh">
                                                </div>
                                                <div>
                                                    <label for="">Frozen</label>
                                                    <input type="checkbox" name="condition" id="" value="frozen">
                                                </div>
                                            </div>
                                            <p class="error-message"></p>
                                        </div>
                                        <div class="product-input-group" style="display: flex; flex-direction: column; gap: 2px;">
                                            <label for="id-image"><span class="req-asterisk">*</span> Weight requirement </label>
                                            <input type="text" name="name" value="" required>
                                            <p class="error-message"></p>
                                        </div>
                                   
                                        <div class="product-input-group" style="display: flex; flex-direction: column; gap: 5px;">
                                            <label for="id-image"><span class="req-asterisk">*</span> Packaging requirement </label>
                                            <div style="gap: 5px;">
                                                <div>
                                                    <label for="" style="font-size: 12px">Primary</label>
                                                    <select name="primary_packaging" id="" style="height: 35px; font-size: 13px">
                                                        <option value="" style="font-size: 13px">Sunny plastic</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label for="" style="font-size: 12px">Secondary</label>
                                                    <select name="secondary_packaging" id="" style="height: 35px; font-size: 13px">
                                                        <option value="" style="font-size: 13px">Sack wrapper</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <p class="error-message"></p>
                                        </div>
                                        <div class="product-input-group" style="display: flex; flex-direction: column; gap: 2px;">
                                            <label for="id-image"><span class="req-asterisk">*</span> Labeling requirement </label>
                                            <input type="text" name="labeling" value="" required>
                                            <p class="error-message"></p>
                                        </div>
                                        <div class="product-input-group" style="display: flex; flex-direction: column; gap: 2px;">
                                            <label for="id-image"><span class="req-asterisk">*</span> Rejection parameter </label>
                                            <input type="text" name="rejection_param" value="" required>
                                            <p class="error-message"></p>
                                        </div>
                                    </div>
                               
                                </div>
                                <div class="product-form-main" style="gap: 5px; width: 45%; padding: 10px; border-radius: 5px; box-shadow: rgba(14, 63, 126, 0.06) 0px 0px 0px 1px, rgba(42, 51, 70, 0.03) 0px 1px 1px -0.5px, rgba(42, 51, 70, 0.04) 0px 2px 2px -1px, rgba(42, 51, 70, 0.04) 0px 3px 3px -1.5px, rgba(42, 51, 70, 0.03) 0px 5px 5px -2.5px, rgba(42, 51, 70, 0.03) 0px 10px 10px -5px, rgba(42, 51, 70, 0.03) 0px 24px 24px -8px;" >
                                    <label for="product-div-fill"> Product 1 </label>
                                    <div class="product-form-div" id="product-div-fill" style="gap: 10px; display: flex; flex-direction: row; width: 100%;">
                                        <div class="product-input"  style="display: flex; flex-direction: column; gap: 2px;">
                                            <label for="id-image"><span class="req-asterisk">*</span> Name </label>
                                            <input type="text" name="name" value="" required>
                                            <p class="error-message"></p>
                                        </div>
                                        <div class="product-input-group">
                                            <label for="id-image"><span class="req-asterisk">*</span> Product condition </label>
                                            <div>
                                                <div>
                                                    <label for="">Fresh</label>
                                                    <input type="checkbox" name="condition" id="" value="fresh">
                                                </div>
                                                <div>
                                                    <label for="">Frozen</label>
                                                    <input type="checkbox" name="condition" id="" value="frozen">
                                                </div>
                                            </div>
                                            <p class="error-message"></p>
                                        </div>
                                        <div class="product-input-group" style="display: flex; flex-direction: column; gap: 2px;">
                                            <label for="id-image"><span class="req-asterisk">*</span> Weight requirement </label>
                                            <input type="text" name="name" value="" required>
                                            <p class="error-message"></p>
                                        </div>
                                   
                                        <div class="product-input-group" style="display: flex; flex-direction: column; gap: 5px;">
                                            <label for="id-image"><span class="req-asterisk">*</span> Packaging requirement </label>
                                            <div style="gap: 5px;">
                                                <div>
                                                    <label for="" style="font-size: 12px">Primary</label>
                                                    <select name="primary_packaging" id="" style="height: 35px; font-size: 13px">
                                                        <option value="" style="font-size: 13px">Sunny plastic</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label for="" style="font-size: 12px">Secondary</label>
                                                    <select name="secondary_packaging" id="" style="height: 35px; font-size: 13px">
                                                        <option value="" style="font-size: 13px">Sack wrapper</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <p class="error-message"></p>
                                        </div>
                                        <div class="product-input-group" style="display: flex; flex-direction: column; gap: 2px;">
                                            <label for="id-image"><span class="req-asterisk">*</span> Labeling requirement </label>
                                            <input type="text" name="labeling" value="" required>
                                            <p class="error-message"></p>
                                        </div>
                                        <div class="product-input-group" style="display: flex; flex-direction: column; gap: 2px;">
                                            <label for="id-image"><span class="req-asterisk">*</span> Rejection parameter </label>
                                            <input type="text" name="rejection_param" value="" required>
                                            <p class="error-message"></p>
                                        </div>
                                    </div>
                               
                                </div>
                            
                            </div>
                        </section>
                    </div>

                    <!-- PEE requirements -->
                    <div class="company-details-div">
                        <section class="company-details">
                            <p class="form-name">3.3 PPE requirements</p>
                            <div class="form-items">
                                <div class="form-group">
                                    <label for="ppe_req"><span class="req-asterisk">*</span> PPE requirements during delivery and receiving </label>
                                    <input type="text" id="ppe_req" name="ppe_note" >
                                    <p class="error-message"></p>
                                </div>
                       
                            
                            </div>
                        </section>
                    </div>

                    <!-- Delivery requirements -->
                    <div class="company-details-div">
                        <section class="company-details">
                            <p class="form-name">3.4 Delivery requirements</p>
                            <div class="form-items">
                                <div class="form-group">
                                    <label for="del_rec_timee"><span class="req-asterisk">*</span> Frequency of delivery and receiving time </label>
                                    <input type="text" id="del_rec_time" name="del_rec_time" >
                                    <p class="error-message"></p>
                                </div>
                                <div class="form-group">
                                    <label for="del_add_1">Delivery address 1</label>
                                    <input type="text" name="del_add_1" id="del_add_1" maxlength="255">
                                </div>
                                <div class="form-group">
                                    <label for="del_add_2">Delivery address 2</label>
                                    <input type="text" name="del_add_2" id="del_add_2" maxlength="255">
                                </div>
                                <div class="form-group">
                                    <label for="del_add_3">Delivery address 3</label>
                                    <input type="text" name="del_add_3" id="del_add_3" maxlength="255">
                                </div>
                            
                            </div>
                        </section>
                    </div>

                    <!-- Remarks/Instructions -->
                    <div class="company-details-div">
                        <section class="company-details">
                            <p class="form-name">3.5 Remarks/Special instructions</p>
                            <div class="form-items">
                                <div class="form-group">
                                    <textarea name="delivery_instruc"  class="instruction-textarea" row="3" maxlength="255" style="">
                                        
                                    </textarea>
                                    <p class="error-message"></p>
                                </div>

                            
                            </div>
                        </section>
                    </div>
             
                </div>


            </form>
            <button class="next-btn btn-transition" type="button"><span class="material-symbols-outlined">arrow_forward_ios</span></button>
        </div>


    </div>


    <script src="{{ asset('js/registration/toggle-stepper.js') }}"></script>
    <script src="{{ asset('js/registration/preview-input-images.js') }}"></script>
    <script src="{{ asset('js/registration/prevent-double-submit.js') }}"></script>
    <script src="{{ asset('js/registration/password-validation.js') }}"></script>
    <script src="{{ asset('js/registration/file-size-validation.js') }}"></script>

</body>
</html>
