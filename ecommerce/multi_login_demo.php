<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Multi-Login System Demo - MyShop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(120deg, #f8fafc 0%, #e0e7ff 100%); }
        .demo-card { background: rgba(255,255,255,0.9); border-radius: 1rem; box-shadow: 0 8px 32px rgba(31, 38, 135, 0.12); }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="demo-card p-4 mb-4">
                    <h1 class="text-center mb-4">Multi-Login System Demo</h1>
                    <p class="text-center">Your ecommerce website now supports multiple user roles with different access levels.</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="demo-card p-4 text-center">
                    <i class="fas fa-user-shield fa-3x text-danger mb-3"></i>
                    <h4>Admin</h4>
                    <p>Full system access, manage everything</p>
                    <ul class="list-unstyled text-start">
                        <li>✓ Manage all products</li>
                        <li>✓ Manage all users</li>
                        <li>✓ View all orders</li>
                        <li>✓ System settings</li>
                    </ul>
                    <a href="admin/admin_login.php" class="btn btn-danger">Admin Login</a>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="demo-card p-4 text-center">
                    <i class="fas fa-user-tie fa-3x text-info mb-3"></i>
                    <h4>Manager</h4>
                    <p>Department-level management access</p>
                    <ul class="list-unstyled text-start">
                        <li>✓ Manage products</li>
                        <li>✓ View orders</li>
                        <li>✓ Generate reports</li>
                        <li>✗ User management</li>
                    </ul>
                    <small class="text-muted">Demo: manager@example.com / password</small>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="demo-card p-4 text-center">
                    <i class="fas fa-store fa-3x text-success mb-3"></i>
                    <h4>Vendor</h4>
                    <p>Sell products on the platform</p>
                    <ul class="list-unstyled text-start">
                        <li>✓ Add own products</li>
                        <li>✓ Manage inventory</li>
                        <li>✓ View own orders</li>
                        <li>✓ Commission tracking</li>
                    </ul>
                    <small class="text-muted">Demo: vendor@example.com / password</small>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="demo-card p-4 text-center">
                    <i class="fas fa-shopping-cart fa-3x text-primary mb-3"></i>
                    <h4>Customer</h4>
                    <p>Shop and place orders</p>
                    <ul class="list-unstyled text-start">
                        <li>✓ Browse products</li>
                        <li>✓ Add to cart</li>
                        <li>✓ Place orders</li>
                        <li>✓ Profile management</li>
                    </ul>
                    <a href="register.php" class="btn btn-primary">Register</a>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="demo-card p-4">
                    <h3>Implementation Features</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <h5>✅ Completed Features:</h5>
                            <ul>
                                <li>Enhanced authentication system</li>
                                <li>Role-based access control</li>
                                <li>Separate dashboards for each role</li>
                                <li>Database structure for multi-roles</li>
                                <li>Registration with role selection</li>
                                <li>Session management</li>
                                <li>Vendor dashboard with business info</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>🔧 Setup Instructions:</h5>
                            <ol>
                                <li>Run the SQL file: <code>mysql data/enhanced_multi_login.sql</code></li>
                                <li>Test different user roles</li>
                                <li>Register as vendor/customer</li>
                                <li>Use existing admin login</li>
                                <li>Demo accounts available for manager/vendor</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12 text-center">
                <a href="index.php" class="btn btn-lg btn-primary">Go to Main Site</a>
                <a href="login.php" class="btn btn-lg btn-outline-primary">Login</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</body>
</html>