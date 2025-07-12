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
  <title>Payments - Admin Panel</title>
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
    .payments-table-card {
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
      .payments-table-card { padding: 1rem 0.5rem; }
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
  <div class="payments-table-card">
    <h2 class="mb-4 fw-bold text-primary text-center">Payments</h2>
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead>
          <tr>
            <th>Sr.</th>
            <th>User Name</th>
            <th>Product Name</th>
            <th>Order ID</th>
            <th>Amount</th>
            <th>Mode</th>
            <th>Status</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $sr = 1;
          $stmt = $pdo->query("
            SELECT 
              payments.id,
              users.name AS user_name,
              products.name AS product_name,
              payments.order_id,
              payments.amount,
              payments.mode,
              payments.status,
              payments.created_at
            FROM payments
            LEFT JOIN orders ON payments.order_id = orders.id
            LEFT JOIN users ON orders.user_id = users.id
            LEFT JOIN order_items ON orders.id = order_items.order_id
            LEFT JOIN products ON order_items.product_id = products.id
            ORDER BY payments.id DESC
          ");
          while($p = $stmt->fetch()):
          ?>
          <tr>
            <td><?= $sr++ ?></td>
            <td><?= htmlspecialchars($p['user_name']) ?></td>
            <td><?= htmlspecialchars($p['product_name']) ?></td>
            <td><?= $p['order_id'] ?></td>
            <td><?= $p['amount'] ?></td>
            <td><?= htmlspecialchars($p['mode']) ?></td>
            <td>
              <?php if ($p['status'] === 'pending'): ?>
                <span class="badge bg-warning text-dark">Pending</span>
              <?php elseif ($p['status'] === 'completed'): ?>
                <span class="badge bg-success">Completed</span>
              <?php elseif ($p['status'] === 'failed'): ?>
                <span class="badge bg-danger">Failed</span>
              <?php else: ?>
                <span class="badge bg-secondary"><?= htmlspecialchars($p['status']) ?></span>
              <?php endif; ?>
            </td>
            <td><?= $p['created_at'] ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>