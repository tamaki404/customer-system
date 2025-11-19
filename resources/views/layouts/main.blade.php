

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
    <link rel="stylesheet" href="{{ asset('css/layout/locked-tabs.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/flash-message.css') }}">
    <link rel="stylesheet" href="{{ asset('css/franken/button.css') }}">
    <link rel="stylesheet" href="{{ asset('css/views/dropdown.css') }}">

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
                                    : asset('assets/default-company-logo.png');
                            @endphp
                            <img src="{{ $imgSrc }}" alt="Profile Image">

                        </div>
                        <div class="nameFrame">
   
                                @if(auth()->user()->role === 'Customer')
                                    <p class="userName">{{  auth()->user()->customer->company_name }}</p>
                                     

                                    {{-- In Blade Views: --}}
                                    @auth('representative')
                                        @php
                                            $rep = auth('representative')->user();
                                            $middleInitial = $rep->rep_middlename ? strtoupper(substr($rep->rep_middlename, 0, 1)) . '.' : '';
                                            $nameParts = [$rep->rep_lastname . ',', $rep->rep_firstname, $middleInitial];
                                            $fullName = implode(' ', array_filter($nameParts));
                                        @endphp

                                        <p style="color: #666">{{ $fullName }}  </p>
                                    @endauth





                                @elseif(auth()->user()->role !== 'Customer')
                                    <p class="userName">
                                        {{  auth()->user()->staff->firstname }}
                                        {{  auth()->user()->staff->lastname }}
                                    </p>
                                @endif

                            @if (Auth()->user()->role !== "Customer")
                                <p class="userTitle">{{  auth()->user()->role_type }}</p>
                            @elseif (Auth()->user()->role === "Customer")
                                <p class="userTitle">{{  auth()->user()->role }}</p>
                            @endif


                             
                            {{-- @if($user->role === 'Staff')
                                <p style="font-size: 13px; color: #666; margin: 0;">{{  auth()->user()->role_type }}</p>
                            @endif --}}


                        </div>

                    </div>
                </div>

                <!-- Side Menu -->
                @if (auth()->user()->role !== 'Customer')
                    <div class="sideMenu" style="gap: 0; margin: 0;">
                        @php $currentRoute = Route::currentRouteName(); @endphp

                        <!-- Group 1: Main Navigation -->
                        <div class="nav-group">
                            <div class="nav-group-title">Main Navigation</div>
                            <a class="nav-item{{ $currentRoute == 'dash.dashboard' ? ' active' : '' }}" href="{{ route('dash.dashboard') }}">
                                <span class="material-symbols-outlined">diamond_shine</span>
                                <p>Dashboard</p>
                                <div class="nav-indicator"></div>
                            </a>

                        </div>

                        <div class="nav-group">
                            <div class="nav-group-title">Credits & Receipts</div>

                                <a class="nav-item{{ $currentRoute == 'pym.list' ? ' active' : '' }}" href="{{ route('pym.list') }}">
                                    <span class="material-symbols-outlined">diamond_shine</span>
                                    <p>Proof of payments</p>
                                    <div class="nav-indicator"></div>
                                </a>                            
                        </div>
                        <div class="nav-group">
                            <div class="nav-group-title">Promo</div>
                            <a class="nav-item{{ $currentRoute == 'prm.list' ? ' active' : '' }}" href="{{ route('prm.list') }}">
                                <span class="material-symbols-outlined">diamond_shine</span>
                                <p>Promo list</p>
                                <div class="nav-indicator"></div>
                            </a>                           
                        </div>
                        <!-- Group 2: Orders & Inventory -->
                    <div class="nav-group">
                            <div class="nav-group-title">Orders & Inventory</div>
     
                            {{-- purchase request --}}
                            <a class="nav-item{{ $currentRoute == 'pr.list' ? ' active' : '' }}" href="{{ route('pr.list') }}">
                                <span class="material-symbols-outlined">diamond_shine</span>
                                <p>Purchase request</p>
                                <div class="nav-indicator"></div>
                            </a>
           
                                {{-- Deliveries --}}
                                <a class="nav-item{{ $currentRoute == 'dlv.list' ? ' active' : '' }}" href="{{ route(name: 'dlv.list') }}">
                                    <span class="material-symbols-outlined">diamond_shine</span>
                                    <p>Deliveries summary</p>
                                    <div class="nav-indicator"></div>
                                </a>
                                <a class="nav-item{{ $currentRoute == 'rtn.list' ? ' active' : '' }}" href="{{ route(name: 'rtn.list') }}">
                                    <span class="material-symbols-outlined">diamond_shine</span>
                                    <p>Return reports</p>
                                    <div class="nav-indicator"></div>
                                </a>

                                <a class="nav-item{{ $currentRoute == 'prd.list' ? ' active' : '' }}" href="{{ route('prd.list') }}">
                                    <span class="material-symbols-outlined">diamond_shine</span>
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
                            <a class="nav-item{{ $currentRoute == 'stff.list' ? ' active' : '' }}" href="{{ route('stff.list') }}">
                                <span class="material-symbols-outlined">diamond_shine</span>
                                <p>Staffs</p>
                                <div class="nav-indicator"></div>
                            </a>
                 
                        </div>

                        <!-- Group 4: Reports -->
                        <div class="nav-group">
                            <div class="nav-group-title">Reports & logs</div>

                            <a class="nav-item{{ $currentRoute == 'lg.list' ? ' active' : '' }}" href="{{ route('lg.list') }}" >
                                <span class="material-symbols-outlined">history</span>

                                <p>Logs</p>
                                <div class="nav-indicator"></div>
                            </a>
                        </div>




                    </div>
                @elseif (auth()->user()->role === 'Customer')
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
                            @if(!empty($permissions['Groups']) && $permissions['Groups'])
                                <a class="nav-item{{ $currentRoute == 'groups.view' ? ' active' : '' }}" href="{{ route('groups.view') }}">
                                    <span class="material-symbols-outlined">group</span>
                                    <p>Groups</p>
                                    <div class="nav-indicator"></div>
                                </a>
                            @endif
                        </div>

                        <div class="nav-group">
                            <div class="nav-group-title">Orders & Deliveries</div>

                            @if (Auth()->user()->role === 'Customer' &&  !empty($user->acc_status->staff_id))
                                {{-- purchase request --}}
                                    <a class="nav-item{{ $currentRoute == 'pr.list' ? ' active' : '' }}" href="{{ route('pr.list') }}">
                                        <span class="material-symbols-outlined">diamond_shine</span>
                                        <p>Purchase request</p>
                                        <div class="nav-indicator"></div>
                                    </a>
               

                                {{-- Deliveries --}}
                                <a class="nav-item{{ $currentRoute == 'dlv.list' ? ' active' : '' }}" href="{{ route(name: 'dlv.list') }}">
                                    <span class="material-symbols-outlined">diamond_shine</span>
                                    <p>Deliveries summary</p>
                                    <div class="nav-indicator"></div>
                                </a>
                 
                                <a class="nav-item{{ $currentRoute == 'prd.list' ? ' active' : '' }}" href="{{ route('prd.list') }}">
                                    <span class="material-symbols-outlined">diamond_shine</span>
                                    <p>Products</p>
                                    <div class="nav-indicator"></div>
                                </a>
                         
                            @else
                                <div class="locked">
                                    <p title="Please wait for a staff member to set it up.">
                                        <span class="material-symbols-outlined">lock</span>
                                        <span>This is currently locked. Hover for more info </span>
                                    </p>
                                </div>
                            @endif
                        </div>

                        <div class="nav-group">
                            <div class="nav-group-title">Credits & Receipts</div>

                            @if (Auth()->user()->role === 'Customer' &&  !empty($user->acc_status->staff_id))
                                {{-- Credits --}}
                                <a class="nav-item{{ $currentRoute == 'crd.list' ? ' active' : '' }}" href="{{ route('crd.list') }}">
                                    <span class="material-symbols-outlined">diamond_shine</span>
                                    <p>Credits</p>
                                    <div class="nav-indicator"></div>
                                </a>
                     
                            @else
                                <div class="locked">
                                    <p title="Please wait for a staff member to set it up.">
                                        <span class="material-symbols-outlined">lock</span>
                                        <span>This is currently locked. Hover for more info </span>
                                    </p>
                                </div>
                            @endif

                        </div>
                        <div class="nav-group">
                            <div class="nav-group-title">Reports & Logs</div>

                                {{-- Logs --}}
                                <a class="nav-item{{ $currentRoute == 'lg.list' ? ' active' : '' }}" href="{{ route('lg.list') }}">
                                    <span class="material-symbols-outlined">diamond_shine</span>
                                    <p>Logs</p>
                                    <div class="nav-indicator"></div>
                                </a>
                     

                        </div>
                    </div>

                @endif

                <!-- Sidebar Footer -->
                <div class="sidebar-footer">
                    @if (auth()->user()->role === 'Customer')
                        <div class="deskFrame">
                            <p class="inquiry">INQUIRIES</p>
                            <p>For any inquiries, contact us at sunny&scramble@gmail.com or 09123456789</p>
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
                <style>
                    .under-construction{
                        display: flex;
                        flex-direction: row;
                        align-items: center;
                        width: 100%;
                        padding: 10px;
                        border-radius: 10px;
                        margin-bottom: 5px;
                        overflow: hidden;
                        height: auto;
                        background-color: #ffb74d;
                        justify-content: center;
                        
                    }
                
                </style>
                <div class="under-construction">
                    <p style="margin: 0; color: #333;">This system is currently under development. You may experience some bugs, please report any issues you find. Thank you! and happy testing!</p>
                </div>
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
    <script src="{{ asset('js/errors/flash-message.js') }}"></script>


</body>
</html>