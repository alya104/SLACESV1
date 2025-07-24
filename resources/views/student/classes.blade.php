@extends('layouts.student')

@section('content')
<section class="content-section">
    <div class="section-header">
        <h2 class="section-title">
            <i class="fas fa-graduation-cap"></i>
            My Classes
        </h2>
        <div class="header-actions">
            <div class="search-input-container">
                <input type="text" class="search-input" placeholder="Search classes..." id="classSearch" onkeyup="filterClasses()">
            </div>
        </div>
    </div>

    <div class="filter-bar">
        <span class="filter-label">Filter by:</span>
        <select class="filter-select" id="statusFilter" onchange="filterClasses()">
            <option value="all">All Status</option>
            <option value="active">Active</option>
            <option value="pending">Pending</option>
        </select>
    </div>

    <div class="classes-grid" id="classesGrid">
        @forelse($enrollments as $enrollment)
            <div class="class-card"
                data-title="{{ strtolower($enrollment->class->title) }}"
                data-status="{{ $enrollment->status }}"
            >
                <div class="class-thumbnail">
                    @if($enrollment->class->thumbnail)
                        <img src="{{ asset('storage/' . $enrollment->class->thumbnail) }}" alt="{{ $enrollment->class->title }}">
                    @else
                        <div class="class-thumbnail-placeholder">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                    @endif
                    <div class="class-status-badge status-{{ $enrollment->status }}">
                        {{ ucfirst($enrollment->status) }}
                    </div>
                </div>
                <div class="class-content">
                    <h3 class="class-title">{{ $enrollment->class->title }}</h3>
                    <p class="class-description">{{ Str::limit($enrollment->class->description, 100) }}</p>
                    
                    <div class="progress-container">
                        <div class="progress" style="height: 16px; background: #e9ecef; border-radius: 8px; overflow: hidden;">
                            <div class="progress-bar"
                                 role="progressbar"
                                 style="width: {{ $enrollment->completion_percentage }}%; background: #4f8cff; height: 100%; transition: width 0.4s;"
                                 aria-valuenow="{{ $enrollment->completion_percentage }}"
                                 aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                        <span class="progress-label" style="margin-left: 8px;">
                            Progress: {{ $enrollment->completion_percentage }}%
                        </span>
                    </div>
                </div>
                <div class="class-footer">
                    <a href="{{ route('student.modules.all', ['class' => $enrollment->class_id]) }}" class="btn btn-primary">
                        <i class="fas fa-play-circle"></i> Start Learning
                    </a>
                    <span class="enrollment-date">
                        <i class="fas fa-calendar-alt"></i> 
                        Enrolled: {{ $enrollment->enrollment_date->format('M d, Y') }}
                    </span>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-graduation-cap"></i>
                <p>You are not enrolled in any classes yet.</p>
            </div>
        @endforelse
    </div>
    
    @if($enrollments->hasPages())
        <div class="pagination-container">
            {{ $enrollments->links() }}
        </div>
    @endif

</section>
@endsection

@push('scripts')
<script>
function filterClasses() {
    const searchValue = document.getElementById('classSearch').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
    
    document.querySelectorAll('.class-card').forEach(card => {
        const title = card.dataset.title;
        const status = card.dataset.status;
        
        const matchTitle = title.includes(searchValue);
        const matchStatus = statusFilter === 'all' || status === statusFilter;
        
        card.style.display = (matchTitle && matchStatus) ? '' : 'none';
    });
}

function openViewClassModal(button) {
    const title = button.getAttribute('data-title');
    const status = button.getAttribute('data-status');
    const date = button.getAttribute('data-date');
    const description = button.getAttribute('data-description');
    const thumbnail = button.getAttribute('data-thumbnail');
    
    // Populate the modal fields with the class data
    document.getElementById('viewClassTitle').innerText = title;
    document.getElementById('viewClassStatus').innerText = status;
    document.getElementById('viewClassDate').innerText = date;
    document.getElementById('viewClassDescription').innerText = description;
    document.getElementById('viewClassThumbnail').src = thumbnail ? '/storage/' + thumbnail : '/images/default-thumbnail.png';
    
}
</script>
@endpush
