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
    // public function registerSupplier(Request $request)
    // {
    //     // Rate limiting for registration attempts
    //     $key = 'registration:' . $request->ip();
    //     if (RateLimiter::tooManyAttempts($key, 5)) {
    //         $seconds = RateLimiter::availableIn($key);
    //         return redirect()->back()->withErrors(['error' => "Too many registration attempts. Please try again in {$seconds} seconds."]);
    //     }
        
    //     RateLimiter::hit($key, 300); // 5 minutes

    //     // Additional security validation
    //     $this->validateSecurity($request);

    //     // Custom validation messages
    //     $messages = [
    //         'email_address.required' => 'Email address is required.',
    //         'email_address.email' => 'Please enter a valid email address.',
    //         'email_address.unique' => 'This email address is already registered.',
    //         'password.required' => 'Password is required.',
    //         'password.min' => 'Password must be at least 8 characters long.',
    //         'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
    //         'image.required' => 'Company image/logo is required.',
    //         'image.image' => 'Company logo must be an image file.',
    //         'image.mimes' => 'Company logo must be a JPG, JPEG, PNG, or WEBP file.',
    //         'image.max' => 'Company logo must be less than 2MB.',
    //         'company_name.required' => 'Company name is required.',
    //         'home_street.required' => 'Home street address is required.',
    //         'home_subdivision.required' => 'Home subdivision is required.',
    //         'home_barangay.required' => 'Home barangay is required.',
    //         'home_city.required' => 'Home city is required.',
    //         'office_street.required' => 'Office street address is required.',
    //         'office_subdivision.required' => 'Office subdivision is required.',
    //         'office_barangay.required' => 'Office barangay is required.',
    //         'office_city.required' => 'Office city is required.',
    //         'mobile_no.required' => 'Mobile number is required.',
    //         'telephone_no.required' => 'Telephone number is required.',
    //         'civil_status.required' => 'Civil status is required.',
    //         'citizenship.required' => 'Citizenship is required.',
    //         'payment_method.required' => 'Payment method is required.',
    //         'agreement.required' => 'You must agree to the terms and conditions.',
    //         'rep_last_name.required' => 'Representative last name is required.',
    //         'rep_first_name.required' => 'Representative first name is required.',
    //         'rep_relationship.required' => 'Representative relationship is required.',
    //         'rep_contact_no.required' => 'Representative contact number is required.',
    //         'signatory_last_name.required' => 'Signatory last name is required.',
    //         'signatory_first_name.required' => 'Signatory first name is required.',
    //         'signatory_relationship.required' => 'Signatory relationship is required.',
    //         'signatory_contact_no.required' => 'Signatory contact number is required.',
    //         'AOL.required' => 'Affidavit of Loss files are required.',
    //         'AOL.array' => 'Affidavit of Loss must be an array of files.',
    //         'AOL.min' => 'At least one Affidavit of Loss file is required.',
    //         'AOL.*.image' => 'Affidavit of Loss files must be images.',
    //         'AOL.*.mimes' => 'Affidavit of Loss files must be JPG, JPEG, PNG, or WEBP.',
    //         'AOL.*.max' => 'Each Affidavit of Loss file must be less than 2MB.',
    //         'COR.required' => 'Certificate of Registration files are required.',
    //         'COR.array' => 'Certificate of Registration must be an array of files.',
    //         'COR.min' => 'At least one Certificate of Registration file is required.',
    //         'COR.*.image' => 'Certificate of Registration files must be images.',
    //         'COR.*.mimes' => 'Certificate of Registration files must be JPG, JPEG, PNG, or WEBP.',
    //         'COR.*.max' => 'Each Certificate of Registration file must be less than 2MB.',
    //         'BC.required' => 'Barangay Clearance files are required.',
    //         'BC.array' => 'Barangay Clearance must be an array of files.',
    //         'BC.min' => 'At least one Barangay Clearance file is required.',
    //         'BC.*.image' => 'Barangay Clearance files must be images.',
    //         'BC.*.mimes' => 'Barangay Clearance files must be JPG, JPEG, PNG, or WEBP.',
    //         'BC.*.max' => 'Each Barangay Clearance file must be less than 2MB.',
    //         'BP.required' => 'Business Permit files are required.',
    //         'BP.array' => 'Business Permit must be an array of files.',
    //         'BP.min' => 'At least one Business Permit file is required.',
    //         'BP.*.image' => 'Business Permit files must be images.',
    //         'BP.*.mimes' => 'Business Permit files must be JPG, JPEG, PNG, or WEBP.',
    //         'BP.*.max' => 'Each Business Permit file must be less than 2MB.',
    //         'SP.required' => 'Sanitary Permit files are required.',
    //         'SP.array' => 'Sanitary Permit must be an array of files.',
    //         'SP.min' => 'At least one Sanitary Permit file is required.',
    //         'SP.*.image' => 'Sanitary Permit files must be images.',
    //         'SP.*.mimes' => 'Sanitary Permit files must be JPG, JPEG, PNG, or WEBP.',
    //         'SP.*.max' => 'Each Sanitary Permit file must be less than 2MB.',
    //         'EMP.required' => 'Environmental Management Permit files are required.',
    //         'EMP.array' => 'Environmental Management Permit must be an array of files.',
    //         'EMP.min' => 'At least one Environmental Management Permit file is required.',
    //         'EMP.*.image' => 'Environmental Management Permit files must be images.',
    //         'EMP.*.mimes' => 'Environmental Management Permit files must be JPG, JPEG, PNG, or WEBP.',
    //         'EMP.*.max' => 'Each Environmental Management Permit file must be less than 2MB.',
    //         'CTC.required' => 'Community Tax Certificate files are required.',
    //         'CTC.array' => 'Community Tax Certificate must be an array of files.',
    //         'CTC.min' => 'At least one Community Tax Certificate file is required.',
    //         'CTC.*.image' => 'Community Tax Certificate files must be images.',
    //         'CTC.*.mimes' => 'Community Tax Certificate files must be JPG, JPEG, PNG, or WEBP.',
    //         'CTC.*.max' => 'Each Community Tax Certificate file must be less than 2MB.',
    //         'PR.required' => 'Product Requirements files are required.',
    //         'PR.array' => 'Product Requirements must be an array of files.',
    //         'PR.min' => 'At least one Product Requirements file is required.',
    //         'PR.*.image' => 'Product Requirements files must be images.',
    //         'PR.*.mimes' => 'Product Requirements files must be JPG, JPEG, PNG, or WEBP.',
    //         'PR.*.max' => 'Each Product Requirements file must be less than 2MB.',
    //     ];

    //     $request->validate([
    //         // User
    //         'email_address'   => 'required|email|unique:users,email_address',
    //         'password'        => 'required|string|confirmed|min:6',
    //         'image'           => 'required|image|mimes:jpg,jpeg,png,webp|max:2048', // 2MB limit

    //         // Supplier
    //         'company_name'    => 'required|string|max:255',
    //         'home_street'     => 'required|string|max:255',
    //         'home_subdivision'=> 'required|string|max:255',
    //         'home_barangay'   => 'required|string|max:255',
    //         'home_city'       => 'required|string|max:100',
    //         'office_street'   => 'required|string|max:255',
    //         'office_subdivision'=> 'required|string|max:255',
    //         'office_barangay' => 'required|string|max:255',
    //         'office_city'     => 'required|string|max:100',
    //         'mobile_no'       => 'required|string|max:15',
    //         'telephone_no'    => 'required|string|max:15',
    //         'civil_status'    => 'required|string',
    //         'citizenship'     => 'required|string',
    //         'payment_method'  => 'required|string',
    //         'salesman_relationship' => 'nullable|string',
    //         'weekly_volume'   => 'nullable|string',
    //         'date_required'   => 'nullable|date',
    //         'agreement'       => 'required|accepted',

    //         // Optional fields validation
    //         'birthdate'       => 'nullable|date',
    //         'valid_id_no'     => 'nullable|string|max:255',
    //         'id_type'         => 'nullable|string',
    //         'other_products_interest' => 'nullable|string|max:255',
    //         'referred_by'     => 'nullable|string|max:255',

    //         // Representatives (make required since form shows required)
    //         'rep_last_name'   => 'required|string|max:50',
    //         'rep_first_name'  => 'required|string|max:50',
    //         'rep_middle_name' => 'nullable|string|max:50',
    //         'rep_relationship'=> 'required|string|max:50',
    //         'rep_contact_no'  => 'required|string|max:15',

    //         // Signatories (make required since form shows required)
    //         'signatory_last_name'   => 'required|string|max:50',
    //         'signatory_first_name'  => 'required|string|max:50',
    //         'signatory_middle_name' => 'nullable|string|max:50',
    //         'signatory_relationship'=> 'required|string|max:50',
    //         'signatory_contact_no'  => 'required|string|max:15',

    //         // Bank details (optional)
    //         'account_name'    => 'nullable|string|max:255',
    //         'bank'            => 'nullable|string|max:255',
    //         'branch'          => 'nullable|string|max:255',
    //         'account_number'  => 'nullable|string|max:255',

    //         // File uploads - make required as per form
    //         'AOL'             => 'required|array|min:1',
    //         'AOL.*'           => 'image|mimes:jpg,jpeg,png,webp|max:2048',
    //         'COR'             => 'required|array|min:1',
    //         'COR.*'           => 'image|mimes:jpg,jpeg,png,webp|max:2048',
    //         'BC'              => 'required|array|min:1',
    //         'BC.*'            => 'image|mimes:jpg,jpeg,png,webp|max:2048',
    //         'BP'              => 'required|array|min:1',
    //         'BP.*'            => 'image|mimes:jpg,jpeg,png,webp|max:2048',
    //         'SP'              => 'required|array|min:1',
    //         'SP.*'            => 'image|mimes:jpg,jpeg,png,webp|max:2048',
    //         'EMP'             => 'required|array|min:1',
    //         'EMP.*'           => 'image|mimes:jpg,jpeg,png,webp|max:2048',
    //         'CTC'             => 'required|array|min:1',
    //         'CTC.*'           => 'image|mimes:jpg,jpeg,png,webp|max:2048',
    //         'PR'              => 'required|array|min:1',
    //         'PR.*'            => 'image|mimes:jpg,jpeg,png,webp|max:2048',
    //     ], $messages);

    //     DB::beginTransaction();

    //     $date = date('Ymd');

    //                 function randomBase36String(int $length): string {
    //                     $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    //                     $str = '';
    //                     for ($i = 0; $i < $length; $i++) {
    //                         $str .= $chars[random_int(0, strlen($chars) - 1)];
    //                     }
    //                     return $str;
    //                 }
    //     $user_id = 'USR-' . $date . '-' . randomBase36String(5);
    //     $supplier_id = 'SUP-' . $date . '-' . randomBase36String(5);
    //     $status_id = 'STATUS-' . $date . '-' . randomBase36String(5);

    //     try {
    //         // Handle company logo upload as BLOB with metadata
    //         $imageBlob = null;
    //         $imageMimeType = null;
    //         $imageFilename = null;
    //         $imageSize = null;
            
    //         if ($request->hasFile('image')) {
    //             $image = $request->file('image');
    //             $imageBlob = file_get_contents($image->getRealPath());
    //             $imageMimeType = $image->getMimeType();
    //             $imageFilename = $image->getClientOriginalName();
    //             $imageSize = $image->getSize();
    //         }

    //         // 1. Create User
    //         $user = User::create([
    //             'user_id'       => $user_id,
    //             'email_address' => $request->email_address,
    //             'password' => $request->password,
    //             'role'          => 'Supplier',
    //             'role_type'     => 'supplier',
    //             'image'         => $imageBlob,
    //             'image_mime_type' => $imageMimeType,
    //             'image_filename' => $imageFilename,
    //             'image_size'    => $imageSize,
    //         ]);

    //             AccountStatus::create([
    //                 'supplier_id'       => $supplier_id,
    //                 'status_id'         => $status_id,
    //                 'acc_status'        => $request->acc_status === 'Pending',
    //                 'reason_to_decline' => $request->acc_status === 'Declined' ? $request->reason_to_decline : null,
    //                 'staff_id'          => $request->staff_id  ? : null,
    //             ]);



    //         // 2. Create Supplier
    //         $supplier = Suppliers::create([
    //             'user_id'       => $user_id,
    //             'supplier_id'   => $supplier_id,
    //             'company_name'  => $request->company_name,
    //             'home_street'   => $request->input('home_street'),
    //             'home_subdivision' => $request->input('home_subdivision'),
    //             'home_barangay' => $request->input('home_barangay'),
    //             'home_city'     => $request->input('home_city'),
    //             'office_street' => $request->input('office_street'),
    //             'office_subdivision' => $request->input('office_subdivision'),
    //             'office_barangay' => $request->input('office_barangay'),
    //             'office_city'   => $request->input('office_city'),
    //             'mobile_no'     => $request->mobile_no,
    //             'telephone_no'  => $request->telephone_no,
    //             'birthdate'     => $request->birthdate,
    //             'valid_id_no'   => $request->valid_id_no,
    //             'id_type'       => $request->id_type,
    //             'civil_status'  => $request->civil_status,
    //             'citizenship'   => $request->citizenship,
    //             'payment_method'=> $request->payment_method,
    //             'salesman_relationship' => $request->salesman_relationship,
    //             'weekly_volume' => $request->weekly_volume,
    //             'other_products_interest' => $request->other_products_interest,
    //             'date_required' => $request->date_required,
    //             'referred_by'   => $request->referred_by,
    //             'product_requirements' => null, 
    //             'agreement'     => true,
    //         ]);

    //         // 3. Authorized Representative
    //         Representatives::create([
    //             'supplier_id'       => $supplier->supplier_id,
    //             'rep_last_name'     => $request->rep_last_name,
    //             'rep_first_name'    => $request->rep_first_name,
    //             'rep_middle_name'   => $request->rep_middle_name,
    //             'rep_relationship'  => $request->rep_relationship,
    //             'rep_contact_no'    => $request->rep_contact_no,
    //         ]);

    //         // 4. Authorized Signatory
    //         Signatories::create([
    //             'supplier_id'           => $supplier->supplier_id,
    //             'signatory_last_name'   => $request->signatory_last_name,
    //             'signatory_first_name'  => $request->signatory_first_name,
    //             'signatory_middle_name' => $request->signatory_middle_name,
    //             'signatory_relationship'=> $request->signatory_relationship,
    //             'signatory_contact_no'  => $request->signatory_contact_no,
    //         ]);

    //         // 5. Bank Details
    //         Banks::create([
    //             'supplier_id'   => $supplier->supplier_id,
    //             'account_name'  => $request->account_name,
    //             'bank'          => $request->bank,
    //             'branch'        => $request->branch,
    //             'account_number'=> $request->account_number,
    //         ]);

    //         // 6. Upload Documents (loop each group)
    //         $docGroups = [
    //             'AOL' => 'Affidavit of Loss',
    //             'COR' => 'Certificate of Registration',
    //             'BC'  => 'Barangay Clearance',
    //             'BP'  => 'Business Permit',
    //             'SP'  => 'Sanitary Permit',
    //             'EMP' => 'Environmental Management Permit',
    //             'CTC' => 'Community Tax Certificate',
    //             'PR'  => 'Product Requirements',
    //         ];

    //         foreach ($docGroups as $key => $type) {
    //             if ($request->hasFile($key)) {
    //                 foreach ($request->file($key) as $file) {
    //                     Documents::create([
    //                         'supplier_id' => $supplier->supplier_id,
    //                         'type'        => $type,
    //                         'file_name'   => $file->getClientOriginalName(),
    //                         'file_mime'   => $file->getMimeType(),
    //                         'file_size'   => $file->getSize(),
    //                         'file'        => file_get_contents($file->getRealPath()), 
    //                     ]);
    //                 }
    //             }
    //         }

    //         // Create and send verification token
    //         $plainToken = Str::random(64);
    //         DB::table('email_verification_tokens')->insert([
    //             'user_id'    => $user->user_id,
    //             'email'      => $user->email_address,
    //             'token'      => hash('sha256', $plainToken),
    //             'expires_at' => now()->addDay(),
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ]);

    //         $verifyUrl = url('/email/verify?token=' . $plainToken . '&uid=' . urlencode($user->user_id));

    //         try {
    //             Mail::send('emails.verify', ['verifyUrl' => $verifyUrl], function($message) use ($user) {
    //                 $message->to($user->email_address)->subject('Verify your email address');
    //             });
    //         } catch (\Throwable $mailErr) {
    //             Log::error('Verification email send failed: ' . $mailErr->getMessage());
    //         }

    //         DB::commit(); 

    //         return redirect()->route('verification.notice')->with('success', 'Registration successful! Please check your email to verify your account before logging in.');

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Supplier registration error: ' . $e->getMessage());
    //         return redirect()->back()->withErrors(['error' => 'Registration failed. Please try again.'])->withInput();
    //     }
    //     }

  
public function registerSupplier(Request $request)
{
    $request->validate([
        // User
        'email_add'       => 'required|email|unique:users,email_address|max:255',
        'password'        => 'required|string|confirmed|min:6|max:255',
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
        'rep_lastname'    => 'required|string|max:50',
        'rep_firstname'   => 'required|string|max:50',
        'rep_middlename'  => 'nullable|string|max:50',
        'auth_position'   => 'required|string|max:50',
        'rep_contact'     => 'required|string|regex:/^09[0-9]{9}$/|size:11',

        // Signatories 
        'sign_lastname'   => 'required|string|max:50',
        'sign_firstname'  => 'required|string|max:50',
        'sign_middlename' => 'nullable|string|max:50',
        'sign_position'   => 'required|string|max:50',
        'e_image'         => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',

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
        'product_ids.*'   => 'required|exists:products,id',

        // Delivery Requirements
        'ppe_requirements'       => 'required|string|max:255',
        'delivery_frequency'     => 'required|string|max:255',
        'delivery_address_1'     => 'required|string|max:255',
        'delivery_address_2'     => 'nullable|string|max:255',
        'delivery_address_3'     => 'nullable|string|max:255',
        'delivery_instructions'  => 'nullable|string|max:255',
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
            $defaultImagePath = public_path('assets/default-company-logo.jpg');
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
        $eSignatureBinary = null;
        $eSignatureMime = null;
        $eSignatureName = null;
        $eSignatureSize = null;
        if ($request->hasFile('e_image')) {
            $eSignature = $request->file('e_image');
            $eSignatureBinary = file_get_contents($eSignature->getRealPath());
            $eSignatureMime = $eSignature->getMimeType();
            $eSignatureName = $eSignature->getClientOriginalName();
            $eSignatureSize = $eSignature->getSize();
        }

        // Create User
        $user = User::create([
            'user_id' => $user_id,
            'email_address' => $request->email_add,
            'password' => Hash::make($request->password),
            'role' => 'Supplier',
            'role_type' => 'Customer',
            'status' => 'Pending',
            'email_verified_at' => null,
            'image' => $companyImageBinary,
            'image_mime_type' => $companyImageMime,
            'image_filename' => $companyImageName,
            'image_size' => $companyImageSize,
        ]);

        $acc_status = AccountStatus::create([
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
        $representative = Representatives::create([
            'user_id' => $user_id,
            'supplier_id' => $supplier_id,

            'rep_lastname' => $request->rep_lastname,
            'rep_firstname' => $request->rep_firstname,
            'rep_middlename' => $request->rep_middlename,
            'auth_position' => $request->auth_position,
            'rep_contact' => $request->rep_contact,
            'is_primary' => true,
        ]);

        // Create Signatory
        $signatory = Signatories::create([
            'user_id' => $user_id,
            'supplier_id' => $supplier_id,

            'sign_lastname' => $request->sign_lastname,
            'sign_firstname' => $request->sign_firstname,
            'sign_middlename' => $request->sign_middlename,
            'sign_position' => $request->sign_position,
            'signature_image' => $eSignatureBinary,
            'signature_image_mime_type' => $eSignatureMime,
            'signature_image_filename' => $eSignatureName,
            'signature_image_size' => $eSignatureSize,
            'is_primary' => true,
        ]);

        // Create Bank Details (if provided)
        if ($request->filled('account_name') || $request->filled('bank')) {
            $bankDetails = Banks::create([
                'user_id' => $user_id,
                'supplier_id' => $supplier_id,

                'account_name' => $request->account_name,
                'bank_name' => $request->bank,
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
            $user = User::create([
                'user_id'       => $user_id,
                'email_address' => $request->email_address,
                'password' => $request->password,
                'role'          => 'Staff',
                'role_type'          => $request->role_type,
                'image'         => $imageBlob,
                'image_mime_type' => $imageMimeType,
                'image_filename' => $imageFilename,
                'image_size'    => $imageSize,

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

    public function signin(Request $request)
    {
        $key = 'login:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return redirect()->back()->withErrors(['loginError' => "Too many attempts. Try again in {$seconds} seconds."])->withInput();
        }
        RateLimiter::hit($key, 300);

        $credentials = $request->validate([
            'email_address' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        $user = User::where('email_address', $credentials['email_address'])->first();
        if (!$user) {
            return redirect()->back()->withErrors(['loginError' => 'Invalid credentials.'])->withInput();
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            return redirect()->back()->withErrors(['loginError' => 'Invalid credentials.'])->withInput();
        }

        $accountStatus = AccountStatus::where('user_id', $user->user_id)->first();

        if (!$accountStatus) {
            return redirect()->route('signin')->with('error', 'Account status not found. Please contact support.');
        }

        if (is_null($accountStatus->email_verified_at)) {
            return redirect()->route('signin')->with('error', 'Please verify your email before signing in.');
        }

        if (strtolower($accountStatus->account_status) !== 'accepted') {
            return redirect()->route('signin')->with('error', 'Your account is not active yet. Kidly wait for a verification.');
        }

        Auth::login($user, false);
        $request->session()->regenerate();

        return redirect()->route('dashboard.view');
    }


    public function verifyEmail(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'uid'   => 'required',
        ]);

        $hashedToken = hash('sha256', $request->query('token'));
        $userId = $request->query('uid');

        $record = DB::table('email_verification_tokens')
            ->where('user_id', $userId)
            ->where('token', $hashedToken)
            ->first();

        if (!$record) {
            return redirect()->route('signin')->with('error', 'Invalid or expired verification link.');
        }

        if ($record->expires_at && now()->greaterThan($record->expires_at)) {
            return redirect()->route('signin')->with('error', 'Verification link has expired.');
        }

        // Mark verified
        $user = User::where('user_id', $userId)->first();
        if (!$user) {
            return redirect()->route('signin')->with('error', 'User not found.');
        }

        // Mark supplier email_verified_at too if exists
        DB::transaction(function() use ($user, $userId) {
 
            DB::table('account_status')->where('user_id', $userId)->update([
                'email_verified_at' => now(),
            ]);

            // Remove used tokens
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



}


