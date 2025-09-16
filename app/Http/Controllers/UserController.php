<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Suppliers;
use App\Models\Representatives;
use App\Models\Signatories;
use App\Models\Banks;
use App\Models\Documents;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{

    public function registerSupplier(Request $request)
    {
        // Rate limiting for registration attempts
        $key = 'registration:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return redirect()->back()->withErrors(['error' => "Too many registration attempts. Please try again in {$seconds} seconds."]);
        }
        
        RateLimiter::hit($key, 300); // 5 minutes

        // Additional security validation
        $this->validateSecurity($request);

        // Custom validation messages
        $messages = [
            'email_address.required' => 'Email address is required.',
            'email_address.email' => 'Please enter a valid email address.',
            'email_address.unique' => 'This email address is already registered.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
            'image.required' => 'Company image/logo is required.',
            'image.image' => 'Company logo must be an image file.',
            'image.mimes' => 'Company logo must be a JPG, JPEG, PNG, or WEBP file.',
            'image.max' => 'Company logo must be less than 2MB.',
            'company_name.required' => 'Company name is required.',
            'home_street.required' => 'Home street address is required.',
            'home_subdivision.required' => 'Home subdivision is required.',
            'home_barangay.required' => 'Home barangay is required.',
            'home_city.required' => 'Home city is required.',
            'office_street.required' => 'Office street address is required.',
            'office_subdivision.required' => 'Office subdivision is required.',
            'office_barangay.required' => 'Office barangay is required.',
            'office_city.required' => 'Office city is required.',
            'mobile_no.required' => 'Mobile number is required.',
            'telephone_no.required' => 'Telephone number is required.',
            'civil_status.required' => 'Civil status is required.',
            'citizenship.required' => 'Citizenship is required.',
            'payment_method.required' => 'Payment method is required.',
            'salesman_relationship.required' => 'Salesman relationship is required.',
            'weekly_volume.required' => 'Weekly volume is required.',
            'date_required.required' => 'Date required is required.',
            'agreement.required' => 'You must agree to the terms and conditions.',
            'rep_last_name.required' => 'Representative last name is required.',
            'rep_first_name.required' => 'Representative first name is required.',
            'rep_relationship.required' => 'Representative relationship is required.',
            'rep_contact_no.required' => 'Representative contact number is required.',
            'signatory_last_name.required' => 'Signatory last name is required.',
            'signatory_first_name.required' => 'Signatory first name is required.',
            'signatory_relationship.required' => 'Signatory relationship is required.',
            'signatory_contact_no.required' => 'Signatory contact number is required.',
            'AOL.required' => 'Affidavit of Loss files are required.',
            'AOL.array' => 'Affidavit of Loss must be an array of files.',
            'AOL.min' => 'At least one Affidavit of Loss file is required.',
            'AOL.*.image' => 'Affidavit of Loss files must be images.',
            'AOL.*.mimes' => 'Affidavit of Loss files must be JPG, JPEG, PNG, or WEBP.',
            'AOL.*.max' => 'Each Affidavit of Loss file must be less than 2MB.',
            'COR.required' => 'Certificate of Registration files are required.',
            'COR.array' => 'Certificate of Registration must be an array of files.',
            'COR.min' => 'At least one Certificate of Registration file is required.',
            'COR.*.image' => 'Certificate of Registration files must be images.',
            'COR.*.mimes' => 'Certificate of Registration files must be JPG, JPEG, PNG, or WEBP.',
            'COR.*.max' => 'Each Certificate of Registration file must be less than 2MB.',
            'BC.required' => 'Barangay Clearance files are required.',
            'BC.array' => 'Barangay Clearance must be an array of files.',
            'BC.min' => 'At least one Barangay Clearance file is required.',
            'BC.*.image' => 'Barangay Clearance files must be images.',
            'BC.*.mimes' => 'Barangay Clearance files must be JPG, JPEG, PNG, or WEBP.',
            'BC.*.max' => 'Each Barangay Clearance file must be less than 2MB.',
            'BP.required' => 'Business Permit files are required.',
            'BP.array' => 'Business Permit must be an array of files.',
            'BP.min' => 'At least one Business Permit file is required.',
            'BP.*.image' => 'Business Permit files must be images.',
            'BP.*.mimes' => 'Business Permit files must be JPG, JPEG, PNG, or WEBP.',
            'BP.*.max' => 'Each Business Permit file must be less than 2MB.',
            'SP.required' => 'Sanitary Permit files are required.',
            'SP.array' => 'Sanitary Permit must be an array of files.',
            'SP.min' => 'At least one Sanitary Permit file is required.',
            'SP.*.image' => 'Sanitary Permit files must be images.',
            'SP.*.mimes' => 'Sanitary Permit files must be JPG, JPEG, PNG, or WEBP.',
            'SP.*.max' => 'Each Sanitary Permit file must be less than 2MB.',
            'EMP.required' => 'Environmental Management Permit files are required.',
            'EMP.array' => 'Environmental Management Permit must be an array of files.',
            'EMP.min' => 'At least one Environmental Management Permit file is required.',
            'EMP.*.image' => 'Environmental Management Permit files must be images.',
            'EMP.*.mimes' => 'Environmental Management Permit files must be JPG, JPEG, PNG, or WEBP.',
            'EMP.*.max' => 'Each Environmental Management Permit file must be less than 2MB.',
            'CTC.required' => 'Community Tax Certificate files are required.',
            'CTC.array' => 'Community Tax Certificate must be an array of files.',
            'CTC.min' => 'At least one Community Tax Certificate file is required.',
            'CTC.*.image' => 'Community Tax Certificate files must be images.',
            'CTC.*.mimes' => 'Community Tax Certificate files must be JPG, JPEG, PNG, or WEBP.',
            'CTC.*.max' => 'Each Community Tax Certificate file must be less than 2MB.',
            'PR.required' => 'Product Requirements files are required.',
            'PR.array' => 'Product Requirements must be an array of files.',
            'PR.min' => 'At least one Product Requirements file is required.',
            'PR.*.image' => 'Product Requirements files must be images.',
            'PR.*.mimes' => 'Product Requirements files must be JPG, JPEG, PNG, or WEBP.',
            'PR.*.max' => 'Each Product Requirements file must be less than 2MB.',
        ];

        $request->validate([
            // User
            'email_address'   => 'required|email|unique:users,email_address',
            'password'        => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
            'image'           => 'required|image|mimes:jpg,jpeg,png,webp|max:2048', // 2MB limit

            // Supplier
            'company_name'    => 'required|string|max:255',
            'home_street'     => 'required|string|max:255',
            'home_subdivision'=> 'required|string|max:255',
            'home_barangay'   => 'required|string|max:255',
            'home_city'       => 'required|string|max:100',
            'office_street'   => 'required|string|max:255',
            'office_subdivision'=> 'required|string|max:255',
            'office_barangay' => 'required|string|max:255',
            'office_city'     => 'required|string|max:100',
            'mobile_no'       => 'required|string|max:15',
            'telephone_no'    => 'required|string|max:15',
            'civil_status'    => 'required|string',
            'citizenship'     => 'required|string',
            'payment_method'  => 'required|string',
            'salesman_relationship' => 'required|string',
            'weekly_volume'   => 'required|string',
            'date_required'   => 'required|date',
            'agreement'       => 'required|accepted',

            // Optional fields validation
            'birthdate'       => 'nullable|date',
            'valid_id_no'     => 'nullable|string|max:255',
            'id_type'         => 'nullable|string',
            'other_products_interest' => 'nullable|string|max:255',
            'referred_by'     => 'nullable|string|max:255',

            // Representatives (make required since form shows required)
            'rep_last_name'   => 'required|string|max:50',
            'rep_first_name'  => 'required|string|max:50',
            'rep_middle_name' => 'nullable|string|max:50',
            'rep_relationship'=> 'required|string|max:50',
            'rep_contact_no'  => 'required|string|max:15',

            // Signatories (make required since form shows required)
            'signatory_last_name'   => 'required|string|max:50',
            'signatory_first_name'  => 'required|string|max:50',
            'signatory_middle_name' => 'nullable|string|max:50',
            'signatory_relationship'=> 'required|string|max:50',
            'signatory_contact_no'  => 'required|string|max:15',

            // Bank details (optional)
            'account_name'    => 'nullable|string|max:255',
            'bank'            => 'nullable|string|max:255',
            'branch'          => 'nullable|string|max:255',
            'account_number'  => 'nullable|string|max:255',

            // File uploads - make required as per form
            'AOL'             => 'required|array|min:1',
            'AOL.*'           => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'COR'             => 'required|array|min:1',
            'COR.*'           => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'BC'              => 'required|array|min:1',
            'BC.*'            => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'BP'              => 'required|array|min:1',
            'BP.*'            => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'SP'              => 'required|array|min:1',
            'SP.*'            => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'EMP'             => 'required|array|min:1',
            'EMP.*'           => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'CTC'             => 'required|array|min:1',
            'CTC.*'           => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'PR'              => 'required|array|min:1',
            'PR.*'            => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ], $messages);

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

        $user_id = 'USR-' . $date . '-' . randomBase36String(5);
        $supplier_id = 'SUP-' . $date . '-' . randomBase36String(5);

        try {
            // Handle company logo upload as BLOB with metadata
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
                'role'          => 'Supplier',
                'image'         => $imageBlob,
                'image_mime_type' => $imageMimeType,
                'image_filename' => $imageFilename,
                'image_size'    => $imageSize,
            ]);

            // 2. Create Supplier
            $supplier = Suppliers::create([
                'user_id'       => $user_id,
                'supplier_id'   => $supplier_id,
                'company_name'  => $request->company_name,
                'home_street'   => $request->input('home_street'),
                'home_subdivision' => $request->input('home_subdivision'),
                'home_barangay' => $request->input('home_barangay'),
                'home_city'     => $request->input('home_city'),
                'office_street' => $request->input('office_street'),
                'office_subdivision' => $request->input('office_subdivision'),
                'office_barangay' => $request->input('office_barangay'),
                'office_city'   => $request->input('office_city'),
                'mobile_no'     => $request->mobile_no,
                'telephone_no'  => $request->telephone_no,
                'birthdate'     => $request->birthdate,
                'valid_id_no'   => $request->valid_id_no,
                'id_type'       => $request->id_type,
                'civil_status'  => $request->civil_status,
                'citizenship'   => $request->citizenship,
                'payment_method'=> $request->payment_method,
                'salesman_relationship' => $request->salesman_relationship,
                'weekly_volume' => $request->weekly_volume,
                'other_products_interest' => $request->other_products_interest,
                'date_required' => $request->date_required,
                'referred_by'   => $request->referred_by,
                'product_requirements' => null, 
                'agreement'     => true,
            ]);

            // 3. Authorized Representative
            Representatives::create([
                'supplier_id'       => $supplier->supplier_id,
                'rep_last_name'     => $request->rep_last_name,
                'rep_first_name'    => $request->rep_first_name,
                'rep_middle_name'   => $request->rep_middle_name,
                'rep_relationship'  => $request->rep_relationship,
                'rep_contact_no'    => $request->rep_contact_no,
            ]);

            // 4. Authorized Signatory
            Signatories::create([
                'supplier_id'           => $supplier->supplier_id,
                'signatory_last_name'   => $request->signatory_last_name,
                'signatory_first_name'  => $request->signatory_first_name,
                'signatory_middle_name' => $request->signatory_middle_name,
                'signatory_relationship'=> $request->signatory_relationship,
                'signatory_contact_no'  => $request->signatory_contact_no,
            ]);

            // 5. Bank Details
            Banks::create([
                'supplier_id'   => $supplier->supplier_id,
                'account_name'  => $request->account_name,
                'bank'          => $request->bank,
                'branch'        => $request->branch,
                'account_number'=> $request->account_number,
            ]);

            // 6. Upload Documents (loop each group)
            $docGroups = [
                'AOL' => 'Affidavit of Loss',
                'COR' => 'Certificate of Registration',
                'BC'  => 'Barangay Clearance',
                'BP'  => 'Business Permit',
                'SP'  => 'Sanitary Permit',
                'EMP' => 'Environmental Management Permit',
                'CTC' => 'Community Tax Certificate',
                'PR'  => 'Product Requirements',
            ];

            foreach ($docGroups as $key => $type) {
                if ($request->hasFile($key)) {
                    foreach ($request->file($key) as $file) {
                        Documents::create([
                            'supplier_id' => $supplier->supplier_id,
                            'type'        => $type,
                            'file_name'   => $file->getClientOriginalName(),
                            'file_mime'   => $file->getMimeType(),
                            'file_size'   => $file->getSize(),
                            'file'        => file_get_contents($file->getRealPath()), 
                        ]);
                    }
                }
            }

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
                \Log::error('Verification email send failed: ' . $mailErr->getMessage());
            }

            DB::commit();

            return redirect()->route('verification.notice')->with('success', 'Registration successful! Please check your email to verify your account before logging in.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Supplier registration error: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Registration failed. Please try again.'])->withInput();
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
            return redirect()->route('verification.notice')->with('error', 'Invalid or expired verification link.');
        }

        if ($record->expires_at && now()->greaterThan($record->expires_at)) {
            return redirect()->route('verification.notice')->with('error', 'Verification link has expired.');
        }

        // Mark verified
        $user = User::where('user_id', $userId)->first();
        if (!$user) {
            return redirect()->route('verification.notice')->with('error', 'User not found.');
        }

        // Mark supplier email_verified_at too if exists
        DB::transaction(function() use ($user, $userId) {
            // If you want to track on users: add email_verified_at/status
            // Here we update suppliers.email_verified_at since schema has it
            DB::table('suppliers')->where('user_id', $userId)->update([
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


