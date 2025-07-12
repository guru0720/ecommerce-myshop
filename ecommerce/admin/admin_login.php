<?php
require_once '../php/config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare("SELECT id, password FROM admins WHERE email=?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        header("Location: dashboard.php");
        exit;
    }
    $error = "Invalid credentials";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login - MyShop</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(120deg, #f8fafc 0%, #e0e7ff 100%);
      min-height: 100vh;
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
    .navbar-custom .nav-link, .navbar-custom .btn {
      color: #f8f9fa !important;
      font-weight: 500;
      margin-left: 0.7rem;
      margin-right: 0.7rem;
      border-radius: 0.5rem;
      padding: 0.5rem 1.2rem;
      transition: background 0.2s, color 0.2s, box-shadow 0.2s;
      box-shadow: 0 2px 8px 0 rgba(31,38,135,0.04);
    }
    .navbar-custom .nav-link.active,
    .navbar-custom .nav-link:hover {
      background: rgba(255,255,255,0.18);
      color: #fff !important;
      box-shadow: 0 4px 16px 0 rgba(31,38,135,0.10);
    }
    .glass-card {
      background: rgba(255,255,255,0.18);
      border-radius: 1.5rem;
      box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.12);
      backdrop-filter: blur(6px);
      padding: 2.5rem 2rem;
      max-width: 400px;
      margin: auto;
    }
    .form-label {
      font-weight: 500;
    }
    .btn-primary {
      border-radius: 2rem;
      font-weight: 600;
      letter-spacing: 0.5px;
      box-shadow: 0 2px 8px 0 rgba(13,110,253,0.10);
      background: linear-gradient(90deg, #0d6efd 60%, #6610f2 100%);
      border: none;
      transition: background 0.2s, box-shadow 0.2s, transform 0.2s;
    }
    .btn-primary:hover, .btn-primary:focus {
      background: linear-gradient(90deg, #6610f2 60%, #0d6efd 100%);
      box-shadow: 0 4px 16px 0 rgba(13,110,253,0.18);
      color: #fff !important;
      transform: translateY(-2px) scale(1.04);
    }
    .admin-login-title {
      font-weight: bold;
      color: #0d6efd;
      letter-spacing: 1px;
      text-shadow: 1px 1px 8px #0001;
    }
    @media (max-width: 576px) {
      .glass-card { padding: 1.2rem 0.5rem; }
    }
  </style>
</head>
<body>
  <!-- Navbar (same as login.php) -->
  <nav class="navbar navbar-expand-lg navbar-custom mb-4">
    <div class="container">
      <a class="navbar-brand" href="../index.php">🛒 MyShop</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? ' active' : '' ?>" href="../index.php">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link<?= basename($_SERVER['PHP_SELF']) == 'product.php' ? ' active' : '' ?>" href="../product.php">Products</a>
          </li>
          <li class="nav-item">
            <a class="nav-link<?= basename($_SERVER['PHP_SELF']) == 'cart.php' ? ' active' : '' ?>" href="../cart.php">Cart</a>
          </li>
          <li class="nav-item">
            <a class="nav-link<?= basename($_SERVER['PHP_SELF']) == 'login.php' ? ' active' : '' ?>" href="../login.php">Login</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="admin_login.php">Admin Login</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <div class="container d-flex align-items-center justify-content-center" style="min-height:100vh;">
    <div class="glass-card shadow">
      <h2 class="mb-4 text-center admin-login-title">Admin Login</h2>
      <?php if (!empty($error)): ?>
        <div class="alert alert-danger text-center"><?= $error ?></div>
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
        <button type="submit" class="btn btn-primary w-100">Login</button>
      </form>
    </div>
  </div>
</body>
</html>