<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AccountStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Customers;
use App\Models\Staffs;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;


class WorkerController extends Controller
{
    public function list(Request $request)
    {
        $user = Auth::user();
        $staffs = Staffs::orderBy('created_at', 'desc')->get();
        foreach ($staffs as $staff) {
            $staff->contactNo = Customers::where('staff_id', $staff->staff_id)->count();
        }
        return view('franken.stff.list', [
            'user' => $user,
            'staffs' => $staffs,
        ]);
    }
    public function staff($staff_id, Request $request)
    {
        $user = Auth::user();
        $staff = Staffs::where('staff_id', $staff_id)->first(); 
        $customers = AccountStatus::where('staff_id', $staff->staff_id)->orderBy('updated_at', 'desc')->get(); 

        return view('franken.stff.staff', [
            'user' => $user,
            'staff' => $staff,
            'customers' => $customers,
        ]);
    }

    public function register(Request $request){

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
                'role_type'  => 'required|string|in:sales_representative,procurement_officer,warehouse_staff,accounting_staff,system_administrator,inventory_staff',
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

            return redirect()->route('stff.list')
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
        public function modifyStaff(Request $request)
        {
            // Debug: Log the incoming request data
            \Log::info('Staff Modify Request Data:', $request->all());

            // Validate the request
            $validator = Validator::make($request->all(), [
                'staff_id' => 'required|string|exists:staffs,staff_id',
                'lastname' => 'required|string|max:50',
                'firstname' => 'required|string|max:50',
                'middlename' => 'nullable|string|max:50',
                'mobile_no' => 'required|string|max:11',
                'telephone_no' => 'nullable|string|max:11',
                'email_address' => 'required|email|max:50',
                'password' => [
                    'nullable',
                    'string',
                    'min:6',
                    'regex:/^(?=.*[0-9])(?=.*[!@#$%^&*(),.?":{}|<>]).*$/'
                ],
                'password_confirmation' => 'required_with:password|same:password',
                'new_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ], [
                'password.min' => 'Password must be at least 6 characters long.',
                'password.regex' => 'Password must contain at least one number and one special character.',
                'password_confirmation.required_with' => 'Password confirmation is required when password is provided.',
                'password_confirmation.same' => 'Password confirmation does not match password.',
            ]);

            if ($validator->fails()) {
                \Log::error('Validation failed:', $validator->errors()->toArray());
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            try {
                // Find the staff record
                $staff = Staffs::where('staff_id', $request->staff_id)->first();

                if (!$staff) {
                    \Log::error('Staff not found with ID: ' . $request->staff_id);
                    return redirect()->back()
                        ->with('error', 'Staff not found.')
                        ->withInput();
                }

                // Get the associated user
                $user = $staff->user;

                if (!$user) {
                    \Log::error('User account not found for staff ID: ' . $request->staff_id);
                    return redirect()->back()
                        ->with('error', 'User account not found.')
                        ->withInput();
                }

                // Capture the original attributes
                $originalStaff = $staff->getOriginal();
                $originalUser  = $user->getOriginal();

                // Update staff data
                $staffUpdateData = [
                    'lastname'     => $request->lastname,
                    'firstname'    => $request->firstname,
                    'middlename'   => $request->middlename,
                    'mobile_no'    => $request->mobile_no,
                    'telephone_no' => $request->telephone_no,
                    'action_by'    => Auth::user()->user_id
                ];

                // Detect staff changes
                $staffChanges = [];
                foreach ($staffUpdateData as $key => $newValue) {
                    $oldValue = $originalStaff[$key] ?? null;
                    if ($oldValue != $newValue) {
                        $staffChanges[] = "Changed {$key} from '{$oldValue}' to '{$newValue}'";
                    }
                }

                $staffUpdated = $staff->update($staffUpdateData);

                // Update user data
                $userUpdateData = [
                    'email_address' => $request->email_address
                ];

                if ($request->filled('password')) {
                    $userUpdateData['password'] = Hash::make($request->password);
                }

                if ($request->hasFile('new_image')) {
                    $image = $request->file('new_image');
                    $userUpdateData['image']            = file_get_contents($image->getRealPath());
                    $userUpdateData['image_mime_type']  = $image->getMimeType();
                    $userUpdateData['image_filename']   = $image->getClientOriginalName();
                    $userUpdateData['image_size']       = $image->getSize();
                }

                // Detect user changes
                $userChanges = [];
                foreach ($userUpdateData as $key => $newValue) {
                    $oldValue = $originalUser[$key] ?? null;

                    if ($key === 'password' && $request->filled('password')) {
                        $userChanges[] = "Updated password";
                    } elseif (in_array($key, ['image', 'image_mime_type', 'image_filename', 'image_size']) && $request->hasFile('new_image')) {
                        $userChanges[] = "Updated profile image";
                    } elseif ($oldValue != $newValue) {
                        $userChanges[] = "Changed {$key} from '{$oldValue}' to '{$newValue}'";
                    }
                }

                $userUpdated = $user->update($userUpdateData);

                    $date = date('Ymd');
                    function randomBase36String(int $length): string {
                        $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        $str = '';
                        for ($i = 0; $i < $length; $i++) {
                            $str .= $chars[random_int(0, strlen($chars) - 1)];
                        }
                        return $str;
                    }

                    $log_id = 'LOG-' . $date . '-' . randomBase36String(5);


                // Create audit logs if there were changes
                if (!empty($staffChanges)) {
                    Logs::create([
                        'user_id'     => Auth::user()->user_id,
                        'action'      => 'Updated Staff Account',
                        'log_id'      => $log_id,
                        'description' => implode("; ", $staffChanges) . " for staff_id {$staff->staff_id}",

                    ]);
                    \Log::info("Staff changes for staff_id {$staff->staff_id}", $staffChanges);
                }

                if (!empty($userChanges)) {
                    Logs::create([
                        'user_id'     => Auth::user()->user_id,
                        'action'      => 'Updated User Account',
                        'description' => implode("; ", $userChanges),
                    ]);
                    \Log::info("User changes for user_id {$user->user_id}", $userChanges);
                }

                if (!$staffUpdated || !$userUpdated) {
                    return redirect()->back()
                        ->with('error', 'Failed to update staff profile. Please check the logs for details.')
                        ->withInput();
                }

                return redirect()->back()
                    ->with('success', 'Staff profile updated successfully.');

            } catch (\Exception $e) {
                \Log::error('Exception in modifyStaff: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                    'request_data' => $request->all()
                ]);

                return redirect()->back()
                    ->with('error', 'An error occurred while updating the staff profile: ' . $e->getMessage())
                    ->withInput();
            }
        }
}
