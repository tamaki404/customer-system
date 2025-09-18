<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Suppliers;
use App\Models\Documents;
use App\Models\Staffs;
use App\Models\User;
use App\Models\Logs;

class StaffsController extends Controller
{
        public function staffsList(Request $request)
        {
            $user = Auth::user();
            $staffs = Staffs::with('user')
                ->whereRelation('user', 'role', 'Staff')
                ->get();

            return view('staffs.list', [
                'user' => $user,
                'staffs' => $staffs,

            ]);
        }

        public function staffView($staff_id, Request $request)
        {
            $user = Auth::user();

            $staff = Staffs::where('staff_id', $staff_id)->first(); 
            $suppliers = Suppliers::where('staff_id', $staff->staff_id)->get(); 



            // Debug: Log staff and user relationship
            if ($staff) {
                \Log::info('Staff View Debug:', [
                    'staff_id' => $staff->staff_id,
                    'user_id' => $staff->user_id,
                    'has_user_relation' => $staff->user ? 'YES' : 'NO',
                    'user_email' => $staff->user ? $staff->user->email_address : 'NO USER',
                    'staff_name' => $staff->firstname . ' ' . $staff->lastname
                ]);
            }

            // OR: $staff = Staffs::find($staff_id);

            return view('staffs.staff', [
                'user' => $user,
                'staff' => $staff,
                'suppliers' => $suppliers,

            ]);
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
