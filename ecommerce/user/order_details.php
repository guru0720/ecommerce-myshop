<?php
session_start();
require_once '../php/config.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$order_id = $_GET['id'];

// Get order details
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: myorders.php");
    exit;
}

// Get order items
$stmt = $pdo->prepare("SELECT oi.*, p.name, p.main_image FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
$stmt->execute([$order_id]);
$orderItems = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Details - MyShop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(120deg, #f8fafc 0%, #e0e7ff 100%);
        }
        .order-card {
            border-radius: 1.5rem;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.12);
            background: #fff;
        }
        .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 0.5rem;
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
          border-radius: 2rem;
          padding: 0.5rem 1.2rem;
          transition: background 0.2s, color 0.2s, box-shadow 0.2s;
          box-shadow: 0 2px 8px 0 rgba(31,38,135,0.04);
        }
        .navbar-custom .nav-link.active,
        .navbar-custom .nav-link:hover {
          background: rgba(255,255,255,0.22);
          color: #fff !important;
          box-shadow: 0 4px 16px 0 rgba(31,38,135,0.10);
        }
    </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-custom mb-4">
    <div class="container">
      <a class="navbar-brand" href="../index.php">🛒 MyShop</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="../product.php">Products</a></li>
          <li class="nav-item"><a class="nav-link" href="../cart.php">Cart</a></li>
          <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
          <li class="nav-item"><a class="nav-link" href="myorders.php">My Orders</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        <div class="card order-card p-4">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-primary">Order Details</h2>
            <a href="myorders.php" class="btn btn-outline-secondary">← Back to Orders</a>
          </div>
          
          <div class="row mb-4">
            <div class="col-md-6">
              <div class="card bg-light">
                <div class="card-body">
                  <h5 class="card-title">Order Information</h5>
                  <p><strong>Order ID:</strong> #<?= $order['id'] ?></p>
                  <p><strong>Date:</strong> <?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></p>
                  <p><strong>Payment Mode:</strong> <?= htmlspecialchars($order['payment_mode']) ?></p>
                  <p><strong>Status:</strong> 
                    <span class="badge <?= $order['status'] === 'Done' ? 'bg-success' : 'bg-warning' ?>">
                      <?= htmlspecialchars($order['status']) ?>
                    </span>
                  </p>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card bg-light">
                <div class="card-body">
                  <h5 class="card-title">Order Summary</h5>
                  <p><strong>Total Amount:</strong> ₹<?= number_format($order['total'], 2) ?></p>
                  <a href="myorders.php?download=1&order_id=<?= $order['id'] ?>" class="btn btn-success">
                    📄 Download Bill
                  </a>
                </div>
              </div>
            </div>
          </div>

          <h4 class="mb-3">Order Items</h4>
          <div class="table-responsive">
            <table class="table table-bordered align-middle">
              <thead class="table-light">
                <tr>
                  <th>Product</th>
                  <th>Name</th>
                  <th>Price</th>
                  <th>Quantity</th>
                  <th>Subtotal</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($orderItems as $item): ?>
                  <tr>
                    <td>
                      <img src="../uploads/<?= htmlspecialchars($item['main_image']) ?>" 
                           alt="Product" class="product-img">
                    </td>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td>₹<?= number_format($item['price'], 2) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td>₹<?= number_format($item['total_price'], 2) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
              <tfoot class="table-light">
                <tr>
                  <th colspan="4" class="text-end">Total:</th>
                  <th>₹<?= number_format($order['total'], 2) ?></th>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>