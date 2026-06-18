{{-- ================================================================
     SIDEBAR USER DINAMIS — mirip sidebar admin
     Menu hanya tampil jika user punya permission pada modul tersebut.
     Menggunakan helper can() / canAny() dari app/Helpers/functions.php
================================================================ --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    :root {
        --sidebar-bg: #1a3a3a;
        --sidebar-hover: #2d5a5a;
        --sidebar-active: #198754;
        --sidebar-header: #142d2d;
    }
    #sidebar {
        min-width: 280px; max-width: 280px;
        background: var(--sidebar-bg); color: #fff;
        transition: all 0.3s ease-in-out;
        height: 100vh; position: sticky; top: 0;
        display: flex; flex-direction: column; z-index: 1050;
    }
    #sidebar.inactive { margin-left: -280px; }
    #sidebar .sidebar-header {
        padding: 20px; background: var(--sidebar-header);
        border-bottom: 1px solid rgba(255,255,255,.1);
        display: flex; justify-content: space-between; align-items: center;
    }
    #close-sidebar {
        background: transparent; border: none; color: white;
        font-size: 1.5rem; cursor: pointer; display: none; line-height: 1;
    }
    #sidebar ul.components { padding: 15px 0; flex-grow: 1; overflow-y: auto; }
    #sidebar ul li a {
        padding: 12px 20px; font-size: 0.95rem;
        display: flex; align-items: center;
        text-decoration: none; color: rgba(255,255,255,.8); transition: 0.2s;
    }
    #sidebar ul li a:hover { background: var(--sidebar-hover); color: #fff; }
    #sidebar ul li.active > a { background: var(--sidebar-active); color: #fff; }
    #sidebar ul li a i { margin-right: 15px; font-size: 1.1rem; }
    .collapse-inner { background: rgba(0,0,0,.15); padding: 5px 0; }
    .collapse-inner a { padding-left: 50px !important; font-size: 0.85rem !important; color: rgba(255,255,255,.7) !important; }
    .collapse-inner a:hover { color: #fff !important; }
    .collapse-inner a.active-sub { color: #4ade80 !important; font-weight: bold; background-color: rgba(25,135,84,.1); border-radius: 5px; padding-left: 10px; }
    .badge-notif { background: #ff4d4d; color: white; padding: 2px 8px; border-radius: 50px; font-size: 0.75rem; font-weight: bold; margin-left: auto; box-shadow: 0 2px 5px rgba(255,77,77,.3); display: none; }
    .logout-section { border-top: 1px solid rgba(255,255,255,.1); padding: 15px; }
    .btn-logout { width: 100%; text-align: left; padding: 12px 15px; background: transparent; border: none; color: #ff8080; display: flex; align-items: center; transition: 0.2s; border-radius: 8px; }
    .btn-logout:hover { background: rgba(255,77,77,.1); color: #ff4d4d; }
    @media (max-width: 768px) {
        #sidebar { position: fixed; left: -280px; margin-left: 0 !important; }
        #sidebar.show-mobile { left: 0; }
        #close-sidebar { display: block; }
    }
</style>

@php
    // Ambil role user untuk header sidebar
    use App\Models\Pengaturan\Role;
    $sidebarUser     = auth()->user();
    $sidebarRoleSlug = $sidebarUser->role ?? $sidebarUser->rules ?? '';
    $sidebarRole     = Role::where('slug', $sidebarRoleSlug)->first();
    $isAdminUser     = $sidebarRoleSlug === 'admin';
@endphp

<nav id="sidebar">
    <div class="sidebar-header">
        <div>
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-shield-check me-2"></i>
                {{ $sidebarRole ? $sidebarRole->nama : 'User Panel' }}
            </h5>
            <small class="text-success text-opacity-75">{{ $sidebarUser->username }}</small>
        </div>
        <button id="close-sidebar" title="Tutup Menu"><i class="bi bi-x-lg"></i></button>
    </div>

    <ul class="list-unstyled components">

        {{-- ── Dashboard ── --}}
        <li class="{{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
            <a href="{{ route('user.dashboard') }}">
                <i class="bi bi-speedometer2"></i> Beranda
            </a>
        </li>

        {{-- ── Profile Sekolah ── --}}
        @if(canAny('profile_sekolah'))
        <li class="{{ Request::is('profile-sekolah*') ? 'active' : '' }}">
            <a href="{{ route('profile-sekolah.index') }}">
                <i class="bi bi-building"></i> Profile Sekolah
            </a>
        </li>
        @endif

        {{-- ── Kelola Informasi ── --}}
        @if(canAny('kelola_informasi') || canAny('prestasi'))
        <li class="{{ request()->routeIs('informasi.*') ? 'active' : '' }}">
            <a href="{{ route('informasi.index') }}">
                <i class="bi bi-info-circle"></i> Kelola Informasi
            </a>
        </li>
        @endif

        {{-- ── Notifikasi PPDB ── --}}
        @if(canAny('notifikasi_ppdb'))
        <li class="{{ request()->routeIs('admin.ppdb.*') ? 'active' : '' }}">
            <a href="{{ route('admin.ppdb.index') }}" class="d-flex align-items-center w-100">
                <i class="bi bi-bell"></i>
                <span>Notifikasi PPDB</span>
                <span id="ppdb-badge" class="badge-notif" style="display:none;">0</span>
            </a>
        </li>
        @endif

        {{-- ── PPDB (Input Murid) ── --}}
        @if(canAny('ppdb_form'))
        <li class="{{ request()->routeIs('murid.create') ? 'active' : '' }}">
            <a href="{{ route('murid.create') }}">
                <i class="bi bi-person-plus"></i> PPDB
            </a>
        </li>
        @endif

        {{-- ── DATA MASTER ── --}}
        @php
            $hasDataMaster = canAny('data_guru') || canAny('jadwal_mengajar') || canAny('data_mapel') ||
                             canAny('data_staff') || canAny('data_murid') || canAny('data_kelas') ||
                             canAny('data_kelulusan') || canAny('data_alumni') || canAny('data_ortu') ||
                             canAny('data_wali') || canAny('catatan');
        @endphp
        @if($hasDataMaster)
        <li class="px-3 mt-4 mb-2">
            <small class="text-uppercase fw-bold" style="font-size:0.85rem;letter-spacing:1px;color:rgba(255,255,255,.6);">Data Master</small>
        </li>
        @endif

        {{-- Guru + Jadwal Mengajar --}}
        @if(canAny('data_guru') || canAny('jadwal_mengajar'))
        @php $isGuruMenu = Request::is('guru*') || Request::is('jadwal-mengajar*'); @endphp
        <li class="{{ $isGuruMenu ? 'active' : '' }}">
            <a href="#submenu-guru" data-bs-toggle="collapse"
               class="d-flex align-items-center {{ $isGuruMenu ? 'active' : '' }}"
               aria-expanded="{{ $isGuruMenu ? 'true' : 'false' }}">
                <i class="bi bi-person-badge"></i>
                <span class="flex-grow-1">Data Guru</span>
                <i class="bi bi-chevron-down small"></i>
            </a>
            <div class="collapse {{ $isGuruMenu ? 'show' : '' }}" id="submenu-guru">
                <ul class="list-unstyled collapse-inner">
                    @if(canAny('data_guru'))
                    <li>
                        <a href="{{ route('guru.index') }}" class="{{ Request::is('guru*') ? 'active-sub' : '' }}">
                            <i class="bi bi-people me-2"></i> Data Guru
                        </a>
                    </li>
                    @endif
                    @if(canAny('jadwal_mengajar'))
                    <li>
                        <a href="{{ route('jadwal-mengajar.index') }}" class="{{ Request::is('jadwal-mengajar*') ? 'active-sub' : '' }}">
                            <i class="bi bi-calendar3-week me-2"></i> Jadwal Mengajar
                        </a>
                    </li>
                    @endif
                </ul>
            </div>
        </li>
        @endif

        @if(canAny('data_mapel'))
        <li class="{{ Request::is('mapel*') ? 'active' : '' }}">
            <a href="{{ route('mapel.index') }}"><i class="bi bi-book"></i> Data Mapel</a>
        </li>
        @endif

        @if(canAny('data_staff'))
        <li class="{{ Request::is('staff*') ? 'active' : '' }}">
            <a href="{{ route('staff.index') }}"><i class="bi bi-person-workspace"></i> Data Staff</a>
        </li>
        @endif

        @if(canAny('data_murid'))
        <li class="{{ request()->routeIs('murid.*') ? 'active' : '' }}">
            <a href="{{ route('murid.index') }}"><i class="bi bi-people"></i> Data Murid</a>
        </li>
        @endif

        @if(canAny('data_ortu'))
        <li class="{{ request()->routeIs('ortu-murid.*') ? 'active' : '' }}">
            <a href="{{ route('ortu-murid.index') }}"><i class="bi bi-person-hearts"></i> Data Ortu Murid</a>
        </li>
        @endif

        @if(canAny('data_wali'))
        <li class="{{ request()->routeIs('wali-murid.*') ? 'active' : '' }}">
            <a href="{{ route('wali-murid.index') }}"><i class="bi bi-person-hearts"></i> Data Wali Murid</a>
        </li>
        @endif

        @if(canAny('data_kelulusan'))
        <li class="{{ request()->routeIs('kelulusan.*') ? 'active' : '' }}">
            <a href="{{ route('kelulusan.index') }}"><i class="bi bi-mortarboard"></i> Data Kelulusan</a>
        </li>
        @endif

        @if(canAny('data_alumni'))
        <li class="{{ request()->routeIs('alumni.*') ? 'active' : '' }}">
            <a href="{{ route('alumni.index') }}"><i class="bi bi-people-fill"></i> Data Alumni</a>
        </li>
        @endif

        @if(canAny('data_kelas'))
        <li class="{{ Request::is('kelas*') ? 'active' : '' }}">
            <a href="{{ route('kelas.index') }}"><i class="bi bi-door-open"></i> Kelola Kelas</a>
        </li>
        @endif

        {{-- Catatan -- selalu tampil untuk user dinamis --}}
        <li class="{{ request()->routeIs('user.catatan.*') ? 'active' : '' }}">
            <a href="{{ route('user.catatan.index') }}">
                <i class="bi bi-journal-text"></i> Catatan Saya
            </a>
        </li>

        {{-- ── DOKUMEN ── --}}
        @if(canAny('dokumen'))
        <li class="px-3 mt-4 mb-2">
            <small class="text-uppercase fw-bold" style="font-size:0.85rem;letter-spacing:1px;color:rgba(255,255,255,.6);">Dokumen</small>
        </li>
        <li class="{{ request()->routeIs('dokumen.*') ? 'active' : '' }}">
            <a href="{{ route('dokumen.index') }}"><i class="bi bi-folder2"></i> Manajemen Dokumen</a>
        </li>
        @endif

        {{-- ── KEUANGAN ── --}}
        @php
            $hasKeuangan = canAny('biaya_murid') || canAny('keuangan_pemasukan') ||
                           canAny('keuangan_pengeluaran') || canAny('laporan_keuangan');
        @endphp
        @if($hasKeuangan)
        <li class="px-3 mt-4 mb-2">
            <small class="text-uppercase fw-bold" style="font-size:0.85rem;letter-spacing:1px;color:rgba(255,255,255,.6);">Keuangan</small>
        </li>
        @endif

        @if(canAny('biaya_murid'))
        <li class="{{ request()->routeIs('biaya-murid.*') ? 'active' : '' }}">
            <a href="{{ route('biaya-murid.index') }}"><i class="bi bi-cash-stack"></i> Biaya Murid</a>
        </li>
        @endif

        @if(canAny('keuangan_pemasukan'))
        <li class="{{ request()->routeIs('keuangan.pemasukan.*') ? 'active' : '' }}">
            <a href="{{ route('keuangan.pemasukan.index') }}"><i class="bi bi-arrow-down-circle"></i> Pemasukan</a>
        </li>
        @endif

        @if(canAny('keuangan_pengeluaran'))
        <li class="{{ request()->routeIs('keuangan.pengeluaran.*') ? 'active' : '' }}">
            <a href="{{ route('keuangan.pengeluaran.index') }}"><i class="bi bi-arrow-up-circle"></i> Pengeluaran</a>
        </li>
        @endif

        @if(canAny('laporan_keuangan'))
        <li class="{{ request()->routeIs('keuangan.laporan.*') ? 'active' : '' }}">
            <a href="{{ route('keuangan.laporan.index') }}"><i class="bi bi-bar-chart-line"></i> Laporan Keuangan</a>
        </li>
        @endif

    </ul>

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
    document.addEventListener('DOMContentLoaded', function () {
        @if(canAny('notifikasi_ppdb'))
        function updatePpdbBadge() {
            fetch("{{ route('admin.ppdb.count') }}")
                .then(r => r.json())
                .then(data => {
                    const badge = document.getElementById('ppdb-badge');
                    if (badge) {
                        if (data.count > 0) { badge.innerText = data.count; badge.style.display = 'block'; }
                        else { badge.style.display = 'none'; }
                    }
                }).catch(() => {});
        }
        updatePpdbBadge();
        setInterval(updatePpdbBadge, 10000);
        @endif

        const sidebar  = document.getElementById('sidebar');
        const closeBtn = document.getElementById('close-sidebar');

        window.toggleSidebar = function () {
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('show-mobile');
                const overlay = document.getElementById('overlay');
                if (overlay) overlay.classList.toggle('active');
            } else {
                sidebar.classList.toggle('inactive');
            }
        };

        if (closeBtn) {
            closeBtn.addEventListener('click', function () {
                sidebar.classList.remove('show-mobile');
                const overlay = document.getElementById('overlay');
                if (overlay) overlay.classList.remove('active');
            });
        }
    });

    function confirmLogout() {
        Swal.fire({
            title: 'Yakin ingin log out?',
            text: 'Anda harus login kembali untuk masuk ke sistem.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Log Out!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then(r => { if (r.isConfirmed) document.getElementById('logout-form').submit(); });
    }

    // ── Alert permission_error (tampil otomatis jika ada flash message) ──
    @if(session('permission_error'))
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: 'warning',
            title: 'Akses Ditolak',
            text: {!! json_encode(session('permission_error')) !!},
            confirmButtonColor: '#198754',
            confirmButtonText: 'Mengerti',
            timer: 6000,
            timerProgressBar: true,
        });
    });
    @endif
</script>
