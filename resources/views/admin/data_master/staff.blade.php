<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Staff</title>
    @if(isset($sekolah->logo))
    <link rel="icon" type="image/png" href="{{ \App\Helpers\ImageHelper::url($sekolah->logo) }}">
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
        #content { width: 100%; padding: 20px 30px; transition: all 0.3s; min-height: 100vh; min-width: 0; }
        #sidebarCollapse { width: 45px; height: 45px; background: var(--primary-green); border: none; color: white; border-radius: 10px; box-shadow: 0 4px 10px rgba(25,135,84,0.2); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .table thead { background-color: var(--primary-green); color: white; }
        .table thead th { font-size: 0.82rem; letter-spacing: 0.4px; font-weight: 600; white-space: nowrap; }
        #overlay { display: none; position: fixed; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 1040; top: 0; left: 0; }
        #overlay.active { display: block; }
        .search-box-wrapper { min-width: 200px; flex: 1 1 220px; max-width: 420px; }
        input:focus, textarea:focus, select:focus { border-color: #198754 !important; outline: none !important; box-shadow: 0 0 0 0.2rem rgba(25,135,84,0.25) !important; }
        .nav-tabs .nav-link { color: #6c757d; font-weight: 500; border-radius: 8px 8px 0 0; font-size: 0.9rem; }
        .nav-tabs .nav-link.active { color: #198754; border-bottom-color: #fff; font-weight: 600; }
        .nav-tabs .nav-link:hover { color: #198754; }
        .row-hidden { display: none; }

        /* Mobile card list */
        .staff-card-mobile { display: none; }
        .staff-card-item {
            background: #fff; border-radius: 12px; padding: 14px 16px;
            margin-bottom: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border-left: 4px solid #198754;
        }
        .staff-card-item.nonaktif { border-left-color: #dc3545; }
        .staff-card-item .sc-name  { font-weight: 700; font-size: 0.97rem; color: #1a3a3a; }
        .staff-card-item .sc-meta  { font-size: 0.8rem; color: #6c757d; margin-top: 3px; }
        .staff-card-item .sc-actions { display: flex; gap: 6px; margin-top: 10px; }
        .staff-card-item .sc-actions .btn { flex: 1; font-size: 0.8rem; }

        /* ── Responsive ── */
        @media (max-width: 991px) {
            #content { padding: 16px 18px; }
            .table thead th, .table tbody td { font-size: 0.8rem; }
        }
        @media (max-width: 767px) {
            #content { padding: 12px 12px; }
            /* Header stack vertikal */
            .page-header { flex-direction: column; align-items: flex-start !important; gap: 10px; }
            .page-header .btn-tambah { width: 100%; }
            /* Search bar full width */
            .search-bar-wrapper { flex-direction: column; align-items: stretch !important; }
            .search-box-wrapper { max-width: 100%; }
            /* Sembunyikan tabel, tampilkan card */
            .table-staff-desktop { display: none !important; }
            .staff-card-mobile   { display: block; }
            /* Tab font lebih kecil */
            .nav-tabs .nav-link { font-size: 0.82rem; padding: 6px 10px; }
        }
    </style>
</head>
<body>
<div id="overlay"></div>
<div class="wrapper">
    @include('admin.sidebar')

    <div id="content">
        <div class="container-fluid">

            {{-- Header --}}
            <div class="d-flex align-items-center justify-content-between mb-4 mt-2 flex-wrap gap-2 page-header">
                <div class="d-flex align-items-center">
                    <button type="button" id="sidebarCollapse" class="btn"><i class="bi bi-list fs-4"></i></button>
                    <h4 class="ms-3 mb-0 fw-bold text-success">Data Staff</h4>
                </div>
                <button class="btn btn-success px-4 shadow-sm fw-bold btn-tambah" data-bs-toggle="modal" data-bs-target="#modalTambahStaff">
                    <i class="bi bi-person-plus me-2"></i>Tambah Staff
                </button>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Search Bar --}}
            <div class="card p-3 mb-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 search-bar-wrapper">
                    <p class="text-muted small mb-0">Kelola data administrasi dan tenaga pendukung secara efisien.</p>
                    <div class="input-group search-box-wrapper">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="search-staff" class="form-control border-start-0" placeholder="Cari Nama, Jabatan, atau Email...">
                    </div>
                </div>
            </div>

            {{-- Tab + Tabel --}}
            <div class="card p-4">

                <ul class="nav nav-tabs mb-3" id="staffTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-aktif-btn" data-bs-toggle="tab"
                                data-bs-target="#tab-aktif" type="button" role="tab" data-tab="aktif">
                            <i class="bi bi-person-check me-1"></i>Aktif
                            <span class="badge bg-success ms-1">{{ $staffsAktif->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-nonaktif-btn" data-bs-toggle="tab"
                                data-bs-target="#tab-nonaktif" type="button" role="tab" data-tab="nonaktif">
                            <i class="bi bi-person-dash me-1"></i>Nonaktif
                            <span class="badge bg-danger ms-1">{{ $staffsNonaktif->count() }}</span>
                        </button>
                    </li>
                </ul>

                <div class="tab-content">

                    {{-- ══ TAB AKTIF ══ --}}
                    <div class="tab-pane fade show active" id="tab-aktif" role="tabpanel">
                        {{-- DESKTOP TABLE --}}
                        <div class="table-responsive table-staff-desktop">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th width="45">No</th>
                                        <th>Nama Staff</th>
                                        <th>Jabatan</th>
                                        <th>Email</th>
                                        <th>WhatsApp</th>
                                        <th>Alamat</th>
                                        <th width="110" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="table-body-aktif">
                                    @forelse($staffsAktif as $index => $s)
                                    <tr class="{{ $index >= 10 ? 'row-extra-aktif row-hidden' : '' }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td class="fw-bold">{{ $s->nama_staff }}</td>
                                        <td>{{ $s->jabatan }}</td>
                                        <td>{{ $s->email }}</td>
                                        <td>{{ $s->no_wa }}</td>
                                        <td>{{ Str::limit($s->alamat, 40) }}</td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-outline-success border-0"
                                                    title="Edit"
                                                    onclick="openEditModal('{{ $s->id }}', '{{ addslashes($s->nama_staff) }}', '{{ addslashes($s->jabatan) }}', '{{ addslashes($s->email) }}', '{{ addslashes($s->no_wa) }}', '{{ addslashes($s->alamat) }}')">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger border-0"
                                                    title="Nonaktifkan"
                                                    onclick="bukaModalNonaktif({{ $s->id }}, '{{ addslashes($s->nama_staff) }}')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="bi bi-people fs-3 d-block mb-2 text-secondary"></i>
                                            Belum ada data staff aktif.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- MOBILE CARD LIST --}}
                        <div class="staff-card-mobile" id="mobile-list-aktif">
                            @forelse($staffsAktif as $index => $s)
                            <div class="staff-card-item {{ $index >= 10 ? 'row-extra-aktif row-hidden' : '' }}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="sc-name">{{ $s->nama_staff }}</div>
                                    <span class="badge bg-success">Aktif</span>
                                </div>
                                <div class="sc-meta"><i class="bi bi-briefcase me-1"></i>{{ $s->jabatan }}</div>
                                <div class="sc-meta"><i class="bi bi-envelope me-1"></i>{{ $s->email }}</div>
                                <div class="sc-meta"><i class="bi bi-whatsapp me-1"></i>{{ $s->no_wa }}</div>
                                <div class="sc-actions">
                                    <button class="btn btn-outline-success btn-sm"
                                        onclick="openEditModal('{{ $s->id }}', '{{ addslashes($s->nama_staff) }}', '{{ addslashes($s->jabatan) }}', '{{ addslashes($s->email) }}', '{{ addslashes($s->no_wa) }}', '{{ addslashes($s->alamat) }}')">
                                        <i class="bi bi-pencil-square me-1"></i>Edit
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm"
                                        onclick="bukaModalNonaktif({{ $s->id }}, '{{ addslashes($s->nama_staff) }}')">
                                        <i class="bi bi-person-dash me-1"></i>Nonaktifkan
                                    </button>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-people fs-3 d-block mb-2 text-secondary"></i>
                                Belum ada data staff aktif.
                            </div>
                            @endforelse
                        </div>

                        {{-- Tombol Lihat Semua --}}
                        @if($staffsAktif->count() > 10)
                        <div class="text-center mt-2" id="btn-lihat-semua-wrapper">
                            <button class="btn btn-outline-success btn-sm px-4" onclick="lihatSemuaStaff()">
                                <i class="bi bi-chevron-down me-1"></i>Lihat Semua Data
                                ({{ $staffsAktif->count() - 10 }} data lainnya)
                            </button>
                        </div>
                        @endif
                    </div>

                    {{-- ══ TAB NONAKTIF ══ --}}
                    <div class="tab-pane fade" id="tab-nonaktif" role="tabpanel">
                        {{-- DESKTOP TABLE --}}
                        <div class="table-responsive table-staff-desktop">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th width="45">No</th>
                                        <th>Nama Staff</th>
                                        <th>Jabatan</th>
                                        <th>Email</th>
                                        <th>WhatsApp</th>
                                        <th>Alasan Nonaktif</th>
                                        <th>Tgl Nonaktif</th>
                                        <th width="110" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="table-body-nonaktif">
                                    @forelse($staffsNonaktif as $index => $s)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td class="fw-bold">{{ $s->nama_staff }}</td>
                                        <td>{{ $s->jabatan }}</td>
                                        <td>{{ $s->email }}</td>
                                        <td>{{ $s->no_wa }}</td>
                                        <td>
                                            <span class="badge bg-danger bg-opacity-10 text-danger px-2">
                                                {{ $s->alasan_nonaktif ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="text-muted small">
                                            {{ $s->tanggal_nonaktif ? \Carbon\Carbon::parse($s->tanggal_nonaktif)->format('d M Y') : '-' }}
                                        </td>
                                        <td class="text-center">
                                            @if($s->surat_keterangan)
                                            <a href="{{ route('staff.download-surat', $s->id) }}"
                                               class="btn btn-sm btn-outline-secondary border-0"
                                               title="Download Surat Keterangan">
                                                <i class="bi bi-file-earmark-arrow-down"></i>
                                            </a>
                                            @endif
                                            <form action="{{ route('staff.restore', $s->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button class="btn btn-sm btn-outline-success border-0"
                                                        title="Pulihkan ke Aktif"
                                                        onclick="return confirm('Pulihkan staff ini ke data aktif?')">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted">
                                            <i class="bi bi-person-x fs-3 d-block mb-2 text-secondary"></i>
                                            Tidak ada data staff nonaktif.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- MOBILE CARD LIST --}}
                        <div class="staff-card-mobile" id="mobile-list-nonaktif">
                            @forelse($staffsNonaktif as $index => $s)
                            <div class="staff-card-item nonaktif">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="sc-name">{{ $s->nama_staff }}</div>
                                    <span class="badge bg-danger">Nonaktif</span>
                                </div>
                                <div class="sc-meta"><i class="bi bi-briefcase me-1"></i>{{ $s->jabatan }}</div>
                                <div class="sc-meta"><i class="bi bi-envelope me-1"></i>{{ $s->email }}</div>
                                <div class="sc-meta"><i class="bi bi-whatsapp me-1"></i>{{ $s->no_wa }}</div>
                                <div class="sc-meta mt-1">
                                    <i class="bi bi-info-circle me-1 text-danger"></i>
                                    <span class="badge bg-danger bg-opacity-10 text-danger">{{ $s->alasan_nonaktif ?? '-' }}</span>
                                    @if($s->tanggal_nonaktif)
                                        <span class="ms-1">· {{ \Carbon\Carbon::parse($s->tanggal_nonaktif)->format('d M Y') }}</span>
                                    @endif
                                </div>
                                <div class="sc-actions">
                                    @if($s->surat_keterangan)
                                    <a href="{{ route('staff.download-surat', $s->id) }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-file-earmark-arrow-down me-1"></i>Surat
                                    </a>
                                    @endif
                                    <form action="{{ route('staff.restore', $s->id) }}" method="POST" class="flex-fill">
                                        @csrf
                                        <button class="btn btn-outline-success btn-sm w-100"
                                                onclick="return confirm('Pulihkan staff ini ke data aktif?')">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i>Pulihkan
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-person-x fs-3 d-block mb-2 text-secondary"></i>
                                Tidak ada data staff nonaktif.
                            </div>
                            @endforelse
                        </div>
                    </div>

                </div>{{-- end tab-content --}}
            </div>{{-- end card --}}

        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════
     MODAL: TAMBAH STAFF
══════════════════════════════════════════════ --}}
<div class="modal fade" id="modalTambahStaff" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('staff.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-plus me-2"></i>Tambah Staff Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Nama Staff <span class="text-danger">*</span></label>
                        <input type="text" name="nama_staff" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Jabatan <span class="text-danger">*</span></label>
                        <input type="text" name="jabatan" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Nomor WhatsApp <span class="text-danger">*</span></label>
                        <input type="text" name="no_wa" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Alamat <span class="text-danger">*</span></label>
                        <textarea name="alamat" class="form-control" rows="3" required></textarea>
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
     MODAL: EDIT STAFF
══════════════════════════════════════════════ --}}
<div class="modal fade" id="modalEditStaff" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="formEditStaff" method="POST">
                @csrf @method('PUT')
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Data Staff</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Nama Staff <span class="text-danger">*</span></label>
                        <input type="text" name="nama_staff" id="edit_nama" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Jabatan <span class="text-danger">*</span></label>
                        <input type="text" name="jabatan" id="edit_jabatan" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="edit_email" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Nomor WhatsApp <span class="text-danger">*</span></label>
                        <input type="text" name="no_wa" id="edit_wa" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Alamat <span class="text-danger">*</span></label>
                        <textarea name="alamat" id="edit_alamat" class="form-control" rows="3" required></textarea>
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

{{-- ══════════════════════════════════════════════
     MODAL: NONAKTIFKAN STAFF
══════════════════════════════════════════════ --}}
<div class="modal fade" id="modalNonaktifStaff" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="formNonaktifStaff" method="POST" enctype="multipart/form-data">
                @csrf @method('DELETE')
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-dash me-2"></i>Nonaktifkan Staff</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted mb-3">
                        Staff <strong id="namaStaffNonaktif"></strong> akan dipindahkan ke data nonaktif.
                        Tindakan ini dapat dipulihkan kembali.
                    </p>

                    {{-- Alasan Nonaktif --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alasan Nonaktif <span class="text-danger">*</span></label>
                        <select name="alasan_nonaktif" id="selectAlasanStaff" class="form-select" required onchange="toggleAlasanLainStaff(this)">
                            <option value="">-- Pilih Alasan --</option>
                            <option value="Diberhentikan">Diberhentikan</option>
                            <option value="Pindah Tugas">Pindah Tugas</option>
                            <option value="Mengundurkan Diri">Mengundurkan Diri</option>
                            <option value="Pensiun">Pensiun</option>
                            <option value="Kontrak Berakhir">Kontrak Berakhir</option>
                            <option value="Lainnya">Lainnya...</option>
                        </select>
                    </div>

                    {{-- Input alasan custom --}}
                    <div class="mb-3 d-none" id="inputAlasanLainStaff">
                        <label class="form-label fw-semibold">Tulis Alasan <span class="text-danger">*</span></label>
                        <input type="text" id="alasanLainStaff" class="form-control"
                               placeholder="Contoh: Mengikuti pasangan pindah kota...">
                    </div>

                    {{-- Upload surat keterangan (opsional) --}}
                    <div class="mb-1">
                        <label class="form-label fw-semibold">
                            Surat Keterangan
                            <span class="text-muted fw-normal small">(Opsional)</span>
                        </label>
                        <input type="file" name="surat_keterangan" class="form-control"
                               accept=".pdf,.jpg,.jpeg,.png">
                        <div class="form-text">Format: PDF, JPG, PNG. Maks 5MB.</div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger px-4 shadow-sm">
                        <i class="bi bi-person-dash me-1"></i>Nonaktifkan
                    </button>
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

    // ── Tab tracking ───────────────────────────────────
    let currentTab = 'aktif';
    $('[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
        currentTab = $(this).data('tab');
        doSearch($('#search-staff').val());
    });

    // ── AJAX Search ────────────────────────────────────
    function doSearch(keyword) {
        const targetBody = currentTab === 'nonaktif' ? '#table-body-nonaktif' : '#table-body-aktif';
        $.ajax({
            type: 'GET',
            url: "{{ route('staff.search') }}",
            data: { search: keyword, tab: currentTab },
            success: function (data) {
                $(targetBody).html(data);
                if (keyword.length > 0) {
                    $('#btn-lihat-semua-wrapper').hide();
                } else {
                    $('#btn-lihat-semua-wrapper').show();
                }
            }
        });
    }

    $('#search-staff').on('keyup', function () {
        doSearch($(this).val());
    });
});

// ── Lihat Semua Data (tab aktif) ───────────────────
function lihatSemuaStaff() {
    $('.row-extra-aktif').removeClass('row-hidden');
    $('#btn-lihat-semua-wrapper').hide();
}

// ── Edit Modal ─────────────────────────────────────
const editModal = new bootstrap.Modal(document.getElementById('modalEditStaff'));
function openEditModal(id, nama, jabatan, email, wa, alamat) {
    document.getElementById('formEditStaff').action = `/staff/${id}`;
    document.getElementById('edit_nama').value    = nama;
    document.getElementById('edit_jabatan').value = jabatan;
    document.getElementById('edit_email').value   = email;
    document.getElementById('edit_wa').value      = wa;
    document.getElementById('edit_alamat').value  = alamat;
    editModal.show();
}

// ── Modal Nonaktifkan Staff ────────────────────────
const nonaktifModal = new bootstrap.Modal(document.getElementById('modalNonaktifStaff'));
function bukaModalNonaktif(id, nama) {
    document.getElementById('namaStaffNonaktif').textContent = nama;
    document.getElementById('formNonaktifStaff').action      = `/staff/${id}`;
    // Reset form
    document.getElementById('selectAlasanStaff').value = '';
    document.getElementById('inputAlasanLainStaff').classList.add('d-none');
    document.getElementById('alasanLainStaff').value = '';
    nonaktifModal.show();
}

function toggleAlasanLainStaff(select) {
    const inputDiv  = document.getElementById('inputAlasanLainStaff');
    const inputTeks = document.getElementById('alasanLainStaff');
    if (select.value === 'Lainnya') {
        inputDiv.classList.remove('d-none');
        inputTeks.required = true;
    } else {
        inputDiv.classList.add('d-none');
        inputTeks.required = false;
    }
}

// Inject alasan custom sebelum submit
document.getElementById('formNonaktifStaff').addEventListener('submit', function (e) {
    const select     = document.getElementById('selectAlasanStaff');
    const alasanLain = document.getElementById('alasanLainStaff').value.trim();

    if (select.value === 'Lainnya') {
        if (!alasanLain) {
            e.preventDefault();
            alert('Harap isi alasan nonaktif terlebih dahulu.');
            return;
        }
        const hidden   = document.createElement('input');
        hidden.type    = 'hidden';
        hidden.name    = 'alasan_nonaktif';
        hidden.value   = alasanLain;
        this.appendChild(hidden);
        select.removeAttribute('name');
    }
});
</script>
</body>
</html>
