<?php
// filepath: c:\xampp\htdocs\ecommarce\user\update_profile.php
session_start();
require_once '../php/config.php';
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT status FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
if ($user && $user['status'] === 'frozen') {
    echo '<div class="alert alert-danger text-center">Your account is frozen.</div>';
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';

// Fetch current user data
$stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Fetch or create userprofile data
$stmt2 = $pdo->prepare("SELECT * FROM userprofile WHERE user_id = ?");
$stmt2->execute([$user_id]);
$profile = $stmt2->fetch();

if (!$profile) {
    // Insert empty profile if not exists
    $pdo->prepare("INSERT INTO userprofile (user_id) VALUES (?)")->execute([$user_id]);
    $stmt2->execute([$user_id]);
    $profile = $stmt2->fetch();
}

if (!$user) {
    echo "<div class='alert alert-danger'>User not found.</div>";
    exit;
}

// Update logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $gender = $_POST['gender'] ?? '';
    $dob = $_POST['dob'] ?? null;
    $education = trim($_POST['education'] ?? '');
    $instagram = trim($_POST['instagram'] ?? '');
    $telegram = trim($_POST['telegram'] ?? '');
    $linkedin = trim($_POST['linkedin'] ?? '');
    $youtube = trim($_POST['youtube'] ?? '');
    $photo = $profile['photo'] ?? '';
    if (!empty($_POST['remove_photo'])) {
        // Remove photo file from server if exists
        if (!empty($photo) && file_exists("../uploads/$photo")) {
            unlink("../uploads/$photo");
        }
        $photo = '';
    } elseif (!empty($_FILES['photo']['name'])) {
        $photo = uniqid('profile_') . '_' . basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], "../uploads/$photo");
    }

    if ($name && $email) {
        $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?")->execute([$name, $email, $user_id]);
        $pdo->prepare("UPDATE userprofile SET photo = ?, phone = ?, address = ?, gender = ?, dob = ?, education = ?, instagram = ?, telegram = ?, linkedin = ?, youtube = ? WHERE user_id = ?")
            ->execute([$photo, $phone, $address, $gender, $dob, $education, $instagram, $telegram, $linkedin, $youtube, $user_id]);
        $_SESSION['user_name'] = $name;
        $message = '<div class="alert alert-success">Profile updated successfully.</div>';
        // Refresh user data
        $user['name'] = $name;
        $user['email'] = $email;
        $profile['phone'] = $phone;
        $profile['address'] = $address;
        $profile['gender'] = $gender;
        $profile['dob'] = $dob;
        $profile['education'] = $education;
        $profile['instagram'] = $instagram;
        $profile['telegram'] = $telegram;
        $profile['linkedin'] = $linkedin;
        $profile['youtube'] = $youtube;
        $profile['photo'] = $photo;
    } else {
        $message = '<div class="alert alert-danger">Name and Email are required.</div>';
    }
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
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
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
    @media (max-width: 576px) {
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
            <li class="nav-item">
              <a class="nav-link active" href="profile.php">
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
            <div class="col-lg-6 col-md-8">
                <div class="card profile-card p-4">
                    <h2 class="card-title mb-4 text-center fw-bold text-primary">Update Profile</h2>
                    <?= $message ?>
                    <form method="post" enctype="multipart/form-data" class="row g-3">
                        <div class="col-12 text-center">
                            <?php if (!empty($profile['photo'])): ?>
                                <img src="../uploads/<?= htmlspecialchars($profile['photo']) ?>" alt="Profile Photo" class="profile-photo-preview shadow-sm">
                                <div class="form-check d-inline-block mt-2 ms-2">
                                    <input class="form-check-input" type="checkbox" name="remove_photo" value="1" id="removePhoto">
                                    <label class="form-check-label small" for="removePhoto">Remove Photo</label>
                                </div>
                            <?php else: ?>
                                <div class="profile-photo-preview bg-light d-flex align-items-center justify-content-center" style="font-size:2.5rem;">👤</div>
                            <?php endif; ?>
                            <label class="form-label d-block mt-2">Profile Photo</label>
                            <input type="file" name="photo" class="form-control">
                            <small class="text-muted">Leave blank to keep existing photo.</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($profile['phone'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-select">
                                <option value="">Select</option>
                                <option value="Male" <?= ($profile['gender'] ?? '') == 'Male' ? 'selected' : '' ?>>Male</option>
                                <option value="Female" <?= ($profile['gender'] ?? '') == 'Female' ? 'selected' : '' ?>>Female</option>
                                <option value="Other" <?= ($profile['gender'] ?? '') == 'Other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="dob" class="form-control" value="<?= htmlspecialchars($profile['dob'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="2"><?= htmlspecialchars($profile['address'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Education</label>
                            <input type="text" name="education" class="form-control" value="<?= htmlspecialchars($profile['education'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Instagram Link</label>
                            <input type="url" name="instagram" class="form-control" value="<?= htmlspecialchars($profile['instagram'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telegram Link</label>
                            <input type="url" name="telegram" class="form-control" value="<?= htmlspecialchars($profile['telegram'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">LinkedIn Link</label>
                            <input type="url" name="linkedin" class="form-control" value="<?= htmlspecialchars($profile['linkedin'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">YouTube Link</label>
                            <input type="url" name="youtube" class="form-control" value="<?= htmlspecialchars($profile['youtube'] ?? '') ?>">
                        </div>
                        <div class="col-12 d-grid gap-2 mt-3">
                            <button type="submit" class="btn btn-success btn-lg">Update</button>
                            <a href="profile.php" class="btn btn-outline-secondary">Back to Profile</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Cropper Modal -->
<div class="modal fade" id="cropperModal" tabindex="-1" aria-labelledby="cropperModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="cropperModalLabel">Edit Profile Image</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <img id="cropperImage" style="max-width:100%; max-height:300px;">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="cropImageBtn">Crop & Upload</button>
      </div>
    </div>
  </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
let cropper;
const input = document.querySelector('input[type="file"][name="photo"]');
const cropperModal = new bootstrap.Modal(document.getElementById('cropperModal'));
const cropperImage = document.getElementById('cropperImage');
const cropImageBtn = document.getElementById('cropImageBtn');

input.addEventListener('change', function(e) {
  const file = e.target.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = function(event) {
    cropperImage.src = event.target.result;
    cropperModal.show();
    if (cropper) cropper.destroy();
    cropper = new Cropper(cropperImage, {
      aspectRatio: 1,
      viewMode: 1,
      minContainerWidth: 300,
      minContainerHeight: 300,
    });
  };
  reader.readAsDataURL(file);
});

// When user clicks "Crop & Upload"
cropImageBtn.addEventListener('click', function() {
  if (cropper) {
    cropper.getCroppedCanvas({
      width: 300,
      height: 300,
      imageSmoothingQuality: 'high'
    }).toBlob(function(blob) {
      // Create a new file input for the cropped image
      const fileInput = document.querySelector('input[type="file"][name="photo"]');
      const dataTransfer = new DataTransfer();
      const croppedFile = new File([blob], "profile_cropped.png", {type: "image/png"});
      dataTransfer.items.add(croppedFile);
      fileInput.files = dataTransfer.files;
      cropperModal.hide();
    }, 'image/png');
  }
});
</script>
</body>
</html>