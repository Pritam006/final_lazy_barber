<?php
session_start();
require_once 'config/database.php';

if (!isset($_GET['id'])) {
    header("Location: shops.php");
    exit;
}

$shopid = $_GET['id'];

// Fetch Shop Info
$stmt = $pdo->prepare("SELECT * FROM SHOPS WHERE shopid = ?");
$stmt->execute([$shopid]);
$shop = $stmt->fetch();

if (!$shop) {
    header("Location: shops.php");
    exit;
}

// Fetch Shop Services
$srvStmt = $pdo->prepare("SELECT * FROM SERVICES WHERE shopid = ?");
$srvStmt->execute([$shopid]);
$services = $srvStmt->fetchAll();

// Fetch Barbers for this Shop
$barberStmt = $pdo->prepare("SELECT userid, name FROM USERS WHERE shopid = ? AND role = 'barber'");
$barberStmt->execute([$shopid]);
$barbers = $barberStmt->fetchAll();

include 'includes/header.php';
?>

<!-- Shop Header -->
<div class="bg-dark pt-5 pb-4 mt-5 border-bottom border-secondary position-relative overflow-hidden">
    <div class="container position-relative" style="z-index: 2;">
        <div class="d-flex align-items-center mb-3">
            <div class="bg-black rounded-circle me-4 border border-secondary" style="width: 100px; height: 100px; display: flex; align-items: center; justify-content: center;">
                <span class="text-white fs-1">&#128136;</span>
            </div>
            <div>
                <h2 class="text-white fw-bold mb-1"><?php echo htmlspecialchars($shop['name']); ?></h2>
                <p class="text-light-grey mb-1"><i class="text-grey"><?php echo htmlspecialchars($shop['address']); ?></i></p>
                <p class="text-light-grey small mb-0">Hours: <?php echo htmlspecialchars($shop['open_time']); ?> | Phone: <?php echo htmlspecialchars($shop['phone']); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <?php if(isset($_SESSION['smart_route_msg'])): ?>
        <div class="alert bg-dark text-white border-secondary mb-4 alert-dismissible fade show">
            <?php 
                echo $_SESSION['smart_route_msg']; 
                unset($_SESSION['smart_route_msg']);
            ?>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Services List -->
        <div class="col-md-8 mb-4">
            <h4 class="text-white fw-bold mb-4">Services</h4>
            <div class="row g-3">
                <?php foreach($services as $service): ?>
                <div class="col-12">
                    <div class="glass-card p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white fw-bold mb-1"><?php echo htmlspecialchars($service['name']); ?></h6>
                            <small class="text-light-grey"><?php echo $service['duration_minutes']; ?> Mins</small>
                        </div>
                        <div class="text-end">
                            <h5 class="text-white fw-bold mb-2">$<?php echo number_format($service['price_aud'], 2); ?></h5>
                            <button class="btn btn-primary-custom btn-sm px-4 rounded-pill" onclick="startBooking(<?php echo $service['serviceid']; ?>, '<?php echo htmlspecialchars(addslashes($service['name'])); ?>', <?php echo $service['price_aud']; ?>)">Book Now</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Info Sidebar -->
        <div class="col-md-4">
            <div class="glass-card p-4 sticky-top" style="top: 100px;">
                <h5 class="text-white fw-bold mb-4">Our Barbers</h5>
                <ul class="list-unstyled mb-0">
                    <?php foreach($barbers as $barber): ?>
                        <li class="d-flex align-items-center mb-3">
                            <div class="bg-dark rounded-circle me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($barber['name']); ?>&background=random&color=fff" alt="Barber" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <span class="text-light-grey fw-bold"><?php echo htmlspecialchars($barber['name']); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Booking Wizard Modal -->
<div class="modal fade" id="bookingWizard" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-black border border-secondary" style="border-radius: 20px;">
      <div class="modal-header border-bottom-0 pb-0">
        <h5 class="modal-title text-white fw-bold">Book Appointment</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <!-- Step 1: Select Barber -->
        <div id="wizard-step-1">
            <h6 class="text-white mb-3">Select a Barber</h6>
            <div class="row g-2 mb-4">
                <?php foreach($barbers as $barber): ?>
                <div class="col-12">
                    <div class="glass-card p-3 d-flex align-items-center border-secondary barber-card" onclick="selectBarber(<?php echo $barber['userid']; ?>, '<?php echo htmlspecialchars(addslashes($barber['name'])); ?>', this)" style="cursor: pointer;">
                        <div class="bg-dark rounded-circle me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($barber['name']); ?>&background=random&color=fff" alt="Barber" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div>
                            <h6 class="text-white mb-0 fw-bold"><?php echo htmlspecialchars($barber['name']); ?></h6>
                        </div>
                        <div class="ms-auto text-white barber-checkmark d-none">&#10003;</div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <button class="btn btn-primary-custom w-100 py-2" onclick="nextStep(2)">Continue to Date & Time</button>
        </div>

        <!-- Step 2: Date/Time -->
        <div id="wizard-step-2" class="d-none">
            <h6 class="text-white mb-3">Select Date & Time</h6>
            <input type="date" class="form-control bg-dark text-white border-secondary mb-3" id="bookDate" required>
            <div class="row g-2 mb-4">
                <!-- Hardcoded times for now -->
                <div class="col-4"><button class="btn btn-outline-light w-100 time-slot">09:00:00</button></div>
                <div class="col-4"><button class="btn btn-outline-light w-100 time-slot">09:30:00</button></div>
                <div class="col-4"><button class="btn btn-outline-light w-100 time-slot">10:00:00</button></div>
                <div class="col-4"><button class="btn btn-outline-light w-100 time-slot">10:30:00</button></div>
                <div class="col-4"><button class="btn btn-outline-light w-100 time-slot">11:00:00</button></div>
                <div class="col-4"><button class="btn btn-outline-light w-100 time-slot">11:30:00</button></div>
            </div>
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary" onclick="nextStep(1)">Back</button>
                <button class="btn btn-primary-custom px-4" onclick="nextStep(3)">Continue</button>
            </div>
        </div>
        
        <!-- Step 3: Confirm -->
        <div id="wizard-step-3" class="d-none">
            <h6 class="text-white mb-3">Confirm Details</h6>
            <div class="bg-dark p-3 rounded mb-4">
                <p class="text-light-grey mb-1">Barber: <span class="text-white fw-bold" id="confirm-barber"></span></p>
                <p class="text-light-grey mb-1">Service: <span class="text-white fw-bold" id="confirm-service"></span></p>
                <p class="text-light-grey mb-1">Date: <span class="text-white fw-bold" id="confirm-date"></span></p>
                <p class="text-light-grey mb-1">Time: <span class="text-white fw-bold" id="confirm-time"></span></p>
                <h5 class="text-white fw-bold mt-3 border-top border-secondary pt-2">Total: $<span id="confirm-price"></span></h5>
            </div>
            <form method="POST" action="book_process.php" id="bookingForm">
                <input type="hidden" name="shop_id" value="<?php echo $shopid; ?>">
                <input type="hidden" name="barber_id" id="input_barber">
                <input type="hidden" name="service_id" id="input_service">
                <input type="hidden" name="date" id="input_date">
                <input type="hidden" name="time" id="input_time">
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" onclick="nextStep(2)">Back</button>
                    <button type="submit" class="btn btn-primary-custom px-4">Confirm Booking</button>
                </div>
            </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
let selectedServiceId = null;
let selectedServiceName = '';
let selectedPrice = 0;

let selectedBarberId = null;
let selectedBarberName = '';
let selectedTime = null;

// Ensure user is logged in
const isLoggedIn = <?php echo isset($_SESSION['userid']) ? 'true' : 'false'; ?>;

function startBooking(serviceId, serviceName, price) {
    if (!isLoggedIn) {
        window.location.href = 'login.php?action=login&route=shop_profile.php?id=<?php echo $shopid; ?>';
        return;
    }
    selectedServiceId = serviceId;
    selectedServiceName = serviceName;
    selectedPrice = price;
    
    // Reset wizard
    selectedBarberId = null;
    selectedTime = null;
    document.querySelectorAll('.barber-card').forEach(b => {
        b.style.border = '1px solid var(--secondary)';
        b.querySelector('.barber-checkmark').classList.add('d-none');
    });
    document.querySelectorAll('.time-slot').forEach(b => {
        b.classList.remove('btn-light', 'text-dark');
        b.classList.add('btn-outline-light');
    });
    document.getElementById('bookDate').value = '';
    
    // Show step 1
    nextStep(1);
    var modal = new bootstrap.Modal(document.getElementById('bookingWizard'));
    modal.show();
}

function selectBarber(barberId, barberName, element) {
    selectedBarberId = barberId;
    selectedBarberName = barberName;
    
    // Reset all cards styling
    document.querySelectorAll('.barber-card').forEach(b => {
        b.style.border = '1px solid var(--secondary)';
        b.querySelector('.barber-checkmark').classList.add('d-none');
    });
    
    // Highlight selected
    element.style.border = '1px solid var(--white)';
    element.querySelector('.barber-checkmark').classList.remove('d-none');
}

// Time slot selection
document.querySelectorAll('.time-slot').forEach(button => {
    button.addEventListener('click', function() {
        document.querySelectorAll('.time-slot').forEach(b => {
            b.classList.remove('btn-light', 'text-dark');
            b.classList.add('btn-outline-light');
        });
        this.classList.remove('btn-outline-light');
        this.classList.add('btn-light', 'text-dark');
        selectedTime = this.innerText;
    });
});

function nextStep(step) {
    if(step === 1) {
        document.getElementById('wizard-step-1').classList.remove('d-none');
        document.getElementById('wizard-step-2').classList.add('d-none');
        document.getElementById('wizard-step-3').classList.add('d-none');
    } else if (step === 2) {
        if (!selectedBarberId) {
            alert('Please select a barber first.');
            return;
        }
        document.getElementById('wizard-step-1').classList.add('d-none');
        document.getElementById('wizard-step-2').classList.remove('d-none');
        document.getElementById('wizard-step-3').classList.add('d-none');
    } else if (step === 3) {
        let dateVal = document.getElementById('bookDate').value;
        if(!dateVal || !selectedTime) {
            alert('Please select a date and time slot.');
            return;
        }
        
        // Populate inputs
        document.getElementById('input_barber').value = selectedBarberId;
        document.getElementById('input_service').value = selectedServiceId;
        document.getElementById('input_date').value = dateVal;
        document.getElementById('input_time').value = selectedTime;
        
        // Populate display text
        document.getElementById('confirm-barber').innerText = selectedBarberName;
        document.getElementById('confirm-service').innerText = selectedServiceName;
        document.getElementById('confirm-date').innerText = dateVal;
        document.getElementById('confirm-time').innerText = selectedTime;
        document.getElementById('confirm-price').innerText = selectedPrice.toFixed(2);
        
        document.getElementById('wizard-step-1').classList.add('d-none');
        document.getElementById('wizard-step-2').classList.add('d-none');
        document.getElementById('wizard-step-3').classList.remove('d-none');
    }
}
</script>

<?php include 'includes/footer.php'; ?>
