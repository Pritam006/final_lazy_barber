    </main>
    <footer class="footer mt-auto py-4 bg-black text-white-50 border-top border-dark">
        <div class="container text-center">
            <div class="row align-items-center">
                <div class="col-md-4 text-md-start mb-3 mb-md-0">
                    <a class="navbar-brand fw-bold text-white fs-4" href="index.php">
                        <span class="text-grey">Lazy</span>Barber
                    </a>
                    <p class="small mt-2 mb-0 text-secondary">Premium grooming, effortless booking.</p>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <!-- Links removed -->
                </div>
                <div class="col-md-4 text-md-end">
                    <p class="small mb-0">&copy; <?php echo date('Y'); ?> Lazy Barber. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <?php if(isset($_SESSION['toast'])): ?>
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            background: '#1a1a1a',
            color: '#fff',
            iconColor: '#28a745'
        });

        Toast.fire({
            icon: 'success',
            title: '<?php echo addslashes($_SESSION['toast']); ?>'
        });
    </script>
    <?php unset($_SESSION['toast']); endif; ?>
    
    <!-- Custom JS -->
    <script src="js/main.js"></script>
</body>
</html>
