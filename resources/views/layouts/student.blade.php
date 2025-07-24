<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Student Portal</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- Student Styles -->
    <link rel="stylesheet" href="{{ asset('css/student.css') }}">
    
    <!-- Additional styles stacks -->
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    @include('student.partials.sidebar')
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        @include('student.partials.header')
        
        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Alert Messages -->
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
            <script>
                setTimeout(function() {
                    var alert = document.getElementById('successAlert');
                    if(alert) alert.style.display = 'none';
                }, 3000); // 3 seconds
            </script>
            @endif
            
            <!-- Main Content -->
            @yield('content')
        </div>
    </div>
    
    <!-- Modals -->
    @include('student.partials.modals')
    
    <!-- Additional scripts -->
    @stack('scripts')

    <!-- WhatsApp Floating Support Button -->
    <a href="https://wa.me/60123456789" class="whatsapp-float" target="_blank" aria-label="WhatsApp Support">
        <i class="fab fa-whatsapp"></i>
    </a>
</body>
</html>