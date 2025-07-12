<?php
session_start();
require_once 'php/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>MyShop - E-commerce</title>
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
    .hero-section {
      min-height: 60vh;
      background: linear-gradient(120deg, #6366f1cc 0%, #0ea5e9cc 100%), url('images/banner.jpg') center center/cover no-repeat;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      overflow: hidden;
    }
    .hero-glass {
      background: rgba(255,255,255,0.18);
      border-radius: 1.5rem;
      box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.12);
      backdrop-filter: blur(6px);
      padding: 3rem 2rem;
      max-width: 500px;
      margin: auto;
    }
    .hero-title {
      font-size: 2.7rem;
      font-weight: bold;
      color: #fff;
      text-shadow: 0 2px 8px #0004;
    }
    .hero-lead {
      color: #f1f5f9;
      font-size: 1.25rem;
      font-weight: 500;
      margin-bottom: 2rem;
      text-shadow: 0 1px 6px #0003;
    }
    .card-img-top {
      border-radius: 1rem 1rem 0 0;
      height: 220px;
      object-fit: cover;
    }
    .card {
      border-radius: 1rem;
      box-shadow: 0 4px 16px 0 rgba(31, 38, 135, 0.08);
      transition: transform 0.15s;
    }
    .card:hover {
      transform: translateY(-6px) scale(1.02);
      box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.16);
    }
    .btn-primary, .btn-outline-primary {
      border-radius: 2rem;
      font-weight: 600;
      letter-spacing: 0.5px;
    }
    @media (max-width: 576px) {
      .hero-glass { padding: 1.5rem 0.5rem; }
      .hero-title { font-size: 2rem; }
      .card-img-top { height: 140px; }
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
            <li class="nav-item"><a class="nav-link" href="admin/dashboard.php">Admin Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="php/logout.php">Logout</a></li>
          <?php elseif (!empty($_SESSION['user_id'])): ?>
            <?php 
            $role = $_SESSION['user_role'] ?? 'customer';
            $dashboard_links = [
                'vendor' => 'vendor/dashboard.php',
                'manager' => 'manager/dashboard.php', 
                'customer' => 'user/profile.php'
            ];
            ?>
            <li class="nav-item">
              <a class="nav-link" href="<?= $dashboard_links[$role] ?? 'user/profile.php' ?>">
                👤 <?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?> (<?= ucfirst($role) ?>)
              </a>
            </li>
            <li class="nav-item"><a class="nav-link" href="php/logout.php">Logout</a></li>
          <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
            <li class="nav-item"><a class="nav-link" href="multi_login_demo.php">Multi-Login Demo</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="hero-section">
    <div class="hero-glass text-center">
      <h1 class="hero-title mb-3">Welcome to <span class="text-warning">MyShop</span></h1>
      <p class="hero-lead">Shop the latest and greatest products with style.</p>
      <a href="#products" class="btn btn-primary btn-lg px-5 shadow">Shop Now</a>
    </div>
  </section>

  <!-- Product Section -->
  <section id="products" class="py-5">
    <div class="container">
      <h2 class="text-center mb-4 fw-bold text-primary">Featured Products</h2>
      <div class="row">
        <?php
        $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC LIMIT 6");
        while ($product = $stmt->fetch()):
        ?>
        <div class="col-md-4 mb-4">
          <div class="card shadow-sm h-100">
            <img src="uploads/<?= htmlspecialchars($product['main_image'] ?? 'default.png') ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name'] ?? 'Product') ?>">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
              <p class="card-text">Rs. <?= number_format($product['price'],2) ?></p>
              <a href="product-detail.php?id=<?= $product['id'] ?>" class="btn btn-outline-primary mt-auto w-100">View Details</a>
            </div>
          </div>
        </div>
        <?php endwhile; ?>
      </div>
    </div>
  </section>

  <!-- Order Success Modal -->
  <div class="modal fade" id="orderSuccessModal" tabindex="-1" aria-labelledby="orderSuccessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title" id="orderSuccessModalLabel">Order Placed</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <i class="fa fa-check-circle fa-3x text-success mb-3"></i>
          <div id="orderSuccessMsg" class="fw-semibold">
            Order placed successfully!
          </div>
        </div>
        <div class="modal-footer justify-content-center">
          <button type="button" class="btn btn-success rounded-pill px-4" data-bs-dismiss="modal">OK</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="navbar-custom text-white text-center p-3 mt-5" style="border-radius: 1rem 1rem 0 0;">
    <p class="mb-0" style="color:#fff; text-shadow:1px 1px 8px #0002;">&copy; 2025 MyShop. All rights reserved.</p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <?php if (!empty($_SESSION['order_success'])): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var modal = new bootstrap.Modal(document.getElementById('orderSuccessModal'));
      document.getElementById('orderSuccessMsg').innerHTML = <?= json_encode($_SESSION['order_success']) ?>;
      modal.show();
    });
  </script>
  <?php unset($_SESSION['order_success']); ?>
  <?php endif; ?>
</body>
</html>
