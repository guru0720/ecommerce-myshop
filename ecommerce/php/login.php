<?php
require 'config.php';
require 'auth.php';

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email && $password) {
    $auth = new Auth($pdo);
    $result = $auth->login($email, $password);
    
    if ($result['success']) {
        header("Location: " . $result['redirect']);
        exit;
    } else {
        header("Location: ../login.php?error=" . urlencode($result['message']));
        exit;
    }
}
header("Location: ../login.php?error=invalid");
exit;
?>