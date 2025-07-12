<?php
// filepath: c:\xampp\htdocs\ecommarce\forget-password.php
session_start();
require_once 'php/config.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if ($email) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            // Yahan aap actual email sending logic add kar sakte hain
            $message = '<div class="alert alert-success">If this email is registered, a password reset link has been sent.</div>';
        } else {
            $message = '<div class="alert alert-danger">Email not found.</div>';
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
      <a class="navbar-brand" href="index.php">🛒 MyShop</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? ' active' : '' ?>" href="index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link<?= basename($_SERVER['PHP_SELF']) == 'product.php' ? ' active' : '' ?>" href="product.php">Products</a></li>
          <li class="nav-item"><a class="nav-link<?= basename($_SERVER['PHP_SELF']) == 'cart.php' ? ' active' : '' ?>" href="cart.php">Cart</a></li>
          <?php if (!empty($_SESSION['admin_id'])): ?>
            <li class="nav-item"><a class="nav-link" href="admin/dashboard.php">Admin Profile</a></li>
            <li class="nav-item"><a class="nav-link" href="admin/logout.php">Logout</a></li>
          <?php elseif (!empty($_SESSION['user_id'])): ?>
            <li class="nav-item">
              <a class="nav-link active" href="user/profile.php">
                👤 <?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?>
              </a>
            </li>
          <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>
  <div class="container d-flex align-items-center justify-content-center" style="min-height:100vh;">
    <div class="card forgot-card shadow p-4" style="max-width: 400px; width: 100%;">
      <h2 class="mb-4 text-center fw-bold text-primary">Forgot Password</h2>
      <?= $message ?>
      <form method="post" class="mb-2">
        <div class="mb-3">
          <label for="email" class="form-label fw-semibold">Enter your registered email</label>
          <input type="email" class="form-control" id="email" name="email" required autofocus>
        </div>
        <button type="submit" class="btn btn-primary w-100 fw-bold">Send Reset Link</button>
      </form>
      <div class="text-center mt-3">
        <a href="login.php" class="btn btn-link">Back to Login</a>
      </div>
    </div>
  </div>
</body>
</html>