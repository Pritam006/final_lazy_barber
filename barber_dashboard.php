<?php
session_start();
require_once 'config/database.php';

// Auth check
if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'barber') {
    header("Location: login.php");
    exit;
}


$userid = $_SESSION['userid'];

// Auto migrate shop avatar column if it doesn't exist
try { $pdo->exec("ALTER TABLE SHOPS ADD COLUMN avatar VARCHAR(255) DEFAULT NULL"); } catch(PDOException $e) {}

// Fetch Barber and Shop Profile
$stmt = $pdo->prepare("
    SELECT u.name, u.email, u.phone, u.shopid, u.avatar, u.is_shopowner, s.name as shop_name, s.avatar as shop_avatar 
    FROM USERS u 
    JOIN SHOPS s ON u.shopid = s.shopid 
    WHERE u.userid = ?
");
$stmt->execute([$userid]);
$barber = $stmt->fetch();
$shopid = $barber['shopid'];

// Check and Initialize Availability for this barber if it doesn't exist
$availCheck = $pdo->prepare("SELECT COUNT(*) FROM AVAILABILITY WHERE barberid = ?");
$availCheck->execute([$userid]);
if ($availCheck->fetchColumn() == 0) {
    $insAvail = $pdo->prepare("INSERT INTO AVAILABILITY (barberid, day_of_week, start_time, end_time, is_blocked) VALUES (?, ?, '09:00:00', '17:00:00', 0)");
    for ($i = 0; $i < 7; $i++) {
        $insAvail->execute([$userid, $i]);
    }
}

// Handle POST Requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Update Profile
    if (isset($_POST['update_profile'])) {
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        
        // Handle Avatar Upload
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'img/avatars/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            $fileName = time() . '_b_' . basename($_FILES['avatar']['name']);
            $targetFile = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetFile)) {
                $pdo->prepare("UPDATE USERS SET avatar = ? WHERE userid = ?")->execute([$fileName, $userid]);
                $barber['avatar'] = $fileName;
            }
        }
        
        // Handle Shop Avatar Upload (Owners Only)
        if ($barber['is_shopowner'] && isset($_FILES['shop_avatar']) && $_FILES['shop_avatar']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'img/avatars/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            $fileName = time() . '_s_' . basename($_FILES['shop_avatar']['name']);
            $targetFile = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['shop_avatar']['tmp_name'], $targetFile)) {
                $pdo->prepare("UPDATE SHOPS SET avatar = ? WHERE shopid = ?")->execute([$fileName, $barber['shopid']]);
                $barber['shop_avatar'] = $fileName;
            }
        }

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
    
    // Edit Service
    if (isset($_POST['edit_service'])) {
        $svcid = $_POST['service_id'];
        $svc_name = $_POST['service_name'];
        $duration = $_POST['duration'];
        $price = $_POST['price'];
        $updStmt = $pdo->prepare("UPDATE SERVICES SET name = ?, duration_minutes = ?, price_aud = ? WHERE serviceid = ? AND shopid = ?");
        $updStmt->execute([$svc_name, $duration, $price, $svcid, $shopid]);
        $msg = "Service updated successfully.";
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
        
        require_once 'classes/NotificationManager.php';
        $notifManager = new NotificationManager($pdo);
        if ($status == 'cancelled') {
            $notifManager->sendCancellation($appid, $barber['name']);
        } else if ($status != 'pending') {
            $notifManager->sendStatusUpdate($appid, $status);
        }

        $msg = "Appointment status updated.";
    }
    
    // Update Availability
    if (isset($_POST['update_availability'])) {
        $availid = $_POST['availability_id'];
        $is_blocked = isset($_POST['is_blocked']) ? 1 : 0;
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];
        
        $avStmt = $pdo->prepare("UPDATE AVAILABILITY SET is_blocked = ?, start_time = ?, end_time = ? WHERE availabilityid = ? AND barberid = ?");
        $avStmt->execute([$is_blocked, $start_time, $end_time, $availid, $userid]);
        $msg = "Availability updated.";
    }
}

// Get Schedule Month from GET, default to current month
$schedule_month = $_GET['schedule_month'] ?? date('Y-m');
$start_date = $schedule_month . '-01';
$end_date = date('Y-m-t', strtotime($start_date));

// Fetch Appointments for Selected Month
if ($barber['is_shopowner']) {
    $todayStmt = $pdo->prepare("
        SELECT a.*, s.name as service_name, c.name as customer_name, c.phone as customer_phone, b.name as appointed_barber_name
        FROM APPOINTMENTS a
        JOIN SERVICES s ON a.serviceid = s.serviceid
        JOIN USERS c ON a.customerid = c.userid
        JOIN USERS b ON a.barberid = b.userid
        WHERE b.shopid = ? AND a.appointment_date BETWEEN ? AND ?
        ORDER BY a.appointment_date ASC, a.time_slot ASC
    ");
    $todayStmt->execute([$shopid, $start_date, $end_date]);
} else {
    $todayStmt = $pdo->prepare("
        SELECT a.*, s.name as service_name, c.name as customer_name, c.phone as customer_phone, b.name as appointed_barber_name
        FROM APPOINTMENTS a
        JOIN SERVICES s ON a.serviceid = s.serviceid
        JOIN USERS c ON a.customerid = c.userid
        JOIN USERS b ON a.barberid = b.userid
        WHERE a.barberid = ? AND a.appointment_date BETWEEN ? AND ?
        ORDER BY a.appointment_date ASC, a.time_slot ASC
    ");
    $todayStmt->execute([$userid, $start_date, $end_date]);
}
$month_appointments = $todayStmt->fetchAll();

// Fetch Availability
$availFetch = $pdo->prepare("SELECT * FROM AVAILABILITY WHERE barberid = ? ORDER BY day_of_week ASC");
$availFetch->execute([$userid]);
$availability = $availFetch->fetchAll();

// Fetch Services
$srvStmt = $pdo->prepare("
    SELECT s.*, (SELECT COUNT(*) FROM APPOINTMENTS a WHERE a.serviceid = s.serviceid AND a.barberid = ?) as bookings
    FROM SERVICES s 
    WHERE s.shopid = ?
");
$srvStmt->execute([$userid, $shopid]);
$services = $srvStmt->fetchAll();

// Calculate Stats
$stat_month_count = count($month_appointments);

if ($barber['is_shopowner']) {
    $custStmt = $pdo->prepare("
        SELECT COUNT(DISTINCT a.customerid) 
        FROM APPOINTMENTS a
        JOIN USERS b ON a.barberid = b.userid
        WHERE b.shopid = ?
    ");
    $custStmt->execute([$shopid]);
} else {
    $custStmt = $pdo->prepare("
        SELECT COUNT(DISTINCT a.customerid) 
        FROM APPOINTMENTS a
        WHERE a.barberid = ?
    ");
    $custStmt->execute([$userid]);
}
$total_customers = $custStmt->fetchColumn();

include 'includes/header.php';
?>

<div class="container py-5 mt-5">
    
    <!-- Top Statistics Row -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="glass-card p-4 text-center border-secondary h-100">
                <h6 class="text-grey mb-2">Appointments This Month</h6>
                <h2 class="text-white fw-bold mb-0"><?php echo $stat_month_count; ?></h2>
            </div>
        </div>
        <div class="col-md-6">
            <div class="glass-card p-4 text-center border-secondary h-100">
                <h6 class="text-grey mb-2"><?php echo $barber['is_shopowner'] ? 'Total Shop Customers' : 'Total Customers'; ?></h6>
                <h2 class="text-white fw-bold mb-0"><?php echo $total_customers; ?></h2>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-md-3 mb-4">
            <div class="glass-card p-4">
                <div class="text-center mb-4">
                    <div class="bg-dark rounded-circle mx-auto mb-3" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        <?php if(!empty($barber['avatar'])): ?>
                            <img src="img/avatars/<?php echo htmlspecialchars($barber['avatar']); ?>" alt="Barber" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($barber['name']); ?>&background=random&color=fff" alt="Barber" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php endif; ?>
                    </div>
                    <h5 class="text-white fw-bold mb-1"><?php echo htmlspecialchars($barber['name']); ?> <?php if($barber['is_shopowner']): ?><i class="bi bi-patch-check-fill text-warning ms-1" title="Shop Owner"></i><?php endif; ?></h5>
                    <?php if($barber['is_shopowner']): ?>
                        <small class="text-warning fw-bold d-block mb-1">SHOP OWNER</small>
                    <?php else: ?>
                        <small class="text-info fw-bold d-block mb-1">BARBER</small>
                    <?php endif; ?>
                    <small class="text-grey d-block mb-2">at <strong class="text-light-grey"><?php echo htmlspecialchars($barber['shop_name']); ?></strong></small>
                </div>
                
                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <button class="nav-link active bg-transparent text-start border-bottom border-secondary rounded-0 py-3" id="v-pills-schedule-tab" data-bs-toggle="pill" data-bs-target="#v-pills-schedule" type="button" role="tab" style="color: var(--white);">Schedule</button>
                    <button class="nav-link bg-transparent text-start border-bottom border-secondary rounded-0 py-3" id="v-pills-availability-tab" data-bs-toggle="pill" data-bs-target="#v-pills-availability" type="button" role="tab" style="color: var(--light-grey);">Availability</button>
                    <button class="nav-link bg-transparent text-start border-bottom border-secondary rounded-0 py-3" id="v-pills-services-tab" data-bs-toggle="pill" data-bs-target="#v-pills-services" type="button" role="tab" style="color: var(--light-grey);">Services</button>
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
                    
                    <!-- TAB 1: Schedule -->
                    <div class="tab-pane fade show active" id="v-pills-schedule" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="text-white fw-bold mb-0">Schedule</h4>
                            
                            <form method="GET" class="d-flex">
                                <input type="month" name="schedule_month" class="form-control bg-dark text-white border-secondary me-2" value="<?php echo htmlspecialchars($schedule_month); ?>" onchange="this.form.submit()">
                                <button type="submit" class="btn btn-primary-custom btn-sm px-3">Search</button>
                            </form>
                        </div>
                        
                        <?php if (count($month_appointments) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-dark table-hover bg-transparent align-middle">
                                    <thead>
                                        <tr class="text-grey">
                                            <th>Date/Time</th>
                                            <th>Customer</th>
                                            <th>Service & Barber</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-top-0">
                                        <?php foreach($month_appointments as $app): ?>
                                            <tr>
                                                <td class="text-white">
                                                    <?php echo date('M d, Y', strtotime($app['appointment_date'])); ?><br>
                                                    <small class="text-light-grey"><?php echo date('h:i A', strtotime($app['time_slot'])); ?></small>
                                                </td>
                                                <td class="text-white"><?php echo htmlspecialchars($app['customer_name']); ?></td>
                                                <td class="text-white">
                                                    <?php echo htmlspecialchars($app['service_name']); ?><br>
                                                    <small class="text-light-grey">with <?php echo htmlspecialchars($app['appointed_barber_name']); ?></small>
                                                </td>
                                                <td>
                                                    <form method="POST">
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
                                                </td>
                                                <td>
                                                    <button class="btn btn-outline-light btn-sm" onclick="viewBooking(<?php echo htmlspecialchars(json_encode([
                                                        'id' => $app['appointmentid'],
                                                        'customer' => $app['customer_name'],
                                                        'phone' => $app['customer_phone'],
                                                        'service' => $app['service_name'],
                                                        'date' => $app['appointment_date'],
                                                        'time' => $app['time_slot'],
                                                        'price' => $app['total_price'],
                                                        'status' => $app['status']
                                                    ])); ?>)">View</button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5 bg-dark rounded border border-secondary">
                                <p class="text-grey mb-0">No appointments found for this month.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- TAB 2: Availability -->
                    <div class="tab-pane fade" id="v-pills-availability" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="text-white fw-bold mb-0">Weekly Availability</h4>
                        </div>
                        
                        <div class="row g-3">
                            <?php 
                            $dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                            foreach($availability as $av): 
                            ?>
                            <div class="col-12">
                                <div class="bg-dark p-3 rounded border border-secondary d-flex justify-content-between align-items-center">
                                    <h6 class="text-white mb-0" style="width: 100px;"><?php echo $dayNames[$av['day_of_week']]; ?></h6>
                                    <div class="ms-3 text-light-grey small">
                                        <?php if($av['is_blocked']): ?>
                                            <span class="text-danger"><i class="bi bi-x-circle"></i> Closed</span>
                                        <?php else: ?>
                                            <span class="text-success"><i class="bi bi-check-circle"></i> Open</span> 
                                            (<?php echo substr($av['start_time'], 0, 5); ?> - <?php echo substr($av['end_time'], 0, 5); ?>)
                                        <?php endif; ?>
                                    </div>
                                    <button class="btn btn-link text-light-grey p-0" onclick="editAvailability(<?php echo $av['availabilityid']; ?>, '<?php echo $dayNames[$av['day_of_week']]; ?>', <?php echo $av['is_blocked']; ?>, '<?php echo $av['start_time']; ?>', '<?php echo $av['end_time']; ?>')"><i class="bi bi-pencil-square"></i> Edit</button>
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
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="border-top-0">
                                    <?php foreach($services as $svc): ?>
                                    <tr>
                                        <td class="text-white"><?php echo htmlspecialchars($svc['name']); ?></td>
                                        <td class="text-light-grey"><?php echo $svc['duration_minutes']; ?> mins</td>
                                        <td class="text-white fw-bold">$<?php echo number_format($svc['price_aud'], 2); ?></td>
                                        <td>
                                            <div class="d-flex">
                                                <button class="btn btn-sm btn-outline-secondary me-2" onclick="editService(<?php echo $svc['serviceid']; ?>, '<?php echo htmlspecialchars(addslashes($svc['name'])); ?>', <?php echo $svc['duration_minutes']; ?>, <?php echo $svc['price_aud']; ?>)">Edit</button>
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


                    <!-- TAB 5: Profile Settings -->
                    <div class="tab-pane fade" id="v-pills-profile" role="tabpanel">
                        <h4 class="text-white fw-bold mb-4">Profile Settings</h4>
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="update_profile" value="1">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-light-grey">Your Profile Picture</label>
                                    <input type="file" name="avatar" class="form-control bg-dark text-white border-secondary" accept="image/*">
                                </div>
                                <?php if($barber['is_shopowner']): ?>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-light-grey">Shop Profile Picture</label>
                                    <input type="file" name="shop_avatar" class="form-control bg-dark text-white border-secondary" accept="image/*">
                                </div>
                                <?php endif; ?>
                            </div>
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

<!-- View Booking Modal -->
<div class="modal fade" id="viewBookingModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-black border border-secondary" style="border-radius: 20px;">
      <div class="modal-header border-bottom-secondary">
        <h5 class="modal-title text-white fw-bold">Booking Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4 text-light-grey">
          <p><strong>Customer:</strong> <span id="v_customer" class="text-white"></span></p>
          <p><strong>Phone:</strong> <span id="v_phone" class="text-white"></span></p>
          <p><strong>Service:</strong> <span id="v_service" class="text-white"></span></p>
          <p><strong>Date:</strong> <span id="v_date" class="text-white"></span></p>
          <p><strong>Time:</strong> <span id="v_time" class="text-white"></span></p>
          <p><strong>Price:</strong> $<span id="v_price" class="text-white"></span></p>
          <p class="mb-0"><strong>Status:</strong> <span id="v_status" class="text-white text-uppercase"></span></p>
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

<!-- Edit Service Modal -->
<div class="modal fade" id="editServiceModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-black border border-secondary" style="border-radius: 20px;">
      <div class="modal-header border-bottom-secondary">
        <h5 class="modal-title text-white fw-bold">Edit Service</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
          <div class="modal-body p-4">
              <input type="hidden" name="edit_service" value="1">
              <input type="hidden" name="service_id" id="e_service_id">
              <div class="mb-3">
                  <label class="form-label text-light-grey">Service Name</label>
                  <input type="text" name="service_name" id="e_service_name" class="form-control bg-dark text-white border-secondary" required>
              </div>
              <div class="mb-3">
                  <label class="form-label text-light-grey">Duration (Minutes)</label>
                  <input type="number" name="duration" id="e_duration" class="form-control bg-dark text-white border-secondary" required>
              </div>
              <div class="mb-3">
                  <label class="form-label text-light-grey">Price ($)</label>
                  <input type="number" step="0.01" name="price" id="e_price" class="form-control bg-dark text-white border-secondary" required>
              </div>
          </div>
          <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary-custom px-4">Save Changes</button>
          </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Availability Modal -->
<div class="modal fade" id="editAvailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-black border border-secondary" style="border-radius: 20px;">
      <div class="modal-header border-bottom-secondary">
        <h5 class="modal-title text-white fw-bold">Edit Availability: <span id="av_day_name"></span></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
          <div class="modal-body p-4">
              <input type="hidden" name="update_availability" value="1">
              <input type="hidden" name="availability_id" id="av_id">
              
              <div class="form-check form-switch mb-4">
                  <input class="form-check-input" type="checkbox" role="switch" name="is_blocked" id="av_blocked" value="0">
                  <label class="form-check-label text-light-grey" id="av_blocked_label">Closed</label>
              </div>
              
              <div class="row g-3">
                  <div class="col-6">
                      <label class="form-label text-light-grey">Start Time</label>
                      <input type="time" name="start_time" id="av_start" class="form-control bg-dark text-white border-secondary" required>
                  </div>
                  <div class="col-6">
                      <label class="form-label text-light-grey">End Time</label>
                      <input type="time" name="end_time" id="av_end" class="form-control bg-dark text-white border-secondary" required>
                  </div>
              </div>
          </div>
          <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary-custom px-4">Save Hours</button>
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

    function viewBooking(booking) {
        document.getElementById('v_customer').innerText = booking.customer;
        document.getElementById('v_phone').innerText = booking.phone || 'N/A';
        document.getElementById('v_service').innerText = booking.service;
        document.getElementById('v_date').innerText = booking.date;
        document.getElementById('v_time').innerText = booking.time;
        document.getElementById('v_price').innerText = booking.price;
        document.getElementById('v_status').innerText = booking.status;
        
        var modal = new bootstrap.Modal(document.getElementById('viewBookingModal'));
        modal.show();
    }
    
    function editService(id, name, duration, price) {
        document.getElementById('e_service_id').value = id;
        document.getElementById('e_service_name').value = name;
        document.getElementById('e_duration').value = duration;
        document.getElementById('e_price').value = price;
        
        var modal = new bootstrap.Modal(document.getElementById('editServiceModal'));
        modal.show();
    }
    
    function editAvailability(id, day, isBlocked, start, end) {
        document.getElementById('av_id').value = id;
        document.getElementById('av_day_name').innerText = day;
        document.getElementById('av_start').value = start;
        document.getElementById('av_end').value = end;
        
        let blockedCheckbox = document.getElementById('av_blocked');
        // If isBlocked is 1 in DB, it means CLOSED. 
        // We want the switch to be ON for OPEN, OFF for CLOSED.
        // Wait, the POST logic handles is_blocked based on if it's set.
        // Let's set it so the checkbox means "Closed".
        blockedCheckbox.checked = isBlocked === 1;
        
        var modal = new bootstrap.Modal(document.getElementById('editAvailModal'));
        modal.show();
    }


</script>

<?php include 'includes/footer.php'; ?>
