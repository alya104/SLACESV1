<aside class="sidebar">
    <div class="sidebar-header">
        <img src="{{ asset('images/logo.png') }}" alt="SMOOTEA Academy" class="logo">
        <h1>SMOOTEA Academy</h1>
    </div>
    
    <nav class="sidebar-menu">
        <ul>
            <li>
                <a href="{{ route('student.dashboard') }}" 
                   data-page="dashboard"
                   aria-current="{{ request()->routeIs('student.dashboard') ? 'page' : false }}"
                   class="{{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('student.classes') }}" 
                   data-page="classes"
                   aria-current="{{ request()->routeIs('student.classes') ? 'page' : false }}"
                   class="{{ request()->routeIs('student.classes') ? 'active' : '' }}">
                    <i class="fas fa-graduation-cap"></i> My Classes
                </a>
            </li>
            <li>
                <a href="{{ route('student.modules.all') }}" 
                   data-page="modules"
                   aria-current="{{ request()->routeIs('student.modules*') ? 'page' : false }}"
                   class="{{ request()->routeIs('student.modules*') ? 'active' : '' }}">
                    <i class="fas fa-book"></i> Modules
                </a>
            </li>
        </ul>
    </nav>
    
    <div class="sidebar-footer">
        <p>&copy; {{ date('Y') }} SMOOTEA Academy</p>
        <p>Version 1.0</p>
        
        <form method="POST" action="{{ route('logout') }}" class="logout-form" id="studentSidebarLogoutForm">
            @csrf
            <button type="button" class="logout-btn" onclick="showStudentSidebarLogoutModal()">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>
</aside>

<!-- Logout Confirmation Modal -->
<div id="studentSidebarLogoutModal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.25); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:12px; padding:32px 28px; max-width:320px; margin:auto; box-shadow:0 8px 32px rgba(0,0,0,0.15); text-align:center;">
        <h4 style="color:#b197fc; margin-bottom:18px;">Confirm Logout</h4>
        <p style="margin-bottom:28px;">Are you sure you want to logout?</p>
        <button type="button" class="btn btn-primary" onclick="submitStudentSidebarLogout()" style="margin-right:10px;">Yes, Logout</button>
        <button type="button" class="btn" onclick="hideStudentSidebarLogoutModal()" style="background:#eee; color:#555;">Cancel</button>
    </div>
</div>

<script>
function showStudentSidebarLogoutModal() {
    document.getElementById('studentSidebarLogoutModal').style.display = 'flex';
}
function hideStudentSidebarLogoutModal() {
    document.getElementById('studentSidebarLogoutModal').style.display = 'none';
}
function submitStudentSidebarLogout() {
    document.getElementById('studentSidebarLogoutForm').submit();
}
</script>