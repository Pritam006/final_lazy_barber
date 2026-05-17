<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lazy Barber | Book Your Appointment</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark glass-nav fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-white" href="index.php">
                <span class="text-grey">Lazy</span>Barber
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <?php if(isset($_SESSION['userid'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo ($_SESSION['role'] === 'barber') ? 'barber_dashboard.php' : 'dashboard.php'; ?>">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-outline-light btn-sm ms-3" href="logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="btn btn-outline-light btn-sm ms-3 fw-bold px-4 rounded-pill" href="login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-outline-light btn-sm ms-3 fw-bold px-4 rounded-pill" href="login.php?action=register">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <main class="main-content">
