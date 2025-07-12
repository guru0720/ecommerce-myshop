<?php
require 'config.php';

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';
$role = $_POST['role'] ?? 'customer';

if ($name && $email && $password && $password === $confirm) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email=?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        header("Location: ../register.php?error=exists");
        exit;
    }
    
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $hash, $role]);
    
    $user_id = $pdo->lastInsertId();
    
    // Create role-specific records
    if ($role === 'vendor') {
        $stmt = $pdo->prepare("INSERT INTO vendors (user_id, business_name, status) VALUES (?, ?, 'pending')");
        $stmt->execute([$user_id, $name . "'s Business"]);
    }
    
    header("Location: ../login.php?registered=1");
    exit;
} else {
    header("Location: ../register.php?error=invalid");
    exit;
}
?>