// Function to toggle appropriate fields based on material type
function toggleTypeFields() {
    const materialType = document.getElementById('materialType').value;
    document.getElementById('videoFields').style.display = materialType === 'video' ? 'block' : 'none';
    document.getElementById('pdfFields').style.display = materialType === 'pdf' ? 'block' : 'none';
    document.getElementById('linkFields').style.display = materialType === 'link' ? 'block' : 'none';
}

// Function to update file name display
function updateFileName(input, elementId) {
    const fileNameElement = document.getElementById(elementId);
    if (input.files.length > 0) {
        fileNameElement.textContent = input.files[0].name;
        fileNameElement.style.display = 'block';
    } else {
        fileNameElement.textContent = '';
        fileNameElement.style.display = 'none';
    }
}

// Function to preview a material
function previewMaterial(materialId) {
    // Show loading state
    const previewModal = document.getElementById('previewModal');
    const previewContent = document.getElementById('previewContent');
    previewContent.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> Loading preview...</div>';
    previewModal.classList.add('active');

    // Fetch material data
    fetch(`/admin/materials/${materialId}/preview`)
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                previewContent.innerHTML = '<div class="error-message">Error loading preview</div>';
                return;
            }

            const material = data.material;

            // Set meta and description
            document.getElementById('previewTitle').textContent = material.title || '';
            document.getElementById('previewMaterialTitle').textContent = material.title || '';
            document.getElementById('previewClass').textContent = material.class ? material.class.title : '';
            document.getElementById('previewType').textContent = material.type ? material.type.charAt(0).toUpperCase() + material.type.slice(1) : '';
            document.getElementById('previewMaterialDescription').textContent = material.description || '';
            document.getElementById('previewMaterialDescription').style.display = 'block';

            // Clear previous content
            previewContent.innerHTML = '';

            // Only set the preview area (video, pdf, image, etc.)
            if (material.type === 'video') {
                if (material.url && (material.url.includes('youtube') || material.url.includes('youtu.be'))) {
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
                } else if (material.thumbnail) {
                    previewContent.innerHTML = `
                        <img src="/storage/${material.thumbnail}" alt="Video thumbnail" style="width:100%;max-width:350px;object-fit:cover;">
                    `;
                } else {
                    previewContent.innerHTML = `<p>No video available.</p>`;
                }
            } else if (material.type === 'pdf') {
                // PDF preview
                console.log('PDF file path:', material.file_path);

                let pdfUrl = material.file_path.startsWith('/') ? material.file_path : '/storage/' + material.file_path;
                let encodedPdfUrl = encodeURIComponent(pdfUrl);

                previewContent.innerHTML = `
                    <div class="pdf-container">
                        <iframe 
                            src="/pdfjs/web/viewer.html?file=${encodedPdfUrl}" 
                            width="100%" 
                            height="500" 
                            style="border: none; border-radius: 8px;">
                        </iframe>
                        <div style="text-align:center; margin-top:10px;">
                            <a href="${pdfUrl}" target="_blank" style="color:#6334a7;">Open PDF in new tab</a>
                        </div>
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

            // Setup edit button to open edit modal
            document.getElementById('editFromPreview').onclick = function() {
                previewModal.classList.remove('active');
                editMaterial(material.id);
            };

            // Update description if available
            const descElem = document.getElementById('previewMaterialDescription');
            if (descElem) {
                descElem.textContent = material.description ?? '';
                descElem.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            previewContent.innerHTML = '<div class="error-message">Error loading preview</div>';
        });
}

// Function to edit a material
function editMaterial(materialId) {
    // Reset form
    document.getElementById('materialForm').reset();

    // Show loading state
    const materialModal = document.getElementById('materialModal');
    document.getElementById('materialModalTitle').textContent = 'Loading Material...';
    materialModal.classList.add('active');

    // Fetch material data
    fetch(`/admin/materials/${materialId}/edit`)
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                alert('Error loading material data');
                return;
            }

            const material = data.material;

            // Fill form with material data
            document.getElementById('materialTitle').value = material.title;
            document.getElementById('materialClass').value = material.class_id;
            document.getElementById('materialType').value = material.type;

            if (document.getElementById('materialDescription')) {
                document.getElementById('materialDescription').value = material.description || '';
            }

            // Create hidden ID field if it doesn't exist
            let materialIdField = document.getElementById('materialId');
            if (!materialIdField) {
                materialIdField = document.createElement('input');
                materialIdField.type = 'hidden';
                materialIdField.id = 'materialId';
                materialIdField.name = 'id';
                document.getElementById('materialForm').appendChild(materialIdField);
            }
            materialIdField.value = material.id;

            // Handle type-specific fields
            if (material.type === 'link' && document.getElementById('materialLink')) {
                document.getElementById('materialLink').value = material.url || '';
            }
            
            // Display existing files
            
            // For video file
            if (material.type === 'video' && material.file_path) {
                // Show file name
                const videoFileName = document.getElementById('videoFileName');
                if (videoFileName) {
                    const filename = material.file_path.split('/').pop();
                    videoFileName.textContent = filename;
                    videoFileName.style.display = 'block';
                    videoFileName.classList.add('existing-file');
                }
                
                // Show video preview if it exists
                const videoPreview = document.getElementById('videoPreview');
                if (videoPreview) {
                    videoPreview.style.display = 'block';
                    videoPreview.src = `/storage/${material.file_path}`;
                }
            }
            
            // For PDF file
            if (material.type === 'pdf' && material.file_path) {
                const pdfFileName = document.getElementById('pdfFileName');
                if (pdfFileName) {
                    const filename = material.file_path.split('/').pop();
                    pdfFileName.textContent = filename;
                    pdfFileName.style.display = 'block';
                    pdfFileName.classList.add('existing-file');
                }
                
                // Optional PDF preview thumbnail
                const pdfPreview = document.getElementById('pdfPreview');
                if (pdfPreview) {
                    pdfPreview.style.display = 'block';
                    pdfPreview.src = material.thumbnail ? `/storage/${material.thumbnail}` : '/images/pdf-icon.png';
                }
            }
            
            // For thumbnail
            if (material.thumbnail) {
                // Show thumbnail filename
                const thumbnailFileName = document.getElementById('thumbnailFileName');
                if (thumbnailFileName) {
                    const filename = material.thumbnail.split('/').pop();
                    thumbnailFileName.textContent = filename;
                    thumbnailFileName.style.display = 'block';
                    thumbnailFileName.classList.add('existing-file');
                }
                
                // Show actual thumbnail preview
                const thumbnailPreview = document.getElementById('thumbnailPreview');
                if (thumbnailPreview) {
                    thumbnailPreview.style.display = 'block';
                    thumbnailPreview.src = `/storage/${material.thumbnail}`;
                    
                    // Make sure container is visible
                    const thumbnailPreviewContainer = document.getElementById('thumbnailPreviewContainer');
                    if (thumbnailPreviewContainer) {
                        thumbnailPreviewContainer.style.display = 'block';
                    }
                }
            }

            // Update modal title and form action
            document.getElementById('materialModalTitle').textContent = 'Edit Material';
            document.getElementById('materialForm').action = `/admin/materials/${material.id}`;

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
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while loading the material data');
        });
}

// Function to delete a material
function deleteMaterial(materialId, materialTitle) {
    const deleteModal = document.getElementById('deleteMaterialModal');
    document.getElementById('deleteMaterialMessage').textContent = 
        `Are you sure you want to delete "${materialTitle}"? This action cannot be undone.`;
    document.getElementById('deleteMaterialForm').action = `/admin/materials/${materialId}`;
    deleteModal.classList.add('active');
}

// Preview thumbnail when selected
function previewThumbnail(input) {
    const previewContainer = document.getElementById('thumbnailPreviewContainer');
    const preview = document.getElementById('thumbnailPreview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.style.display = 'block';
        };
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.src = '';
        previewContainer.style.display = 'none';
    }
}

// Initialize form actions when document is ready
document.addEventListener('DOMContentLoaded', function() {
    // Close modals when cancel buttons are clicked
    document.querySelectorAll('.modal .btn-outline, .modal .close').forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.modal').classList.remove('active');
        });
    });
    
    // Specific close button for preview modal
    document.getElementById('closePreview').addEventListener('click', function() {
        document.getElementById('previewModal').classList.remove('active');
        
        // Stop video playback if exists
        const videoPlayer = document.getElementById('videoPlayer');
        if (videoPlayer) {
            videoPlayer.pause();
            videoPlayer.currentTime = 0;
        }
    });

    // Setup preview buttons
    document.querySelectorAll('.btn-preview-material').forEach(button => {
        button.addEventListener('click', function() {
            const materialId = this.getAttribute('data-material-id');
            previewMaterial(materialId);
        });
    });

    // Setup edit buttons
    document.querySelectorAll('.btn-edit-material').forEach(button => {
        button.addEventListener('click', function() {
            const materialId = this.getAttribute('data-material-id');
            editMaterial(materialId);
        });
    });

    // Setup delete buttons
    document.querySelectorAll('.btn-delete-material').forEach(button => {
        button.addEventListener('click', function() {
            const materialId = this.getAttribute('data-material-id');
            const materialTitle = this.getAttribute('data-material-title');
            deleteMaterial(materialId, materialTitle);
        });
    });

    // Show Add Class Modal
    document.getElementById('addClassBtn').addEventListener('click', function() {
        document.getElementById('classForm').reset();
        document.getElementById('classModalTitle').textContent = 'Add New Class';
        document.getElementById('classForm').action = window.classStoreUrl;
        document.getElementById('classId').value = '';
        document.getElementById('classModal').classList.add('active');
    });

    // Show Edit Class Modal
    document.querySelectorAll('.edit-class-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.getElementById('classModalTitle').textContent = 'Edit Class';
            document.getElementById('classForm').action = '/admin/classes/' + btn.dataset.id;
            document.getElementById('classId').value = btn.dataset.id;
            document.getElementById('className').value = btn.dataset.title; // <-- changed from name to title
            document.getElementById('classDescription').value = btn.dataset.description;
            document.getElementById('classStatus').value = btn.dataset.status;
            document.getElementById('classDate').value = btn.dataset.date;
            document.getElementById('classModal').classList.add('active');
        });
    });

    // Hide modal on cancel
    document.getElementById('cancelClass').addEventListener('click', function() {
        document.getElementById('classModal').classList.remove('active');
    });

    // Set student email on invoice load
    if (window.invoiceData) {
        document.getElementById('studentEmail').value = window.invoiceData.email;
    }
});

function openEditClassModal(button) {
    const id = button.getAttribute('data-id');
    const title = button.getAttribute('data-title');
    const description = button.getAttribute('data-description');
    const status = button.getAttribute('data-status');
    const date = button.getAttribute('data-date');
    const thumbnail = button.getAttribute('data-thumbnail');

    document.getElementById('classId').value = id;
    document.getElementById('className').value = title;
    document.getElementById('classDescription').value = description;
    document.getElementById('classStatus').value = status;
    document.getElementById('classDate').value = date;

    // Show current thumbnail if exists
    if (thumbnail) {
        document.getElementById('currentThumbnailImg').src = '/storage/' + thumbnail;
        document.getElementById('currentThumbnail').style.display = 'block';
    } else {
        document.getElementById('currentThumbnail').style.display = 'none';
    }

    document.getElementById('classModalTitle').textContent = 'Edit Class';
    document.getElementById('classForm').action = `/admin/classes/${id}`;
    document.getElementById('classMethod').value = 'PUT';
    document.getElementById('classModal').classList.add('active');
}

// View enrollment details by ID
function openViewEnrollmentModalById(id) {
    fetch(`/admin/enrollments/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                openViewEnrollmentModal(data.enrollment);
            } else {
                alert('Could not load enrollment details.');
            }
        });
}