@extends('layout')
@section('content')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/staff_view.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fadein.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Staff Details</title>
</head>
<body>
    <script src="{{ asset('js/fadein.js') }}"></script>
    
        <!-- confirmation modal -->
    <div class="modal fade" id="confirmModal" style="display: none;"  tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"  style="justify-self: center; align-self: center; ">
            <div class="modal-content" style="border-top: 4px solid #ffde59;">
                <div class="modal-header">
                    <h5 class="modal-title" style="padding: 0; margin: 0;">Confirm action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body" style="border: none; font-size: 14px;">
                    Are you sure you want to commit changes?
                </div>

                <div class="modal-footer" style="padding: 5px">
                    <button type="button" id="cancelBtn" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmSaveBtn" class="btn" style="background: #ffde59; font-weight: bold; font-size: 14px;">Confirm</button>
                </div>
            </div>
        </div>
    </div>


    <div class="staffFrame">

        @if(session('success'))
            <div class="alert alert-success" style="background: #d4edda; color: #155724; font-weight: normal; position: absolute; z-index: 100; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger" style="background: #f8d7da; font-weight: normal; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                {{ session('error') }}
            </div>
        @endif

         <a class="go-back-a" href="/staffs" ><- Staffs</a>
            <style>
                .go-back-a{
                    font-size: 15px;
                    color: #f8912a;
                    text-decoration: none;
                    width: 80px;
                    margin-right: auto
                }
                .go-back-a:hover{
                    color: #cd741c;
                }
            </style>
        <div class="header">
            @if (auth()->user()->id === $staff->id)
                <h2>Your details</h2>
            @else
                <h2 >Staff Details</h2>

            @endif
        </div>

        
        @if(auth()->user()->user_type === 'Admin' || auth()->user()->id === $staff->id)
        <div class="actionsSection">
            
            <div class="action-buttons">
                <!-- Edit Profile Modal Trigger -->
                <button class="action-btn edit-btn" onclick="openEditModal()">
                    <i class="fas fa-edit"></i> Edit Profile Info
                </button>
                
                <!-- Change Password Modal Trigger -->
                <button class="action-btn password-btn" onclick="openPasswordModal()">
                    <i class="fas fa-key"></i> Change Password
                </button>
                
             @if(auth()->user()->user_type === 'Admin' && auth()->user()->id !== $staff->id)
                 <!-- Update Status -->
                 <button class="action-btn status-btn" onclick="openStatusModal()">
                     <i class="fas fa-user-edit"></i> Update Status
                 </button>
                 
                 <!-- Deactivate/Delete Account -->
                 <button class="action-btn deactivate-btn" onclick="openDeactivateModal()">
                     <i class="fas fa-user-slash"></i> Deactivate Account
                 </button>
                 
                 <button class="action-btn delete-btn" onclick="openDeleteModal()">
                     <i class="fas fa-trash"></i> Delete Account
                 </button>
                 @endif
            </div>
        </div>
        @endif
        <div class="staffDetails">
            <div class="imageSection">
                @if($staff->image)
                    @php
                        $isBase64 = !empty($staff->image_mime);
                        $imgSrc = $isBase64 ? ('data:' . $staff->image_mime . ';base64,' . $staff->image) : asset('images/' . $staff->image);
                    @endphp
                    <img src="{{ $imgSrc }}" alt="Staff Image" class="staff-image">
                @else
                    <div class="no-image">
                        <i class="fas fa-user"></i>
                        <span>No Image</span>
                    </div>
                @endif
                
                @if(auth()->user()->user_type === 'Admin' || auth()->user()->id === $staff->id)
                <form action="{{ url('/staff/upload-image/' . $staff->id) }}" id="submitForm" method="POST" enctype="multipart/form-data" class="image-upload-form">
                    @csrf
                    <input type="file" name="image" id="image" accept="image/*" style="display: none;">
                    <button type="button" class="upload-btn" onclick="document.getElementById('image').click()">
                        <i class="fas fa-camera"></i> Change Image
                    </button>
                    <p id="file-error" style="color: red; display: none; margin: 0; font-size: 12px; margin: 0;"></p>

                    <button type="submit" id="submitBtn" class="save-btn" style="display: none;">Use this Image</button>
                </form>
                @endif
            </div>

            <div class="detailsSection">
                <div class="info-grid">
                    <div class="info-item">
                        <label>Staff ID:</label>
                        <span>{{ $staff->id }}</span>
                    </div>
                    
                    <div class="info-item">
                        <label>Username:</label>
                        <span>{{ $staff->username }}</span>
                    </div>
                    
                    <div class="info-item">
                        <label>Name:</label>
                        <span>{{ $staff->name }}</span>
                    </div>
                    
                    <div class="info-item">
                        <label>Email:</label>
                        <span>{{ $staff->email }}</span>
                    </div>
                    
                    <div class="info-item">
                        <label>Role/User Type:</label>
                        <span class="role-badge {{ strtolower($staff->user_type) }}">{{ $staff->user_type }}</span>
                    </div>
                    
                    <div class="info-item">
                        <label>Status:</label>
                        <span class="status-badge {{ $staff->acc_status ?? 'active' }}">
                            {{ $staff->acc_status ?? 'Active' }}
                        </span>
                    </div>
                    
                    <div class="info-item">
                        <label>Created At:</label>
                        <span>{{ $staff->created_at->format('F j, Y, g:i A') }}</span>
                    </div>
                    
                    @if($staff->last_seen_at)
                    <div class="info-item">
                        <label>Last Seen:</label>
                        <span>{{ \Carbon\Carbon::parse($staff->last_seen_at)->format('F j, Y, g:i A') }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>


        @if(session('success'))
            <div class="alert alert-success" style="background: #d4edda; color: #155724; position: absolute; z-index: 100; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Edit Profile Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h3>Edit Profile Information</h3>
            <form action="{{ url('/staff/update-profile/' . $staff->id) }}" id="editProfileForm" method="POST">
                @csrf
                <div class="form-group">
                    <label>Name:</label>
                    <input type="text" name="name" value="{{ $staff->name }}" required>
                </div>
                <div class="form-group">
                    <label>Username:</label>
                    <input type="text" name="username" value="{{ $staff->username }}" required>
                </div>
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" value="{{ $staff->email }}" required>
                </div>
                <div class="form-actions">
                    <button type="submit" style="background-color: #28a745; font-weight: normal;" class="reactivate-btn save-btn">
                        Save changes
                    </button>

                    <button type="button" class="cancel-btn" onclick="closeEditModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>


         <!-- Change Password Modal -->
     <div id="passwordModal" class="modal">
         <div class="modal-content">
             <span class="close" onclick="closePasswordModal()">&times;</span>
             <h3>Change Password</h3>
             <form action="{{ url('/staff/change-password/' . $staff->id) }}" id="submitForm" method="POST">
                 @csrf
                 @if(auth()->user()->user_type !== 'Admin' || auth()->user()->id === $staff->id)
                 <div class="form-group">
                     <label>Current Password:</label>
                     <input type="password" name="current_password" required>
                 </div>
                 @else
                 <div class="form-group">
                     <p style="color: #666; font-size: 14px; margin-bottom: 15px;">
                        As an admin, you can change this staff member's password without requiring their current password.
                     </p>
                 </div>
                 @endif
                 <div class="form-group">
                     <label>New Password:</label>
                     <input type="password" name="new_password" required>
                 </div>
                 <div class="form-group">
                     <label>Confirm New Password:</label>
                     <input type="password" name="new_password_confirmation" required>
                 </div>
                 <div class="form-actions">
                     <button type="submit" id="submitBtn" class="save-btn">Change Password</button>
                     <button type="button" id="submitBtn" class="cancel-btn" onclick="closePasswordModal()">Cancel</button>
                 </div>
             </form>
         </div>
     </div>

         <!-- Update Status Modal -->
     <div id="statusModal" class="modal">
         <div class="modal-content">
             <span class="close" onclick="closeStatusModal()">&times;</span>
             <h3>Update Staff Status</h3>
             <form action="{{ url('/staff/update-status/' . $staff->id) }}" id="submitForm" method="POST">
                 @csrf
                 <div class="form-group">
                     <label>Current Status:</label>
                     <span class="status-badge {{ $staff->acc_status ?? 'active' }}" style="display: inline-block; margin-left: 10px;">
                         {{ $staff->acc_status ?? 'Active' }}
                     </span>
                 </div>
                 <div class="form-group">
                     <label>New Status:</label>
                     <select name="acc_status" required>
                         <option value="">Select Status</option>
                         <option value="active" {{ ($staff->acc_status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
                         <option value="pending" {{ ($staff->acc_status ?? 'active') === 'pending' ? 'selected' : '' }}>Pending</option>
                         <option value="Suspended" {{ ($staff->acc_status ?? 'active') === 'Suspended' ? 'selected' : '' }}>Suspended</option>
                     </select>
                 </div>
                 <div class="form-actions">
                     <button type="submit" class="status-btn save-btn"  id="submitBtn">Update Status</button>
                     <button type="button" class="cancel-btn" onclick="closeStatusModal()"  id="submitBtn">Cancel</button>
                 </div>
             </form>
         </div>
     </div>

     <!-- Deactivate Account Modal -->
     <div id="deactivateModal" class="modal">
         <div class="modal-content">
             <span class="close" onclick="closeDeactivateModal()">&times;</span>
             <h3>Deactivate Account</h3>
             <p style="font-size: 14px">Are you sure you want to deactivate this staff account? They will not be able to log in until reactivated.</p>
             <form action="{{ url('/staff/deactivate/' . $staff->id) }}" method="POST" id="submitForm">
                 @csrf
                 <div class="form-actions">
                     <button type="submit" class="deactivate-btn save-btn">Deactivate</button>
                     <button type="button" class="cancel-btn" onclick="closeDeactivateModal()">Cancel</button>
                 </div>
             </form id="submitBtn">
         </div>
     </div>

    <!-- Delete Account Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeDeleteModal()">&times;</span>
            <h3>Delete Account</h3>
            <p style="font-size: 14px"><strong>Warning:</strong> This action cannot be undone. All data associated with this account will be permanently deleted.</p>
            <form action="{{ url('/staff/delete/' . $staff->id) }}" method="POST"  id="submitForm">
                @csrf
                @method('DELETE')
                <div class="form-group">
                    <label>Type "DELETE" to confirm:</label>
                    <input type="text" name="confirm_delete" placeholder="DELETE" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="delete-btn save-btn"  id="deleteBtn">Delete Account</button>
                    <button type="button" class="cancel-btn" onclick="closeDeleteModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="{{ asset('js/disableBtn.js') }}"></script>
    <script src="{{ asset('js/customer_view.js') }}"></script>
    <script src="{{ asset('js/confirmation-modal/staff_view.js') }}"></script>
    <script src="{{ asset('js/staffs/image.js') }}"></script>

</body>
</html>

@endsection 