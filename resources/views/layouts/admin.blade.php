<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - SMOOTEA Academy</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="SMOOTEA Academy Admin Dashboard">
    
    <!-- Font Awesome & Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom Styles -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    
    <!-- Additional Styles -->
    @stack('styles')
</head>
<body>
<div class="dashboard-layout">
    <!-- Sidebar Partial -->
    @include('admin.partials.sidebar')

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header Partial -->
        @include('admin.partials.header')

        <!-- Main Content Slot -->
        <main>
            @yield('alerts')
            @yield('content')
        </main>
    </div>
</div>

<!-- Modals -->
@include('admin.partials.modals')
@stack('modals')

<!-- Scripts -->
<script src="{{ asset('js/admin.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Add CSRF token to all AJAX requests
    document.addEventListener('DOMContentLoaded', function() {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // For fetch API
        window.fetchWithToken = function(url, options = {}) {
            options.headers = options.headers || {};
            options.headers['X-CSRF-TOKEN'] = token;
            return fetch(url, options);
        };
    });
    
    // Close alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.style.display = 'none';
                }, 500);
            });
        }, 5000);
    });
</script>

<!-- Additional Scripts -->
@stack('scripts')
</body>
</html>