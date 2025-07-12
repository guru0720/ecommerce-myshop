<!-- filepath: c:\xampp\htdocs\ecommarce\product-detail.php -->
<?php
session_start();
require_once 'php/config.php';
$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM products WHERE id=?");
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) { header("Location: product.php"); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($product['name']) ?> - MyShop</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
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
    }
    .navbar-custom .nav-link.active,
    .navbar-custom .nav-link:hover {
      background: rgba(255,255,255,0.18);
      color: #fff !important;
    }
    .product-card {
      border-radius: 1.5rem;
      box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.12);
      background: #fff;
      padding: 2rem 1.5rem;
    }
    .main-img {
      width: 100%;
      max-height: 350px;
      object-fit: cover;
      border-radius: 1rem;
      box-shadow: 0 2px 12px #0001;
      margin-bottom: 1rem;
      background: #f8fafc;
    }
    .thumb-img {
      width: 60px;
      height: 60px;
      object-fit: cover;
      cursor: pointer;
      border-radius: 0.5rem;
      border: 2px solid #e0e7ff;
      background: #f8fafc;
      transition: border-color 0.2s;
    }
    .thumb-img:hover {
      border-color: #0d6efd;
    }
    .product-title {
      font-size: 2rem;
      font-weight: bold;
      color: #0d6efd;
    }
    .product-price {
      font-size: 1.5rem;
      color: #16a34a;
      font-weight: 600;
    }
    @media (max-width: 768px) {
      .product-card { padding: 1rem 0.5rem; }
      .main-img { max-height: 220px; }
      .product-title { font-size: 1.3rem; }
      .product-price { font-size: 1.1rem; }
    }
  </style>
</head>
<body>
  <!-- Navbar (same as index.php) -->
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
              <a class="nav-link" href="user/profile.php">
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
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        <div class="row g-4 product-card">
          <!-- Main and Additional Images with Hover Preview -->
          <div class="col-md-5 text-center">
            <?php
              $main_image = !empty($product['main_image']) ? $product['main_image'] : '';
              $additional_images = json_decode($product['image'] ?? '[]', true) ?: [];
              $all_images = $main_image ? array_merge([$main_image], $additional_images) : $additional_images;
            ?>
            <img id="mainProductImg" src="uploads/<?= htmlspecialchars($all_images[0] ?? 'default.png') ?>" class="main-img shadow-sm" alt="<?= htmlspecialchars($product['name']) ?>">
            <?php if (count($all_images) > 1): ?>
              <div class="d-flex gap-2 justify-content-center">
                <?php foreach ($all_images as $img): ?>
                  <img src="uploads/<?= htmlspecialchars($img) ?>" alt="" class="thumb-img" onmouseover="showMainImage('uploads/<?= htmlspecialchars($img) ?>')">
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
          <div class="col-md-7">
            <h2 class="product-title mb-2"><?= htmlspecialchars($product['name']) ?></h2>
            <div class="product-price mb-3">Rs. <?= number_format($product['price'],2) ?></div>
            <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
            <p><strong>Stock:</strong> <?= $product['stock'] > 0 ? "<span class='text-success'>In Stock</span>" : "<span class='text-danger'>Out of Stock</span>" ?></p>
            <?php if ($product['stock'] > 0): ?>
              <form method="post" action="php/add-to-cart.php" class="d-flex align-items-center flex-wrap gap-2">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <input type="number" name="quantity" value="1" min="1" max="<?= $product['stock'] ?>" class="form-control w-auto" style="max-width:90px;">
                <button type="submit" class="btn btn-primary">Add to Cart</button>
              </form>
            <?php endif; ?>
            <div class="mt-4">
              <a href="product.php" class="btn btn-outline-secondary">Back to Products</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function showMainImage(src) {
      document.getElementById('mainProductImg').src = src;
    }
  </script>
</body>
</html>