<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login | Research Portal</title>
    <link rel="shortcut icon" href="/assets/img/favicon.ico" type="image/x-icon">
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            margin: 0;
            background: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .register-container {
            display: flex;
            max-width: 1000px;
            background: white;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
            width: 95%;
            margin: 20px;
        }

        .register-left {
            flex: 1;
            background: #f9fafc;
            padding: 40px 30px;
            text-align: center;
        }

        .logo {
            height: 200px;
            margin-bottom: 20px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .tagline {
            color: #007b55;
            font-size: 1.2rem;
            margin: 20px 0 10px;
        }

        .welcome-text {
            color: #333;
            line-height: 1.6;
        }

        .register-form {
            flex: 1;
            padding: 40px 30px;
            background: #fff;
        }

        .register-form h3 {
            color: #007b55;
            margin-bottom: 20px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .register-btn {
            background: #007b55;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            width: 100%;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .register-btn:hover {
            background: #005f3d;
        }

        .recaptcha-box {
            margin: 20px 0;
        }

        .login-link {
            text-align: center;
            margin-top: 15px;
        }

        .login-link a {
            color: #007b55;
            text-decoration: none;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .register-container {
                flex-direction: column;
            }

            .register-left,
            .register-form {
                padding: 20px;
            }

            .logo {
                height: 50px;
            }

            .register-form h3 {
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <div class="register-container">
        <div class="register-left">
            <img src="/assets/img/logo.png" alt="Institution Logo" class="logo">
            <h2>KATH R & D Research Portal</h2>
            <p class="tagline">Welcome Back!</p>
            <p class="welcome-text">
                Log in to your portal to continue your application journey. We’re happy to have you back!
            </p>
        </div>

        <div class="register-form">
            <h3>Login</h3>
            <form action="/actions/login.php" method="POST">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="Enter your email" required />

                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password" required />

                <button type="submit" class="register-btn">Login</button>
            </form>
            <p class="login-link">Don't have an account? <a href="register.php">Register</a></p>
        </div>
    </div>
</body>

</html>