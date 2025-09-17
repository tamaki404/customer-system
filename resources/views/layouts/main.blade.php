<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sunny & Scramble</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="{{ asset('css/layout/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/search.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/date-range.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/pagination.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/btn-hover.css') }}">



     @stack('styles')

</head>
<body>
    @auth

        <div class="mainFrame">


        <div class="sideAccess" id="sideAccess">
            
            <!-- Sidebar Header -->
            <div class="sidebar-header">
                <div class="userProfile">
                    <div class="imgFrame">
                        @php
                            $imgSrc = auth()->user()->image 
                                ? ('data:' . auth()->user()->image_mime_type . ';base64,' . base64_encode(auth()->user()->image))
                                : asset('images/default-avatar.png');
                        @endphp
                        <img src="{{ $imgSrc }}" alt="Profile Image">

                    </div>
                    <div class="nameFrame">
                        @if($user->role === 'Admin')
                            <p class="userName">{{ auth()->user()->supplier->company_name }}</p>
                        @elseif($user->role === 'Supplier' && $supplier)
                            <p class="userName">{{  auth()->user()->supplier->company_name }}</p>
                        @endif

                        <p class="userTitle">{{  auth()->user()->role }}</p>
                    </div>

                </div>
            </div>

            <!-- Side Menu -->
            <div class="sideMenu" style="gap: 0; margin: 0;">
                @php $currentRoute = Route::currentRouteName(); @endphp

                <!-- Group 1: Main Navigation -->
                <div class="nav-group">
                    <div class="nav-group-title">Main Navigation</div>
                    <a class="nav-item{{ $currentRoute == 'dashboard.view' ? ' active' : '' }}" href="{{ route('dashboard.view') }}">
                        <span class="material-symbols-outlined">dashboard</span>
                        <p>Dashboard</p>
                        <div class="nav-indicator"></div>
                    </a>
                    <a class="nav-item">
                        <span class="material-symbols-outlined">person</span>
                        <p>Profile</p>
                        <div class="nav-indicator"></div>
                    </a>
                </div>

                <!-- Group 2: Orders & Inventory -->
                <div class="nav-group">
                    <div class="nav-group-title">Orders & Inventory</div>
                    <a class="nav-item">
                        <span class="material-symbols-outlined">receipt</span>
                        <p>Receipts</p>
                        <div class="nav-indicator"></div>
                    </a>
                    <a class="nav-item">
                        <span class="material-symbols-outlined">shopping_bag</span>
                        <p>Orders</p>
                        <div class="nav-indicator"></div>
                    </a>
                    <a class="nav-item">
                        <span class="material-symbols-outlined">receipt_long</span>
                        <p>Purchase Order</p>
                        <div class="nav-indicator"></div>
                    </a>
                    <a class="nav-item">
                        <span class="material-symbols-outlined">store</span>
                        <p>Inventory</p>
                        <div class="nav-indicator"></div>
                    </a>
                </div>

                <!-- Group 3: People -->
                <div class="nav-group">
                    <div class="nav-group-title">People</div>
                    <a class="nav-item{{ $currentRoute == 'customers.list' ? ' active' : '' }}" href="{{ route('customers.list') }}">
                        <span class="material-symbols-outlined">groups</span>
                        <p>Customers</p>
                        <div class="nav-indicator"></div>
                    </a>
                    <a class="nav-item" >
                        <span class="material-symbols-outlined">supervisor_account</span>
                        <p>Staffs</p>
                        <div class="nav-indicator"></div>
                    </a>
                </div>

                <!-- Group 4: Reports -->
                <div class="nav-group">
                    <div class="nav-group-title">Reports & logs</div>
                    <a class="nav-item">
                        <span class="material-symbols-outlined">bar_chart</span>
                        <p>Reports</p>
                        <div class="nav-indicator"></div>
                    </a>
                    <a class="nav-item" >
                        <span class="material-symbols-outlined">history</span>

                        <p>Logs</p>
                        <div class="nav-indicator"></div>
                    </a>
                </div>




            </div>

            <!-- Sidebar Footer -->
            <div class="sidebar-footer">
                @if (auth()->user()->user_type === 'Customer')
                    <div class="deskFrame">
                        <p class="inquiry">INQUIRIES</p>
                        <p>For any inquiries, contact us at rplai_riza@gmail.com or 09123456789</p>
                    </div>
                @endif
                
                <div class="logoutFrame">
                    <form action="/logout-user" method="post">
                        @csrf
                        <button class="logoutButton" type="submit">
                            <span class="material-symbols-outlined">logout</span>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>

                <div class="ownFrame">
                    <p>OWNED BY</p>
                    <img src="{{ asset('assets/sunnyLogo1.png') }}" alt="Owner Image" width="100" class="ownerImage">
                </div>
            </div>
            
        </div>



            <div class="showScreen" id="showScreen">
                @yield('content')

            </div>
            
        </div>

    @else
        <script>window.location.href = '{{ route("login") }}';</script>
    @endauth
    
    @stack('scripts')

</body>
</html>