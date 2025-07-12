<?php
require_once '../php/config.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}
$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM products WHERE id=?");
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) { header('Location: index.php'); exit; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'];
  $price = floatval($_POST['price']);
  $desc = $_POST['description'];
  $stock = intval($_POST['stock']);
  $img = $product['image'];
  if (!empty($_FILES['image']['name'])) {
    $img = basename($_FILES['image']['name']);
    move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/$img");
  }
  $stmt = $pdo->prepare("UPDATE products SET name=?, price=?, description=?, stock=?, image=? WHERE id=?");
  $stmt->execute([$name, $price, $desc, $stock, $img, $id]);
  header('Location: index.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Product - Admin Panel</title>
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
    .edit-product-card {
      border-radius: 1.5rem;
      box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.10);
      background: #fff;
      border: none;
      padding: 2rem 1.5rem;
      max-width: 600px;
      margin: 2rem auto;
    }
    .form-label {
      font-weight: 500;
    }
    .btn {
      border-radius: 2rem !important;
      font-weight: 500;
    }
    @media (max-width: 576px) {
      .edit-product-card { padding: 1rem 0.5rem; }
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
<div class="container">
  <div class="edit-product-card">
    <h2 class="mb-4 fw-bold text-primary text-center">Edit Product</h2>
    <form method="post" enctype="multipart/form-data">
      <div class="mb-3">
        <label class="form-label">Product Name</label>
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Price (₹)</label>
        <input type="number" name="price" step="0.01" class="form-control" value="<?= $product['price'] ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" required><?= htmlspecialchars($product['description']) ?></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Stock</label>
        <input type="number" name="stock" class="form-control" value="<?= $product['stock'] ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Image</label>
        <input type="file" name="image" class="form-control">
        <?php if ($product['image']): ?>
          <img src="../uploads/<?= htmlspecialchars($product['image']) ?>" alt="" width="80" class="mt-2 rounded shadow-sm border">
        <?php endif; ?>
      </div>
      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary flex-fill">Update Product</button>
        <a href="index.php" class="btn btn-secondary flex-fill">Back</a>
      </div>
    </form>
  </div>
</div>
</body>
</html>