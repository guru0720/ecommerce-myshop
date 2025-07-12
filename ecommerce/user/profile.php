<?php
session_start();
require_once '../php/config.php';

if (empty($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT name, email, status FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$stmt2 = $pdo->prepare("SELECT * FROM userprofile WHERE user_id = ?");
$stmt2->execute([$user_id]);
$profile = $stmt2->fetch();

if (!$user) {
    echo "<div class='alert alert-danger'>User not found.</div>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MyShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
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
            transition: background 0.2s, color 0.2s;
        }
        .navbar-custom .nav-link.active,
        .navbar-custom .nav-link:hover {
            background: rgba(255,255,255,0.18);
            color: #fff !important;
        }
        .card, .glass-card {
            border-radius: 1rem;
            box-shadow: 0 4px 16px 0 rgba(31, 38, 135, 0.08);
            transition: transform 0.15s, box-shadow 0.15s;
            background: rgba(255,255,255,0.18);
            backdrop-filter: blur(6px);
        }
        .card:hover, .glass-card:hover, .glass-card:focus-within {
            transform: translateY(-6px) scale(1.02);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.16);
        }
        .btn-primary, .btn-outline-primary {
            border-radius: 2rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: background 0.2s, box-shadow 0.2s, color 0.2s;
        }
        .btn-primary:hover, .btn-outline-primary:hover {
            background: linear-gradient(90deg, #6610f2 60%, #0d6efd 100%);
            color: #fff !important;
            box-shadow: 0 4px 16px 0 rgba(13,110,253,0.18);
            transform: translateY(-2px) scale(1.04);
        }
        .form-control {
            border-radius: 1rem;
            border: 1.5px solid #e0e7ff;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-control:focus {
            border-color: #6610f2;
            box-shadow: 0 0 0 0.15rem rgba(102,16,242,0.15);
        }
        .profile-card {
            border-radius: 1.5rem;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.12);
            background: #fff;
        }
        .profile-photo-preview {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #0d6efd22;
            margin-bottom: 1rem;
        }
        .profile-table th {
            width: 35%;
            background: #f1f5f9;
        }
        .profile-table td, .profile-table th {
            vertical-align: middle;
        }
        @media (max-width: 576px) {
            .profile-card { padding: 1rem !important; }
            .profile-photo-preview { width: 90px; height: 90px; }
            .card, .glass-card { padding: 1.2rem 0.5rem; }
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
          <li class="nav-item"><a class="nav-link<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? ' active' : '' ?>" href="../index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link<?= basename($_SERVER['PHP_SELF']) == 'product.php' ? ' active' : '' ?>" href="../product.php">Products</a></li>
          <li class="nav-item"><a class="nav-link<?= basename($_SERVER['PHP_SELF']) == 'cart.php' ? ' active' : '' ?>" href="../cart.php">Cart</a></li>
          <?php if (!empty($_SESSION['admin_id'])): ?>
            <li class="nav-item"><a class="nav-link" href="admin/dashboard.php">Admin Profile</a></li>
            <li class="nav-item"><a class="nav-link" href="admin/logout.php">Logout</a></li>
          <?php elseif (!empty($_SESSION['user_id'])): ?>
            <li class="nav-item"><a class="nav-link active" href="profile.php">Profile</a></li>
            <li class="nav-item"><a class="nav-link" href="myorders.php">My Orders</a></li>
          <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9">
                <div class="card profile-card p-4">
                    <div class="text-center">
                        <?php if (!empty($profile['photo'])): ?>
                            <img src="../uploads/<?= htmlspecialchars($profile['photo']) ?>" alt="Profile Photo" class="profile-photo-preview shadow-sm">
                        <?php else: ?>
                            <div class="profile-photo-preview bg-light d-flex align-items-center justify-content-center" style="font-size:2.5rem;">👤</div>
                        <?php endif; ?>
                        <h2 class="card-title mb-3 mt-2 fw-bold text-primary"><?= htmlspecialchars($user['name']) ?></h2>
                    </div>
                    <table class="table table-bordered profile-table mt-3">
                        <tbody>
                            <tr>
                                <th>Status</th>
                                <td>
                                    <?php
                                        $status = $user['status'] ?? 'active';
                                        if ($status === 'frozen') {
                                            echo "<span class='badge bg-warning text-dark'>Frozen</span>";
                                        } else {
                                            echo "<span class='badge bg-success'>Active</span>";
                                        }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                            </tr>
                            <?php if ($profile): ?>
                                <?php if (!empty($profile['phone'])): ?>
                                    <tr><th>Phone</th><td><?= htmlspecialchars($profile['phone']) ?></td></tr>
                                <?php endif; ?>
                                <?php if (!empty($profile['address'])): ?>
                                    <tr><th>Address</th><td><?= htmlspecialchars($profile['address']) ?></td></tr>
                                <?php endif; ?>
                                <?php if (!empty($profile['gender'])): ?>
                                    <tr><th>Gender</th><td><?= htmlspecialchars($profile['gender']) ?></td></tr>
                                <?php endif; ?>
                                <?php if (!empty($profile['dob'])): ?>
                                    <tr><th>Date of Birth</th><td><?= htmlspecialchars($profile['dob']) ?></td></tr>
                                <?php endif; ?>
                                <?php if (!empty($profile['education'])): ?>
                                    <tr><th>Education</th><td><?= htmlspecialchars($profile['education']) ?></td></tr>
                                <?php endif; ?>
                                <?php if (!empty($profile['instagram'])): ?>
                                    <tr><th>Instagram</th><td><a href="<?= htmlspecialchars($profile['instagram']) ?>" target="_blank"><?= htmlspecialchars($profile['instagram']) ?></a></td></tr>
                                <?php endif; ?>
                                <?php if (!empty($profile['telegram'])): ?>
                                    <tr><th>Telegram</th><td><a href="<?= htmlspecialchars($profile['telegram']) ?>" target="_blank"><?= htmlspecialchars($profile['telegram']) ?></a></td></tr>
                                <?php endif; ?>
                                <?php if (!empty($profile['linkedin'])): ?>
                                    <tr><th>LinkedIn</th><td><a href="<?= htmlspecialchars($profile['linkedin']) ?>" target="_blank"><?= htmlspecialchars($profile['linkedin']) ?></a></td></tr>
                                <?php endif; ?>
                                <?php if (!empty($profile['youtube'])): ?>
                                    <tr><th>YouTube</th><td><a href="<?= htmlspecialchars($profile['youtube']) ?>" target="_blank"><?= htmlspecialchars($profile['youtube']) ?></a></td></tr>
                                <?php endif; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <div class="d-flex flex-wrap justify-content-center gap-2 mt-4">
                        <a href="../index.php" class="btn btn-primary">Back to Home</a>
                        <a href="myorders.php" class="btn btn-info">My Orders</a>
                        <a href="update_profile.php" class="btn btn-warning">Update Profile</a>
                        <a href="../php/logout.php" class="btn btn-danger">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>