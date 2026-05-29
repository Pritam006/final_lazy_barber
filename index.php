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

<!-- Chatbot Widget -->
<div id="chatWidget" class="chat-widget position-fixed bottom-0 end-0 m-3 m-md-4" style="z-index: 1050; display: none; width: calc(100vw - 2rem); max-width: 350px;">
    <div class="glass-card d-flex flex-column" style="height: 500px; max-height: calc(100vh - 6rem); border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
        <!-- Header -->
        <div class="bg-black p-3 border-bottom border-secondary d-flex justify-content-between align-items-center">
            <h5 class="text-white mb-0 fw-bold fs-6 d-flex align-items-center">
                <img src="img/ai_avatar.png" alt="AI" class="rounded-circle me-2" style="width: 30px; height: 30px; object-fit: cover; border: 1px solid #444;">
                Lazy Barber AI
            </h5>
            <button class="btn-close btn-close-white" onclick="toggleChat()"></button>
        </div>
        <!-- Messages -->
        <div id="chatMessages" class="p-3 flex-grow-1 bg-dark" style="overflow-y: auto; scrollbar-width: none;">
            <div class="mb-3 text-start">
                <span class="bg-black text-white px-3 py-2 rounded-3 d-inline-block border border-secondary shadow-sm" style="max-width: 85%; font-size: 0.95rem;">
                    Hello! I'm the Lazy Barber AI assistant. How can I help you find a fresh cut today?
                </span>
            </div>
        </div>
        <!-- Input -->
        <div class="p-3 bg-black border-top border-secondary">
            <form id="chatForm" onsubmit="sendMessage(event)" class="d-flex">
                <input type="text" id="chatInput" class="form-control bg-dark text-white border-secondary me-2" placeholder="Type a message..." required autocomplete="off" style="border-radius: 10px;">
                <button type="submit" class="btn btn-light rounded-circle px-3 flex-shrink-0"><i class="bi bi-send-fill text-black"></i></button>
            </form>
        </div>
    </div>
</div>

<!-- Floating Bubble -->
<button id="chatBubbleBtn" class="btn btn-black rounded-circle position-fixed bottom-0 end-0 m-3 m-md-4 shadow-lg d-flex align-items-center justify-content-center p-0" style="width: 65px; height: 65px; z-index: 1040; transition: transform 0.2s; border: 2px solid #555; overflow: hidden;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'" onclick="toggleChat()">
    <img src="img/ai_avatar.png" alt="AI Chat" style="width: 100%; height: 100%; object-fit: cover;">
</button>

<script>
    function toggleChat() {
        var widget = document.getElementById('chatWidget');
        var bubble = document.getElementById('chatBubbleBtn');
        if (widget.style.display === 'none') {
            widget.style.display = 'block';
            bubble.style.display = 'none';
        } else {
            widget.style.display = 'none';
            bubble.style.display = 'flex';
        }
    }

    async function sendMessage(e) {
        e.preventDefault();
        var input = document.getElementById('chatInput');
        var message = input.value.trim();
        if (!message) return;

        // Add user message to UI
        appendMessage(message, 'user');
        input.value = '';

        // Add loading indicator
        var loadingId = appendMessage('', 'ai', true);

        try {
            const response = await fetch('api/chat.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: message })
            });
            const data = await response.json();
            
            // Remove loading indicator
            document.getElementById(loadingId).remove();
            
            if (data.reply) {
                appendMessage(data.reply, 'ai');
            } else {
                appendMessage('Oops, I ran into an error. Did you add the API Key?', 'ai');
            }
        } catch (error) {
            document.getElementById(loadingId).remove();
            appendMessage('Connection error.', 'ai');
        }
    }

    function appendMessage(text, sender, isLoading = false) {
        var msgContainer = document.getElementById('chatMessages');
        var div = document.createElement('div');
        div.className = 'mb-3 ' + (sender === 'user' ? 'text-end' : 'text-start');
        
        var span = document.createElement('span');
        span.className = sender === 'user' 
            ? 'bg-light text-black px-3 py-2 rounded-3 d-inline-block shadow-sm'
            : 'bg-black text-white px-3 py-2 rounded-3 d-inline-block border border-secondary shadow-sm';
        span.style.maxWidth = '85%';
        span.style.fontSize = '0.95rem';
        span.style.wordBreak = 'break-word';
        
        if (isLoading) {
            span.innerHTML = '<span class="spinner-grow spinner-grow-sm text-grey" role="status" style="width: 10px; height: 10px;"></span><span class="spinner-grow spinner-grow-sm text-grey mx-1" role="status" style="width: 10px; height: 10px;"></span><span class="spinner-grow spinner-grow-sm text-grey" role="status" style="width: 10px; height: 10px;"></span>';
            var id = 'loading_' + Date.now();
            div.id = id;
        } else {
            span.innerText = text;
        }
        
        div.appendChild(span);
        msgContainer.appendChild(div);
        msgContainer.scrollTop = msgContainer.scrollHeight;
        
        return isLoading ? div.id : null;
    }
</script>

<?php
include 'includes/footer.php';
?>
