 @extends('layout')

@section('content')


<!DOCTYPE html>
<html>
<head>
    <title>Users List</title>
    <link rel="stylesheet" href="{{ asset('css/staffs.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />

</head>
<body>
    
<div class="bodyFrame">
    <h1>Admin and Staff Users</h1>

    
        <form action="/add-staff" class="addStaff"  method="post" enctype="multipart/form-data">
            @csrf
            <input type="text" name="username" id="" placeholder="username">
            <input type="password" name="password" id="" placeholder="password">
            <img src="" name="image" alt="">
            <input type="text" name="user_type"  value="Staff" hidden>
            <input type="text" name="acc_status"  value="Active" hidden>
            <input type="file" name="image" accept="image/*" required>
            <input type="text" name="action_by" value="{{ auth()->user()->username}}" hidden>

             <button>Add Staff</button>
        </form>
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



</body>
</html>

 
@endsection
