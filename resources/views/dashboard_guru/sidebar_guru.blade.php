<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

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
            <h5 class="mb-0 fw-bold"><i class="bi bi-shield-check me-2"></i>GURU PANEL</h5>
            <small class="text-success text-opacity-75">Sistem Sekolah</small>
        </div>
        <button id="close-sidebar" title="Tutup Menu">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <ul class="list-unstyled components">
        <li class="{{ Request::is('dashboard_admin') ? 'active' : '' }}">
            <a href="{{ route('guru.home') }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>

        <li>
        <a href="{{ route('guru.keaktifan') }}"><i class="bi bi-activity"></i> Keaktifan</a>
        </li>

        <li>
        <a href="{{ route('guru.pelanggaran') }}"><i class="bi bi-x-octagon"></i> Pelanggaran</a>
        </li>

        <li>
        <a href="{{ route('guru.absensi') }}"><i class="bi bi-calendar-check"></i> Absensi</a>
        </li>
        <!-- <li><a href="#"><i class="bi bi-door-open"></i> Kelola Kelas</a></li> -->

        <!-- <li class="px-3 mt-4 mb-1">
            <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem; letter-spacing: 1px;">Data Master</small>
        </li> -->

        <!-- <li><a href="#"><i class="bi bi-person-badge"></i> Data Guru / Staff</a></li>
        <li><a href="#"><i class="bi bi-people"></i> Data Murid</a></li>
        <li><a href="#"><i class="bi bi-person-hearts"></i> Data Wali Murid</a></li>

        <li>
            <a href="#submenuMurid" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                <i class="bi bi-graph-up"></i> Aktifitas Murid
            </a>
            <div class="collapse {{ Request::is('aktifitas-murid/*') ? 'show' : '' }}" id="submenuMurid">
                <div class="collapse-inner">
                    <a href="#" class="d-block">Keaktifan</a>
                    <a href="#" class="d-block">Pelanggaran</a>
                    <a href="#" class="d-block">Arsip Absensi</a>
                </div>
            </div>
        </li> -->

        <!-- <li><a href="#"><i class="bi bi-journal-check"></i> Aktifitas Guru</a></li>

        <li>
            <a href="#submenuAkun" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                <i class="bi bi-shield-lock"></i> Kelola Akun
            </a>
            <div class="collapse" id="submenuAkun">
                <div class="collapse-inner">
                    <a href="#" class="d-block">Akun Guru</a>
                    <a href="#" class="d-block">Akun Wali Murid</a>
                </div>
            </div>
        </li> -->
    </ul>

    <div class="logout-section">
        <form action="{{ route('logout') }}" method="POST" id="logout-form">
            @csrf
            <button type="submit" class="btn-logout">
                <i class="bi bi-box-arrow-right me-3"></i>
                <span>Log Out</span>
            </button>
        </form>
    </div>
</nav>