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
    
    <div class="staffFrame">
        <div class="header">
            <h2 >Staff Details</h2>
            <a href="{{ url('/staffs') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Staff List
            </a>
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
                    <img src="{{ asset('images/' . $staff->image) }}" alt="Staff Image" class="staff-image">
                @else
                    <div class="no-image">
                        <i class="fas fa-user"></i>
                        <span>No Image</span>
                    </div>
                @endif
                
                @if(auth()->user()->user_type === 'Admin' || auth()->user()->id === $staff->id)
                <form action="{{ url('/staff/upload-image/' . $staff->id) }}" method="POST" enctype="multipart/form-data" class="image-upload-form">
                    @csrf
                    <input type="file" name="image" id="image" accept="image/*" style="display: none;">
                    <button type="button" class="upload-btn" onclick="document.getElementById('image').click()">
                        <i class="fas fa-camera"></i> Change Image
                    </button>
                    <button type="submit" class="save-btn" style="display: none;">Use this Image</button>
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
            <form action="{{ url('/staff/update-profile/' . $staff->id) }}" method="POST">
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
                    <button type="submit" class="save-btn">Save Changes</button>
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
             <form action="{{ url('/staff/change-password/' . $staff->id) }}" method="POST">
                 @csrf
                 @if(auth()->user()->user_type !== 'Admin' || auth()->user()->id === $staff->id)
                 <div class="form-group">
                     <label>Current Password:</label>
                     <input type="password" name="current_password" required>
                 </div>
                 @else
                 <div class="form-group">
                     <p style="color: #666; font-style: italic; margin-bottom: 15px;">
                         <i class="fas fa-info-circle"></i> As an admin, you can change this staff member's password without requiring their current password.
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
                     <button type="submit" class="save-btn">Change Password</button>
                     <button type="button" class="cancel-btn" onclick="closePasswordModal()">Cancel</button>
                 </div>
             </form>
         </div>
     </div>

         <!-- Update Status Modal -->
     <div id="statusModal" class="modal">
         <div class="modal-content">
             <span class="close" onclick="closeStatusModal()">&times;</span>
             <h3>Update Staff Status</h3>
             <form action="{{ url('/staff/update-status/' . $staff->id) }}" method="POST">
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
                         <option value="suspended" {{ ($staff->acc_status ?? 'active') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                     </select>
                 </div>
                 <div class="form-actions">
                     <button type="submit" class="status-btn">Update Status</button>
                     <button type="button" class="cancel-btn" onclick="closeStatusModal()">Cancel</button>
                 </div>
             </form>
         </div>
     </div>

     <!-- Deactivate Account Modal -->
     <div id="deactivateModal" class="modal">
         <div class="modal-content">
             <span class="close" onclick="closeDeactivateModal()">&times;</span>
             <h3>Deactivate Account</h3>
             <p>Are you sure you want to deactivate this staff account? They will not be able to log in until reactivated.</p>
             <form action="{{ url('/staff/deactivate/' . $staff->id) }}" method="POST">
                 @csrf
                 <div class="form-actions">
                     <button type="submit" class="deactivate-btn">Deactivate</button>
                     <button type="button" class="cancel-btn" onclick="closeDeactivateModal()">Cancel</button>
                 </div>
             </form>
         </div>
     </div>

    <!-- Delete Account Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeDeleteModal()">&times;</span>
            <h3>Delete Account</h3>
            <p><strong>Warning:</strong> This action cannot be undone. All data associated with this account will be permanently deleted.</p>
            <form action="{{ url('/staff/delete/' . $staff->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="form-group">
                    <label>Type "DELETE" to confirm:</label>
                    <input type="text" name="confirm_delete" placeholder="DELETE" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="delete-btn">Delete Account</button>
                    <button type="button" class="cancel-btn" onclick="closeDeleteModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Image upload functionality
        document.getElementById('image').addEventListener('change', function() {
            const saveBtn = document.querySelector('.save-btn');
            if (this.files.length > 0) {
                saveBtn.style.display = 'inline-block';
            } else {
                saveBtn.style.display = 'none';
            }
        });

        // Modal functions
        function openEditModal() {
            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        function openPasswordModal() {
            document.getElementById('passwordModal').style.display = 'block';
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').style.display = 'none';
        }

                 function openStatusModal() {
             document.getElementById('statusModal').style.display = 'block';
         }
 
         function closeStatusModal() {
             document.getElementById('statusModal').style.display = 'none';
         }
 
         function openDeactivateModal() {
             document.getElementById('deactivateModal').style.display = 'block';
         }
 
         function closeDeactivateModal() {
             document.getElementById('deactivateModal').style.display = 'none';
         }

        function openDeleteModal() {
            document.getElementById('deleteModal').style.display = 'block';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        }

        // Auto-hide success/error messages after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.5s ease';
                setTimeout(function() {
                    alert.style.display = 'none';
                }, 500);
            });
        }, 5000);
    </script>
</body>
</html>

@endsection 