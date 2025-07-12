<?php
// filepath: c:\xampp\htdocs\ecommarce\admin\user_action.php
session_start();
require_once '../php/config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['user_id']) && !empty($_POST['action'])) {
    $user_id = intval($_POST['user_id']);
    $action = $_POST['action'];

    if ($action === 'freeze') {
        $pdo->prepare("UPDATE users SET status='frozen' WHERE id=?")->execute([$user_id]);
    } elseif ($action === 'unfreeze') {
        $pdo->prepare("UPDATE users SET status='active' WHERE id=?")->execute([$user_id]);
    }
}
header('Location: users.php');
exit;