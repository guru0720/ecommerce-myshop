<?php
require_once '../php/config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'];
  $price = floatval($_POST['price']);
  $desc = $_POST['description'];
  $stock = intval($_POST['stock']);

  // Main image upload
  $main_image = '';
  if (!empty($_FILES['main_image']['name'])) {
    $main_image = basename($_FILES['main_image']['name']);
    $target = "../uploads/$main_image";
    move_uploaded_file($_FILES['main_image']['tmp_name'], $target);
  }

  // Additional images upload (max 5)
  $additional_images = [];
  if (!empty($_FILES['additional_images']['name'][0])) {
    $count = 0;
    foreach ($_FILES['additional_images']['name'] as $key => $imgName) {
      if ($count >= 5) break;
      $img = basename($imgName);
      $target = "../uploads/$img";
      if (move_uploaded_file($_FILES['additional_images']['tmp_name'][$key], $target)) {
        $additional_images[] = $img;
        $count++;
      }
    }
  }
  $additional_images_json = json_encode($additional_images);

  $stmt = $pdo->prepare("INSERT INTO products (name, price, description, stock, image, main_image) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->execute([$name, $price, $desc, $stock, $additional_images_json, $main_image]);
  header('Location: products.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Product - Admin Panel</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5">
    <h2 class="mb-4">Add New Product</h2>
    <form method="post" enctype="multipart/form-data">
      <div class="mb-3">
        <label class="form-label">Product Name</label>
        <input type="text" name="name" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Price (₹)</label>
        <input type="number" name="price" step="0.01" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" required></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Stock</label>
        <input type="number" name="stock" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Main Image</label>
        <input type="file" name="main_image" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Additional Images (Max 5)</label>
        <input type="file" name="additional_images[]" class="form-control" multiple>
        <small class="text-muted">You can select up to 5 images.</small>
      </div>
      <button type="submit" class="btn btn-success">Add Product</button>
      <a href="index.php" class="btn btn-secondary">Back</a>
    </form>
  </div>
</body>
</html>