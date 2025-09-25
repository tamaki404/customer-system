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


</head>
<body style="overflow: hidden">


    <div class="display-message">
        <p class="error-text"></p>
    </div>

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
                <section class="step-section" id="step1">
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
                                    <option value="">Wholesale</option>
                                    <option value="">Distributor</option>
                                    <option value="">HRI</option>
                                    <option value="">Dealer</option>
                                </select>
                            </div>
                            <div class="input-forms">
                                <label for="company-image"><span class="req-asterisk"></span> Company image/logo</label>
                                <input type="file" class="image" id="company-image" name="image" accept="image/*">
                                <div class="use-default"><input type="checkbox" name="default_img">Use default</div>
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
                                    <p class="error-text" style="display: none"></p>
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
                        <p style="margin: 0; font-size: 13px; color: #666; margin-bottom: 5px;">Authorized signatories to accept deliviries and sign invoices</p>
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
                                    <p class="error-text" style="display: none"></p>
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
                </section>


            </form>

            <button class="next-btn act-btn" type="button"><span class="material-symbols-outlined">arrow_forward_ios</span></button>
        </div>


    </div>




    <script src="{{ asset('js/registration/x/clone-auth-set.js') }}"></script>
    <script src="{{ asset('js/registration/x/clone-sign-set.js') }}"></script>
    <script src="{{ asset('js/registration/x/file-size.js') }}"></script>


</body>
</html>
