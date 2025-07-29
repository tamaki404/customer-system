 @extends('layout')

@section('content')


<!DOCTYPE html>
<html>
<head>
    <style>
        .usersList {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.95rem;
            background: #fff;
            box-shadow: 0 2px 12px #0001;
        }
        .usersList th, .usersList td {
            width: 120px;
            max-width: 120px;
            min-width: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            padding: 12px 14px;
            border-bottom: 1px solid #e0e0e0;
            text-align: left;
        }
        .usersList th {
            background-color: #ffde59;
            color: #333;
            font-weight: 600;
        }
        .usersList tr:hover {
            background-color: #f9f9f9;
            cursor: pointer;
        }
        .userTable {
            background: white;
            padding: 40px;
            width: 100%;
            height: 100%;
            flex-direction: column;
            overflow-x: auto;
        }
        .titleFrame {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            margin-bottom: 20px;
        }
        .titleFrame h2 {
            color: #ffde59;
            font-size: 1.8rem;
            font-weight: 600;
            margin: 0;
        }
        .titleFrame button {
            background-color: #1976d2;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
        }
        .titleFrame button:hover {
            background: #0d47a1;
        }
    </style>
    <title>Users List</title>
    <link rel="stylesheet" href="{{ asset('css/staffs.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fadein.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
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
      
        <form action="/add-staff" class="addStaff"  method="post" enctype="multipart/form-data">
            @csrf
            <input type="text" name="username" id="" placeholder="username">
            <input type="password" name="password" id="" placeholder="password">
            <img src="" name="image" alt="">
            <input type="text" name="user_type"  value="Staff" hidden>
            <input type="text" name="acc_status"  value="Active" hidden>
            <input type="text" name="store_name" placeholder="Store Name" value="Rizal Poultry & Livestock Assoctiation .Inc" hidden>
            <input type="file" name="image" accept="image/*" required>
            <input type="text" name="action_by" value="{{ auth()->user()->username}}" hidden>

             <button>Add Staff</button>
        </form>
    @endif

    
  </div>
</div>
    
<div class="bodyFrame">
    <div class="titleFrame">
        <h2>Staffs</h2>
        <form action="" method="GET" style="display:flex;align-items:center;gap:10px;">
            <input type="text" name="search" placeholder="Search by ID, Name, or User Type" value="{{ request('search') }}" style="padding:8px 12px; border-radius:4px; border:1px solid #ccc; width:260px;">
            <button type="submit" style="background:#1976d2;color:#fff;border:none;padding:8px 16px;border-radius:4px;font-weight:600;cursor:pointer;">Search</button>
        </form>
        @if(auth()->user()->user_type === 'Admin')
        <button id="openModalBtn">Add Staff</button>
        @endif
    </div>
    <div class="userTable">
        <table class="usersList">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Username</th>
                    <th>User Type</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                <tr accesskey="`{{ $user->id }}`" onclick="window.location='{{ url('/staff_view/' . $user->id) }}'">
                    <td>{{ $user->id }}</td>
                    <td>
                        <img src="{{ asset('images/' . $user->image) }}" alt="Customer Image" style="max-width:50px;max-height:50px;border-radius:6px;object-fit:cover;border:1px solid #ccc;">
                    </td>
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->user_type }}</td>
                    <td>{{ $user->acc_status }}</td>
                    <td><button style="background:#ffde59;color:#333;border:none;padding:6px 14px;border-radius:4px;font-weight:600;cursor:pointer;">Edit</button></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

</body>
</html>

@endauth

<script src="{{ asset('scripts/open-modal.js') }}"></script>



@endsection
