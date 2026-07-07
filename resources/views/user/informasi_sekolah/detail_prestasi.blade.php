<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detail Prestasi: {{ $prestasi->judul_prestasi }}</title>
        @include('favicon')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #198754; }
        body { font-family: 'Inter', sans-serif; background: #f4f7f6; overflow-x: hidden; }
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        #content { width: 100%; padding: 24px 30px; min-height: 100vh; min-width: 0; transition: all .3s; }
        #sidebarCollapse { width: 45px; height: 45px; background: var(--primary); border: none; color: white; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .sec { background: white; border-radius: 14px; box-shadow: 0 4px 18px rgba(0,0,0,.05); margin-bottom: 1.5rem; overflow: hidden; }
        .sec-head { padding: 14px 20px; border-bottom: 1px solid #f0f0f0; display: flex; align-items: center; justify-content: space-between; background: #fafafa; flex-wrap: wrap; gap: 8px; }
        .sec-head h6 { margin: 0; font-weight: 700; font-size: .93rem; }
        .sec-body { padding: 20px; }
        .page-header { gap: 12px; }
        .jenis-options { display: flex; gap: 1rem; flex-wrap: wrap; }
        input:focus, textarea:focus, select:focus { border-color: #198754 !important; box-shadow: 0 0 0 .2rem rgba(25,135,84,.25) !important; outline: none !important; }
        .catatan-card { border: 1px solid #e9ecef; border-left: 4px solid var(--primary); border-radius: 8px; padding: 14px 18px; margin-bottom: 12px; background: #fff; }
        .catatan-judul { font-weight: 700; font-size: .9rem; margin-bottom: 6px; }
        .catatan-isi { font-size: .86rem; color: #555; white-space: pre-wrap; line-height: 1.6; }
        .catatan-meta { font-size: .76rem; color: #aaa; margin-top: 8px; }
        #isi_catatan, #edit_isi_catatan { font-family: 'Courier New', monospace; font-size: .88rem; min-height: 160px; resize: vertical; background: #fdfdfd; }
        /* Murid search */
        #murid-search-result .murid-item { display: flex; align-items: center; gap: 10px; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 6px; cursor: pointer; transition: .15s; }
        #murid-search-result .murid-item:hover { background: #f0fdf4; border-color: var(--primary); }
        #murid-search-result .murid-item.selected { background: #dcfce7; border-color: var(--primary); }
        #overlay { display: none; position: fixed; width: 100vw; height: 100vh; background: rgba(0,0,0,.5); z-index: 1040; top: 0; left: 0; }
        #overlay.active { display: block; }
        .modal-footer { flex-wrap: wrap; gap: .5rem; }
        @media (max-width: 991px) { #content { padding: 16px 18px; } }
        @media (max-width: 767px) {
            #content { padding: 12px 12px; }
            .page-header { flex-direction: column; align-items: flex-start !important; }
            .page-header > .badge { width: 100%; justify-content: center; }
            .sec-head { align-items: flex-start; }
            .sec-head .btn { width: 100%; }
            .sec-body { padding: 16px; }
            .jenis-options { flex-direction: column; gap: .5rem; }
            .modal-footer .btn { width: 100%; }
            .modal-body { padding: 1rem !important; }
            .table-responsive { font-size: .8rem; }
        }
    </style>
</head>
<body>
@php
    $__canCreate = can('prestasi', 'create');
    $__canEdit   = can('prestasi', 'edit');
    $__canDelete = can('prestasi', 'delete');
@endphp

<div id="overlay"></div>
<div class="wrapper">
    @include('user.sidebar')
    <div id="content">
        <div class="container-fluid">

            {{-- Header --}}
            <div class="d-flex align-items-center justify-content-between mb-4 mt-1 flex-wrap gap-3 page-header">
                <div class="d-flex align-items-center gap-3">
                    <button id="sidebarCollapse" class="btn"><i class="bi bi-list fs-4"></i></button>
                    <div>
                        <a href="{{ route('informasi.index') }}" class="text-muted text-decoration-none small">
                            <i class="bi bi-arrow-left-circle me-1"></i>Kembali ke Informasi
                        </a>
                        <h4 class="mb-0 fw-bold text-success mt-1">{{ $prestasi->judul_prestasi }}</h4>
                        <p class="text-muted small mb-0">{{ $prestasi->deskripsi_prestasi ?? 'Detail Prestasi' }}</p>
                    </div>
                </div>
                @php $det = $prestasi->detail; @endphp
                @if($det)
                    <span class="badge {{ $det->jenis === 'murid' ? 'bg-primary' : 'bg-success' }} px-3 py-2" style="font-size:.82rem;">
                        <i class="bi bi-trophy me-1"></i>
                        Prestasi {{ $det->jenis === 'murid' ? 'Murid' : 'Sekolah' }}
                    </span>
                @endif
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

                {{-- KOLOM KIRI: Detail Utama --}}
                <div class="col-lg-6">
                    <div class="sec">
                        <div class="sec-head">
                            <h6><i class="bi bi-trophy-fill me-2 text-warning"></i>Detail Prestasi</h6>
                        </div>
                        <div class="sec-body">
                            <form action="{{ route('prestasi.detail.update', $prestasi->id) }}" method="POST">
                                @csrf
                                <div class="row g-3">

                                    {{-- Jenis Prestasi --}}
                                    <div class="col-12">
                                        <label class="form-label fw-semibold small">Jenis Prestasi <span class="text-danger">*</span></label>
                                        <div class="d-flex gap-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="jenis" id="jenis_murid" value="murid"
                                                    {{ (!$det || $det->jenis === 'murid') ? 'checked' : '' }}
                                                    onchange="toggleJenis('murid')">
                                                <label class="form-check-label fw-semibold" for="jenis_murid">
                                                    <i class="bi bi-person-fill text-primary me-1"></i>Prestasi Murid
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="jenis" id="jenis_sekolah" value="sekolah"
                                                    {{ ($det && $det->jenis === 'sekolah') ? 'checked' : '' }}
                                                    onchange="toggleJenis('sekolah')">
                                                <label class="form-check-label fw-semibold" for="jenis_sekolah">
                                                    <i class="bi bi-building text-success me-1"></i>Prestasi Sekolah
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Nama Tim (khusus sekolah) --}}
                                    <div class="col-12" id="row-nama-tim" style="{{ (!$det || $det->jenis !== 'sekolah') ? 'display:none' : '' }}">
                                        <label class="form-label fw-semibold small">Nama Tim / Unit</label>
                                        <input type="text" name="nama_tim" class="form-control" value="{{ $det->nama_tim ?? '' }}"
                                            placeholder="Contoh: Tim Paskibra, UKS, OSIS...">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Bidang</label>
                                        <input type="text" name="bidang" class="form-control" value="{{ $det->bidang ?? '' }}"
                                            placeholder="Matematika, Seni, Olahraga, Robotik...">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Tingkat</label>
                                        <select name="tingkat" class="form-select">
                                            <option value="">-- Pilih Tingkat --</option>
                                            @foreach(\App\Http\Controllers\Informasi\DetailPrestasiController::TINGKAT_LIST as $t)
                                                <option value="{{ $t }}" {{ ($det && $det->tingkat === $t) ? 'selected' : '' }}>{{ $t }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Peringkat / Juara</label>
                                        <input type="text" name="peringkat" class="form-control" value="{{ $det->peringkat ?? '' }}"
                                            placeholder="Juara 1, Juara Umum, Medali Emas...">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Tanggal Pelaksanaan</label>
                                        <input type="date" name="tanggal_pelaksanaan" class="form-control"
                                            value="{{ $det && $det->tanggal_pelaksanaan ? $det->tanggal_pelaksanaan->format('Y-m-d') : '' }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold small">Penyelenggara</label>
                                        <input type="text" name="penyelenggara" class="form-control" value="{{ $det->penyelenggara ?? '' }}"
                                            placeholder="Kemendikbud, Dinas Pendidikan, Universitas...">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold small">Lokasi</label>
                                        <input type="text" name="lokasi" class="form-control" value="{{ $det->lokasi ?? '' }}"
                                            placeholder="Kota / Venue pelaksanaan...">
                                    </div>
                                    <div class="col-12 text-end">
                                        <button type="submit" class="btn btn-success px-4">
                                            <i class="bi bi-save me-1"></i>Simpan Detail
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- KOLOM KANAN: Murid Peraih --}}
                <div class="col-lg-6" id="kolom-murid" style="{{ ($det && $det->jenis === 'sekolah') ? 'display:none' : '' }}">
                    <div class="sec">
                        <div class="sec-head">
                            <h6>
                                <i class="bi bi-people-fill me-2 text-primary"></i>Murid Peraih Prestasi
                                <span class="badge bg-primary bg-opacity-10 text-primary ms-1">{{ $prestasi->murids->count() }}</span>
                            </h6>
                            @if($__canCreate)<button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalTambahMurid">
                                <i class="bi bi-person-plus me-1"></i>Tambah
                            </button>@endif
                        </div>
                        <div class="sec-body">
                            @if($prestasi->murids->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle" style="font-size:.85rem;">
                                        <thead style="background:#eff6ff;">
                                            <tr>
                                                <th>NIS</th>
                                                <th>Nama Murid</th>
                                                <th>Kelas</th>
                                                <th>Peran</th>
                                                <th width="50" class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($prestasi->murids as $i => $pm)
                                                <tr class="{{ $i >= 10 ? 'murid-extra d-none' : '' }}">
                                                    <td class="text-muted">{{ $pm->murid->nis_baru ?? ($pm->murid->nisn ?? '-') }}</td>
                                                    <td class="fw-semibold">{{ $pm->murid->nama_lengkap ?? '(dihapus)' }}</td>
                                                    <td>
                                                        <span class="badge bg-info bg-opacity-10 text-info">
                                                            {{ $pm->murid && $pm->murid->kelas->first() ? $pm->murid->kelas->first()->nama_kelas : '-' }}
                                                        </span>
                                                    </td>
                                                    <td><span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $pm->peran }}</span></td>
                                                    <td class="text-center">
                                                        <form action="{{ route('prestasi.murid.destroy', [$prestasi->id, $pm->id]) }}" method="POST">
                                                            @csrf @method('DELETE')
                                                            <button class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Hapus murid ini dari prestasi?')">
                                                                <i class="bi bi-x-lg"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                @if($prestasi->murids->count() > 10)
                                    <div id="murid-info-bar" class="d-flex align-items-center justify-content-between mt-2 px-1">
                                        <small class="text-muted">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Menampilkan <strong>10</strong> dari <strong>{{ $prestasi->murids->count() }}</strong> murid.
                                        </small>
                                        <button type="button" class="btn btn-outline-primary btn-sm px-3" onclick="lihatSemuaMurid()">
                                            <i class="bi bi-chevron-down me-1"></i>Lihat Semua Data
                                            <span class="badge bg-primary ms-1">{{ $prestasi->murids->count() - 10 }} lainnya</span>
                                        </button>
                                    </div>
                                @endif

                            @else
                                <p class="text-muted small text-center py-3 mb-0">
                                    <i class="bi bi-people d-block fs-4 mb-1"></i>Belum ada murid terdaftar.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Kolom kanan untuk sekolah: placeholder --}}
                <div class="col-lg-6" id="kolom-sekolah" style="{{ (!$det || $det->jenis !== 'sekolah') ? 'display:none' : '' }}">
                    <div class="sec">
                        <div class="sec-head">
                            <h6><i class="bi bi-building-fill me-2 text-success"></i>Info Prestasi Sekolah</h6>
                        </div>
                        <div class="sec-body">
                            <div class="alert alert-success py-2 mb-0" style="font-size:.85rem;">
                                <i class="bi bi-info-circle me-1"></i>
                                Prestasi ini mewakili institusi sekolah secara keseluruhan.
                                Detail lengkap (tim, bidang, tingkat, penyelenggara) sudah tersedia di form Detail Prestasi di sebelah kiri.
                                Gunakan fitur <strong>Catatan</strong> di bawah untuk mencatat detail tambahan.
                            </div>
                        </div>
                    </div>
                </div>

            </div>{{-- /row --}}

            {{-- CATATAN (full width) --}}
            <div class="sec">
                <div class="sec-head">
                    <h6>
                        <i class="bi bi-journal-text me-2 text-success"></i>Catatan Prestasi
                        <span class="text-muted fw-normal" style="font-size:.78rem; margin-left:6px;">Kronologi, proses latihan, pembimbing, dll.</span>
                    </h6>
                    @if($__canCreate)<button class="btn btn-success btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalTambahCatatan">
                        <i class="bi bi-plus-lg me-1"></i>Tambah Catatan
                    </button>@endif
                </div>
                <div class="sec-body">
                    @forelse($prestasi->catatans as $ct)
                        <div class="catatan-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="catatan-judul">{{ $ct->judul }}</div>
                                <div class="d-flex gap-1 ms-3 flex-shrink-0">
                                    @if($__canEdit)<button class="btn btn-sm btn-outline-success border-0" onclick="openEditCatatan({{ $ct->id }})" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>@endif
                                    <form action="{{ route('prestasi.catatan.destroy', [$prestasi->id, $ct->id]) }}" method="POST" class="d-inline">
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
                            Belum ada catatan. Tambahkan kronologi, nama pembimbing, proses latihan, atau detail lainnya.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>

{{-- MODAL TAMBAH MURID --}}
<div class="modal fade" id="modalTambahMurid" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('prestasi.murid.store', $prestasi->id) }}" method="POST" id="formTambahMurid">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-plus me-2"></i>Tambah Murid Peraih Prestasi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">

                    {{-- Peran --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Peran dalam Prestasi</label>
                        <input type="text" name="peran" class="form-control" value="Peserta"
                            placeholder="Peserta, Ketua Tim, Anggota, Perwakilan..." maxlength="100">
                    </div>

                    {{-- Search murid --}}
                    <label class="form-label fw-semibold small">Cari & Pilih Murid</label>
                    <div class="input-group mb-2">
                        <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="inputCariMurid" class="form-control border-start-0"
                            placeholder="Cari nama atau NIS murid...">
                        <button type="button" class="btn btn-outline-secondary" onclick="cariMurid()">Cari</button>
                    </div>

                    {{-- Info --}}
                    <div class="alert alert-info py-2 mb-2" style="font-size:.8rem;" id="murid-search-info">
                        <i class="bi bi-info-circle me-1"></i>
                        Menampilkan <strong id="murid-count-shown">{{ min(10, $totalMurid) }}</strong>
                        dari <strong id="murid-count-total">{{ $totalMurid }}</strong> murid.
                        Ketik nama atau NIS untuk mencari lebih banyak.
                    </div>

                    {{-- Hidden inputs untuk murid terpilih --}}
                    <div id="selected-murid-inputs"></div>

                    {{-- Chip murid terpilih --}}
                    <div id="selected-murid-chips" class="mb-2 d-flex flex-wrap gap-1"></div>

                    {{-- Hasil pencarian --}}
                    <div id="murid-search-result" style="max-height:280px;overflow-y:auto;"></div>

                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4" id="btnSimpanMurid" disabled>
                        <i class="bi bi-save me-1"></i>Tambahkan
                        <span id="badge-selected" class="badge bg-white text-primary ms-1">0</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH CATATAN --}}
<div class="modal fade" id="modalTambahCatatan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('prestasi.catatan.store', $prestasi->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-journal-plus me-2"></i>Tambah Catatan Prestasi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Judul <span class="text-danger">*</span></label>
                        <input type="text" name="judul" class="form-control" required maxlength="255"
                            placeholder="Kronologi Pencapaian, Nama Pembimbing, Proses Latihan, Catatan Juri...">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Isi Catatan <span class="text-danger">*</span></label>
                        <textarea name="isi" id="isi_catatan" class="form-control" rows="8" required
                            placeholder="Tulis catatan di sini...&#10;&#10;Contoh:&#10;- Pembimbing: Pak Budi (Guru Matematika)&#10;- Proses latihan: 3x seminggu selama 2 bulan&#10;- Catatan khusus: Lolos seleksi 3 tahap"></textarea>
                        <div class="form-text"><i class="bi bi-keyboard me-1"></i>Teks bebas. Enter untuk baris baru.</div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4"><i class="bi bi-save me-1"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL EDIT CATATAN --}}
<div class="modal fade" id="modalEditCatatan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <form id="formEditCatatan" method="POST">
                @csrf @method('PUT')
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Catatan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Judul <span class="text-danger">*</span></label>
                        <input type="text" name="judul" id="edit_judul_catatan" class="form-control" required maxlength="255">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Isi Catatan <span class="text-danger">*</span></label>
                        <textarea name="isi" id="edit_isi_catatan" class="form-control" rows="8" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4"><i class="bi bi-save me-1"></i>Perbarui</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const SEARCH_URL  = "{{ route('prestasi.search-murid') }}";
const MURID_IDS_EXISTING = @json($muridIds);
let selectedMuridIds = [];

$(document).ready(function () {
    // Sidebar
    function toggleSidebar() {
        if ($(window).width() <= 768) { $('#sidebar').toggleClass('show-mobile'); $('#overlay').toggleClass('active'); }
        else { $('#sidebar').toggleClass('inactive'); }
    }
    $('#sidebarCollapse, #close-sidebar, #overlay').on('click', toggleSidebar);

    // Load murid awal saat modal dibuka
    $('#modalTambahMurid').on('show.bs.modal', function () {
        selectedMuridIds = [];
        $('#selected-murid-inputs').empty();
        $('#selected-murid-chips').empty();
        $('#badge-selected').text('0');
        $('#btnSimpanMurid').prop('disabled', true);
        $('#inputCariMurid').val('');
        loadMurid('', 10);
    });

    // Enter key search
    $('#inputCariMurid').on('keypress', function (e) {
        if (e.which === 13) { e.preventDefault(); cariMurid(); }
    });
});

function loadMurid(search, limit) {
    const $container = $('#murid-search-result');
    $container.html('<div class="text-center py-3 text-muted small"><div class="spinner-border spinner-border-sm me-1"></div> Memuat...</div>');

    $.get(SEARCH_URL, { search, limit }, function (res) {
        const shown = res.data.length;
        const total = res.total;

        $('#murid-count-shown').text(shown);
        $('#murid-count-total').text(total);

        let html = '';
        res.data.forEach(m => {
            if (MURID_IDS_EXISTING.includes(m.id)) return; // sudah terdaftar
            const isSel = selectedMuridIds.includes(m.id);
            html += `
                <div class="murid-item ${isSel ? 'selected' : ''}" data-id="${m.id}" data-nama="${m.nama}" onclick="toggleMurid(this, ${m.id}, '${m.nama.replace(/'/g,"\\'")}')">
                    <div class="flex-shrink-0" style="width:34px;height:34px;background:#dbeafe;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-person-fill text-primary"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold" style="font-size:.88rem;">${m.nama}</div>
                        <small class="text-muted">NIS: ${m.nis} | Kelas: ${m.kelas}</small>
                    </div>
                    ${isSel ? '<i class="bi bi-check-circle-fill text-success"></i>' : ''}
                </div>`;
        });

        if (!html) {
            html = '<p class="text-muted text-center py-3 small mb-0"><i class="bi bi-person-x d-block fs-4 mb-1"></i>Tidak ada murid ditemukan.</p>';
        }

        $container.html(html);

        // Tombol lihat semua jika ada lebih
        if (total > shown && search === '') {
            $container.append(`
                <button type="button" class="btn btn-outline-primary btn-sm w-100 mt-2" onclick="loadMurid('', ${total})">
                    <i class="bi bi-chevron-down me-1"></i>Tampilkan semua ${total} murid
                </button>`);
        }
    });
}

function cariMurid() {
    const q = $('#inputCariMurid').val().trim();
    loadMurid(q, q ? 50 : 10);
}

function toggleMurid(el, id, nama) {
    const idx = selectedMuridIds.indexOf(id);
    if (idx === -1) {
        selectedMuridIds.push(id);
        $(el).addClass('selected').find('.bi-person-fill').after('<i class="bi bi-check-circle-fill text-success ms-auto"></i>');
        // Tambah chip
        $('#selected-murid-chips').append(
            `<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1" id="chip-${id}">
                ${nama} <span style="cursor:pointer;" onclick="toggleMuridById(${id})">&times;</span>
            </span>`
        );
        // Tambah hidden input
        $('#selected-murid-inputs').append(`<input type="hidden" name="id_murids[]" value="${id}" id="inp-${id}">`);
    } else {
        selectedMuridIds.splice(idx, 1);
        $(el).removeClass('selected').find('.bi-check-circle-fill').remove();
        $(`#chip-${id}`).remove();
        $(`#inp-${id}`).remove();
    }
    const cnt = selectedMuridIds.length;
    $('#badge-selected').text(cnt);
    $('#btnSimpanMurid').prop('disabled', cnt === 0);
}

function toggleMuridById(id) {
    const $el = $(`[data-id="${id}"]`);
    if ($el.length) toggleMurid($el[0], id, '');
    else {
        const idx = selectedMuridIds.indexOf(id);
        if (idx !== -1) {
            selectedMuridIds.splice(idx, 1);
            $(`#chip-${id}`).remove();
            $(`#inp-${id}`).remove();
            const cnt = selectedMuridIds.length;
            $('#badge-selected').text(cnt);
            $('#btnSimpanMurid').prop('disabled', cnt === 0);
        }
    }
}

// Toggle kolom murid vs sekolah saat pilih jenis
function toggleJenis(jenis) {
    if (jenis === 'murid') {
        $('#kolom-murid').show();
        $('#kolom-sekolah').hide();
        $('#row-nama-tim').hide();
    } else {
        $('#kolom-murid').hide();
        $('#kolom-sekolah').show();
        $('#row-nama-tim').show();
    }
}

// Lihat semua murid di tabel
function lihatSemuaMurid() {
    document.querySelectorAll('.murid-extra').forEach(r => r.classList.remove('d-none'));
    const bar = document.getElementById('murid-info-bar');
    if (bar) bar.remove();
}

// Modal edit catatan
function openEditCatatan(catatanId) {
    $('#formEditCatatan').attr('action', `/informasi/prestasi/{{ $prestasi->id }}/catatan/${catatanId}`);
    $('#edit_judul_catatan').val('');
    $('#edit_isi_catatan').val('');
    $('#modalEditCatatan').modal('show');

    $.get(`{{ url("informasi/prestasi/{$prestasi->id}/catatan") }}/${catatanId}`, function (d) {
        $('#edit_judul_catatan').val(d.judul);
        $('#edit_isi_catatan').val(d.isi);
    });
}
</script>
</body>
</html>
