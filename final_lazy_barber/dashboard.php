<?php
session_start();
require_once 'config/database.php';

// Auth check
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit;
}

$userid = $_SESSION['userid'];

// Fetch User Profile
$stmt = $pdo->prepare("SELECT name, email, phone FROM users WHERE userid = ?");
$stmt->execute([$userid]);
$user = $stmt->fetch();

// Update Profile logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    
    $updateStmt = $pdo->prepare("UPDATE users SET name = ?, phone = ? WHERE userid = ?");
    $updateStmt->execute([$name, $phone, $userid]);
    $user['name'] = $name;
    $user['phone'] = $phone;
    $msg = "Profile updated successfully.";
}

// Cancel Appointment logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancel_appointment'])) {
    $appid = $_POST['appointment_id'];
    $cancelStmt = $pdo->prepare("UPDATE appointments SET status = 'cancelled' WHERE appointmentid = ? AND customerid = ?");
    $cancelStmt->execute([$appid, $userid]);
    $msg = "Appointment cancelled successfully.";
}

// Fetch Appointments
$appStmt = $pdo->prepare("
    SELECT a.*, s.name as service_name, b.name as barber_name, sh.name as shop_name 
    FROM appointments a
    JOIN services s ON a.serviceid = s.serviceid
    JOIN users b ON a.barberid = b.userid
    JOIN SHOPS sh ON b.shopid = sh.shopid
    WHERE a.customerid = ?
    ORDER BY a.appointment_date DESC, a.time_slot DESC
");
$appStmt->execute([$userid]);
$appointments = $appStmt->fetchAll();

include 'includes/header.php';
?>

<div class="container py-5 mt-5">
    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-md-3 mb-4">
            <div class="glass-card p-4">
                <div class="text-center mb-4">
                    <div class="bg-dark rounded-circle mx-auto mb-3" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                        <span class="text-secondary fs-2">&#128100;</span>
                    </div>
                    <h5 class="text-white fw-bold mb-0"><?php echo htmlspecialchars($user['name']); ?></h5>
                    <small class="text-grey">Customer</small>
                </div>
                
                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <button class="nav-link active bg-transparent text-start border-bottom border-secondary rounded-0 py-3" id="v-pills-overview-tab" data-bs-toggle="pill" data-bs-target="#v-pills-overview" type="button" role="tab" style="color: var(--white);">Overview</button>
                    <button class="nav-link bg-transparent text-start border-bottom border-secondary rounded-0 py-3" id="v-pills-appointments-tab" data-bs-toggle="pill" data-bs-target="#v-pills-appointments" type="button" role="tab" style="color: var(--light-grey);">Appointments</button>
                    <button class="nav-link bg-transparent text-start rounded-0 py-3" id="v-pills-profile-tab" data-bs-toggle="pill" data-bs-target="#v-pills-profile" type="button" role="tab" style="color: var(--light-grey);">Profile Settings</button>
                </div>
            </div>
        </div>
        
        <!-- Dashboard Content -->
        <div class="col-md-9">
            <div class="glass-card p-4 p-md-5 min-vh-50">
                <?php if(isset($msg)): ?>
                    <div class="alert alert-success bg-transparent text-success border-success"><?php echo $msg; ?></div>
                <?php endif; ?>
                
                <div class="tab-content" id="v-pills-tabContent">
                    <!-- Overview -->
                    <div class="tab-pane fade show active" id="v-pills-overview" role="tabpanel">
                        <h4 class="text-white fw-bold mb-4">Dashboard Overview</h4>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="bg-dark p-4 rounded border border-secondary h-100">
                                    <h6 class="text-grey mb-2">Total Appointments</h6>
                                    <h2 class="text-white fw-bold mb-0"><?php echo count($appointments); ?></h2>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-dark p-4 rounded border border-secondary h-100">
                                    <h6 class="text-grey mb-2">Next Appointment</h6>
                                    <?php 
                                        $upcoming = array_filter($appointments, function($a) { return $a['status'] == 'pending' || $a['status'] == 'confirmed'; });
                                        if (count($upcoming) > 0) {
                                            $next = reset($upcoming);
                                            echo "<h4 class='text-white fw-bold mb-1'>" . htmlspecialchars($next['service_name']) . "</h4>";
                                            echo "<p class='text-light-grey mb-0'>" . $next['appointment_date'] . " at " . $next['time_slot'] . "</p>";
                                        } else {
                                            echo "<h5 class='text-white fw-bold mb-0'>No upcoming appointments</h5>";
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Appointments -->
                    <div class="tab-pane fade" id="v-pills-appointments" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="text-white fw-bold mb-0">Your Appointments</h4>
                            <a href="shops.php" class="btn btn-outline-light btn-sm">+ Add Appointment</a>
                        </div>
                        <?php if (count($appointments) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-dark table-hover bg-transparent align-middle">
                                    <thead>
                                        <tr class="text-grey">
                                            <th>Date & Time</th>
                                            <th>Service</th>
                                            <th>Barber</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-top-0">
                                        <?php foreach($appointments as $app): ?>
                                            <tr>
                                                <td class="text-white"><?php echo $app['appointment_date']; ?><br><small class="text-light-grey"><?php echo $app['time_slot']; ?></small></td>
                                                <td class="text-white"><?php echo htmlspecialchars($app['service_name']); ?></td>
                                                <td class="text-white">
                                                    <?php echo htmlspecialchars($app['shop_name']); ?><br>
                                                    <small class="text-light-grey"><?php echo htmlspecialchars($app['barber_name']); ?></small>
                                                </td>
                                                <td>
                                                    <?php 
                                                        $badgeClass = 'bg-secondary';
                                                        if($app['status'] == 'confirmed') $badgeClass = 'bg-white text-dark';
                                                        if($app['status'] == 'completed') $badgeClass = 'bg-dark border border-secondary text-white';
                                                        if($app['status'] == 'cancelled') $badgeClass = 'bg-danger';
                                                    ?>
                                                    <span class="badge <?php echo $badgeClass; ?> rounded-pill px-3"><?php echo ucfirst($app['status']); ?></span>
                                                </td>
                                                <td>
                                                    <?php if($app['status'] == 'pending' || $app['status'] == 'confirmed'): ?>
                                                        <form method="POST" action="dashboard.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to cancel this appointment?');">
                                                            <input type="hidden" name="cancel_appointment" value="1">
                                                            <input type="hidden" name="appointment_id" value="<?php echo $app['appointmentid']; ?>">
                                                            <button type="submit" class="btn btn-outline-light btn-sm">Cancel</button>
                                                        </form>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-grey">You have no booking history.</p>
                            <a href="shops.php" class="btn btn-primary-custom mt-3">Book Now</a>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Profile Settings -->
                    <div class="tab-pane fade" id="v-pills-profile" role="tabpanel">
                        <h4 class="text-white fw-bold mb-4">Profile Settings</h4>
                        <form method="POST" action="dashboard.php">
                            <input type="hidden" name="update_profile" value="1">
                            <div class="mb-3">
                                <label class="form-label text-light-grey">Full Name</label>
                                <input type="text" name="name" class="form-control bg-dark text-white border-secondary" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-light-grey">Email Address</label>
                                <input type="email" class="form-control bg-dark text-grey border-secondary" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                                <div class="form-text text-secondary">Email cannot be changed.</div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label text-light-grey">Phone Number</label>
                                <input type="text" name="phone" class="form-control bg-dark text-white border-secondary" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                            </div>
                            <button type="submit" class="btn btn-primary-custom px-4 py-2">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Simple script to toggle active styles for vertical tabs
    document.querySelectorAll('#v-pills-tab button[data-bs-toggle="pill"]').forEach((t) => {
        t.addEventListener('shown.bs.tab', (e) => {
            document.querySelectorAll('#v-pills-tab button').forEach(b => {
                b.style.color = 'var(--light-grey)';
            });
            e.target.style.color = 'var(--white)';
        });
    });
</script>

<?php include 'includes/footer.php'; ?>
