<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Jadwal Mengajar</title>
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
        body { font-family: 'Inter', sans-serif; background-color: #f4f7f6; overflow-x: hidden; }
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        #content { width: 100%; padding: 20px 30px; transition: all 0.3s; min-height: 100vh; min-width: 0; }
        #sidebarCollapse { width: 45px; height: 45px; background: var(--primary-green); border: none; color: white; border-radius: 10px; box-shadow: 0 4px 10px rgba(25,135,84,0.2); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        input:focus, textarea:focus, select:focus { border-color: #198754 !important; outline: none !important; box-shadow: 0 0 0 0.2rem rgba(25,135,84,0.25) !important; }
        #overlay { display: none; position: fixed; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 1040; top: 0; left: 0; }
        #overlay.active { display: block; }

        /* Tabel jadwal per hari */
        .hari-section { margin-bottom: 1.5rem; }
        .hari-header {
            background: var(--primary-green);
            color: white;
            padding: 8px 16px;
            border-radius: 8px 8px 0 0;
            font-weight: 600;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .hari-header .badge-count {
            background: rgba(255,255,255,0.25);
            color: white;
            border-radius: 50px;
            padding: 1px 10px;
            font-size: 0.78rem;
        }
        .table-jadwal { border-radius: 0 0 8px 8px; overflow: hidden; margin-bottom: 0; }
        .table-jadwal thead { background-color: #f0f9f4; }
        .table-jadwal thead th { font-size: 0.8rem; font-weight: 600; color: #555; border-top: none; white-space: nowrap; }
        .table-jadwal tbody tr:hover { background-color: #f8fff9; }
        .jam-badge { background: #e8f5e9; color: #2e7d32; border-radius: 6px; padding: 3px 10px; font-size: 0.82rem; font-weight: 600; white-space: nowrap; display: inline-block; }
        .empty-hari { background: #f8f9fa; padding: 12px 16px; border-radius: 0 0 8px 8px; color: #adb5bd; font-size: 0.85rem; text-align: center; border: 1px solid #dee2e6; border-top: none; }

        /* Filter bar */
        .filter-bar { background: white; border-radius: 12px; padding: 16px 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 1.5rem; }

        /* Card jadwal mobile */
        .jadwal-card-mobile { display: none; }
        .jadwal-card-item {
            background: #fff; border-radius: 10px; padding: 12px 14px;
            margin-bottom: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.06);
            border-left: 3px solid #198754;
        }
        .jadwal-card-item .jc-jam { font-weight: 700; color: #14532d; font-size: 0.92rem; }
        .jadwal-card-item .jc-mapel { font-weight: 600; font-size: 0.9rem; color: #1e293b; }
        .jadwal-card-item .jc-meta { font-size: 0.78rem; color: #6c757d; margin-top: 3px; }
        .jadwal-card-item .jc-actions { display: flex; gap: 6px; margin-top: 8px; }
        .jadwal-card-item .jc-actions .btn { flex: 1; font-size: 0.8rem; }

        /* ── Responsive ── */
        @media (max-width: 991px) {
            #content { padding: 16px 18px; }
            .filter-bar .row { row-gap: 8px; }
        }
        @media (max-width: 767px) {
            #content { padding: 12px 12px; }
            .filter-bar { padding: 12px 14px; }
            /* Sembunyikan tabel, tampilkan card mobile */
            .table-jadwal-desktop { display: none !important; }
            .jadwal-card-mobile { display: block; }
            /* Header proporsional */
            .page-header { flex-direction: column; align-items: flex-start !important; gap: 10px; }
            .page-header .btn-tambah { width: 100%; }
            /* Filter stack vertikal */
            .filter-bar .col-auto { flex: 1 1 100%; }
            .filter-bar .col-auto.ms-auto { flex: 0 0 auto; }
            .filter-bar .btn { width: 100%; }
            .filter-bar form { flex-direction: column; }
            /* Hari header */
            .hari-header { font-size: 0.85rem; padding: 7px 12px; }
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
                    <div class="ms-3">
                        <h4 class="mb-0 fw-bold text-success">Jadwal Mengajar</h4>
                        <p class="text-muted small mb-0 d-none d-sm-block">Kelola jadwal mengajar guru per hari dan jam pelajaran</p>
                    </div>
                </div>
                <button class="btn btn-success px-4 shadow-sm fw-bold btn-tambah" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Jadwal
                </button>
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

            {{-- Filter Bar --}}
            <div class="filter-bar">
                <form method="GET" action="{{ route('jadwal-mengajar.index') }}" class="row g-2 align-items-end">
                    <div class="col-12 col-sm-6 col-md-3">
                        <label class="form-label small fw-semibold mb-1">Filter Guru</label>
                        <select name="filter_guru" class="form-select form-select-sm select2-filter">
                            <option value="">Semua Guru</option>
                            @foreach($guruList as $g)
                                <option value="{{ $g->id }}" {{ request('filter_guru') == $g->id ? 'selected' : '' }}>
                                    {{ $g->nama_guru }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <label class="form-label small fw-semibold mb-1">Filter Kelas</label>
                        <select name="filter_kelas" class="form-select form-select-sm select2-filter">
                            <option value="">Semua Kelas</option>
                            @foreach($kelasList as $k)
                                <option value="{{ $k->id }}" {{ request('filter_kelas') == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-md-2">
                        <label class="form-label small fw-semibold mb-1">Filter Hari</label>
                        <select name="filter_hari" class="form-select form-select-sm">
                            <option value="">Semua Hari</option>
                            @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h)
                                <option value="{{ $h }}" {{ request('filter_hari') == $h ? 'selected' : '' }}>{{ $h }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-md-auto d-flex gap-2">
                        <button type="submit" class="btn btn-success btn-sm px-3 flex-fill flex-md-grow-0">
                            <i class="bi bi-funnel me-1"></i>Filter
                        </button>
                        <a href="{{ route('jadwal-mengajar.index') }}" class="btn btn-outline-secondary btn-sm px-3 flex-fill flex-md-grow-0">
                            <i class="bi bi-x-circle me-1"></i>Reset
                        </a>
                    </div>
                    <div class="col-12 col-md-auto ms-md-auto">
                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 d-inline-block w-100 text-center" style="font-size:0.85rem;">
                            <i class="bi bi-calendar3 me-1"></i>Total: {{ $jadwals->count() }} jadwal
                        </span>
                    </div>
                </form>
            </div>

            {{-- Tabel Jadwal Per Hari --}}
            @php
                $hariList = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
                $hariIcon = ['Senin'=>'bi-1-circle','Selasa'=>'bi-2-circle','Rabu'=>'bi-3-circle','Kamis'=>'bi-4-circle','Jumat'=>'bi-5-circle','Sabtu'=>'bi-6-circle'];
            @endphp

            @foreach($hariList as $hari)
                @php $rowsHari = $jadwalPerHari[$hari] ?? collect(); @endphp
                <div class="hari-section">
                    <div class="hari-header">
                        <i class="bi {{ $hariIcon[$hari] }}"></i>
                        {{ $hari }}
                        <span class="badge-count">{{ $rowsHari->count() }} sesi</span>
                    </div>

                    @if($rowsHari->count() > 0)
                    {{-- DESKTOP TABLE --}}
                    <div class="table-responsive table-jadwal-desktop">
                        <table class="table table-jadwal table-hover align-middle mb-0 border">
                            <thead>
                                <tr>
                                    <th width="40">No</th>
                                    <th>Jam</th>
                                    <th>Guru</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Kelas</th>
                                    <th>Ruangan</th>
                                    <th width="100" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rowsHari as $i => $j)
                                <tr>
                                    <td class="text-muted small">{{ $i + 1 }}</td>
                                    <td>
                                        <span class="jam-badge">
                                            {{ \Carbon\Carbon::createFromFormat('H:i:s', $j->jam_mulai)->format('H:i') }}
                                            –
                                            {{ \Carbon\Carbon::createFromFormat('H:i:s', $j->jam_selesai)->format('H:i') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-semibold">{{ $j->guru->nama_guru ?? '-' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1">
                                            {{ $j->mapel->nama_mapel ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info bg-opacity-10 text-info px-2 py-1">
                                            {{ $j->kelas->nama_kelas ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">{{ $j->ruangan ?? '-' }}</td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-success border-0"
                                                title="Edit"
                                                onclick="openEditModal({{ $j->id }})">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger border-0"
                                                title="Hapus"
                                                onclick="hapusJadwal({{ $j->id }}, '{{ addslashes($j->guru->nama_guru ?? '') }}', '{{ $hari }}')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- MOBILE CARD LIST --}}
                    <div class="jadwal-card-mobile px-1 pt-1 pb-1" style="background:#f8f9fa; border:1px solid #dee2e6; border-top:none; border-radius:0 0 8px 8px;">
                        @foreach($rowsHari as $i => $j)
                        <div class="jadwal-card-item mt-2">
                            <div class="d-flex justify-content-between align-items-start">
                                <span class="jam-badge jc-jam">
                                    {{ \Carbon\Carbon::createFromFormat('H:i:s', $j->jam_mulai)->format('H:i') }}
                                    – {{ \Carbon\Carbon::createFromFormat('H:i:s', $j->jam_selesai)->format('H:i') }}
                                </span>
                                @if($j->ruangan)
                                    <span class="badge bg-warning bg-opacity-20 text-dark" style="font-size:0.75rem;">{{ $j->ruangan }}</span>
                                @endif
                            </div>
                            <div class="jc-mapel mt-1">{{ $j->mapel->nama_mapel ?? '-' }}</div>
                            <div class="jc-meta"><i class="bi bi-person me-1"></i>{{ $j->guru->nama_guru ?? '-' }}</div>
                            <div class="jc-meta"><i class="bi bi-mortarboard me-1"></i>{{ $j->kelas->nama_kelas ?? '-' }}</div>
                            <div class="jc-actions">
                                <button class="btn btn-outline-success btn-sm" onclick="openEditModal({{ $j->id }})">
                                    <i class="bi bi-pencil-square me-1"></i>Edit
                                </button>
                                <button class="btn btn-outline-danger btn-sm" onclick="hapusJadwal({{ $j->id }}, '{{ addslashes($j->guru->nama_guru ?? '') }}', '{{ $hari }}')">
                                    <i class="bi bi-trash me-1"></i>Hapus
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    @else
                        <div class="empty-hari">
                            <i class="bi bi-calendar-x me-1"></i>
                            Tidak ada jadwal untuk hari {{ $hari }}
                            @if(request()->hasAny(['filter_guru','filter_kelas','filter_hari']))
                                dengan filter yang dipilih
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach

        </div>{{-- /container --}}
    </div>{{-- /content --}}
</div>{{-- /wrapper --}}


{{-- ══════════════════════════════════════════════
     MODAL: TAMBAH JADWAL
══════════════════════════════════════════════ --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('jadwal-mengajar.store') }}" method="POST" id="formTambah">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>Tambah Jadwal Mengajar</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        {{-- Guru --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Guru <span class="text-danger">*</span></label>
                            <select name="id_guru" id="tambah_guru" class="form-select select2-guru" required>
                                <option value="">-- Pilih Guru --</option>
                                @foreach($guruList as $g)
                                    <option value="{{ $g->id }}">{{ $g->nama_guru }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- Mata Pelajaran --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Mata Pelajaran <span class="text-danger">*</span></label>
                            <select name="id_mapel" id="tambah_mapel" class="form-select select2-mapel" required disabled>
                                <option value="">-- Pilih Guru Dulu --</option>
                            </select>
                        </div>
                        {{-- Kelas --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Kelas <span class="text-danger">*</span></label>
                            <select name="id_kelas" id="tambah_kelas" class="form-select select2-kelas" required disabled>
                                <option value="">-- Pilih Mapel Dulu --</option>
                            </select>
                        </div>
                        {{-- Hari --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Hari <span class="text-danger">*</span></label>
                            <select name="hari" class="form-select" required>
                                <option value="">-- Pilih Hari --</option>
                                @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h)
                                    <option value="{{ $h }}" {{ old('hari') == $h ? 'selected' : '' }}>{{ $h }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- Jam Mulai --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Jam Mulai <span class="text-danger">*</span></label>
                            <input type="time" name="jam_mulai" class="form-control" value="{{ old('jam_mulai') }}" required>
                        </div>
                        {{-- Jam Selesai --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Jam Selesai <span class="text-danger">*</span></label>
                            <input type="time" name="jam_selesai" class="form-control" value="{{ old('jam_selesai') }}" required>
                        </div>
                        {{-- Ruangan --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Ruangan <span class="text-muted fw-normal">(opsional)</span></label>
                            <input type="text" name="ruangan" class="form-control" placeholder="Contoh: Kelas 7A, Lab IPA" value="{{ old('ruangan') }}" maxlength="100">
                        </div>
                    </div>
                    {{-- Info --}}
                    <div class="alert alert-info py-2 mt-3 mb-0" style="font-size:0.82rem;">
                        <i class="bi bi-info-circle me-1"></i>
                        Sistem akan otomatis mendeteksi jika terdapat <strong>benturan jadwal</strong> antar guru maupun kelas.
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4 shadow-sm">
                        <i class="bi bi-save me-1"></i>Simpan Jadwal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════
     MODAL: EDIT JADWAL
══════════════════════════════════════════════ --}}
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <form id="formEdit" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Jadwal Mengajar</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4" id="editModalBody">
                    <div class="text-center py-5">
                        <div class="spinner-border text-success"></div>
                        <p class="mt-2 text-muted small">Memuat data...</p>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4 shadow-sm">
                        <i class="bi bi-save me-1"></i>Perbarui Jadwal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Form hapus (hidden) --}}
<form id="formHapus" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function () {

    // ── Sidebar Toggle ──────────────────────────────────────────────
    function toggleSidebar() {
        if ($(window).width() <= 768) {
            $('#sidebar').toggleClass('show-mobile');
            $('#overlay').toggleClass('active');
        } else {
            $('#sidebar').toggleClass('inactive');
        }
    }
    $('#sidebarCollapse, #close-sidebar, #overlay').on('click', toggleSidebar);

    // ── Select2 untuk filter --
    $('.select2-filter').select2({ theme: 'bootstrap-5', width: '100%', placeholder: 'Semua' });

    // ── Select2 untuk modal tambah --
    function initSelect2Modal(prefix) {
        $(`#${prefix}_guru`).select2({ theme: 'bootstrap-5', width: '100%', dropdownParent: $(`#modalTambah`), placeholder: '-- Pilih Guru --' });
        $(`#${prefix}_mapel`).select2({ theme: 'bootstrap-5', width: '100%', dropdownParent: $(`#modalTambah`), placeholder: '-- Pilih Guru Dulu --' });
        $(`#${prefix}_kelas`).select2({ theme: 'bootstrap-5', width: '100%', dropdownParent: $(`#modalTambah`), placeholder: '-- Pilih Mapel Dulu --' });
    }
    initSelect2Modal('tambah');

    // ── Dropdown dinamis: Guru → Mapel → Kelas (modal TAMBAH) ──────
    $('#tambah_guru').on('change', function () {
        const guruId = $(this).val();
        const $mapel = $('#tambah_mapel');
        const $kelas = $('#tambah_kelas');

        $mapel.empty().append('<option value="">-- Memuat... --</option>').prop('disabled', true);
        $kelas.empty().append('<option value="">-- Pilih Mapel Dulu --</option>').prop('disabled', true);

        if (!guruId) {
            $mapel.empty().append('<option value="">-- Pilih Guru Dulu --</option>');
            return;
        }

        $.get(`{{ url('jadwal-mengajar/guru') }}/${guruId}/mapel`, function (data) {
            $mapel.empty().append('<option value="">-- Pilih Mapel --</option>');
            data.forEach(m => $mapel.append(`<option value="${m.id}">${m.nama_mapel}</option>`));
            $mapel.prop('disabled', false).trigger('change.select2');
        }).fail(() => {
            $mapel.empty().append('<option value="">Gagal memuat</option>');
        });
    });

    $('#tambah_mapel').on('change', function () {
        const guruId  = $('#tambah_guru').val();
        const mapelId = $(this).val();
        const $kelas  = $('#tambah_kelas');

        $kelas.empty().append('<option value="">-- Memuat... --</option>').prop('disabled', true);

        if (!guruId || !mapelId) {
            $kelas.empty().append('<option value="">-- Pilih Mapel Dulu --</option>');
            return;
        }

        $.get("{{ route('jadwal-mengajar.kelas-by-guru-mapel') }}", { id_guru: guruId, id_mapel: mapelId }, function (data) {
            $kelas.empty().append('<option value="">-- Pilih Kelas --</option>');
            data.forEach(k => $kelas.append(`<option value="${k.id}">${k.nama_kelas}</option>`));
            $kelas.prop('disabled', data.length === 0).trigger('change.select2');
            if (data.length === 0) {
                $kelas.append('<option value="" disabled>Tidak ada kelas terkait</option>');
            }
        }).fail(() => {
            $kelas.empty().append('<option value="">Gagal memuat</option>');
        });
    });

    // Reset modal tambah saat ditutup
    $('#modalTambah').on('hidden.bs.modal', function () {
        document.getElementById('formTambah').reset();
        $('#tambah_mapel').empty().append('<option value="">-- Pilih Guru Dulu --</option>').prop('disabled', true).trigger('change.select2');
        $('#tambah_kelas').empty().append('<option value="">-- Pilih Mapel Dulu --</option>').prop('disabled', true).trigger('change.select2');
    });
});

// ── Buka Modal Edit ────────────────────────────────────────────────
function openEditModal(id) {
    const $body  = $('#editModalBody');
    const $form  = $('#formEdit');
    $form.attr('action', `/jadwal-mengajar/${id}`);

    $body.html(`
        <div class="text-center py-5">
            <div class="spinner-border text-success"></div>
            <p class="mt-2 text-muted small">Memuat data jadwal...</p>
        </div>`);

    $('#modalEdit').modal('show');

    $.get(`{{ url('jadwal-mengajar') }}/${id}`, function (data) {
        const hariOptions = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu']
            .map(h => `<option value="${h}" ${data.hari === h ? 'selected' : ''}>${h}</option>`)
            .join('');

        // Guru options
        const guruOptions = {!! $guruList->map(fn($g) => '{"id":'.$g->id.',"nama":'.json_encode($g->nama_guru).'}')->values()->toJson() !!};
        let guruOpts = '<option value="">-- Pilih Guru --</option>';
        guruOptions.forEach(g => {
            guruOpts += `<option value="${g.id}" ${data.id_guru == g.id ? 'selected' : ''}>${g.nama}</option>`;
        });

        $body.html(`
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Guru <span class="text-danger">*</span></label>
                    <select name="id_guru" id="edit_guru" class="form-select" required>${guruOpts}</select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Mata Pelajaran <span class="text-danger">*</span></label>
                    <select name="id_mapel" id="edit_mapel" class="form-select" required>
                        <option value="${data.id_mapel}">${data.mapel ? data.mapel.nama_mapel : 'Mapel #'+data.id_mapel}</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Kelas <span class="text-danger">*</span></label>
                    <select name="id_kelas" id="edit_kelas" class="form-select" required>
                        <option value="${data.id_kelas}">${data.kelas ? data.kelas.nama_kelas : 'Kelas #'+data.id_kelas}</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Hari <span class="text-danger">*</span></label>
                    <select name="hari" class="form-select" required>${hariOptions}</select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Jam Mulai <span class="text-danger">*</span></label>
                    <input type="time" name="jam_mulai" class="form-control"
                           value="${data.jam_mulai ? data.jam_mulai.substring(0,5) : ''}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Jam Selesai <span class="text-danger">*</span></label>
                    <input type="time" name="jam_selesai" class="form-control"
                           value="${data.jam_selesai ? data.jam_selesai.substring(0,5) : ''}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Ruangan <span class="text-muted fw-normal">(opsional)</span></label>
                    <input type="text" name="ruangan" class="form-control"
                           value="${data.ruangan ?? ''}" maxlength="100" placeholder="Contoh: Lab IPA">
                </div>
            </div>`);

        // Init select2 edit
        $('#edit_guru, #edit_mapel, #edit_kelas').select2({
            theme: 'bootstrap-5', width: '100%',
            dropdownParent: $('#modalEdit')
        });

        // Load mapel saat guru berubah
        $('#edit_guru').on('change', function () {
            const gid = $(this).val();
            const $m  = $('#edit_mapel');
            $m.empty().append('<option value="">-- Memuat... --</option>');
            if (!gid) return;
            $.get(`{{ url('jadwal-mengajar/guru') }}/${gid}/mapel`, function(mapels) {
                $m.empty().append('<option value="">-- Pilih Mapel --</option>');
                mapels.forEach(m => $m.append(`<option value="${m.id}">${m.nama_mapel}</option>`));
                $m.trigger('change.select2');
            });
        });

        // Load kelas saat mapel berubah
        $('#edit_mapel').on('change', function () {
            const gid = $('#edit_guru').val();
            const mid = $(this).val();
            const $k  = $('#edit_kelas');
            $k.empty().append('<option value="">-- Memuat... --</option>');
            if (!gid || !mid) return;
            $.get("{{ route('jadwal-mengajar.kelas-by-guru-mapel') }}", { id_guru: gid, id_mapel: mid }, function(kelas) {
                $k.empty().append('<option value="">-- Pilih Kelas --</option>');
                kelas.forEach(k => $k.append(`<option value="${k.id}">${k.nama_kelas}</option>`));
                $k.trigger('change.select2');
            });
        });

    }).fail(function () {
        $body.html('<div class="alert alert-danger">Gagal memuat data jadwal.</div>');
    });
}

// ── Hapus Jadwal ────────────────────────────────────────────────────
function hapusJadwal(id, namaGuru, hari) {
    Swal.fire({
        title: 'Hapus Jadwal?',
        html: `Jadwal mengajar <strong>${namaGuru}</strong> hari <strong>${hari}</strong> akan dihapus permanen.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
    }).then(result => {
        if (result.isConfirmed) {
            const form = document.getElementById('formHapus');
            form.action = `/jadwal-mengajar/${id}`;
            form.submit();
        }
    });
}
</script>
</body>
</html>
