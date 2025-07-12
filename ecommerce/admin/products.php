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
  <title>Admin Panel - Products</title>
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
    .products-table-card {
      border-radius: 1.5rem;
      box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.10);
      background: #fff;
      border: none;
      padding: 2rem 1.5rem;
    }
    .table thead th {
      background: #e0e7ff;
    }
    .btn-sm {
      border-radius: 2rem !important;
      font-weight: 500;
      padding: 0.35rem 1.1rem;
    }
    @media (max-width: 576px) {
      .products-table-card { padding: 1rem 0.5rem; }
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
  <div class="products-table-card">
    <h2 class="mb-4 fw-bold text-primary text-center">Product Management</h2>
    <a href="add_product.php" class="btn btn-success mb-3">Add New Product</a>
    <div class="table-responsive">
      <table class="table table-bordered align-middle bg-white">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Price (₹)</th>
            <th>Stock</th>
            <th>Image</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
          while ($row = $stmt->fetch()):
          ?>
          <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= number_format($row['price'],2) ?></td>
            <td><?= $row['stock'] ?></td>
            <td>
              <?php if ($row['image']): ?>
                <img src="../uploads/<?= htmlspecialchars($row['image']) ?>" alt="" width="50">
              <?php endif; ?>
            </td>
            <td>
              <a href="edit_product.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
              <a href="delete_product.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this product?')">Delete</a>
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