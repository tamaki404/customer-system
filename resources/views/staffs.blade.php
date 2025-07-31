 @extends('layout')

@section('content')


<!DOCTYPE html>
<html>
<head>

    <title>Users List</title>
    <link rel="stylesheet" href="{{ asset('css/staffs.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fadein.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

        <link rel="stylesheet" href="{{ asset('css/open-modal.css') }}">


</head>
<body>
<script src="{{ asset('js/fadein.js') }}"></script>

@auth
    
{{-- add staff modal --}}
<div id="myModal" class="modal">
  <div class="modal-content">
    <span class="close-btn">&times;</span>
     @if(auth()->user()->user_type === 'Admin')
        <div class="form-section">
            <h3 class="form-title"  style="margin: 1px">Add New Staff Member</h3>
            <p style="font-size: 16px;">Please ensure all information entered is accurate and complete.</p>

            <form action="/add-staff"  class="receipt-form"  method="post" enctype="multipart/form-data">
                @csrf
                <div class="form-grid">
                    <div>
                        <label>Name</label>
                        <input type="text" name="name" placeholder="Name" required>
                    </div>
                    <div>
                        <label>Username</label>
                        <input type="text" name="username" placeholder="Unique username" id="username" ...>
                        <span id="username-error" class="error-message"></span>
                    </div>
                    <div>
                        <label>Email</label>
                        <input type="email" name="email" placeholder="New email" id="email" ...>
                        <span id="email-error" class="error-message"></span>
                    </div>
                    <div>
                        <label>Password</label>
                        <input type="text" name="password" placeholder="8 characters minimum" id="password" ...>
                        <span id="password-error" class="error-message"></span>
                    </div>
                    <div>
                        <label>Image</label>
                        <input type="file" name="image" id="image" ...>
                        <span id="image-error" class="error-message"></span>
                    </div>


                    <input type="text" name="action_by" value="{{ auth()->user()->username}}" hidden>
                    <input type="text" name="store_name" placeholder="Store Name" value="Sunny & Scramble" hidden>
                    <input type="text" name="user_type"  value="Staff" hidden>
                    <input type="text" name="acc_status"  value="Active" hidden>
            
                </div>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif


                <button type="submit" class="submit-btn" id="submitBtn" style="color: #333; font-size: 15px; box-shadow: rgba(0, 0, 0, 0.15) 1.95px 1.95px 2.6px;">Add Staff</button>

            </form>
        </div>

    @endif

  </div>
</div>
    
<div class="bodyFrame">
    <div class="titleFrame">


    <form method="GET" action="" class="date-search">
        <input type="text" name="search" style="outline:none;" value="{{ request('search') }}" placeholder="Search by ID, Name, or User Type">
        <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
    </form>
 

        @if(auth()->user()->user_type === 'Admin')
        <button id="openModalBtn" class="addStaffBtn">Add Staff</button>
        @endif
    </div>

    <div class="titleCount"> 
        <h2>Staffs List</h2> 

    </div>
    
    <!-- Pagination Info -->
    <div style="margin-bottom: 1rem; font-size: 0.9rem; color: #666;">
        Page {{ $users->currentPage() }} of {{ $users->lastPage() }} ({{ $users->total() }} total staff)
    </div>

<div class="userList" style="padding: 15px;">
    @if(isset($users) && count($users) > 0)
    <table style="width:100%; border-collapse:collapse; table-layout:fixed;">
        <thead>
            <tr style="background:#f7f7fa;">
                <th style="padding:10px 8px; text-align:left; width:80px;"></th>
                <th style="padding:10px 8px; text-align:left; width:20%;">Name</th>
                <th style="padding:10px 8px; text-align:left; width:20%;">Username</th>
                <th style="padding:10px 8px; text-align:left; width:15%;">ID</th>
                <th style="padding:10px 8px; text-align:left; width:15%;">User type</th>
                <th style="padding:10px 8px; text-align:left; width:15%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
            <tr style="border-bottom:1px solid #eee; align-items: center; cursor: pointer;" onclick="window.location='{{ url('/staff_view/' . $user->id) }}'">
                <td style="padding:10px 8px; width:80px;">
                    @if($user->image)
                        <img src="{{ asset('images/' . $user->image) }}" alt="User Image" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                    @else
                        <span style="color:#aaa;">N/A</span>
                    @endif
                </td>
                <td style="padding:10px 8px; width:20%; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                    {{ $user->name }}
                </td>

                <td style="padding:10px 8px; width:20%; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                    {{ $user->username }}
                </td>
                <td style="padding:10px 8px; width:15%; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                    {{ $user->id }}
                </td>
                <td style="padding:10px 8px; width:15%; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                    {{ $user->user_type }}
                </td>
                <td style="padding:10px 8px; width:15%; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                    <span class="status-badge {{ $user->acc_status ?? 'active' }}" style="padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; text-transform: uppercase; color: white;">
                        {{ $user->acc_status ?? 'Active' }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <!-- Pagination Controls -->
    <div class="pagination-wrapper" style="margin-top: 2rem; text-align: center;">
        @if($users->hasPages())
            <div class="pagination-controls" style="display: flex; justify-content: center; align-items: center; gap: 1rem;">
                @if($users->onFirstPage())
                    <span style="color: #ccc; cursor: not-allowed;">Previous</span>
                @else
                    <a href="{{ $users->previousPageUrl() }}" 
                       style="color: #1976d2; text-decoration: none; padding: 0.5rem 1rem; border: 1px solid #1976d2; border-radius: 4px;">
                        Previous
                    </a>
                @endif
                
                @if($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}" 
                       style="color: #1976d2; text-decoration: none; padding: 0.5rem 1rem; border: 1px solid #1976d2; border-radius: 4px;">
                        Next
                    </a>
                @else
                    <span style="color: #ccc; cursor: not-allowed;">Next</span>
                @endif
            </div>
        @endif
    </div>
    @else
        <div class="noStaff">No staffs found.</div>
    @endif
</div>

</div>

</body>
</html>

@endauth

<script src="{{ asset('scripts/open-modal.js') }}"></script>



@endsection
