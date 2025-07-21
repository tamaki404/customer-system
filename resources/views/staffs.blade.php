 @extends('layout')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <title>Staffs</title>
</head>
<body>

<!DOCTYPE html>
<html>
<head>
    <title>Users List</title>
</head>
<body>
    <h1>Admin and Staff Users</h1>

    <button>
        for
        <form action="/add-staff"  method="post" enctype="multipart/form-data">
            @csrf
            <input type="text" name="username" id="" placeholder="username">
            <input type="password" name="password" id="" placeholder="password">
            <img src="" name="image" alt="">
            <input type="text" name="user_type"  value="Staff" hidden>
            <input type="text" name="acc_status"  value="Active" hidden>
            <input type="file" name="image" accept="image/*" required>

             <button>Add Staff</button>
        </form>s
    </button>
    <table border="1" class="usersList">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>User Type</th>
            <th>Status</th>
            <th>Action</th>

        </tr>
        @foreach ($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->username }}</td>
                <td>{{ $user->user_type }}</td>
                <td>{{$user->acc_status}}</td>
                <td><button>Edit</button></td>
            </tr>
        @endforeach
    </table>
</body>
</html>



</body>
</html>

 
@endsection
