<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f4f4f4;
      font-family: 'Segoe UI', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .login-box {
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 400px;
    }

    .login-box h4 {
      color: #007b55;
      margin-bottom: 25px;
      text-align: center;
    }

    .btn-custom {
      background-color: #007b55;
      color: white;
      transition: background 0.3s ease;
    }

    .btn-custom:hover {
      background-color: #005f3d;
    }

    .back-link {
      display: block;
      margin-top: 15px;
      text-align: center;
    }

    .back-link a {
      text-decoration: none;
      color: #007b55;
    }

    .back-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <div class="login-box">
    <h4>Admin Login</h4>
    <form action="../actions/admin_login.php" method="POST">
      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" name="username" id="username" class="form-control" placeholder="Admin Username" required>
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
      </div>

      <button type="submit" class="btn btn-custom w-100">Login</button>
    </form>

    <div class="back-link mt-3">
      <a href="../login.php">&larr; Back to User Login</a>
    </div>
  </div>

</body>
</html>
