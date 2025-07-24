<!-- Logout Modal -->
<div class="modal" id="logoutModal">
    <div class="modal-content">
        <h3 class="modal-title">Log Out</h3>
        <p>Are you sure you want to log out of your account?</p>
        <div class="modal-footer">
            <button class="btn btn-outline" id="cancelLogout">Cancel</button>
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn" id="confirmLogout">Log Out</button>
            </form>
        </div>
    </div>
</div>

<!-- Add/Edit Class Modal -->
<div class="modal" id="classModal">
    <div class="modal-content">
        <h3 class="modal-title" id="classModalTitle">Add New Class</h3>
        <form id="classForm" method="POST" action="" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="classMethod" name="_method" value="POST">
            <input type="hidden" id="classId" name="class_id">
            <div class="form-group">
                <label class="form-label" for="className">Class Name</label>
                <input type="text" id="className" name="title" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="classDescription">Description</label>
                <textarea id="classDescription" name="description" class="form-control" rows="5" required></textarea>
            </div>
            <div class="form-group">
                <label class="form-label" for="classStatus">Status</label>
                <select id="classStatus" name="status" class="form-control" required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="upcoming">Upcoming</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="classDate">Created Date</label>
                <input type="date" id="classDate" name="created_at" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="classThumbnail">Thumbnail</label>
                <input type="file" id="classThumbnail" name="thumbnail" class="form-control" accept="image/*">
                <div id="currentThumbnail" style="margin-top:8px; display:none;">
                    <img id="currentThumbnailImg" src="" alt="Current Thumbnail" style="max-width:120px; border-radius:6px;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" id="cancelClass">Cancel</button>
                <button type="submit" class="btn" id="saveClass">Save Class</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Class Confirmation Modal -->
<div class="modal" id="deleteClassModal">
    <div class="modal-content">
        <h3 class="modal-title">Delete Class</h3>
        <p>Are you sure you want to delete this class? This action cannot be undone.</p>
        <div class="modal-footer">
            <button class="btn btn-outline" id="cancelDeleteClass">Cancel</button>
            <form id="deleteClassForm" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" id="confirmDeleteClass">Delete</button>
            </form>
        </div>
    </div>
</div>

<!-- Add/Edit Invoice Modal -->
<div class="modal" id="invoiceModal">
    <div class="modal-content">
        <h3 class="modal-title" id="invoiceModalTitle">Add Invoice</h3>
        <form id="invoiceForm" method="POST" action="{{ route('admin.invoices.store') }}">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" id="invoiceId" name="invoice_id">
            <div class="form-group">
                <label class="form-label" for="invoiceNumber">Invoice Number</label>
                <input type="text" id="invoiceNumber" name="invoice_number" class="form-control" required>
            </div>
            <!-- Defensive Blade code for students and users dropdowns -->
            <div class="form-group">
    <label class="form-label" for="studentEmail">Student Email</label>
    <input type="email" id="studentEmail" name="student_email" class="form-control" required>
</div>

            <div class="form-group">
                <label class="form-label" for="invoiceClass">Class</label>
                <select id="invoiceClass" name="class_id" class="form-control" required>
                    <option value="">Select Class</option>
                    @isset($classes)
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->title }}</option>
                        @endforeach
                    @endisset
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="invoiceAmount">Amount (RM)</label>
                <input type="number" id="invoiceAmount" name="amount" class="form-control" step="0.01" min="0" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="invoiceStatus">Status</label>
                <select name="status" id="invoiceStatus" class="form-control" required>
                    <option value="unused">Unused</option>
                    <option value="used">Used</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="invoiceDate">Invoice Date</label>
                <input type="date" id="invoiceDate" name="invoice_date" class="form-control" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('invoiceModal').classList.remove('active')">Cancel</button>
                <button type="submit" class="btn" id="saveInvoice">Save Invoice</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Invoice Confirmation Modal -->
<div class="modal" id="deleteInvoiceModal">
    <div class="modal-content">
        <h3 class="modal-title">Delete Invoice</h3>
        <p>Are you sure you want to delete this invoice? This action cannot be undone.</p>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="document.getElementById('deleteInvoiceModal').classList.remove('active')">Cancel</button>
            <form id="deleteInvoiceForm" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
    </div>
</div>

<!-- Revoke Enrollment Confirmation Modal -->
<div class="modal" id="revokeEnrollmentModal">
    <div class="modal-content">
        <h3 class="modal-title">Revoke Enrollment</h3>
        <p>Are you sure you want to revoke this student's enrollment? They will lose access to class materials.</p>
        <form id="revokeEnrollmentForm" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label" for="revokeReason">Reason (optional)</label>
                <textarea id="revokeReason" name="reason" class="form-control" rows="3" placeholder="Enter reason for revocation"></textarea>
            </div>
            <div class="form-group">
                <input type="checkbox" id="sendNotification" name="send_notification" value="1">
                <label for="sendNotification">Send notification email to student</label>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" id="cancelRevokeEnrollment">Cancel</button>
                <button type="submit" class="btn btn-danger" id="confirmRevokeEnrollment">Revoke Enrollment</button>
            </div>
        </form>
    </div>
</div>

<!-- Add/Edit Material Modal -->
<div class="modal" id="materialModal">
    <div class="modal-content">
        <h3 class="modal-title" id="materialModalTitle">Upload New Material</h3>
        <form id="materialForm" method="POST" action="{{ route('admin.materials.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="materialId" name="material_id">
            <div class="form-group">
                <label class="form-label" for="materialTitle">Material Title</label>
                <input type="text" id="materialTitle" name="title" class="form-control" required placeholder="Enter a descriptive title">
            </div>
            <div class="form-group">
                <label class="form-label" for="materialClass">Class</label>
                <select id="materialClass" name="class_id" class="form-control" required>
                    <option value="">Select Class</option>
                    @isset($classes)
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->title }}</option>
                        @endforeach
                    @endisset
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="materialType">Material Type</label>
                <select id="materialType" name="type" class="form-control" required onchange="toggleTypeFields()">
                    <option value="">Select Type</option>
                    <option value="video">Video</option>
                    <option value="pdf">PDF Document</option>
                    <option value="link">Link</option>
                </select>
            </div>
            <!-- For video file fields -->
            <div id="videoFields" style="display: none;">
                <div class="form-group">
                    <label class="form-label" for="videoFile">Upload Video</label>
                    <div class="file-upload">
                        <label for="videoFile" class="file-upload-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            Click or drag to upload video
                        </label>
                        <input type="file" id="videoFile" name="video_file" accept="video/*" style="display: none;" 
                               onchange="updateFileName(this, 'videoFileName')">
                        <div id="videoFileName" class="file-name"></div>
                    </div>
                    <div id="videoPreviewContainer" style="display: none; margin-top: 15px;">
                        <h5>Current Video:</h5>
                        <video id="videoPreview" controls width="100%" height="150"></video>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="videoDuration">Duration (minutes)</label>
                    <input type="number" id="videoDuration" name="duration" class="form-control" placeholder="e.g. 15" min="1">
                </div>
            </div>
            <!-- For PDF file fields -->
            <div id="pdfFields" style="display: none;">
                <div class="form-group">
                    <label class="form-label" for="pdfFile">Upload PDF</label>
                    <div class="file-upload">
                        <label for="pdfFile" class="file-upload-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            Click or drag to upload PDF
                        </label>
                        <input type="file" id="pdfFile" name="pdf_file" accept=".pdf" style="display: none;" 
                               onchange="updateFileName(this, 'pdfFileName')">
                        <div id="pdfFileName" class="file-name"></div>
                    </div>
                    <div id="pdfPreviewContainer" style="display: none; margin-top: 15px;">
                        <h5>Current PDF:</h5>
                        <img id="pdfPreview" src="" alt="PDF Preview" style="max-width: 100px; max-height: 100px;">
                    </div>
                </div>
            </div>
            <div id="linkFields" style="display: none;">
                <div class="form-group">
                    <label class="form-label" for="materialLink">Link URL</label>
                    <input type="url" id="materialLink" name="url" class="form-control" placeholder="https://example.com">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="materialDescription">Description</label>
                <textarea id="materialDescription" name="description" class="form-control" rows="4" placeholder="Provide a brief description of this material"></textarea>
            </div>
            <!-- Thumbnail field (add this in all material types) -->
            <div class="form-group">
                <label class="form-label" for="thumbnailFile">Custom Thumbnail</label>
                <div class="file-upload">
                    <label for="thumbnailFile" class="file-upload-label">
                        <i class="fas fa-image"></i>
                        Upload thumbnail image
                    </label>
                    <input type="file" id="thumbnailFile" name="thumbnail" accept="image/*" style="display: none;" 
                           onchange="updateFileName(this, 'thumbnailFileName'); previewThumbnail(this);">
                    <div id="thumbnailFileName" class="file-name"></div>
                </div>
                <div id="thumbnailPreviewContainer" style="display: none; margin-top: 15px;">
                    <h5>Thumbnail Preview:</h5>
                    <img id="thumbnailPreview" src="" alt="Thumbnail Preview" style="max-width: 150px; max-height: 150px; border-radius: 5px;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" id="cancelMaterial">Cancel</button>
                <button type="submit" class="btn" id="saveMaterial">Save Material</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Material Confirmation Modal -->
<div class="modal" id="deleteMaterialModal">
    <div class="modal-content">
        <h3 class="modal-title">Delete Material</h3>
        <p>Are you sure you want to delete this material? This action cannot be undone.</p>
        <div class="modal-footer">
            <button class="btn btn-outline" id="cancelDeleteMaterial">Cancel</button>
            <form id="deleteMaterialForm" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" id="confirmDeleteMaterial">Delete</button>
            </form>
        </div>
    </div>
</div>

<!-- Preview Material Modal -->
<div class="modal preview-modal" id="previewModal">
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

<!-- Add/Edit Enrollment Modal -->
<div class="modal" id="enrollmentModal">
    <div class="modal-content">
        <h3 class="modal-title" id="enrollmentModalTitle">Edit Enrollment</h3>
        <form id="enrollmentForm" method="POST" action="">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="PUT">
            <input type="hidden" id="enrollmentId" name="enrollment_id">

            <div class="form-group">
                <label class="form-label" for="studentSelect">Student</label>
                <select id="studentSelect" name="user_id" class="form-control" required>
                    <option value="">Select Student</option>
                    @isset($students)
                        @foreach($students as $student)
                            <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->email }})</option>
                        @endforeach
                    @endisset
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="classSelect">Class</label>
                <select id="classSelect" name="class_id" class="form-control" required>
                    <option value="">Select Class</option>
                    @isset($classes)
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->title }}</option>
                        @endforeach
                    @endisset
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="statusSelect">Status</label>
                <select id="statusSelect" name="status" class="form-control" required>
                    <option value="active">Active</option>
                    <option value="pending">Pending</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="enrollmentDate">Enrollment Date</label>
                <input type="date" id="enrollmentDate" name="enrollment_date" class="form-control" required>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline" id="cancelEnrollment">Cancel</button>
                <button type="submit" class="btn" id="saveEnrollment">Save Changes</button>
            </div>
        </form>
    </div>
</div>


<!-- Delete Enrollment Confirmation Modal -->
<div class="modal" id="deleteEnrollmentModal">
    <div class="modal-content">
        <h3 class="modal-title">Delete Enrollment</h3>
        <p>Are you sure you want to delete this enrollment?</p>
        <div class="modal-footer">
            <button class="btn btn-outline" id="cancelDeleteEnrollment">Cancel</button>
            <form id="deleteEnrollmentForm" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" id="confirmDeleteEnrollment">Delete</button>
            </form>
        </div>
    </div>
</div>

<!-- View Class Modal -->
<div class="modal" id="viewClassModal">
    <div class="modal-content">
        <h3 class="modal-title" id="viewClassModalTitle">Class Details</h3>
        <div id="viewClassContent">
            <div style="display: flex; align-items: center; gap: 18px;">
                <img id="viewClassThumbnail" src="" alt="Class Thumbnail" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;">
                <div>
                    <h4 id="viewClassTitle" style="margin-bottom: 6px;"></h4>
                    <span class="badge bg-primary" id="viewClassStatus"></span>
                    <div style="margin-top: 6px; color: #888;">
                        Created: <span id="viewClassDate"></span>
                    </div>
                </div>
            </div>
            <div style="margin-top: 18px;">
                <strong>Description:</strong>
                <p id="viewClassDescription" style="margin-top: 6px;"></p>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline" onclick="document.getElementById('viewClassModal').classList.remove('active')">Close</button>
        </div>
    </div>
</div>

<!-- View Enrollment Modal (Refined) -->
<div class="modal" id="viewEnrollmentModal">
    <div class="modal-content" style="max-width: 480px;">
        <h3 class="modal-title" style="color:#6334a7;display:flex;align-items:center;gap:8px;">
            <span style="font-size:1.5rem;">🟣</span> Enrollment Details
        </h3>
        <div id="viewEnrollmentContent" style="margin-top:18px;">
            <div style="margin-bottom:12px;">
                <strong>Name:</strong> <span id="enrollmentStudentName"></span><br>
                <strong>Email:</strong> <span id="enrollmentStudentEmail"></span><br>
                <strong>Class:</strong> <span id="enrollmentClassTitle"></span><br>
                <strong>Enrollment Date:</strong> <span id="enrollmentDate"></span>
            </div>
            <div style="margin-bottom:12px;">
                <strong>Status:</strong> <span id="enrollmentStatus" class="badge"></span>
            </div>
            <div style="margin-bottom:12px;">
                <strong>Progress:</strong>
                <div style="margin-left:12px;">
                    <span id="enrollmentProgress"></span><br>
                    <span id="enrollmentLastAccessed"></span>
                </div>
            </div>
            <div>
                <strong>Other Enrolled Classes</strong>
                <ul id="otherEnrolledClasses" style="margin:8px 0 0 16px; padding:0; list-style:disc;"></ul>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline" onclick="document.getElementById('viewEnrollmentModal').classList.remove('active')">Close</button>
        </div>
    </div>
</div>

<script>
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
            
            // Display existing files and thumbnails
            if (material.file_path) {
                // Set file name for display
                const fileNameElement = document.getElementById(material.type === 'video' ? 'videoFileName' : 'pdfFileName');
                if (fileNameElement) {
                    const filename = material.file_path.split('/').pop();
                    fileNameElement.textContent = filename;
                    fileNameElement.style.display = 'block';
                    fileNameElement.classList.add('existing-file');
                }
                
                // Show video preview if it's a video
                if (material.type === 'video' && document.getElementById('videoPreviewContainer')) {
                    const videoPreview = document.getElementById('videoPreview');
                    if (videoPreview) {
                        videoPreview.src = `/storage/${material.file_path}`;
                        document.getElementById('videoPreviewContainer').style.display = 'block';
                        videoPreview.style.display = 'block';
                        
                        // Set duration if available
                        if (material.duration && document.getElementById('videoDuration')) {
                            document.getElementById('videoDuration').value = material.duration;
                        }
                    }
                }
                
                // Show PDF icon if it's a PDF
                if (material.type === 'pdf' && document.getElementById('pdfPreviewContainer')) {
                    document.getElementById('pdfPreviewContainer').style.display = 'block';
                    if (document.getElementById('pdfPreview')) {
                        document.getElementById('pdfPreview').src = '/images/pdf-icon.png';
                    }
                }
            }
            
            // Display thumbnail if exists
            if (material.thumbnail) {
                const thumbnailFileName = document.getElementById('thumbnailFileName');
                if (thumbnailFileName) {
                    const filename = material.thumbnail.split('/').pop();
                    thumbnailFileName.textContent = filename;
                    thumbnailFileName.style.display = 'block';
                    thumbnailFileName.classList.add('existing-file');
                }
                
                // Show thumbnail preview
                const thumbnailPreview = document.getElementById('thumbnailPreview');
                if (thumbnailPreview) {
                    thumbnailPreview.src = `/storage/${material.thumbnail}`;
                    document.getElementById('thumbnailPreviewContainer').style.display = 'block';
                }
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
 * Preview the thumbnail image
 */
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
    }
}

/**
 * Show the material preview modal
 */
function showPreviewModal(id) {
    const previewModal = document.getElementById('previewModal');
    const previewContent = document.getElementById('previewContent');
    previewContent.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> Loading preview...</div>';
    previewModal.classList.add('active');

    fetch(`/admin/materials/${id}/preview`)
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                previewContent.innerHTML = '<div class="error-message">Error loading preview</div>';
                return;
            }

            const material = data.material;

            document.getElementById('previewTitle').textContent = material.title || '';
            document.getElementById('previewMaterialTitle').textContent = material.title || '';
            document.getElementById('previewClass').textContent = material.class ? material.class.title : '';
            document.getElementById('previewType').textContent = material.type ? material.type.charAt(0).toUpperCase() + material.type.slice(1) : '';
            document.getElementById('previewMaterialDescription').textContent = material.description || '';
            document.getElementById('previewMaterialDescription').style.display = 'block';

            // Render content based on material type
            if (material.type === 'video') {
                if (material.thumbnail) {
                    // Show just the thumbnail image for videos
                    previewContent.innerHTML = `
                        <div class="video-thumbnail-container" style="text-align:center;">
                            <img src="/storage/${material.thumbnail}" alt="Video thumbnail" style="max-width:100%;max-height:350px;border-radius:8px;box-shadow:0 4px 8px rgba(0,0,0,0.1);">
                            <div style="margin-top:15px;">
                                <a href="/storage/${material.file_path}" target="_blank" class="btn" style="background:#6334a7;color:white;text-decoration:none;padding:8px 16px;border-radius:5px;display:inline-block;">
                                    <i class="fas fa-play-circle"></i> Play Video
                                </a>
                            </div>
                        </div>
                    `;
                } else if (material.url && (material.url.includes('youtube') || material.url.includes('youtu.be'))) {
                    // YouTube embed if no thumbnail
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
                    // Fallback to Vimeo embed if no thumbnail
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
                    // Fallback to video player if no thumbnail
                    previewContent.innerHTML = `
                        <video controls width="100%" height="300">
                            <source src="/storage/${material.file_path}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    `;
                } else {
                    // No video or thumbnail available
                    previewContent.innerHTML = `<p>No video preview available.</p>`;
                }
            } else if (material.type === 'pdf') {
                const pdfUrl = `/storage/${material.file_path}`;
                previewContent.innerHTML = `
                    <div class="pdf-container">
                        <iframe 
                            src="https://mozilla.github.io/pdf.js/web/viewer.html?file=${encodeURIComponent(window.location.origin + pdfUrl)}" 
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

/**
 * Update the displayed file name
 */
function updateFileName(input, fileNameId) {
    const fileNameDiv = document.getElementById(fileNameId);
    if (input.files && input.files[0]) {
        fileNameDiv.textContent = input.files[0].name;
        fileNameDiv.style.display = 'block';
    }
}

/**
 * Open the view class modal
 */
function openViewClassModal(button) {
    document.getElementById('viewClassTitle').textContent = button.getAttribute('data-title');
    document.getElementById('viewClassStatus').textContent = button.getAttribute('data-status');
    document.getElementById('viewClassDate').textContent = button.getAttribute('data-date');
    document.getElementById('viewClassDescription').textContent = button.getAttribute('data-description');
    document.getElementById('viewClassThumbnail').src = '/storage/' + button.getAttribute('data-thumbnail');
    document.getElementById('viewClassModal').classList.add('active');
}

/**
 * Open the view enrollment modal
 */
function openViewEnrollmentModal(enrollment) {
    document.getElementById('enrollmentStudentName').textContent = enrollment.student.name;
    document.getElementById('enrollmentStudentEmail').textContent = enrollment.student.email;
    document.getElementById('enrollmentClassTitle').textContent = enrollment.class.title;
    document.getElementById('enrollmentDate').textContent = enrollment.enrollment_date;

    // Status badge
    const statusSpan = document.getElementById('enrollmentStatus');
    statusSpan.textContent = enrollment.status.charAt(0).toUpperCase() + enrollment.status.slice(1);
    statusSpan.className = 'badge ' + (enrollment.status === 'active' ? 'badge-success' : 'badge-warning');

    // Progress
    document.getElementById('enrollmentProgress').textContent =
        `▸ ${enrollment.completed_materials} / ${enrollment.total_materials} materials completed (${enrollment.completion_percentage}%)`;

    // Last accessed (optional)
    document.getElementById('enrollmentLastAccessed').textContent =
        enrollment.last_accessed_material
            ? `▸ Last accessed: ${enrollment.last_accessed_material}`
            : '';

    // Other enrolled classes
    const otherList = document.getElementById('otherEnrolledClasses');
    otherList.innerHTML = '';
    (enrollment.other_classes || []).forEach(cls => {
        const li = document.createElement('li');
        li.textContent = cls;
        otherList.appendChild(li);
    });

    document.getElementById('viewEnrollmentModal').classList.add('active');
}
</script>

@php
    $material = $material ?? null;
@endphp