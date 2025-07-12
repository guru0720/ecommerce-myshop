<?php
session_start();
require_once '../php/config.php';
require_once '../php/auth.php';

$auth = new Auth($pdo);
if (!$auth->isLoggedIn() || $auth->getUserRole() !== 'vendor') {
    header("Location: ../login.php");
    exit;
}

$vendor_stmt = $pdo->prepare("SELECT * FROM vendors WHERE user_id = ?");
$vendor_stmt->execute([$_SESSION['user_id']]);
$vendor = $vendor_stmt->fetch();

$products_stmt = $pdo->prepare("SELECT COUNT(*) as count FROM products WHERE vendor_id = ?");
$products_stmt->execute([$vendor['id']]);
$product_count = $products_stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vendor Dashboard - MyShop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand" href="../index.php">🛒 MyShop - Vendor</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="products.php">My Products</a>
                <a class="nav-link" href="orders.php">Orders</a>
                <a class="nav-link" href="../php/logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <h2>Vendor Dashboard</h2>
                <p>Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?>!</p>
                
                <?php if ($vendor['status'] === 'pending'): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-clock"></i> Your vendor account is pending approval.
                </div>
                <?php elseif ($vendor['status'] === 'suspended'): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-ban"></i> Your vendor account is suspended.
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-box fa-3x text-primary mb-3"></i>
                        <h5>My Products</h5>
                        <h3><?= $product_count ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-store fa-3x text-success mb-3"></i>
                        <h5>Business</h5>
                        <p><?= htmlspecialchars($vendor['business_name']) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-percentage fa-3x text-info mb-3"></i>
                        <h5>Commission</h5>
                        <h4><?= $vendor['commission_rate'] ?>%</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-check-circle fa-3x text-<?= $vendor['status'] === 'approved' ? 'success' : 'warning' ?> mb-3"></i>
                        <h5>Status</h5>
                        <span class="badge bg-<?= $vendor['status'] === 'approved' ? 'success' : 'warning' ?>"><?= ucfirst($vendor['status']) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <a href="add_product.php" class="btn btn-primary me-2">
                            <i class="fas fa-plus"></i> Add Product
                        </a>
                        <a href="products.php" class="btn btn-outline-primary me-2">
                            <i class="fas fa-list"></i> Manage Products
                        </a>
                        <a href="orders.php" class="btn btn-outline-success">
                            <i class="fas fa-shopping-cart"></i> View Orders
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>