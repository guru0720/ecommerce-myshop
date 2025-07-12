<?php
session_start();
require_once '../php/config.php';
require_once '../php/fpdf.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle PDF download
if (isset($_GET['download']) && isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
    
    // Get order details
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $stmt->execute([$order_id, $user_id]);
    $order = $stmt->fetch();
    
    if ($order) {
        // Get order items
        $stmt = $pdo->prepare("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
        $stmt->execute([$order_id]);
        $orderItems = $stmt->fetchAll();
        
        // Generate PDF
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',16);
        $pdf->Cell(0,10,'MyShop - Order Bill',0,1,'C');
        $pdf->SetFont('Arial','',12);
        $pdf->Cell(0,10,'Order ID: ' . $order['id'],0,1);
        $pdf->Cell(0,10,'Payment Mode: ' . $order['payment_mode'],0,1);
        $pdf->Cell(0,10,'Status: ' . $order['status'],0,1);
        $pdf->Ln(5);

        // Table header
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(60,10,'Product',1);
        $pdf->Cell(30,10,'Price',1);
        $pdf->Cell(30,10,'Qty',1);
        $pdf->Cell(40,10,'Subtotal',1);
        $pdf->Ln();

        $pdf->SetFont('Arial','',12);
        foreach ($orderItems as $item) {
            $pdf->Cell(60,10,$item['name'],1);
            $pdf->Cell(30,10,'Rs.'.number_format($item['price'],2),1);
            $pdf->Cell(30,10,$item['quantity'],1);
            $pdf->Cell(40,10,'Rs.'.number_format($item['total_price'],2),1);
            $pdf->Ln();
        }
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(120,10,'Total',1);
        $pdf->Cell(40,10,'Rs.'.number_format($order['total'],2),1);

        $pdf->Output('D', 'Order_'.$order_id.'_Bill.pdf');
        exit;
    }
}

// Get user's orders
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders - MyShop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(120deg, #f8fafc 0%, #e0e7ff 100%);
        }
        .orders-card {
            border-radius: 1.5rem;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.12);
            background: #fff;
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
          <li class="nav-item"><a class="nav-link active" href="myorders.php">My Orders</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        <div class="card orders-card p-4">
          <h2 class="mb-4 text-center fw-bold text-primary">My Orders</h2>
          
          <?php if (empty($orders)): ?>
            <div class="alert alert-info text-center">You haven't placed any orders yet.</div>
            <div class="text-center">
                <a href="../product.php" class="btn btn-primary">Start Shopping</a>
            </div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-bordered align-middle">
                <thead class="table-light">
                  <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Payment Mode</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($orders as $order): ?>
                    <tr>
                      <td>#<?= $order['id'] ?></td>
                      <td><?= date('d M Y', strtotime($order['created_at'])) ?></td>
                      <td><?= htmlspecialchars($order['payment_mode']) ?></td>
                      <td>
                        <span class="badge <?= $order['status'] === 'Done' ? 'bg-success' : 'bg-warning' ?>">
                          <?= htmlspecialchars($order['status']) ?>
                        </span>
                      </td>
                      <td>₹<?= number_format($order['total'], 2) ?></td>
                      <td>
                        <a href="order_details.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary">View Details</a>
                        <a href="?download=1&order_id=<?= $order['id'] ?>" class="btn btn-sm btn-success">Download Bill</a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>