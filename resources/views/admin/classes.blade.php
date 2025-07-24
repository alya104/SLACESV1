@extends('layouts.admin')
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
        <div class="table-actions">
            <h2 class="section-title">Manage Classes</h2>
            <button class="btn btn-primary" id="addClassBtn" onclick="openAddClassModal()">
                <i class="fas fa-plus"></i> Add New Class
            </button>
        </div>
        
        <div class="search-filter">
            <div class="header-search">
                <i class="fas fa-search"></i>
                <input type="text" id="classSearch" placeholder="Search classes..." onkeyup="filterClasses()">
            </div>
            <select id="statusFilter" onchange="filterClasses()">
                <option value="">All Status</option>
                <option value="available">Available</option>
                <option value="unavailable">Unavailable</option>
                <option value="coming">Coming Soon</option>
            </select>
        </div>

        <div class="view-toggle">
            <button class="btn btn-light" id="cardViewBtn"><i class="fas fa-th-large"></i> Card View</button>
            <button class="btn btn-light" id="listViewBtn"><i class="fas fa-list"></i> List View</button>
        </div>

        <div class="table-container">
            <table class="table" id="classesTable">
                <thead>
                    <tr>
                        <th>Class Name</th>
                        <th>Description</th>
                        <th>Created Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classes as $class)
                    <tr data-status="{{ $class->status }}">
                        <td>{{ $class->title }}</td>
                        <td>{{ Str::limit($class->description, 50) }}</td>
                        <td>{{ $class->created_at->format('Y-m-d') }}</td>
                        <td>
                            @if($class->status === 'active')
                                <span class="status-badge status-active">Active</span>
                            @elseif($class->status === 'inactive')
                                <span class="status-badge status-inactive">Inactive</span>
                            @elseif($class->status === 'upcoming')
                                <span class="status-badge status-upcoming">Upcoming</span>
                            @else
                                <span class="status-badge">{{ ucfirst($class->status) }}</span>
                            @endif
                        </td>
                        <td>
                            <button type="button" class="action-btn view-class-btn" title="View"
                                data-title="{{ $class->title }}"
                                data-status="{{ ucfirst($class->status) }}"
                                data-date="{{ $class->created_at->format('Y-m-d') }}"
                                data-description="{{ $class->description }}"
                                data-thumbnail="{{ $class->thumbnail }}"
                                onclick="openViewClassModal(this)">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn edit-class-btn" 
                                    title="Edit" 
                                    data-id="{{ $class->id }}"
                                    data-title="{{ $class->title }}"
                                    data-description="{{ $class->description }}"
                                    data-status="{{ $class->status }}"
                                    data-date="{{ $class->created_at->format('Y-m-d') }}"
                                    onclick="openEditClassModal(this)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn delete-class-btn" 
                                    title="Delete" 
                                    data-id="{{ $class->id }}"
                                    data-title="{{ $class->title }}"
                                    onclick="openDeleteClassModal(this)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">No classes found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($classes->hasPages())
        <div class="pagination-container">
            {{ $classes->links() }}
        </div>
        @endif

        <div class="classes-grid" id="adminClassesGrid" style="display:none;">
            @forelse($classes as $class)
                <div class="class-card" data-title="{{ strtolower($class->title) }}" data-status="{{ $class->status }}">
                    <div class="class-thumbnail">
                        <img src="{{ asset('storage/' . $class->thumbnail) }}" alt="{{ $class->title }}">
                        <div class="class-status-badge status-{{ $class->status }}">
                            {{ ucfirst($class->status) }}
                        </div>
                    </div>
                    <div class="class-content">
                        <h3 class="class-title">{{ $class->title }}</h3>
                        <p class="class-description">{{ Str::limit($class->description, 100) }}</p>
                        <span class="created-date"><i class="fas fa-calendar-alt"></i> {{ $class->created_at->format('Y-m-d') }}</span>
                    </div>
                    <div class="class-footer">
                        <button type="button" class="action-btn view-class-btn" title="View"
                            data-title="{{ $class->title }}"
                            data-status="{{ ucfirst($class->status) }}"
                            data-date="{{ $class->created_at->format('Y-m-d') }}"
                            data-description="{{ $class->description }}"
                            data-thumbnail="{{ $class->thumbnail }}"
                            onclick="openViewClassModal(this)">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="action-btn edit-class-btn" 
                                title="Edit" 
                                data-id="{{ $class->id }}"
                                data-title="{{ $class->title }}"
                                data-description="{{ $class->description }}"
                                data-status="{{ $class->status }}"
                                data-date="{{ $class->created_at->format('Y-m-d') }}"
                                onclick="openEditClassModal(this)">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="action-btn delete-class-btn" 
                                title="Delete" 
                                data-id="{{ $class->id }}"
                                data-title="{{ $class->title }}"
                                onclick="openDeleteClassModal(this)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-graduation-cap"></i>
                    <p>No classes found</p>
                </div>
            @endforelse
        </div>
    </section>

    @include('admin.partials.modals', ['users' => $users])

@endsection

@push('scripts')
<script>
    window.classStoreUrl = "{{ route('admin.classes.store') }}";
</script>
<script>
    // Open the Add Class Modal
    function openAddClassModal() {
        // Reset the form
        document.getElementById('classForm').reset();
        document.getElementById('classId').value = '';
        document.getElementById('classModalTitle').textContent = 'Add New Class';
        document.getElementById('classForm').action = "{{ route('admin.classes.store') }}";
        document.getElementById('classMethod').value = 'POST';
        
        // Show the modal
        document.getElementById('classModal').classList.add('active');
    }
    
    // Open the Edit Class Modal
    function openEditClassModal(button) {
        const id = button.getAttribute('data-id');
        const title = button.getAttribute('data-title');
        const description = button.getAttribute('data-description');
        const status = button.getAttribute('data-status');
        const date = button.getAttribute('data-date');

        document.getElementById('classId').value = id;
        document.getElementById('className').value = title; // <-- use title, not btn.dataset.name
        document.getElementById('classDescription').value = description;
        document.getElementById('classStatus').value = status;
        document.getElementById('classDate').value = date;

        document.getElementById('classModalTitle').textContent = 'Edit Class';
        document.getElementById('classForm').action = `/admin/classes/${id}`;
        document.getElementById('classMethod').value = 'PUT';
        document.getElementById('classModal').classList.add('active');
    }
    
    // Open the Delete Class Modal
    function openDeleteClassModal(button) {
        const id = button.getAttribute('data-id');
        const title = button.getAttribute('data-title');
        
        // Set confirmation message
        document.querySelector('#deleteClassModal p').textContent = 
            `Are you sure you want to delete the class "${title}"? This action cannot be undone.`;
        
        // Set form action
        document.getElementById('deleteClassForm').action = `/admin/classes/${id}`;
        
        // Show the modal
        document.getElementById('deleteClassModal').classList.add('active');
    }
    
    // Filter classes by search input and status
    function filterClasses() {
        const searchValue = document.getElementById('classSearch').value.toLowerCase();
        const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
        const rows = document.querySelectorAll('#classesTable tbody tr');
        
        rows.forEach(row => {
            const titleCell = row.cells[0].textContent.toLowerCase();
            const descriptionCell = row.cells[1].textContent.toLowerCase();
            const statusMatch = statusFilter === '' || row.getAttribute('data-status') === statusFilter;
            const textMatch = titleCell.includes(searchValue) || 
                             descriptionCell.includes(searchValue);
            
            row.style.display = (textMatch && statusMatch) ? '' : 'none';
        });
    }
    
    // Close modals when cancel buttons are clicked
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('cancelClass').addEventListener('click', function() {
            document.getElementById('classModal').classList.remove('active');
        });
        
        document.getElementById('cancelDeleteClass').addEventListener('click', function() {
            document.getElementById('deleteClassModal').classList.remove('active');
        });
    });

    document.getElementById('cardViewBtn').onclick = function() {
        document.getElementById('adminClassesGrid').style.display = '';
        document.getElementById('classesTable').parentElement.style.display = 'none';
    };
    document.getElementById('listViewBtn').onclick = function() {
        document.getElementById('adminClassesGrid').style.display = 'none';
        document.getElementById('classesTable').parentElement.style.display = '';
    };
    // Set card view as default on page load
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('adminClassesGrid').style.display = '';
        document.getElementById('classesTable').parentElement.style.display = 'none';
    });
</script>
@endpush