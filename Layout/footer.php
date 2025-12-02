<footer>
        <div class="container">
            <p class="mb-1">&copy; <?= date('Y') ?> E-Clinic Management System.</p>
            <small class="text-muted opacity-50">Dibuat untuk Tugas UAS Basis Data (Muslim & Rahmi)</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script Efek Glass Navbar (Dinonaktifkan Sementara untuk Debugging) -->
    <!-- 
    <script>
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('mainNavbar');
            if (navbar) {
                if (window.scrollY > 50) {
                    navbar.classList.add('navbar-glass');
                    navbar.classList.remove('bg-primary');
                } else {
                    navbar.classList.remove('navbar-glass');
                    navbar.classList.add('bg-primary');
                }
            }
        });

        // Inisialisasi saat load
        const navbar = document.getElementById('mainNavbar');
        if (navbar) {
            if (window.scrollY > 50) {
                navbar.classList.add('navbar-glass');
            } else {
                navbar.classList.add('bg-primary');
            }
        }
    </script> 
    -->

    <!-- Fitur Pencarian Cepat Tabel (Client Side) -->
    <script>
    document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.createElement("input");
    searchInput.setAttribute("type", "text");
    searchInput.setAttribute("placeholder", "Cari dokter atau poli...");
    searchInput.classList.add("form-control", "mb-3", "shadow-sm");
    
    // Cari tabel jadwal dokter
    const tableContainer = document.querySelector("#jadwal .glass-panel .card-body");
    if(tableContainer) {
        tableContainer.insertBefore(searchInput, tableContainer.firstChild);
        
        searchInput.addEventListener("keyup", function() {
            const value = this.value.toLowerCase();
            const rows = document.querySelectorAll("#jadwal table tbody tr");
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.indexOf(value) > -1 ? "" : "none";
            });
        });
    }
});
    </script>
</body>
</html>