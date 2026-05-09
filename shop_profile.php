<?php
session_start();
require_once 'config/database.php';

$shop_id = $_GET['id'] ?? null;
if (!$shop_id) {
    header("Location: shops.php");
    exit;
}

// Fetch shop details
$stmt = $pdo->prepare("SELECT * FROM users WHERE userid = ? AND role = 'barber'");
$stmt->execute([$shop_id]);
$shop = $stmt->fetch();

if (!$shop) {
    header("Location: shops.php");
    exit;
}

// Fetch services
$stmt = $pdo->query("SELECT * FROM services WHERE is_active = 1");
$services = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container py-5 mt-5">
    <div class="row">
        <!-- Shop Profile Info -->
        <div class="col-md-4 mb-4">
            <div class="glass-card p-4 text-center">
                <div class="bg-dark rounded-circle mx-auto mb-3" style="width: 120px; height: 120px; display: flex; align-items: center; justify-content: center;">
                    <span class="text-secondary fs-1">&#128136;</span>
                </div>
                <h3 class="text-white fw-bold"><?php echo htmlspecialchars($shop['name']); ?></h3>
                <p class="text-grey mb-4">Premium Barber Shop</p>
                <div class="d-flex justify-content-center mb-3">
                    <span class="star-rating fs-5">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                    <span class="text-light-grey ms-2 mt-1">(42 Reviews)</span>
                </div>
            </div>
        </div>

        <!-- Services & Booking -->
        <div class="col-md-8">
            <div class="glass-card p-4">
                <h4 class="text-white fw-bold border-bottom border-secondary pb-3 mb-4">Our Services</h4>
                
                <div class="list-group list-group-flush bg-transparent">
                    <?php foreach($services as $service): ?>
                    <div class="list-group-item bg-transparent border-secondary px-0 py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white mb-1 fw-bold"><?php echo htmlspecialchars($service['name']); ?></h6>
                            <small class="text-light-grey"><?php echo $service['duration_minutes']; ?> mins &bull; <?php echo htmlspecialchars($service['description']); ?></small>
                        </div>
                        <div class="text-end">
                            <span class="text-white fw-bold d-block mb-2">$<?php echo number_format($service['price_aud'], 2); ?></span>
                            <!-- Trigger Booking Modal -->
                            <button class="btn btn-outline-light btn-sm rounded-pill px-3" onclick="openBookingWizard(<?php echo $service['serviceid']; ?>)">Book Now</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Booking Wizard Modal (Sliding/Multi-step) -->
<div class="modal fade" id="bookingWizard" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content glass-card border-secondary">
      <div class="modal-header border-secondary">
        <h5 class="modal-title text-white fw-bold">Book Appointment</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <!-- Step 1: Date/Time -->
        <div id="wizard-step-1">
            <h6 class="text-white mb-3">Select Date & Time</h6>
            <input type="date" class="form-control bg-dark text-white border-secondary mb-3" id="bookDate" required>
            <div class="row g-2 mb-4">
                <div class="col-4"><button class="btn btn-outline-light w-100 time-slot">09:00</button></div>
                <div class="col-4"><button class="btn btn-outline-light w-100 time-slot">09:30</button></div>
                <div class="col-4"><button class="btn btn-outline-light w-100 time-slot">10:00</button></div>
                <!-- Mock slots for demo -->
            </div>
            <button class="btn btn-primary-custom w-100 py-2" onclick="nextStep(2)">Continue</button>
        </div>
        
        <!-- Step 2: Confirm -->
        <div id="wizard-step-2" class="d-none">
            <h6 class="text-white mb-3">Confirm Details</h6>
            <div class="bg-dark p-3 rounded mb-4">
                <p class="text-light-grey mb-1">Service: <span class="text-white fw-bold" id="confirm-service"></span></p>
                <p class="text-light-grey mb-1">Date: <span class="text-white fw-bold" id="confirm-date"></span></p>
                <p class="text-light-grey mb-0">Time: <span class="text-white fw-bold" id="confirm-time"></span></p>
            </div>
            <form method="POST" action="book_process.php" id="bookingForm">
                <input type="hidden" name="shop_id" value="<?php echo $shop_id; ?>">
                <input type="hidden" name="service_id" id="input_service_id">
                <input type="hidden" name="date" id="input_date">
                <input type="hidden" name="time" id="input_time">
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" onclick="nextStep(1)">Back</button>
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
let selectedTime = null;

function openBookingWizard(serviceId) {
    // Smart Routing check via PHP session directly injected into JS logic
    <?php if(!isset($_SESSION['userid'])): ?>
        // Redirect to login but save this page to return to
        window.location.href = 'login.php?msg=Please sign in to secure your appointment';
        return;
    <?php endif; ?>

    selectedServiceId = serviceId;
    document.getElementById('input_service_id').value = serviceId;
    document.getElementById('confirm-service').innerText = "Service #" + serviceId; // Simplified
    
    // Reset wizard
    nextStep(1);
    
    // Show modal
    var myModal = new bootstrap.Modal(document.getElementById('bookingWizard'));
    myModal.show();
}

function nextStep(step) {
    if(step === 1) {
        document.getElementById('wizard-step-1').classList.remove('d-none');
        document.getElementById('wizard-step-2').classList.add('d-none');
    } else if (step === 2) {
        let dateVal = document.getElementById('bookDate').value;
        if(!dateVal || !selectedTime) {
            alert('Please select a date and time slot.');
            return;
        }
        document.getElementById('input_date').value = dateVal;
        document.getElementById('confirm-date').innerText = dateVal;
        
        document.getElementById('input_time').value = selectedTime;
        document.getElementById('confirm-time').innerText = selectedTime;
        
        document.getElementById('wizard-step-1').classList.add('d-none');
        document.getElementById('wizard-step-2').classList.remove('d-none');
    }
}

// Time slot selection logic
document.querySelectorAll('.time-slot').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.time-slot').forEach(b => {
            b.classList.remove('btn-light', 'text-dark');
            b.classList.add('btn-outline-light');
        });
        this.classList.remove('btn-outline-light');
        this.classList.add('btn-light', 'text-dark');
        selectedTime = this.innerText;
    });
});
</script>

<?php include 'includes/footer.php'; ?>
