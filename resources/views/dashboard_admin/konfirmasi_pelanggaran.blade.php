<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pelanggaran</title>
    @if(isset($sekolah->logo))
    <link rel="icon" type="image/png" href="{{ asset($sekolah->logo) }}">
    @else
    <link rel="icon" type="image/png" href="{{ asset('assets/img/default-favicon.png') }}">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --admin-green: #198754; --soft-green: #e8f5e9; --dark-green: #0a4d2e; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8faf9; }
        .wrapper { display: flex; }
        #content { width: 100%; padding: 30px; transition: all 0.3s; }
        .sidebar-header { padding: 20px; background: var(--dark-green); color: white; }
        #sidebarCollapse { background: var(--admin-green); border: none; color: white; border-radius: 8px; padding: 5px 12px; }
        
        /* Card Style */
        .card-notif { border: none; border-radius: 15px; transition: 0.3s; background: white; border-left: 5px solid var(--admin-green); }
        .card-notif:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
        .btn-confirm { background-color: var(--admin-green); color: white; border-radius: 10px; font-weight: 600; border: none; }
        .btn-confirm:hover { background-color: var(--dark-green); color: white; }
        .btn-reject { background-color: #fff1f0; color: #d93025; border-radius: 10px; font-weight: 600; border: 1px solid #ffccc7; }
        .btn-reject:hover { background-color: #ffccc7; }
        .badge-pending { background-color: #fff8e1; color: #f57f17; border: 1px solid #ffe082; }
    </style>
</head>
<body>

<div class="wrapper">
    {{-- Memanggil Sidebar Admin --}}
    @include('dashboard_admin.sidebar_admin') 

    <div id="content">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center">
                    <button type="button" id="sidebarCollapse" class="btn me-3">
                        <i class="bi bi-list fs-4"></i>
                    </button>
                    <div>
                        <h4 class="fw-bold mb-0">Konfirmasi Pelanggaran</h4>
                        <p class="text-muted small mb-0">Validasi laporan pelanggaran dari guru</p>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm mb-4 alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row g-4">
                @forelse($pendingPelanggaran as $item)
                <div class="col-md-6 col-lg-4">
                    <div class="card card-notif shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between mb-3">
                                <span class="badge badge-pending px-3 py-2 rounded-pill small">
                                    <i class="bi bi-clock-history me-1"></i> Menunggu
                                </span>
                                <small class="text-muted">{{ date('d/m/Y H:i', strtotime($item->created_at)) }}</small>
                            </div>
                            
                            <h5 class="fw-bold mb-1 text-dark">{{ $item->nama_lengkap }}</h5>
                            <p class="text-success fw-semibold small mb-3">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $item->nama_pelanggaran }} ({{ $item->skor }} Poin)
                            </p>
                            
                            <div class="bg-light p-3 rounded-3 mb-4">
                                <small class="text-muted d-block mb-1">Keterangan:</small>
                                <p class="mb-0 small text-dark italic">"{{ $item->keterangan ?? 'Tidak ada keterangan' }}"</p>
                            </div>

                            <div class="row g-2">
                                <div class="col-6">
                                    <form action="{{ route('admin.pelanggaran.approve', $item->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-confirm w-100 py-2" onclick="return confirm('Konfirmasi pelanggaran ini?')">
                                            <i class="bi bi-check-lg me-1"></i> Terima
                                        </button>
                                    </form>
                                </div>
                                <div class="col-6">
                                    <form action="{{ route('admin.pelanggaran.reject', $item->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-reject w-100 py-2" onclick="return confirm('Tolak laporan ini?')">
                                            <i class="bi bi-x-lg me-1"></i> Tolak
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <div class="bg-white rounded-4 p-5 shadow-sm d-inline-block">
                        <i class="bi bi-shield-check text-success display-1"></i>
                        <h5 class="mt-4 fw-bold">Semua Bersih!</h5>
                        <p class="text-muted mb-0">Tidak ada laporan pelanggaran yang perlu dikonfirmasi saat ini.</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Sidebar Toggle Logic
    document.getElementById('sidebarCollapse').addEventListener('click', function() {
        const sidebar = document.getElementById('sidebar');
        if(sidebar) {
            sidebar.classList.toggle('inactive');
        }
    });
</script>
</body>
</html>