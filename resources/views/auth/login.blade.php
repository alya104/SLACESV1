@extends('layouts.login_layout')

@section('content')
<div class="login-container">
    <div class="login-header">
        <h1>Login</h1>
        <p>Welcome back! Please login to your account.</p>
    </div>
    <form method="POST" action="{{ route('login') }}" class="login-form">
        @csrf
        @if($errors->any())
            <div class="error-container">
                <ul class="error-list">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input id="password" type="password" class="form-control" name="password" required>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn">Login</button>
        </div>
    </form>
    <div class="register-link-container">
        <a href="{{ route('register') }}" class="register-link">Don't have an account? Register</a>
    </div>
</div>
@endsection