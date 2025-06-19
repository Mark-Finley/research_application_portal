<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register | Research Portal</title>
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
            text-align: left;
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
            <h2>Registration Portal</h2>
            <p class="tagline">Step into excellence!</p>
            <p class="welcome-text">
                Welcome to our Research Application Portal! Please register to begin your journey. We’re excited to have you on board!
            </p>
        </div>

        <div class="register-form">
            <h3>Registration</h3>
            <form action="../actions/register.php" method="POST">
                <label>Full Name</label>
                <input type="text" name="full_name" placeholder="Enter full name" required />

                <label>Email Address</label>
                <input type="email" name="email" placeholder="Institutional Email" required />

                <label>Password</label>
                <input type="password" name="password" placeholder="Enter password" required />

                <label>Confirm Password</label>
                <input type="password" name="confirm_password" placeholder="Re-enter password" required />            

                <button type="submit" class="register-btn">Register</button>
            </form>
            <p class="login-link">Already have an account? <a href="login.php">Sign In</a></p>
        </div>
    </div>
</body>

</html>