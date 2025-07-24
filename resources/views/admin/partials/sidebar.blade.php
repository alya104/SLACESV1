<aside class="sidebar">
    <div class="sidebar-header">
        <img src="{{ asset('images/logo.png') }}" alt="SMOOTEA Academy" class="logo">
        <h1>SMOOTEA Academy</h1>
    </div>
    
    <nav class="sidebar-menu">
        <ul>
            <li>
                <a href="{{ route('admin.dashboard') }}" 
                   data-page="dashboard"
                   aria-current="{{ request()->routeIs('admin.dashboard') ? 'page' : false }}"
                   class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('admin.classes') }}" 
                   data-page="classes"
                   aria-current="{{ request()->routeIs('admin.classes*') ? 'page' : false }}"
                   class="{{ request()->routeIs('admin.classes*') ? 'active' : '' }}">
                    <i class="fas fa-graduation-cap"></i> Classes
                </a>
            </li>
            <li>
                <a href="{{ route('admin.enrollments.index') }}" 
                   data-page="enrollments"
                   aria-current="{{ request()->routeIs('admin.enrollments*') ? 'page' : false }}"
                   class="{{ request()->routeIs('admin.enrollments*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Enroll Students
                </a>
            </li>
            <li>
                <a href="{{ route('admin.invoices.index') }}" 
                   data-page="invoices"
                   aria-current="{{ request()->routeIs('admin.invoices*') ? 'page' : false }}"
                   class="{{ request()->routeIs('admin.invoices*') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice"></i> Invoices
                </a>
            </li>
            <li>
                <a href="{{ route('admin.materials') }}" 
                   data-page="materials"
                   aria-current="{{ request()->routeIs('admin.materials*') ? 'page' : false }}"
                   class="{{ request()->routeIs('admin.materials*') ? 'active' : '' }}">
                    <i class="fas fa-book"></i> Materials
                </a>
            </li>
            <li>
                <a href="{{ route('admin.logs') }}" 
                   data-page="logs"
                   aria-current="{{ request()->routeIs('admin.logs') ? 'page' : false }}"
                   class="{{ request()->routeIs('admin.logs') ? 'active' : '' }}">
                    <i class="fas fa-history"></i> Logs
                </a>
            </li>
        </ul>
    </nav>
    
    <div class="sidebar-footer">
        <p>&copy; {{ date('Y') }} SMOOTEA Academy</p>
        <p>Version 1.0</p>
        
        <form method="POST" action="{{ route('logout') }}" class="logout-form" id="adminSidebarLogoutForm">
            @csrf
            <button type="button" class="logout-btn" onclick="showAdminSidebarLogoutModal()">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>

<!-- Sidebar Logout Confirmation Modal -->
<div id="adminSidebarLogoutModal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.25); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:12px; padding:32px 28px; max-width:320px; margin:auto; box-shadow:0 8px 32px rgba(0,0,0,0.15); text-align:center;">
        <h4 style="color:#b197fc; margin-bottom:18px;">Confirm Logout</h4>
        <p style="margin-bottom:28px;">Are you sure you want to logout?</p>
        <button type="button" class="btn btn-primary" onclick="submitAdminSidebarLogout()" style="margin-right:10px;">Yes, Logout</button>
        <button type="button" class="btn" onclick="hideAdminSidebarLogoutModal()" style="background:#eee; color:#555;">Cancel</button>
    </div>
</div>

<script>
function showAdminSidebarLogoutModal() {
    document.getElementById('adminSidebarLogoutModal').style.display = 'flex';
}
function hideAdminSidebarLogoutModal() {
    document.getElementById('adminSidebarLogoutModal').style.display = 'none';
}
function submitAdminSidebarLogout() {
    document.getElementById('adminSidebarLogoutForm').submit();
}
</script>