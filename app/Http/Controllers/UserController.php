<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Suppliers;
use App\Models\Representatives;
use App\Models\Signatories;
use App\Models\Banks;
use App\Models\Documents;
use App\Models\Staffs;
use App\Models\AccountStatus;
use Illuminate\Support\Facades\Log;
use App\Models\Address;
use App\Models\Business;
use App\Models\Products;

use App\Models\DeliveryRequirements;
use App\Models\ProductRequirements;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
        public static function randomBase36String(int $length): string
        {
            $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $str = '';
            for ($i = 0; $i < $length; $i++) {
                $str .= $chars[random_int(0, strlen($chars) - 1)];
            }
            return $str;
        }
   
    public function showSignupForm()
    {
    $products = Products::all();
    return view('registration.signup', compact('products'));
    }

    public function signinRepresentative(Request $request)
    {
        $request->validate([
            'rep_id' => 'required|exists:representatives,rep_id',
            'user_id' => 'required|exists:users,user_id',
            'auth_position' => 'required'
        ]);

        $user = User::where('user_id', $request->user_id)->firstOrFail();
        $rep = Representatives::where('rep_id', $request->rep_id)->firstOrFail();

        // If Admin, verify the password
        if ($request->auth_position === "Admin") {
            $request->validate([
                'password' => 'required|string|min:8',
            ]);

            if (!Hash::check($request->password, $user->password)) {
                return back()->withErrors(['loginError' => 'Invalid admin password.']);
            }
        } else {
            // If not Admin, match cid
            $request->validate([
                'cid' => 'required|string',
            ]);

            if ($request->cid !== $rep->cid) {
                return back()->withErrors(['loginError' => 'Invalid representative CID.']);
            }
        }

        // Login the representative using the representative guard
        Auth::guard('representative')->login($rep);

        return redirect()->route('dashboard.view')->with('success', 'Representative signed in successfully.');
    }
    public function registerSupplier(Request $request)


        {
            $request->validate([
                // User
                'email_add'       => 'required|email|unique:users,email_address|max:255',
                'password'        => 'required|string|confirmed|min:6|max:255',
                'gate_password'        => 'required|string|confirmed|min:6|max:255',

                'image'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'default_image'   => 'nullable|string|in:true,false',
                'agreement'       => 'required',

                // Addresses
                'home_street'     => 'required|string|max:255',
                'home_subdivision'=> 'required|string|max:255',
                'home_barangay'   => 'required|string|max:255',
                'home_city'       => 'required|string|max:100',
                'office_street'   => 'required|string|max:255',
                'office_subdivision'=> 'required|string|max:255',
                'office_barangay' => 'required|string|max:255',
                'office_city'     => 'required|string|max:100',

                // Suppliers
                'company_name'    => 'required|string|max:200',
                'category'        => 'required|string|in:Wholesale,Distributor,HRI,Dealer',
                'mobile'          => 'required|string|regex:/^09[0-9]{9}$/|size:11',
                'citizenship'     => 'required|string|max:100',
                'payment_method'  => 'required|string|in:Cash,Gcash,Bank transfer',
                'tele'            => 'nullable|string|size:9',
                'civil_status'    => 'nullable|string|in:Single,Married,Divorced,Widowed',
                'id_image'        => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
                'id_type'         => 'required|string|in:Passport,Driver\'s License,National ID,SSS ID,GSIS ID,UMID,Postal ID,PhilHealth ID,Voter\'s ID,PRC ID',
                'id_number'       => 'required|string|max:100',
                'birthdate'       => 'required|date|before:today',

                // Representatives 
                // 'rep_lastname'    => 'required|string|max:50',
                // 'rep_firstname'   => 'required|string|max:50',
                // 'rep_middlename'  => 'nullable|string|max:50',
                // 'auth_position'   => 'required|string|max:50',
                // 'rep_contact'     => 'required|string|regex:/^09[0-9]{9}$/|size:11',

                'rep_lastname'    => 'required|array|min:1',
                'rep_lastname.*'  => 'required|string|max:50',
                'rep_firstname'   => 'required|array|min:1',
                'rep_firstname.*' => 'required|string|max:50',
                'rep_middlename'  => 'nullable|array',
                'rep_middlename.*'=> 'nullable|string|max:50',
                'auth_position'   => 'required|array|min:1',
                'auth_position.*' => 'required|string|max:50',
                'rep_contact'     => 'required|array|min:1',
                'rep_contact.*'   => 'required|string|regex:/^09[0-9]{9}$/|size:11',


                // Signatories 
                // 'sign_lastname'   => 'required|string|max:50',
                // 'sign_firstname'  => 'required|string|max:50',
                // 'sign_middlename' => 'nullable|string|max:50',
                // 'sign_position'   => 'required|string|max:50',
                // 'e_image'         => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
                'sign_lastname'   => 'required|array|min:1',
                'sign_lastname.*' => 'required|string|max:50',
                'sign_firstname'  => 'required|array|min:1',
                'sign_firstname.*'=> 'required|string|max:50',
                'sign_middlename' => 'nullable|array',
                'sign_middlename.*'=> 'nullable|string|max:50',
                'sign_position'   => 'required|array|min:1',
                'sign_position.*' => 'required|string|max:50',
                'e_image'         => 'required|array|min:1',
                'e_image.*'       => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',

                // Banks
                'account_name'    => 'nullable|string|max:255',
                'bank'            => 'nullable|string|max:255',
                'branch'          => 'nullable|string|max:200',
                'account_number'  => 'nullable|string|max:50',

                // Business
                'years'           => 'nullable|string|max:50',
                'referred_by'     => 'nullable|string|max:255',
                'contacted_by'    => 'nullable|string|max:200',

                // Documents (all required PDFs)
                'SEC'             => 'required|file|mimes:pdf|max:2048',
                'BP'              => 'required|file|mimes:pdf|max:2048',
                'BIR'             => 'required|file|mimes:pdf|max:2048',
                'MP'              => 'required|file|mimes:pdf|max:2048',
                'valid_one'       => 'required|file|mimes:pdf|max:2048',
                'valid_two'       => 'required|file|mimes:pdf|max:2048',
                'BS'              => 'required|file|mimes:pdf|max:2048',
                'PB'              => 'required|file|mimes:pdf|max:2048',
                'NCC'             => 'required|file|mimes:pdf|max:2048',
                'AIB'             => 'required|file|mimes:pdf|max:2048',

                // Product Requirements (arrays for multiple products)
                'product_ids'     => 'required|array|min:1',
                'product_ids.*'   => 'required|exists:products,product_id',

                // Delivery Requirements
                'ppe_requirements'       => 'required|string|max:255',
                'delivery_frequency'     => 'required|string|max:255',
                'delivery_address_1'     => 'required|string|max:255',
                'delivery_address_2'     => 'nullable|string|max:255',
                'delivery_address_3'     => 'nullable|string|max:255',
                'delivery_instructions'  => 'nullable|string|max:255',
                'deliveries_per_week'    => 'nullable|integer|min:0',
                'delivery_days.*'          => 'required|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
                'receiving_time'         => 'nullable|date_format:H:i',
                'deliveries_per_month'    => 'nullable|integer|min:0',

            ]);

            // Additional dynamic validation for product requirements
            $productIds = $request->input('product_ids', []);
            $dynamicRules = [];
            
            foreach ($productIds as $productId) {
                $dynamicRules["condition_{$productId}"] = 'required|array|min:1';
                $dynamicRules["condition_{$productId}.*"] = 'in:fresh,frozen';
                $dynamicRules["weight_requirement_{$productId}"] = 'required|string|max:50';
                $dynamicRules["primary_packaging_{$productId}"] = 'required|string|max:100';
                $dynamicRules["secondary_packaging_{$productId}"] = 'required|string|max:100';
                $dynamicRules["labeling_requirement_{$productId}"] = 'required|string|max:255';
                $dynamicRules["rejection_parameter_{$productId}"] = 'required|string|max:255';
            }
            
            $request->validate($dynamicRules);

            DB::beginTransaction();

            // ID generation
            $date = date('Ymd');
            $user_id = 'USR-' . $date . '-' . $this->randomBase36String(5);
            $supplier_id = 'SUP-' . $date . '-' . $this->randomBase36String(5);
            $status_id = 'STAT-' . $date . '-' . $this->randomBase36String(5);
            $rep_id = 'REP-' . $date . '-' . $this->randomBase36String(5);

            // Define document types for later use
            $documentTypes = [
                'SEC' => 'Securities and Exchange Commission',
                'BP' => 'Business Permit',
                'BIR' => 'BIR Form 2303',
                'MP' => 'Mayor\'s Permit',
                'valid_one' => 'Valid ID 1',
                'valid_two' => 'Valid ID 2',
                'BS' => 'Bank Statement',
                'PB' => 'Proof of Billing',
                'NCC' => 'Notarized Corporation Certificate',
                'AIB' => 'Articles of Incorporation and Bylaws',
            ];

            try {
                // Handle company image upload or use default
                $companyImageBinary = null;
                $companyImageMime = null;
                $companyImageName = null;
                $companyImageSize = null;
                if ($request->input('default_image') === 'true' || $request->boolean('use_default')) {
                    $defaultImagePath = public_path('assets/default-company-logo.png');
                    if (file_exists($defaultImagePath)) {
                        $companyImageBinary = file_get_contents($defaultImagePath);
                        $companyImageMime = mime_content_type($defaultImagePath);
                        $companyImageName = basename($defaultImagePath);
                        $companyImageSize = filesize($defaultImagePath);
                    }
                } elseif ($request->hasFile('image')) {
                    $companyImage = $request->file('image');
                    $companyImageBinary = file_get_contents($companyImage->getRealPath());
                    $companyImageMime = $companyImage->getMimeType();
                    $companyImageName = $companyImage->getClientOriginalName();
                    $companyImageSize = $companyImage->getSize();
                }

                // Handle ID image upload
                $idImageBinary = null;
                $idImageMime = null;
                $idImageName = null;
                $idImageSize = null;
                if ($request->hasFile('id_image')) {
                    $idImage = $request->file('id_image');
                    $idImageBinary = file_get_contents($idImage->getRealPath());
                    $idImageMime = $idImage->getMimeType();
                    $idImageName = $idImage->getClientOriginalName();
                    $idImageSize = $idImage->getSize();
                }

                // Handle e-signature image upload
                // $eSignatureBinary = null;
                // $eSignatureMime = null;
                // $eSignatureName = null;
                // $eSignatureSize = null;
                // if ($request->hasFile('e_image')) {
                //     $eSignature = $request->file('e_image');
                //     $eSignatureBinary = file_get_contents($eSignature->getRealPath());
                //     $eSignatureMime = $eSignature->getMimeType();
                //     $eSignatureName = $eSignature->getClientOriginalName();
                //     $eSignatureSize = $eSignature->getSize();
                // }

                // Create User
                $user = User::create([
                    'user_id' => $user_id,
                    'email_address' => $request->email_add,
                    'password' => Hash::make($request->password),
                    'gate_password' => Hash::make($request->gate_password),

                    'role' => 'Supplier',
                    'role_type' => 'Customer',
                    'status' => 'Pending',
                    'email_verified_at' => null,
                    'image' => $companyImageBinary,
                    'image_mime_type' => $companyImageMime,
                    'image_filename' => $companyImageName,
                    'image_size' => $companyImageSize,
                ]);

                $account_status = AccountStatus::create([
                    'supplier_id' => $supplier_id,
                    'user_id' => $user_id,
                    'status_id' => $status_id,
                    'account_status' => 'Pending',
                ]);

                // Create Home Address
                $homeAddress = Address::create([
                    'user_id' => $user_id,
                    'supplier_id' => $supplier_id,

                    'home_street' => $request->home_street,
                    'home_subdivision' => $request->home_subdivision,
                    'home_barangay' => $request->home_barangay,
                    'home_city' => $request->home_city,
                    'office_street' => $request->office_street,
                    'office_subdivision' => $request->office_subdivision,
                    'office_barangay' => $request->office_barangay,
                    'office_city' => $request->office_city,
                ]);

                // Create Supplier
                $supplier = Suppliers::create([
                    'user_id' => $user_id,
                    'supplier_id' => $supplier_id,
                    'company_name' => $request->company_name,
                    'category' => $request->category,
                    'image' => $companyImageBinary,
                    'image_mime_type' => $companyImageMime,
                    'image_filename' => $companyImageName,
                    'image_size' => $companyImageSize,
                    'mobile' => $request->mobile,
                    'telephone' => $request->tele,
                    'civil_status' => $request->civil_status,
                    'citizenship' => $request->citizenship,
                    'payment_method' => $request->payment_method,
                    'id_image' => $idImageBinary,
                    'id_image_mime_type' => $idImageMime,
                    'id_image_filename' => $idImageName,
                    'id_image_size' => $idImageSize,
                    'id_type' => $request->id_type,
                    'id_number' => $request->id_number,
                    'birthdate' => $request->birthdate,
                    'years_in_industry' => $request->years,
                    'referred_by' => $request->referred_by,
                    'contacted_by' => $request->contacted_by,
                    'registration_date' => now(),
                    'status' => 'Pending',
                ]);

                // Create Representative

                $repLastnames = $request->input('rep_lastname', []);
                $repFirstnames = $request->input('rep_firstname', []);
                $repMiddlenames = $request->input('rep_middlename', []);
                $authPositions = $request->input('auth_position', []);
                $repContacts = $request->input('rep_contact', []);

                foreach ($repLastnames as $index => $lastname) {
                    // Default permissions (empty or limited)
                    $permissions = [];

                    // If the auth position is Admin, assign full permissions
                    if ($authPositions[$index] === 'Admin') {
                        $permissions = [
                            'Dashboard' => true,
                            'Profile'   => true,
                            'Credits'   => true,
                            'POP'       => true,
                            'PO'        => true,
                            'Orders'    => true,
                            'Products'  => true,
                            'Groups'    => true,
                        ];
                    }

                    Representatives::create([
                        'user_id'        => $user_id,
                        'supplier_id'    => $supplier_id,
                        'rep_id'         => $rep_id,
                        'rep_lastname'   => $lastname,
                        'rep_firstname'  => $repFirstnames[$index] ?? '',
                        'rep_middlename' => $repMiddlenames[$index] ?? null,
                        'auth_position'  => $authPositions[$index] ?? '',
                        'rep_contact'    => $repContacts[$index] ?? '',
                        'permissions'    => $permissions, 
                    ]);

                }


                // Create Signatories (loop through all)
                $signLastnames = $request->input('sign_lastname', []);
                $signFirstnames = $request->input('sign_firstname', []);
                $signMiddlenames = $request->input('sign_middlename', []);
                $signPositions = $request->input('sign_position', []);
                $eImages = $request->file('e_image', []);

                foreach ($signLastnames as $index => $lastname) {
                    $eSignatureBinary = null;
                    $eSignatureMime = null;
                    $eSignatureName = null;
                    $eSignatureSize = null;
                    
                    if (isset($eImages[$index]) && $eImages[$index]->isValid()) {
                        $eSignature = $eImages[$index];
                        $eSignatureBinary = file_get_contents($eSignature->getRealPath());
                        $eSignatureMime = $eSignature->getMimeType();
                        $eSignatureName = $eSignature->getClientOriginalName();
                        $eSignatureSize = $eSignature->getSize();
                    }
                    
                    Signatories::create([
                        'user_id' => $user_id,
                        'supplier_id' => $supplier_id,
                        'sign_lastname' => $lastname,
                        'sign_firstname' => $signFirstnames[$index] ?? '',
                        'sign_middlename' => $signMiddlenames[$index] ?? null,
                        'sign_position' => $signPositions[$index] ?? '',
                        'e_image' => $eSignatureBinary,
                        'e_mime_type' => $eSignatureMime,
                        'e_filename' => $eSignatureName,
                        'image_size' => $eSignatureSize,
                    ]);
                }

                // Create Bank Details (if provided)
                if ($request->filled('account_name') || $request->filled('bank')) {
                    $bankDetails = Banks::create([
                        'user_id' => $user_id,
                        'supplier_id' => $supplier_id,

                        'account_name' => $request->account_name,
                        'bank' => $request->bank,
                        'branch' => $request->branch,
                        'account_number' => $request->account_number,
                    ]);
                }

                $business = Business::create([
                    'user_id' => $user_id,
                    'supplier_id' => $supplier_id,
                    'years' => $request->years,
                    'referred_by' => $request->referred_by,
                    'contacted_by' => $request->contacted_by,
                ]);
                
                // Handle document uploads (store as mediumblob)
                foreach ($documentTypes as $key => $description) {
                    if ($request->hasFile($key)) {
                        $file = $request->file($key);
                        Documents::create([
                            'user_id' => $user_id,
                            'supplier_id' => $supplier_id,

                            'type' => $key,
                            'description' => $description,
                            'file' => file_get_contents($file->getRealPath()),
                            'file_mime' => $file->getMimeType(),
                            'file_name' => $file->getClientOriginalName(),
                            'file_size' => $file->getSize(),
                            'uploaded_at' => now(),
                        ]);
                    }
                }

                // Create Product Requirements for each selected product
                foreach ($productIds as $productId) {
                    $conditions = $request->input("condition_{$productId}", []);
                    $conditionString = implode(',', $conditions);
                    
                    ProductRequirements::create([
                        'user_id' => $user_id,
                        'supplier_id' => $supplier_id,

                        'product_id' => $productId,
                        'condition' => $conditionString,
                        'weight_requirement' => $request->input("weight_requirement_{$productId}"),
                        'primary_packaging' => $request->input("primary_packaging_{$productId}"),
                        'secondary_packaging' => $request->input("secondary_packaging_{$productId}"),
                        'labeling_requirement' => $request->input("labeling_requirement_{$productId}"),
                        'rejection_parameter' => $request->input("rejection_parameter_{$productId}"),
                    ]);
                }

                // Create Delivery Requirements
                DeliveryRequirements::create([
                    'user_id' => $user_id,
                    'supplier_id' => $supplier_id,

                    'ppe_requirements' => $request->ppe_requirements,
                    'delivery_frequency' => $request->delivery_frequency,
                    'delivery_address_1' => $request->delivery_address_1,
                    'delivery_address_2' => $request->delivery_address_2,
                    'delivery_address_3' => $request->delivery_address_3,
                    'delivery_instructions' => $request->delivery_instructions,
                    'deliveries_per_week' => $request->deliveries_per_week,
                    'delivery_days' => isset($request->delivery_days) ? implode(',', $request->delivery_days) : null,
                    'receiving_time' => $request->receiving_time,
                    'deliveries_per_month' => $request->deliveries_per_month,
                ]);

                // Create and send verification token
                            $plainToken = Str::random(64);
                            DB::table('email_verification_tokens')->insert([
                                'user_id'    => $user->user_id,
                                'email'      => $user->email_address,
                                'token'      => hash('sha256', $plainToken),
                                'expires_at' => now()->addDay(),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);

                            $verifyUrl = url('/email/verify?token=' . $plainToken . '&uid=' . urlencode($user->user_id));

                            try {
                                Mail::send('emails.verify', ['verifyUrl' => $verifyUrl], function($message) use ($user) {
                                    $message->to($user->email_address)->subject('Verify your email address');
                                });
                            } catch (\Throwable $mailErr) {
                                Log::error('Verification email send failed: ' . $mailErr->getMessage());
                            }


                DB::commit();

                // Send email verification notification
                try {
                    $user->sendEmailVerificationNotification();
                } catch (\Exception $emailError) {
                    Log::warning('Email verification failed to send after registration', [
                        'user_id' => $user_id,
                        'email' => $request->email_add,
                        'error' => $emailError->getMessage()
                    ]);
                }

                // Log successful registration
                Log::info('Supplier registration successful', [
                    'user_id' => $user_id,
                    'supplier_id' => $supplier_id,
                    'email' => $request->email_add,
                    'company_name' => $request->company_name,
                    'registration_time' => now()
                ]);

                return redirect()->route('signin')->with('success', 
                    'Registration successful! Your supplier account has been created and is pending approval. ' .
                    'Please check your email for verification and wait for admin confirmation.'
                );

            } catch (\Exception $e) {
                DB::rollBack();
                

                Log::error('Supplier registration error: ' . $e->getMessage(), [
                    'user_id' => $user_id ?? 'N/A',
                    'supplier_id' => $supplier_id ?? 'N/A',
                    'email' => $request->email_add ?? 'N/A',
                    'company_name' => $request->company_name ?? 'N/A',
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                return redirect()->back()
                    ->withErrors(['error' => 'Registration failed due to a system error. Please try again or contact support if the problem persists.'])
                    ->withInput($request->except([
                        'password',
                        'password_confirmation',
                        'image',
                        'id_image',
                        'e_image',
                        'SEC',
                        'BP',
                        'BIR',
                        'MP',
                        'valid_one',
                        'valid_two',
                        'BS',
                        'PB',
                        'NCC',
                        'AIB'
                    ]));

                    }
        }
  
    public function registerStaff(Request $request){
       
        // Log the incoming request for debugging
        Log::info('Staff Registration Request Data:', $request->all());

        try {
            $request->validate([
                'email_address' => 'required|email|unique:users,email_address|max:50',
                'password' => [
                    'required',
                    'string',
                    'min:6',
                    'confirmed',
                    'regex:/^(?=.*[0-9])(?=.*[!@#$%^&*(),.?":{}|<>]).*$/'
                ],
                'mobile_no'       => 'required|string|max:11',
                'telephone_no'    => 'nullable|string|max:11',
                'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
                'lastname'   => 'required|string|max:50',
                'firstname'  => 'required|string|max:50',
                'middlename' => 'nullable|string|max:50',
                'action_by' => 'required|exists:users,user_id',
                'role_type'  => 'required|string|in:sales_representative,procurement_officer,warehouse_staff,accounting_staff,system_admin,inventory_staff',
            ], [
                'password.min' => 'Password must be at least 6 characters long.',
                'password.regex' => 'Password must contain at least one number and one special character.',
                'password.confirmed' => 'Password confirmation does not match password.',
                'email_address.unique' => 'This email address is already registered.',
                'email_address.email' => 'Please enter a valid email address.',
                'role_type.required' => 'Please select a staff role.',
                'role_type.in' => 'Please select a valid staff role.',
                'image.required' => 'Profile picture is required.',
                'image.image' => 'Profile picture must be an image file.',
                'image.mimes' => 'Profile picture must be a jpg, jpeg, png, or webp file.',
                'image.max' => 'Profile picture must be less than 2MB.',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Staff Registration Validation Failed:', $e->errors());
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        }


        DB::beginTransaction();

        $date = date('Ymd');
        function randomBase36String(int $length): string {
            $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $str = '';
            for ($i = 0; $i < $length; $i++) {
                $str .= $chars[random_int(0, strlen($chars) - 1)];
            }
            return $str;
        }

        $staff_id = 'STAFF-' . $date . '-' . randomBase36String(5);
        $log_id = 'LOG-' . $date . '-' . randomBase36String(5);
        $user_id = 'USR-' . $date . '-' . randomBase36String(5);

        try {
            $imageBlob = null;
            $imageMimeType = null;
            $imageFilename = null;
            $imageSize = null;
            
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageBlob = file_get_contents($image->getRealPath());
                $imageMimeType = $image->getMimeType();
                $imageFilename = $image->getClientOriginalName();
                $imageSize = $image->getSize();
            }

            // 1. Create User
            $role = 'Staff';
            if ($request->role_type === "system_administrator") {
                $role = 'Admin';
            }

            $user = User::create([
                'user_id'          => $user_id,
                'email_address'    => $request->email_address,
                'password'         => $request->password,
                'role_type'        => $request->role_type,
                'role'             => $role,
                'image'            => $imageBlob,
                'image_mime_type'  => $imageMimeType,
                'image_filename'   => $imageFilename,
                'image_size'       => $imageSize,
                'gate_password'    => $request->password,
            ]);


            $staff = Staffs::create([
                'user_id'       => $user_id,
                'staff_id'       => $staff_id,
                'action_by'     =>  $request->action_by,
                'log_id'     => $log_id,
                'firstname'    => $request->firstname,
                'lastname'    => $request->lastname,
                'middlename'    => $request->middlename,
                'mobile_no'    => $request->mobile_no,
                'telephone_no'    => $request->telephone_no,
                'status' => 'Pending',

            ]);

            // $log = Logs::create([
            //     'action'       => "Added staff {{$}}",
            //     'action_at'       => today(),
            //     'action_by' => $user_id,

            // ]);

            // Create and send verification token
            $plainToken = Str::random(64);
            DB::table('email_verification_tokens')->insert([
                'user_id'    => $user->user_id,
                'email'      => $user->email_address,
                'token'      => hash('sha256', $plainToken),
                'expires_at' => now()->addDay(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $verifyUrl = url('/email/verify?token=' . $plainToken . '&uid=' . urlencode($user->user_id));

            try {
                Mail::send('emails.verify', ['verifyUrl' => $verifyUrl], function($message) use ($user) {
                    $message->to($user->email_address)->subject('Verify your email address');
                });
            } catch (\Throwable $mailErr) {
                Log::error('Verification email send failed: ' . $mailErr->getMessage());
            }

            DB::commit();

            return redirect()->route('staffs.list')
                ->with('success', 'Staff member has been successfully registered and verification email has been sent.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Staff registration error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            // Check for specific database errors
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                return redirect()->back()
                    ->with('error', 'A staff member with this information already exists. Please check the email address or try again.')
                    ->withInput();
            }
            
            if (str_contains($e->getMessage(), 'foreign key constraint')) {
                return redirect()->back()
                    ->with('error', 'Invalid user reference. Please refresh the page and try again.')
                    ->withInput();
            }
            
            return redirect()->back()
                ->with('error', 'Staff registration failed: ' . $e->getMessage() . '. Please check the logs for more details.')
                ->withInput();
        }
        }

    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $exists = User::where('email_address', $request->email)->exists();
        
        return response()->json(['exists' => $exists]);
    }

    // public function signin(Request $request)
    // {
    //     $key = 'login:' . $request->ip();
    //     if (RateLimiter::tooManyAttempts($key, 5)) {
    //         $seconds = RateLimiter::availableIn($key);
    //         return redirect()->back()->withErrors(['loginError' => "Too many attempts. Try again in {$seconds} seconds."])->withInput();
    //     }
    //     RateLimiter::hit($key, 300);

    //     $credentials = $request->validate([
    //         'email_address' => 'required|email',
    //         'password' => 'required|string|min:8',
    //     ]);

    //     $user = User::where('email_address', $credentials['email_address'])->first();
    //     if (!$user) {
    //         return redirect()->back()->withErrors(['loginError' => 'Invalid credentials.'])->withInput();
    //     }

    //     if (!Hash::check($credentials['password'], $user->password)) {
    //         return redirect()->back()->withErrors(['loginError' => 'Invalid credentials.'])->withInput();
    //     }

    //     //  Branch depending on role
    //     if ($user->role === 'Staff') {
    //         $staff = Staffs::where('user_id', $user->user_id)->first();

    //         if (!$staff) {
    //             return redirect()->route('signin')->with('error', 'Staff record not found. Please contact support.');
    //         }

    //         if (is_null($staff->email_verified_at)) {
    //             return redirect()->route('signin')->with('error', 'Please verify your email before signing in.');
    //         }

    //         if (strtolower($staff->status) !== 'accepted') {
    //             return redirect()->route('signin')->with('error', 'Your staff account is not active yet. Kindly wait for verification.');
    //         }
    //     } else {
    //         $accountStatus = AccountStatus::where('user_id', $user->user_id)->first();

    //         if (!$accountStatus) {
    //             return redirect()->route('signin')->with('error', 'Account status not found. Please contact support.');
    //         }

    //         if (is_null($accountStatus->email_verified_at)) {
    //             return redirect()->route('signin')->with('error', 'Please verify your email before signing in.');
    //         }

    //         if (strtolower($accountStatus->account_status) !== 'accepted') {
    //             return redirect()->route('signin')->with('error', 'Your account is not active yet. Kindly wait for verification.');
    //         }
    //     }

    //     Auth::login($user, false);
    //     $request->session()->regenerate();

    //     return redirect()->route('dashboard.view');
    // }



    public function verifyEmail(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'uid'   => 'required',
        ]);

        $hashedToken = hash('sha256', $request->query('token'));
        $userId = $request->query('uid');

        // check token exists
        $record = DB::table('email_verification_tokens')
            ->where('user_id', $userId)
            ->where('token', $hashedToken)
            ->first();

        if (!$record) {
            return redirect()->route('signin')->with('error', 'Invalid or expired verification link.');
        }

        // check if expired
        if ($record->expires_at && now()->greaterThan($record->expires_at)) {
            return redirect()->route('signin')->with('error', 'Verification link has expired.');
        }

        // find user
        $user = User::where('user_id', $userId)->first();
        if (!$user) {
            return redirect()->route('signin')->with('error', 'User not found.');
        }

        // Mark verified in the correct table
        DB::transaction(function() use ($user, $userId) {
            if ($user->role === 'Staff') {
                //  Update staff table
                DB::table('staffs')->where('user_id', $userId)->update([
                    'email_verified_at' => now(),
                    'status' => 'Accepted',
                ]);
            } else {
                DB::table('account_status')->where('user_id', $userId)->update([
                    'email_verified_at' => now(),
                ]);
            }

            // Remove used token
            DB::table('email_verification_tokens')->where('user_id', $userId)->delete();
        });

        return redirect()->route('signin')->with('success', 'Email verified successfully. You may now sign in.');
    }


    private function validateSecurity(Request $request)
    {
        // Check for suspicious patterns
        $suspiciousPatterns = [
            '/<script/i',
            '/javascript:/i',
            '/on\w+\s*=/i',
            '/<iframe/i',
            '/<object/i',
            '/<embed/i',
            '/<link/i',
            '/<meta/i',
            '/<style/i'
        ];

        $allInputs = $request->all();
        foreach ($allInputs as $key => $value) {
            if (is_string($value)) {
                foreach ($suspiciousPatterns as $pattern) {
                    if (preg_match($pattern, $value)) {
                        abort(400, 'Suspicious input detected. Please check your input and try again.');
                    }
                }
            }
        }

        // Validate file uploads for security
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $image->getRealPath());
            finfo_close($finfo);
            
            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
            if (!in_array($mimeType, $allowedMimes)) {
                abort(400, 'Invalid file type detected.');
            }
        }

        // Check for file uploads in document arrays
        $documentArrays = ['AOL', 'COR', 'BC', 'BP', 'SP', 'EMP', 'CTC', 'PR'];
        foreach ($documentArrays as $arrayKey) {
            if ($request->hasFile($arrayKey)) {
                foreach ($request->file($arrayKey) as $file) {
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeType = finfo_file($finfo, $file->getRealPath());
                    finfo_close($finfo);
                    
                    $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
                    if (!in_array($mimeType, $allowedMimes)) {
                        abort(400, 'Invalid file type detected in documents.');
                    }
                }
            }
        }
    }

    // public function signin(Request $request)
    // {
    //     // $key = 'login:' . $request->ip();
    //     // if (RateLimiter::tooManyAttempts($key, 5)) {
    //     //     $seconds = RateLimiter::availableIn($key);
    //     //     return redirect()->back()->withErrors(['loginError' => "Too many attempts. Try again in {$seconds} seconds."])->withInput();
    //     // }
    //     // RateLimiter::hit($key, 300);

    //     $credentials = $request->validate([
    //         'email_address' => 'required|email',
    //         'password' => 'required|string|min:8',
    //     ]);

    //     $user = User::where('email_address', $credentials['email_address'])->first();
    //     if (!$user) {
    //         return redirect()->back()->withErrors(['loginError' => 'Invalid credentials.'])->withInput();
    //     }

    //     if (!Hash::check($credentials['password'], $user->gate_password)) {
    //         return redirect()->back()->withErrors(['loginError' => 'Invalid credentials.'])->withInput();
    //     }

    //     if ($user->role === 'Staff') {
    //         $staff = Staffs::where('user_id', $user->user_id)->first();

    //         if (!$staff) {
    //             return redirect()->route('signin')->with('error', 'Staff record not found. Please contact support.');
    //         }

    //         if (is_null($staff->email_verified_at)) {
    //             return redirect()->route('signin')->with('error', 'Please verify your email before signing in.');
    //         }

    //         if (strtolower($staff->status) !== 'accepted') {
    //             return redirect()->route('signin')->with('error', 'Your staff account is not active yet. Kindly wait for verification.');
    //         }
    //     } else {
    //         $accountStatus = AccountStatus::where('user_id', $user->user_id)->first();

    //         if (!$accountStatus) {
    //             return redirect()->route('signin')->with('error', 'Account status not found. Please contact support.');
    //         }

    //         if (is_null($accountStatus->email_verified_at)) {
    //             return redirect()->route('signin')->with('error', 'Please verify your email before signing in.');
    //         }

    //         if (strtolower($accountStatus->account_status) !== 'accepted') {
    //             return redirect()->route('signin')->with('error', 'Your account is not active yet. Kindly wait for verification.');
    //         }
    //     }

    //     Auth::login($user, false);
    //     $request->session()->regenerate();

    //     return redirect()->route('dashboard.view');
    // }

//     public function signin(Request $request) 
// {
//     $credentials = $request->validate([
//         'email_address' => 'required|email',
//         'password' => 'required|string|min:8',
//     ]);

//     $user = User::where('email_address', $credentials['email_address'])->first();

//     if (!$user) {
//         return back()->withErrors(['loginError' => 'Invalid credentials.'])->withInput();
//     }

//     // Determine which password column to use based on role
//     $passwordColumn = $user->role === 'Supplier' ? 'password' : 'gate_password';

//     if (!Hash::check($credentials['password'], $user->$passwordColumn)) {
//         return back()->withErrors(['loginError' => 'Invalid credentials.'])->withInput();
//     }

//     // --- Optional account status verification ---
//     if ($user->role === 'Staff') {
//         $staff = Staffs::where('user_id', $user->user_id)->first();
//         if (!$staff) {
//             return back()->withErrors(['loginError' => 'Staff record not found.'])->withInput();
//         }
//         if (is_null($staff->email_verified_at)) {
//             return back()->withErrors(['loginError' => 'Please verify your email before signing in.'])->withInput();
//         }
//         if (strtolower($staff->status) !== 'accepted') {
//             return back()->withErrors(['loginError' => 'Your staff account is not active yet.'])->withInput();
//         }
//     } else {
//         $accountStatus = AccountStatus::where('user_id', $user->user_id)->first();
//         if (!$accountStatus) {
//             return back()->withErrors(['loginError' => 'Account status not found.'])->withInput();
//         }
//         if (is_null($accountStatus->email_verified_at)) {
//             return back()->withErrors(['loginError' => 'Please verify your email before signing in.'])->withInput();
//         }
//         if (strtolower($accountStatus->account_status) !== 'accepted') {
//             return back()->withErrors(['loginError' => 'Your account is not active yet.'])->withInput();
//         }
//     }

//     //  Login user
//     Auth::login($user, false);
//     $request->session()->regenerate();

//     //  Redirect to choose account page after gate_password authentication
//     return redirect()->route('choose.accounts');
// }


public function signin(Request $request) 
{
    $credentials = $request->validate([
        'email_address' => 'required|email',
        'password' => 'required|string|min:8',
    ]);

    $user = User::where('email_address', $credentials['email_address'])->first();

    if (!$user) {
        return back()->withErrors(['loginError' => 'Invalid credentials.'])->withInput();
    }

    // Determine which password column to use
    $passwordColumn = match ($user->role) {
        'Supplier' => 'password',
        'Admin', 'Staff' => 'password',
        default => 'gate_password',
    };

    if (!Hash::check($credentials['password'], $user->$passwordColumn)) {
        return back()->withErrors(['loginError' => 'Invalid credentials.'])->withInput();
    }

    // Staff verification
    if ($user->role === 'Staff') {
        $staff = Staffs::where('user_id', $user->user_id)->first();
        if (!$staff) {
            return back()->withErrors(['loginError' => 'Staff record not found.'])->withInput();
        }
        if (is_null($staff->email_verified_at)) {
            return back()->withErrors(['loginError' => 'Please verify your email before signing in.'])->withInput();
        }
        if (strtolower($staff->status) !== 'accepted') {
            return back()->withErrors(['loginError' => 'Your staff account is not active yet.'])->withInput();
        }
    }

    // Supplier or other account verification
    if ($user->role !== 'Staff') {
        $accountStatus = AccountStatus::where('user_id', $user->user_id)->first();
        if (!$accountStatus) {
            return back()->withErrors(['loginError' => 'Account status not found.'])->withInput();
        }
        if (is_null($accountStatus->email_verified_at)) {
            return back()->withErrors(['loginError' => 'Please verify your email before signing in.'])->withInput();
        }
        if (strtolower($accountStatus->account_status) !== 'accepted') {
            return back()->withErrors(['loginError' => 'Your account is not active yet.'])->withInput();
        }
    }

    Auth::login($user, false);
    $request->session()->regenerate();

    // Redirect based on role
    return match ($user->role) {
        'Supplier' => redirect()->route('choose.accounts'),
        default => redirect()->route('dashboard.view'),
    };
}


}


