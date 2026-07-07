<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Kelas {{ $kelas->nama_kelas }}</title>
        @include('favicon')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary-green: #198754; }
        body { background-color: #f4f7f6; font-family: 'Inter', sans-serif; overflow-x: hidden; }
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        #content { width: 100%; padding: 20px 30px; transition: all 0.3s; min-height: 100vh; min-width: 0; }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }

        /* Sidebar collapse button */
        #sidebarCollapse {
            width: 42px; height: 42px; background: var(--primary-green);
            border: none; color: white; border-radius: 10px;
            box-shadow: 0 4px 10px rgba(25,135,84,0.2);
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        #overlay { display: none; position: fixed; width: 100vw; height: 100vh;
            background: rgba(0,0,0,0.5); z-index: 1040; top: 0; left: 0; }
        #overlay.active { display: block; }

        .student-row:hover { background-color: #f8f9fa; }

        /* Wali kelas badge */
        .wali-badge {
            display: flex; align-items: flex-start; gap: 10px;
            background: #e8f5e9; border: 1px solid #a5d6a7;
            border-radius: 10px; padding: 12px 14px;
            font-size: 0.9rem; width: 100%;
        }

        /* Guru row */
        .guru-row {
            display: flex; align-items: flex-start; gap: 10px;
            background: #fff; border: 1px solid #e0e0e0;
            border-radius: 10px; padding: 10px 14px;
            font-size: 0.88rem; margin-top: 8px;
        }

        /* Mapel card */
        .mapel-card {
            background: #fff; border: 1px solid #e0e0e0;
            border-radius: 15px; padding: 18px 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04); height: 100%;
        }
        .mapel-pill {
            display: inline-flex; align-items: center; gap: 6px;
            background: #f0faf4; border: 1px solid #b2dfdb;
            border-radius: 20px; padding: 5px 12px;
            font-size: 0.82rem; color: #1b5e20; margin: 3px 3px 3px 0;
        }

        /* Tabel murid — mobile card */
        .murid-card-mobile { display: none; }
        .murid-card-item {
            background: #fff; border-radius: 12px; padding: 12px 14px;
            margin-bottom: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.06);
            border-left: 4px solid #198754;
        }
        .murid-card-item .mc-name { font-weight: 700; font-size: 0.95rem; color: #1a3a3a; }
        .murid-card-item .mc-nisn { font-size: 0.8rem; color: #6c757d; margin-top: 2px; }

        /* ── Responsive ── */
        @media (max-width: 991px) {
            #content { padding: 16px 18px; }
            .mapel-card { height: auto; }
        }
        @media (max-width: 767px) {
            #content { padding: 12px 12px; }
            /* Header tombol stack */
            .page-header { flex-direction: column; align-items: flex-start !important; gap: 10px; }
            .page-header .btn-group-action { width: 100%; display: flex; gap: 8px; }
            .page-header .btn-group-action .btn { flex: 1; }
            /* Wali badge — tombol hapus mepet bawah di mobile kecil */
            .wali-badge { flex-wrap: wrap; }
            .wali-badge .flex-shrink-0 { margin-left: auto; }
            /* Tabel murid → card mobile */
            .table-murid-desktop { display: none !important; }
            .murid-card-mobile   { display: block; }
            /* Mapel pills lebih kecil */
            .mapel-pill { font-size: 0.78rem; padding: 4px 10px; }
        }
        @media (max-width: 479px) {
            .page-header .btn-group-action { flex-direction: column; }
            .page-header .btn-group-action .btn { width: 100%; }
        }
        .selected-btn {
            min-width: 75px;
            transition: all 0.2s ease;
        }
        .selected-btn:hover {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
            color: white !important;
        }
    </style>
</head>
<body>
    <div id="overlay"></div>
    <div class="wrapper">
        @include('admin.sidebar')

        <div id="content">
            <div class="container-fluid">

                {{-- Breadcrumb + Hamburger --}}
                <div class="d-flex align-items-center gap-3 mb-3 mt-1 flex-wrap">
                    <button type="button" id="sidebarCollapse" class="btn">
                        <i class="bi bi-list fs-4"></i>
                    </button>
                    <nav aria-label="breadcrumb" class="mb-0">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('kelas.index') }}" class="text-success text-decoration-none">
                                    <i class="bi bi-arrow-left me-1"></i>Kembali
                                </a>
                            </li>
                            <li class="breadcrumb-item active">{{ $kelas->nama_kelas }}</li>
                        </ol>
                    </nav>
                </div>

                {{-- Header: Judul + Tombol --}}
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 page-header">
                    <h4 class="fw-bold text-success mb-0">
                        <i class="bi bi-door-open me-2"></i>Kelas: {{ $kelas->nama_kelas }}
                    </h4>
                    <div class="d-flex gap-2 flex-wrap btn-group-action">
                        <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalWaliKelas">
                            <i class="bi bi-person-badge me-1 me-md-2"></i><span>+ Wali Kelas</span>
                        </button>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambahMurid">
                            <i class="bi bi-person-plus me-1 me-md-2"></i><span>+ Tambah Murid</span>
                        </button>
                    </div>
                </div>

                @php
                    // Kumpulkan mapel unik yang ada di kelas ini dari tabel pengajar
                    $mapelDiKelas = $kelas->pengajars
                        ->filter(fn($p) => $p->mapel !== null)
                        ->map(fn($p) => $p->mapel)
                        ->unique('id')
                        ->values();

                    // Kelompokkan pengajar: per guru, kumpulkan mapelnya
                    $pengajarPerGuru = $kelas->pengajars
                        ->filter(fn($p) => $p->guru !== null)
                        ->groupBy('id_guru')
                        ->map(function ($items) {
                            return [
                                'guru'   => $items->first()->guru,
                                'mapels' => $items->filter(fn($p) => $p->mapel !== null)
                                                  ->map(fn($p) => $p->mapel->nama_mapel)
                                                  ->unique()
                                                  ->values(),
                            ];
                        })
                        ->values();
                @endphp

                {{-- Layout dua kolom: Kiri = wali kelas + guru, Kanan = card mapel --}}
                <div class="row g-3 mb-4">
                    {{-- Kolom Kiri: Wali Kelas & Daftar Pengajar --}}
                    <div class="col-12 col-lg-7">

                        {{-- Wali Kelas --}}
                        @if($kelas->waliKelas)
                        @php
                            $mapelWali = $kelas->pengajars
                                ->filter(fn($p) => $p->id_guru === $kelas->waliKelas->id && $p->mapel !== null)
                                ->map(fn($p) => $p->mapel->nama_mapel)
                                ->unique()->values();
                        @endphp
                        <div class="wali-badge mb-1">
                            <i class="bi bi-person-check-fill text-success fs-5 mt-1 flex-shrink-0"></i>
                            <div class="flex-grow-1">
                                <div>
                                    <span class="text-muted small">Wali Kelas:</span>
                                    <span class="fw-bold text-dark ms-1">{{ $kelas->waliKelas->nama_guru }}</span>
                                </div>
                                @if($mapelWali->isNotEmpty())
                                <div class="mt-1">
                                    <span class="text-muted small">Mapel: </span>
                                    @foreach($mapelWali as $nm)
                                        <span class="badge bg-success bg-opacity-75 me-1">{{ $nm }}</span>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            <form action="{{ route('kelas.removeWaliKelas', $kelas->uuid) }}" method="POST" class="m-0 flex-shrink-0">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2"
                                        onclick="return confirm('Hapus wali kelas ini?')"
                                        title="Hapus Wali Kelas">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </form>
                        </div>
                        @else
                        <div class="text-muted small mb-2">
                            <i class="bi bi-info-circle me-1"></i>Belum ada wali kelas yang ditetapkan.
                        </div>
                        @endif

                        {{-- Daftar Pengajar --}}
                        @if($pengajarPerGuru->isNotEmpty())
                        <div class="mt-2">
                            <p class="text-muted small fw-semibold mb-1 ms-1">
                                <i class="bi bi-person-workspace me-1"></i>Pengajar di kelas ini:
                            </p>
                            @foreach($pengajarPerGuru as $item)
                            <div class="guru-row">
                                <i class="bi bi-person-fill text-secondary fs-6 mt-1 flex-shrink-0"></i>
                                <div>
                                    <span class="fw-semibold text-dark">{{ $item['guru']->nama_guru }}</span>
                                    @if($item['mapels']->isNotEmpty())
                                    <div class="mt-1">
                                        <span class="text-muted small">Mapel: </span>
                                        @foreach($item['mapels'] as $nm)
                                            <span class="badge bg-secondary bg-opacity-20 text-dark border me-1">{{ $nm }}</span>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-muted small mt-2 ms-1">
                            <i class="bi bi-info-circle me-1"></i>Belum ada pengajar yang ditugaskan di kelas ini.
                        </div>
                        @endif

                    </div>

                    {{-- Kolom Kanan: Card Mapel --}}
                    <div class="col-12 col-md-6 col-lg-5">
                        <div class="mapel-card">
                            <p class="fw-bold text-success mb-3 small">
                                <i class="bi bi-book-fill me-1"></i>Mata Pelajaran di Kelas Ini
                            </p>
                            @if($mapelDiKelas->isNotEmpty())
                                <div>
                                    @foreach($mapelDiKelas as $mp)
                                    <span class="mapel-pill">
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                        {{ $mp->nama_mapel }}
                                    </span>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-3">
                                    <i class="bi bi-journal-x fs-2 text-muted opacity-50 d-block mb-2"></i>
                                    <span class="text-muted small">Belum ada mapel di kelas ini.</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>{{-- end row --}}

                @if(session('success'))
                    <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('success') }}</div>
                @endif

                {{-- Kartu daftar murid --}}
                <div class="card p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-people me-2 text-success"></i>Daftar Murid Terdaftar
                            <span class="badge bg-success bg-opacity-10 text-success ms-1">{{ $kelas->murid->count() }}</span>
                        </h6>
                    </div>

                    {{-- DESKTOP TABLE --}}
                    <div class="table-responsive table-murid-desktop">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th>Nama Murid</th>
                                    <th>NISN</th>
                                    <th width="140" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kelas->murid as $index => $m)
                                <tr class="student-row">
                                    <td>{{ $index + 1 }}</td>
                                    <td class="fw-bold">{{ $m->nama_lengkap }}</td>
                                    <td class="text-muted">{{ $m->nisn }}</td>
                                    <td class="text-center">
                                        <form action="{{ route('kelas.removeStudent', $m->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Keluarkan murid ini dari kelas?')">
                                                <i class="bi bi-x-circle me-1"></i>Keluarkan
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="bi bi-people fs-2 d-block mb-2 opacity-25"></i>
                                        Belum ada murid di kelas ini.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- MOBILE CARD LIST --}}
                    <div class="murid-card-mobile">
                        @forelse($kelas->murid as $index => $m)
                        <div class="murid-card-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="mc-name">{{ $m->nama_lengkap }}</div>
                                    <div class="mc-nisn"><i class="bi bi-credit-card me-1"></i>NISN: {{ $m->nisn }}</div>
                                </div>
                                <span class="badge bg-success bg-opacity-15 text-success">{{ $index + 1 }}</span>
                            </div>
                            <form action="{{ route('kelas.removeStudent', $m->id) }}" method="POST" class="mt-2">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger w-100"
                                        onclick="return confirm('Keluarkan murid ini dari kelas?')">
                                    <i class="bi bi-x-circle me-1"></i>Keluarkan dari Kelas
                                </button>
                            </form>
                        </div>
                        @empty
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-2 d-block mb-2 opacity-25"></i>
                            Belum ada murid di kelas ini.
                        </div>
                        @endforelse
                    </div>

                </div>{{-- end card --}}
            </div>
        </div>
    </div>
    {{-- ===== MODAL TAMBAH MURID ===== --}}
    <div class="modal fade" id="modalTambahMurid" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
            <form action="{{ route('kelas.addStudent') }}" method="POST" class="modal-content border-0 shadow m-0">
                @csrf
                <input type="hidden" name="id_kelas" value="{{ $kelas->id }}">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-plus me-2"></i>Pilih Murid Untuk Kelas Ini</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3 p-md-4">

                    {{-- Search box --}}
                    <div class="input-group mb-3 shadow-sm rounded">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="searchMurid" class="form-control border-start-0 ps-0" placeholder="Cari nama atau NISN...">
                    </div>

                    {{-- Info keterangan tampil 5 --}}
                    @if(count($muridTersedia) > 5)
                    <div class="alert alert-info py-2 px-3 d-flex align-items-center small shadow-sm mb-3" role="alert" style="border-radius: 10px;">
                        <i class="bi bi-info-circle-fill me-2 fs-5 flex-shrink-0"></i>
                        <div>
                            Menampilkan <strong>5 dari {{ count($muridTersedia) }} murid</strong> yang tersedia.
                            Gunakan pencarian untuk menemukan murid lainnya.
                        </div>
                    </div>
                    @endif

                    {{-- Daftar murid --}}
                    <div id="muridList">
                        @forelse($muridTersedia as $index => $mt)
                        <div class="student-select-item p-3 border rounded mb-2 align-items-center justify-content-between {{ $index >= 5 ? 'd-none' : 'd-flex' }}"
                             data-name="{{ strtolower($mt->nama_lengkap) }} {{ $mt->nisn }}"
                             data-index="{{ $index }}">
                            <div class="flex-grow-1 me-2">
                                <div class="fw-bold text-dark">{{ $mt->nama_lengkap }}</div>
                                <small class="text-muted"><i class="bi bi-person-badge me-1"></i>NISN: {{ $mt->nisn }}</small>
                            </div>
                            <div class="flex-shrink-0">
                                <input type="checkbox" name="id_murid[]" value="{{ $mt->id }}" id="chk-murid-{{ $mt->id }}" class="d-none murid-checkbox" onchange="toggleStudentSelection(this, 'btn-murid-{{ $mt->id }}')">
                                <button type="button" id="btn-murid-{{ $mt->id }}" class="btn btn-sm btn-outline-success px-3 fw-medium murid-select-btn" onclick="toggleCheckboxClick('chk-murid-{{ $mt->id }}')">Pilih</button>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-5">
                            <i class="bi bi-person-check fs-1 text-muted opacity-25"></i>
                            <p class="text-muted mt-2 mb-0">Semua murid yang terdaftar sudah memiliki kelas.</p>
                        </div>
                        @endforelse

                        <div id="muridNotFound" class="text-center py-4 d-none">
                            <i class="bi bi-search fs-1 text-muted opacity-25"></i>
                            <p class="text-muted mt-2 mb-0">Murid tidak ditemukan.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" id="btnSimpanMurid" disabled><i class="bi bi-save me-1"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== MODAL WALI KELAS ===== --}}
    <div class="modal fade" id="modalWaliKelas" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
            <form action="{{ route('kelas.setWaliKelas', $kelas->uuid) }}" method="POST" class="modal-content border-0 shadow m-0">
                @csrf
                <input type="hidden" name="id_guru" id="selected_id_guru" value="">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-badge me-2"></i>Pilih Wali Kelas</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3 p-md-4">

                    {{-- Search box --}}
                    <div class="input-group mb-3 shadow-sm rounded">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="searchGuru" class="form-control border-start-0 ps-0" placeholder="Cari nama guru atau mata pelajaran...">
                    </div>

                    {{-- Info keterangan tampil 5 --}}
                    @if(count($semuaGuru) > 5)
                    <div class="alert alert-info py-2 px-3 d-flex align-items-center small shadow-sm mb-3" role="alert" style="border-radius: 10px;">
                        <i class="bi bi-info-circle-fill me-2 fs-5 flex-shrink-0"></i>
                        <div>
                            Menampilkan <strong>5 dari {{ count($semuaGuru) }} guru aktif</strong> yang tersedia.
                            Gunakan pencarian untuk menemukan guru lainnya.
                        </div>
                    </div>
                    @endif

                    {{-- Daftar guru --}}
                    <div id="guruList">
                        @forelse($semuaGuru as $index => $guru)
                        <div class="guru-select-item p-3 border rounded mb-2 align-items-center justify-content-between {{ $index >= 5 ? 'd-none' : 'd-flex' }}"
                             data-name="{{ strtolower($guru->nama_guru) }} {{ strtolower($guru->mapel ?? '') }}"
                             data-index="{{ $index }}">
                            <div class="flex-grow-1 me-2">
                                <div class="fw-bold text-dark">{{ $guru->nama_guru }}</div>
                                <small class="text-muted">
                                    <i class="bi bi-book me-1"></i>{{ $guru->mapel ?? '-' }}
                                </small>
                            </div>
                            <div class="flex-shrink-0">
                                <button type="button" id="btn-guru-{{ $guru->id }}" class="btn btn-sm btn-outline-success px-3 fw-medium guru-select-btn" onclick="selectGuru('{{ $guru->id }}', 'btn-guru-{{ $guru->id }}')">Pilih</button>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-5">
                            <i class="bi bi-person-x fs-1 text-muted opacity-25"></i>
                            <p class="text-muted mt-2 mb-0">Belum ada data guru.</p>
                        </div>
                        @endforelse

                        <div id="guruNotFound" class="text-center py-4 d-none">
                            <i class="bi bi-search fs-1 text-muted opacity-25"></i>
                            <p class="text-muted mt-2 mb-0">Guru tidak ditemukan.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" id="btnSimpanGuru" disabled><i class="bi bi-save me-1"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ── Sidebar Toggle ────────────────────────────────────────────
        (function () {
            const sidebar     = document.getElementById('sidebar');
            const collapseBtn = document.getElementById('sidebarCollapse');
            const overlay     = document.getElementById('overlay');
            const closeBtn    = document.getElementById('close-sidebar');

            function toggleSidebar() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.toggle('show-mobile');
                    overlay.classList.toggle('active');
                } else {
                    sidebar.classList.toggle('inactive');
                }
            }

            if (collapseBtn) collapseBtn.addEventListener('click', toggleSidebar);
            if (closeBtn)    closeBtn.addEventListener('click', function () {
                sidebar.classList.remove('show-mobile');
                overlay.classList.remove('active');
            });
            if (overlay)     overlay.addEventListener('click', toggleSidebar);
        })();

        // ── Selection & Toggle Logic ──────────────────────────────────
        function toggleCheckboxClick(checkboxId) {
            const checkbox = document.getElementById(checkboxId);
            checkbox.checked = !checkbox.checked;
            checkbox.dispatchEvent(new Event('change'));
        }

        function toggleStudentSelection(checkbox, buttonId) {
            const btn = document.getElementById(buttonId);
            if (checkbox.checked) {
                btn.classList.remove('btn-outline-success');
                btn.classList.add('btn-success', 'selected-btn');
                btn.innerHTML = '<i class="bi bi-check-lg"></i>';
            } else {
                btn.classList.remove('btn-success', 'selected-btn', 'btn-danger');
                btn.classList.add('btn-outline-success');
                btn.innerHTML = 'Pilih';
            }
            updateSubmitButtonState('modalTambahMurid', '.murid-checkbox');
        }

        function selectGuru(guruId, buttonId) {
            const hiddenInput = document.getElementById('selected_id_guru');
            const allButtons = document.querySelectorAll('.guru-select-btn');
            const targetBtn = document.getElementById(buttonId);

            if (hiddenInput.value == guruId) {
                // Deselect
                hiddenInput.value = '';
                targetBtn.classList.remove('btn-success', 'selected-btn', 'btn-danger');
                targetBtn.classList.add('btn-outline-success');
                targetBtn.innerHTML = 'Pilih';
            } else {
                // Select this one, deselect others
                hiddenInput.value = guruId;
                allButtons.forEach(btn => {
                    btn.classList.remove('btn-success', 'selected-btn', 'btn-danger');
                    btn.classList.add('btn-outline-success');
                    btn.innerHTML = 'Pilih';
                });
                targetBtn.classList.remove('btn-outline-success');
                targetBtn.classList.add('btn-success', 'selected-btn');
                targetBtn.innerHTML = '<i class="bi bi-check-lg"></i>';
            }
            updateSubmitButtonState('modalWaliKelas', '#selected_id_guru');
        }

        function updateSubmitButtonState(modalId, selector) {
            const modal = document.getElementById(modalId);
            const submitBtn = modal.querySelector('button[type="submit"]');
            if (selector === '#selected_id_guru') {
                const val = document.getElementById('selected_id_guru').value;
                submitBtn.disabled = val === '';
            } else {
                const checkedCount = modal.querySelectorAll('.murid-checkbox:checked').length;
                submitBtn.disabled = checkedCount === 0;
            }
        }

        // Hover events for selected buttons to show 'x'
        document.addEventListener('mouseenter', function(e) {
            if (e.target && e.target.classList.contains('selected-btn')) {
                e.target.classList.remove('btn-success');
                e.target.classList.add('btn-danger');
                e.target.innerHTML = '<i class="bi bi-x-lg"></i>';
            }
        }, true);

        document.addEventListener('mouseleave', function(e) {
            if (e.target && e.target.classList.contains('selected-btn')) {
                e.target.classList.remove('btn-danger');
                e.target.classList.add('btn-success');
                e.target.innerHTML = '<i class="bi bi-check-lg"></i>';
            }
        }, true);

        // ── Pencarian generik (murid & guru) ─────────────────────────
        function setupSearch(inputId, itemClass, notFoundId) {
            document.getElementById(inputId).addEventListener('keyup', function () {
                const filter = this.value.toLowerCase().trim();
                const items  = document.querySelectorAll('.' + itemClass);
                let visible  = 0;

                items.forEach(function (item) {
                    const text  = item.getAttribute('data-name');
                    const index = parseInt(item.getAttribute('data-index'));

                    if (filter.length > 0) {
                        const match = text.includes(filter);
                        item.classList.toggle('d-none', !match);
                        item.classList.toggle('d-flex', match);
                        if (match) visible++;
                    } else {
                        const show = index < 5;
                        item.classList.toggle('d-none', !show);
                        item.classList.toggle('d-flex', show);
                        if (show) visible++;
                    }
                });

                const notFound = document.getElementById(notFoundId);
                if (notFound) {
                    notFound.classList.toggle('d-none', !(filter.length > 0 && visible === 0));
                }
            });
        }

        setupSearch('searchMurid', 'student-select-item', 'muridNotFound');
        setupSearch('searchGuru',  'guru-select-item',    'guruNotFound');

        // Reset pencarian & seleksi saat modal ditutup
        document.getElementById('modalTambahMurid').addEventListener('hidden.bs.modal', function () {
            const inp = document.getElementById('searchMurid');
            inp.value = '';
            inp.dispatchEvent(new Event('keyup'));
            
            const checkboxes = this.querySelectorAll('.murid-checkbox');
            checkboxes.forEach(chk => {
                chk.checked = false;
                chk.dispatchEvent(new Event('change'));
            });
        });
        document.getElementById('modalWaliKelas').addEventListener('hidden.bs.modal', function () {
            const inp = document.getElementById('searchGuru');
            inp.value = '';
            inp.dispatchEvent(new Event('keyup'));

            const hiddenInput = document.getElementById('selected_id_guru');
            hiddenInput.value = '';
            const allButtons = this.querySelectorAll('.guru-select-btn');
            allButtons.forEach(btn => {
                btn.classList.remove('btn-success', 'selected-btn', 'btn-danger');
                btn.classList.add('btn-outline-success');
                btn.innerHTML = 'Pilih';
            });
            updateSubmitButtonState('modalWaliKelas', '#selected_id_guru');
        });
    </script>
</body>
</html>
