<?php
session_start();
require_once 'config/database.php';
require_once 'classes/Auth.php';

$auth = new Auth($pdo);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'register') {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $res = $auth->register($name, $email, $password, $phone);
        if ($res['success']) {
            $success = $res['message'];
        } else {
            $error = $res['message'];
        }
    } elseif (isset($_POST['action']) && $_POST['action'] == 'login') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $res = $auth->login($email, $password);
        if ($res['success']) {
            // Smart routing redirect
            if (isset($_SESSION['redirect_to'])) {
                $url = $_SESSION['redirect_to'];
                unset($_SESSION['redirect_to']);
                header("Location: $url");
            } else {
                if ($_SESSION['role'] === 'barber') {
                    header("Location: barber_dashboard.php");
                } else {
                    header("Location: dashboard.php");
                }
            }
            exit;
        } else {
            $error = $res['message'];
        }
    }
}

// Check if we should show register tab by default
$is_register = isset($_GET['action']) && $_GET['action'] == 'register';
$smart_msg = $_GET['msg'] ?? '';

include 'includes/header.php';
?>

<div class="container py-5 d-flex justify-content-center align-items-center" style="min-height: calc(100vh - 200px); margin-top: 56px;">
    <div class="glass-card p-4 p-md-5" style="width: 100%; max-width: 500px;">
        <h2 class="text-center text-white fw-bold mb-4">Welcome</h2>
        
        <?php if($error): ?>
            <div class="alert alert-danger bg-transparent text-danger border-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="alert alert-success bg-transparent text-success border-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if($smart_msg): ?>
            <div class="alert alert-info bg-transparent text-info border-info"><?php echo htmlspecialchars($smart_msg); ?></div>
        <?php endif; ?>
        <?php if(isset($_SESSION['smart_route_msg'])): ?>
            <div class="alert alert-info bg-transparent text-info border-info"><?php echo $_SESSION['smart_route_msg']; unset($_SESSION['smart_route_msg']); ?></div>
        <?php endif; ?>

        <ul class="nav nav-pills nav-justified mb-4 border-bottom border-secondary pb-3" id="authTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link bg-transparent <?php echo !$is_register ? 'active text-white fw-bold' : 'text-grey'; ?>" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab">Login</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link bg-transparent <?php echo $is_register ? 'active text-white fw-bold' : 'text-grey'; ?>" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab">Register</button>
            </li>
        </ul>

        <div class="tab-content" id="authTabsContent">
            <!-- Login Form -->
            <div class="tab-pane fade <?php echo !$is_register ? 'show active' : ''; ?>" id="login" role="tabpanel">
                <form method="POST" action="login.php">
                    <input type="hidden" name="action" value="login">
                    <div class="mb-3">
                        <label class="form-label text-light-grey">Email address</label>
                        <input type="email" name="email" class="form-control bg-dark text-white border-secondary" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-light-grey">Password</label>
                        <input type="password" name="password" class="form-control bg-dark text-white border-secondary" required>
                    </div>
                    <button type="submit" class="btn btn-primary-custom w-100 py-2">Sign In</button>
                </form>
            </div>

            <!-- Register Form -->
            <div class="tab-pane fade <?php echo $is_register ? 'show active' : ''; ?>" id="register" role="tabpanel">
                <form method="POST" action="login.php?action=register">
                    <input type="hidden" name="action" value="register">
                    <div class="mb-3">
                        <label class="form-label text-light-grey">Full Name</label>
                        <input type="text" name="name" class="form-control bg-dark text-white border-secondary" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-light-grey">Email address</label>
                        <input type="email" name="email" class="form-control bg-dark text-white border-secondary" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-light-grey">Phone Number</label>
                        <input type="text" name="phone" class="form-control bg-dark text-white border-secondary" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-light-grey">Password</label>
                        <input type="password" name="password" class="form-control bg-dark text-white border-secondary" required>
                    </div>
                    <button type="submit" class="btn btn-primary-custom w-100 py-2">Create Account</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Simple script to toggle active styles for tabs
    document.querySelectorAll('#authTabs button[data-bs-toggle="tab"]').forEach((t) => {
        t.addEventListener('shown.bs.tab', (e) => {
            document.querySelectorAll('#authTabs button').forEach(b => {
                b.classList.remove('text-white', 'fw-bold');
                b.classList.add('text-grey');
            });
            e.target.classList.add('text-white', 'fw-bold');
            e.target.classList.remove('text-grey');
        });
    });
</script>

<?php include 'includes/footer.php'; ?>
