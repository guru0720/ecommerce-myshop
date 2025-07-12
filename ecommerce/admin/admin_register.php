<?php
session_start();
require_once '../php/config.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (!$email || !$password || !$confirm) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM admins WHERE email=?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email already registered.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO admins (email, password) VALUES (?, ?)");
            if ($stmt->execute([$email, $hashed])) {
                $success = "Registration successful. You can now <a href='admin_login.php'>login</a>.";
            } else {
                $error = "Registration failed. Try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>MyShop</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    body {
      background: linear-gradient(120deg, #f8fafc 0%, #e0e7ff 100%);
    }
    .navbar-custom {
      background: linear-gradient(90deg, #0d6efd 0%, #6610f2 100%);
      border-radius: 0 0 1rem 1rem;
      box-shadow: 0 4px 16px rgba(0,0,0,0.10);
    }
    .navbar-custom .navbar-brand {
      font-weight: bold;
      font-size: 1.7rem;
      letter-spacing: 1px;
      color: #fff !important;
      text-shadow: 1px 1px 8px #0002;
    }
    .navbar-custom .nav-link {
      color: #f8f9fa !important;
      font-weight: 500;
      margin-left: 0.7rem;
      margin-right: 0.7rem;
      border-radius: 0.5rem;
      transition: background 0.2s, color 0.2s;
    }
    .navbar-custom .nav-link.active,
    .navbar-custom .nav-link:hover {
      background: rgba(255,255,255,0.18);
      color: #fff !important;
    }
    .card, .glass-card {
      border-radius: 1rem;
      box-shadow: 0 4px 16px 0 rgba(31, 38, 135, 0.08);
      transition: transform 0.15s, box-shadow 0.15s;
      background: rgba(255,255,255,0.18);
      backdrop-filter: blur(6px);
      padding: 2.5rem 2rem;
      max-width: 400px;
      margin: auto;

    }
    .card:hover, .glass-card:hover, .glass-card:focus-within {
      transform: translateY(-6px) scale(1.02);
      box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.16);
    }
    .btn-primary, .btn-outline-primary {
      border-radius: 2rem;
      font-weight: 600;
      letter-spacing: 0.5px;
      transition: background 0.2s, box-shadow 0.2s, color 0.2s;
    }
    .btn-primary:hover, .btn-outline-primary:hover {
      background: linear-gradient(90deg, #6610f2 60%, #0d6efd 100%);
      color: #fff !important;
      box-shadow: 0 4px 16px 0 rgba(13,110,253,0.18);
      transform: translateY(-2px) scale(1.04);
    }
    .form-control {
      border-radius: 1rem;
      border: 1.5px solid #e0e7ff;
      transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-control:focus {
      border-color: #6610f2;
      box-shadow: 0 0 0 0.15rem rgba(102,16,242,0.15);
    }
    @media (max-width: 576px) {
      .card, .glass-card { padding: 1.2rem 0.5rem; }
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-custom mb-4">
    <div class="container">
      <a class="navbar-brand" href="../index.php">🛒 MyShop</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <a class="nav-link active" href="admin_register.php">Admin Register</a>
        </ul>
      </div>
    </div>
  </nav>
  <div class="container d-flex align-items-center justify-content-center" style="min-height:100vh;">
    <div class="glass-card shadow">
      <h2 class="mb-4 text-center admin-register-title">Admin Registration</h2>
      <?php if ($error): ?>
        <div class="alert alert-danger text-center"><?= $error ?></div>
      <?php elseif ($success): ?>
        <div class="alert alert-success text-center"><?= $success ?></div>
      <?php endif; ?>
      <form method="post">
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control" id="email" name="email" required autofocus>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="mb-3">
          <label for="confirm_password" class="form-label">Confirm Password</label>
          <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Register</button>
      </form>
      <div class="mt-3 text-center">
        <a href="admin_login.php" class="text-decoration-none fw-semibold" style="color:#6610f2;">Already have an account? Login</a>
      </div>
    </div>
  </div>
</body>
</html>