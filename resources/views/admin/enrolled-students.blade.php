@extends('layouts.admin')

@section('alerts')
@section('alerts')
@if(session('success'))
    <div class="alert alert-success" id="successAlert">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif
@endsection

@section('content')
    <section class="content-section">
        <div class="table-actions">
            <h2 class="section-title">Enrolled Students</h2>
        </div>
        
        <div class="search-filter">
            <div class="header-search">
                <i class="fas fa-search"></i>
                <input type="text" id="studentSearch" placeholder="Search students..." onkeyup="filterStudents()">
            </div>
            <select id="classFilter" onchange="filterStudents()">
                <option value="">All Classes</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->title }}</option>
                @endforeach
            </select>
            <select id="statusFilter" onchange="filterStudents()">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="pending">Pending</option>
            </select>
        </div>

        <div class="table-container">
            <table class="table" id="studentsTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Class</th>
                        <th>Status</th>
                        <th>Completion</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($enrollments as $enrollment)
                        <tr data-class="{{ $enrollment->class->id ?? '' }}" data-status="{{ $enrollment->status }}">
                            <td>{{ $enrollment->student->name }}</td>
                            <td>{{ $enrollment->student->email }}</td>
                            <td>{{ $enrollment->class->title ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $enrollment->status === 'active' ? 'success' : ($enrollment->status === 'pending' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($enrollment->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="progress" style="height: 18px; min-width: 90px;">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{ $enrollment->completion_percentage ?? 0 }}%;"
                                         aria-valuenow="{{ $enrollment->completion_percentage ?? 0 }}"
                                         aria-valuemin="0" aria-valuemax="100">
                                        {{ $enrollment->completion_percentage ?? 0 }}%
                                    </div>
                                </div>
                            </td>
                            <td>
                                <button
                                    class="btn btn-sm btn-primary"
                                    onclick="fetchAndShowEnrollment({{ $enrollment->id }})"
                                >
                                    View
                                </button>
                            </td>
                        </tr>

                        <!-- Edit Enrollment Modal -->
                        <div class="modal fade" id="editEnrollmentModal-{{ $enrollment->id }}">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('admin.enrollments.update', $enrollment->id) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5>Edit Enrollment</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <select name="status" class="form-control">
                                                <option value="active" {{ $enrollment->status === 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="pending" {{ $enrollment->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                            </select>
                                            <input type="number" name="completion_percentage" class="form-control mt-2"
                                                value="{{ $enrollment->status === 'pending' ? 0 : $enrollment->completion_percentage }}">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Save</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if($enrollments->hasPages())
            <div class="pagination-container">
                <div class="pagination-info">
                    Showing {{ $enrollments->firstItem() }} to {{ $enrollments->lastItem() }} of {{ $enrollments->total() }} results
                </div>
                <div class="pagination-nav">
                    <button class="pagination-nav-btn" {{ $enrollments->onFirstPage() ? 'disabled' : '' }}
                        onclick="window.location='{{ $enrollments->previousPageUrl() }}'">
                        « Previous
                    </button>
                    <ul class="pagination">
                        @foreach ($enrollments->getUrlRange(1, $enrollments->lastPage()) as $page => $url)
                            @if ($page == $enrollments->currentPage())
                                <li class="active"><span>{{ $page }}</span></li>
                            @else
                                <li><a href="{{ $url }}">{{ $page }}</a></li>
                            @endif
                        @endforeach
                    </ul>
                    <button class="pagination-nav-btn" {{ !$enrollments->hasMorePages() ? 'disabled' : '' }}
                        onclick="window.location='{{ $enrollments->nextPageUrl() }}'">
                        Next »
                    </button>
                </div>
            </div>
        @endif
    </section>
@endsection

@push('scripts')
<script>
    // Filter students by search input, class and status
    function filterStudents() {
        const searchValue = document.getElementById('studentSearch').value.toLowerCase();
        const classFilter = document.getElementById('classFilter').value;
        const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
        const rows = document.querySelectorAll('#studentsTable tbody tr');
        
        rows.forEach(row => {
            const nameCell = row.cells[0].textContent.toLowerCase();
            const emailCell = row.cells[1].textContent.toLowerCase();
            const classMatch = classFilter === '' || row.getAttribute('data-class') === classFilter;
            const statusMatch = statusFilter === '' || row.getAttribute('data-status') === statusFilter;
            const textMatch = nameCell.includes(searchValue) || emailCell.includes(searchValue);
            
            row.style.display = (textMatch && classMatch && statusMatch) ? '' : 'none';
        });
    }
    
    // Open the Enroll Student Modal
    function openEnrollStudentModal() {
        document.getElementById('enrollmentForm').reset();
        document.getElementById('enrollmentId').value = '';
        document.getElementById('enrollmentModalTitle').textContent = 'Enroll New Student';
        document.getElementById('enrollmentForm').action = "{{ route('admin.enrollments.store') }}";
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('enrollmentModal').classList.add('active');
    }
    
    // Open the Edit Enrollment Modal
    function openEditEnrollmentModal(button) {
        const id = button.getAttribute('data-id');
        const studentId = button.getAttribute('data-student');
        const classId = button.getAttribute('data-class');
        const status = button.getAttribute('data-status');
        const date = button.getAttribute('data-date'); // get enrollment date

        document.getElementById('enrollmentId').value = id;
        document.getElementById('studentSelect').value = studentId;
        document.getElementById('classSelect').value = classId;
        document.getElementById('statusSelect').value = status;
        document.getElementById('enrollmentDate').value = date || ''; // set the date

        document.getElementById('enrollmentModalTitle').textContent = 'Edit Enrollment';
        document.getElementById('enrollmentForm').action = "/admin/enrollments/" + id;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('enrollmentModal').classList.add('active');
    }
    
    // Open the Delete Enrollment Modal
    function openDeleteEnrollmentModal(button) {
        const id = button.getAttribute('data-id');
        const studentName = button.getAttribute('data-student');
        
        document.querySelector('#deleteEnrollmentModal p').textContent = 
            `Are you sure you want to delete the enrollment for "${studentName}"? This action cannot be undone.`;
        
        document.getElementById('deleteEnrollmentForm').action = "/admin/enrollments/" + id;
        
        document.getElementById('deleteEnrollmentModal').classList.add('active');
    }
    
    // Change enrollment status
    function changeEnrollmentStatus(id, status) {
        // Create form data
        const formData = new FormData();
        formData.append('status', status);
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'PATCH');
        
        // Send AJAX request
        fetch(`/admin/enrollments/${id}/status`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = window.location.pathname;
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the status');
        });
    }
    
    // Fetch and show enrollment details
    function fetchAndShowEnrollment(id) {
        fetch(`/admin/enrollments/${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    openViewEnrollmentModal(data.enrollment);
                } else {
                    alert('Could not load enrollment details.');
                }
            })
            .catch(() => alert('Could not load enrollment details.'));
    }
    
    // Close modals when cancel buttons are clicked
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.modal .btn-outline').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('.modal').classList.remove('active');
            });
        });
    });
</script>
@endpush