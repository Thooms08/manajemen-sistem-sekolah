<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan</title>
    @if(isset($sekolah->logo))
    <link rel="icon" type="image/png" href="{{ \App\Helpers\ImageHelper::url($sekolah->logo) }}">
    @else
    <link rel="icon" type="image/png" href="{{ asset('assets/img/default-favicon.png') }}">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --green:  #198754;
            --red:    #dc3545;
            --blue:   #0d6efd;
            --yellow: #ffc107;
            --teal:   #0dcaf0;
        }
        body { font-family: 'Inter', sans-serif; background: #f4f7f6; overflow-x: hidden; }
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        #content { width: 100%; padding: 20px 30px; min-height: 100vh; min-width: 0; }
        #sidebarCollapse { width: 45px; height: 45px; background: var(--green); border: none; color: white; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        #overlay { display: none; position: fixed; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 1040; top: 0; left: 0; }
        #overlay.active { display: block; }

        /* ── Summary Cards ── */
        .summary-card { border-radius: 14px; transition: transform 0.2s, box-shadow 0.2s; }
        .summary-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.1) !important; }
        .summary-icon { width: 54px; height: 54px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; }
        .summary-label { font-size: 0.78rem; font-weight: 600; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; }
        .summary-value { font-size: 1.2rem; font-weight: 700; line-height: 1.2; word-break: break-word; }
        .summary-sub { font-size: 0.75rem; color: #6c757d; margin-top: 2px; }

        /* ── Filter Bar ── */
        .filter-bar { background: #fff; border-radius: 12px; padding: 14px 18px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .btn-periode { font-size: 0.8rem; padding: 5px 14px; border-radius: 20px; font-weight: 600; }
        .btn-periode.active { background: var(--green); color: #fff; border-color: var(--green); }
        .btn-periode:not(.active) { background: #fff; color: #495057; border-color: #dee2e6; }
        .btn-periode:not(.active):hover { background: #f0faf5; border-color: var(--green); color: var(--green); }
        input:focus, select:focus { border-color: #198754 !important; box-shadow: 0 0 0 0.2rem rgba(25,135,84,0.2) !important; }

        /* ── Chart cards ── */
        .chart-card { border-radius: 14px; }
        .chart-card .chart-title { font-size: 0.88rem; font-weight: 700; color: #344054; }
        .chart-wrapper { position: relative; }

        /* ── Tabel bulanan ── */
        .table thead { background: var(--green); color: #fff; }
        .table thead th { font-size: 0.82rem; font-weight: 600; white-space: nowrap; }
        .badge-surplus  { background: #d1fae5; color: #065f46; font-size: 0.73rem; padding: 3px 10px; border-radius: 20px; font-weight: 600; }
        .badge-defisit  { background: #fee2e2; color: #991b1b; font-size: 0.73rem; padding: 3px 10px; border-radius: 20px; font-weight: 600; }

        /* ── Transaksi terakhir ── */
        .tx-item { border-radius: 10px; padding: 10px 14px; border: 1px solid #f0f0f0; margin-bottom: 8px; transition: background 0.15s; }
        .tx-item:hover { background: #f8f9fa; }
        .tx-amount { font-weight: 700; font-size: 0.9rem; white-space: nowrap; }

        /* ── Saldo card warna ── */
        .saldo-positif { background: linear-gradient(135deg, #d1fae5, #a7f3d0); border: 1px solid #6ee7b7; }
        .saldo-negatif { background: linear-gradient(135deg, #fee2e2, #fecaca); border: 1px solid #fca5a5; }

        /* ── Responsive ── */
        @media (max-width: 991px) {
            #content { padding: 16px 18px; }
            .summary-value { font-size: 1rem; }
        }
        @media (max-width: 767px) {
            #content { padding: 12px 12px; }
            /* Header stack */
            .page-header { flex-direction: column; align-items: flex-start !important; gap: 10px; }
            .page-header .ms-auto { margin-left: 0 !important; }
            .page-header .btn { width: 100%; }
            /* Filter bar: tanggal stack */
            .filter-bar .date-range-row { flex-direction: column; align-items: stretch !important; }
            .filter-bar .date-range-row input[type="date"] { width: 100% !important; max-width: 100% !important; }
            .filter-bar .date-range-row .btn { width: 100%; }
            /* Summary value lebih kecil di xs */
            .summary-value { font-size: 0.95rem; }
            .summary-icon { width: 44px; height: 44px; font-size: 1.2rem; }
            /* Chart wrapper tinggi lebih kecil di mobile */
            .chart-wrapper-tall { height: 220px !important; }
            .chart-wrapper-short { height: 180px !important; }
            /* Tabel font lebih kecil */
            .table thead th, .table tbody td { font-size: 0.78rem; }
            /* Transaksi terakhir row */
            .tx-item { padding: 8px 10px; }
        }
    </style>
</head>
<body>
<div id="overlay"></div>
<div class="wrapper">
    @include('admin.sidebar')

    <div id="content">
        <div class="container-fluid">

            {{-- ══ Header ══ --}}
            <div class="d-flex align-items-center mb-4 mt-2 flex-wrap gap-2 page-header">
                <button type="button" id="sidebarCollapse" class="btn" onclick="toggleSidebar()">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <div class="flex-grow-1">
                    <h4 class="mb-0 fw-bold text-success">
                        <i class="bi bi-bar-chart-line me-2"></i>Laporan Keuangan
                    </h4>
                    <small class="text-muted">
                        Periode:
                        @if($periode === 'custom' && $dateFrom && $dateTo)
                            {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }} — {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}
                        @else
                            {{ $from->format('d M Y') }} — {{ $to->format('d M Y') }}
                        @endif
                    </small>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('keuangan.laporan.export-excel', array_filter(['periode' => $periode, 'date_from' => $dateFrom, 'date_to' => $dateTo])) }}"
                       class="btn btn-success btn-sm px-4 fw-semibold shadow-sm">
                        <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
                    </a>
                </div>
            </div>

            {{-- ══ Filter Periode ══ --}}
            <form method="GET" action="{{ route('keuangan.laporan.index') }}" id="formFilter" class="mb-4">
                <div class="filter-bar">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                        <span class="text-muted small fw-semibold">Periode:</span>
                        @foreach(['1bulan' => '1 Bulan', '3bulan' => '3 Bulan', '6bulan' => '6 Bulan', '1tahun' => '1 Tahun', 'tahunini' => 'Tahun Ini'] as $key => $label)
                            <button type="button"
                                    class="btn btn-periode {{ $periode === $key ? 'active' : '' }}"
                                    onclick="setPeriode('{{ $key }}')">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2 date-range-row">
                        <span class="text-muted small fw-semibold">Atau rentang kustom:</span>
                        <input type="date" name="date_from" id="inputDateFrom"
                               class="form-control form-control-sm flex-fill" style="min-width:130px; max-width:175px;" value="{{ $dateFrom }}">
                        <span class="text-muted small">s/d</span>
                        <input type="date" name="date_to" id="inputDateTo"
                               class="form-control form-control-sm flex-fill" style="min-width:130px; max-width:175px;" value="{{ $dateTo }}">
                        <button type="submit" class="btn btn-outline-success btn-sm px-3 fw-semibold">
                            <i class="bi bi-funnel me-1"></i>Terapkan
                        </button>
                        @if($periode !== '6bulan' || $dateFrom || $dateTo)
                            <a href="{{ route('keuangan.laporan.index') }}" class="btn btn-outline-secondary btn-sm px-3">
                                <i class="bi bi-x-circle me-1"></i>Reset
                            </a>
                        @endif
                    </div>
                    <input type="hidden" name="periode" id="inputPeriode" value="{{ $periode }}">
                </div>
            </form>

            <div class="row g-3 mb-4">

                {{-- Total Pemasukan --}}
                <div class="col-6 col-lg-3">
                    <div class="card summary-card p-3 h-100">
                        <div class="d-flex align-items-center gap-3">
                            <div class="summary-icon bg-success bg-opacity-10">
                                <i class="bi bi-arrow-down-circle text-success"></i>
                            </div>
                            <div class="min-width-0">
                                <div class="summary-label">Total Pemasukan</div>
                                <div class="summary-value text-success text-truncate">
                                    Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
                                </div>
                                <div class="summary-sub">{{ number_format($jumlahTxPemasukan) }} transaksi</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Total Pengeluaran --}}
                <div class="col-6 col-lg-3">
                    <div class="card summary-card p-3 h-100">
                        <div class="d-flex align-items-center gap-3">
                            <div class="summary-icon bg-danger bg-opacity-10">
                                <i class="bi bi-arrow-up-circle text-danger"></i>
                            </div>
                            <div class="min-width-0">
                                <div class="summary-label">Total Pengeluaran</div>
                                <div class="summary-value text-danger text-truncate">
                                    Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                                </div>
                                <div class="summary-sub">{{ number_format($jumlahTxPengeluaran) }} transaksi</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Saldo Bersih --}}
                <div class="col-6 col-lg-3">
                    <div class="card summary-card p-3 h-100 {{ $saldoBersih >= 0 ? 'saldo-positif' : 'saldo-negatif' }}">
                        <div class="d-flex align-items-center gap-3">
                            <div class="summary-icon {{ $saldoBersih >= 0 ? 'bg-success bg-opacity-20' : 'bg-danger bg-opacity-20' }}">
                               <i class="bi bi-cash-stack {{ $saldoBersih >= 0 ? 'text-success' : 'text-danger' }}"></i>
                            </div>
                            <div class="min-width-0">
                                <div class="summary-label">Saldo Bersih</div>
                                <div class="summary-value {{ $saldoBersih >= 0 ? 'text-success' : 'text-danger' }} text-truncate">
                                    {{ $saldoBersih >= 0 ? '' : '-' }}Rp {{ number_format(abs($saldoBersih), 0, ',', '.') }}
                                </div>
                                <div class="summary-sub">
                                    @if($saldoBersih >= 0)
                                        <span class="text-success fw-semibold"><i class="bi bi-check-circle-fill me-1"></i>Surplus</span>
                                    @else
                                        <span class="text-danger fw-semibold"><i class="bi bi-exclamation-circle-fill me-1"></i>Defisit</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Rasio Pengeluaran --}}
                <div class="col-6 col-lg-3">
                    <div class="card summary-card p-3 h-100">
                        <div class="d-flex align-items-center gap-3">
                            <div class="summary-icon bg-info bg-opacity-10">
                                <i class="bi bi-pie-chart text-info"></i>
                            </div>
                            <div class="min-width-0">
                                <div class="summary-label">Rasio Pengeluaran</div>
                                @php
                                    $rasio = $totalPemasukan > 0
                                        ? round(($totalPengeluaran / $totalPemasukan) * 100, 1)
                                        : 0;
                                @endphp
                                <div class="summary-value text-info">{{ $rasio }}%</div>
                                <div class="summary-sub">dari total pemasukan</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ══ Grafik Tren + Donut ══ --}}
            <div class="row g-3 mb-4">

                {{-- Line/Bar Chart: Tren Bulanan --}}
                <div class="col-12 col-lg-8">
                    <div class="card chart-card p-4 h-100">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="chart-title">
                                <i class="bi bi-graph-up me-2 text-success"></i>Tren Pemasukan vs Pengeluaran
                            </div>
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-secondary btn-sm active" id="btnChartBar" onclick="gantiTipe('bar')">
                                    <i class="bi bi-bar-chart me-1"></i>Batang
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnChartLine" onclick="gantiTipe('line')">
                                    <i class="bi bi-graph-up me-1"></i>Garis
                                </button>
                            </div>
                        </div>
                        <div class="chart-wrapper chart-wrapper-tall" style="height: 300px;">
                            <canvas id="chartTren"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Donut: Komposisi Pengeluaran --}}
                <div class="col-12 col-lg-4">
                    <div class="card chart-card p-4 h-100">
                        <div class="chart-title mb-3">
                            <i class="bi bi-pie-chart me-2 text-danger"></i>Komposisi Pengeluaran
                        </div>
                        <div class="chart-wrapper chart-wrapper-short" style="height: 260px;">
                            <canvas id="chartDonutPengeluaran"></canvas>
                        </div>
                        {{-- Legend --}}
                        <div class="mt-3">
                            @php
                                $donutColors = ['#0d6efd', '#ffc107', '#198754', '#6c757d'];
                                $jenisLabels = ['Operasional', 'Gaji Staff', 'Gaji Guru', 'Lainnya'];
                                $jenisKeys   = ['operasional', 'gaji_staff', 'gaji_guru', 'lainnya'];
                            @endphp
                            @foreach($jenisLabels as $i => $lbl)
                                @if($donutData[$i] > 0)
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width:10px; height:10px; border-radius:50%; background:{{ $donutColors[$i] }};"></div>
                                        <span style="font-size:0.78rem;">{{ $lbl }}</span>
                                    </div>
                                    <span style="font-size:0.78rem; font-weight:600;">Rp {{ number_format($donutData[$i], 0, ',', '.') }}</span>
                                </div>
                                @endif
                            @endforeach
                            @if(array_sum($donutData) === 0)
                                <div class="text-center text-muted small py-2">Belum ada data</div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

            {{-- ══ Bar Chart Pemasukan + Saldo Bulanan ══ --}}
            <div class="row g-3 mb-4">

                <div class="col-12 col-lg-5">
                    <div class="card chart-card p-4 h-100">
                        <div class="chart-title mb-3">
                            <i class="bi bi-bar-chart-steps me-2 text-success"></i>Breakdown Jenis Pemasukan
                        </div>
                        <div class="chart-wrapper chart-wrapper-short" style="height: 240px;">
                            <canvas id="chartBarPemasukan"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-7">
                    <div class="card chart-card p-4 h-100">
                        <div class="chart-title mb-3">
                            <i class="bi bi-activity me-2 text-primary"></i>Saldo Bersih per Bulan
                        </div>
                        <div class="chart-wrapper chart-wrapper-short" style="height: 240px;">
                            <canvas id="chartSaldo"></canvas>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ══ Tabel Laporan Bulanan ══ --}}
            <div class="card p-4 mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="fw-bold text-success mb-0">
                        <i class="bi bi-table me-2"></i>Ringkasan Per Bulan
                    </h5>
                </div>
                <div class="table-responsive">
                    @php
                        $allP = array_column($tabelBulanan, 'pemasukan');
                        $allE = array_column($tabelBulanan, 'pengeluaran');
                        $maxBulan = count($allP) > 0 ? max(max($allP), max($allE), 1) : 1;
                    @endphp
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Bulan</th>
                                <th class="text-end">Pemasukan</th>
                                <th class="text-end">Pengeluaran</th>
                                <th class="text-end">Saldo</th>
                                <th class="text-center">Status</th>
                                <th class="text-center" style="width:130px;">Proporsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tabelBulanan as $row)
                            <tr>
                                <td class="fw-semibold small">{{ $row['bulan'] }}</td>
                                <td class="text-end small text-success fw-semibold">
                                    Rp {{ number_format($row['pemasukan'], 0, ',', '.') }}
                                </td>
                                <td class="text-end small text-danger fw-semibold">
                                    Rp {{ number_format($row['pengeluaran'], 0, ',', '.') }}
                                </td>
                                <td class="text-end small fw-bold {{ $row['saldo'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $row['saldo'] >= 0 ? '' : '-' }}Rp {{ number_format(abs($row['saldo']), 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    @if($row['status'] === 'surplus')
                                        <span class="badge-surplus">Surplus</span>
                                    @else
                                        <span class="badge-defisit">Defisit</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $pctP = round(($row['pemasukan'] / $maxBulan) * 100);
                                        $pctE = round(($row['pengeluaran'] / $maxBulan) * 100);
                                    @endphp
                                    <div class="d-flex flex-column gap-1" title="Hijau: Pemasukan | Merah: Pengeluaran">
                                        <div style="background:#e9ecef; border-radius:4px; height:6px; overflow:hidden;">
                                            <div style="width:{{ $pctP }}%; background:#198754; height:100%; border-radius:4px;"></div>
                                        </div>
                                        <div style="background:#e9ecef; border-radius:4px; height:6px; overflow:hidden;">
                                            <div style="width:{{ $pctE }}%; background:#dc3545; height:100%; border-radius:4px;"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block mb-2 text-secondary"></i>
                                    Tidak ada data pada periode ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if(count($tabelBulanan) > 0)
                        <tfoot style="background:#f8f9fa; font-weight:700;">
                            <tr>
                                <td class="small">Total</td>
                                <td class="text-end small text-success">
                                    Rp {{ number_format(array_sum(array_column($tabelBulanan, 'pemasukan')), 0, ',', '.') }}
                                </td>
                                <td class="text-end small text-danger">
                                    Rp {{ number_format(array_sum(array_column($tabelBulanan, 'pengeluaran')), 0, ',', '.') }}
                                </td>
                                @php $totalSaldo = array_sum(array_column($tabelBulanan, 'saldo')); @endphp
                                <td class="text-end small {{ $totalSaldo >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $totalSaldo >= 0 ? '' : '-' }}Rp {{ number_format(abs($totalSaldo), 0, ',', '.') }}
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>

            {{-- ══ Transaksi Terakhir ══ --}}
            <div class="row g-3 mb-4">

                {{-- 5 Pemasukan Terakhir --}}
                <div class="col-12 col-md-6">
                    <div class="card p-4">
                        <h6 class="fw-bold text-success mb-3">
                            <i class="bi bi-clock-history me-2"></i>5 Pemasukan Terakhir
                        </h6>
                        @forelse($txPemasukan as $tx)
                        @php
                            $labelMap = ['biaya_ppdb' => 'Biaya PPDB', 'donasi' => 'Donasi', 'bantuan_sosial' => 'Bantuan Sosial', 'lainnya' => 'Lainnya'];
                        @endphp
                        <div class="tx-item">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="small fw-semibold">{{ $labelMap[$tx->jenis_pemasukan] ?? $tx->jenis_pemasukan }}</div>
                                    <div class="text-muted" style="font-size:0.72rem;">
                                        {{ \Carbon\Carbon::parse($tx->created_at)->format('d M Y, H:i') }}
                                        @if($tx->keterangan_biaya) · {{ $tx->keterangan_biaya }} @endif
                                    </div>
                                </div>
                                <div class="tx-amount text-success">+Rp {{ number_format($tx->total, 0, ',', '.') }}</div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center text-muted small py-3">Belum ada transaksi.</div>
                        @endforelse
                    </div>
                </div>

                {{-- 5 Pengeluaran Terakhir --}}
                <div class="col-12 col-md-6">
                    <div class="card p-4">
                        <h6 class="fw-bold text-danger mb-3">
                            <i class="bi bi-clock-history me-2"></i>5 Pengeluaran Terakhir
                        </h6>
                        @php
                            $jenisLabelMap = ['operasional' => 'Operasional', 'gaji_staff' => 'Gaji Staff', 'gaji_guru' => 'Gaji Guru', 'lainnya' => 'Lainnya'];
                        @endphp
                        @forelse($txPengeluaran as $tx)
                        <div class="tx-item">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="small fw-semibold">{{ $tx->nama_pengeluaran }}</div>
                                    <div class="text-muted" style="font-size:0.72rem;">
                                        {{ \Carbon\Carbon::parse($tx->created_at)->format('d M Y, H:i') }}
                                        · {{ $jenisLabelMap[$tx->jenis_pengeluaran] ?? $tx->jenis_pengeluaran }}
                                    </div>
                                </div>
                                <div class="tx-amount text-danger">-Rp {{ number_format($tx->total, 0, ',', '.') }}</div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center text-muted small py-3">Belum ada transaksi.</div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>{{-- end container-fluid --}}
    </div>{{-- end #content --}}
</div>{{-- end .wrapper --}}


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
// ── Sidebar ──────────────────────────────────────────────────────────
window.toggleSidebar = function () {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    if (window.innerWidth <= 768) {
        sidebar.classList.toggle('show-mobile');
        overlay.classList.toggle('active');
    } else {
        sidebar.classList.toggle('inactive');
    }
};
document.getElementById('overlay').addEventListener('click', toggleSidebar);

// ── Filter Periode ────────────────────────────────────────────────────
function setPeriode(val) {
    document.getElementById('inputPeriode').value = val;
    document.getElementById('inputDateFrom').value = '';
    document.getElementById('inputDateTo').value = '';
    document.getElementById('formFilter').submit();
}

// ── Data dari PHP ─────────────────────────────────────────────────────
const labels        = @json($chartLabels);
const dataPemasukan = @json($chartPemasukan);
const dataPengeluaran = @json($chartPengeluaran);
const dataSaldo     = @json($chartSaldo);
const donutLabels   = @json($donutLabels);
const donutData     = @json($donutData);
const barPLabels    = @json($barPemasukanLabels);
const barPData      = @json($barPemasukanData);

// ── Formatter Rupiah ─────────────────────────────────────────────────
function fmtRupiah(v) {
    if (v >= 1_000_000_000) return 'Rp ' + (v / 1_000_000_000).toFixed(1) + 'M';
    if (v >= 1_000_000)     return 'Rp ' + (v / 1_000_000).toFixed(1) + 'jt';
    if (v >= 1_000)         return 'Rp ' + (v / 1_000).toFixed(0) + 'rb';
    return 'Rp ' + v;
}

// ── 1. Chart Tren (Bar/Line) ─────────────────────────────────────────
let chartTipeAktif = 'bar';
let chartTrenInstance = null;

function buatChartTren(tipe) {
    const ctx = document.getElementById('chartTren').getContext('2d');
    if (chartTrenInstance) chartTrenInstance.destroy();

    chartTrenInstance = new Chart(ctx, {
        type: tipe,
        data: {
            labels,
            datasets: [
                {
                    label: 'Pemasukan',
                    data: dataPemasukan,
                    backgroundColor: tipe === 'bar' ? 'rgba(25,135,84,0.7)' : 'rgba(25,135,84,0.15)',
                    borderColor: '#198754',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: tipe === 'line',
                    pointRadius: tipe === 'line' ? 4 : 0,
                    pointHoverRadius: 6,
                    borderRadius: tipe === 'bar' ? 6 : 0,
                },
                {
                    label: 'Pengeluaran',
                    data: dataPengeluaran,
                    backgroundColor: tipe === 'bar' ? 'rgba(220,53,69,0.7)' : 'rgba(220,53,69,0.1)',
                    borderColor: '#dc3545',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: tipe === 'line',
                    pointRadius: tipe === 'line' ? 4 : 0,
                    pointHoverRadius: 6,
                    borderRadius: tipe === 'bar' ? 6 : 0,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'top', labels: { font: { size: 11 }, padding: 16 } },
                tooltip: {
                    callbacks: {
                        label: ctx => ' ' + ctx.dataset.label + ': ' + new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(ctx.raw)
                    }
                }
            },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                y: {
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: {
                        font: { size: 11 },
                        callback: v => fmtRupiah(v)
                    }
                }
            }
        }
    });
}

function gantiTipe(tipe) {
    chartTipeAktif = tipe;
    document.getElementById('btnChartBar').classList.toggle('active', tipe === 'bar');
    document.getElementById('btnChartLine').classList.toggle('active', tipe === 'line');
    buatChartTren(tipe);
}

buatChartTren('bar');

// ── 2. Donut Chart Pengeluaran ────────────────────────────────────────
const ctxDonut = document.getElementById('chartDonutPengeluaran').getContext('2d');
new Chart(ctxDonut, {
    type: 'doughnut',
    data: {
        labels: donutLabels,
        datasets: [{
            data: donutData,
            backgroundColor: ['#0d6efd', '#ffc107', '#198754', '#6c757d'],
            borderWidth: 2,
            borderColor: '#fff',
            hoverOffset: 8,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '60%',
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => ' ' + ctx.label + ': ' + new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(ctx.raw)
                }
            }
        }
    }
});

// ── 3. Bar Chart Pemasukan per Jenis ─────────────────────────────────
const ctxBarP = document.getElementById('chartBarPemasukan').getContext('2d');
new Chart(ctxBarP, {
    type: 'bar',
    data: {
        labels: barPLabels,
        datasets: [{
            label: 'Total Pemasukan',
            data: barPData,
            backgroundColor: ['#198754', '#0dcaf0', '#6f42c1', '#fd7e14'],
            borderRadius: 8,
            borderWidth: 0,
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => ' ' + new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(ctx.raw)
                }
            }
        },
        scales: {
            x: {
                grid: { color: 'rgba(0,0,0,0.05)' },
                ticks: { font: { size: 10 }, callback: v => fmtRupiah(v) }
            },
            y: { grid: { display: false }, ticks: { font: { size: 11 } } }
        }
    }
});

// ── 4. Saldo Line Chart ───────────────────────────────────────────────
const ctxSaldo = document.getElementById('chartSaldo').getContext('2d');
new Chart(ctxSaldo, {
    type: 'line',
    data: {
        labels,
        datasets: [{
            label: 'Saldo Bersih',
            data: dataSaldo,
            borderColor: '#0d6efd',
            backgroundColor: ctx => {
                const v = ctx.raw;
                return v >= 0 ? 'rgba(25,135,84,0.1)' : 'rgba(220,53,69,0.1)';
            },
            borderWidth: 2.5,
            tension: 0.4,
            fill: true,
            pointRadius: 5,
            pointHoverRadius: 7,
            pointBackgroundColor: dataSaldo.map(v => v >= 0 ? '#198754' : '#dc3545'),
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => {
                        const v = ctx.raw;
                        const sign = v >= 0 ? '+' : '';
                        return ' Saldo: ' + sign + new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(v);
                    }
                }
            }
        },
        scales: {
            x: { grid: { display: false }, ticks: { font: { size: 11 } } },
            y: {
                grid: { color: 'rgba(0,0,0,0.05)' },
                ticks: {
                    font: { size: 11 },
                    callback: v => fmtRupiah(v)
                }
            }
        }
    }
});
</script>
</body>
</html>
