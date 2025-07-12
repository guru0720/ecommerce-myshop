<?php
require_once '../php/config.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - MyShop</title>
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
      border-radius: 2rem;
      padding: 0.5rem 1.2rem;
      transition: background 0.2s, color 0.2s, box-shadow 0.2s;
      box-shadow: 0 2px 8px 0 rgba(31,38,135,0.04);
    }
    .navbar-custom .btn-danger {
      background: #dc3545;
      border: none;
    }
    .navbar-custom .btn-danger:hover {
      background: #b52a37;
    }
    .dashboard-card {
      border-radius: 1.5rem;
      box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.10);
      background: #fff;
      transition: transform 0.15s, box-shadow 0.15s;
      border: none;
    }
    .dashboard-card:hover {
      transform: translateY(-6px) scale(1.03);
      box-shadow: 0 12px 36px 0 rgba(31, 38, 135, 0.18);
    }
    .dashboard-btn {
      border-radius: 2rem;
      font-weight: 600;
      letter-spacing: 0.5px;
      font-size: 1.1rem;
      padding: 1.2rem 0;
      transition: background 0.2s, color 0.2s, box-shadow 0.2s;
      box-shadow: 0 2px 8px 0 rgba(31,38,135,0.04);
    }
    .dashboard-btn i {
      font-size: 1.5rem;
      margin-right: 0.5rem;
      vertical-align: middle;
    }
    @media (max-width: 576px) {
      .dashboard-btn { font-size: 1rem; padding: 1rem 0; }
      .dashboard-card { padding: 1rem 0.5rem; }
    }
  </style>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-custom mb-4">
  <div class="container">
    <a class="navbar-brand" href="#">🛠️ Admin Panel</a>
    <div class="ms-auto">
      <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
  </div>
</nav>
<div class="container py-5">
  <h2 class="mb-4 fw-bold text-primary text-center">Admin Dashboard</h2>
  <div class="row g-4 justify-content-center">
    <div class="col-md-3">
      <div class="card dashboard-card text-center">
        <a href="users.php" class="btn btn-outline-primary dashboard-btn w-100 my-3">
          <i class="fa fa-users"></i>User Activity
        </a>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card dashboard-card text-center">
        <a href="orders.php" class="btn btn-outline-success dashboard-btn w-100 my-3">
          <i class="fa fa-box"></i>Orders
        </a>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card dashboard-card text-center">
        <a href="payments.php" class="btn btn-outline-warning dashboard-btn w-100 my-3">
          <i class="fa fa-credit-card"></i>Payments
        </a>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card dashboard-card text-center">
        <a href="products.php" class="btn btn-outline-secondary dashboard-btn w-100 my-3">
          <i class="fa fa-laptop"></i>Product Management
        </a>
      </div>
    </div>
  </div>
</div>
</body>
</html>