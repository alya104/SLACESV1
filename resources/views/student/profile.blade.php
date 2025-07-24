@extends('layouts.student')

@section('alerts')
@if(session('success'))
    <div class="alert alert-success" id="successAlert">
        <div class="alert-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="alert-message">
            {{ session('success') }}
        </div>
        <div class="alert-dismiss" onclick="document.getElementById('successAlert').style.display='none';">
            <i class="fas fa-times"></i>
        </div>
    </div>
@endif
@endsection

@section('content')
<section class="content-section">
    <h2 class="section-title">Student Profile</h2>
    <div class="profile-card">
        <div class="profile-header">
            <img src="{{ $user->avatar_url ?? asset('images/default-avatar.png') }}" alt="Avatar" class="profile-avatar">
            <div>
                <h3>{{ $user->name }}</h3>
                <p>{{ $user->email }}</p>
            </div>
        </div>
        <div class="profile-details">
            <div><strong>{{ ucfirst($user->role) }}</strong> </div>
            <div><strong>Joined:</strong> {{ $user->created_at->format('Y-m-d') }}</div>
        </div>
    </div>
    <button class="btn btn-primary" id="editProfileBtn">Edit Profile</button>
</section>

<!-- Edit Profile Modal -->
<div class="modal" id="editProfileModal">
    <div class="modal-content">
        <form method="POST" action="{{ route('student.profile.update') }}" enctype="multipart/form-data">
            @csrf
            <h3>Edit Profile</h3>
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" value="{{ $user->name }}" required class="form-control">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="{{ $user->email }}" class="form-control" readonly>
            </div>
            <div class="form-group">
                <label>Avatar</label>
                <input type="file" name="avatar" class="form-control">
            </div>
            <div class="form-group">
                <label>New Password <small>(leave blank to keep current)</small></label>
                <input type="password" name="password" class="form-control" autocomplete="new-password">
            </div>
            <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" id="cancelEditProfile">Cancel</button>
                <button type="submit" class="btn btn-success">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
.profile-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.07);
    padding: 24px;
    max-width: 400px;
    margin: 0 auto 32px auto;
}
.profile-header {
    display: flex;
    align-items: center;
    gap: 20px;
}
.profile-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #eee;
}
.profile-details {
    margin-top: 16px;
    font-size: 16px;
}
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0; top: 0;
    width: 100vw; height: 100vh;
    background: rgba(0,0,0,0.3);
    justify-content: center;
    align-items: center;
}
.modal.active {
    display: flex;
}
.modal-content {
    background: #fff;
    padding: 32px 24px;
    border-radius: 8px;
    min-width: 320px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.12);
}
.form-group {
    margin-bottom: 18px;
}
.form-group label {
    display: block;
    margin-bottom: 6px;
    font-weight: 500;
}
.form-control {
    width: 100%;
    padding: 8px 10px;
    border-radius: 4px;
    border: 1px solid #ccc;
}
.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 18px;
}
</style>
@endpush

@push('scripts')
<script>
document.getElementById('editProfileBtn').onclick = function() {
    document.getElementById('editProfileModal').classList.add('active');
};
document.getElementById('cancelEditProfile').onclick = function() {
    document.getElementById('editProfileModal').classList.remove('active');
};
// Optional: close modal on outside click
document.getElementById('editProfileModal').onclick = function(event) {
    if (event.target === this) {
        this.classList.remove('active');
    }
};
</script>
@endpush