<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout_final.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif !important; }</style>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" href="{{ asset('faviconSVG.svg') }}" type="image/svg+xml">
    <title>Sunny & Scramble</title>

    @stack('styles')
    @stack('cdn')
</head>
<body>

@auth
    <div class="mainFrame">

        <!-- Mobile Toggle Button -->
        <div class="mobile-toggle" onclick="toggleSidebar()">
            <span class="material-symbols-outlined">menu</span>
        </div>

        <!-- Side Navigation -->
        <div class="sideAccess" id="sideAccess">
            
            <!-- Sidebar Header -->
            <div class="sidebar-header">
                <div class="userProfile">
                    <div class="imgFrame">
                        @php
                            $isBase64 = !empty(auth()->user()->image_mime);
                            $imgSrc = $isBase64 
                                ? ('data:' . auth()->user()->image_mime . ';base64,' . auth()->user()->image) 
                                : asset('images/' . auth()->user()->image);
                        @endphp
                        <img src="{{ $imgSrc }}" alt="Customer Image">
                    </div>
                    <div class="nameFrame">
                        @if(auth()->user()->user_type === 'Admin')
                            <p class="userName">{{ auth()->user()->name }}</p>
                        @elseif(auth()->user()->user_type === 'Customer')
                            <p class="userName">{{ auth()->user()->store_name }}</p>
                        @elseif(auth()->user()->user_type === 'Staff')
                            <p class="userName">{{ auth()->user()->name }}</p>
                        @endif
                        <p class="userTitle">{{ auth()->user()->user_type }}</p> 
                    </div>
                </div>
            </div>

            <!-- Side Menu -->
            <div class="sideMenu" style="gap: 0; margin: 0;">
                @php $currentRoute = Route::currentRouteName(); @endphp

                @if(auth()->user()->user_type === 'Admin')

                <!-- Group 1: Main Navigation -->
                <div class="nav-group">
                    <div class="nav-group-title">Main Navigation</div>
                    <a class="nav-item{{ $currentRoute == 'dashboard' ? ' active' : '' }}" href="{{ route('dashboard') }}">
                        <span class="material-symbols-outlined">dashboard</span>
                        <p>Dashboard</p>
                        <div class="nav-indicator"></div>
                    </a>
                    <a class="nav-item{{ $currentRoute == 'profile' ? ' active' : '' }}" href="{{ route('profile') }}">
                        <span class="material-symbols-outlined">person</span>
                        <p>Profile</p>
                        <div class="nav-indicator"></div>
                    </a>
                </div>

                <!-- Group 2: Orders & Inventory -->
                <div class="nav-group">
                    <div class="nav-group-title">Orders & Inventory</div>
                    <a class="nav-item{{ $currentRoute == 'receipts' ? ' active' : '' }}" href="{{ route('receipts') }}">
                        <span class="material-symbols-outlined">receipt</span>
                        <p>Receipts</p>
                        <div class="nav-indicator"></div>
                    </a>
                    <a class="nav-item{{ $currentRoute == 'orders' ? ' active' : '' }}" href="{{ route('orders') }}">
                        <span class="material-symbols-outlined">shopping_bag</span>
                        <p>Orders</p>
                        <div class="nav-indicator"></div>
                    </a>
                    <a class="nav-item{{ $currentRoute == 'purchase_order' ? ' active' : '' }}" href="{{ route('purchase_order') }}">
                        <span class="material-symbols-outlined">receipt_long</span>
                        <p>Purchase Order</p>
                        <div class="nav-indicator"></div>
                    </a>
                    <a class="nav-item{{ $currentRoute == 'store' ? ' active' : '' }}" href="{{ route('store') }}">
                        <span class="material-symbols-outlined">store</span>
                        <p>Inventory</p>
                        <div class="nav-indicator"></div>
                    </a>
                </div>

                <!-- Group 3: People -->
                <div class="nav-group">
                    <div class="nav-group-title">People</div>
                    <a class="nav-item{{ $currentRoute == 'customers' ? ' active' : '' }}" href="{{ route('customers') }}">
                        <span class="material-symbols-outlined">groups</span>
                        <p>Customers</p>
                        <div class="nav-indicator"></div>
                    </a>
                    <a class="nav-item{{ $currentRoute == 'staffs' ? ' active' : '' }}" href="{{ route('staffs') }}">
                        <span class="material-symbols-outlined">supervisor_account</span>
                        <p>Staffs</p>
                        <div class="nav-indicator"></div>
                    </a>
                </div>

                <!-- Group 4: Reports -->
                <div class="nav-group">
                    <div class="nav-group-title">Reports & logs</div>
                    <a class="nav-item{{ $currentRoute == 'reports' ? ' active' : '' }}" href="{{ route('reports') }}">
                        <span class="material-symbols-outlined">bar_chart</span>
                        <p>Reports</p>
                        <div class="nav-indicator"></div>
                    </a>
                    <a class="nav-item{{ $currentRoute == 'logs' ? ' active' : '' }}" href="{{ route('logs.index') }}">
                        <span class="material-symbols-outlined">history</span>

                        <p>Logs</p>
                        <div class="nav-indicator"></div>
                    </a>
                </div>



                @elseif(auth()->user()->user_type === 'Staff')

                        <!-- Group 1: General -->
                        <div class="nav-group">
                            <div class="nav-group-title">General</div>
                            <a class="nav-item{{ $currentRoute == 'dashboard' ? ' active' : '' }}" href="{{ route('dashboard') }}">
                                <span class="material-symbols-outlined">dashboard</span>
                                <p>Dashboard</p>
                                <div class="nav-indicator"></div>
                            </a>
                            <a class="nav-item{{ $currentRoute == 'profile' ? ' active' : '' }}" href="{{ route('profile') }}">
                                <span class="material-symbols-outlined">person</span>
                                <p>Profile</p>
                                <div class="nav-indicator"></div>
                            </a>
                        </div>

                        <!-- Group 2: Customers & Inventory -->
                        <div class="nav-group">
                            <div class="nav-group-title">Customers & Inventory</div>
                            <a class="nav-item{{ $currentRoute == 'customers' ? ' active' : '' }}" href="{{ route('customers') }}">
                                <span class="material-symbols-outlined">groups</span>
                                <p>Customers</p>
                                <div class="nav-indicator"></div>
                            </a>
                            <a class="nav-item{{ $currentRoute == 'orders' ? ' active' : '' }}" href="{{ route('orders') }}">
                                <span class="material-symbols-outlined">shopping_bag</span>
                                <p>Orders</p>
                                <div class="nav-indicator"></div>
                            </a>
                            <a class="nav-item{{ $currentRoute == 'receipts' ? ' active' : '' }}" href="{{ route('receipts') }}">
                                <span class="material-symbols-outlined">receipt</span>
                                <p>Receipts</p>
                                <div class="nav-indicator"></div>
                            </a>
                            <a class="nav-item{{ $currentRoute == 'purchase_order' ? ' active' : '' }}" href="{{ route('purchase_order') }}">
                                <span class="material-symbols-outlined">receipt_long</span>
                                <p>Purchase Order</p>
                                <div class="nav-indicator"></div>
                            </a>
                            <a class="nav-item{{ $currentRoute == 'store' ? ' active' : '' }}" href="{{ route('store') }}">
                                <span class="material-symbols-outlined">store</span>
                                <p>Inventory</p>
                                <div class="nav-indicator"></div>
                            </a>
                        </div>

                        <!-- Group 3: Reports -->
                        <div class="nav-group">
                            <div class="nav-group-title">Reports & Logs</div>
                            <a class="nav-item{{ $currentRoute == 'reports' ? ' active' : '' }}" href="{{ route('reports') }}">
                                <span class="material-symbols-outlined">bar_chart</span>
                                <p>Reports</p>
                                <div class="nav-indicator"></div>
                            </a>
                            <a class="nav-item{{ $currentRoute == 'logs' ? ' active' : '' }}" href="{{ route('logs.index') }}">
                                <span class="material-symbols-outlined">bar_chart</span>
                                <p>Logs</p>
                                <div class="nav-indicator"></div>
                            </a>
                        </div>



                @else
                    <!-- Customer Menu -->
                    <a class="nav-item{{ $currentRoute == 'dashboard' ? ' active' : '' }}" href="{{ route('dashboard') }}">
                        <span class="material-symbols-outlined">dashboard</span>
                        <p>Dashboard</p>
                        <div class="nav-indicator"></div>
                    </a>
                    <a class="nav-item{{ $currentRoute == 'profile' ? ' active' : '' }}" href="{{ route('profile') }}">
                        <span class="material-symbols-outlined">person</span>
                        <p>Profile</p>
                        <div class="nav-indicator"></div>
                    </a>
                    <a class="nav-item{{ $currentRoute == 'receipts' ? ' active' : '' }}" href="{{ route('receipts') }}">
                        <span class="material-symbols-outlined">receipt</span>
                        <p>Receipts</p>
                        <div class="nav-indicator"></div>
                    </a>
                    <a class="nav-item{{ $currentRoute == 'store' ? ' active' : '' }}" href="{{ route('store') }}">
                        <span class="material-symbols-outlined">storefront</span>
                        <p>Store</p>
                        <div class="nav-indicator"></div>
                    </a>
                    <a class="nav-item{{ $currentRoute == 'customer_orders' ? ' active' : '' }}" href="{{ route('customer_orders') }}">
                        <span class="material-symbols-outlined">shopping_cart</span>
                        <p>My Orders</p>
                        <div class="nav-indicator"></div>
                    </a>
                    <a class="nav-item{{ $currentRoute == 'purchase_order' ? ' active' : '' }}" href="{{ route('purchase_order') }}">
                        <span class="material-symbols-outlined">receipt_long</span>
                        <p>Purchase Order</p>
                        <div class="nav-indicator"></div>
                    </a>
                    
                    <a class="nav-item{{ $currentRoute == 'reports' ? ' active' : '' }}" href="{{ route('customer_reports') }}">
                        <span class="material-symbols-outlined">bar_chart</span>
                        <p>Reports</p>
                        <div class="nav-indicator"></div>
                    </a>
                @endif



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

        <!-- Main Content -->
        <div class="showScreen" id="showScreen">
            @yield('content')

        </div>

    </div>
    @else
        <script>window.location.href = '{{ route("login") }}';</script>
@endauth

<script src="{{ asset('js/layout.js') }}"></script>
@stack('scripts')

</body>
</html>
