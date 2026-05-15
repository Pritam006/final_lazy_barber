<?php
session_start();
require_once 'config/database.php';

// Auth check
if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'barber') {
    header("Location: login.php");
    exit;
}

$userid = $_SESSION['userid'];

// Fetch Barber and Shop Profile
$stmt = $pdo->prepare("
    SELECT u.name, u.email, u.phone, u.shopid, s.name as shop_name 
    FROM USERS u 
    JOIN SHOPS s ON u.shopid = s.shopid 
    WHERE u.userid = ?
");
$stmt->execute([$userid]);
$barber = $stmt->fetch();
$shopid = $barber['shopid'];

// Handle POST Requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Update Profile
    if (isset($_POST['update_profile'])) {
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $updateStmt = $pdo->prepare("UPDATE USERS SET name = ?, phone = ? WHERE userid = ?");
        $updateStmt->execute([$name, $phone, $userid]);
        $barber['name'] = $name;
        $barber['phone'] = $phone;
        $msg = "Profile updated successfully.";
    }

    // Add Service
    if (isset($_POST['add_service'])) {
        $svc_name = $_POST['service_name'];
        $duration = $_POST['duration'];
        $price = $_POST['price'];
        $addStmt = $pdo->prepare("INSERT INTO SERVICES (shopid, name, duration_minutes, price_aud) VALUES (?, ?, ?, ?)");
        $addStmt->execute([$shopid, $svc_name, $duration, $price]);
        $msg = "Service added successfully.";
    }

    // Delete Service
    if (isset($_POST['delete_service'])) {
        $svcid = $_POST['service_id'];
        $delStmt = $pdo->prepare("DELETE FROM SERVICES WHERE serviceid = ? AND shopid = ?");
        $delStmt->execute([$svcid, $shopid]);
        $msg = "Service deleted successfully.";
    }

    // Update Appointment Status
    if (isset($_POST['update_status'])) {
        $appid = $_POST['appointment_id'];
        $status = $_POST['new_status'];
        $statStmt = $pdo->prepare("UPDATE APPOINTMENTS SET status = ? WHERE appointmentid = ? AND barberid = ?");
        $statStmt->execute([$status, $appid, $userid]);
        $msg = "Appointment status updated.";
    }
}

// Fetch Today's Appointments
$todayStmt = $pdo->prepare("
    SELECT a.*, s.name as service_name, c.name as customer_name 
    FROM APPOINTMENTS a
    JOIN SERVICES s ON a.serviceid = s.serviceid
    JOIN USERS c ON a.customerid = c.userid
    WHERE a.barberid = ? AND a.appointment_date = CURDATE()
    ORDER BY a.time_slot ASC
");
$todayStmt->execute([$userid]);
$todays_appointments = $todayStmt->fetchAll();

// Fetch Services
$srvStmt = $pdo->prepare("
    SELECT s.*, (SELECT COUNT(*) FROM APPOINTMENTS a WHERE a.serviceid = s.serviceid AND a.barberid = ?) as bookings
    FROM SERVICES s 
    WHERE s.shopid = ?
");
$srvStmt->execute([$userid, $shopid]);
$services = $srvStmt->fetchAll();

// Calculate Stats
$stat_today_count = count($todays_appointments);

$revStmt = $pdo->prepare("SELECT SUM(total_price) as rev FROM APPOINTMENTS WHERE barberid = ? AND status = 'completed' AND appointment_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
$revStmt->execute([$userid]);
$week_revenue = $revStmt->fetchColumn() ?: 0.00;

$custStmt = $pdo->prepare("SELECT COUNT(DISTINCT customerid) FROM APPOINTMENTS WHERE barberid = ?");
$custStmt->execute([$userid]);
$total_customers = $custStmt->fetchColumn();

include 'includes/header.php';
?>

<div class="container py-5 mt-5">
    
    <!-- Top Statistics Row -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="glass-card p-4 text-center border-secondary h-100">
                <h6 class="text-grey mb-2">Today's Appointments</h6>
                <h2 class="text-white fw-bold mb-0"><?php echo $stat_today_count; ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-4 text-center border-secondary h-100">
                <h6 class="text-grey mb-2">Week Revenue</h6>
                <h2 class="text-white fw-bold mb-0">$<?php echo number_format($week_revenue, 2); ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-4 text-center border-secondary h-100">
                <h6 class="text-grey mb-2">Total Customers</h6>
                <h2 class="text-white fw-bold mb-0"><?php echo $total_customers; ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-4 text-center border-secondary h-100">
                <h6 class="text-grey mb-2">Average Rating</h6>
                <h2 class="text-white fw-bold mb-0">4.9 <span class="fs-5 text-warning">&#9733;</span></h2>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-md-3 mb-4">
            <div class="glass-card p-4">
                <div class="text-center mb-4">
                    <div class="bg-dark rounded-circle mx-auto mb-3" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($barber['name']); ?>&background=random&color=fff" alt="Barber" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <h5 class="text-white fw-bold mb-1"><?php echo htmlspecialchars($barber['name']); ?></h5>
                    <small class="text-grey d-block mb-2">Barber at <strong class="text-light-grey"><?php echo htmlspecialchars($barber['shop_name']); ?></strong></small>
                </div>
                
                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <button class="nav-link active bg-transparent text-start border-bottom border-secondary rounded-0 py-3" id="v-pills-schedule-tab" data-bs-toggle="pill" data-bs-target="#v-pills-schedule" type="button" role="tab" style="color: var(--white);">Today's Schedule</button>
                    <button class="nav-link bg-transparent text-start border-bottom border-secondary rounded-0 py-3" id="v-pills-availability-tab" data-bs-toggle="pill" data-bs-target="#v-pills-availability" type="button" role="tab" style="color: var(--light-grey);">Availability</button>
                    <button class="nav-link bg-transparent text-start border-bottom border-secondary rounded-0 py-3" id="v-pills-services-tab" data-bs-toggle="pill" data-bs-target="#v-pills-services" type="button" role="tab" style="color: var(--light-grey);">Services</button>
                    <button class="nav-link bg-transparent text-start border-bottom border-secondary rounded-0 py-3" id="v-pills-analytics-tab" data-bs-toggle="pill" data-bs-target="#v-pills-analytics" type="button" role="tab" style="color: var(--light-grey);">Analytics</button>
                    <button class="nav-link bg-transparent text-start rounded-0 py-3" id="v-pills-profile-tab" data-bs-toggle="pill" data-bs-target="#v-pills-profile" type="button" role="tab" style="color: var(--light-grey);">Profile Settings</button>
                </div>
            </div>
        </div>
        
        <!-- Dashboard Content -->
        <div class="col-md-9">
            <div class="glass-card p-4 p-md-5 min-vh-50">
                <?php if(isset($msg)): ?>
                    <div class="alert alert-success bg-transparent text-success border-success alert-dismissible fade show">
                        <?php echo $msg; ?>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <div class="tab-content" id="v-pills-tabContent">
                    
                    <!-- TAB 1: Today's Schedule -->
                    <div class="tab-pane fade show active" id="v-pills-schedule" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="text-white fw-bold mb-0">Today's Schedule</h4>
                            <span class="text-light-grey"><?php echo date('l, d M Y'); ?></span>
                        </div>
                        
                        <?php if (count($todays_appointments) > 0): ?>
                            <div class="row g-3">
                                <?php foreach($todays_appointments as $app): ?>
                                <div class="col-12">
                                    <div class="bg-dark p-3 rounded border border-secondary d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="text-white mb-1"><?php echo $app['time_slot']; ?> - <span class="fw-bold"><?php echo htmlspecialchars($app['customer_name']); ?></span></h5>
                                            <p class="text-grey mb-0 small"><?php echo htmlspecialchars($app['service_name']); ?></p>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <form method="POST" class="me-2">
                                                <input type="hidden" name="update_status" value="1">
                                                <input type="hidden" name="appointment_id" value="<?php echo $app['appointmentid']; ?>">
                                                <select name="new_status" class="form-select form-select-sm bg-black text-white border-secondary" onchange="this.form.submit()">
                                                    <option value="pending" <?php if($app['status']=='pending') echo 'selected'; ?>>Pending</option>
                                                    <option value="confirmed" <?php if($app['status']=='confirmed') echo 'selected'; ?>>Confirmed</option>
                                                    <option value="completed" <?php if($app['status']=='completed') echo 'selected'; ?>>Completed</option>
                                                    <option value="cancelled" <?php if($app['status']=='cancelled') echo 'selected'; ?>>Cancelled</option>
                                                    <option value="no_show" <?php if($app['status']=='no_show') echo 'selected'; ?>>No Show</option>
                                                </select>
                                            </form>
                                            <button class="btn btn-outline-light btn-sm">View</button>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <p class="text-grey mb-0">You have no appointments scheduled for today.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- TAB 2: Availability -->
                    <div class="tab-pane fade" id="v-pills-availability" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="text-white fw-bold mb-0">Weekly Availability</h4>
                            <button class="btn btn-outline-light btn-sm"><i class="bi bi-pencil"></i> Edit Hours</button>
                        </div>
                        
                        <div class="row g-3">
                            <?php 
                            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                            foreach($days as $day): 
                            ?>
                            <div class="col-12">
                                <div class="bg-dark p-3 rounded border border-secondary d-flex justify-content-between align-items-center">
                                    <h6 class="text-white mb-0" style="width: 100px;"><?php echo $day; ?></h6>
                                    <div class="form-check form-switch ms-3">
                                        <input class="form-check-input" type="checkbox" role="switch" checked>
                                        <label class="form-check-label text-light-grey small">Open (09:00 - 17:00)</label>
                                    </div>
                                    <button class="btn btn-link text-light-grey p-0"><i class="bi bi-pencil-square"></i> Edit</button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- TAB 3: Services -->
                    <div class="tab-pane fade" id="v-pills-services" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="text-white fw-bold mb-0">Services & Pricing</h4>
                            <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#addServiceModal">+ Add Service</button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-dark table-hover bg-transparent align-middle">
                                <thead>
                                    <tr class="text-grey">
                                        <th>Service Name</th>
                                        <th>Duration</th>
                                        <th>Price</th>
                                        <th>Bookings</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="border-top-0">
                                    <?php foreach($services as $svc): ?>
                                    <tr>
                                        <td class="text-white"><?php echo htmlspecialchars($svc['name']); ?></td>
                                        <td class="text-light-grey"><?php echo $svc['duration_minutes']; ?> mins</td>
                                        <td class="text-white fw-bold">$<?php echo number_format($svc['price_aud'], 2); ?></td>
                                        <td class="text-light-grey"><?php echo $svc['bookings']; ?></td>
                                        <td>
                                            <div class="d-flex">
                                                <button class="btn btn-sm btn-outline-secondary me-2">Edit</button>
                                                <form method="POST" onsubmit="return confirm('Are you sure you want to delete this service?');">
                                                    <input type="hidden" name="delete_service" value="1">
                                                    <input type="hidden" name="service_id" value="<?php echo $svc['serviceid']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB 4: Analytics -->
                    <div class="tab-pane fade" id="v-pills-analytics" role="tabpanel">
                        <h4 class="text-white fw-bold mb-4">Performance Analytics</h4>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="bg-dark p-4 rounded border border-secondary h-100">
                                    <h6 class="text-grey mb-3">Weekly Revenue</h6>
                                    <div class="d-flex align-items-end" style="height: 100px;">
                                        <!-- Mock Chart Bars -->
                                        <div class="bg-white w-100 mx-1" style="height: 40%;"></div>
                                        <div class="bg-white w-100 mx-1" style="height: 60%;"></div>
                                        <div class="bg-white w-100 mx-1" style="height: 50%;"></div>
                                        <div class="bg-white w-100 mx-1" style="height: 80%;"></div>
                                        <div class="bg-white w-100 mx-1" style="height: 100%;"></div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-2 text-grey small">
                                        <span>Mon</span><span>Wed</span><span>Fri</span><span>Sun</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-dark p-4 rounded border border-secondary h-100">
                                    <h6 class="text-grey mb-3">Popular Services</h6>
                                    <ul class="list-unstyled mb-0">
                                        <?php 
                                        $sorted = $services;
                                        usort($sorted, function($a, $b) { return $b['bookings'] <=> $a['bookings']; });
                                        $top3 = array_slice($sorted, 0, 3);
                                        foreach($top3 as $index => $t):
                                        ?>
                                        <li class="d-flex justify-content-between mb-2">
                                            <span class="text-white"><?php echo htmlspecialchars($t['name']); ?></span>
                                            <span class="badge bg-secondary rounded-pill"><?php echo $t['bookings']; ?></span>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-dark p-4 rounded border border-secondary h-100">
                                    <h6 class="text-grey mb-2">Customer Retention</h6>
                                    <h2 class="text-white fw-bold mb-0">68%</h2>
                                    <p class="text-success small mb-0">+5% from last month</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-dark p-4 rounded border border-secondary h-100">
                                    <h6 class="text-grey mb-2">Reviews Summary</h6>
                                    <h2 class="text-white fw-bold mb-0">4.9 / 5</h2>
                                    <p class="text-light-grey small mb-0">Based on 124 reviews</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 5: Profile Settings -->
                    <div class="tab-pane fade" id="v-pills-profile" role="tabpanel">
                        <h4 class="text-white fw-bold mb-4">Profile Settings</h4>
                        <form method="POST">
                            <input type="hidden" name="update_profile" value="1">
                            <div class="mb-3">
                                <label class="form-label text-light-grey">Full Name</label>
                                <input type="text" name="name" class="form-control bg-dark text-white border-secondary" value="<?php echo htmlspecialchars($barber['name']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-light-grey">Email Address</label>
                                <input type="email" class="form-control bg-dark text-grey border-secondary" value="<?php echo htmlspecialchars($barber['email']); ?>" disabled>
                            </div>
                            <div class="mb-4">
                                <label class="form-label text-light-grey">Phone Number</label>
                                <input type="text" name="phone" class="form-control bg-dark text-white border-secondary" value="<?php echo htmlspecialchars($barber['phone']); ?>" required>
                            </div>
                            <button type="submit" class="btn btn-primary-custom px-4 py-2">Save Changes</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Service Modal -->
<div class="modal fade" id="addServiceModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-black border border-secondary" style="border-radius: 20px;">
      <div class="modal-header border-bottom-secondary">
        <h5 class="modal-title text-white fw-bold">Add New Service</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
          <div class="modal-body p-4">
              <input type="hidden" name="add_service" value="1">
              <div class="mb-3">
                  <label class="form-label text-light-grey">Service Name</label>
                  <input type="text" name="service_name" class="form-control bg-dark text-white border-secondary" required>
              </div>
              <div class="mb-3">
                  <label class="form-label text-light-grey">Duration (Minutes)</label>
                  <input type="number" name="duration" class="form-control bg-dark text-white border-secondary" required>
              </div>
              <div class="mb-3">
                  <label class="form-label text-light-grey">Price ($)</label>
                  <input type="number" step="0.01" name="price" class="form-control bg-dark text-white border-secondary" required>
              </div>
          </div>
          <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary-custom px-4">Add Service</button>
          </div>
      </form>
    </div>
  </div>
</div>

<script>
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
