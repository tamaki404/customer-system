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
        {{-- <h2>Staffs</h2> --}}


        {{-- <form action="" method="GET" style="display:flex;align-items:center;gap:10px;">
            <input type="text" name="search" placeholder="Search by ID, Name, or User Type" value="{{ request('search') }}" style="padding:8px 12px; border-radius:4px; border:1px solid #ccc; width:260px;">
            <button type="submit" style="background:#1976d2;color:#fff;border:none;padding:8px 16px;border-radius:4px;font-weight:600;cursor:pointer;">Search</button>
        </form> --}}

    <form method="GET" action="" class="date-search">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by ID, Name, or User Type">
                <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
    </form>

    

    {{-- <div class="titleCount"> <h2>Staffs List</h2></div> --}}
 

        @if(auth()->user()->user_type === 'Admin')
        <button id="openModalBtn">Add Staff</button>
        @endif
    </div>

    <div class="titleCount"> <h2>Staffs List</h2> <span style=" width: auto; display: flex; flex-direction: row; gap: 5px;"><p style="font-weight: bold; font-size: 15px;">Total:</p>{{ count($users) }}</span></div>

    <div class="userList">
        @if(isset($users) && count($users) > 0)
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#f7f7fa;">
                    <th style="padding:10px 8px;text-align:left;">ID</th>
                    <th style="padding:10px 8px;text-align:left;">Image</th>
                    <th style="padding:10px 8px;text-align:left;">Username</th>
                    <th style="padding:10px 8px;text-align:left;">User type</th>
                    <th style="padding:10px 8px;text-align:left;">Status</th>
                    <th style="padding:10px 8px;text-align:left;">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                <tr style="border-bottom:1px solid #eee; align-items: center;" accesskey="`{{ $user->id }}`" onclick="window.location='{{ url('/staff_view/' . $user->id) }}'">
                    <td style="padding:10px 8px;">{{  $user->id }}</td>
                    <td style="padding:10px 8px;">
                        @if($user->image)
                         <img src="{{ asset(path: 'images/' . $user->image) }}" alt="Customer Image" style="max-width:50px;max-height:50px;border-radius:6px;object-fit:cover;border:1px solid #ccc;">
                        @else
                            <span style="color:#aaa;">N/A</span>
                        @endif
                    </td>
                    <td style="padding:10px 8px;">{{ $user->username }}</td>
                    <td style="padding:10px 8px;">{{ $user->user_type }}</td>
                    <td style="padding:10px 8px;">{{ $user->acc_status }}</td>
                    <td><button style="background:#ffde59;color:#333;border:none;padding:6px 14px;border-radius:4px;font-weight:600;cursor:pointer;">Edit</button></td>
                </tr>
                @endforeach
            </tbody>
        </table>
         @else
            <p>No staffs found.</p>
        @endif
    </div>

</div>

</body>
</html>

@endauth

<script src="{{ asset('scripts/open-modal.js') }}"></script>



@endsection
