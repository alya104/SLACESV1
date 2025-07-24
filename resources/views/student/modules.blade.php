@extends('layouts.student')

@section('content')
<section class="content-section">
    <div class="section-header">
        <div>
            <h2 class="section-title">
                <i class="fas fa-book"></i>
                {{ $class->title ?? 'Learning Materials' }}
            </h2>
            <p class="section-subtitle">
                Browse and view your learning materials.
            </p>
        </div>
        <div class="header-actions">
            <div class="search-input-container">
                <input type="text" class="search-input" placeholder="Search materials..." id="materialSearch" onkeyup="filterMaterials()">
            </div>
        </div>
    </div>

    <div class="filter-bar">
        <span class="filter-label">Filter by:</span>
        @if(!isset($class))
        <select class="filter-select" id="classFilter" onchange="filterMaterials()">
            <option value="all">All Classes</option>
            @foreach($classes as $classItem)
                <option value="{{ $classItem->id }}">{{ $classItem->title }}</option>
            @endforeach
        </select>
        @endif
        <select class="filter-select" id="typeFilter" onchange="filterMaterials()">
            <option value="all">All Types</option>
            <option value="video">Videos</option>
            <option value="pdf">PDFs</option>
            <option value="link">Links</option>
        </select>
    </div>

    <div class="material-grid" id="materialGrid">
        {{-- Show modules for a specific class --}}
        @if(isset($class))
            @forelse($modules as $material)
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
                                    <div class="material-thumbnail-placeholder video-placeholder">
                                        <i class="fas fa-video"></i>
                                        <span>Video</span>
                                    </div>
                                @endif
                                <div class="play-button">
                                    <i class="fas fa-play-circle"></i>
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
                        <span class="material-type">
                            <i class="fas fa-file"></i>
                            {{ ucfirst($material->type) }}
                        </span>
                        <p class="material-description">{{ Str::limit($material->description, 80) }}</p>
                    </div>
                    <div class="material-footer">
                        <div class="material-stats">
                            <span class="material-stat">
                                <i class="fas fa-calendar-alt"></i>
                                {{ $material->created_at->format('M d, Y') }}
                            </span>
                        </div>
                        <div class="material-actions">
                            <button class="btn btn-primary btn-view-material" data-material-id="{{ $material->id }}">
                                <i class="fas fa-eye"></i> View
                            </button>
                            @php
                                $isDone = isset($completedMaterials) && in_array($material->id, $completedMaterials);
                            @endphp
                            @if(!$isDone)
                                <form method="POST" action="{{ route('student.classes.progress.mark', ['class_id' => $material->class_id, 'module_id' => $material->id]) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fas fa-check"></i> Mark as Done
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('student.classes.progress.unmark', ['class_id' => $material->class_id, 'module_id' => $material->id]) }}" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-warning btn-sm">
                                        <i class="fas fa-undo"></i> Mark as Undone
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-folder-open"></i>
                    <p>No materials found for this class.</p>
                </div>
            @endforelse
        @else
            {{-- Show all materials from all classes --}}
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
                                    <div class="material-thumbnail-placeholder video-placeholder">
                                        <i class="fas fa-video"></i>
                                        <span>Video</span>
                                    </div>
                                @endif
                                <div class="play-button">
                                    <i class="fas fa-play-circle"></i>
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
                        <span class="material-type">
                            <i class="fas fa-file"></i>
                            {{ ucfirst($material->type) }}
                        </span>
                        <p class="material-description">{{ Str::limit($material->description, 80) }}</p>
                    </div>
                    <div class="material-footer">
                        <div class="material-stats">
                            <span class="material-stat">
                                <i class="fas fa-calendar-alt"></i>
                                {{ $material->created_at->format('M d, Y') }}
                            </span>
                        </div>
                        <div class="material-actions">
                            <button class="btn btn-primary btn-view-material" data-material-id="{{ $material->id }}">
                                <i class="fas fa-eye"></i> View
                            </button>
                            @php
                                $isDone = isset($completedMaterials) && in_array($material->id, $completedMaterials);
                            @endphp
                            @if(!$isDone)
                                <form method="POST" action="{{ route('student.classes.progress.mark', ['class_id' => $material->class_id, 'module_id' => $material->id]) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fas fa-check"></i> Mark as Done
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('student.classes.progress.unmark', ['class_id' => $material->class_id, 'module_id' => $material->id]) }}" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-warning btn-sm">
                                        <i class="fas fa-undo"></i> Mark as Undone
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-folder-open"></i>
                    <p>No materials found for your enrolled classes.</p>
                </div>
            @endforelse
        @endif
    </div>

    {{-- Place pagination OUTSIDE the grid --}}
    @if(isset($class))
        @if($modules instanceof \Illuminate\Pagination\LengthAwarePaginator && $modules->hasPages())
            <div class="pagination-container">
                <div class="pagination-info">
                    Showing {{ $modules->firstItem() }} to {{ $modules->lastItem() }} of {{ $modules->total() }} results
                </div>
                <div class="pagination-nav">
                    <button class="pagination-nav-btn" {{ $modules->onFirstPage() ? 'disabled' : '' }}
                        onclick="window.location='{{ $modules->previousPageUrl() }}'">
                        « Previous
                    </button>
                    <ul class="pagination">
                        @foreach ($modules->getUrlRange(1, $modules->lastPage()) as $page => $url)
                            @if ($page == $modules->currentPage())
                                <li class="active"><span>{{ $page }}</span></li>
                            @else
                                <li><a href="{{ $url }}">{{ $page }}</a></li>
                            @endif
                        @endforeach
                    </ul>
                    <button class="pagination-nav-btn" {{ !$modules->hasMorePages() ? 'disabled' : '' }}
                        onclick="window.location='{{ $modules->nextPageUrl() }}'">
                        Next »
                    </button>
                </div>
            </div>
        @endif
    @else
        @if($materials->hasPages())
            <div class="pagination-container">
                <div class="pagination-info">
                    Showing {{ $materials->firstItem() }} to {{ $materials->lastItem() }} of {{ $materials->total() }} results
                </div>
                <div class="pagination-nav">
                    <button class="pagination-nav-btn" {{ $materials->onFirstPage() ? 'disabled' : '' }}
                        onclick="window.location='{{ $materials->previousPageUrl() }}'">
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
                        onclick="window.location='{{ $materials->nextPageUrl() }}'">
                        Next »
                    </button>
                </div>
            </div>
        @endif
    @endif

    <div class="enrollment-actions">
        @if(isset($class) && $class)
            <a href="{{ route('student.modules', ['class_id' => $class->id]) }}" class="btn btn-primary">
                @if(isset($enrollment) && $enrollment->completion_percentage > 0)
                    <i class="fas fa-play-circle"></i> Continue Learning
                @else
                    <i class="fas fa-play-circle"></i> Start Learning
                @endif
            </a>
        @endif
    </div>
    
</section>

<!-- Material Preview Modal -->
<div class="modal" id="materialPreviewModal">
    <div class="modal-content">
        <h3 class="modal-title" id="previewTitle">Material Preview</h3>
        <div id="previewContent" class="preview-content"></div>
        <div class="preview-info">
            <h4 class="preview-title" id="previewMaterialTitle"></h4>
            <div class="preview-meta">
                <span>Class: <span id="previewClass"></span></span>
                <span>Type: <span id="previewType"></span></span>
            </div>
            <p class="preview-description" id="previewMaterialDescription"></p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" id="closePreview">Close</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function filterMaterials() {
    const searchValue = document.getElementById('materialSearch').value.toLowerCase();
    const typeFilter = document.getElementById('typeFilter').value;
    const classFilter = document.getElementById('classFilter') ? document.getElementById('classFilter').value : 'all';
    
    document.querySelectorAll('.material-card').forEach(card => {
        const title = card.dataset.title;
        const type = card.dataset.type;
        const classId = card.dataset.class;
        
        const matchTitle = title.includes(searchValue);
        const matchType = typeFilter === 'all' || type === typeFilter;
        const matchClass = classFilter === 'all' || classId === classFilter;
        
        card.style.display = (matchTitle && matchType && matchClass) ? '' : 'none';
    });
}

// Open material preview modal
function openMaterialPreview(materialId) {
    const modal = document.getElementById('materialPreviewModal');
    const previewContent = document.getElementById('previewContent');
    previewContent.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> Loading preview...</div>';

    // Show the modal
    modal.classList.add('active');

    // Fetch material data
    fetch(`/student/modules/${materialId}/preview`)
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                previewContent.innerHTML = '<div class="error-message">Error loading preview</div>';
                return;
            }

            const material = data.material;

            // Set modal info
            document.getElementById('previewTitle').textContent = material.title || '';
            document.getElementById('previewMaterialTitle').textContent = material.title || '';
            document.getElementById('previewClass').textContent = material.class ? material.class.title : '';
            document.getElementById('previewType').textContent = material.type ? material.type.charAt(0).toUpperCase() + material.type.slice(1) : '';
            document.getElementById('previewMaterialDescription').innerHTML = (material.description || '').replace(/\n/g, '<br>');
            document.getElementById('previewMaterialDescription').style.display = 'block';

            // Render content based on material type (admin-style)
            if (material.type === 'video') {
                if (material.thumbnail && material.file_path) {
                    previewContent.innerHTML = `
                        <div class="video-thumbnail-container" style="text-align:center;">
                            <video controls width="100%" height="350" poster="/storage/${material.thumbnail}" style="border-radius:8px;box-shadow:0 4px 8px rgba(0,0,0,0.1);">
                                <source src="/storage/${material.file_path}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    `;
                } else if (material.url && (material.url.includes('youtube') || material.url.includes('youtu.be'))) {
                    const match = material.url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/);
                    if (match && match[1]) {
                        const youtubeId = match[1];
                        previewContent.innerHTML = `
                            <iframe width="100%" height="300" src="https://www.youtube.com/embed/${youtubeId}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        `;
                    } else {
                        previewContent.innerHTML = `<p>Invalid YouTube URL.</p>`;
                    }
                } else if (material.url && material.url.includes('vimeo')) {
                    const match = material.url.match(/vimeo\.com\/(\d+)/);
                    if (match && match[1]) {
                        const vimeoId = match[1];
                        previewContent.innerHTML = `
                            <iframe src="https://player.vimeo.com/video/${vimeoId}" width="100%" height="300" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                        `;
                    } else {
                        previewContent.innerHTML = `<p>Invalid Vimeo URL.</p>`;
                    }
                } else if (material.file_path) {
                    previewContent.innerHTML = `
                        <video controls width="100%" height="300">
                            <source src="/storage/${material.file_path}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    `;
                } else {
                    previewContent.innerHTML = `<p>No video preview available.</p>`;
                }
            } else if (material.type === 'pdf') {
                const pdfUrl = `/storage/${material.file_path}`;
                const viewerUrl = `/pdfjs/web/viewer.html?file=${encodeURIComponent(pdfUrl)}`;
                previewContent.innerHTML = `
                    <div class="pdf-container">
                        <iframe 
                            src="${viewerUrl}" 
                            width="100%" 
                            height="500" 
                            style="border: none; border-radius: 8px;">
                        </iframe>
                        <a href="${pdfUrl}" target="_blank" class="btn" style="margin-top:10px; display:block; text-align:center; background:#6334a7; color:white; text-decoration:none; padding:8px; border-radius:5px;">
                            <i class="fas fa-download"></i> Download PDF
                        </a>
                    </div>
                `;
            } else if (material.type === 'link') {
                previewContent.innerHTML = `
                    <div class="link-preview">
                        <a href="${material.url}" target="_blank" class="material-link">
                            <i class="fas fa-external-link-alt"></i> ${material.url}
                        </a>
                        <p>Click the link above to open in a new tab</p>
                    </div>
                `;
            } else if (material.type === 'image') {
                previewContent.innerHTML = `
                    <div class="image-preview">
                        <img src="/storage/${material.file_path}" alt="${material.title}">
                    </div>
                `;
            } else {
                previewContent.innerHTML = `
                    <div class="unsupported-preview">
                        <p>Preview not available for this type of material.</p>
                        <a href="/storage/${material.file_path}" class="btn" download>
                            <i class="fas fa-download"></i> Download Material
                        </a>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            previewContent.innerHTML = '<div class="error-message">Error loading preview</div>';
        });
}

document.addEventListener('DOMContentLoaded', function() {
    // Set up view buttons
    document.querySelectorAll('.btn-view-material').forEach(button => {
        button.addEventListener('click', function() {
            const materialId = this.getAttribute('data-material-id');
            openMaterialPreview(materialId);
        });
    });
    
    // Close modal when close button is clicked
    document.getElementById('closePreview').addEventListener('click', function() {
        document.getElementById('materialPreviewModal').classList.remove('active');
    });
    
    // Close modal when clicking outside
    document.getElementById('materialPreviewModal').addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
        }
    });

    const urlParams = new URLSearchParams(window.location.search);
    const classFilter = urlParams.get('class');
    if (classFilter) {
        const filterSelect = document.getElementById('classFilter');
        if (filterSelect) {
            filterSelect.value = classFilter;
            filterMaterials();
        }
    }
});
</script>
@endpush
