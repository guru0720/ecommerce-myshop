<?php
require_once '../php/config.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}
$stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Activity - Admin Panel</title>
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
    .user-table-card {
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
      .user-table-card { padding: 1rem 0.5rem; }
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
  <div class="user-table-card">
    <h2 class="mb-4 fw-bold text-primary text-center">User List</h2>
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php while($u = $stmt->fetch()): ?>
          <tr>
            <td><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['name']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= htmlspecialchars($u['role'] ?? 'user') ?></td>
            <td>
              <?php if ($u['status'] === 'frozen'): ?>
                <span class="badge bg-warning text-dark">Frozen</span>
              <?php else: ?>
                <span class="badge bg-success">Active</span>
              <?php endif; ?>
            </td>
            <td>
              <form method="post" action="user_action.php" style="display:inline;">
                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                <?php if ($u['status'] === 'frozen'): ?>
                  <button type="submit" name="action" value="unfreeze" class="btn btn-success btn-sm" onclick="return confirm('Unfreeze this account?');">Unfreeze</button>
                <?php else: ?>
                  <button type="submit" name="action" value="freeze" class="btn btn-warning btn-sm" onclick="return confirm('Freeze this account?');">Freeze</button>
                <?php endif; ?>
                </form>
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