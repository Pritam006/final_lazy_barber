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
                    <?php if(isset($_SESSION['userid'])): ?>
                        <a href="dashboard.php" class="text-white text-decoration-none mx-2 hover-white">Dashboard</a>
                    <?php endif; ?>
                </div>
                <div class="col-md-4 text-md-end">
                    <p class="small mb-0">&copy; <?php echo date('Y'); ?> Lazy Barber. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="js/main.js"></script>
</body>
</html>
