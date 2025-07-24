<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ config('app.name', 'Laravel') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #b19cd9;
            --primary-dark: #9c64a6;
            --secondary-color: #d6c6e1;
            --accent-color: #9370db;
            --light-color: #f8f5fd;
            --text-color: #4a4a4a;
            --text-light: #6e6e6e;
            --border-color: #e0e0e0;
            --danger-color: #e57373;
            --bg-gradient: linear-gradient(135deg, #9c64a6, #b19cd9);
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        * {margin:0; padding:0; box-sizing:border-box; font-family:'Poppins',sans-serif;}
        body {
            background-color: var(--light-color);
            background-image: url('https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            position: relative;
        }
        body::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-color: rgba(177, 156, 217, 0.25);
            backdrop-filter: blur(8px);
            z-index: -1;
        }
        .login-container {
            background-color: white;
            border-radius: 18px;
            box-shadow: var(--card-shadow);
            width: 100%; 
            max-width: 450px;
            overflow: hidden;
            position: relative; 
            z-index: 1;
            padding-bottom: 1.5rem;
        }
        .login-container::before {
            content: '';
            position: absolute; 
            top: 0; 
            left: 0;
            width: 100%; 
            height: 8px;
            background: var(--bg-gradient);
            z-index: 2;
        }
        .login-header {
            background: var(--bg-gradient);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .login-header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        .login-header p {
            opacity: 0.9;
            font-size: 1rem;
        }
        .login-form {
            padding: 2rem;
        }
        .form-group { 
            margin-bottom: 1.5rem;
            max-width: 350px;
            margin-left: auto;
            margin-right: auto;
        }
        .form-group label { 
            display: block; 
            margin-bottom: 0.5rem; 
            font-size: 0.9rem; 
            font-weight: 500; 
            color: var(--text-color);
        }
        .form-control {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(177, 156, 217, 0.2);
        }
        .form-actions {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 1rem;
            margin-bottom: 0.5rem;
        }
        .btn {
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            font-size: 1rem;
            background: var(--bg-gradient);
            color: white;
            width: 100%;
            max-width: 200px;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(156, 100, 166, 0.3);
        }
        .error-container { margin-bottom: 1.5rem; }
        .error-list {
            background-color: rgba(229, 115, 115, 0.1);
            border-left: 3px solid var(--danger-color);
            padding: 1rem;
            list-style-position: inside;
            border-radius: 4px;
            color: var(--danger-color);
            font-size: 0.9rem;
        }
        .register-link-container {
            text-align: center;
            margin-top: 0.5rem;
            font-size: 0.9rem;
        }
        .register-link {
            color: var(--primary-dark);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }
        .register-link:hover {
            color: var(--accent-color);
            text-decoration: underline;
        }
        @media (max-width: 576px) {
            .login-form { padding: 1.5rem; }
        }
    </style>
</head>
<body>
    <main>
        @yield('content')
    </main>
</body>
</html>