<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Kelas {{ $kelas->nama_kelas }}</title>
    @if(isset($sekolah->logo))
    <link rel="icon" type="image/png" href="{{ asset($sekolah->logo) }}">
    @else
    <link rel="icon" type="image/png" href="{{ asset('assets/img/default-favicon.png') }}">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Inter', sans-serif; }
        .wrapper { display: flex; width: 100%; }
        #content { width: 100%; padding: 20px 30px; }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .btn-success { background-color: #198754; border: none; }
        .student-row:hover { background-color: #f8f9fa; }
        .wali-badge {
            display: inline-flex;
            align-items: flex-start;
            gap: 10px;
            background: #e8f5e9;
            border: 1px solid #a5d6a7;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 0.9rem;
            width: 100%;
        }
        .guru-row {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 0.88rem;
            margin-top: 8px;
        }
        .mapel-card {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 15px;
            padding: 18px 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
            height: 100%;
        }
        .mapel-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f0faf4;
            border: 1px solid #b2dfdb;
            border-radius: 20px;
            padding: 5px 12px;
            font-size: 0.82rem;
            color: #1b5e20;
            margin: 3px 3px 3px 0;
        }
        @media (max-width: 768px) { #content { padding: 15px; } }
    </style>
</head>
<body>
    <div class="wrapper">
        @include('admin.sidebar')

        <div id="content">
            <div class="container-fluid">
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('kelas.index') }}" class="text-success"><i class="bi bi-arrow-left"></i> Kembali</a></li>
                        <li class="breadcrumb-item active">{{ $kelas->nama_kelas }}</li>
                    </ol>
                </nav>

                {{-- Header: Judul + Tombol --}}
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <h3 class="fw-bold text-success mb-0"><i class="bi bi-door-open me-2"></i>Kelas: {{ $kelas->nama_kelas }}</h3>
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-outline-success px-4" data-bs-toggle="modal" data-bs-target="#modalWaliKelas">
                            <i class="bi bi-person-badge me-2"></i>+ Wali Kelas
                        </button>
                        <button class="btn btn-success px-4" data-bs-toggle="modal" data-bs-target="#modalTambahMurid">
                            <i class="bi bi-person-plus me-2"></i>+ Tambah Murid
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
                    <div class="col-lg-7">

                        {{-- Wali Kelas --}}
                        @if($kelas->waliKelas)
                        @php
                            // Mapel yang diajarkan wali kelas di kelas ini
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
                            <form action="{{ route('kelas.removeWaliKelas', $kelas->id) }}" method="POST" class="m-0 flex-shrink-0">
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
                    <div class="col-lg-5">
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

                <div class="card p-4">
                    <h6 class="fw-bold mb-4">Daftar Murid Terdaftar</h6>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Murid</th>
                                    <th>NISN</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kelas->murid as $index => $m)
                                <tr class="student-row">
                                    <td>{{ $index + 1 }}</td>
                                    <td class="fw-bold">{{ $m->nama_lengkap }}</td>
                                    <td>{{ $m->nisn }}</td>
                                    <td class="text-center">
                                        <form action="{{ route('kelas.removeStudent', $m->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Keluarkan murid ini?')">
                                                <i class="bi bi-x-circle me-1"></i> Keluarkan
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center p-5 text-muted">Belum ada murid di kelas ini.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== MODAL TAMBAH MURID ===== --}}
    <div class="modal fade" id="modalTambahMurid" tabindex="-1">
        <div class="modal-dialog modal-md modal-dialog-scrollable">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-plus me-2"></i>Pilih Murid Untuk Kelas Ini</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">

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
                            Gunakan kolom pencarian untuk menemukan murid lainnya.
                        </div>
                    </div>
                    @endif

                    {{-- Daftar murid --}}
                    <div id="muridList" style="overflow-x: hidden;">
                        @forelse($muridTersedia as $index => $mt)
                        <div class="student-select-item p-3 border rounded mb-2 align-items-center justify-content-between {{ $index >= 5 ? 'd-none' : 'd-flex' }}"
                             data-name="{{ strtolower($mt->nama_lengkap) }} {{ $mt->nisn }}"
                             data-index="{{ $index }}">
                            <div>
                                <div class="fw-bold text-dark">{{ $mt->nama_lengkap }}</div>
                                <small class="text-muted"><i class="bi bi-person-badge me-1"></i>NISN: {{ $mt->nisn }}</small>
                            </div>
                            <form action="{{ route('kelas.addStudent') }}" method="POST" class="m-0">
                                @csrf
                                <input type="hidden" name="id_kelas" value="{{ $kelas->id }}">
                                <input type="hidden" name="id_murid" value="{{ $mt->id }}">
                                <button type="submit" class="btn btn-sm btn-success px-3 fw-medium">Pilih</button>
                            </form>
                        </div>
                        @empty
                        <div class="text-center py-5">
                            <i class="bi bi-person-check fs-1 text-muted opacity-25"></i>
                            <p class="text-muted mt-2 mb-0">Semua murid yang terdaftar sudah memiliki kelas.</p>
                        </div>
                        @endforelse

                        {{-- Pesan tidak ditemukan saat search --}}
                        <div id="muridNotFound" class="text-center py-4 d-none">
                            <i class="bi bi-search fs-1 text-muted opacity-25"></i>
                            <p class="text-muted mt-2 mb-0">Murid tidak ditemukan.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== MODAL WALI KELAS ===== --}}
    <div class="modal fade" id="modalWaliKelas" tabindex="-1">
        <div class="modal-dialog modal-md modal-dialog-scrollable">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-badge me-2"></i>Pilih Wali Kelas</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">

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
                            Gunakan kolom pencarian untuk menemukan guru lainnya.
                        </div>
                    </div>
                    @endif

                    {{-- Daftar guru --}}
                    <div id="guruList" style="overflow-x: hidden;">
                        @forelse($semuaGuru as $index => $guru)
                        <div class="guru-select-item p-3 border rounded mb-2 align-items-center justify-content-between {{ $index >= 5 ? 'd-none' : 'd-flex' }}"
                             data-name="{{ strtolower($guru->nama_guru) }} {{ strtolower($guru->mapel ?? '') }}"
                             data-index="{{ $index }}">
                            <div>
                                <div class="fw-bold text-dark">{{ $guru->nama_guru }}</div>
                                <small class="text-muted">
                                    <i class="bi bi-book me-1"></i>
                                    {{ $guru->mapel ?? '-' }}
                                </small>
                            </div>
                            <form action="{{ route('kelas.setWaliKelas', $kelas->id) }}" method="POST" class="m-0">
                                @csrf
                                <input type="hidden" name="id_guru" value="{{ $guru->id }}">
                                <button type="submit" class="btn btn-sm btn-success px-3 fw-medium">Pilih</button>
                            </form>
                        </div>
                        @empty
                        <div class="text-center py-5">
                            <i class="bi bi-person-x fs-1 text-muted opacity-25"></i>
                            <p class="text-muted mt-2 mb-0">Belum ada data guru.</p>
                        </div>
                        @endforelse

                        {{-- Pesan tidak ditemukan saat search --}}
                        <div id="guruNotFound" class="text-center py-4 d-none">
                            <i class="bi bi-search fs-1 text-muted opacity-25"></i>
                            <p class="text-muted mt-2 mb-0">Guru tidak ditemukan.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ===== FUNGSI PENCARIAN GENERIK =====
        function setupSearch(inputId, itemClass, notFoundId) {
            document.getElementById(inputId).addEventListener('keyup', function () {
                const filter = this.value.toLowerCase().trim();
                const items = document.querySelectorAll('.' + itemClass);
                let visibleCount = 0;

                items.forEach(function (item) {
                    const text  = item.getAttribute('data-name');
                    const index = parseInt(item.getAttribute('data-index'));

                    if (filter.length > 0) {
                        // Mode pencarian: tampilkan semua yang cocok
                        if (text.includes(filter)) {
                            item.classList.remove('d-none');
                            item.classList.add('d-flex');
                            visibleCount++;
                        } else {
                            item.classList.remove('d-flex');
                            item.classList.add('d-none');
                        }
                    } else {
                        // Mode default: kembalikan ke 5 baris pertama saja
                        if (index < 5) {
                            item.classList.remove('d-none');
                            item.classList.add('d-flex');
                            visibleCount++;
                        } else {
                            item.classList.remove('d-flex');
                            item.classList.add('d-none');
                        }
                    }
                });

                // Tampilkan pesan "tidak ditemukan" jika 0 hasil
                const notFound = document.getElementById(notFoundId);
                if (notFound) {
                    if (filter.length > 0 && visibleCount === 0) {
                        notFound.classList.remove('d-none');
                    } else {
                        notFound.classList.add('d-none');
                    }
                }
            });
        }

        setupSearch('searchMurid', 'student-select-item', 'muridNotFound');
        setupSearch('searchGuru',  'guru-select-item',    'guruNotFound');

        // Reset input pencarian ketika modal ditutup
        document.getElementById('modalTambahMurid').addEventListener('hidden.bs.modal', function () {
            const input = document.getElementById('searchMurid');
            input.value = '';
            input.dispatchEvent(new Event('keyup'));
        });
        document.getElementById('modalWaliKelas').addEventListener('hidden.bs.modal', function () {
            const input = document.getElementById('searchGuru');
            input.value = '';
            input.dispatchEvent(new Event('keyup'));
        });
    </script>
</body>
</html>
