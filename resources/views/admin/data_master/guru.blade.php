<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Guru</title>
    @if(isset($sekolah->logo))
    <link rel="icon" type="image/png" href="{{ \App\Helpers\ImageHelper::url($sekolah->logo) }}">
    @else
    <link rel="icon" type="image/png" href="{{ asset('assets/img/default-favicon.png') }}">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
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
        .search-box-wrapper { min-width: 200px; flex: 1 1 250px; max-width: 420px; }
        input:focus, textarea:focus, select:focus { border-color: #198754 !important; outline: none !important; box-shadow: 0 0 0 0.2rem rgba(25,135,84,0.25) !important; }
        .nav-tabs .nav-link { color: #6c757d; font-weight: 500; border-radius: 8px 8px 0 0; font-size: 0.9rem; }
        .nav-tabs .nav-link.active { color: #198754; border-bottom-color: #fff; font-weight: 600; }
        .nav-tabs .nav-link:hover { color: #198754; }
        .row-hidden { display: none; }
        /* Tabel responsif — kolom alamat & WhatsApp disembunyikan di mobile */
        .col-hide-sm { }
        /* Card list mobile */
        .guru-card-mobile { display: none; }

        /* ── Responsive ── */
        @media (max-width: 991px) {
            #content { padding: 16px 18px; }
            .table thead th, .table tbody td { font-size: 0.8rem; }
        }
        @media (max-width: 767px) {
            #content { padding: 12px 12px; }
            /* Sembunyikan tabel besar di mobile, tampilkan card */
            .table-guru-desktop { display: none !important; }
            .guru-card-mobile { display: block; }
            /* Header responsif */
            .page-header { flex-direction: column; align-items: flex-start !important; gap: 10px; }
            .page-header .btn-tambah { width: 100%; }
            /* Searchbar full width mobile */
            .search-bar-wrapper { flex-direction: column; align-items: stretch !important; }
            .search-box-wrapper { max-width: 100%; }
            /* Tab font lebih kecil */
            .nav-tabs .nav-link { font-size: 0.82rem; padding: 6px 10px; }
        }

        /* Guru card (mobile list view) */
        .guru-card-item {
            background: #fff; border-radius: 12px; padding: 14px 16px;
            margin-bottom: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border-left: 4px solid #198754;
        }
        .guru-card-item .guru-name { font-weight: 700; font-size: 0.97rem; color: #1a3a3a; }
        .guru-card-item .guru-meta { font-size: 0.8rem; color: #6c757d; margin-top: 3px; }
        .guru-card-item .badge-wrap { display: flex; flex-wrap: wrap; gap: 4px; margin-top: 6px; }
        .guru-card-item .card-actions { display: flex; gap: 6px; margin-top: 10px; }
        .guru-card-item .card-actions .btn { flex: 1; font-size: 0.8rem; padding: 6px 4px; }
        .guru-card-nonaktif { border-left-color: #dc3545; }
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
                    <h4 class="ms-3 mb-0 fw-bold text-success">Data Guru</h4>
                </div>
                <button class="btn btn-success px-4 shadow-sm fw-bold btn-tambah" data-bs-toggle="modal" data-bs-target="#modalTambahGuru">
                    <i class="bi bi-person-plus me-2"></i>Tambah Guru
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
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 search-bar-wrapper">
                    <p class="text-muted small mb-0">Kelola data tenaga pendidik secara efisien.</p>
                    <div class="input-group search-box-wrapper">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="search-guru" class="form-control border-start-0" placeholder="Cari Nama, Mapel, Email...">
                    </div>
                </div>
            </div>

            {{-- Tab + Tabel --}}
            <div class="card p-4">

                <ul class="nav nav-tabs mb-3" id="guruTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-aktif-btn" data-bs-toggle="tab"
                                data-bs-target="#tab-aktif" type="button" role="tab" data-tab="aktif">
                            <i class="bi bi-person-check me-1"></i>Aktif
                            <span class="badge bg-success ms-1">{{ $gurusAktif->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-nonaktif-btn" data-bs-toggle="tab"
                                data-bs-target="#tab-nonaktif" type="button" role="tab" data-tab="nonaktif">
                            <i class="bi bi-person-dash me-1"></i>Nonaktif
                            <span class="badge bg-danger ms-1">{{ $gurusNonaktif->count() }}</span>
                        </button>
                    </li>
                </ul>

                <div class="tab-content">

                    {{-- ══ TAB AKTIF ══ --}}
                    <div class="tab-pane fade show active" id="tab-aktif" role="tabpanel">
                        {{-- DESKTOP TABLE --}}
                        <div class="table-responsive table-guru-desktop">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th width="45">No</th>
                                        <th>Nama Guru</th>
                                        <th>Mapel</th>
                                        <th>Kelas</th>
                                        <th>Email</th>
                                        <th>WhatsApp</th>
                                        <th>Alamat</th>
                                        <th width="110" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="table-body-aktif">
                                    @forelse($gurusAktif as $index => $g)
                                    <tr class="{{ $index >= 10 ? 'row-extra-aktif row-hidden' : '' }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td class="fw-bold">{{ $g->nama_guru }}</td>
                                        <td width="20%">
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($g->pengajars->pluck('mapel.nama_mapel')->filter()->unique() as $m)
                                                    <span class="badge bg-secondary">{{ $m }}</span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td width="20%">
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($g->pengajars->pluck('kelas.nama_kelas')->filter()->unique() as $k)
                                                    <span class="badge bg-info text-dark">{{ $k }}</span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>{{ $g->email }}</td>
                                        <td>{{ $g->no_whatsapp }}</td>
                                        <td>{{ Str::limit($g->alamat, 40) }}</td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-outline-success border-0"
                                                    title="Edit"
                                                    onclick='openEditModal("{{ $g->id }}", "{{ addslashes($g->nama_guru) }}", {{ $g->pengajars->map(function($p) { return ["id_mapel"=>$p->id_mapel, "id_kelas"=>$p->id_kelas]; })->toJson() }}, "{{ addslashes($g->email) }}", "{{ addslashes($g->no_whatsapp) }}", "{{ addslashes($g->alamat) }}")'>
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger border-0"
                                                    title="Nonaktifkan"
                                                    onclick="bukaModalNonaktif({{ $g->id }}, '{{ addslashes($g->nama_guru) }}')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="bi bi-person-badge fs-3 d-block mb-2 text-secondary"></i>
                                            Belum ada data guru aktif.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- MOBILE CARD LIST --}}
                        <div class="guru-card-mobile" id="mobile-list-aktif">
                            @forelse($gurusAktif as $index => $g)
                            <div class="guru-card-item {{ $index >= 10 ? 'row-extra-aktif row-hidden' : '' }}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="guru-name">{{ $g->nama_guru }}</div>
                                    <span class="badge bg-success">Aktif</span>
                                </div>
                                <div class="guru-meta"><i class="bi bi-envelope me-1"></i>{{ $g->email }}</div>
                                <div class="guru-meta"><i class="bi bi-whatsapp me-1"></i>{{ $g->no_whatsapp }}</div>
                                <div class="badge-wrap mt-1">
                                    @foreach($g->pengajars->pluck('mapel.nama_mapel')->filter()->unique() as $m)
                                        <span class="badge bg-secondary">{{ $m }}</span>
                                    @endforeach
                                    @foreach($g->pengajars->pluck('kelas.nama_kelas')->filter()->unique() as $k)
                                        <span class="badge bg-info text-dark">{{ $k }}</span>
                                    @endforeach
                                </div>
                                <div class="card-actions">
                                    <button class="btn btn-outline-success btn-sm"
                                        onclick='openEditModal("{{ $g->id }}", "{{ addslashes($g->nama_guru) }}", {{ $g->pengajars->map(function($p) { return ["id_mapel"=>$p->id_mapel, "id_kelas"=>$p->id_kelas]; })->toJson() }}, "{{ addslashes($g->email) }}", "{{ addslashes($g->no_whatsapp) }}", "{{ addslashes($g->alamat) }}")'>
                                        <i class="bi bi-pencil-square me-1"></i>Edit
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm"
                                        onclick="bukaModalNonaktif({{ $g->id }}, '{{ addslashes($g->nama_guru) }}')">
                                        <i class="bi bi-person-dash me-1"></i>Nonaktifkan
                                    </button>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-person-badge fs-3 d-block mb-2 text-secondary"></i>
                                Belum ada data guru aktif.
                            </div>
                            @endforelse
                        </div>

                        {{-- Tombol Lihat Semua --}}
                        @if($gurusAktif->count() > 10)
                        <div class="text-center mt-2" id="btn-lihat-semua-wrapper">
                            <button class="btn btn-outline-success btn-sm px-4" id="btn-lihat-semua" onclick="lihatSemuaGuru()">
                                <i class="bi bi-chevron-down me-1"></i>Lihat Semua Data
                                ({{ $gurusAktif->count() - 10 }} data lainnya)
                            </button>
                        </div>
                        @endif
                    </div>

                    {{-- ══ TAB NONAKTIF ══ --}}
                    <div class="tab-pane fade" id="tab-nonaktif" role="tabpanel">
                        {{-- DESKTOP TABLE --}}
                        <div class="table-responsive table-guru-desktop">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th width="45">No</th>
                                        <th>Nama Guru</th>
                                        <th>Mapel</th>
                                        <th>Kelas</th>
                                        <th>Email</th>
                                        <th>WhatsApp</th>
                                        <th>Alasan Nonaktif</th>
                                        <th>Tgl Nonaktif</th>
                                        <th width="110" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="table-body-nonaktif">
                                    @forelse($gurusNonaktif as $index => $g)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td class="fw-bold">{{ $g->nama_guru }}</td>
                                        <td width="20%">
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($g->pengajars->pluck('mapel.nama_mapel')->filter()->unique() as $m)
                                                    <span class="badge bg-secondary">{{ $m }}</span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td width="20%">
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($g->pengajars->pluck('kelas.nama_kelas')->filter()->unique() as $k)
                                                    <span class="badge bg-info text-dark">{{ $k }}</span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>{{ $g->email }}</td>
                                        <td>{{ $g->no_whatsapp }}</td>
                                        <td>
                                            <span class="badge bg-danger bg-opacity-10 text-danger px-2">
                                                {{ $g->alasan_nonaktif ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="text-muted small">
                                            {{ $g->tanggal_nonaktif ? \Carbon\Carbon::parse($g->tanggal_nonaktif)->format('d M Y') : '-' }}
                                        </td>
                                        <td class="text-center">
                                            @if($g->surat_keterangan)
                                            <a href="{{ route('guru.download-surat', $g->id) }}"
                                               class="btn btn-sm btn-outline-secondary border-0"
                                               title="Download Surat Keterangan">
                                                <i class="bi bi-file-earmark-arrow-down"></i>
                                            </a>
                                            @endif
                                            <form action="{{ route('guru.restore', $g->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button class="btn btn-sm btn-outline-success border-0"
                                                        title="Pulihkan ke Aktif"
                                                        onclick="return confirm('Pulihkan guru ini ke data aktif?')">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted">
                                            <i class="bi bi-person-x fs-3 d-block mb-2 text-secondary"></i>
                                            Tidak ada data guru nonaktif.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- MOBILE CARD LIST --}}
                        <div class="guru-card-mobile" id="mobile-list-nonaktif">
                            @forelse($gurusNonaktif as $index => $g)
                            <div class="guru-card-item guru-card-nonaktif">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="guru-name">{{ $g->nama_guru }}</div>
                                    <span class="badge bg-danger">Nonaktif</span>
                                </div>
                                <div class="guru-meta"><i class="bi bi-envelope me-1"></i>{{ $g->email }}</div>
                                <div class="guru-meta"><i class="bi bi-whatsapp me-1"></i>{{ $g->no_whatsapp }}</div>
                                <div class="guru-meta mt-1">
                                    <i class="bi bi-info-circle me-1 text-danger"></i>
                                    <span class="badge bg-danger bg-opacity-10 text-danger">{{ $g->alasan_nonaktif ?? '-' }}</span>
                                    @if($g->tanggal_nonaktif)
                                        <span class="ms-1">· {{ \Carbon\Carbon::parse($g->tanggal_nonaktif)->format('d M Y') }}</span>
                                    @endif
                                </div>
                                <div class="badge-wrap mt-1">
                                    @foreach($g->pengajars->pluck('mapel.nama_mapel')->filter()->unique() as $m)
                                        <span class="badge bg-secondary">{{ $m }}</span>
                                    @endforeach
                                    @foreach($g->pengajars->pluck('kelas.nama_kelas')->filter()->unique() as $k)
                                        <span class="badge bg-info text-dark">{{ $k }}</span>
                                    @endforeach
                                </div>
                                <div class="card-actions">
                                    @if($g->surat_keterangan)
                                    <a href="{{ route('guru.download-surat', $g->id) }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-file-earmark-arrow-down me-1"></i>Surat
                                    </a>
                                    @endif
                                    <form action="{{ route('guru.restore', $g->id) }}" method="POST" class="flex-fill">
                                        @csrf
                                        <button class="btn btn-outline-success btn-sm w-100"
                                                onclick="return confirm('Pulihkan guru ini ke data aktif?')">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i>Pulihkan
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-person-x fs-3 d-block mb-2 text-secondary"></i>
                                Tidak ada data guru nonaktif.
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
     MODAL: TAMBAH GURU
══════════════════════════════════════════════ --}}
<div class="modal fade" id="modalTambahGuru" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('guru.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-plus me-2"></i>Tambah Guru Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Nama Guru <span class="text-danger">*</span></label>
                        <input type="text" name="nama_guru" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Mata Pelajaran & Kelas <span class="text-danger">*</span></label>
                        <div id="wrapper-pengajar-tambah">
                            <div class="row g-2 mb-2 pengajar-item">
                                <div class="col-5">
                                    <select name="mapel_ids[]" class="form-select select2-mapel" required>
                                        <option value="">-- Mapel --</option>
                                        @foreach($mapels as $m)
                                            <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-5">
                                    <select name="kelas_ids[]" class="form-select select2-kelas" required>
                                        <option value="">-- Kelas --</option>
                                        @foreach($kelases as $k)
                                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-2">
                                    <button type="button" class="btn btn-success w-100 btn-add-pengajar"><i class="bi bi-plus-lg"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold s  mall">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Nomor WhatsApp <span class="text-danger">*</span></label>
                        <input type="text" name="no_whatsapp" class="form-control" required>
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
     MODAL: EDIT GURU
══════════════════════════════════════════════ --}}
<div class="modal fade" id="modalEditGuru" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="formEditGuru" method="POST">
                @csrf @method('PUT')
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Data Guru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Nama Guru <span class="text-danger">*</span></label>
                        <input type="text" name="nama_guru" id="edit_nama" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Mata Pelajaran & Kelas <span class="text-danger">*</span></label>
                        <div id="wrapper-pengajar-edit">
                            </div>
                        <button type="button" class="btn btn-sm btn-outline-success mt-2" id="btn-add-pengajar-edit">
                            <i class="bi bi-plus-lg me-1"></i> Tambah Kelas/Mapel
                        </button>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="edit_email" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Nomor WhatsApp <span class="text-danger">*</span></label>
                        <input type="text" name="no_whatsapp" id="edit_whatsapp" class="form-control" required>
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
     MODAL: NONAKTIFKAN GURU
══════════════════════════════════════════════ --}}
<div class="modal fade" id="modalNonaktifGuru" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="formNonaktifGuru" method="POST" enctype="multipart/form-data">
                @csrf @method('DELETE')
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-dash me-2"></i>Nonaktifkan Guru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted mb-3">
                        Guru <strong id="namaGuruNonaktif"></strong> akan dipindahkan ke data nonaktif.
                        Tindakan ini dapat dipulihkan kembali.
                    </p>

                    {{-- Alasan Nonaktif --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alasan Nonaktif <span class="text-danger">*</span></label>
                        <select name="alasan_nonaktif" id="selectAlasanGuru" class="form-select" required onchange="toggleAlasanLainGuru(this)">
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
                    <div class="mb-3 d-none" id="inputAlasanLainGuru">
                        <label class="form-label fw-semibold">Tulis Alasan <span class="text-danger">*</span></label>
                        <input type="text" id="alasanLainGuru" class="form-control"
                               placeholder="Contoh: Melanjutkan studi ke luar kota...">
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
                    <button type="submit" class="btn btn-danger px-4 shadow-sm" id="btnSubmitNonaktif">
                        <i class="bi bi-person-dash me-1"></i>Nonaktifkan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
        doSearch($('#search-guru').val());
    });

    // ── AJAX Search ────────────────────────────────────
    function doSearch(keyword) {
        const targetBody = currentTab === 'nonaktif' ? '#table-body-nonaktif' : '#table-body-aktif';
        $.ajax({
            type: 'GET',
            url: "{{ route('guru.search') }}",
            data: { search: keyword, tab: currentTab },
            success: function (data) {
                $(targetBody).html(data);
                // Sembunyikan tombol lihat semua saat sedang search
                if (keyword.length > 0) {
                    $('#btn-lihat-semua-wrapper').hide();
                } else {
                    $('#btn-lihat-semua-wrapper').show();
                }
            }
        });
    }

    $('#search-guru').on('keyup', function () {
        doSearch($(this).val());
    });
});

// ── Lihat Semua Data (tab aktif) ───────────────────
function lihatSemuaGuru() {
    $('.row-extra-aktif').removeClass('row-hidden');
    $('#btn-lihat-semua-wrapper').hide();
}

// ── Edit Modal ─────────────────────────────────────
const editModal = new bootstrap.Modal(document.getElementById('modalEditGuru'));
function openEditModal(id, nama, pengajarDataArray, email, whatsapp, alamat) {
    document.getElementById('formEditGuru').action = `/guru/${id}`;
    document.getElementById('edit_nama').value     = nama;
    document.getElementById('edit_email').value    = email;
    document.getElementById('edit_whatsapp').value = whatsapp;
    document.getElementById('edit_alamat').value   = alamat;
    
    let wrapper = $('#wrapper-pengajar-edit');
    wrapper.empty();

    // pengajarDataArray formatnya sekarang [{id_mapel: x, id_kelas: y}]
    if(pengajarDataArray && pengajarDataArray.length > 0) {
        pengajarDataArray.forEach(function(item) {
            addSelectPengajarEdit(item.id_mapel, item.id_kelas);
        });
    } else {
        addSelectPengajarEdit('', '');
    }
    editModal.show();
}
// ── Modal Nonaktifkan Guru ─────────────────────────
const nonaktifModal = new bootstrap.Modal(document.getElementById('modalNonaktifGuru'));
function bukaModalNonaktif(id, nama) {
    document.getElementById('namaGuruNonaktif').textContent = nama;
    document.getElementById('formNonaktifGuru').action      = `/guru/${id}`;
    // Reset form
    document.getElementById('selectAlasanGuru').value = '';
    document.getElementById('inputAlasanLainGuru').classList.add('d-none');
    document.getElementById('alasanLainGuru').value = '';
    nonaktifModal.show();
}

function toggleAlasanLainGuru(select) {
    const inputDiv = document.getElementById('inputAlasanLainGuru');
    const inputTeks = document.getElementById('alasanLainGuru');
    if (select.value === 'Lainnya') {
        inputDiv.classList.remove('d-none');
        inputTeks.required = true;
    } else {
        inputDiv.classList.add('d-none');
        inputTeks.required = false;
    }
}

// Inject alasan custom sebelum submit
document.getElementById('formNonaktifGuru').addEventListener('submit', function (e) {
    const select    = document.getElementById('selectAlasanGuru');
    const alasanLain = document.getElementById('alasanLainGuru').value.trim();

    if (select.value === 'Lainnya') {
        if (!alasanLain) {
            e.preventDefault();
            alert('Harap isi alasan nonaktif terlebih dahulu.');
            return;
        }
        // Ganti value select dengan input manual
        const hidden = document.createElement('input');
        hidden.type  = 'hidden';
        hidden.name  = 'alasan_nonaktif';
        hidden.value = alasanLain;
        this.appendChild(hidden);
        select.removeAttribute('name'); // hindari duplikasi
    }
});

const mapelData = @json($mapels);
const kelasData = @json($kelases);

$(document).ready(function() {
    function initSelect2() {
        $('.select2-mapel, .select2-kelas').select2({
            theme: 'bootstrap-5',
            dropdownParent: $(this).closest('.modal') // Adaptif ke modal mana yg terbuka
        });
    }
    
    $('#modalTambahGuru').on('shown.bs.modal', function () { initSelect2.call(this); });
    $('#modalEditGuru').on('shown.bs.modal', function () { initSelect2.call(this); });

    // --- LOGIKA TOMBOL PLUS (+) DI MODAL TAMBAH ---
    $(document).on('click', '.btn-add-pengajar', function() {
        let wrapper = $('#wrapper-pengajar-tambah');
        let newRow = buildPengajarRow('', '', true); // True = tombol hapus
        wrapper.append(newRow);
        initSelect2.call(document.getElementById('modalTambahGuru'));
    });

    // --- LOGIKA TOMBOL PLUS (+) DI MODAL EDIT ---
    $('#btn-add-pengajar-edit').on('click', function() {
        addSelectPengajarEdit('', ''); 
    });

    // --- LOGIKA TOMBOL HAPUS (TONG SAMPAH) ---
    $(document).on('click', '.btn-remove-pengajar', function() {
        $(this).closest('.pengajar-item').remove();
    });
});

// Fungsi pembuat elemen baris Select untuk Mapel & Kelas
function buildPengajarRow(selectedMapel, selectedKelas, isRemovable) {
    let btnHtml = isRemovable 
        ? `<button type="button" class="btn btn-danger w-100 btn-remove-pengajar"><i class="bi bi-trash"></i></button>` 
        : `<button type="button" class="btn btn-danger w-100" style="visibility: hidden;"><i class="bi bi-trash"></i></button>`;

    let mapelOptions = mapelData.map(m => `<option value="${m.id}" ${(m.id == selectedMapel) ? 'selected' : ''}>${m.nama_mapel}</option>`).join('');
    let kelasOptions = kelasData.map(k => `<option value="${k.id}" ${(k.id == selectedKelas) ? 'selected' : ''}>${k.nama_kelas}</option>`).join('');

    return `
        <div class="row g-2 mb-2 pengajar-item">
            <div class="col-5">
                <select name="mapel_ids[]" class="form-select select2-mapel" required>
                    <option value="">-- Mapel --</option>
                    ${mapelOptions}
                </select>
            </div>
            <div class="col-5">
                <select name="kelas_ids[]" class="form-select select2-kelas" required>
                    <option value="">-- Kelas --</option>
                    ${kelasOptions}
                </select>
            </div>
            <div class="col-2">
                ${btnHtml}
            </div>
        </div>
    `;
}

// Tambah row spesifik di modal Edit
function addSelectPengajarEdit(selectedMapel, selectedKelas) {
    let wrapper = $('#wrapper-pengajar-edit');
    let isFirst = wrapper.children().length === 0;
    
    let newRow = buildPengajarRow(selectedMapel, selectedKelas, !isFirst);
    wrapper.append(newRow);

    // Re-inisiasi select2
    wrapper.find('.select2-mapel').last().select2({ theme: 'bootstrap-5', dropdownParent: $('#modalEditGuru') });
    wrapper.find('.select2-kelas').last().select2({ theme: 'bootstrap-5', dropdownParent: $('#modalEditGuru') });
}
</script>
</body>
</html>
