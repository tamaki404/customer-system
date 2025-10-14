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
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/layout/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/search.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/date-range.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/pagination.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/btn-hover.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/error-message.css') }}">

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
                                @if( auth()->user()->role === 'Admin')
                                    <p class="userName">{{ auth()->user()->supplier->company_name }}</p>
                                @elseif(auth()->user()->role === 'Supplier')
                                    <p class="userName">{{  auth()->user()->supplier->company_name }}</p>
                                     

                                    {{-- In Blade Views: --}}
                                    @auth('representative')
                                        @php
                                            $rep = auth('representative')->user();
                                            $middleInitial = $rep->rep_middlename ? strtoupper(substr($rep->rep_middlename, 0, 1)) . '.' : '';
                                            $nameParts = [$rep->rep_lastname . ',', $rep->rep_firstname, $middleInitial];
                                            $fullName = implode(' ', array_filter($nameParts));
                                        @endphp

                                        <p style="color: #666">{{ $fullName }}</p>
                                    @endauth





                                @elseif(auth()->user()->role === 'Staff')
                                    <p class="userName">
                                        {{  auth()->user()->staff->firstname }}
                                        {{  auth()->user()->staff->lastname }}
                                    </p>
                                  
                                @endif
                            <p class="userTitle">{{  auth()->user()->role }}</p>
                            {{-- @if($user->role === 'Staff')
                                <p style="font-size: 13px; color: #666; margin: 0;">{{  auth()->user()->role_type }}</p>
                            @endif --}}


                        </div>

                    </div>
                </div>

                <!-- Side Menu -->
                @if (auth()->user()->role !== 'Supplier')
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
                            {{-- <a class="nav-item{{ $currentRoute == 'profile.view' ? ' active' : '' }}" href="{{ route('profile.view') }}">
                                <span class="material-symbols-outlined">person</span>
                                <p>Profile</p>
                                <div class="nav-indicator"></div>
                            </a> --}}
                        </div>

                        <div class="nav-group">
                            <div class="nav-group-title">Credits & Receipts</div>
                            {{-- <a class="nav-item{{ $currentRoute == 'credits.list' ? ' active' : '' }}" href="{{ route('credits.list') }}">
                                <span class="material-symbols-outlined">credit_card</span>
                                <p>Credits</p>
                                <div class="nav-indicator"></div>
                            </a> --}}
                            <a class="nav-item{{ $currentRoute == 'receipts.list' ? ' active' : '' }}" href="{{ route('receipts.list') }}">
                                <span class="material-symbols-outlined">receipt</span>

                                <p>Proof of payments</p>
                                <div class="nav-indicator"></div>
                            </a>
                        </div>

                        <!-- Group 2: Orders & Inventory -->
                    <div class="nav-group">
                            <div class="nav-group-title">Orders & Inventory</div>
                            {{-- <a class="nav-item">
                                <span class="material-symbols-outlined">receipt</span>
                                <p>Receipts</p>
                                <div class="nav-indicator"></div>
                            </a>--}}
                            <a class="nav-item{{ $currentRoute == 'purchaseorders.list' ? ' active' : '' }}" href="{{ route('purchaseorders.list') }}">
                                <span class="material-symbols-outlined">shopping_bag</span>
                                <p>Purchase orders</p>
                                <div class="nav-indicator"></div>
                            </a>

                            <a class="nav-item{{ $currentRoute == 'orders.list' ? ' active' : '' }}" href="{{ route('orders.list') }}">
                                <span class="material-symbols-outlined">receipt_long</span>
                                <p>Orders</p>
                                <div class="nav-indicator"></div>
                            </a>
                            <a class="nav-item{{ $currentRoute == 'deliveries.list' ? ' active' : '' }}" href="{{ route('deliveries.list') }}">
                                <span class="material-symbols-outlined">delivery_truck_speed</span>
                                <p>Deliveries</p>
                                <div class="nav-indicator"></div>
                            </a>

                            <a class="nav-item{{ $currentRoute == 'products.list' ? ' active' : '' }}" href="{{ route('products.list') }}">
                                <span class="material-symbols-outlined">store</span>
                                <p>Products</p>
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
                            <a class="nav-item{{ $currentRoute == 'staffs.list' ? ' active' : '' }}" href="{{ route('staffs.list') }}" >
                                <span class="material-symbols-outlined">supervisor_account</span>
                                <p>Staffs</p>
                                <div class="nav-indicator"></div>
                            </a>
                        </div>

                        <!-- Group 4: Reports -->
                        <div class="nav-group">
                            <div class="nav-group-title">Reports & logs</div>
                            {{-- <a class="nav-item">
                                <span class="material-symbols-outlined">bar_chart</span>
                                <p>Reports</p>
                                <div class="nav-indicator"></div>
                            </a> --}}
                            <a class="nav-item{{ $currentRoute == 'logs.list' ? ' active' : '' }}" href="{{ route('logs.list') }}" >
                                <span class="material-symbols-outlined">history</span>

                                <p>Logs</p>
                                <div class="nav-indicator"></div>
                            </a>
                        </div>




                    </div>
                @elseif (auth()->user()->role === 'Supplier')
                    @php
                        $permissions = auth('representative')->user()->permissions ?? [];
                        $currentRoute = Route::currentRouteName();
                    @endphp

                    <div class="sideMenu" style="gap: 0; margin: 0;">
                        <div class="nav-group">
                            <div class="nav-group-title">Main Navigation</div>

                            {{-- Dashboard --}}
                            @if(!empty($permissions['Dashboard']) && $permissions['Dashboard'])
                                <a class="nav-item{{ $currentRoute == 'dashboard.view' ? ' active' : '' }}" href="{{ route('dashboard.view') }}">
                                    <span class="material-symbols-outlined">dashboard</span>
                                    <p>Dashboard</p>
                                    <div class="nav-indicator"></div>
                                </a>
                            @endif

                            {{-- Profile --}}
                            @if(!empty($permissions['Profile']) && $permissions['Profile'])
                                <a class="nav-item{{ $currentRoute == 'profile.view' ? ' active' : '' }}" href="{{ route('profile.view') }}">
                                    <span class="material-symbols-outlined">person</span>
                                    <p>Profile</p>
                                    <div class="nav-indicator"></div>
                                </a>
                            @endif
                        </div>

                        <div class="nav-group">
                            <div class="nav-group-title">Credits & Receipts</div>

                            {{-- Credits --}}
                            @if(!empty($permissions['Credits']) && $permissions['Credits'])
                                <a class="nav-item{{ $currentRoute == 'credits.list' ? ' active' : '' }}" href="{{ route('credits.list') }}">
                                    <span class="material-symbols-outlined">credit_card</span>
                                    <p>Credits</p>
                                    <div class="nav-indicator"></div>
                                </a>
                            @endif

                            {{-- Proof of Payments --}}
                            @if(!empty($permissions['POP']) && $permissions['POP'])
                                <a class="nav-item{{ $currentRoute == 'receipts.list' ? ' active' : '' }}" href="{{ route('receipts.list') }}">
                                    <span class="material-symbols-outlined">receipt</span>
                                    <p>Proof of payments</p>
                                    <div class="nav-indicator"></div>
                                </a>
                            @endif
                        </div>

                        <div class="nav-group">
                            <div class="nav-group-title">Orders & Inventory</div>

                            {{-- Purchase Orders --}}
                            @if(!empty($permissions['PO']) && $permissions['PO'])
                                <a class="nav-item{{ $currentRoute == 'purchaseorders.list' ? ' active' : '' }}" href="{{ route('purchaseorders.list') }}">
                                    <span class="material-symbols-outlined">shopping_bag</span>
                                    <p>Purchase orders</p>
                                    <div class="nav-indicator"></div>
                                </a>
                            @endif

                            {{-- Orders --}}
                            @if(!empty($permissions['Orders']) && $permissions['Orders'])
                                <a class="nav-item{{ $currentRoute == 'orders.list' ? ' active' : '' }}" href="{{ route('orders.list') }}">
                                    <span class="material-symbols-outlined">receipt_long</span>
                                    <p>Orders</p>
                                    <div class="nav-indicator"></div>
                                </a>
                            @endif

                            {{-- Products --}}
                            @if(!empty($permissions['Products']) && $permissions['Products'])
                                <a class="nav-item{{ $currentRoute == 'products.list' ? ' active' : '' }}" href="{{ route('products.list') }}">
                                    <span class="material-symbols-outlined">store</span>
                                    <p>Products</p>
                                    <div class="nav-indicator"></div>
                                </a>
                            @endif
                        </div>
                    </div>

                @endif

                <!-- Sidebar Footer -->
                <div class="sidebar-footer">
                    @if (auth()->user()->role === 'Supplier')
                        <div class="deskFrame">
                            <p class="inquiry">INQUIRIES</p>
                            <p>For any inquiries, contact us at rplai_riza@gmail.com or 09123456789</p>
                        </div>
                    @endif
                    
                    <div class="logoutFrame">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="logoutButton" type="submit">
                                <span class="material-symbols-outlined">logout</span>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>

                    <div class="ownFrame">
                        <p>OWNED BY</p>
                        <img src="{{ asset(path: 'assets/sunnyLogo1.png') }}" alt="Owner Image" width="100" class="ownerImage">
                    </div>
                </div>
                
            </div>

            <div class="showScreen" id="showScreen">
                @yield('content')
            </div>
            
        </div>

    @else
        <script>window.location.href = '{{ route("login") }}';</script>
        {{-- public function signoutRepresentative(Request $request)
        {
            Auth::guard('representative')->logout();
            
            return redirect()->route('dashboard.view')->with('success', 'Representative signed out successfully.');
        } --}}
    @endauth
    
    @stack('scripts')

</body>
</html>