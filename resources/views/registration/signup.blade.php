<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Signup</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">
    <style>body {  }</style>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('css/registration/new-signup.css') }}">
    <link rel="stylesheet" href="{{ asset('css/links/scroll-bar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/displays/alerts.css') }}">
    <link rel="stylesheet" href="{{ asset('css/notification/display-error.css') }}">


    <style>
        .alert {
            padding: 15px 20px;
            margin-bottom: 15px;
            border-radius: 5px;
            border-left: 4px solid;
            font-size: 14px;
            position: relative;
            top: 0;
            z-index: 1000;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-left-color: #28a745;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-left-color: #dc3545;
        }

        .auto-hide {
            animation: fadeOut 0.5s ease-in 4.5s forwards;
        }

        @keyframes fadeOut {
            to {
                opacity: 0;
                height: 0;
                padding: 0;
                margin: 0;
            }
        }
    </style>

</head>
<body style="overflow: hidden">


    @if ($errors->any())
        <div class="alert alert-danger auto-hide">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li style="font-size: 14px;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger auto-hide">
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success auto-hide">
            {{ session('success') }}
        </div>
    @endif

    <div class="log-form-bg">

        <div class="header">
            <img src="{{ asset('assets/sunnyLogo1.png') }}" alt="Owner Image">
            <div class="header-texts">
                <h2>Create Account</h2>
                <p class="kindly-mess">Please fill out the form below to create your account</p>
                <p>Already have an account? <a class="href-link" href="{{ route('signin') }}" style="color: #f5922a">Sign In</a></p>
            </div>

        </div>

        <div class="form-container">
            <button class="prev-btn act-btn" type="button"><span class="material-symbols-outlined">arrow_back_ios</span></button>

            <form method="POST" action="{{ route('registration.supplier.register') }}" class="log-form" id="registerForm" enctype="multipart/form-data">
                @csrf

                <div class="step-section" id="step1">
                    <p class="step-title-info">
                        <span class="title">1/3 Company information</span>
                        <span class="info">Answer the inputs below regarding the right details of your company</span>
                    </p>
                    <section class="group-details">
                        <p class="group-name">Profile</p>
                        <div class="form-list">
                            <div class="input-forms">
                                <label for="company_name"><span class="req-asterisk">*</span> Company name</label>
                                <input type="text" name="company_name" id="company_name" style="width: 250px"  maxlength="200" required>
                                <p class="error-text" style="display: none"></p>
                            </div>
                            <div class="input-forms">
                                <label for="category"><span class="req-asterisk">*</span> Category</label>
                                <select name="category" id="category" required>
                                    <option value="" disabled selected>-- Select category --</option>
                                    <option value="Wholesale">Wholesale</option>
                                    <option value="Distributor">Distributor</option>
                                    <option value="HRI">HRI</option>
                                    <option value="Dealer">Dealer</option>
                                </select>
                            </div>

                            <div class="input-forms">
                                <label for="company-image">
                                    <span class="req-asterisk">*</span> Company image/logo
                                </label>

                                <input type="file" class="image" id="company-image" name="image" accept="image/*">

                                <div class="use-default">
                                    <input type="checkbox" id="use-default" name="use_default">
                                    <label for="use-default">Use default</label>
                                </div>

                                <input type="hidden" id="default-image-flag" name="default_image" value="false">

                                <p class="error-text" style="display: none"></p>
                            </div>


                        </div>
                    </section>
                    <section class="group-details">
                        <p class="group-name">Address</p>
                        <div class="form-list">
                            <div class="input-forms">
                                <label for="home_street"><span class="req-asterisk">*</span> Home address</label>
                                <div class="office-address" >
                                    <input type="text" name="home_street" id="home_street" required placeholder="Street" maxlength="255">
                                    <input type="text" name="home_subdivision" id="home-subdivision" required placeholder="Subdivision" maxlength="255">
                                    <input type="text" name="home_barangay" id="home-barangay" required placeholder="Barangay" maxlength="255">
                                    <input type="text" name="home_city" id="home-city" required placeholder="City" maxlength="100">
                                </div>
                                <p class="error-text" style="display: none"></p>
                            </div>
                            <div class="input-forms">
                                <label for="office-street"><span class="req-asterisk">*</span> Office address</label>
                                    <div class="office-address">
                                        <input type="text" name="office_street" id="office-street" required placeholder="Street" maxlength="255">
                                        <input type="text" name="office_subdivision" id="office-subdivision" required placeholder="Subdivision" maxlength="255">
                                        <input type="text" name="office_barangay" id="office-barangay" required placeholder="Barangay" maxlength="255">
                                        <input type="text" name="office_city" id="office-city" required placeholder="City" maxlength="100">
                                    </div>
                                <p class="error-text" style="display: none"></p>
                            </div>
                        </div>
                    </section>
                    <section class="group-details">
                        <p class="group-name"></p>
                        <div class="form-list">
                            <div class="input-forms">
                                <label for="mobile"><span class="req-asterisk">*</span> Mobile no.</label>
                                <input type="text" name="mobile" id="mobile" style="width: 150px" placeholder="ex: 09XX-XXX-XXXX"  maxlength="11" required>
                                <p class="error-text" style="display: none"></p>
                            </div>
                            <div class="input-forms">
                                <label for="tele"> Telephone no.</label>
                                <input type="text" name="tele" id="tele" style="width: 150px" placeholder="ex: 02-XXX-XXXX"  maxlength="9" >
                                <p class="error-text" style="display: none"></p>
                            </div>
                            <div class="input-forms">
                                <label for="civil-status"> Civil status</label>
                                    <select name="civil_status" id="civil-status">
                                        <option value="" disabled selected>-- Select civil status --</option>
                                        <option value="Single">Single</option>
                                        <option value="Married">Married</option>
                                        <option value="Divorced">Divorced</option>
                                        <option value="Widowed">Widowed</option>
                                    </select>
                                <p class="error-text" style="display: none"></p>
                            </div>
                            <div class="input-forms">
                                <label for="citizenship"> Citizenship</label>
                                    <select name="civil_status" id="citizenship">
                                        <option value="" disabled selected>-- Select citizenship --</option>
                                        <option value="Filipino">Filipino</option>
                                        <option value="American">American</option>
                                        <option value="Canadian">Canadian</option>
                                        <option value="British">British</option>
                                        <option value="Other">Other</option>
                                    </select>
                                <p class="error-text" style="display: none"></p>
                            </div>
                            <div class="input-forms">
                                <label for="payment"><span class="req-asterisk">*</span> Payment method</label>
                                    <select name="payment_method" id="payment" required>
                                        <option value="" disabled selected>-- Select payment method --</option>
                                        <option value="Cash">Cash</option>
                                        <option value="Gcash">Gcash</option>
                                        <option value="Bank transfer">Bank transfer</option>
                                    </select>
                                <p class="error-text" style="display: none"></p>
                            </div>
                        </div>



                    </section>
                    <section class="group-details">
                        <p class="group-name">ID verification</p>
                        <div class="form-list">
                            <div class="input-forms">
                                <label for="id-image"><span class="req-asterisk">*</span> ID image</label>
                                <input type="file" id="id-image" name="id_image" accept="image/*" required>
                                <p class="error-text" style="display: none"></p>
                            </div>

                            <div class="input-forms">
                                <label for="id-type"><span class="req-asterisk">*</span> Type of ID</label>
                                <select name="category" id="id-type" required>
                                    <option value="" disabled selected>-- Select type of ID --</option>
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
                                <input type="text" name="id_number" id="id_number" style="width: 200px"  maxlength="100" required>
                                <p class="error-text" style="display: none"></p>
                            </div>
                            <div class="input-forms">
                                <label for="birthdate"> Birthdate </label>
                                <input type="date" name="birthdate" id="birthdate" style="width: 150px"  maxlength="100" required>
                                <p class="error-text" style="display: none"></p>
                            </div>
                        </div>
                    </section>
                    <section class="group-details">
                        <p class="group-name">Authorized representative</p>
                        <p class="error-text-auth" style="display: none"></p>
                        <div class="form-list" id="auth-container">
         

                            <div style="display: flex; flex-direction: row; flex-wrap: wrap; gap:10px">
                                <div class="input-forms">
                                    <label for="id-image"><span class="req-asterisk">*</span> Name</label>
                                    <div>
                                        <input id="rep-name" type="text" name="lastname" placeholder="Last name" required maxlength="50">
                                        <input type="text" name="firstname" placeholder="First name" required maxlength="50">
                                        <input type="text" name="middlename" placeholder="Middle name" maxlength="50">
                                    </div>
                                    <p class="error-text" style="display: none"></p>
                                </div>
                                <div class="input-forms">
                                    <label for="auth_position"><span class="req-asterisk">*</span> Position</label>
                                    <div>
                                        <input id="auth_position" type="text" name="auth_position" value="Admin" disabled required maxlength="50">
                                    </div>
                                    <p class="error-text" style="display: none"></p>
                                </div>
                                <div class="input-forms">
                                    <label for="contact"><span class="req-asterisk">*</span> Contact no.</label>
                                    <div>
                                        <input id="contact" type="text" name="contact" placeholder="ex: 09XX-XXX-XXXX" required maxlength="11">
                                    </div>
                                </div>
                            </div>

                            <button class="add-btn" type="button" id="add-auth-btn" onclick="addAuthRow()" class="add-auth-btn add-btn">
                                <span class="material-symbols-outlined">add</span>
                                Representative
                            </button>
                           
                        </div>
                    </section>
                    <section class="group-details">
                        <p class="group-name">Signatories</p>
                        <p class="error-text" style="display: none"></p>
                        <p style="margin: 0; font-size: 13px; color: #666; margin-bottom: 5px;">Authorized signatories to accept deliviries and sign invoices</p>
                        <p class="error-text" style="display: none"></p>
                        <div class="form-list" id="signature-container">
                            
                            <div class="sign-set" style="display: flex; flex-direction: row; flex-wrap: wrap; gap:10px; ">
                                <div class="input-forms">
                                    <label for="sign-name"><span class="req-asterisk">*</span> Name</label>
                                    <div>
                                        <input id="sign-name" type="text" name="lastname" placeholder="Last name" required maxlength="50">
                                        <input type="text" name="firstname" placeholder="First name" required maxlength="50">
                                        <input type="text" name="middlename" placeholder="Middle name" maxlength="50">
                                    </div>
                                    <p class="error-text" style="display: none"></p>
                                </div>
                                <div class="input-forms">
                                    <label for="position"><span class="req-asterisk">*</span> Position</label>
                                    <div>
                                        <input id="position" type="text" name="position" required maxlength="50">
                                    </div>
                                    <p class="error-text" style="display: none"></p>
                                </div>
                                <div class="input-forms">
                                    <label for="id-signature"><span class="req-asterisk">*</span> E-signature</label>
                                    <div>
                                        <input type="file" id="id-signature" name="e_signature" accept="image/*" required>
                                    </div>
                                </div>
                            </div>

                            <button type="button" id="add-signatory-btn" onclick="addSignatureRow()" class="add-signatory-btn add-btn">
                                <span class="material-symbols-outlined">add</span>
                                Signatories
                            </button>

                        </div>
                    </section>
                    <section class="group-details">
                        <p class="group-name">Bank details</p>
                        <div class="form-list">
                            <div style="display: flex; flex-direction: row; flex-wrap: wrap; gap:10px">
                                <div class="input-forms">
                                    <label for="account_name"><span class="req-asterisk">*</span> Account name</label>
                                    <input type="text" name="account_name" id="account_name" style="width: 300px"  maxlength="255" required>
                                    <p class="error-text" style="display: none"></p>
                                </div>
                                <div class="input-forms">
                                    <label for="bank"><span class="req-asterisk">*</span> Bank</label>
                                    <input type="text" name="bank" id="bank" style="width: 80px"  maxlength="255" required>
                                    <p class="error-text" style="display: none"></p>
                                </div>
                                <div class="input-forms">
                                    <label for="branch"><span class="req-asterisk">*</span> Branch</label>
                                    <input type="text" name="branch" id="branch" style="width: 150px"  maxlength="200" required>
                                    <p class="error-text" style="display: none"></p>
                                </div>   
                                <div class="input-forms">
                                    <label for="account_number"><span class="req-asterisk">*</span> Account number</label>
                                    <input type="text" name="account_number" id="account_number" style="width: 150px"  maxlength="50" required>
                                    <p class="error-text" style="display: none"></p>
                                </div> 
                            </div>  
                            <button class="add-btn">
                                <span class="material-symbols-outlined">add</span>
                                Bank account
                            </button>
                        </div>
                    </section>
                    <section class="group-details">
                        <p class="group-name">Business</p>
                        <div class="form-list">
                                <div class="input-forms">
                                    <label for="years"> How long have you been in the industry?</label>
                                    <input type="text" name="years" id="years" placeholder="5 years" style="width: 150px"  maxlength="50">
                                    <p class="error-text" style="display: none"></p>
                                </div>
                                <div class="input-forms">
                                    <label for="reffered_by"> Referred by</label>
                                    <input type="text" name="reffered_by" id="reffered_by" style="width: 200px"  maxlength="255" >
                                    <p class="error-text" style="display: none"></p>
                                </div>
                                <div class="input-forms">
                                    <label for="contacted_by"> Contacted by</label>
                                    <input type="text" name="contacted_by" id="contacted_by" style="width: 200px"  maxlength="200" >
                                    <p class="error-text" style="display: none"></p>
                                </div>   
                         
                            </div>  
                     
                    </section>
                </div>
                <div class="step-section" id="step2">
                    <p class="step-title-info">
                        <span class="title">2/3 Documents filing</span>
                        <span class="info">All documents listed are required. Files must be in PDF format and below 2MB.</span>
                    </p>
                    <section class="group-details" style="width: 100%">
                        <p class="group-name" style="margin-bottom: 10px;">Necessary documents</p>
                        <div class="form-list" style="display: flex; flex-direction: column;">
                            <div class="input-forms" >
                                <label for="sec"><span class="req-asterisk">*</span> Securities and Exchange Commission (SEC)</label>
                                <input type="file" class="docu-file" id="sec" name="SEC" accept="application/pdf" required>
                                <p class="error-text" style="display: none"></p>
                            </div>

                            <div class="input-forms">
                                <label for="bp"><span class="req-asterisk">*</span> Business permit</label>
                                <input type="file" class="docu-file" id="bp" name="BP" accept="application/pdf" required>
                                <p class="error-text" style="display: none"></p>
                            </div>

                            <div class="input-forms">
                                <label for="bir"><span class="req-asterisk">*</span> BIR form 2303</label>
                                <input type="file" class="docu-file" id="bir" name="BIR" accept="application/pdf" required>
                                <p class="error-text" style="display: none"></p>
                            </div>

                            <div class="input-forms">
                                <label for="mp"><span class="req-asterisk">*</span> Mayor's permit</label>
                                <input type="file" class="docu-file" id="mp" name="MP" accept="application/pdf" required>
                                <p class="error-text" style="display: none"></p>
                            </div>

                            <div class="input-forms">
                                <label><span class="req-asterisk">*</span> Valid ID (2)</label>
                                <input type="file" class="docu-file" id="valid_one" name="valid_one" accept="application/pdf" required>
                                <input type="file" class="docu-file" id="valid_two" name="valid_two" accept="application/pdf" required>
                                <p class="error-text" style="display: none"></p>
                            </div>

                            <div class="input-forms">
                                <label for="bs"><span class="req-asterisk">*</span> Bank statement (min. 6 months)</label>
                                <input type="file" class="docu-file" id="bs" name="BS" accept="application/pdf" required>
                                <p class="error-text" style="display: none"></p>
                            </div>

                            <div class="input-forms">
                                <label for="pb"><span class="req-asterisk">*</span> Proof of billing</label>
                                <input type="file" class="docu-file" id="pb" name="PB" accept="application/pdf" required>
                                <p class="error-text" style="display: none"></p>
                            </div>

                            <div class="input-forms">
                                <label for="ncc"><span class="req-asterisk">*</span> Notarized corporation certificate (CORP)</label>
                                <input type="file" class="docu-file" id="ncc" name="NCC" accept="application/pdf" required>
                                <p class="error-text" style="display: none"></p>
                            </div>

                            <div class="input-forms">
                                <label for="aib"><span class="req-asterisk">*</span> Articles of incorporation and bylaws (CORP)</label>
                                <input type="file" class="docu-file" id="aib" name="AIB" accept="application/pdf" required>
                                <p class="error-text" style="display: none"></p>
                            </div>

                        </div>
                    </section>

                </div>
                <div class="step-section" id="step3">
                    <p class="step-title-info">
                        <span class="title">3/3 Customer product requirements</span>
                        <span class="info">Answer the inputs below regarding your product requirements and delivery preferences</span>
                    </p>
                    
                    <section class="group-details">
                        <p class="group-name">Product search and add</p>
                        <div class="form-list">
                            <div class="input-forms">
                                <label for="product-search"><span class="req-asterisk">*</span> Product search</label>
                                <div style="display: flex; gap: 5px;">
                                    <input type="text" id="product-search" name="product_search" placeholder="Search by name" maxlength="100" style="width: 300px" required>
                                    <button class="search-prod-btn" type="button">
                                        <span class="material-symbols-outlined" style="font-size: 17px">search</span>
                                    </button>
                                </div>
                                <p class="error-text" style="display: none"></p>
                            </div>
                        </div>
                    </section>

                    <section class="group-details">
                        <p class="group-name">Product details verification</p>
                        <div class="form-list">
                            <!-- Product 1 -->
                            <div class="product-form-main" style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 8px; width: 45%">
                                <h4 style="margin: 0 0 15px 0; font-weight: normal; font-size: 14px;">Product</h4>
                                <div style="display: flex; flex-wrap: wrap; gap: 15px;">
                                    <div class="input-forms">
                                        <label for="product-name-1"><span class="req-asterisk">*</span> Name</label>
                                        <input type="text" id="product-name-1" name="product_name_1" required maxlength="100">
                                        <p class="error-text" style="display: none"></p>
                                    </div>
                                    
                                    <div class="input-forms">
                                        <label><span class="req-asterisk">*</span> Product condition</label>
                                        <div style="display: flex; gap: 15px; align-items: center;">
                                            <div style="display: flex; align-items: center; gap: 5px;">
                                                <input type="checkbox" name="condition_1" id="fresh-1" value="fresh">
                                                <label for="fresh-1">Fresh</label>
                                            </div>
                                            <div style="display: flex; align-items: center; gap: 5px;">
                                                <input type="checkbox" name="condition_1" id="frozen-1" value="frozen">
                                                <label for="frozen-1">Frozen</label>
                                            </div>
                                        </div>
                                        <p class="error-text" style="display: none"></p>
                                    </div>
                                    
                                    <div class="input-forms">
                                        <label for="weight-req-1"><span class="req-asterisk">*</span> Weight requirement</label>
                                        <input type="text" id="weight-req-1" name="weight_requirement_1" required maxlength="50">
                                        <p class="error-text" style="display: none"></p>
                                    </div>
                                    
                                    <div class="input-forms">
                                        <label><span class="req-asterisk">*</span> Packaging requirement</label>
                                        <div style="display: flex; gap: 10px;">
                                            <div>
                                                <label for="primary-pack-1" style="font-size: 12px; display: block;">Primary</label>
                                                <select name="primary_packaging_1" id="primary-pack-1" style="height: 35px; font-size: 13px;" required>
                                                    <option value="" disabled selected>-- Select primary packaging --</option>
                                                    <option value="sunny_plastic">Sunny plastic</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label for="secondary-pack-1" style="font-size: 12px; display: block;">Secondary</label>
                                                <select name="secondary_packaging_1" id="secondary-pack-1" style="height: 35px; font-size: 13px;" required>
                                                    <option value="" disabled selected>-- Select secondary packaging --</option>
                                                    <option value="sack_wrapper">Sack wrapper</option>
                                                </select>
                                            </div>
                                        </div>
                                        <p class="error-text" style="display: none"></p>
                                    </div>
                                    
                                    <div class="input-forms">
                                        <label for="labeling-req-1"><span class="req-asterisk">*</span> Labeling requirement</label>
                                        <input type="text" id="labeling-req-1" name="labeling_requirement_1" required maxlength="255">
                                        <p class="error-text" style="display: none"></p>
                                    </div>
                                    
                                    <div class="input-forms">
                                        <label for="rejection-param-1"><span class="req-asterisk">*</span> Rejection parameter</label>
                                        <input type="text" id="rejection-param-1" name="rejection_parameter_1" required maxlength="255">
                                        <p class="error-text" style="display: none"></p>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="button" class="add-btn" onclick="addProductRow()">
                                <span class="material-symbols-outlined">add</span>
                                Product
                            </button>
                        </div>
                    </section>

                    <section class="group-details">
                        <p class="group-name">PPE requirements</p>
                        <div class="form-list">
                            <div class="input-forms">
                                <label for="ppe-req"><span class="req-asterisk">*</span> PPE requirements during delivery and receiving</label>
                                <input type="text" id="ppe-req" name="ppe_requirements" required maxlength="255">
                                <p class="error-text" style="display: none"></p>
                            </div>
                        </div>
                    </section>

                    <section class="group-details">
                        <p class="group-name">Delivery requirements</p>
                        <div class="form-list">
                            <div class="input-forms">
                                <label for="del-freq"><span class="req-asterisk">*</span> Frequency of delivery and receiving time</label>
                                <input type="text" id="del-freq" name="delivery_frequency" required maxlength="255">
                                <p class="error-text" style="display: none"></p>
                            </div>
                            
                            <div class="input-forms">
                                <label for="del-add-1">Delivery address 1</label>
                                <input type="text" id="del-add-1" name="delivery_address_1" maxlength="255">
                                <p class="error-text" style="display: none"></p>
                            </div>
                            
                            <div class="input-forms">
                                <label for="del-add-2">Delivery address 2</label>
                                <input type="text" id="del-add-2" name="delivery_address_2" maxlength="255">
                                <p class="error-text" style="display: none"></p>
                            </div>
                            
                            <div class="input-forms">
                                <label for="del-add-3">Delivery address 3</label>
                                <input type="text" id="del-add-3" name="delivery_address_3" maxlength="255">
                                <p class="error-text" style="display: none"></p>
                            </div>
                        </div>
                    </section>

                    <section class="group-details">
                        <p class="group-name">Remarks/Special instructions</p>
                        <div class="form-list">
                            <div class="input-forms">
                                <label for="special-instruc">Special delivery instructions</label>
                                <textarea id="special-instruc" name="delivery_instructions" rows="3" maxlength="255" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; resize: vertical;"></textarea>
                                <p class="error-text" style="display: none"></p>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="step-section" id="step4">
                    <p class="step-title-info">
                        <span class="title">Account security set-up</span>
                        <span class="info">Use your official or a private email you don’t share with others. Choose a strong, unique password to keep your account secure.</span>
                    </p>
                    <section class="group-details">
                        <p class="group-name">Email address</p>
                        <div class="form-list">
                            <div class="input-forms">
                                <label for="email_add"><span class="req-asterisk">*</span> An email confirmation link will be sen to this address</label>
                                <input type="text" name="email_add" id="email_add" required placeholder="@gmail.com" maxlength="255">
                                <p class="error-text" style="display: none"></p>
                            </div>
                            
                        </div>
                    </section>
                    <section class="group-details">
                        <p class="group-name">Password</p>
                        <div class="form-list">
                   
                            <div class="input-forms">
                                <label for="password"><span class="req-asterisk">*</span> Password</label>
                                <input type="password" name="password" id="password" required minlength="6" maxlength="255">
                            </div>

                            <div class="input-forms">
                                <label for="password_confirmation"><span class="req-asterisk">*</span> Confirm Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" required minlength="6" maxlength="255">
                                <p class="error-text" id="password-match-error"></p>
                            </div>

                        
                        </div>
                            <div id="password-strength" style="margin-top: 5px; font-size: 12px;">
                                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                                    <span id="length-check" style="color: #ccc;">✓ Minimum of 6 characters</span>
                                    <span id="number-check" style="color: #ccc;">✓ Contains a number</span>
                                    <span id="special-check" style="color: #ccc;">✓ Contains special character</span>
                                    <span id="match-check" style="color: #ccc;">✓ Passwords match</span>
                                </div>
                            </div>
                    </section>

                    <section class="group-details">
                        <p class="group-name">Agreement</p>
                        <div class="form-list">
                            
                            <div class="input-forms">
                                <p class="agreement-text" style="margin: 0; color: #333; font-size: 13px;">
                                    I/We agree that all information provided in this form is true and correct. 
                                    <br> 
                                    I/We agree that Sunny and Scramble Corporation may use this information for purpose of background check and marketing purposes.  
                                    <br>
                                    I agree that my/our information will be kept confidential.
                                </p>
                            </div>
                            <div class="input-forms" style="flex-direction: row">
                                <input type="checkbox" name="agreement" id="agreement" required style="margin: 0" required>
                                <label for="agreement" style="margin: 0">
                                    I have read and understood the above agreement, and I hereby confirm my acceptance of the terms and conditions stated.
                                </label>
                            </div>
                        </div>
                    </section>

                    <section class="group-details" style="display: flex; align-items: center; justify-content: center; margin-top: 20px; background-color: transparent; box-shadow: none;">
                    
                        <button type="submit" class="register-btn btn-transition" style="">
                            Register account
                        </button>
                            
                    </section>


                </div>

            </form>

            <button class="next-btn act-btn" type="button"><span class="material-symbols-outlined">arrow_forward_ios</span></button>
        </div>


    </div>


    <script src="{{ asset('js/registration/x/clone-auth-set.js') }}"></script>
    <script src="{{ asset('js/registration/x/clone-sign-set.js') }}"></script>
    <script src="{{ asset('js/registration/x/file-size.js') }}"></script>
    <script src="{{ asset('js/registration/x/toggle-stepper.js') }}"></script>
    <script src="{{ asset('js/registration/password-validation.js') }}"></script>
    <script src="{{ asset('js/registration/x/default-logo.js') }}"></script>


</body>
</html>
