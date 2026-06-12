<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    @if(isset($sekolah->logo))
    <link rel="icon" type="image/png" href="{{ \App\Helpers\ImageHelper::url($sekolah->logo) }}">
    @else
    <link rel="icon" type="image/png" href="{{ asset('assets/img/default-favicon.png') }}">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        :root {
            --green-primary: #198754;
            --green-dark:    #0f5132;
            --green-soft:    #d1e7dd;
            --green-light:   #f0faf5;
            --bg:            #f3f7f5;
            --surface:       #ffffff;
            --border:        #e2ebe6;
            --text-main:     #1a2e25;
            --text-muted:    #6c8f7d;
            --shadow-sm:     0 2px 8px rgba(25,135,84,.08);
            --shadow-md:     0 6px 24px rgba(25,135,84,.12);
            --radius:        14px;
        }

        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text-main); overflow-x: hidden; }

        .wrapper { display: flex; width: 100%; align-items: stretch; }
        #content  { width: 100%; padding: 24px 30px; transition: all .3s; min-height: 100vh; }

        #overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 1040; }
        #overlay.active { display: block; }

        /* ── Sidebar Collapse Btn ── */
        #sidebarCollapse {
            width: 42px; height: 42px;
            background: var(--green-primary); border: none; color: #fff;
            border-radius: 10px; box-shadow: var(--shadow-sm);
            display: flex; align-items: center; justify-content: center;
            transition: background .2s;
        }
        #sidebarCollapse:hover { background: var(--green-dark); }

        /* ── Welcome Banner ── */
        .welcome-banner {
            background: linear-gradient(135deg, #0f5132 0%, #198754 55%, #34d399 100%);
            border-radius: var(--radius);
            padding: 28px 32px;
            color: #fff;
            position: relative;
            overflow: hidden;
        }
        .welcome-banner::after {
            content: '\f26e';
            font-family: 'bootstrap-icons';
            font-size: 9rem;
            position: absolute;
            right: -20px; top: -20px;
            opacity: .07;
            line-height: 1;
        }
        .welcome-banner h2 { font-size: 1.45rem; font-weight: 800; margin-bottom: 4px; }
        .welcome-banner p  { opacity: .82; font-size: .9rem; margin: 0; }
        .clock-pill {
            background: rgba(255,255,255,.15);
            backdrop-filter: blur(6px);
            border: 1px solid rgba(255,255,255,.25);
            border-radius: 40px;
            padding: 7px 18px;
            font-size: .82rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            white-space: nowrap;
        }

        /* ── Stat Card ── */
        .stat-card {
            background: var(--surface);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
            padding: 22px 24px;
            display: flex;
            align-items: center;
            gap: 18px;
            transition: transform .2s, box-shadow .2s;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); }
        .stat-icon {
            width: 52px; height: 52px; flex-shrink: 0;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
        }
        .stat-icon.green  { background: var(--green-light); color: var(--green-primary); }
        .stat-icon.blue   { background: #eff6ff; color: #3b82f6; }
        .stat-icon.purple { background: #f5f3ff; color: #8b5cf6; }
        .stat-icon.orange { background: #fff7ed; color: #f97316; }
        .stat-icon.red    { background: #fef2f2; color: #ef4444; }
        .stat-icon.teal   { background: #f0fdfa; color: #14b8a6; }
        .stat-label { font-size: .78rem; color: var(--text-muted); font-weight: 500; margin-bottom: 3px; }
        .stat-value { font-size: 1.55rem; font-weight: 800; color: var(--text-main); line-height: 1; }
        .stat-sub   { font-size: .75rem; color: var(--text-muted); margin-top: 3px; }

        /* ── Panel / Card ── */
        .panel {
            background: var(--surface);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
            overflow: hidden;
        }
        .panel-header {
            padding: 16px 22px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }
        .panel-title {
            font-size: .92rem;
            font-weight: 700;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .panel-title i { color: var(--green-primary); }
        .panel-body { padding: 20px 22px; }

        /* ── Quick links ── */
        .quick-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 18px 10px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            text-decoration: none;
            color: var(--text-main);
            font-size: .78rem;
            font-weight: 600;
            text-align: center;
            transition: background .2s, transform .2s, border-color .2s;
        }
        .quick-link:hover {
            background: var(--green-light);
            border-color: var(--green-primary);
            color: var(--green-primary);
            transform: translateY(-2px);
        }
        .quick-link i { font-size: 1.5rem; color: var(--green-primary); }

        /* ── Finance balance card ── */
        .balance-card {
            background: linear-gradient(135deg, #0f5132, #198754);
            border-radius: var(--radius);
            padding: 24px;
            color: #fff;
        }
        .balance-card .label { font-size: .8rem; opacity: .78; margin-bottom: 4px; }
        .balance-card .amount { font-size: 1.7rem; font-weight: 800; }
        .finance-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
        }
        .finance-item:last-child { border-bottom: none; }
        .finance-dot {
            width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0;
        }

        /* ── Activity list ── */
        .activity-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 11px 0;
            border-bottom: 1px solid var(--border);
        }
        .activity-item:last-child { border-bottom: none; }
        .activity-avatar {
            width: 38px; height: 38px; border-radius: 50%;
            background: var(--green-light);
            color: var(--green-primary);
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: .9rem; flex-shrink: 0;
        }

        /* ── Artikel card ── */
        .artikel-item {
            display: flex;
            gap: 14px;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
        }
        .artikel-item:last-child { border-bottom: none; }
        .artikel-thumb {
            width: 68px; height: 52px; object-fit: cover; border-radius: 8px; flex-shrink: 0;
        }
        .artikel-no-thumb {
            width: 68px; height: 52px; border-radius: 8px; flex-shrink: 0;
            background: var(--green-light);
            display: flex; align-items: center; justify-content: center;
            color: var(--green-primary); font-size: 1.3rem;
        }

        /* ── Badge status ── */
        .badge-status {
            font-size: .68rem;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 20px;
        }
        .badge-konfirmasi { background: #d1fae5; color: #065f46; }
        .badge-pending    { background: #fef9c3; color: #713f12; }

        /* ── Section label ── */
        .section-label {
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 14px;
            margin-top: 6px;
        }

        @media (max-width: 768px) {
            #content { padding: 15px; }
            .stat-value { font-size: 1.3rem; }
            .welcome-banner h2 { font-size: 1.2rem; }
        }
    </style>
</head>
<body>
<div id="overlay"></div>
<div class="wrapper">
    @include('admin.sidebar')

    <div id="content">
        <div class="container-fluid px-0">

            {{-- ── TOP BAR ── --}}
            <div class="d-flex align-items-center justify-content-between mb-4 mt-1">
                <div class="d-flex align-items-center gap-3">
                    <button type="button" id="sidebarCollapse" class="btn">
                        <i class="bi bi-list fs-4"></i>
                    </button>
                    <div>
                        <h5 class="mb-0 fw-bold">Dashboard</h5>
                        <small class="text-muted">Ringkasan kondisi sekolah hari ini</small>
                    </div>
                </div>
                <span class="clock-pill text-success bg-white border shadow-sm d-none d-md-inline-flex">
                    <i class="bi bi-clock-fill"></i>
                    <span id="realtime-clock">–</span>
                </span>
            </div>

            {{-- ── WELCOME BANNER ── --}}
            <div class="welcome-banner mb-4">
                <div class="row align-items-center">
                    <div class="col-md-7">
                        <h2>Halo, Selamat Datang Admin</h2>
                        <p>Berikut ringkasan data terkini sistem manajemen {{ $sekolah->nama_sekolah ?? 'sekolah' }}.</p>
                    </div>
                    <div class="col-md-5 text-md-end mt-3 mt-md-0">
                        <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                            <a href="{{ route('murid.create') }}" class="btn btn-sm text-white border-white border-opacity-50 rounded-pill px-3" style="background:rgba(255,255,255,.15)">
                                <i class="bi bi-person-plus me-1"></i> Input Murid Baru
                            </a>
                            <a href="{{ route('informasi.index') }}" class="btn btn-sm text-white border-white border-opacity-50 rounded-pill px-3" style="background:rgba(255,255,255,.15)">
                                <i class="bi bi-pencil-square me-1"></i> Kelola Konten
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── STAT CARDS ROW 1: DATA MASTER ── --}}
            <p class="section-label"><i class="bi bi-database me-1"></i> Data Master</p>
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="{{ route('murid.index') }}" class="text-decoration-none">
                        <div class="stat-card">
                            <div class="stat-icon green"><i class="bi bi-people-fill"></i></div>
                            <div>
                                <div class="stat-label">Murid Aktif</div>
                                <div class="stat-value">{{ number_format($totalMuridAktif) }}</div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="{{ route('admin.ppdb.index') }}" class="text-decoration-none">
                        <div class="stat-card">
                            <div class="stat-icon orange"><i class="bi bi-hourglass-split"></i></div>
                            <div>
                                <div class="stat-label">Pendaftar Baru</div>
                                <div class="stat-value">{{ number_format($totalMuridPending) }}</div>
                                <div class="stat-sub">menunggu konfirmasi</div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="{{ route('guru.index') }}" class="text-decoration-none">
                        <div class="stat-card">
                            <div class="stat-icon blue"><i class="bi bi-person-badge-fill"></i></div>
                            <div>
                                <div class="stat-label">Guru Aktif</div>
                                <div class="stat-value">{{ number_format($totalGuruAktif) }}</div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="{{ route('staff.index') }}" class="text-decoration-none">
                        <div class="stat-card">
                            <div class="stat-icon purple"><i class="bi bi-person-workspace"></i></div>
                            <div>
                                <div class="stat-label">Staff Aktif</div>
                                <div class="stat-value">{{ number_format($totalStaffAktif) }}</div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="{{ route('kelas.index') }}" class="text-decoration-none">
                        <div class="stat-card">
                            <div class="stat-icon teal"><i class="bi bi-door-open-fill"></i></div>
                            <div>
                                <div class="stat-label">Total Kelas</div>
                                <div class="stat-value">{{ number_format($totalKelas) }}</div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="{{ route('informasi.index') }}" class="text-decoration-none">
                        <div class="stat-card">
                            <div class="stat-icon green"><i class="bi bi-newspaper"></i></div>
                            <div>
                                <div class="stat-label">Total Artikel</div>
                                <div class="stat-value">{{ number_format($totalArtikel) }}</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            {{-- ── ROW 2: KEUANGAN + GRAFIK ── --}}
            <div class="row g-4 mb-4">

                {{-- Saldo & Keuangan Bulan Ini --}}
                <div class="col-lg-4">
                    <div class="balance-card mb-3">
                        <div class="label">Saldo Keseluruhan</div>
                        <div class="amount">Rp {{ number_format($saldo, 0, ',', '.') }}</div>
                        <small class="opacity-75">pemasukan – pengeluaran</small>
                    </div>

                    <div class="panel">
                        <div class="panel-header">
                            <div class="panel-title"><i class="bi bi-calendar2-month"></i> Bulan Ini</div>
                            <small class="text-muted">{{ now()->isoFormat('MMMM YYYY') }}</small>
                        </div>
                        <div class="panel-body py-2 px-3">
                            <div class="finance-item">
                                <span class="finance-dot" style="background:#198754;"></span>
                                <div class="flex-grow-1">
                                    <div style="font-size:.78rem; color:var(--text-muted);">Pemasukan</div>
                                    <div class="fw-700" style="font-size:.92rem; color:#198754;">
                                        Rp {{ number_format($totalPemasukanBulanIni, 0, ',', '.') }}
                                    </div>
                                </div>
                                <a href="{{ route('keuangan.pemasukan.index') }}" class="btn btn-sm btn-outline-success btn-sm" style="font-size:.72rem;">Detail</a>
                            </div>
                            <div class="finance-item">
                                <span class="finance-dot" style="background:#ef4444;"></span>
                                <div class="flex-grow-1">
                                    <div style="font-size:.78rem; color:var(--text-muted);">Pengeluaran</div>
                                    <div class="fw-700" style="font-size:.92rem; color:#ef4444;">
                                        Rp {{ number_format($totalPengeluaranBulanIni, 0, ',', '.') }}
                                    </div>
                                </div>
                                <a href="{{ route('keuangan.pengeluaran.index') }}" class="btn btn-sm btn-outline-danger" style="font-size:.72rem;">Detail</a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Grafik 6 Bulan --}}
                <div class="col-lg-8">
                    <div class="panel h-100">
                        <div class="panel-header">
                            <div class="panel-title"><i class="bi bi-bar-chart-fill"></i> Arus Kas 6 Bulan Terakhir</div>
                            <a href="{{ route('keuangan.laporan.index') }}" class="btn btn-sm btn-outline-success" style="font-size:.75rem;">
                                <i class="bi bi-file-earmark-bar-graph me-1"></i>Laporan Lengkap
                            </a>
                        </div>
                        <div class="panel-body">
                            <canvas id="cashflowChart" height="180"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── ROW 3: AKTIVITAS TERBARU + ARTIKEL + QUICK LINKS ── --}}
            <div class="row g-4 mb-4">

                {{-- Murid Terbaru --}}
                <div class="col-lg-4">
                    <div class="panel h-100">
                        <div class="panel-header">
                            <div class="panel-title"><i class="bi bi-person-lines-fill"></i> Pendaftar Terbaru</div>
                            <a href="{{ route('murid.index') }}" class="btn btn-sm btn-outline-success" style="font-size:.75rem;">Lihat Semua</a>
                        </div>
                        <div class="panel-body py-2">
                            @forelse($muridTerbaru as $m)
                            <div class="activity-item">
                                <div class="activity-avatar">{{ strtoupper(substr($m->nama_lengkap, 0, 1)) }}</div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="fw-600 text-truncate" style="font-size:.85rem; max-width:160px;">{{ $m->nama_lengkap }}</div>
                                    <small class="text-muted">{{ $m->created_at->diffForHumans() }}</small>
                                </div>
                                <span class="badge-status {{ $m->status === 'konfirmasi' ? 'badge-konfirmasi' : 'badge-pending' }}">
                                    {{ $m->status === 'konfirmasi' ? 'Aktif' : 'Pending' }}
                                </span>
                            </div>
                            @empty
                            <div class="text-center text-muted py-4" style="font-size:.85rem;">
                                <i class="bi bi-inbox display-5 d-block mb-2 opacity-40"></i>
                                Belum ada data murid.
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Artikel Terbaru --}}
                <div class="col-lg-5">
                    <div class="panel h-100">
                        <div class="panel-header">
                            <div class="panel-title"><i class="bi bi-newspaper"></i> Artikel Terbaru</div>
                            <a href="{{ route('informasi.index') }}" class="btn btn-sm btn-outline-success" style="font-size:.75rem;">Kelola</a>
                        </div>
                        <div class="panel-body py-2">
                            @forelse($artikelTerbaru as $art)
                            <div class="artikel-item">
                                @if($art->foto_artikel)
                                    <img src="{{ \App\Helpers\ImageHelper::url($art->foto_artikel) }}" class="artikel-thumb" alt="">
                                @else
                                    <div class="artikel-no-thumb"><i class="bi bi-image"></i></div>
                                @endif
                                <div class="overflow-hidden">
                                    <div class="fw-600 text-truncate" style="font-size:.85rem;">{{ $art->judul }}</div>
                                    <div class="text-muted text-truncate" style="font-size:.76rem;">{{ $art->teaser ?? Str::limit($art->deskripsi, 60) }}</div>
                                    <small class="text-muted">{{ $art->created_at->isoFormat('D MMM YYYY') }}</small>
                                </div>
                            </div>
                            @empty
                            <div class="text-center text-muted py-4" style="font-size:.85rem;">
                                <i class="bi bi-journal-x display-5 d-block mb-2 opacity-40"></i>
                                Belum ada artikel.
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Quick Links --}}
                <div class="col-lg-3">
                    <div class="panel h-100">
                        <div class="panel-header">
                            <div class="panel-title"><i class="bi bi-grid-3x3-gap-fill"></i> Akses Cepat</div>
                        </div>
                        <div class="panel-body">
                            <div class="row g-2">
                                <div class="col-6">
                                    <a href="{{ route('murid.create') }}" class="quick-link">
                                        <i class="bi bi-person-plus-fill"></i>
                                        <span>Input Murid</span>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('admin.ppdb.index') }}" class="quick-link">
                                        <i class="bi bi-bell-fill"></i>
                                        <span>Notif PPDB</span>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('keuangan.pemasukan.index') }}" class="quick-link">
                                        <i class="bi bi-arrow-down-circle-fill"></i>
                                        <span>Pemasukan</span>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('keuangan.pengeluaran.index') }}" class="quick-link">
                                        <i class="bi bi-arrow-up-circle-fill"></i>
                                        <span>Pengeluaran</span>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('kelas.index') }}" class="quick-link">
                                        <i class="bi bi-door-open-fill"></i>
                                        <span>Kelas</span>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('dokumen.index') }}" class="quick-link">
                                        <i class="bi bi-folder2-open"></i>
                                        <span>Dokumen</span>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('guru.index') }}" class="quick-link">
                                        <i class="bi bi-person-badge-fill"></i>
                                        <span>Data Guru</span>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('keuangan.laporan.index') }}" class="quick-link">
                                        <i class="bi bi-bar-chart-fill"></i>
                                        <span>Laporan</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ── ROW 4: STATISTIK KONTEN ── --}}
            <p class="section-label"><i class="bi bi-info-circle me-1"></i> Statistik Konten Website</p>
            <div class="row g-3 mb-2">
                <div class="col-6 col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon green"><i class="bi bi-newspaper"></i></div>
                        <div>
                            <div class="stat-label">Artikel</div>
                            <div class="stat-value">{{ $totalArtikel }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon blue"><i class="bi bi-trophy-fill"></i></div>
                        <div>
                            <div class="stat-label">Prestasi</div>
                            <div class="stat-value">{{ $totalPrestasi }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon purple"><i class="bi bi-camera-fill"></i></div>
                        <div>
                            <div class="stat-label">Dokumentasi</div>
                            <div class="stat-value">{{ $totalKegiatan }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <a href="{{ route('home') }}" target="_blank" class="text-decoration-none">
                        <div class="stat-card">
                            <div class="stat-icon teal"><i class="bi bi-box-arrow-up-right"></i></div>
                            <div>
                                <div class="stat-label">Halaman Publik</div>
                                <div class="stat-value" style="font-size:1rem;">Lihat</div>
                                <div class="stat-sub">buka website</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

        </div>{{-- end container --}}
    </div>{{-- end content --}}
</div>{{-- end wrapper --}}

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // ── Sidebar toggle ────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar     = document.getElementById('sidebar');
        const collapseBtn = document.getElementById('sidebarCollapse');
        const closeBtn    = document.getElementById('close-sidebar');
        const overlay     = document.getElementById('overlay');

        function toggle() {
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('show-mobile');
                overlay.classList.toggle('active');
            } else {
                sidebar.classList.toggle('inactive');
            }
        }

        collapseBtn.addEventListener('click', toggle);
        if (closeBtn)  closeBtn.addEventListener('click', toggle);
        if (overlay)   overlay.addEventListener('click', toggle);
    });

    // ── Jam real-time ─────────────────────────────────────────────
    function updateClock() {
        const now = new Date();
        const date = now.toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' });
        const time = now.toLocaleTimeString('id-ID', { hour12: false });
        const el = document.getElementById('realtime-clock');
        if (el) el.textContent = date + ' • ' + time;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // ── Grafik Arus Kas ───────────────────────────────────────────
    const grafikData = @json($grafik);

    const ctx = document.getElementById('cashflowChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: grafikData.map(d => d.label),
            datasets: [
                {
                    label: 'Pemasukan',
                    data: grafikData.map(d => d.pemasukan),
                    backgroundColor: 'rgba(25, 135, 84, 0.75)',
                    borderRadius: 6,
                    borderSkipped: false,
                },
                {
                    label: 'Pengeluaran',
                    data: grafikData.map(d => d.pengeluaran),
                    backgroundColor: 'rgba(239, 68, 68, 0.65)',
                    borderRadius: 6,
                    borderSkipped: false,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { font: { size: 12 }, usePointStyle: true, pointStyle: 'circle' }
                },
                tooltip: {
                    callbacks: {
                        label: ctx => ' Rp ' + Number(ctx.raw).toLocaleString('id-ID')
                    }
                }
            },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                y: {
                    grid: { color: 'rgba(0,0,0,.05)' },
                    ticks: {
                        font: { size: 11 },
                        callback: val => 'Rp ' + (val >= 1000000
                            ? (val / 1000000).toFixed(1) + 'jt'
                            : val >= 1000 ? (val / 1000).toFixed(0) + 'rb' : val)
                    }
                }
            }
        }
    });
</script>
</body>
</html>
