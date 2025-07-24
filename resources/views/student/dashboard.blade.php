@extends('layouts.student')

@section('content')
<div class="welcome-section">
    <div class="welcome-header">
        <div>
            <h1 class="welcome-title">Student Dashboard</h1>
            <p class="welcome-text">Welcome back, {{ Auth::user()->name }}!</p>
        </div>
        <a href="{{ route('student.join-class') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Join New Class
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <!-- Enrolled Classes -->
    <div class="stat-card">
        <div class="stat-card-header">
            <div>
                <div class="stat-card-title">Enrolled Classes</div>
                <div class="stat-card-value">{{ $enrolledClassesCount ?? 0 }}</div>
            </div>
            <div class="stat-card-icon classes">
                <i class="fas fa-graduation-cap"></i>
            </div>
        </div>
        <div class="stat-card-footer">
            <a href="{{ route('student.classes') }}" class="stat-card-link">View all classes</a>
        </div>
    </div>

    <!-- Overall Completion -->
    <div class="stat-card">
        <div class="stat-card-header">
            <div>
                <div class="stat-card-title">Overall Progress</div>
                <div class="stat-card-value">{{ number_format($overallCompletionPercentage ?? 0, 0) }}%</div>
            </div>
            <div class="stat-card-icon progress">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
        <div class="progress" style="height: 8px; margin-top: 12px;">
            <div class="progress-bar" role="progressbar" 
                style="width: {{ $overallCompletionPercentage ?? 0 }}%;"
                aria-valuenow="{{ $overallCompletionPercentage ?? 0 }}" 
                aria-valuemin="0" aria-valuemax="100">
            </div>
        </div>
    </div>

    <!-- Learning Materials -->
    <div class="stat-card">
        <div class="stat-card-header">
            <div>
                <div class="stat-card-title">Learning Materials</div>
                <div class="stat-card-value">{{ $availableMaterialsCount ?? 0 }}</div>
            </div>
            <div class="stat-card-icon materials">
                <i class="fas fa-book"></i>
            </div>
        </div>
        <div class="stat-card-footer">
            <a href="{{ route('student.modules.all') }}" class="stat-card-link">View all materials</a>
        </div>
    </div>
</div>

<!-- Recent Progress Section -->
<div class="content-section">
    <div class="section-header">
        <h2 class="section-title">My Progress</h2>
        <a href="{{ route('student.classes') }}" class="btn btn-primary">View All Classes</a>
    </div>

    @if(count($enrolledClasses ?? []) > 0)
    <div class="progress-cards">
        @foreach($enrolledClasses ?? [] as $enrollment)
        <div class="progress-card">
            <div class="progress-card-header">
                <h3>{{ $enrollment->class->title }}</h3>
                <span class="status-badge status-{{ $enrollment->status }}">{{ ucfirst($enrollment->status) }}</span>
            </div>
            <div class="progress-card-body">
                <div class="progress-info">
                    <span class="progress-label">Progress: {{ $enrollment->completion_percentage }}%</span>
                    <span class="progress-value">{{ $enrollment->completed_modules_count }}/{{ $enrollment->total_modules_count }} modules</span>
                </div>
                <div class="progress" style="height: 8px; margin: 10px 0;">
                    <div class="progress-bar" role="progressbar" 
                        style="width: {{ $enrollment->completion_percentage }}%;"
                        aria-valuenow="{{ $enrollment->completion_percentage }}" 
                        aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
            </div>
            <div class="progress-card-footer">
                <a href="{{ route('student.modules.all', ['class' => $enrollment->class_id]) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-play-circle"></i> Continue 
                </a>
                <span class="last-activity">Last activity: {{ $enrollment->updated_at->diffForHumans() }}</span>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="empty-state">
        <i class="fas fa-graduation-cap"></i>
        <p>You are not enrolled in any classes yet.</p>
    </div>
    @endif
</div>

@endsection
