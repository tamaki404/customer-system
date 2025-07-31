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

    <!-- Favicon: ICO for best compatibility, SVG for modern browsers -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" href="{{ asset('faviconSVG.svg') }}" type="image/svg+xml">

    <title>Sunny & Scramble</title>
</head>
<body>

@auth
<div class="mainFrame">
    <!-- Mobile Toggle Button -->
    <div class="mobile-toggle" onclick="toggleSidebar()">
        <span class="material-symbols-outlined">menu</span>
    </div>

    <!-- Animated Side Navigation -->
    <div class="sideAccess" id="sideAccess">
        <div class="sidebar-header">
            <button class="userProfile" onclick="toggleUserProfile()">
                <div class="imgFrame"> 
                    <img src="{{ asset('images/' . auth()->user()->image) }}" alt="Customer Image">
                </div>
                <div class="nameFrame">
                    @if(auth()->user()->user_type === 'Admin')
                    <p class="userName">{{ auth()->user()->store_name }}</p>
                    @elseif(auth()->user()->user_type === 'Customer')
                    <p class="userName">{{ auth()->user()->store_name }}</p>
                    @elseif(auth()->user()->user_type === 'Staff')
                    <p class="userName">{{ auth()->user()->username }}</p>
                    @endif
                    <p class="userTitle">{{ auth()->user()->user_type }}</p> 
                </div>
            </button>
        </div>

        <div class="sideMenu">
            @php
                $currentRoute = Route::currentRouteName();
            @endphp
            
            @if(auth()->user()->user_type === 'Admin')
            <a class="nav-item{{ $currentRoute == 'dashboard' ? ' active' : '' }}" href="{{ route('dashboard') }}" data-tooltip="Dashboard">
                <span class="material-symbols-outlined">dashboard</span>
                <p>Dashboard</p>
                <div class="nav-indicator"></div>
            </a>
            
            <a class="nav-item{{ $currentRoute == 'profile' ? ' active' : '' }}" href="{{ route('profile') }}" data-tooltip="Profile">
                <span class="material-symbols-outlined">person</span>
                <p>Profile</p>
                <div class="nav-indicator"></div>
            </a>
            
            <a class="nav-item{{ $currentRoute == 'receipts' ? ' active' : '' }}" href="{{ route('receipts') }}" data-tooltip="Receipts">
                <span class="material-symbols-outlined">receipt</span>
                <p>Receipts</p>
                <div class="nav-indicator"></div>
            </a>

            <a class="nav-item{{ $currentRoute == 'staffs' ? ' active' : '' }}" href="{{ route('staffs') }}" data-tooltip="Staff Management">
                <span class="material-symbols-outlined">group</span>
                <p>Staffs</p>
                <div class="nav-indicator"></div>
            </a>
            
            <a class="nav-item{{ $currentRoute == 'customers' ? ' active' : '' }}" href="{{ route('customers') }}" data-tooltip="Customer Management">
                <span class="material-symbols-outlined">groups</span>
                <p>Customers</p>
                <div class="nav-indicator"></div>
            </a>

            @elseif(auth()->user()->user_type === 'Staff')
            <a class="nav-item{{ $currentRoute == 'dashboard' ? ' active' : '' }}" href="{{ route('dashboard') }}" data-tooltip="Dashboard">
                <span class="material-symbols-outlined">dashboard</span>
                <p>Dashboard</p>
                <div class="nav-indicator"></div>
            </a>
            
            <a class="nav-item{{ $currentRoute == 'profile' ? ' active' : '' }}" href="{{ route('profile') }}" data-tooltip="Profile">
                <span class="material-symbols-outlined">person</span>
                <p>Profile</p>
                <div class="nav-indicator"></div>
            </a>
            
            <a class="nav-item{{ $currentRoute == 'receipts' ? ' active' : '' }}" href="{{ route('receipts') }}" data-tooltip="Receipts">
                <span class="material-symbols-outlined">receipt</span>
                <p>Receipts</p>
                <div class="nav-indicator"></div>
            </a>

            <a class="nav-item{{ $currentRoute == 'customers' ? ' active' : '' }}" href="{{ route('customers') }}" data-tooltip="Customer Management">
                <span class="material-symbols-outlined">groups</span>
                <p>Customers</p>
                <div class="nav-indicator"></div>
            </a>

            @else
            <a class="nav-item{{ $currentRoute == 'dashboard' ? ' active' : '' }}" href="{{ route('dashboard') }}" data-tooltip="Dashboard">
                <span class="material-symbols-outlined">dashboard</span>
                <p>Dashboard</p>
                <div class="nav-indicator"></div>
            </a>

            <a class="nav-item{{ $currentRoute == 'profile' ? ' active' : '' }}" href="{{ route('profile') }}" data-tooltip="Profile">
                <span class="material-symbols-outlined">person</span>
                <p>Profile</p>
                <div class="nav-indicator"></div>
            </a>
            
            <a class="nav-item{{ $currentRoute == 'receipts' ? ' active' : '' }}" href="{{ route('receipts') }}" data-tooltip="Receipts">
                <span class="material-symbols-outlined">receipt</span>
                <p>Receipts</p>
                <div class="nav-indicator"></div>
            </a>
            @endif
        </div>

        <div class="sidebar-footer">
            <div class="deskFrame">
                <p class="inquiry">INQUIRIES</p>
                <p>For any inquiries, please contact us at rplai_riza@gmail.com or call us at 09123456789</p>
            </div>
            
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

    <!-- Main Content Area -->
    <div class="showScreen" id="showScreen">
        @yield('content')
    </div>
</div>

@else
<!-- Redirect to login if not authenticated -->
<script>
    window.location.href = '{{ route("login") }}';
</script>
@endauth

<script>
// Sidebar toggle functionality
function toggleSidebar() {
    const sidebar = document.getElementById('sideAccess');
    const mainContent = document.getElementById('showScreen');
    const toggleBtn = document.querySelector('.mobile-toggle');
    
    sidebar.classList.toggle('collapsed');
    mainContent.classList.toggle('expanded');
    toggleBtn.classList.toggle('active');
}

// User profile toggle
function toggleUserProfile() {
    const userProfile = document.querySelector('.userProfile');
    const toggleIcon = document.querySelector('.toggle-icon');
    
    userProfile.classList.toggle('expanded');
    toggleIcon.style.transform = userProfile.classList.contains('expanded') ? 'rotate(180deg)' : 'rotate(0deg)';
}

// Add hover effects for navigation items
document.addEventListener('DOMContentLoaded', function() {
    const navItems = document.querySelectorAll('.nav-item');
    
    navItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(10px)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
        
        // Add click animation
        item.addEventListener('click', function() {
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);
        });
    });
    
    // Auto-hide sidebar on mobile after navigation
    if (window.innerWidth <= 768) {
        const navLinks = document.querySelectorAll('.nav-item');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                setTimeout(() => {
                    document.getElementById('sideAccess').classList.add('collapsed');
                    document.getElementById('showScreen').classList.add('expanded');
                }, 300);
            });
        });
    }
});

// Handle window resize
window.addEventListener('resize', function() {
    if (window.innerWidth > 768) {
        document.getElementById('sideAccess').classList.remove('collapsed');
        document.getElementById('showScreen').classList.remove('expanded');
    }
});
</script>

</body>
</html>