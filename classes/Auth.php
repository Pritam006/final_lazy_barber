<?php
// classes/Auth.php
require_once __DIR__ . '/../config/database.php';

class Auth {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function register($name, $email, $password, $phone, $role = 'customer') {
        // Check if email exists
        $stmt = $this->pdo->prepare("SELECT userid FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Email already registered.'];
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        
        $stmt = $this->pdo->prepare("INSERT INTO users (name, email, password_hash, phone, role) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $email, $hash, $phone, $role])) {
            return ['success' => true, 'message' => 'Registration successful. Please login.'];
        }
        return ['success' => false, 'message' => 'Registration failed. Please try again.'];
    }

    public function login($email, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            if (!$user['is_active']) {
                return ['success' => false, 'message' => 'Account is inactive.'];
            }
            
            $_SESSION['userid'] = $user['userid'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['last_activity'] = time(); // For 30-min timeout
            
            return ['success' => true, 'user' => $user];
        }
        return ['success' => false, 'message' => 'Invalid email or password.'];
    }

    public static function checkSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
            // 30 mins timeout
            session_unset();
            session_destroy();
            return false;
        }
        
        if (isset($_SESSION['userid'])) {
            $_SESSION['last_activity'] = time();
            return true;
        }
        return false;
    }
}
?>
