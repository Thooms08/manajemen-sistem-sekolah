<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detail Program: {{ $program->nama_program }}</title>
    @if(isset($sekolah->logo))
    <link rel="icon" type="image/png" href="{{ \App\Helpers\ImageHelper::url($sekolah->logo) }}">
    @else
    <link rel="icon" type="image/png" href="{{ asset('assets/img/default-favicon.png') }}">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
    <style>
        :root { --primary-green: #198754; }
        body { font-family: 'Inter', sans-serif; background: #f4f7f6; overflow-x: hidden; }
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        #content { width: 100%; padding: 20px 30px; min-height: 100vh; min-width: 0; transition: all 0.3s; }
        #sidebarCollapse { width: 45px; height: 45px; background: var(--primary-green); border: none; color: white; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .card { border: none; border-radius: 14px; box-shadow: 0 4px 18px rgba(0,0,0,0.05); }
        .section-card { background: white; border-radius: 14px; box-shadow: 0 4px 18px rgba(0,0,0,0.05); margin-bottom: 1.5rem; }
        .section-header { padding: 16px 22px 14px; border-bottom: 1px solid #f0f0f0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; }
        .section-header h6 { margin: 0; font-weight: 700; font-size: 0.95rem; }
        .section-body { padding: 20px 22px; }
        .page-header { gap: 12px; }
        input:focus, textarea:focus, select:focus { border-color: #198754 !important; box-shadow: 0 0 0 0.2rem rgba(25,135,84,.25) !important; outline: none !important; }
        /* Bagan organisasi */
        .bagan-card { background: white; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 14px 18px; margin-bottom: 10px; display: flex; align-items: center; gap: 14px; transition: 0.2s; }
        .bagan-card:hover { border-color: var(--primary-green); box-shadow: 0 2px 10px rgba(25,135,84,0.08); }
        .bagan-jabatan { font-weight: 700; font-size: 0.8rem; color: #198754; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px; }
        .bagan-nama { font-size: 0.92rem; color: #333; }
        /* Anggota chip */
        .anggota-chip { display: inline-flex; align-items: center; gap: 6px; background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; border-radius: 50px; padding: 4px 12px; font-size: 0.82rem; margin: 4px; }
        .anggota-chip .btn-del { background: none; border: none; padding: 0; color: #dc3545; cursor: pointer; font-size: 0.85rem; line-height: 1; }
        /* Catatan card */
        .catatan-card { border: 1px solid #e9ecef; border-left: 4px solid var(--primary-green); border-radius: 8px; padding: 14px 18px; margin-bottom: 12px; background: #fff; }
        .catatan-card .catatan-judul { font-weight: 700; font-size: 0.92rem; margin-bottom: 6px; }
        .catatan-card .catatan-isi { font-size: 0.88rem; color: #555; white-space: pre-wrap; }
        .catatan-card .catatan-meta { font-size: 0.78rem; color: #aaa; margin-top: 8px; }
        /* Editor textarea */
        #isi_catatan, #edit_isi_catatan { font-family: 'Courier New', monospace; font-size: 0.9rem; min-height: 200px; resize: vertical; background: #fdfdfd; }
        #overlay { display: none; position: fixed; width: 100vw; height: 100vh; background: rgba(0,0,0,.5); z-index: 1040; top: 0; left: 0; }
        #overlay.active { display: block; }
        .modal-footer { flex-wrap: wrap; gap: .5rem; }
        @media (max-width: 991px) { #content { padding: 16px 18px; } }
        @media (max-width: 767px) {
            #content { padding: 12px 12px; }
            .page-header { flex-direction: column; align-items: flex-start !important; }
            .section-header { align-items: flex-start; }
            .section-header .btn { width: 100%; }
            .section-body { padding: 16px; }
            .bagan-card { flex-wrap: wrap; }
            .bagan-card .d-flex.gap-1 { width: 100%; justify-content: flex-end; }
            .catatan-card .d-flex { flex-wrap: wrap; gap: .5rem; }
            .modal-footer .btn { width: 100%; }
            .modal-body { padding: 1rem !important; }
            #anggota-info-bar { flex-direction: column; align-items: flex-start !important; gap: .5rem; }
            #anggota-info-bar .btn { width: 100%; }
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
            <div class="d-flex align-items-center justify-content-between mb-4 mt-2 flex-wrap gap-3 page-header">
                <div class="d-flex align-items-center gap-3">
                    <button type="button" id="sidebarCollapse" class="btn"><i class="bi bi-list fs-4"></i></button>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('informasi.index') }}" class="text-muted text-decoration-none small">
                                <i class="bi bi-arrow-left-circle me-1"></i>Kembali
                            </a>
                        </div>
                        <h4 class="mb-0 fw-bold text-success">{{ $program->nama_program }}</h4>
                        <p class="text-muted small mb-0">{{ $program->deskripsi_program }}</p>
                    </div>
                </div>
            </div>

            {{-- Alert --}}
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

            <div class="row g-4">

                {{-- KOLOM KIRI --}}
                <div class="col-lg-6">

                    {{-- ═══ SECTION: PEMBINA ═══ --}}
                    <div class="section-card">
                        <div class="section-header">
                            <h6><i class="bi bi-person-badge-fill me-2 text-success"></i>Pembina Program</h6>
                            <button class="btn btn-success btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalTambahPembina">
                                <i class="bi bi-plus-lg me-1"></i>Tambah
                            </button>
                        </div>
                        <div class="section-body">
                            @forelse($program->pembinas as $pb)
                                <div class="d-flex align-items-center justify-content-between border rounded px-3 py-2 mb-2">
                                    <div>
                                        <div class="fw-semibold" style="font-size:.9rem;">{{ $pb->nama }}</div>
                                        <small class="text-muted">
                                            <span class="badge {{ $pb->tipe === 'guru' ? 'bg-primary' : 'bg-warning text-dark' }} me-1">
                                                {{ ucfirst($pb->tipe) }}
                                            </span>
                                            {{ $pb->peran ?? 'Pembina' }}
                                        </small>
                                    </div>
                                    <form action="{{ route('program.pembina.destroy', [$program->id, $pb->id]) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger border-0" title="Hapus" onclick="return confirm('Hapus pembina ini?')">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            @empty
                                <p class="text-muted small text-center py-3 mb-0">
                                    <i class="bi bi-person-x d-block fs-4 mb-1"></i>Belum ada pembina.
                                </p>
                            @endforelse
                        </div>
                    </div>

                    {{-- ═══ SECTION: BAGAN ORGANISASI ═══ --}}
                    <div class="section-card">
                        <div class="section-header">
                            <h6><i class="bi bi-diagram-3-fill me-2 text-success"></i>Bagan Organisasi</h6>
                            <button class="btn btn-success btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalTambahBagan">
                                <i class="bi bi-plus-lg me-1"></i>Tambah
                            </button>
                        </div>
                        <div class="section-body">
                            @forelse($program->bagans as $bg)
                                <div class="bagan-card">
                                    <div class="flex-shrink-0" style="width:40px;height:40px;background:#e8f5e9;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                                        <i class="bi bi-person-fill text-success"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="bagan-jabatan">{{ $bg->jabatan }}</div>
                                        <div class="bagan-nama">{{ $bg->nama_pemegang }}</div>
                                    </div>
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-sm btn-outline-success border-0" title="Edit" onclick="openEditBagan({{ $bg->id }})">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <form action="{{ route('program.bagan.destroy', [$program->id, $bg->id]) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Hapus entri bagan ini?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted small text-center py-3 mb-0">
                                    <i class="bi bi-diagram-3 d-block fs-4 mb-1"></i>Belum ada bagan organisasi.
                                </p>
                            @endforelse
                        </div>
                    </div>

                </div>{{-- /kolom kiri --}}

                {{-- KOLOM KANAN --}}
                <div class="col-lg-6">

                    {{-- ═══ SECTION: ANGGOTA ═══ --}}
                    <div class="section-card">
                        <div class="section-header">
                            <h6><i class="bi bi-people-fill me-2 text-success"></i>Anggota Program
                                <span class="badge bg-success bg-opacity-10 text-success ms-1">{{ $program->anggotas->count() }}</span>
                            </h6>
                            <button class="btn btn-success btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalTambahAnggota">
                                <i class="bi bi-person-plus me-1"></i>Tambah
                            </button>
                        </div>
                        <div class="section-body">
                            @if($program->anggotas->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle" style="font-size:.85rem;">
                                        <thead style="background:#f0fdf4;">
                                            <tr>
                                                <th>NIS</th>
                                                <th>Nama Murid</th>
                                                <th>Kelas</th>
                                                <th width="50" class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($program->anggotas as $i => $ag)
                                                <tr class="{{ $i >= 10 ? 'anggota-extra d-none' : '' }}">
                                                    <td class="text-muted">{{ $ag->murid->nis_baru ?? ($ag->murid->nisn ?? '-') }}</td>
                                                    <td class="fw-semibold">{{ $ag->murid->nama_lengkap ?? '(dihapus)' }}</td>
                                                    <td>
                                                        @if($ag->murid && $ag->murid->kelas->first())
                                                            <span class="badge bg-info bg-opacity-10 text-info">{{ $ag->murid->kelas->first()->nama_kelas }}</span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <form action="{{ route('program.anggota.destroy', [$program->id, $ag->id]) }}" method="POST">
                                                            @csrf @method('DELETE')
                                                            <button class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Hapus anggota ini?')">
                                                                <i class="bi bi-x-lg"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Info + tombol lihat semua — hanya muncul jika > 10 --}}
                                @if($program->anggotas->count() > 10)
                                    <div id="anggota-info-bar" class="d-flex align-items-center justify-content-between mt-2 px-1">
                                        <small class="text-muted">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Menampilkan <strong>10</strong> dari <strong>{{ $program->anggotas->count() }}</strong> anggota.
                                        </small>
                                        <button type="button" class="btn btn-outline-success btn-sm px-3"
                                            id="btnLihatSemuaAnggota" onclick="lihatSemuaAnggota()">
                                            <i class="bi bi-chevron-down me-1"></i>
                                            Lihat Semua Data
                                            <span class="badge bg-success ms-1">{{ $program->anggotas->count() - 10 }} lainnya</span>
                                        </button>
                                    </div>
                                @endif

                            @else
                                <p class="text-muted small text-center py-3 mb-0">
                                    <i class="bi bi-people d-block fs-4 mb-1"></i>Belum ada anggota terdaftar.
                                </p>
                            @endif
                        </div>
                    </div>

                </div>{{-- /kolom kanan --}}

            </div>{{-- /row --}}

            {{-- ═══ SECTION: CATATAN (full width) ═══ --}}
            <div class="section-card">
                <div class="section-header">
                    <h6><i class="bi bi-journal-text me-2 text-success"></i>Catatan Program
                        <span class="text-muted fw-normal" style="font-size:.8rem; margin-left:6px;">Program kerja, jadwal latihan, dll.</span>
                    </h6>
                    <button class="btn btn-success btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalTambahCatatan">
                        <i class="bi bi-plus-lg me-1"></i>Tambah Catatan
                    </button>
                </div>
                <div class="section-body">
                    @forelse($program->catatans as $ct)
                        <div class="catatan-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="catatan-judul">{{ $ct->judul }}</div>
                                <div class="d-flex gap-1 ms-2 flex-shrink-0">
                                    <button class="btn btn-sm btn-outline-success border-0" onclick="openEditCatatan({{ $ct->id }})" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <form action="{{ route('program.catatan.destroy', [$program->id, $ct->id]) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Hapus catatan ini?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="catatan-isi">{{ $ct->isi }}</div>
                            <div class="catatan-meta"><i class="bi bi-clock me-1"></i>{{ $ct->updated_at->diffForHumans() }}</div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-journal-x fs-2 d-block mb-2"></i>
                            Belum ada catatan. Tambahkan catatan program kerja, jadwal latihan, atau informasi lainnya.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>{{-- /container --}}
    </div>{{-- /content --}}
</div>{{-- /wrapper --}}

{{-- ══════════════════════════════════════════════════
     MODAL: TAMBAH PEMBINA
══════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalTambahPembina" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('program.pembina.store', $program->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-badge me-2"></i>Tambah Pembina</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Sumber Pembina <span class="text-danger">*</span></label>
                        <select name="tipe" id="tipe_pembina" class="form-select" required onchange="loadPembinaOptions(this.value)">
                            <option value="">-- Pilih Tipe --</option>
                            <option value="guru">Guru</option>
                            <option value="staff">Staff</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Nama <span class="text-danger">*</span></label>
                        <select name="id_sumber" id="id_sumber_pembina" class="form-select select2-pembina" required disabled>
                            <option value="">-- Pilih Tipe Dulu --</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Peran <span class="text-muted fw-normal">(opsional)</span></label>
                        <input type="text" name="peran" class="form-control" placeholder="Contoh: Pembina Utama, Asisten Pembina" maxlength="100">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════
     MODAL: TAMBAH ANGGOTA (multiple select)
══════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalTambahAnggota" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('program.anggota.store', $program->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-plus me-2"></i>Tambah Anggota</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <label class="form-label fw-semibold small">Pilih Murid <span class="text-danger">*</span>
                        <span class="text-muted fw-normal">(bisa pilih banyak)</span>
                    </label>
                    <select name="id_murids[]" class="form-select select2-anggota" multiple required>
                        @foreach($muridList as $m)
                            @if(!in_array($m->id, $anggotaIds))
                                <option value="{{ $m->id }}"
                                    data-nis="{{ $m->nis_baru ?? $m->nisn }}"
                                    data-kelas="{{ $m->kelas->first()->nama_kelas ?? '-' }}">
                                    {{ $m->nama_lengkap }}
                                    (NIS: {{ $m->nis_baru ?? $m->nisn ?? '-' }}
                                    | {{ $m->kelas->first()->nama_kelas ?? 'Tanpa Kelas' }})
                                </option>
                            @endif
                        @endforeach
                    </select>
                    <div class="form-text mt-2">
                        <i class="bi bi-info-circle me-1"></i>
                        Murid yang sudah terdaftar tidak ditampilkan.
                        Total tersedia: {{ $muridList->count() - count($anggotaIds) }} murid.
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4">Tambahkan Anggota</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════
     MODAL: TAMBAH BAGAN ORGANISASI
══════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalTambahBagan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('program.bagan.store', $program->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-diagram-3 me-2"></i>Tambah Bagan Organisasi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold small">Jabatan <span class="text-danger">*</span></label>
                        <input type="text" name="jabatan" class="form-control" placeholder="Ketua, Sekretaris, Bendahara, ..." maxlength="100" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Urutan</label>
                        <input type="number" name="urutan" class="form-control" value="0" min="0">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Tipe Pemegang <span class="text-danger">*</span></label>
                        <select name="tipe_pemegang" id="tipe_pemegang_bagan" class="form-select" required onchange="loadPemegangBagan(this.value)">
                            <option value="">-- Pilih Tipe --</option>
                            <option value="murid">Murid</option>
                            <option value="guru">Guru</option>
                            <option value="staff">Staff</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Nama Pemegang <span class="text-danger">*</span></label>
                        <select name="id_pemegang" id="id_pemegang_bagan" class="form-select select2-pemegang-bagan" required disabled>
                            <option value="">-- Pilih Tipe Dulu --</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════
     MODAL: EDIT BAGAN ORGANISASI
══════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalEditBagan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content border-0 shadow">
            <form id="formEditBagan" method="POST">
                @csrf @method('PUT')
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Bagan Organisasi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4" id="editBaganBody">
                    <div class="text-center py-4"><div class="spinner-border text-success"></div></div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4">Perbarui</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════
     MODAL: TAMBAH CATATAN
══════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalTambahCatatan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('program.catatan.store', $program->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-journal-plus me-2"></i>Tambah Catatan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Judul Catatan <span class="text-danger">*</span></label>
                        <input type="text" name="judul" class="form-control" placeholder="Contoh: Program Kerja 2026, Jadwal Latihan Mingguan, ..." maxlength="255" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Isi Catatan <span class="text-danger">*</span></label>
                        <textarea name="isi" id="isi_catatan" class="form-control" rows="10"
                            placeholder="Tulis catatan di sini...&#10;Contoh:&#10;- Senin: Latihan pukul 14.00&#10;- Selasa: Rapat koordinasi&#10;- ..." required></textarea>
                        <div class="form-text"><i class="bi bi-keyboard me-1"></i>Gunakan teks bebas. Enter untuk baris baru.</div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4"><i class="bi bi-save me-1"></i>Simpan Catatan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════
     MODAL: EDIT CATATAN
══════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalEditCatatan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">
        <div class="modal-content border-0 shadow">
            <form id="formEditCatatan" method="POST">
                @csrf @method('PUT')
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Catatan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Judul Catatan <span class="text-danger">*</span></label>
                        <input type="text" name="judul" id="edit_judul_catatan" class="form-control" required maxlength="255">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Isi Catatan <span class="text-danger">*</span></label>
                        <textarea name="isi" id="edit_isi_catatan" class="form-control" rows="10" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4"><i class="bi bi-save me-1"></i>Perbarui Catatan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function () {
    // Sidebar toggle
    function toggleSidebar() {
        if ($(window).width() <= 768) {
            $('#sidebar').toggleClass('show-mobile');
            $('#overlay').toggleClass('active');
        } else {
            $('#sidebar').toggleClass('inactive');
        }
    }
    $('#sidebarCollapse, #close-sidebar, #overlay').on('click', toggleSidebar);

    // Select2 anggota (multiple)
    $('.select2-anggota').select2({
        theme: 'bootstrap-5', width: '100%',
        dropdownParent: $('#modalTambahAnggota'),
        placeholder: 'Cari murid berdasarkan nama atau NIS...',
    });

    // Select2 pemegang bagan di modal tambah
    $('#id_pemegang_bagan').select2({
        theme: 'bootstrap-5', width: '100%',
        dropdownParent: $('#modalTambahBagan'),
        placeholder: '-- Pilih Tipe Dulu --',
    });

    // Select2 pembina
    $('#id_sumber_pembina').select2({
        theme: 'bootstrap-5', width: '100%',
        dropdownParent: $('#modalTambahPembina'),
        placeholder: '-- Pilih Tipe Dulu --',
    });

    // Reset modal saat tutup
    $('#modalTambahPembina').on('hidden.bs.modal', function () {
        $('#tipe_pembina').val('').trigger('change');
        $('#id_sumber_pembina').empty().append('<option value="">-- Pilih Tipe Dulu --</option>').prop('disabled', true).trigger('change.select2');
    });
    $('#modalTambahBagan').on('hidden.bs.modal', function () {
        $('#tipe_pemegang_bagan').val('');
        $('#id_pemegang_bagan').empty().append('<option value="">-- Pilih Tipe Dulu --</option>').prop('disabled', true).trigger('change.select2');
    });
});

// ── Load dropdown pembina berdasarkan tipe ──────────────────────────
function loadPembinaOptions(tipe) {
    const $sel    = $('#id_sumber_pembina');
    const baseUrl = "{{ route('program.pemegang-by-tipe') }}";
    const LIMIT   = 10;

    $sel.empty().append('<option value="">-- Pilih Tipe Dulu --</option>').prop('disabled', true);
    if (!tipe) return;

    if ($sel.data('select2')) $sel.select2('destroy');
    $sel.prop('disabled', false);

    $sel.select2({
        theme: 'bootstrap-5', width: '100%',
        dropdownParent: $('#modalTambahPembina'),
        placeholder: 'Cari nama...',
        minimumInputLength: 0,
        ajax: {
            url: baseUrl,
            dataType: 'json',
            delay: 300,
            data: params => ({ tipe, search: params.term || '', limit: LIMIT }),
            processResults: function (response) {
                const total   = response.results.length > 0 ? response.results[0].total : 0;
                const results = response.results.map(d => ({ id: d.id, text: d.nama }));
                const $hint   = $('#id_sumber_pembina').closest('.col-12').find('.sel2-hint-pb');
                if (total > LIMIT) {
                    if (!$hint.length) {
                        $('#id_sumber_pembina').closest('.col-12').append(
                            `<div class="sel2-hint-pb form-text text-muted mt-1">
                                <i class="bi bi-info-circle me-1"></i>
                                Menampilkan <strong>${LIMIT}</strong> dari <strong>${total}</strong> data.
                                Ketik nama untuk mencari lebih banyak.
                            </div>`
                        );
                    }
                } else { $hint.remove(); }
                return { results };
            },
        },
        language: { searching: () => 'Mencari...', noResults: () => 'Tidak ditemukan.' },
    });
}

// ── Load dropdown pemegang bagan berdasarkan tipe ───────────────────
function loadPemegangBagan(tipe) {
    const $sel = $('#id_pemegang_bagan');
    $sel.empty().append('<option value="">-- Pilih Tipe Dulu --</option>').prop('disabled', true);
    if (!tipe) return;

    const LIMIT   = 10;
    const baseUrl = "{{ route('program.pemegang-by-tipe') }}";
    const progId  = {{ $program->id }};

    // Destroy instance Select2 lama dulu jika ada
    if ($sel.data('select2')) $sel.select2('destroy');

    $sel.prop('disabled', false);

    $sel.select2({
        theme: 'bootstrap-5',
        width: '100%',
        dropdownParent: $('#modalTambahBagan'),
        placeholder: 'Cari nama...',
        minimumInputLength: 0,
        ajax: {
            url: baseUrl,
            dataType: 'json',
            delay: 300,
            data: function (params) {
                return {
                    tipe:       tipe,
                    id_program: tipe === 'murid' ? progId : null,
                    search:     params.term || '',
                    limit:      LIMIT,
                };
            },
            processResults: function (response) {
                const total   = response.results.length > 0 ? response.results[0].total : 0;
                const results = response.results.map(d => ({ id: d.id, text: d.nama }));

                // Tambahkan pesan info jika data lebih dari limit
                if (!$('#sel2-info-tambah').length && total > LIMIT) {
                    $('#id_pemegang_bagan').closest('.col-12')
                        .find('.sel2-hint').remove();
                    $('#id_pemegang_bagan').closest('.col-12').append(
                        `<div class="sel2-hint form-text text-muted mt-1">
                            <i class="bi bi-info-circle me-1"></i>
                            Menampilkan <strong>${LIMIT}</strong> dari <strong>${total}</strong> data.
                            Ketik nama untuk mencari lebih banyak.
                        </div>`
                    );
                } else if (total <= LIMIT) {
                    $('#id_pemegang_bagan').closest('.col-12').find('.sel2-hint').remove();
                }

                return { results };
            },
        },
        language: {
            inputTooShort: () => tipe === 'murid'
                ? 'Hanya anggota program yang tampil. Ketik untuk mencari.'
                : 'Ketik untuk mencari...',
            noResults: () => tipe === 'murid'
                ? 'Tidak ditemukan. Pastikan murid sudah ditambahkan sebagai anggota terlebih dahulu.'
                : 'Tidak ditemukan.',
            searching: () => 'Mencari...',
        },
    });
}

// ── Open modal edit bagan ───────────────────────────────────────────
function openEditBagan(baganId) {
    const $body = $('#editBaganBody');
    const $form = $('#formEditBagan');
    $form.attr('action', `/informasi/program/{{ $program->id }}/bagan/${baganId}`);
    $body.html('<div class="text-center py-4"><div class="spinner-border text-success"></div></div>');
    $('#modalEditBagan').modal('show');

    $.get(`{{ url("informasi/program/{$program->id}/bagan") }}/${baganId}`, function (d) {
        const hariOpts = ['murid','guru','staff'].map(t =>
            `<option value="${t}" ${d.tipe_pemegang===t?'selected':''}>${t.charAt(0).toUpperCase()+t.slice(1)}</option>`
        ).join('');

        $body.html(`
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-semibold small">Jabatan <span class="text-danger">*</span></label>
                    <input type="text" name="jabatan" class="form-control" value="${d.jabatan}" maxlength="100" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Urutan</label>
                    <input type="number" name="urutan" class="form-control" value="${d.urutan}" min="0">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold small">Tipe Pemegang <span class="text-danger">*</span></label>
                    <select name="tipe_pemegang" id="edit_tipe_pemegang_bagan" class="form-select" required onchange="loadEditPemegangBagan(this.value, ${d.id_pemegang})">${hariOpts}</select>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold small">Pemegang <span class="text-danger">*</span></label>
                    <select name="id_pemegang" id="edit_id_pemegang_bagan" class="form-select select2-edit-pemegang" required>
                        <option value="${d.id_pemegang}" selected>${d.nama_pemegang}</option>
                    </select>
                </div>
            </div>`);

        $('#edit_id_pemegang_bagan').select2({
            theme: 'bootstrap-5', width: '100%',
            dropdownParent: $('#modalEditBagan'),
        });

        // Pre-load options
        loadEditPemegangBagan(d.tipe_pemegang, d.id_pemegang);
    });
}

function loadEditPemegangBagan(tipe, selectedId) {
    const $sel = $('#edit_id_pemegang_bagan');
    if (!tipe) return;

    const baseUrl = "{{ route('program.pemegang-by-tipe') }}";
    const progId  = {{ $program->id }};
    const LIMIT   = 10;

    if ($sel.data('select2')) $sel.select2('destroy');

    $sel.select2({
        theme: 'bootstrap-5', width: '100%',
        dropdownParent: $('#modalEditBagan'),
        placeholder: 'Cari nama...',
        minimumInputLength: 0,
        ajax: {
            url: baseUrl,
            dataType: 'json',
            delay: 300,
            data: function (params) {
                return {
                    tipe:       tipe,
                    id_program: tipe === 'murid' ? progId : null,
                    search:     params.term || '',
                    limit:      LIMIT,
                };
            },
            processResults: function (response) {
                const total   = response.results.length > 0 ? response.results[0].total : 0;
                const results = response.results.map(d => ({ id: d.id, text: d.nama }));

                const $hint = $('#edit_id_pemegang_bagan').closest('.col-12').find('.sel2-hint-edit');
                if (total > LIMIT) {
                    if (!$hint.length) {
                        $('#edit_id_pemegang_bagan').closest('.col-12').append(
                            `<div class="sel2-hint-edit form-text text-muted mt-1">
                                <i class="bi bi-info-circle me-1"></i>
                                Menampilkan <strong>${LIMIT}</strong> dari <strong>${total}</strong> data.
                                Ketik nama untuk mencari lebih banyak.
                            </div>`
                        );
                    }
                } else {
                    $hint.remove();
                }
                return { results };
            },
        },
        language: {
            noResults: () => tipe === 'murid'
                ? 'Tidak ditemukan. Pastikan murid sudah ditambahkan sebagai anggota.'
                : 'Tidak ditemukan.',
            searching: () => 'Mencari...',
        },
    });

    // Set selected value
    if (selectedId) {
        $.get(baseUrl, { tipe, id_program: tipe === 'murid' ? progId : null, search: '', limit: 100 }, function (res) {
            const found = res.results.find(r => r.id == selectedId);
            if (found) {
                const opt = new Option(found.nama, found.id, true, true);
                $sel.append(opt).trigger('change.select2');
            }
        });
    }
}

// ── Lihat semua anggota ─────────────────────────────────────────────
function lihatSemuaAnggota() {
    document.querySelectorAll('.anggota-extra').forEach(function (row) {
        row.classList.remove('d-none');
    });
    const bar = document.getElementById('anggota-info-bar');
    if (bar) bar.remove();
}

// ── Open modal edit catatan ─────────────────────────────────────────
function openEditCatatan(catatanId) {
    const $form = $('#formEditCatatan');
    $form.attr('action', `/informasi/program/{{ $program->id }}/catatan/${catatanId}`);
    $('#edit_judul_catatan').val('');
    $('#edit_isi_catatan').val('');
    $('#modalEditCatatan').modal('show');

    $.get(`{{ url("informasi/program/{$program->id}/catatan") }}/${catatanId}`, function (d) {
        $('#edit_judul_catatan').val(d.judul);
        $('#edit_isi_catatan').val(d.isi);
    });
}
</script>
</body>
</html>
