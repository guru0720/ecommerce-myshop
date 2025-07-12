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
  <title>Orders - Admin Panel</title>
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
    .orders-table-card {
      border-radius: 1.5rem;
      box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.10);
      background: #fff;
      border: none;
      padding: 2rem 1.5rem;
    }
    .table thead th {
      background: #e0e7ff;
    }
    @media (max-width: 576px) {
      .orders-table-card { padding: 1rem 0.5rem; }
      .navbar-custom .nav-link, .navbar-custom .btn { padding: 0.5rem 0.7rem; }
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-custom mb-4">
  <div class="container">
    <a class="navbar-brand" href="dashboard.php">🛠️ Admin Panel</a>
    <div class="ms-auto d-flex align-items-center">
      <a href="dashboard.php" class="btn btn-success me-2">Admin Dashboard</a>
      <a href="../index.php" class="btn btn-primary me-2">Home</a>
      <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
  </div>
</nav>
<div class="container py-5">
  <div class="orders-table-card">
    <h2 class="mb-4 fw-bold text-primary text-center">Orders</h2>
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead>
          <tr>
            <th>Sr.</th>
            <th>User Name</th>
            <th>Product Name</th>
            <th>Quantity</th>
            <th>Price (₹)</th>
            <th>Order Date & Time</th>
            <th>Payment Type</th>         <!-- Added -->
            <th>Total Amount (₹)</th>     <!-- Added -->
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $sr = 1;
          $stmt = $pdo->query("
            SELECT 
              orders.id as order_id,
              users.name as user_name,
              products.name as product_name,
              order_items.quantity,
              order_items.price,
              orders.created_at,
              orders.status,
              orders.payment_mode,         -- Added
              orders.total                 -- Added
            FROM orders
            JOIN users ON orders.user_id = users.id
            JOIN order_items ON orders.id = order_items.order_id
            JOIN products ON order_items.product_id = products.id
            ORDER BY orders.id DESC
          ");
          while($row = $stmt->fetch()):
          ?>
          <tr>
            <td><?= $sr++ ?></td>
            <td><?= htmlspecialchars($row['user_name']) ?></td>
            <td><?= htmlspecialchars($row['product_name']) ?></td>
            <td><?= $row['quantity'] ?></td>
            <td><?= number_format($row['price'],2) ?></td>
            <td><?= $row['created_at'] ?></td>
            <td><?= htmlspecialchars($row['payment_mode']) ?></td>      <!-- Payment Type -->
            <td><?= number_format($row['total'],2) ?></td>              <!-- Total Amount -->
            <td>
              <?php if ($row['status'] === 'pending'): ?>
                <span class="badge bg-warning text-dark">Pending</span>
              <?php elseif ($row['status'] === 'completed'): ?>
                <span class="badge bg-success">Completed</span>
              <?php elseif ($row['status'] === 'cancelled'): ?>
                <span class="badge bg-danger">Cancelled</span>
              <?php else: ?>
                <span class="badge bg-secondary"><?= htmlspecialchars($row['status']) ?></span>
              <?php endif; ?>
            </td>
            <td>
              <a href="../product-detail.php?id=<?= $row['order_id'] ?>" class="btn btn-sm btn-info rounded-pill">View</a>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>