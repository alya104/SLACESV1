<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMOOTEA Academy | Beverage Entrepreneurship Education</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #b19cd9;
            --primary-dark: #9c64a6;
            --secondary-color: #d6c6e1;
            --accent-color: #9370db;
            --light-color: #f8f5fd;
            --text-color: #4a4a4a;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-image: url('https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(177, 156, 217, 0.25);
            backdrop-filter: blur(8px);
            z-index: -1;
        }

        .container {
            width: 100%;
            max-width: 600px;
            padding: 40px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .logo {
            margin-bottom: 30px;
        }

        .logo i {
            font-size: 42px;
            color: var(--primary-color);
        }

        h1 {
            color: var(--primary-color);
            font-weight: 600;
            font-size: 28px;
            margin-top: 15px;
        }

        .subtitle {
            color: var(--text-color);
            margin-bottom: 35px;
            font-size: 15px;
            line-height: 1.5;
        }

        p {
            color: var(--text-color);
            margin-bottom: 30px;
            font-size: 16px;
            line-height: 1.6;
        }

        .btn-container {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-top: 30px;
        }

        .btn {
            padding: 14px 28px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-outline {
            background-color: transparent;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(147, 112, 219, 0.3);
        }

        .btn-primary:hover {
            background-color: var(--accent-color);
        }

        .btn-outline:hover {
            background-color: rgba(177, 156, 217, 0.1);
        }

        .features {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin: 40px auto;
            max-width: 500px;
        }

        .feature {
            text-align: center;
            flex: 0 0 auto;
            width: calc(50% - 20px);
        }

        .feature i {
            font-size: 30px;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .feature h3 {
            font-size: 16px;
            margin-bottom: 10px;
        }

        .feature p {
            font-size: 14px;
            color: var(--text-light);
        }

        @media (max-width: 768px) {
            .container {
                margin: 20px;
            }
            
            .features {
                flex-direction: column;
                align-items: center;
                gap: 30px;
            }
            
            .feature {
                width: 100%;
                max-width: 300px;
            }
            
            .btn-container {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <i class="fas fa-mug-hot"></i>
            <h1>SMOOTEA Academy</h1>
        </div>
        <p class="subtitle">Malaysia's Premier F&B Education Platform<br>Crafting Entrepreneurs, One Drink at a Time</p>
        
        <p>Welcome to SMOOTEA Academy, where we transform beverage enthusiasts into successful entrepreneurs. Join our community of tea artisans and business leaders.</p>
        
        <div class="features">
            <div class="feature">
                <i class="fas fa-graduation-cap"></i>
                <h3>Expert Training</h3>
                <p>Learn from industry professionals</p>
            </div>
            <div class="feature">
                <i class="fas fa-business-time"></i>
                <h3>Business Skills</h3>
                <p>Develop entrepreneurship expertise</p>
            </div>
        </div>

        <div class="btn-container">
            <a href="{{ route('login') }}" class="btn btn-primary">
                <i class="fas fa-sign-in-alt"></i> Sign In
            </a>
            <a href="{{ route('register') }}" class="btn btn-outline">
                <i class="fas fa-user-plus"></i> Register
            </a>
        </div>
    </div>
</body>
</html>