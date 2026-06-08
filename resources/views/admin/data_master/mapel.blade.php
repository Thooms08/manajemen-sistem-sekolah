<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mata Pelajaran</title>
    @if(isset($sekolah->logo))
    <link rel="icon" type="image/png" href="{{ asset($sekolah->logo) }}">
    @else
    <link rel="icon" type="image/png" href="{{ asset('assets/img/default-favicon.png') }}">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary-green: #198754; }
        body { font-family: 'Inter', sans-serif; background-color: #f4f7f6; overflow-x: hidden; }
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        #content { width: 100%; padding: 20px 30px; transition: all 0.3s; min-height: 100vh; }
        #sidebarCollapse { width: 45px; height: 45px; background: var(--primary-green); border: none; color: white; border-radius: 10px; box-shadow: 0 4px 10px rgba(25,135,84,0.2); display: flex; align-items: center; justify-content: center; }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .table thead { background-color: var(--primary-green); color: white; }
        .table thead th { font-size: 0.82rem; letter-spacing: 0.4px; font-weight: 600; }
        #overlay { display: none; position: fixed; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 1040; top: 0; left: 0; }
        #overlay.active { display: block; }
        .search-box-wrapper { max-width: 400px; }
        input:focus, textarea:focus { border-color: #198754 !important; outline: none !important; box-shadow: 0 0 0 0.2rem rgba(25,135,84,0.25) !important; }
        .row-hidden { display: none; }
        @media (max-width: 768px) { #content { padding: 15px; } }
    </style>
</head>
<body>
<div id="overlay"></div>
<div class="wrapper">
    @include('admin.sidebar')

    <div id="content">
        <div class="container-fluid">

            {{-- Header --}}
            <div class="d-flex align-items-center justify-content-between mb-4 mt-2">
                <div class="d-flex align-items-center">
                    <button type="button" id="sidebarCollapse" class="btn"><i class="bi bi-list fs-4"></i></button>
                    <h4 class="ms-3 mb-0 fw-bold text-success">Data Mapel</h4>
                </div>
                <button class="btn btn-success px-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahMapel">
                    <i class="bi bi-journal-plus me-2"></i>Tambah Mapel
                </button>
            </div>

            {{-- Alert Messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4">
                    <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Gagal Menyimpan Data:</div>
                    <ul class="mb-0 small ps-3">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Search Bar --}}
            <div class="card p-3 mb-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <p class="text-muted small mb-0">Kelola data mata pelajaran (Mapel) sekolah dengan mudah.</p>
                    <div class="input-group search-box-wrapper">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="search-mapel" class="form-control border-start-0" placeholder="Cari Nama Mapel atau Deskripsi...">
                    </div>
                </div>
            </div>

            {{-- Tabel Data --}}
            <div class="card p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th width="45">No</th>
                                <th>Nama Mapel</th>
                                <th>Deskripsi</th>
                                <th width="110" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="table-body-mapel">
                            @forelse($mapels as $index => $m)
                            <tr class="{{ $index >= 10 ? 'row-extra row-hidden' : '' }}">
                                <td>{{ $index + 1 }}</td>
                                <td class="fw-bold">{{ $m->nama_mapel }}</td>
                                <td>{{ Str::limit($m->deskripsi, 80) }}</td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-outline-success border-0" 
                                            title="Edit"
                                            onclick="openEditModal('{{ $m->id }}', '{{ addslashes($m->nama_mapel) }}', '{{ addslashes($m->deskripsi) }}')">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <form action="{{ route('mapel.destroy', $m->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mapel {{ addslashes($m->nama_mapel) }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger border-0" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="bi bi-journal-x fs-3 d-block mb-2 text-secondary"></i>
                                    Belum ada data mata pelajaran.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Tombol Lihat Semua --}}
                @if($mapels->count() > 10)
                <div class="text-center mt-2" id="btn-lihat-semua-wrapper">
                    <button class="btn btn-outline-success btn-sm px-4" id="btn-lihat-semua" onclick="lihatSemuaMapel()">
                        <i class="bi bi-chevron-down me-1"></i>Lihat Semua Data 
                        ({{ $mapels->count() - 10 }} data lainnya)
                    </button>
                </div>
                @endif
            </div>

        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════
     MODAL: TAMBAH MAPEL
══════════════════════════════════════════════ --}}
<div class="modal fade" id="modalTambahMapel" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('mapel.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-journal-plus me-2"></i>Tambah Mapel Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Nama Mapel <span class="text-danger">*</span></label>
                        <input type="text" name="nama_mapel" class="form-control" placeholder="Cth: Matematika" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3" placeholder="Deskripsi opsional..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4 shadow-sm">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════
     MODAL: EDIT MAPEL
══════════════════════════════════════════════ --}}
<div class="modal fade" id="modalEditMapel" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="formEditMapel" method="POST">
                @csrf @method('PUT')
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Mapel</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Nama Mapel <span class="text-danger">*</span></label>
                        <input type="text" name="nama_mapel" id="edit_nama_mapel" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Deskripsi</label>
                        <textarea name="deskripsi" id="edit_deskripsi" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4 shadow-sm">Perbarui Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function () {
    // ── Sidebar Toggle ─────────────────────────────────
    function toggleSidebar() {
        if ($(window).width() <= 768) {
            $('#sidebar').toggleClass('show-mobile');
            $('#overlay').toggleClass('active');
        } else {
            $('#sidebar').toggleClass('inactive');
        }
    }
    $('#sidebarCollapse, #close-sidebar, #overlay').on('click', toggleSidebar);

    // ── AJAX Search ────────────────────────────────────
    $('#search-mapel').on('keyup', function () {
        let keyword = $(this).val();
        
        $.ajax({
            type: 'GET',
            url: "{{ route('mapel.search') }}",
            data: { search: keyword },
            success: function (data) {
                $('#table-body-mapel').html(data);
                
                // Menyembunyikan tombol 'Lihat Semua' jika sedang melakukan pencarian
                if (keyword.length > 0) {
                    $('#btn-lihat-semua-wrapper').hide();
                } else {
                    $('#btn-lihat-semua-wrapper').show();
                    
                    // Kembalikan limit 10 jika pencarian kosong
                    $('#table-body-mapel tr').each(function(index) {
                        if(index >= 10) $(this).addClass('row-hidden');
                    });
                }
            }
        });
    });
});

// ── Lihat Semua Data ───────────────────────────────
function lihatSemuaMapel() {
    $('.row-extra').removeClass('row-hidden');
    $('#btn-lihat-semua-wrapper').hide();
}

// ── Edit Modal ─────────────────────────────────────
const editModalMapel = new bootstrap.Modal(document.getElementById('modalEditMapel'));
function openEditModal(id, nama_mapel, deskripsi) {
    document.getElementById('formEditMapel').action = `/mapel/${id}`;
    document.getElementById('edit_nama_mapel').value = nama_mapel;
    document.getElementById('edit_deskripsi').value = deskripsi;
    editModalMapel.show();
}
</script>
</body>
</html>