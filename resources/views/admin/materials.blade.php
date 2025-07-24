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

@php
    $material = $material ?? null;
@endphp
<section class="content-section">
    <div class="section-header">
        <h2 class="section-title">
            <i class="fas fa-video"></i>
            Class Materials
        </h2>
        <div class="header-actions">
            <!-- Upload/Add Button -->
            <button class="btn" onclick="showAddMaterialModal()">
                <i class="fas fa-plus"></i>
                Upload New Material
            </button>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.materials') }}" id="materialFilterForm" class="filter-bar">
        <span class="filter-label">Filter by:</span>
        <select class="filter-select" name="class_id" onchange="this.form.submit()">
            <option value="all">All Classes</option>
            @foreach($classes as $class)
                <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->title }}</option>
            @endforeach
        </select>
        <select class="filter-select" name="type" onchange="this.form.submit()">
            <option value="all">All Types</option>
            <option value="video" {{ request('type') == 'video' ? 'selected' : '' }}>Videos</option>
            <option value="pdf" {{ request('type') == 'pdf' ? 'selected' : '' }}>PDFs</option>
            <option value="link" {{ request('type') == 'link' ? 'selected' : '' }}>Links</option>
        </select>
        <input type="text" class="search-input" name="search" placeholder="Search materials..." value="{{ request('search') }}" onkeyup="if(event.key==='Enter'){this.form.submit();}">
    </form>

    <div class="material-grid" id="materialGrid">
        @forelse($materials as $material)
            <div class="material-card"
                data-title="{{ strtolower($material->title) }}"
                data-class="{{ $material->class_id }}"
                data-type="{{ $material->type }}"
            >
                <div class="material-thumbnail">
                    @if($material->type === 'video')
                        <div style="position:relative; width:100%; height:130px; display:flex; align-items:center; justify-content:center; overflow:hidden;">
                            @if($material->thumbnail)
                                <img src="{{ asset('storage/' . $material->thumbnail) }}" alt="Video thumbnail" style="width:100%; height:100%; object-fit:cover;">
                            @else
                                <div class="material-thumbnail-placeholder video-placeholder" style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:#f3f0fa;">
                                    <i class="fas fa-video"></i>
                                    <span>Video</span>
                                </div>
                            @endif
                            <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%);">
                                <i class="fas fa-play-circle" style="font-size:2.5rem; color:white; text-shadow:0 0 5px rgba(0,0,0,0.5);"></i>
                            </div>
                        </div>
                        <span class="material-type">Video</span>
                        @if($material->duration)
                            <span class="video-duration">{{ $material->duration }} min</span>
                        @endif
                    @elseif($material->type === 'pdf')
                        @if($material->thumbnail)
                            <img src="{{ asset('storage/' . $material->thumbnail) }}" alt="PDF thumbnail">
                        @else
                            <div class="material-thumbnail-placeholder pdf-placeholder">
                                <i class="fas fa-file-pdf"></i>
                                <span>PDF</span>
                            </div>
                        @endif
                        <span class="material-type">PDF</span>
                    @elseif($material->type === 'link')
                        @if($material->thumbnail)
                            <img src="{{ asset('storage/' . $material->thumbnail) }}" alt="Link thumbnail">
                        @else
                            <div class="material-thumbnail-placeholder link-placeholder">
                                <i class="fas fa-link"></i>
                                <span>Link</span>
                            </div>
                        @endif
                        <span class="material-type">Link</span>
                    @endif
                </div>
                <div class="material-content">
                    <h3 class="material-title">{{ $material->title }}</h3>
                    <div class="material-class">
                        <i class="fas fa-book"></i>
                        {{ $material->class->title ?? '-' }}
                    </div>
                </div>
                <div class="material-footer">
                    <div class="material-stats">
                        <span class="material-stat">
                            <i class="fas fa-calendar-alt"></i>
                            {{ $material->created_at->format('M d, Y') }}
                        </span>
                    </div>
                    <div class="material-actions">
                        <button class="btn btn-preview-material" data-material-id="{{ $material->id }}">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-edit-material" data-material-id="{{ $material->id }}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-delete-material" 
                                data-material-id="{{ $material->id }}" 
                                data-material-title="{{ $material->title }}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state" style="grid-column: 1/-1;">
                <i class="fas fa-folder-open"></i>
                <p>No materials found.</p>
            </div>
        @endforelse
    </div>

    <div class="pagination-container">
        <div class="pagination-info">
            Showing {{ $materials->firstItem() }} to {{ $materials->lastItem() }} of {{ $materials->total() }} results
        </div>
        <div class="pagination-nav">
            <button class="pagination-nav-btn" {{ $materials->onFirstPage() ? 'disabled' : '' }}
                onclick="window.location='{{ $materials->previousPageUrl() }}'" >
                « Previous
            </button>
            <ul class="pagination">
                @foreach ($materials->getUrlRange(1, $materials->lastPage()) as $page => $url)
                    @if ($page == $materials->currentPage())
                        <li class="active"><span>{{ $page }}</span></li>
                    @else
                        <li><a href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            </ul>
            <button class="pagination-nav-btn" {{ !$materials->hasMorePages() ? 'disabled' : '' }}
                onclick="window.location='{{ $materials->nextPageUrl() }}'" >
                Next »
            </button>
        </div>
    </div>
</section>

@foreach($materials as $material)
    @include('admin.partials.modals', ['material' => $material])
@endforeach

@endsection

@push('scripts')
<script>
// Filter function for search and dropdowns
function filterMaterials() {
    const search = document.getElementById('materialSearch').value.toLowerCase();
    const classFilter = document.getElementById('classFilter').value;
    const typeFilter = document.getElementById('typeFilter').value;
    document.querySelectorAll('.material-card').forEach(card => {
        const title = card.dataset.title;
        const classId = card.dataset.class;
        const type = card.dataset.type;
        const matchTitle = title.includes(search);
        const matchClass = classFilter === 'all' || classId === classFilter;
        const matchType = typeFilter === 'all' || type === typeFilter;
        card.style.display = (matchTitle && matchClass && matchType) ? '' : 'none';
    });
}

/**
 * Show the add material modal
 */
function showAddMaterialModal() {
    // Reset the form
    document.getElementById('materialForm').reset();
    
    // Set the title and action
    document.getElementById('materialModalTitle').textContent = 'Upload New Material';
    document.getElementById('materialForm').action = "{{ route('admin.materials.store') }}";
    
    // Reset method to POST
    let methodField = document.querySelector('#materialForm input[name="_method"]');
    if (methodField) {
        methodField.value = 'POST';
    } else {
        // If the method field doesn't exist, create it
        methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'POST';
        document.getElementById('materialForm').appendChild(methodField);
    }
    
    // Clear material ID if it exists
    const materialIdField = document.getElementById('materialId');
    if (materialIdField) {
        materialIdField.value = '';
    }
    
    // Hide all type specific fields
    if (document.getElementById('videoFields')) {
        document.getElementById('videoFields').style.display = 'none';
    }
    if (document.getElementById('pdfFields')) {
        document.getElementById('pdfFields').style.display = 'none';
    }
    if (document.getElementById('linkFields')) {
        document.getElementById('linkFields').style.display = 'none';
    }
    
    // Reset file name displays if they exist
    if (document.getElementById('videoFileName')) {
        document.getElementById('videoFileName').textContent = '';
    }
    if (document.getElementById('pdfFileName')) {
        document.getElementById('pdfFileName').textContent = '';
    }
    
    // Show the modal
    document.getElementById('materialModal').classList.add('active');
}

/**
 * Toggle display of type-specific fields based on selected type
 */
function toggleTypeFields() {
    const materialType = document.getElementById('materialType').value;
    
    // Hide all fields first
    if (document.getElementById('videoFields')) {
        document.getElementById('videoFields').style.display = 'none';
    }
    if (document.getElementById('pdfFields')) {
        document.getElementById('pdfFields').style.display = 'none';
    }
    if (document.getElementById('linkFields')) {
        document.getElementById('linkFields').style.display = 'none';
    }
    
    // Show fields based on selected type
    if (materialType === 'video' && document.getElementById('videoFields')) {
        document.getElementById('videoFields').style.display = 'block';
    } else if (materialType === 'pdf' && document.getElementById('pdfFields')) {
        document.getElementById('pdfFields').style.display = 'block';
    } else if (materialType === 'link' && document.getElementById('linkFields')) {
        document.getElementById('linkFields').style.display = 'block';
    }
}

/**
 * Show the edit material modal
 */
function showEditMaterialModal(id) {
    // Fetch material data first
    fetch(`/admin/materials/${id}/edit`)
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                alert('Error loading material data');
                return;
            }
            
            const material = data.material;
            
            // Reset the form
            document.getElementById('materialForm').reset();
            
            // Fill form with material data
            document.getElementById('materialTitle').value = material.title;
            document.getElementById('materialClass').value = material.class_id;
            document.getElementById('materialType').value = material.type;
            
            if (document.getElementById('materialDescription')) {
                document.getElementById('materialDescription').value = material.description || '';
            }
            
            // Set hidden ID field
            if (!document.getElementById('materialId')) {
                const idField = document.createElement('input');
                idField.type = 'hidden';
                idField.id = 'materialId';
                idField.name = 'id';
                document.getElementById('materialForm').appendChild(idField);
            }
            document.getElementById('materialId').value = material.id;
            
            // Handle type-specific fields
            if (material.type === 'link' && document.getElementById('materialLink')) {
                document.getElementById('materialLink').value = material.url || '';
            }
            
            // Update modal title and form action
            document.getElementById('materialModalTitle').textContent = 'Edit Material';
            document.getElementById('materialForm').action = `/admin/materials/${id}`;
            
            // Set method to PUT
            let methodField = document.querySelector('#materialForm input[name="_method"]');
            if (!methodField) {
                methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                document.getElementById('materialForm').appendChild(methodField);
            }
            methodField.value = 'PUT';
            
            // Toggle type fields
            toggleTypeFields();
            
            // Show the modal
            document.getElementById('materialModal').classList.add('active');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while loading the material data');
        });
}

/**
 * Show material preview modal
 */
function showPreviewModal(id) {
    // Fetch material data
    fetch(`/admin/materials/${id}/preview`)
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                alert('Error loading preview data');
                return;
            }
            
            const material = data.material;
            document.getElementById('previewTitle').textContent = material.title;
            document.getElementById('previewClass').textContent = material.class.title;
            document.getElementById('previewType').textContent = material.type.charAt(0).toUpperCase() + material.type.slice(1);

            // *** Fix: Set the description! ***
            document.getElementById('previewMaterialDescription').textContent = material.description || '';
            document.getElementById('previewMaterialDescription').style.display = 'block';

            // Handle different material types
            const previewContent = document.getElementById('previewContent');
            previewContent.innerHTML = '';

            if (material.type === 'video') {
                // Youtube
                if (material.url && (material.url.includes('youtube') || material.url.includes('youtu.be'))) {
                    const match = material.url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/);
                    if (match && match[1]) {
                        const youtubeId = match[1];
                        previewContent.innerHTML = `
                            <iframe width="100%" height="400" src="https://www.youtube.com/embed/${youtubeId}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        `;
                    } else {
                        previewContent.innerHTML = `<p>Invalid YouTube URL.</p>`;
                    }
                }
                // Vimeo
                else if (material.url && material.url.includes('vimeo')) {
                    const match = material.url.match(/vimeo\.com\/(\d+)/);
                    if (match && match[1]) {
                        const vimeoId = match[1];
                        previewContent.innerHTML = `
                            <iframe src="https://player.vimeo.com/video/${vimeoId}" width="100%" height="400" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                        `;
                    } else {
                        previewContent.innerHTML = `<p>Invalid Vimeo URL.</p>`;
                    }
                }
                // Local video
                else if (material.file_path) {
                    previewContent.innerHTML = `
                        <video controls width="100%" height="400">
                            <source src="/storage/${material.file_path}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    `;
                }
                // Fallback to thumbnail
                else if (material.thumbnail) {
                    previewContent.innerHTML = `
                        <img src="/storage/${material.thumbnail}" alt="Video thumbnail" style="width:100%;max-width:350px;object-fit:cover;">
                    `;
                }
            }
            else if (material.type === 'pdf') {
                previewContent.innerHTML = `
                    <div class="pdf-container">
                        <iframe 
                            src="/pdfjs/web/viewer.html?file=${encodeURIComponent('/storage/' + material.file_path)}" 
                            width="100%" 
                            height="500" 
                            style="border: none; border-radius: 8px;">
                        </iframe>
                    </div>
                `;
            }
            else if (material.type === 'link') {
                previewContent.innerHTML = `
                    <div class="link-preview">
                        <a href="${material.url}" target="_blank" class="material-link">
                            <i class="fas fa-external-link-alt"></i> ${material.url}
                        </a>
                        <p>Click the link above to open in a new tab</p>
                    </div>
                `;
            }
            else if (material.type === 'image') {
                previewContent.innerHTML = `
                    <div class="image-preview">
                        <img src="/storage/${material.file_path}" alt="${material.title}">
                    </div>
                `;
            }
            else {
                previewContent.innerHTML = `
                    <div class="unsupported-preview">
                        <p>Preview not available for this type of material.</p>
                        <a href="/storage/${material.file_path}" class="btn" download>
                            <i class="fas fa-download"></i> Download Material
                        </a>
                    </div>
                `;
            }

            // Show the modal
            document.getElementById('previewModal').classList.add('active');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while loading the preview');
        });
}

/**
 * Open the delete material confirmation modal
 */
function openDeleteMaterialModal(deleteUrl, materialTitle) {
    // Set the form action
    document.getElementById('deleteMaterialForm').action = deleteUrl;
    
    // Update the confirmation message to include the material title
    document.querySelector('#deleteMaterialModal p').textContent = 
        `Are you sure you want to delete "${materialTitle}"? This action cannot be undone.`;
    
    // Show the modal
    document.getElementById('deleteMaterialModal').classList.add('active');
}

// Initialize when document is ready
document.addEventListener('DOMContentLoaded', function() {
    // Close modals when cancel buttons are clicked
    document.querySelectorAll('.modal .btn-outline, .modal .close').forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.modal').classList.remove('active');
        });
    });
    
    // Setup preview buttons
    document.querySelectorAll('.btn-preview-material').forEach(button => {
        button.addEventListener('click', function() {
            const materialId = this.getAttribute('data-material-id');
            showPreviewModal(materialId);
        });
    });

    // Setup edit buttons
    document.querySelectorAll('.btn-edit-material').forEach(button => {
        button.addEventListener('click', function() {
            const materialId = this.getAttribute('data-material-id');
            showEditMaterialModal(materialId);
        });
    });

    // Setup delete buttons
    document.querySelectorAll('.btn-delete-material').forEach(button => {
        button.addEventListener('click', function() {
            const materialId = this.getAttribute('data-material-id');
            const materialTitle = this.getAttribute('data-material-title');
            openDeleteMaterialModal(`/admin/materials/${materialId}`, materialTitle);
        });
    });
    
    // Find all success messages
    const successMessages = document.querySelectorAll('.alert-success, .alert:contains("success")');
    
    // If there's more than one, remove all except the first one
    if (successMessages.length > 1) {
        for (let i = 1; i < successMessages.length; i++) {
            successMessages[i].remove();
        }
    }
    
    // Log all success messages for debugging
    console.log('Checking for success messages...');
    const allMessages = document.querySelectorAll('*');
    let found = 0;
    
    allMessages.forEach(el => {
        if (el.textContent && el.textContent.includes('successfully')) {
            found++;
            console.log('Found message #' + found + ':', el);
            console.log('Parent:', el.parentElement);
            
            // Mark it for visual identification
            el.style.border = '2px solid red';
        }
    });
    
    console.log('Total messages found:', found);
});

document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit to ensure all content is fully loaded
    setTimeout(() => {
        // Find all text nodes containing "successfully"
        const allTextNodes = [];
        
        function findTextNodes(node) {
            if (node.nodeType === 3) { // Text node
                if (node.textContent.includes('successfully')) {
                    allTextNodes.push(node);
                }
            } else {
                for (let i = 0; i < node.childNodes.length; i++) {
                    findTextNodes(node.childNodes[i]);
                }
            }
        }
        
        findTextNodes(document.body);
        console.log('Found text nodes:', allTextNodes.length);
        
        // Keep only the first success message that's in your styled alert
        let keptFirst = false;
        allTextNodes.forEach(node => {
            // Check if this node is inside our styled alert
            if (node.parentElement && node.parentElement.closest('.alert-success')) {
                if (!keptFirst) {
                    keptFirst = true;
                    console.log('Keeping this success message:', node.textContent);
                } else {
                    // This is a duplicate inside an alert-success, remove its container
                    const alertElement = node.parentElement.closest('.alert-success');
                    if (alertElement) {
                        console.log('Removing duplicate:', node.textContent);
                        alertElement.remove();
                    }
                }
            } else if (
                document.querySelector('.alert-success .alert-message') &&
                node.textContent.trim() === document.querySelector('.alert-success .alert-message').textContent.trim()
            ) {
                // This is the duplicate outside our styled alert
                // Find its container (up to 3 levels up) and remove it
                let parent = node.parentElement;
                for (let i = 0; i < 3; i++) {
                    if (parent) {
                        console.log('Removing duplicate container:', parent);
                        const toRemove = parent;
                        parent = parent.parentElement;
                        toRemove.remove();
                        break;
                    }
                }
            }
        });
    }, 100); // Small delay to ensure everything is rendered
});
</script>
@endpush

<style>
/* Section headers and filter components */
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1.2rem;
    margin-bottom: 1.5rem;
    padding: 0.5rem 0;
}

.section-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-dark);
    display: flex;
    align-items: center;
    gap: 0.6rem;
}

.section-title i {
    color: var(--primary-color);
    font-size: 1.3rem;
}

.header-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
    flex-wrap: wrap;
}

.search-input {
    padding: 0.6rem 1.2rem;
    border: 1px solid #e0d2f7;
    border-radius: 8px;
    background: #f8f5fd;
    font-size: 1rem;
    color: #4a4a4a;
    min-width: 220px;
    margin-right: 0.5rem;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.search-input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(177, 156, 217, 0.15);
}

.filter-bar {
    display: flex;
    gap: 1rem;
    align-items: center;
    flex-wrap: wrap;
    margin-bottom: 1.5rem;
    padding: 0.5rem 0;
}

.filter-label {
    font-weight: 500;
    color: var(--primary-dark);
    font-size: 0.95rem;
}

.filter-select {
    padding: 0.6rem 1rem;
    border: 1px solid #e0d2f7;
    border-radius: 8px;
    background: #fff;
    font-size: 1rem;
    min-width: 140px;
    color: #4a4a4a;
    cursor: pointer;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.filter-select:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(177, 156, 217, 0.15);
}

/* Button styles - enhance existing ones */
.btn {
    padding: 0.65rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    background: #b19cd9;
    color: #fff;
    border: none;
    cursor: pointer;
    font-size: 1rem;
    transition: background 0.14s, box-shadow 0.14s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn:hover {
    background: #9370db;
    box-shadow: 0 4px 10px rgba(177, 156, 217, 0.3);
}

.btn i {
    font-size: 0.95rem;
}

/* Pagination styles */
.pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    margin-top: 1.5rem;
    padding: 0.5rem 0;
    border-top: 1px solid #e0d2f7;
}

.pagination-info {
    font-size: 0.95rem;
    color: #4a4a4a;
}

.pagination-nav {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.pagination-nav-btn {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-weight: 500;
    background: #f3f0fa;
    color: #4a4a4a;
    border: 1px solid #e0d2f7;
    cursor: pointer;
    font-size: 0.9rem;
    transition: background 0.14s, box-shadow 0.14s;
}

.pagination-nav-btn:hover {
    background: #e0d2f7;
    box-shadow: 0 2px 5px rgba(177, 156, 217, 0.2);
}

.pagination-nav-btn:disabled {
    background: #f9f9f9;
    color: #b0b0b0;
    cursor: not-allowed;
}

.pagination {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    list-style: none;
    padding: 0;
    margin: 0;
}

.pagination li {
    margin: 0;
}

.pagination a {
    display: block;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-weight: 500;
    background: #f3f0fa;
    color: #4a4a4a;
    text-decoration: none;
    transition: background 0.14s, box-shadow 0.14s;
}

.pagination a:hover {
    background: #e0d2f7;
    box-shadow: 0 2px 5px rgba(177, 156, 217, 0.2);
}

.pagination li.active span {
    display: block;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-weight: 600;
    background: var(--primary-color);
    color: #fff;
}

/* Responsive design */
@media (max-width: 768px) {
    .section-header {
        padding: 0.8rem 0;
    }
    
    .header-actions {
        width: 100%;
        justify-content: space-between;
    }
    
    .search-input {
        flex: 1;
        min-width: 150px;
    }
}

@media (max-width: 700px) {
    .section-header, 
    .header-actions, 
    .filter-bar {
        flex-direction: column;
        align-items: stretch;
        gap: 0.8rem;
    }
    
    .search-input {
        margin-right: 0;
        width: 100%;
        min-width: unset;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
    
    .filter-bar {
        padding-top: 0.5rem;
    }
    
    .filter-label {
        margin-bottom: 0.3rem;
    }
}
</style>