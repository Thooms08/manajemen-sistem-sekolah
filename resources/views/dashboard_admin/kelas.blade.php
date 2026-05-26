<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kelas</title>
    @if(isset($sekolah->logo))
    <link rel="icon" type="image/png" href="{{ asset($sekolah->logo) }}">
    @else
    <link rel="icon" type="image/png" href="{{ asset('assets/img/default-favicon.png') }}">
    @endif
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-green: #198754;
            --dark-green: #146c43;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f6;
            overflow-x: hidden;
        }

        .wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
        }

        #content {
            width: 100%;
            padding: 20px 30px;
            transition: all 0.3s;
            min-height: 100vh;
        }

        /* Hamburger Button Styling */
        #sidebarCollapse {
            width: 45px;
            height: 45px;
            background: var(--primary-green);
            border: none;
            color: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(25, 135, 84, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Class Card Styling */
        .class-card {
            border: none;
            border-radius: 15px;
            transition: all 0.3s ease;
            overflow: hidden;
            border-bottom: 5px solid var(--primary-green);
            background: #fff;
        }

        .class-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 25px rgba(0,0,0,0.1) !important;
        }

        .card-body-link {
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .icon-box {
            font-size: 3rem;
            color: var(--primary-green);
            margin-bottom: 10px;
        }

        /* Overlay for Mobile */
        #overlay {
            display: none;
            position: fixed;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1040;
            top: 0;
            left: 0;
        }

        #overlay.active {
            display: block;
        }

        @media (max-width: 768px) {
            #content { padding: 15px; }
        }
    </style>
</head>
<body>

    <div id="overlay"></div>

    <div class="wrapper">
        @include('dashboard_admin.sidebar_admin')

        <div id="content">
            <div class="container-fluid">
                
                <div class="d-flex align-items-center justify-content-between mb-4 mt-2">
                    <div class="d-flex align-items-center">
                        <button type="button" id="sidebarCollapse" class="btn">
                            <i class="bi bi-list fs-4"></i>
                        </button>
                        <div class="ms-3">
                            <h4 class="mb-0 fw-bold text-success">Manajemen Kelas</h4>
                            <p class="text-muted small mb-0">Klik pada kelas untuk mengelola murid</p>
                        </div>
                    </div>
                    <button class="btn btn-success px-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahKelas">
                        <i class="bi bi-plus-lg me-2"></i>Tambah Kelas
                    </button>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row g-4">
                    @forelse($kelas as $k)
                    <div class="col-md-4 col-lg-3">
                        <div class="card class-card shadow-sm h-100">
                            <a href="{{ route('kelas.show', $k->id) }}" class="card-body-link">
                                <div class="card-body p-4 text-center">
                                    <div class="icon-box">
                                        <i class="bi bi-door-open-fill"></i>
                                    </div>
                                    <h5 class="fw-bold mb-1">{{ $k->nama_kelas }}</h5>
                                    <p class="text-muted small mb-0">
                                        <i class="bi bi-people me-1"></i> {{ $k->murid_count }} Murid Terdaftar
                                    </p>
                                </div>
                            </a>
                            <div class="card-footer bg-white border-0 pb-3 text-center">
                                <button class="btn btn-sm btn-outline-success border-0" 
                                    onclick="openEditModal('{{ $k->id }}', '{{ $k->nama_kelas }}')">
                                    <i class="bi bi-pencil-square"></i> Edit Kelas
                                </button>
                                <form action="{{ route('kelas.destroy', $k->id) }}" method="POST" onsubmit="return confirm('Hapus kelas ini? Semua data murid di dalamnya akan terlepas.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger fw-semibold border-0">
                                        <i class="bi bi-trash me-1"></i> Hapus Kelas
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center py-5">
                        <div class="display-1 text-muted opacity-25 mb-3"><i class="bi bi-folder-x"></i></div>
                        <h5 class="text-muted">Belum ada data kelas.</h5>
                    </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTambahKelas" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <form action="{{ route('kelas.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title fw-bold">Buat Kelas Baru</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Kelas</label>
                            <input type="text" name="nama_kelas" class="form-control form-control-lg" placeholder="Contoh: 7B" required>
                            <small class="text-muted">Pastikan nama kelas belum digunakan.</small>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success px-4">Simpan Kelas</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalEditKelas" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form id="formEditKelas" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title fw-bold">Edit Nama Kelas</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <label class="form-label fw-bold">Nama Kelas Baru</label>
                        <input type="text" name="nama_kelas" id="edit_nama_kelas" class="form-control" required>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-success px-4">Perbarui Nama</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const sidebar = document.getElementById('sidebar');
            const collapseBtn = document.getElementById('sidebarCollapse');
            const overlay = document.getElementById('overlay');
            const closeBtn = document.getElementById('close-sidebar');

            function toggleSidebar() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.toggle('show-mobile');
                    overlay.classList.toggle('active');
                } else {
                    sidebar.classList.toggle('inactive');
                }
            }

            if(collapseBtn) collapseBtn.onclick = toggleSidebar;
            if(closeBtn) closeBtn.onclick = toggleSidebar;
            if(overlay) overlay.onclick = toggleSidebar;
        });
        const editModal = new bootstrap.Modal(document.getElementById('modalEditKelas'));
        function openEditModal(id, nama) {
            const form = document.getElementById('formEditKelas');
            form.action = `/kelas/${id}`; // Mengarahkan action form ke route update
            document.getElementById('edit_nama_kelas').value = nama; // Mengisi input dengan nama saat ini
            editModal.show();
        }
    </script>
</body>
</html>