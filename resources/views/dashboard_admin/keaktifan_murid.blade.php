<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keaktifan Murid</title>
    @if(isset($sekolah->logo))
    <link rel="icon" type="image/png" href="{{ asset($sekolah->logo) }}">
    @else
    <link rel="icon" type="image/png" href="{{ asset('assets/img/default-favicon.png') }}">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --admin-green: #198754; --soft-green: #f0fdf4; --dark-green: #142d2d; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f4f7f6; }
        .wrapper { display: flex; }
        #content { width: 100%; padding: 30px; transition: all 0.3s; }
        .card { border: none; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.03); }
        .btn-filter { background-color: var(--admin-green); color: white; border-radius: 10px; font-weight: 600; }
        .btn-filter:hover { background-color: var(--dark-green); color: white; }
        .table thead { background-color: var(--admin-green); color: white; }
        .status-icon { font-size: 1.3rem; }
        .gallery-icon { cursor: pointer; color: var(--admin-green); font-size: 1.2rem; transition: 0.2s; }
        .gallery-icon:hover { transform: scale(1.2); color: var(--dark-green); }
        #sidebarCollapse { width: 45px; height: 45px; background: var(--admin-green); border: none; color: white; border-radius: 12px; }
        .modal-content { border-radius: 20px; border: none; }
    </style>
</head>
<body>

<div class="wrapper">
    @include('dashboard_admin.sidebar_admin')

    <div id="content">
        <div class="container-fluid">
            <div class="d-flex align-items-center mb-4">
                <button type="button" id="sidebarCollapse" class="btn"><i class="bi bi-list fs-4"></i></button>
                <div class="ms-3">
                    <h4 class="mb-0 fw-bold text-dark">Laporan Keaktifan Murid</h4>
                    <p class="text-muted small mb-0">Pantau progres keaktifan harian seluruh murid</p>
                </div>
            </div>

            <div class="card p-4 mb-4">
                <form action="{{ route('admin.keaktifan.index') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Pilih Kelas</label>
                        <select name="id_kelas" class="form-select border-success shadow-none" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}" {{ $kelasId == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Pilih Tanggal</label>
                        <input type="date" name="tanggal" class="form-control border-success shadow-none" value="{{ $tanggal }}" required>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-filter w-100 py-2">
                            <i class="bi bi-search me-2"></i>Tampilkan Laporan
                        </button>
                    </div>
                </form>
            </div>

            <div class="card p-0 overflow-hidden">
                <div class="card-header bg-white p-4 border-0">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-table me-2 text-success"></i>Data Keaktifan</h5>
                </div>
                <div class="table-responsive px-4 pb-4">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th class="py-3">Nama Murid</th>
                                <th class="py-3">Nama Keaktifan</th>
                                <th class="py-3 text-center">Status</th>
                                <th class="py-3">Tanggal</th>
                                <th class="py-3 text-center">Dokumentasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($kelasId)
                                @forelse($dataKeaktifan as $data)
                                <tr>
                                    <td class="fw-semibold">{{ $data->nama_lengkap }}</td>
                                    <td>{{ $data->nama_keaktifan }}</td>
                                    <td class="text-center">
                                        @if($data->is_active)
                                            <i class="bi bi-check-circle-fill text-success status-icon" title="Aktif"></i>
                                        @else
                                            <i class="bi bi-x-circle-fill text-danger status-icon" title="Tidak Aktif"></i>
                                        @endif
                                    </td>
                                    <td>{{ date('d/m/Y', strtotime($data->tanggal)) }}</td>
                                    <td class="text-center">
                                        @if($data->foto)
                                            <i class="bi bi-image gallery-icon" 
                                            onclick="showPhoto('{{ asset($data->foto) }}', '{{ $data->nama_keaktifan }}')"></i>
                                        @else
                                            <span class="text-muted small italic">No Photo</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">Tidak ada data keaktifan untuk kelas dan tanggal ini.</td>
                                </tr>
                                @endforelse
                            @else
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="bi bi-arrow-up-circle fs-1 d-block mb-2"></i>
                                        Silakan pilih kelas dan tanggal terlebih dahulu.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalPhoto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold" id="modalTitle">Dokumentasi Keaktifan</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <img src="" id="targetPhoto" class="img-fluid rounded-4 shadow-sm" alt="Foto Keaktifan">
            </div>
        </div>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Sidebar Hamburger Menu Logic
    document.getElementById('sidebarCollapse')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('inactive');
    });

    // Modal Photo Logic
    function showPhoto(url, title) {
        document.getElementById('targetPhoto').src = url;
        document.getElementById('modalTitle').innerText = 'Foto: ' + title;
        const modal = new bootstrap.Modal(document.getElementById('modalPhoto'));
        modal.show();
    }
</script>
</body>
</html>