<header class="guest-header">
    <div class="container" style="display: flex; align-items: center; gap: 16px; padding: 16px 0;">
        <img src="{{ asset('images/default-avatar.png') }}" alt="Default Avatar" class="profile-avatar" style="width:60px;height:60px;border-radius:50%;">
        <div>
            <span style="font-weight: bold;">{{ $user->name ?? 'Guest' }}</span>
            <div style="font-size: 14px; color: #888;">{{ $user->email ?? '' }}</div>
        </div>
        <form id="logoutForm" action="{{ route('logout') }}" method="POST" style="margin-left:auto;">
            @csrf
            <button type="button" class="btn btn-primary" onclick="showLogoutModal()">Logout</button>
        </form>
    </div>
    <hr>
</header>

<!-- Logout Confirmation Modal -->
<div id="logoutModal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.25); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:12px; padding:32px 28px; max-width:320px; margin:auto; box-shadow:0 8px 32px rgba(0,0,0,0.15); text-align:center;">
        <h4 style="color:#b197fc; margin-bottom:18px;">Confirm Logout</h4>
        <p style="margin-bottom:28px;">Are you sure you want to logout?</p>
        <button type="button" class="btn btn-primary" onclick="submitLogout()" style="margin-right:10px;">Yes, Logout</button>
        <button type="button" class="btn" onclick="hideLogoutModal()" style="background:#eee; color:#555;">Cancel</button>
    </div>
</div>

<script>
function showLogoutModal() {
    document.getElementById('logoutModal').style.display = 'flex';
}
function hideLogoutModal() {
    document.getElementById('logoutModal').style.display = 'none';
}
function submitLogout() {
    document.getElementById('logoutForm').submit();
}
</script>