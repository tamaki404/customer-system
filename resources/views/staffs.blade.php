 @extends('layout')

@section('content')


<!DOCTYPE html>
<html>
<head>
    <title>Users List</title>
    <link rel="stylesheet" href="{{ asset('css/staffs.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
        <link rel="stylesheet" href="{{ asset('css/open-modal.css') }}">


</head>
<body>


@auth
    
{{-- add staff modal --}}
<div id="myModal" class="modal">
  <div class="modal-content">
    <span class="close-btn">&times;</span>
     @if(auth()->user()->user_type === 'Staff')
      
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
    <h1>Staffs</h1>
        <button id="openModalBtn">Submit a Receipt</button>

    

    <div class="userTable">
        <table border="1" class="usersList">
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Username</th>
                <th>User Type</th>
                <th>Status</th>
                <th>Action</th>

            </tr>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td><img src="{{ asset('images/' . $user->image) }}" alt="Customer Image" width="100"></td>
                    
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->user_type }}</td>
                    <td>{{$user->acc_status}}</td>
                    <td><button>Edit</button></td>
                </tr>
            @endforeach
        </table>
    </div>

</div>

</body>
</html>

@endauth

<script src="{{ asset('scripts/open-modal.js') }}"></script>



@endsection
