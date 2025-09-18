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
            $suppliers = Suppliers::where('supplier_id', $staff->supplier_id)->get(); 

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

                // Debug: Log current data before update
                \Log::info('Current Staff Data:', [
                    'staff_id' => $staff->staff_id,
                    'lastname' => $staff->lastname,
                    'firstname' => $staff->firstname,
                    'middlename' => $staff->middlename,
                    'mobile_no' => $staff->mobile_no,
                    'telephone_no' => $staff->telephone_no
                ]);

                \Log::info('Current User Data:', [
                    'user_id' => $user->user_id,
                    'email_address' => $user->email_address
                ]);

                // Check if email is being changed and if it's already taken
                if ($request->email_address !== $user->email_address) {
                    $existingUser = User::where('email_address', $request->email_address)
                        ->where('user_id', '!=', $user->user_id)
                        ->first();
                    
                    if ($existingUser) {
                        \Log::warning('Email already taken: ' . $request->email_address);
                        return redirect()->back()
                            ->withErrors(['email_address' => 'The email address is already taken.'])
                            ->withInput();
                    }
                }

                // Update staff information
                $staffUpdateData = [
                    'lastname' => $request->lastname,
                    'firstname' => $request->firstname,
                    'middlename' => $request->middlename,
                    'mobile_no' => $request->mobile_no,
                    'telephone_no' => $request->telephone_no,
                    // 'action_at' => now(),
                    'action_by' => Auth::user()->user_id
                ];

                \Log::info('Updating Staff with data:', $staffUpdateData);
                $staffUpdated = $staff->update($staffUpdateData);
                \Log::info('Staff update result: ' . ($staffUpdated ? 'SUCCESS' : 'FAILED'));

                // Prepare user update data
                $userUpdateData = [
                    'email_address' => $request->email_address
                ];

                // Handle password update if provided
                if ($request->filled('password')) {
                    $userUpdateData['password'] = Hash::make($request->password);
                    \Log::info('Password will be updated');
                }

                // Handle image upload
                if ($request->hasFile('new_image')) {
                    $image = $request->file('new_image');
                    $imageData = file_get_contents($image->getRealPath());
                    $mimeType = $image->getMimeType();
                    $filename = $image->getClientOriginalName();
                    $size = $image->getSize();

                    $userUpdateData['image'] = $imageData;
                    $userUpdateData['image_mime_type'] = $mimeType;
                    $userUpdateData['image_filename'] = $filename;
                    $userUpdateData['image_size'] = $size;
                    \Log::info('Image will be updated: ' . $filename);
                }

                \Log::info('Updating User with data:', $userUpdateData);
                $userUpdated = $user->update($userUpdateData);
                \Log::info('User update result: ' . ($userUpdated ? 'SUCCESS' : 'FAILED'));

                // Verify the updates by fetching fresh data
                $staff->refresh();
                $user->refresh();
                
                \Log::info('After update - Staff Data:', [
                    'lastname' => $staff->lastname,
                    'firstname' => $staff->firstname,
                    'middlename' => $staff->middlename,
                    'mobile_no' => $staff->mobile_no,
                    'telephone_no' => $staff->telephone_no
                ]);

                \Log::info('After update - User Data:', [
                    'email_address' => $user->email_address
                ]);

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
