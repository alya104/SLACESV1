<header class="dashboard-header">
    <h1 class="page-title" id="pageTitle">@yield('page_title', 'Student Dashboard')</h1>
    <div class="profile-dropdown" id="profileDropdown">
        <button class="profile-btn" onclick="toggleDropdown(event)">
            @if(Auth::user()->avatar_url)
                <img src="{{ asset(Auth::user()->avatar_url) }}" alt="Profile">
            @elseif(Auth::user()->avatar)
                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Profile">
            @else
                <img src="{{ asset('images/default-avatar.png') }}" alt="Profile">
            @endif
            {{ Auth::user()->name }}
            <i class="fas fa-chevron-down" style="margin-left:0.5rem;"></i>
        </button>
        <div class="dropdown-menu">
            <a href="{{ route('student.profile') }}"><i class="fas fa-user"></i> Profile</a>
            <form method="POST" action="{{ route('logout') }}" class="logout-form" id="studentLogoutForm">
                @csrf
                <button type="button" class="logout-btn" onclick="showStudentLogoutModal(event)">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </div>
</header>

<!-- Logout Confirmation Modal -->
<div id="studentLogoutModal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.25); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:12px; padding:32px 28px; max-width:320px; margin:auto; box-shadow:0 8px 32px rgba(0,0,0,0.15); text-align:center;">
        <h4 style="color:#b197fc; margin-bottom:18px;">Confirm Logout</h4>
        <p style="margin-bottom:28px;">Are you sure you want to logout?</p>
        <button type="button" class="btn btn-primary" onclick="submitStudentLogout()" style="margin-right:10px;">Yes, Logout</button>
        <button type="button" class="btn" onclick="hideStudentLogoutModal()" style="background:#eee; color:#555;">Cancel</button>
    </div>
</div>

<script>
function toggleDropdown(event) {
    event.stopPropagation();
    const dropdown = document.querySelector('#profileDropdown .dropdown-menu');
    dropdown.classList.toggle('active');
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function closeDropdown() {
        dropdown.classList.remove('active');
        document.removeEventListener('click', closeDropdown);
    });
}

function showStudentLogoutModal(e) {
    e.preventDefault();
    document.getElementById('studentLogoutModal').style.display = 'flex';
}
function hideStudentLogoutModal() {
    document.getElementById('studentLogoutModal').style.display = 'none';
}
function submitStudentLogout() {
    document.getElementById('studentLogoutForm').submit();
}
</script>