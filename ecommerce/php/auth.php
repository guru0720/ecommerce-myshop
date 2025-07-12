<?php
require_once 'config.php';

class Auth {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function login($email, $password) {
        // Check in users table first
        $stmt = $this->pdo->prepare("SELECT id, name, email, password, role, status FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] !== 'active') {
                return ['success' => false, 'message' => 'Account is suspended'];
            }
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            return ['success' => true, 'role' => $user['role'], 'redirect' => $this->getRedirectUrl($user['role'])];
        }
        
        // Check in admins table for backward compatibility
        $stmt = $this->pdo->prepare("SELECT id, email, password FROM admins WHERE email = ?");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['user_role'] = 'admin';
            return ['success' => true, 'role' => 'admin', 'redirect' => '../admin/dashboard.php'];
        }
        
        return ['success' => false, 'message' => 'Invalid credentials'];
    }
    
    private function getRedirectUrl($role) {
        switch ($role) {
            case 'admin':
                return '../admin/dashboard.php';
            case 'vendor':
                return '../vendor/dashboard.php';
            case 'manager':
                return '../manager/dashboard.php';
            case 'customer':
            default:
                return '../index.php';
        }
    }
    
    public function logout() {
        session_destroy();
        header("Location: ../index.php");
        exit;
    }
    
    public function isLoggedIn() {
        return !empty($_SESSION['user_id']) || !empty($_SESSION['admin_id']);
    }
    
    public function getUserRole() {
        return $_SESSION['user_role'] ?? 'guest';
    }
    
    public function hasPermission($permission) {
        $role = $this->getUserRole();
        
        $permissions = [
            'admin' => ['all'],
            'manager' => ['products', 'orders', 'reports'],
            'vendor' => ['own_products', 'own_orders'],
            'customer' => ['shop', 'profile']
        ];
        
        return in_array('all', $permissions[$role] ?? []) || in_array($permission, $permissions[$role] ?? []);
    }
}
?>