<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    :root {
        --sidebar-bg: #1a3a3a; /* Hijau Gelap Modern */
        --sidebar-hover: #2d5a5a;
        --sidebar-active: #198754; /* Hijau Bootstrap Success */
        --sidebar-header: #142d2d;
    }

    #sidebar {
        min-width: 280px;
        max-width: 280px;
        background: var(--sidebar-bg);
        color: #fff;
        transition: all 0.3s ease-in-out;
        height: 100vh;
        position: sticky;
        top: 0;
        display: flex;
        flex-direction: column;
        z-index: 1050;
    }

    /* Efek tutup untuk tampilan Desktop */
    #sidebar.inactive {
        margin-left: -280px;
    }

    #sidebar .sidebar-header {
        padding: 20px;
        background: var(--sidebar-header);
        border-bottom: 1px solid rgba(255,255,255,0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Tombol X (Close) hanya muncul di Mobile */
    #close-sidebar {
        background: transparent;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        display: none; /* Sembunyi di desktop */
        line-height: 1;
    }

    #sidebar ul.components {
        padding: 15px 0;
        flex-grow: 1;
        overflow-y: auto;
    }

    #sidebar ul li a {
        padding: 12px 20px;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        text-decoration: none;
        color: rgba(255, 255, 255, 0.8);
        transition: 0.2s;
    }

    #sidebar ul li a:hover {
        background: var(--sidebar-hover);
        color: #fff;
    }

    #sidebar ul li.active > a {
        background: var(--sidebar-active);
        color: #fff;
    }

    #sidebar ul li a i {
        margin-right: 15px;
        font-size: 1.1rem;
    }

    /* Styling Submenu */
    .collapse-inner {
        background: rgba(0, 0, 0, 0.15);
        padding: 5px 0;
    }

    .collapse-inner a {
        padding-left: 50px !important;
        font-size: 0.85rem !important;
        color: rgba(255, 255, 255, 0.7) !important;
    }

    .collapse-inner a:hover {
        color: #fff !important;
    }

    /* Section Logout di paling bawah */
    .logout-section {
        border-top: 1px solid rgba(255,255,255,0.1);
        padding: 15px;
    }

    .btn-logout {
        width: 100%;
        text-align: left;
        padding: 12px 15px;
        background: transparent;
        border: none;
        color: #ff8080;
        display: flex;
        align-items: center;
        transition: 0.2s;
        border-radius: 8px;
    }

    .btn-logout:hover {
        background: rgba(255, 77, 77, 0.1);
        color: #ff4d4d;
    }
    /* Gaya untuk link sub-menu yang aktif */
.collapse-inner a.active-sub {
    color: #198754 !important; /* Warna hijau */
    font-weight: bold;
    background-color: rgba(25, 135, 84, 0.1);
    border-radius: 5px;
    padding-left: 10px;
}

/* Gaya untuk parent dropdown saat aktif */
.dropdown-toggle.active {
    background-color: rgba(25, 135, 84, 0.05);
}

.badge-notif {
        background: #ff4d4d;
        color: white;
        padding: 2px 8px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: bold;
        margin-left: auto; /* Mendorong ke kanan */
        box-shadow: 0 2px 5px rgba(255, 77, 77, 0.3);
        display: none; /* Sembunyi jika 0 */
    }

    /* --- RESPONSIVE MOBILE LOGIC --- */
    @media (max-width: 768px) {
        #sidebar {
            position: fixed;
            left: -280px; /* Sembunyi ke kiri secara default */
            margin-left: 0 !important; /* Override margin desktop */
        }
        
        #sidebar.show-mobile {
            left: 0; /* Geser ke kanan saat dibuka */
        }

        #close-sidebar {
            display: block; /* Muncul di HP */
        }
    }
</style>

<nav id="sidebar">
    <div class="sidebar-header">
        <div>
            <h5 class="mb-0 fw-bold"><i class="bi bi-shield-check me-2"></i>ADMIN PANEL</h5>
            <small class="text-success text-opacity-75">Sistem Sekolah</small>
        </div>
        <button id="close-sidebar" title="Tutup Menu">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <?php $menu_biaya = request()->routeIs('biaya-murid.*') ? 'active' : ''; ?>

    <ul class="list-unstyled components">
        <li class="{{ Request::is('admin') ? 'active' : '' }}">
            <a href="{{ route('admin.home') }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>

        <li class="{{ Request::is('profile-sekolah*') ? 'active' : '' }}">
            <a href="{{ route('profile-sekolah.index') }}">
                <i class="bi bi-building"></i> Profile Sekolah
            </a>
        </li>
       <li class="{{ request()->routeIs('informasi.*') ? 'active' : '' }}">
            <a href="{{ route('informasi.index') }}">
                <i class="bi bi-info-circle"></i> Kelola Informasi
            </a>
        </li>
        <li class="{{ request()->routeIs('admin.ppdb.*') ? 'active' : '' }}">
            <a href="{{ route('admin.ppdb.index') }}" class="d-flex align-items-center w-100">
                <i class="bi bi-bell"></i> 
                <span>Notifikasi PPDB</span>
                <span id="ppdb-badge" class="badge-notif" style="display: none;">0</span>
            </a>
        </li>
        <li class="{{ request()->routeIs('murid.create') ? 'active' : '' }}">
            <a href="{{ route('murid.create') }}" class="d-flex align-items-center w-100">
                <i class="bi bi-person-plus"></i> 
                <span>PPDB</span>
                <span id="ppdb-badge" class="badge-notif" style="display: none;">0</span>
            </a>
        </li>
        <li class="{{ Request::is('kelas*') ? 'active' : '' }}">
            <a href="{{ route('kelas.index') }}">
                <i class="bi bi-door-open"></i> Kelola Kelas
            </a>
        </li>

        <li class="px-3 mt-4 mb-2">
            <small class="text-uppercase fw-bold" style="font-size: 1rem; letter-spacing: 1px; color:white;">Data Master</small>
        </li>

       <li class="{{ Request::is('guru*') ? 'active' : '' }}">
            <a href="{{ route('guru.index') }}">
                <i class="bi bi-person-badge"></i> Data Guru
            </a>
        </li>
        <li class="{{ Request::is('mapel*') ? 'active' : '' }}">
            <a href="{{ route('mapel.index') }}">
                <i class="bi bi-person-badge"></i> Data Mapel
            </a>
        </li>
        <li class="{{ Request::is('staff*') ? 'active' : '' }}">
            <a href="{{ route('staff.index') }}">
                <i class="bi bi-person-badge"></i> Data Staff
            </a>
        </li>
        <li class="{{ request()->routeIs('murid.*') ? 'active' : '' }}">
            <a href="{{ route('murid.index') }}">
                <i class="bi bi-people"></i> Data Murid
            </a>
        </li>
        <li class="{{ request()->routeIs('ortu-murid.*') ? 'active' : '' }}">
            <a href="{{ route('ortu-murid.index') }}">
                <i class="bi bi-person-hearts"></i> Data Ortu Murid
            </a>
        </li>
        <li class="{{ request()->routeIs('wali-murid.*') ? 'active' : '' }}">
            <a href="{{ route('wali-murid.index') }}">
                <i class="bi bi-person-hearts"></i> Data Wali Murid
            </a>
        </li>
        <li class="{{ request()->routeIs('kelulusan.*') ? 'active' : '' }}">
            <a href="{{ route('kelulusan.index') }}">
                <i class="bi bi-people-fill"></i> Data Kelulusan
            </a>
        </li>
        <li class="{{ request()->routeIs('alumni.*') ? 'active' : '' }}">
            <a href="{{ route('alumni.index') }}">
                <i class="bi bi-people-fill"></i> Data Alumni
            </a>
        </li>
         <li class="{{ request()->routeIs('dokumen.*') ? 'active' : '' }}">
            <a href="{{ route('dokumen.index') }}">
                <i class="bi bi-people-fill"></i> Manajemen Dokumen
            </a>
        </li>
        <li class="{{ request()->routeIs('admin.pengaturan-form-ppdb.*') ? 'active' : '' }}">
            <a href="{{ route('admin.pengaturan-form-ppdb') }}">
                <i class="bi bi-gear"></i> Pengaturan Form PPDB
            </a>
        </li>
        <li class="{{ $menu_biaya }}">
            <a href="{{ route('biaya-murid.index') }}">
                <i class="bi bi-cash-stack"></i> Biaya Murid
            </a>
        </li>

        <li class="px-3 mt-4 mb-2">
            <small class="text-uppercase fw-bold" style="font-size: 1rem; letter-spacing: 1px; color:white;">Keuangan</small>
        </li>

        <li class="{{ request()->routeIs('keuangan.pemasukan.*') ? 'active' : '' }}">
            <a href="{{ route('keuangan.pemasukan.index') }}">
                <i class="bi bi-arrow-down-circle"></i> Pemasukan
            </a>
        </li>
        <li class="{{ request()->routeIs('keuangan.pengeluaran.*') ? 'active' : '' }}">
            <a href="{{ route('keuangan.pengeluaran.index') }}">
                <i class="bi bi-arrow-up-circle"></i> Pengeluaran
            </a>
        </li>
        <li class="{{ request()->routeIs('keuangan.laporan.*') ? 'active' : '' }}">
            <a href="{{ route('keuangan.laporan.index') }}">
                <i class="bi bi-bar-chart-line"></i> Laporan Keuangan
            </a>
        </li>
        <li class="{{ request()->routeIs('keuangan.laporan.*') ? 'active' : '' }}">
            <a href="{{ route('keuangan.laporan.index') }}">
                <i class="bi bi-bar-chart-line"></i> Kelola Akun Monitoring
            </a>
        </li>

    <div class="logout-section">
        <form action="{{ route('logout') }}" method="POST" id="logout-form">
            @csrf
            <button type="button" class="btn-logout" onclick="confirmLogout()">
                <i class="bi bi-box-arrow-right me-3"></i>
                <span>Log Out</span>
            </button>
        </form>
    </div>
</nav>
@include('loading')
<script>
    /**
     * Fungsi Terpusat untuk Memperbarui Semua Badge Notifikasi
     */
    function updateAllBadges() {
        // 1. Update Badge Konfirmasi Pelanggaran
        fetch("{{ route('admin.pelanggaran.count') }}")
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('notif-count');
                if (badge) {
                    if (data.count > 0) {
                        badge.innerText = data.count;
                        badge.style.display = 'block';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            })
            .catch(error => console.error('Error Pelanggaran Badge:', error));

        // 2. Update Badge Notifikasi PPDB
        fetch("{{ route('admin.ppdb.count') }}")
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('ppdb-badge');
                if (badge) {
                    if (data.count > 0) {
                        badge.innerText = data.count;
                        badge.style.display = 'block';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            })
            .catch(error => console.error('Error PPDB Badge:', error));
    }

    // Jalankan pertama kali saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        updateAllBadges();

        // Cek berkala setiap 10 detik (Real-time polling)
        setInterval(updateAllBadges, 10000); 

        // --- Logika Sidebar Hamburger ---
        const sidebar = document.getElementById('sidebar');
        const closeBtn = document.getElementById('close-sidebar');
        
        window.toggleSidebar = function() {
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('show-mobile');
            } else {
                sidebar.classList.toggle('inactive');
            }
        };

        if(closeBtn) {
            closeBtn.addEventListener('click', function() {
                sidebar.classList.remove('show-mobile');
            });
        }
    });
    
    function confirmLogout() {
        Swal.fire({
            title: 'Yakin ingin log out?',
            text: "Anda harus login kembali untuk masuk ke sistem.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#198754', 
            cancelButtonColor: '#d33',     
            confirmButtonText: 'Ya, Log Out!',
            cancelButtonText: 'Batal',
            reverseButtons: true 
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        });
    }
</script>