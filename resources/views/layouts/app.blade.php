<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name', 'SMOOTEA Academy') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @stack('styles')
</head>
<body>
    @yield('content')
    @stack('scripts')
</body>
</html>