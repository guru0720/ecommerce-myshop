<?php
require_once '../php/config.php';
$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("DELETE FROM products WHERE id=?");
$stmt->execute([$id]);
header('Location: products.php');
exit;
?>