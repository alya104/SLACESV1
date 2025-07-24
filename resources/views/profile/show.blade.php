@extends('layouts.admin')

@section('content')
<div class="container" style="padding: 2rem;">
    <h1>User Profile</h1>
    <div class="profile-card" style="background: #fff; padding: 2rem; border-radius: 10px; box-shadow: var(--card-shadow);">
        <div style="display: flex; align-items: center; margin-bottom: 2rem;">
            <img src="{{ Auth::user()->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name) }}" 
                 alt="Profile" style="width: 100px; height: 100px; border-radius: 50%; margin-right: 2rem;">
            <div>
                <h2>{{ Auth::user()->name }}</h2>
                <p>{{ Auth::user()->email }}</p>
                <p>Role: {{ ucfirst(Auth::user()->role) }}</p>
            </div>
        </div>
        
        <h3>Account Information</h3>
        <p>This is a simple profile page. In a real implementation, you would add functionality to update profile information.</p>
    </div>
    <div class="stat-card" style="margin-top: 2rem; display: flex; justify-content: space-between; background: #fff; padding: 1.5rem; border-radius: 10px; box-shadow: var(--card-shadow);">
        <div class="stat-card-item" style="text-align: center;">
            <div class="stat-card-title" style="font-weight: bold; margin-bottom: 0.5rem;">Total Invoices</div>
            <div class="stat-card-value" style="font-size: 1.5rem;">{{ $invoiceCount }}</div>
        </div>
        <div class="stat-card-item" style="text-align: center;">
            <div class="stat-card-title" style="font-weight: bold; margin-bottom: 0.5rem;">Pending Invoices</div>
            <div class="stat-card-value" style="font-size: 1.5rem;">{{ $pendingInvoiceCount }}</div>
        </div>
        <div class="stat-card-item" style="text-align: center;">
            <div class="stat-card-title" style="font-weight: bold; margin-bottom: 0.5rem;">Paid Invoices</div>
            <div class="stat-card-value" style="font-size: 1.5rem;">{{ $paidInvoiceCount }}</div>
        </div>
    </div>
</div>
@endsection
