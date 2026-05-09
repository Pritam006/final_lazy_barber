<?php
session_start();
include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container text-center">
        <h1 class="hero-title text-white mb-4">Master Your <span class="text-grey">Style</span></h1>
        <p class="lead text-light-grey mb-5 mx-auto" style="max-width: 600px;">
            Experience premium grooming with our seamless booking platform. Find top-rated barbers and secure your spot in seconds.
        </p>
        <a href="shops.php" class="btn btn-primary-custom btn-lg px-5 py-3 rounded-pill">
            Book an Appointment
        </a>
    </div>
</section>

<!-- Reviews Section -->
<section class="py-5 bg-dark-grey">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold text-white">What Our Customers Say</h2>
            <p class="text-grey">Real reviews from our community.</p>
        </div>
        
        <div class="row g-4">
            <!-- Review 1 -->
            <div class="col-md-4">
                <div class="glass-card p-4 h-100 review-card">
                    <div class="d-flex justify-content-between mb-3">
                        <h5 class="text-white mb-0">James W.</h5>
                        <div class="star-rating">
                            &#9733;&#9733;&#9733;&#9733;&#9733;
                        </div>
                    </div>
                    <p class="text-light-grey mb-0">"The easiest way to book a haircut. No more waiting in lines, the platform is slick and the barbers are top-tier."</p>
                </div>
            </div>
            <!-- Review 2 -->
            <div class="col-md-4">
                <div class="glass-card p-4 h-100 review-card">
                    <div class="d-flex justify-content-between mb-3">
                        <h5 class="text-white mb-0">Michael T.</h5>
                        <div class="star-rating">
                            &#9733;&#9733;&#9733;&#9733;&#9733;
                        </div>
                    </div>
                    <p class="text-light-grey mb-0">"I love the black and white aesthetic of this app. It's fast, the smart routing works perfectly, and my barber was excellent."</p>
                </div>
            </div>
            <!-- Review 3 -->
            <div class="col-md-4">
                <div class="glass-card p-4 h-100 review-card">
                    <div class="d-flex justify-content-between mb-3">
                        <h5 class="text-white mb-0">David L.</h5>
                        <div class="star-rating">
                            &#9733;&#9733;&#9733;&#9733;&#9734;
                        </div>
                    </div>
                    <p class="text-light-grey mb-0">"Great experience overall. Found a barber right next to my office and booked a slot in 2 minutes. Highly recommend."</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
include 'includes/footer.php';
?>
