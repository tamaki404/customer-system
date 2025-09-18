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

            // OR: $staff = Staffs::find($staff_id);

            return view('staffs.staff', [
                'user' => $user,
                'staff' => $staff,
                'suppliers' => $suppliers,

            ]);
        }

        public function modifyStaff(Request $request)
        {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'staff_id' => 'required|string|exists:staffs,staff_id',
                'lastname' => 'required|string|max:50',
                'firstname' => 'required|string|max:50',
                'middlename' => 'nullable|string|max:50',
                'mobile_no' => 'required|string|max:11',
                'telephone_no' => 'nullable|string|max:11',
                'email_address' => 'required|email|max:50',
                'password' => 'nullable|string|min:6|confirmed',
                'new_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            try {
                // Find the staff record
                $staff = Staffs::where('staff_id', $request->staff_id)->first();
                
                if (!$staff) {
                    return redirect()->back()
                        ->with('error', 'Staff not found.')
                        ->withInput();
                }

                // Get the associated user
                $user = $staff->user;
                
                if (!$user) {
                    return redirect()->back()
                        ->with('error', 'User account not found.')
                        ->withInput();
                }

                // Check if email is being changed and if it's already taken
                if ($request->email_address !== $user->email_address) {
                    $existingUser = User::where('email_address', $request->email_address)
                        ->where('user_id', '!=', $user->user_id)
                        ->first();
                    
                    if ($existingUser) {
                        return redirect()->back()
                            ->withErrors(['email_address' => 'The email address is already taken.'])
                            ->withInput();
                    }
                }

                // Update staff information
                $staff->update([
                    'lastname' => $request->lastname,
                    'firstname' => $request->firstname,
                    'middlename' => $request->middlename,
                    'mobile_no' => $request->mobile_no,
                    'telephone_no' => $request->telephone_no,
                    'action_at' => now(),
                    'action_by' => Auth::user()->user_id
                ]);

                // Prepare user update data
                $userUpdateData = [
                    'email_address' => $request->email_address
                ];

                // Handle password update if provided
                if ($request->filled('password')) {
                    $userUpdateData['password'] = Hash::make($request->password);
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
                }

                // Update user information
                $user->update($userUpdateData);

                return redirect()->back()
                    ->with('success', 'Staff profile updated successfully.');

            } catch (\Exception $e) {
                return redirect()->back()
                    ->with('error', 'An error occurred while updating the staff profile. Please try again.')
                    ->withInput();
            }
        }







}
