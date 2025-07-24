<!-- Logout Modal -->
<div class="modal" id="logoutModal">
    <div class="modal-content">
        <h3 class="modal-title">Log Out</h3>
        <p>Are you sure you want to log out of your account?</p>
        <div class="modal-footer">
            <button class="btn btn-outline" id="cancelLogout">Cancel</button>
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-primary" id="confirmLogout">Log Out</button>
            </form>
        </div>
    </div>
</div>

<!-- Material Preview Modal -->
<div class="modal" id="materialPreviewModal">
    <div class="modal-content">
        <h3 class="modal-title" id="materialTitle">Material Preview</h3>
        <div id="materialPreviewContent" class="material-preview-content"></div>
        <div class="material-preview-info">
            <h4 class="material-preview-title" id="materialPreviewTitle"></h4>
            <div class="material-preview-meta">
                <span>Class: <span id="materialPreviewClass"></span></span>
                <span>Type: <span id="materialPreviewType"></span></span>
            </div>
            <p class="material-preview-description" id="materialPreviewDescription"></p>
        </div>
        <div class="modal-footer">
            <form method="POST" id="materialCompletionForm" class="me-auto">
                @csrf
                <button type="submit" class="btn btn-primary" id="materialCompletionButton"></button>
            </form>
            <button class="btn btn-outline" id="closeMaterialPreview">Close</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Close modals when buttons are clicked
    document.querySelectorAll('.modal .btn-outline').forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.modal').classList.remove('active');
        });
    });
    
    // Close modals when clicking outside
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });
    });
});
</script>