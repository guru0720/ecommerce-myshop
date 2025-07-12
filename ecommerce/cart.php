<?php
session_start();
require_once 'php/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT c.product_id, c.quantity, p.name, p.price, p.main_image FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$cartItems = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Cart - MyShop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(120deg, #f8fafc 0%, #e0e7ff 100%);
        }
        .cart-card {
            border-radius: 1.5rem;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.12);
            background: #fff;
        }
        .cart-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 0.5rem;
            border: 2px solid #e0e7ff;
            background: #f8fafc;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        @media (max-width: 576px) {
            .cart-card { padding: 1rem !important; }
            .cart-img { width: 40px; height: 40px; }
            .table-responsive { font-size: 0.95rem; }
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
      <div class="col-lg-10 col-md-11">
        <div class="card cart-card p-4">
          <h2 class="mb-4 text-center fw-bold text-primary">🛒 My Cart</h2>
          <?php if (empty($cartItems)): ?>
            <div class="alert alert-info text-center">Your cart is empty.</div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-bordered align-middle">
                <thead class="table-light">
                  <tr>
                    <th>Product</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $total = 0; ?>
                  <?php foreach ($cartItems as $item): ?>
                    <?php $subtotal = $item['price'] * $item['quantity']; $total += $subtotal; ?>
                    <tr>
                      <td><img src="uploads/<?= htmlspecialchars($item['main_image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="cart-img"></td>
                      <td><?= htmlspecialchars($item['name']) ?></td>
                      <td>₹<?= number_format($item['price'], 2) ?></td>
                      <td><?= $item['quantity'] ?></td>
                      <td>₹<?= number_format($subtotal, 2) ?></td>
                      <td>
                        <form method="post" action="php/remove-from-cart.php" onsubmit="return confirm('Remove this product from cart?');" style="display:inline;">
                          <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                          <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                        </form>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
                <tfoot>
                  <tr>
                    <th colspan="4" class="text-end">Total</th>
                    <th colspan="2">₹<?= number_format($total, 2) ?></th>
                  </tr>
                </tfoot>
              </table>
            </div>
          <?php endif; ?>
          <div class="d-flex flex-wrap justify-content-between mt-4 gap-2">
            <a href="product.php" class="btn btn-secondary">Continue Shopping</a>
            <?php if (!empty($cartItems)): ?>
              <a href="checkout.php" class="btn btn-success">Checkout</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>