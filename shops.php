<?php
session_start();
require_once 'config/database.php';
include 'includes/header.php';

// Fetch all active barbers (shops)
$stmt = $pdo->query("SELECT userid, name, email FROM users WHERE role = 'barber' AND is_active = 1 LIMIT 4");
$shops = $stmt->fetchAll();
?>

<div class="container py-5 mt-5">
    <!-- Search / Filter Bar -->
    <div class="glass-card p-4 mb-5 sticky-top mt-3" style="z-index: 1020; top: 70px;">
        <form method="GET" action="shops.php" class="row g-3 align-items-center">
            <div class="col-md-4">
                <input type="text" name="location" class="form-control bg-dark text-white border-secondary" placeholder="Location">
            </div>
            <div class="col-md-3">
                <input type="date" name="date" class="form-control bg-dark text-white border-secondary">
            </div>
            <div class="col-md-3">
                <input type="time" name="time" class="form-control bg-dark text-white border-secondary">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary-custom w-100">Search</button>
            </div>
        </form>
    </div>

    <!-- Recommendations -->
    <div class="mb-4">
        <h2 class="fw-bold text-white">Recommended For You</h2>
        <p class="text-grey">Top-rated barbers available near you.</p>
    </div>

    <div class="row g-4">
        <?php foreach($shops as $shop): ?>
        <div class="col-md-6 col-lg-3">
            <div class="glass-card h-100 review-card overflow-hidden">
                <div class="bg-dark" style="height: 150px; display: flex; align-items: center; justify-content: center;">
                    <span class="text-secondary fs-1">&#128136;</span>
                </div>
                <div class="p-4 position-relative">
                    <span class="badge bg-white text-black position-absolute top-0 start-50 translate-middle rounded-pill px-3 py-2 border border-dark">
                        3 slots left today!
                    </span>
                    <h5 class="text-white mt-3 fw-bold"><?php echo htmlspecialchars($shop['name']); ?></h5>
                    <p class="text-light-grey small mb-3">Premium cuts & styling.</p>
                    <a href="shop_profile.php?id=<?php echo $shop['userid']; ?>" class="btn btn-outline-light w-100 btn-sm">View Shop</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        
        <?php if(count($shops) === 0): ?>
            <div class="col-12 text-center py-5">
                <p class="text-grey">No shops available at the moment.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
