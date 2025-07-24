@extends('layouts.admin')

@section('content')
<div class="welcome-section">
    <h1 class="welcome-title">Admin Dashboard</h1>
    <p class="welcome-text">Welcome back, {{ Auth::user()->name }}!</p>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <!-- Total Invoices -->
    <div class="stat-card">
        <div class="stat-card-header">
            <div>
                <div class="stat-card-title">Total Invoices</div>
                <div class="stat-card-value">{{ number_format($invoiceCount) }}</div>
            </div>
            <div class="stat-card-icon invoices">
                <i class="fas fa-file-invoice"></i>
            </div>
        </div>
        <div class="stat-card-footer">
            <i class="fas fa-arrow-up"></i> 12% from last month
        </div>
    </div>

    <!-- Students Enrolled -->
    <div class="stat-card">
        <div class="stat-card-header">
            <div>
                <div class="stat-card-title">Students Enrolled</div>
                <div class="stat-card-value">{{ number_format($studentCount) }}</div>
            </div>
            <div class="stat-card-icon students">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div class="stat-card-footer">
            <i class="fas fa-arrow-up"></i> 8% from last month
        </div>
    </div>

    <!-- Active Classes -->
    <div class="stat-card">
        <div class="stat-card-header">
            <div>
                <div class="stat-card-title">Active Classes</div>
                <div class="stat-card-value">{{ number_format($classCount) }}</div>
            </div>
            <div class="stat-card-icon classes">
                <i class="fas fa-graduation-cap"></i>
            </div>
        </div>
        <div class="stat-card-footer">
            <i class="fas fa-arrow-up"></i> 2 new this week
        </div>
    </div>

    <!-- Pending Enrollments -->
    <div class="stat-card">
        <div class="stat-card-header">
            <div>
                <div class="stat-card-title">
                    Pending Enrollments
                    <span title="Number of invoices not yet redeemed by students">(unused invoices)</span>
                </div>
                <div class="stat-card-value">{{ \App\Models\Invoice::where('status', 'unused')->count() }}</div>
            </div>
            <div class="stat-card-icon pending">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>
</div>

<!-- Recent Enrollments -->
<div class="content-section">
    <div class="table-actions">
        <h2 class="section-title">Recent Enrollments</h2>
        <a href="{{ route('admin.enrollments.index') }}" class="btn btn-primary">View All</a>
    </div>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Email</th>
                    <th>Class</th>
                    <th>Date Joined</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentEnrollments as $enrollment)
                <tr>
                    <td>{{ $enrollment->user->name ?? 'Unknown User' }}</td>
                    <td>{{ $enrollment->user->email ?? 'No Email' }}</td>
                    <td>{{ $enrollment->class->title ?? 'Unknown Class' }}</td>
                    <td>{{ $enrollment->enrollment_date ? \Carbon\Carbon::parse($enrollment->enrollment_date)->format('Y-m-d') : 'N/A' }}</td>
                    <td>
                        <span class="status-badge status-{{ strtolower($enrollment->status ?? 'pending') }}">
                            {{ ucfirst($enrollment->status ?? 'pending') }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Refresh dashboard every 60 seconds
    setTimeout(function(){
        window.location.reload();
    }, 60000);
</script>
@endpush