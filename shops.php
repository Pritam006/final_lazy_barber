<?php
session_start();
require_once 'config/database.php';
include 'includes/header.php';
// Handle search
$searchLoc = $_GET['location'] ?? '';
$searchDate = $_GET['date'] ?? '';
$searchTime = $_GET['time'] ?? '';

// Fetch shops
$sql = "SELECT shopid, name, address, suburb, open_time, phone FROM SHOPS WHERE is_active = 1";
$params = [];

if (!empty($searchLoc)) {
    $sql .= " AND (name LIKE ? OR suburb LIKE ? OR address LIKE ?)";
    $params[] = "%$searchLoc%";
    $params[] = "%$searchLoc%";
    $params[] = "%$searchLoc%";
}

if (!empty($searchDate) && !empty($searchTime)) {
    // Filter shops that have at least one barber available at the given date and time
    $sql .= " AND shopid IN (
        SELECT shopid FROM USERS 
        WHERE role = 'barber' AND is_active = 1 
        AND userid NOT IN (
            SELECT barberid FROM appointments 
            WHERE appointment_date = ? AND time_slot = ? AND status != 'cancelled'
        )
    )";
    // Ensure time has seconds for exact match in DB if needed, though HTML time input usually omits seconds if 00
    $timeParam = (strlen($searchTime) == 5) ? $searchTime . ':00' : $searchTime;
    $params[] = $searchDate;
    $params[] = $timeParam;
}

if (empty($searchLoc) && empty($searchDate) && empty($searchTime)) {
    $sql .= " LIMIT 5";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$shops = $stmt->fetchAll();

$isSearch = (!empty($searchLoc) || !empty($searchDate) || !empty($searchTime));

?>

<div class="container py-5 mt-5">
    <!-- Search / Filter Bar -->
    <div class="glass-card p-4 mb-5 sticky-top mt-3" style="z-index: 1020; top: 70px;">
        <form method="GET" action="shops.php" class="row g-3 align-items-center">
            <div class="col-md-4">
                <input type="text" name="location" class="form-control bg-dark text-white border-secondary" placeholder="Location or Shop Name" value="<?php echo htmlspecialchars($searchLoc); ?>">
            </div>
            <div class="col-md-3">
                <input type="date" name="date" class="form-control bg-dark text-white border-secondary" value="<?php echo htmlspecialchars($searchDate); ?>">
            </div>
            <div class="col-md-3">
                <input type="time" name="time" class="form-control bg-dark text-white border-secondary" value="<?php echo htmlspecialchars($searchTime); ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary-custom w-100">Search</button>
            </div>
        </form>
    </div>

    <!-- Recommendations -->
    <div class="mb-4">
        <h2 class="fw-bold text-white"><?php echo $isSearch ? "Search Results" : "Recommended For You"; ?></h2>
        <?php if(!$isSearch): ?>
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
