<?php
session_start();
require_once 'php/config.php';
require_once __DIR__ . '/php/fpdf.php';

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT status FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
if ($user && $user['status'] === 'frozen') {
    echo '<div class="alert alert-danger text-center">Your account is frozen.</div>';
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT c.product_id, c.quantity, p.name, p.price, p.main_image FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$cartItems = $stmt->fetchAll();

$total = 0;
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}

$success = '';
$selected_payment = '';
$generate_pdf = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($cartItems)) {
    $selected_payment = $_POST['payment_mode'] ?? '';
    $status = ($selected_payment === 'Cash on Delivery') ? 'Pending' : 'Done';
    $total = 0;
    foreach ($cartItems as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    // 1. Insert into orders table
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, payment_mode, payment_method, status, total) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $selected_payment, $selected_payment, $status, $total]);
    $order_id = $pdo->lastInsertId();

    // Payment insert bhi yahin karein:
    $payment_status = ($selected_payment === 'Cash on Delivery') ? 'pending' : 'completed';
    $stmt_payment = $pdo->prepare("INSERT INTO payments (order_id, user_id, amount, payment_method, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt_payment->execute([$order_id, $user_id, $total, $selected_payment, $payment_status]);

    // 2. Insert each product into order_items table
    $stmt_item = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, total_price) VALUES (?, ?, ?, ?, ?)");
    foreach ($cartItems as $item) {
        // Validate all required fields
        if (empty($item['product_id']) || empty($item['quantity']) || empty($item['price'])) {
            error_log("Missing required fields for cart item: " . print_r($item, true));
            continue;
        }
        $total_price = $item['price'] * $item['quantity'];
        $stmt_item->execute([$order_id, $item['product_id'], $item['quantity'], $item['price'], $total_price]);
    }

    // 3. Clear cart
    $pdo->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$user_id]);
    $_SESSION['order_success'] = "Order placed successfully!";
    header("Location: index.php");
    exit;
}

// PDF generation logic
if ($generate_pdf) {
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',16);
    $pdf->Cell(0,10,'MyShop - Order Bill',0,1,'C');
    $pdf->SetFont('Arial','',12);
    $pdf->Cell(0,10,'Payment Mode: ' . $selected_payment,0,1);
    $pdf->Ln(5);

    // Table header
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(60,10,'Product',1);
    $pdf->Cell(30,10,'Price',1);
    $pdf->Cell(30,10,'Qty',1);
    $pdf->Cell(40,10,'Subtotal',1);
    $pdf->Ln();

    $pdf->SetFont('Arial','',12);
    foreach ($cartItems as $item) {
        $pdf->Cell(60,10,$item['name'],1);
        $pdf->Cell(30,10,'₹'.number_format($item['price'],2),1);
        $pdf->Cell(30,10,$item['quantity'],1);
        $pdf->Cell(40,10,'₹'.number_format($item['price']*$item['quantity'],2),1);
        $pdf->Ln();
    }
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(120,10,'Total',1);
    $pdf->Cell(40,10,'₹'.number_format($total,2),1);
    $pdf->Ln();

    // Output PDF
    $pdf->Output('D', 'Order_Bill.pdf');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout - MyShop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(120deg, #f8fafc 0%, #e0e7ff 100%);
        }
        .checkout-card {
            border-radius: 1.5rem;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.12);
            background: #fff;
        }
        .checkout-img {
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
            .checkout-card { padding: 1rem !important; }
            .checkout-img { width: 40px; height: 40px; }
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
      <div class="col-lg-9 col-md-11">
        <div class="card checkout-card p-4">
          <h2 class="mb-4 text-center fw-bold text-primary">Checkout</h2>
          <?php if ($success): ?>
            <div class="alert alert-success text-center"><?= $success ?></div>
            <form method="post" class="text-center">
                <input type="hidden" name="payment_mode" value="<?= htmlspecialchars($selected_payment) ?>">
                <button type="submit" name="download_pdf" class="btn btn-primary">Download Bill (PDF)</button>
            </form>
            <div class="text-center mt-3">
                <a href="product.php" class="btn btn-secondary">Shop More</a>
            </div>
          <?php elseif (empty($cartItems)): ?>
            <div class="alert alert-info text-center">Your cart is empty.</div>
            <div class="text-center">
                <a href="product.php" class="btn btn-secondary">Continue Shopping</a>
            </div>
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
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($cartItems as $item): ?>
                    <tr>
                      <td><img src="uploads/<?= htmlspecialchars($item['main_image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="checkout-img"></td>
                      <td><?= htmlspecialchars($item['name']) ?></td>
                      <td>₹<?= number_format($item['price'], 2) ?></td>
                      <td><?= $item['quantity'] ?></td>
                      <td>₹<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
                <tfoot>
                  <tr>
                    <th colspan="4" class="text-end">Total</th>
                    <th>₹<?= number_format($total, 2) ?></th>
                  </tr>
                </tfoot>
              </table>
            </div>
            <form method="post" class="row g-3 mt-4">
                <div class="col-md-6">
                    <label for="payment_mode" class="form-label">Payment Mode</label>
                    <select class="form-select" id="payment_mode" name="payment_mode" required>
                        <option value="">Select Payment Mode</option>
                        <option value="Cash on Delivery">Cash on Delivery</option>
                        <option value="Online Payment">Online Payment</option>
                    </select>
                </div>
                <div class="col-12 d-flex flex-wrap gap-2 mt-2">
                    <button type="submit" class="btn btn-success">Place Order</button>
                    <a href="cart.php" class="btn btn-outline-secondary">Back to Cart</a>
                </div>
            </form>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</body>
</html>