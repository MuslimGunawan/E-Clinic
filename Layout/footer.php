<footer>
        <div class="container">
            <p class="mb-1">&copy; <?= date('Y') ?> E-Clinic Management System.</p>
            <small class="text-muted opacity-50">Dibuat untuk Tugas UAS Basis Data (Muslim & Rahmi)</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script Efek Glass Navbar -->
    <script>
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('mainNavbar');
            if (window.scrollY > 50) {
                navbar.classList.add('navbar-glass'); // Tambah efek kaca
                navbar.classList.remove('bg-primary'); // Hapus warna solid default (opsional jika override CSS kuat)
            } else {
                navbar.classList.remove('navbar-glass'); // Kembali normal
                navbar.classList.add('bg-primary'); // Kembali ke warna solid
            }
        });

        // Inisialisasi saat load (jika di-refresh posisi sudah di bawah)
        if (window.scrollY > 50) {
            document.getElementById('mainNavbar').classList.add('navbar-glass');
        } else {
            document.getElementById('mainNavbar').classList.add('bg-primary');
        }
    </script>
</body>
</html>