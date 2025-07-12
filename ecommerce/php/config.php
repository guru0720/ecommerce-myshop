<?php
$pdo = new PDO("mysql:host=localhost;dbname=ecommerce1;charset=utf8mb4", "root", "", [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Check if the user is logged in


?>