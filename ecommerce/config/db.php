<?php
$pdo = new PDO("mysql:host=localhost;dbname=ecommerce1;charset=utf8mb4", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);
session_start();
?>