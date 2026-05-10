<?php
session_start();
require_once 'config/database.php';
include 'includes/header.php';
// Handle search
$searchQuery = $_GET['search'] ?? '';
$searchParam = "%$searchQuery%";

// Fetch shops
if (!empty($searchQuery)) {
    $stmt = $pdo->prepare("SELECT shopid, name, address, suburb, open_time, phone FROM SHOPS WHERE is_active = 1 AND (name LIKE ? OR suburb LIKE ?)");
    $stmt->execute([$searchParam, $searchParam]);
} else {
    // Show 5 recommendations
    $stmt = $pdo->query("SELECT shopid, name, address, suburb, open_time, phone FROM SHOPS WHERE is_active = 1 LIMIT 5");
}
$shops = $stmt->fetchAll();
?>

<div class="container py-5 mt-5">
    <!-- Search / Filter Bar -->
    <div class="glass-card p-4 mb-5 sticky-top mt-3" style="z-index: 1020; top: 70px;">
        <form method="GET" action="shops.php" class="row g-3 align-items-center">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control bg-dark text-white border-secondary" placeholder="Search by Shop Name or Suburb" value="<?php echo htmlspecialchars($searchQuery); ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary-custom w-100">Search</button>
            </div>
        </form>
    </div>

    <!-- Recommendations -->
    <div class="mb-4">
        <h2 class="fw-bold text-white"><?php echo !empty($searchQuery) ? "Search Results for '" . htmlspecialchars($searchQuery) . "'" : "Recommended For You"; ?></h2>
        <?php if(empty($searchQuery)): ?>
        <p class="text-grey">Top-rated barbers available near you.</p>
        <?php endif; ?>
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
                    <h5 class="text-white mt-3 fw-bold mb-1"><?php echo htmlspecialchars($shop['name']); ?></h5>
                    <p class="text-light-grey small mb-2"><i class="text-grey"><?php echo htmlspecialchars($shop['address']); ?></i></p>
                    
                    <div class="mb-3 text-light-grey small">
                        <div class="d-flex justify-content-between border-bottom border-secondary pb-1 mb-1">
                            <span>Hours:</span> <span class="text-white"><?php echo htmlspecialchars($shop['open_time']); ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Phone:</span> <span class="text-white"><?php echo htmlspecialchars($shop['phone']); ?></span>
                        </div>
                    </div>
                    
                    <a href="shop_profile.php?id=<?php echo $shop['shopid']; ?>" class="btn btn-outline-light w-100 btn-sm">View Shop</a>
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
