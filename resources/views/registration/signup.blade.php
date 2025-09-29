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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

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

        .search-result{
            cursor: pointer;
            border-bottom: 1px solid #6666661e;
            padding: 5px
        }
        .search-result:hover{
            background-color: #f9fbfc;
        }

    </style>
<script>
    // Pass PHP session data to JavaScript
    window.preserveStep = @json(session('preserve_step'));
    window.validationFailed = @json(session('validation_failed'));
</script>
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
                        <span class="title">1/4 Company information</span>
                        <span class="info">Answer the inputs below regarding the right details of your company</span>
                    </p>
                    <section class="group-details" style="border-top-left-radius: 5px; border-top-right-radius: 5px;">
                        <p class="group-name">Profile</p>
                        <div class="form-list">
                            <div class="input-forms">
                                <label for="company_name"><span class="req-asterisk">*</span> Company name</label>
                                <input type="text" name="company_name" id="company_name" style="width: 250px"  maxlength="200" required value="{{ old('company_name') }}">
                                <p class="error-text" style="display: none"></p>
                            </div>
                            <div class="input-forms">
                                <label for="category"><span class="req-asterisk">*</span> Category</label>
                                <select name="category" id="category" required>
                                    <option value="" disabled {{ old('category') ? '' : 'selected' }}>-- Select category --</option>
                                    <option value="Wholesale" {{ old('category') == 'Wholesale' ? 'selected' : '' }}>Wholesale</option>
                                    <option value="Distributor" {{ old('category') == 'Distributor' ? 'selected' : '' }}>Distributor</option>
                                    <option value="HRI" {{ old('category') == 'HRI' ? 'selected' : '' }}>HRI</option>
                                    <option value="Dealer" {{ old('category') == 'Dealer' ? 'selected' : '' }}>Dealer</option>
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
                                    <input type="text" name="home_street" id="home_street" required placeholder="Street" maxlength="255" value="{{ old('home_street') }}">
                                    <input type="text" name="home_subdivision" id="home-subdivision" required placeholder="Subdivision" maxlength="255" value="{{ old('home_subdivision') }}">
                                    <input type="text" name="home_barangay" id="home-barangay" required placeholder="Barangay" maxlength="255" value="{{ old('home_barangay') }}">
                                    <input type="text" name="home_city" id="home-city" required placeholder="City" maxlength="100" value="{{ old('home_city') }}">
                                </div>
                                <p class="error-text" style="display: none"></p>
                            </div>
                            <div class="input-forms">
                                <label for="office-street"><span class="req-asterisk">*</span> Office address</label>
                                    <div class="office-address">
                                        <input type="text" name="office_street" id="office-street" required placeholder="Street" maxlength="255" value="{{ old('office_street') }}">
                                        <input type="text" name="office_subdivision" id="office-subdivision" required placeholder="Subdivision" maxlength="255" value="{{ old('office_subdivision') }}">
                                        <input type="text" name="office_barangay" id="office_barangay" required placeholder="Barangay" maxlength="255" value="{{ old('office_barangay') }}">
                                        <input type="text" name="office_city" id="office-city" required placeholder="City" maxlength="100" value="{{ old('office_city') }}">
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
                                <input type="text" name="mobile" id="mobile" style="width: 150px" placeholder="ex: 09XX-XXX-XXXX"  maxlength="11" required value="{{ old('mobile') }}">
                                <p class="error-text" style="display: none"></p>
                            </div>
                            <div class="input-forms">
                                <label for="tele"> Telephone no.</label>
                                <input type="text" name="tele" id="tele" style="width: 150px" placeholder="ex: 02-XXX-XXXX"  maxlength="9" value="{{ old('tele') }}">
                                <p class="error-text" style="display: none"></p>
                            </div>
                            <div class="input-forms">
                                <label for="civil-status"> Civil status</label>
                                    <select name="civil_status" id="civil-status">
                                        <option value="" disabled {{ old('civil_status') ? '' : 'selected' }}>-- Select civil status --</option>
                                        <option value="Single" {{ old('civil_status') == 'Single' ? 'selected' : '' }}>Single</option>
                                        <option value="Married" {{ old('civil_status') == 'Married' ? 'selected' : '' }}>Married</option>
                                        <option value="Divorced" {{ old('civil_status') == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                                        <option value="Widowed" {{ old('civil_status') == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                    </select>
                                <p class="error-text" style="display: none"></p>
                            </div>
                            <div class="input-forms">
                                <label for="citizenship" ><span class="req-asterisk">*</span> Citizenship</label>
                                    <select name="citizenship" id="citizenship" required>
                                        <option value="" disabled {{ old('citizenship') ? '' : 'selected' }}>-- Select citizenship --</option>
                                        <option value="Filipino" {{ old('citizenship') == 'Filipino' ? 'selected' : '' }}>Filipino</option>
                                        <option value="American" {{ old('citizenship') == 'American' ? 'selected' : '' }}>American</option>
                                        <option value="Canadian" {{ old('citizenship') == 'Canadian' ? 'selected' : '' }}>Canadian</option>
                                        <option value="British" {{ old('citizenship') == 'British' ? 'selected' : '' }}>British</option>
                                        <option value="Other" {{ old('citizenship') == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                <p class="error-text" style="display: none"></p>
                            </div>
                            <div class="input-forms">
                                <label for="payment"><span class="req-asterisk">*</span> Payment method</label>
                                    <select name="payment_method" id="payment" required>
                                        <option value="" disabled {{ old('payment_method') ? '' : 'selected' }}>-- Select payment method --</option>
                                        <option value="Cash" {{ old('payment_method') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="Gcash" {{ old('payment_method') == 'Gcash' ? 'selected' : '' }}>Gcash</option>
                                        <option value="Bank transfer" {{ old('payment_method') == 'Bank transfer' ? 'selected' : '' }}>Bank transfer</option>
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
                                <select name="id_type" id="id-type" required>
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
                                <input type="text" name="id_number" id="id_number" style="width: 200px"  maxlength="100" required value="{{ old('id_number') }}">
                                <p class="error-text" style="display: none"></p>
                            </div>
                            <div class="input-forms">
                                <label for="birthdate"> Birthdate </label>
                                <input type="date" name="birthdate" id="birthdate" style="width: 150px" required maxlength="100" value="{{ old('birthdate') }}">
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
                                        <input id="rep-name" type="text" name="rep_lastname" placeholder="Last name" required maxlength="50" value="{{ old('rep_lastname') }}">
                                        <input type="text" name="rep_firstname" placeholder="First name" required maxlength="50" value="{{ old('rep_firstname') }}">
                                        <input type="text" name="rep_middlename" placeholder="Middle name" maxlength="50" value="{{ old('rep_middlename') }}">
                                    </div>
                                    <p class="error-text" style="display: none"></p>
                                </div>
                                <div class="input-forms">
                                    <label for="auth_position"><span class="req-asterisk">*</span> Position</label>
                                    <div>
                                        <input id="auth_position" type="text" name="auth_position" value="Admin" readonly  required maxlength="50">
                                    </div>
                                    <p class="error-text" style="display: none"></p>
                                </div>
                                <div class="input-forms">
                                    <label for="contact"><span class="req-asterisk">*</span> Contact no.</label>
                                    <div>
                                        <input id="contact" type="text" name="rep_contact" placeholder="ex: 09XX-XXX-XXXX" required maxlength="11" value="{{ old('rep_contact') }}">
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
                                        <input id="sign-name" type="text" name="sign_lastname" placeholder="Last name" required maxlength="50" value="{{ old('sign_lastname') }}">
                                        <input type="text" name="sign_firstname" placeholder="First name" required maxlength="50" value="{{ old('sign_firstname') }}">
                                        <input type="text" name="sign_middlename" placeholder="Middle name" maxlength="50" value="{{ old('sign_middlename') }}">
                                    </div>
                                    <p class="error-text" style="display: none"></p>
                                </div>
                                <div class="input-forms">
                                    <label for="position"><span class="req-asterisk">*</span> Position</label>
                                    <div>
                                        <input id="position" type="text" name="sign_position" required maxlength="50" value="{{ old('sign_position') }}">
                                    </div>
                                    <p class="error-text" style="display: none"></p>
                                </div>
                                <div class="input-forms">
                                    <label for="id-signature"><span class="req-asterisk">*</span> E-signature</label>
                                        <input type="file" id="id-signature" name="e_image" accept="image/*" required>
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
                                    <label for="account_name"> Account name</label>
                                    <input type="text" name="account_name" id="account_name" style="width: 300px"  maxlength="255" value="{{ old('account_name') }}">
                                    <p class="error-text" style="display: none"></p>
                                </div>
                                <div class="input-forms">
                                    <label for="bank"> Bank</label>
                                    <input type="text" name="bank" id="bank" style="width: 80px"  maxlength="255" value="{{ old('bank') }}">
                                    <p class="error-text" style="display: none"></p>
                                </div>
                                <div class="input-forms">
                                    <label for="branch"> Branch</label>
                                    <input type="text" name="branch" id="branch" style="width: 150px"  maxlength="200" value="{{ old('branch') }}">
                                    <p class="error-text" style="display: none"></p>
                                </div>   
                                <div class="input-forms">
                                    <label for="account_number"> Account number</label>
                                    <input type="text" name="account_number" id="account_number" style="width: 150px"  maxlength="50" value="{{ old('account_number') }}">
                                    <p class="error-text" style="display: none"></p>
                                </div> 
                            </div>  
                           
                        </div>
                    </section>
                    <section class="group-details">
                        <p class="group-name">Business</p>
                        <div class="form-list">
                                <div class="input-forms">
                                    <label for="years"> How long have you been in the industry?</label>
                                    <input type="text" name="years" id="years" placeholder="5 years" style="width: 150px"  maxlength="50" value="{{ old('years') }}">
                                    <p class="error-text" style="display: none"></p>
                                </div>
                                <div class="input-forms">
                                    <label for="reffered_by"> Referred by</label>
                                    <input type="text" name="reffered_by" id="reffered_by" style="width: 200px"  maxlength="255" value="{{ old('reffered_by') }}">
                                    <p class="error-text" style="display: none"></p>
                                </div>
                                <div class="input-forms">
                                    <label for="contacted_by"> Contacted by</label>
                                    <input type="text" name="contacted_by" id="contacted_by" style="width: 200px"  maxlength="200" value="{{ old('contacted_by') }}">
                                    <p class="error-text" style="display: none"></p>
                                </div>   
                         
                            </div>  
                     
                    </section>
                </div>
                <div class="step-section" id="step2">
                    <p class="step-title-info">
                        <span class="title">2/4 Documents filing</span>
                        <span class="info">All documents listed are required. Files must be in PDF format and below 2MB.</span>
                    </p>
                    <section class="group-details" style="border-top-left-radius: 5px; border-top-right-radius: 5px;">
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
                                <label for="valid_one"><span class="req-asterisk">*</span> Valid ID (2)</label>
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
                <div class="step-section" id="step3" style="width: 100%"> 
                    <p class="step-title-info">
                        <span class="title">3/4 Customer product requirements</span>
                        <span class="info">Answer the inputs below regarding your product requirements and delivery preferences</span>
                    </p>
                    
                    <section class="group-details" style="border-top-left-radius: 5px; border-top-right-radius: 5px;">
                        <p class="group-name">Product search and add</p>
                        <div class="form-list">
                            <div class="input-forms" style="margin-bottom: 20px; width: 45%;">
                                <label for="product-search"><span class="req-asterisk">*</span> Search for a product</label>
                                <input type="text" id="product-search" style="width: 100%;" placeholder="Type to search products...">
                                <div id="product-search-results" style="display:none; width: 300px; position: absolute; background: #fff; border: 1px solid #ccc; z-index: 10;  max-height: 200px; overflow-y: auto; border-radius: 10px; padding: 10px;">
                                    
                                </div>
                            </div>
                            <div id="all-products" style="display:none;">
                                @foreach($products as $product)
                                    <div class="product-item" data-id="{{ $product->id }}" data-name="{{ $product->name }}">{{ $product->name }}</div>
                                @endforeach
                            </div>
                        </div>
                    </section>

                    <section class="group-details">
                        <p class="group-name">Product details verification</p>
                        <div class="form-list" id="product-forms-container">
                            <!-- Forms will be injected here -->
                        </div>

                        <template id="product-form-template">
                          
                            <div class="product-form-main" style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 8px; width: 45%;">
                                <h4 style="margin: 0 0 15px 0; font-weight: normal; font-size: 14px;">Product: __PRODUCT_NAME__</h4>
                                <input type="hidden" name="product_ids[]"  value="__PRODUCT_ID__">

                                <div style="display: flex; flex-wrap: wrap; gap: 15px;">
                                    <div class="input-forms">
                                        <label for="product_name"><span class="req-asterisk">*</span> Name</label>
                                        <input id="product_name" type="text" name="product_name___PRODUCT_ID__" value="__PRODUCT_NAME__" readonly>

                                    </div>

                                    <div class="input-forms">
                                        <label for="fresh-__PRODUCT_ID__"><span class="req-asterisk">*</span> Product condition</label>
                                        <div  style="display: flex; gap: 15px; align-items: center;">
                                            <div style="display: flex; align-items: center; gap: 5px;">
                                                <input type="checkbox" name="condition___PRODUCT_ID__[]" id="fresh-__PRODUCT_ID__" value="fresh">
                                                <label for="fresh-__PRODUCT_ID__">Fresh</label>

                                            </div>
                                            <div style="display: flex; align-items: center; gap: 5px;">
                                                <input type="checkbox" name="condition___PRODUCT_ID__[]" id="frozen-__PRODUCT_ID__" value="frozen">
                                                <label for="frozen-__PRODUCT_ID__">Frozen</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="input-forms">
                                        <label for="weight_req"><span class="req-asterisk">*</span> Weight requirement</label>
                                        <input type="text" id="weight_req" name="weight_requirement___PRODUCT_ID__" required maxlength="50">
                                    </div>

                                    <div class="input-forms">
                                        <label for="select_primary"><span class="req-asterisk">*</span> Packaging requirement</label>
                                        <div  style="display: flex; gap: 10px;">
                                            <div>
                                                <label for="select_primary" style="font-size: 12px; display: block;">Primary</label>
                                                <select id="select_primary" name="primary_packaging___PRODUCT_ID__" style="height: 35px; font-size: 13px;" required>
                                                    <option  value=""  disabled selected>-- Select primary packaging --</option>
                                                    <option value="sunny_plastic">Sunny plastic</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label for="sec_packaging" style="font-size: 12px; display: block;">Secondary</label>
                                                <select id="sec_packaging" name="secondary_packaging___PRODUCT_ID__" style="height: 35px; font-size: 13px;" required>
                                                    <option value="" disabled selected>-- Select secondary packaging --</option>
                                                    <option value="sack_wrapper">Sack wrapper</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="input-forms">
                                        <label for="labeling_req"><span class="req-asterisk">*</span> Labeling requirement</label>
                                        <input id="labeling_req" type="text" name="labeling_requirement___PRODUCT_ID__" required maxlength="255">
                                    </div>

                                    <div class="input-forms">
                                        <label for="reject_param"><span class="req-asterisk">*</span> Rejection parameter</label>
                                        <input id="reject_param" type="text" name="rejection_parameter___PRODUCT_ID__" required maxlength="255">
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="remove-btn" onclick="this.closest('.product-form-main').remove();" style="margin-top: 10px; background: #f8d7da; color: #721c24; border: none; padding: 5px 10px; border-radius: 4px;">
                                Remove Product
                            </button>

                        </template>


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
                                <label for="del-add-1"><span class="req-asterisk">*</span> Delivery address 1</label>
                                <input type="text" id="del-add-1" name="delivery_address_1" maxlength="255" required>
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
                                <textarea id="special-instruc" name="delivery_instructions" rows="3" maxlength="255" style="width: 500px; padding: 8px;  border-radius: 4px; resize: none;">{{ old('delivery_instructions') }}</textarea>
                                <p class="error-text" style="display: none"></p>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="step-section" id="step4">
                    <p class="step-title-info">
                        <span class="title">4/4 Account security set-up</span>
                        <span class="info">Use your official or a private email you don’t share with others. Choose a strong, unique password to keep your account secure.</span>
                    </p>
                   <section class="group-details" style="border-top-left-radius: 5px; border-top-right-radius: 5px;">
                        <p class="group-name">Email address</p>
                        <div class="form-list">
                            <div class="input-forms">
                                <label for="email_add"><span class="req-asterisk">*</span> An email confirmation link will be sen to this address</label>
                                <input type="text" name="email_add" id="email_add" required placeholder="@gmail.com" maxlength="255" value="{{ old('email_add') }}">
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
                                <span class="req-asterisk">*</span>
                                <input type="checkbox" name="agreement" id="agreement" required>
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
    <script src="{{ asset('js/registration/x/two-mb.js') }}"></script>
    <script src="{{ asset('js/registration/x/digit-only.js') }}"></script>

<!-- jQuery (must be first) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(function () {
    const $search = $('#product-search');
    const $results = $('#product-search-results');
    const $allProducts = $('#all-products .product-item');

    $search.on('input', function() {
        const val = $(this).val().toLowerCase();
        $results.empty();
        if (!val) {
            $results.hide();
            return;
        }
        let found = false;
        $allProducts.each(function() {
            const name = $(this).data('name').toLowerCase();
            if (name.includes(val)) {
                $results.append(`<div class="search-result" data-id="${$(this).data('id')}" data-name="${$(this).data('name')}">${$(this).data('name')}</div>`);
                found = true;
            }
        });
        $results.toggle(found);
    });

    $results.on('click', '.search-result', function() {
        const productId = $(this).data('id');
        const productName = $(this).data('name');
        if (document.querySelector(`.product-form-main[data-id="${productId}"]`)) {
            alert("Product already added.");
            $results.hide();
            return;
        }
        const template = document.getElementById('product-form-template').innerHTML;
        const filledTemplate = template
            .replace(/__PRODUCT_ID__/g, productId)
            .replace(/__PRODUCT_NAME__/g, productName);
        const wrapper = document.createElement('div');
        wrapper.innerHTML = filledTemplate.trim();
        const formElement = wrapper.firstElementChild;
        formElement.setAttribute('data-id', productId);
        document.getElementById('product-forms-container').appendChild(formElement);
        $results.hide();
        $search.val('');
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#product-search, #product-search-results').length) {
            $results.hide();
        }
    });
});
</script>
</body>
</html>
