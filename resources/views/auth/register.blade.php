@extends('layouts.registers')

@section('content')

<div class="registration-container">
    <div class="registration-header">
        <h1>Join SMOOTEA Academy</h1>
        <p>Begin your journey in beverage entrepreneurship education</p>
    </div>
    <form method="POST" action="{{ route('register') }}" class="registration-form" autocomplete="off">
        @csrf
        @if ($errors->any())
            <div class="error-container">
                <ul class="error-list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Enter your full name">
        </div>
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="Enter your email address">
        </div>
        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone" class="form-control" value="{{ old('phone') }}" required placeholder="Enter your phone number">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" required placeholder="Create a password">
            <div class="password-requirements">
                Password must be at least 8 characters long
            </div>
        </div>
        <div class="form-group">
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required placeholder="Confirm your password">
        </div>
        <input type="hidden" name="role" value="student">
        <div class="form-actions">
            <button type="submit" class="btn">Register</button>
        </div>
        <div class="login-link-container">
            Already have an account? <a href="{{ route('login') }}" class="login-link">Sign in</a>
        </div>
    </form>
</div>
@endsection