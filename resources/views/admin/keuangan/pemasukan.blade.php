<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemasukan Keuangan</title>
        @include('favicon')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root { --primary-green: #198754; }
        body { font-family: 'Inter', sans-serif; background-color: #f4f7f6; overflow-x: hidden; }
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        #content { width: 100%; padding: 20px 30px; transition: all 0.3s; min-height: 100vh; min-width: 0; }
        #sidebarCollapse { width: 45px; height: 45px; background: var(--primary-green); border: none; color: white; border-radius: 10px; box-shadow: 0 4px 10px rgba(25,135,84,0.2); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .card  { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .table thead { background-color: var(--primary-green); color: white; }
        .table thead th { font-size: 0.82rem; letter-spacing: 0.4px; font-weight: 600; white-space: nowrap; }
        #overlay { display: none; position: fixed; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 1040; top: 0; left: 0; }
        #overlay.active { display: block; }
        input:focus, textarea:focus, select:focus { border-color: #198754 !important; outline: none !important; box-shadow: 0 0 0 0.2rem rgba(25,135,84,0.25) !important; }

        /* Summary cards */
        .summary-card { border-radius: 12px; border: none; transition: transform 0.2s; }
        .summary-card:hover { transform: translateY(-2px); }
        .summary-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0; }

        /* Form */
        .section-divider { border: 0; border-top: 2px dashed #dee2e6; margin: 1.2rem 0; }
        .form-label { font-weight: 600; font-size: 0.85rem; }
        .hint-text { font-size: 0.75rem; color: #6c757d; margin-top: 4px; }

        /* Murid pill */
        .murid-selected-pill { background: #e8f5e9; border: 1px solid #a5d6a7; border-radius: 8px; padding: 8px 14px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .murid-selected-pill .btn-clear { background: none; border: none; color: #dc3545; padding: 0; font-size: 1.1rem; line-height: 1; }

        /* Akun info box */
        .akun-info-box { background: #f0f9ff; border: 1px solid #b6e0fe; border-radius: 10px; padding: 14px 16px; }
        .akun-info-box .bank-name { font-weight: 700; font-size: 0.95rem; color: #0369a1; }
        .akun-info-box .rekening-number { font-size: 1.1rem; font-weight: 700; letter-spacing: 2px; color: #1e293b; word-break: break-all; }
        .akun-info-box .atas-nama { font-size: 0.82rem; color: #64748b; }
        .qris-img { max-width: 160px; width: 100%; border-radius: 8px; border: 1px solid #e2e8f0; }

        /* Filter bar */
        .filter-bar { background: #fff; border-radius: 12px; padding: 14px 18px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .btn-range { font-size: 0.8rem; padding: 5px 14px; border-radius: 20px; font-weight: 600; }
        .btn-range.active { background: var(--primary-green); color: #fff; border-color: var(--primary-green); }
        .btn-range:not(.active) { background: #fff; color: #495057; border-color: #dee2e6; }
        .btn-range:not(.active):hover { background: #f0faf5; border-color: var(--primary-green); color: var(--primary-green); }

        /* Nav tabs */
        .nav-tabs .nav-link { color: #6c757d; font-weight: 500; border-radius: 8px 8px 0 0; }
        .nav-tabs .nav-link.active { color: #198754; font-weight: 600; border-bottom-color: #fff; }
        .nav-tabs .nav-link:hover { color: #198754; }

        /* Modal murid */
        #modal-murid-table tbody tr:hover { background-color: #f0faf5; }
        .murid-loader { text-align: center; padding: 20px; color: #6c757d; }

        /* Baris tersembunyi */
        .row-extra { display: none; }

        /* Mode Edit */
        .edit-mode-banner { background: #e8f5e9; border: 1px solid #a5d6a7; border-radius: 10px; padding: 10px 16px; display: none; margin-bottom: 14px; }
        .edit-mode-banner.show { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .label-edited { font-size: 0.68rem; color: #6c757d; font-style: italic; display: block; margin-top: 2px; }

        /* ── Responsive ── */
        @media (max-width: 991px) {
            #content { padding: 16px 18px; }
        }
        @media (max-width: 767px) {
            #content { padding: 12px 12px; }
            /* Filter bar: tanggal stack */
            .filter-bar .date-range-row { flex-direction: column; align-items: stretch !important; }
            .filter-bar .date-range-row input[type="date"] { width: 100% !important; }
            .filter-bar .date-range-row .btn { width: 100%; }
            /* Tabel laporan → scroll horizontal tetap, tapi font lebih kecil */
            .table thead th, .table tbody td { font-size: 0.78rem; }
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
            <div class="d-flex align-items-center mb-4 mt-2 flex-wrap gap-2">
                <button type="button" id="sidebarCollapse" class="btn" onclick="toggleSidebar()">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <h4 class="mb-0 fw-bold text-success">
                    <i class="bi bi-arrow-down-circle me-2"></i>Pemasukan Keuangan
                </h4>
            </div>

            {{-- ══ Alert ══ --}}
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
                    <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Gagal menyimpan:</div>
                    <ul class="mb-0 small ps-3">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- ══════════════════════════════════════════
                 FORM INPUT PEMASUKAN
            ══════════════════════════════════════════ --}}
            <div class="card p-4 mb-4" id="cardForm">
                {{-- ── Banner mode edit ── --}}
                <div class="edit-mode-banner" id="editModeBanner">
                    <i class="bi bi-pencil-square text-success fs-5"></i>
                    <div class="flex-grow-1">
                        <span class="fw-bold text-success small">Mode Edit</span>
                        <span class="text-muted small ms-2" id="editModeBannerText"></span>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="batalEdit()">
                        <i class="bi bi-x-lg me-1"></i>Batal Edit
                    </button>
                </div>

                <h5 class="fw-bold text-success mb-3" id="formTitle">
                    <i class="bi bi-plus-circle me-2"></i>Input Pemasukan Baru
                </h5>

                {{-- ══ FORM STORE (default) ══ --}}
                <form action="{{ route('keuangan.pemasukan.store') }}" method="POST" id="formPemasukan">
                    @csrf

                    {{-- STEP 1: Jenis Biaya --}}
                    <div class="mb-3">
                        <label class="form-label">Jenis Biaya <span class="text-danger">*</span></label>
                        <select name="jenis_pemasukan" id="selectJenisBiaya" class="form-select" required>
                            <option value="">-- Pilih Jenis Biaya --</option>
                            <option value="biaya_ppdb"     {{ old('jenis_pemasukan') === 'biaya_ppdb'     ? 'selected' : '' }}>Biaya PPDB</option>
                            <option value="donasi"         {{ old('jenis_pemasukan') === 'donasi'         ? 'selected' : '' }}>Donasi</option>
                            <option value="bantuan_sosial" {{ old('jenis_pemasukan') === 'bantuan_sosial' ? 'selected' : '' }}>Bantuan Sosial</option>
                            <option value="lainnya"        {{ old('jenis_pemasukan') === 'lainnya'        ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>

                    {{-- STEP 2A: Biaya PPDB --}}
                    <div id="section-biaya-ppdb" class="d-none">
                        <div class="mb-3">
                            <label class="form-label">Jenis Biaya PPDB <span class="text-danger">*</span></label>
                            <select name="jenis_biaya_ppdb" id="selectJenisBiayaPpdb" class="form-select">
                                <option value="">-- Pilih Jenis Biaya PPDB --</option>
                                @foreach($biayas as $biaya)
                                    <option value="{{ $biaya->name }}"
                                            data-amount="{{ (int)$biaya->amount }}"
                                            {{ old('jenis_biaya_ppdb') === $biaya->name ? 'selected' : '' }}>
                                        {{ $biaya->name }} (Rp {{ number_format($biaya->amount, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Info Rekening/QRIS --}}
                        <div id="section-akun-info" class="d-none mb-3">
                            <div class="akun-info-box">
                                <div class="d-flex align-items-start gap-3">
                                    <div id="akun-info-rekening" class="d-none flex-grow-1">
                                        <div class="bank-name mb-1" id="akun-bank-name">—</div>
                                        <div class="rekening-number" id="akun-rekening-number">—</div>
                                        <div class="atas-nama mt-1">a.n. <span id="akun-holder">—</span></div>
                                    </div>
                                    <div id="akun-info-qris" class="d-none text-center">
                                        <div class="bank-name mb-2" id="akun-qris-bank">—</div>
                                        <img id="akun-qris-img" src="" alt="QRIS" class="qris-img">
                                        <div class="text-muted small mt-1">Scan QRIS untuk pembayaran</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Pilih Murid --}}
                        <div id="section-btn-pilih-murid" class="d-none mb-3">
                            <button type="button" class="btn btn-outline-success" id="btnPilihMurid"
                                    onclick="bukaModalMurid()">
                                <i class="bi bi-person-search me-2"></i>Pilih Murid
                            </button>
                            <input type="hidden" name="id_murid" id="inputIdMurid">

                            <div id="murid-terpilih" class="d-none mt-2">
                                <div class="murid-selected-pill">
                                    <i class="bi bi-person-check-fill text-success"></i>
                                    <div>
                                        <div class="fw-semibold small" id="murid-nama-display">-</div>
                                        <div class="text-muted" style="font-size:0.75rem;">
                                            NIS: <span id="murid-nis-display">-</span>&nbsp;|&nbsp;NISN: <span id="murid-nisn-display">-</span>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-clear ms-auto" onclick="clearMurid()" title="Hapus pilihan">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- STEP 2B: Jenis Lainnya --}}
                    <div id="section-lainnya" class="d-none mb-3">
                        <label class="form-label">Keterangan Jenis Biaya <span class="text-danger">*</span></label>
                        <input type="text" name="keterangan_lainnya" id="inputKetLainnya"
                               class="form-control" placeholder="Contoh: Sumbangan Alumni, Koperasi Sekolah..."
                               value="{{ old('keterangan_lainnya') }}">
                    </div>

                    {{-- STEP 3: Detail Nominal --}}
                    <div id="section-detail-form" class="d-none">
                        <hr class="section-divider">
                        <div class="mb-3">
                            <label class="form-label">Keterangan Biaya <span class="text-muted fw-normal small">(Opsional)</span></label>
                            <input type="text" name="keterangan_biaya" class="form-control"
                                   placeholder="Catatan tambahan..." value="{{ old('keterangan_biaya') }}">
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Nominal (Rp) <span class="text-danger">*</span></label>
                                <input type="number" name="nominal" id="inputNominal" class="form-control"
                                       placeholder="0" min="1" required value="{{ old('nominal') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">QTY <span class="text-danger">*</span></label>
                                <input type="number" name="qty" id="inputQty" class="form-control"
                                       placeholder="1" min="1" value="{{ old('qty', 1) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Total (Rp)</label>
                                <input type="number" name="total" id="inputTotal" class="form-control bg-light"
                                       placeholder="0" readonly>
                                <div class="hint-text">
                                    <i class="bi bi-info-circle me-1"></i>Otomatis: Nominal × QTY
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-success px-5 shadow-sm fw-bold">
                                <i class="bi bi-save me-2"></i>Simpan Pemasukan
                            </button>
                        </div>
                    </div>

                </form>

                {{-- ══ FORM EDIT (tersembunyi, muncul saat mode edit) ══ --}}
                <form action="" method="POST" id="formEdit" class="d-none">
                    @csrf

                    {{-- Jenis Biaya --}}
                    <div class="mb-3">
                        <label class="form-label">Jenis Biaya <span class="text-danger">*</span></label>
                        <select name="jenis_pemasukan" id="edit_selectJenisBiaya" class="form-select" required>
                            <option value="">-- Pilih Jenis Biaya --</option>
                            <option value="biaya_ppdb">Biaya PPDB</option>
                            <option value="donasi">Donasi</option>
                            <option value="bantuan_sosial">Bantuan Sosial</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>

                    {{-- Biaya PPDB section --}}
                    <div id="edit_section-biaya-ppdb" class="d-none">
                        <div class="mb-3">
                            <label class="form-label">Jenis Biaya PPDB <span class="text-danger">*</span></label>
                            <select name="jenis_biaya_ppdb" id="edit_selectJenisBiayaPpdb" class="form-select">
                                <option value="">-- Pilih Jenis Biaya PPDB --</option>
                                @foreach($biayas as $biaya)
                                    <option value="{{ $biaya->name }}" data-amount="{{ (int)$biaya->amount }}">
                                        {{ $biaya->name }} (Rp {{ number_format($biaya->amount, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Info Rekening/QRIS --}}
                        <div id="edit_section-akun-info" class="d-none mb-3">
                            <div class="akun-info-box">
                                <div class="d-flex align-items-start gap-3">
                                    <div id="edit_akun-info-rekening" class="d-none flex-grow-1">
                                        <div class="bank-name mb-1" id="edit_akun-bank-name">—</div>
                                        <div class="rekening-number" id="edit_akun-rekening-number">—</div>
                                        <div class="atas-nama mt-1">a.n. <span id="edit_akun-holder">—</span></div>
                                    </div>
                                    <div id="edit_akun-info-qris" class="d-none text-center">
                                        <div class="bank-name mb-2" id="edit_akun-qris-bank">—</div>
                                        <img id="edit_akun-qris-img" src="" alt="QRIS" class="qris-img">
                                        <div class="text-muted small mt-1">Scan QRIS untuk pembayaran</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Pilih Murid --}}
                        <div id="edit_section-btn-pilih-murid" class="d-none mb-3">
                            <button type="button" class="btn btn-outline-success" onclick="bukaModalMuridEdit()">
                                <i class="bi bi-person-search me-2"></i>Pilih / Ganti Murid
                            </button>
                            <input type="hidden" name="id_murid" id="edit_inputIdMurid">
                            <div id="edit_murid-terpilih" class="d-none mt-2">
                                <div class="murid-selected-pill">
                                    <i class="bi bi-person-check-fill text-success"></i>
                                    <div>
                                        <div class="fw-semibold small" id="edit_murid-nama-display">-</div>
                                        <div class="text-muted" style="font-size:0.75rem;">
                                            NIS: <span id="edit_murid-nis-display">-</span>&nbsp;|&nbsp;NISN: <span id="edit_murid-nisn-display">-</span>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-clear ms-auto" onclick="clearMuridEdit()" title="Hapus pilihan">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Lainnya --}}
                    <div id="edit_section-lainnya" class="d-none mb-3">
                        <label class="form-label">Keterangan Jenis Biaya <span class="text-danger">*</span></label>
                        <input type="text" name="keterangan_lainnya" id="edit_inputKetLainnya"
                               class="form-control" placeholder="Contoh: Sumbangan Alumni...">
                    </div>

                    {{-- Detail Nominal --}}
                    <div id="edit_section-detail-form" class="d-none">
                        <hr class="section-divider">
                        <div class="mb-3">
                            <label class="form-label">Keterangan Biaya <span class="text-muted fw-normal small">(Opsional)</span></label>
                            <input type="text" name="keterangan_biaya" id="edit_inputKetBiaya"
                                   class="form-control" placeholder="Catatan tambahan...">
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Nominal (Rp) <span class="text-danger">*</span></label>
                                <input type="number" name="nominal" id="edit_inputNominal"
                                       class="form-control" placeholder="0" min="1" required
                                       oninput="hitungTotalEdit()">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">QTY <span class="text-danger">*</span></label>
                                <input type="number" name="qty" id="edit_inputQty"
                                       class="form-control" placeholder="1" min="1" value="1" required
                                       oninput="hitungTotalEdit()">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Total (Rp)</label>
                                <input type="number" name="total" id="edit_inputTotal"
                                       class="form-control bg-light" placeholder="0" readonly>
                                <div class="hint-text"><i class="bi bi-info-circle me-1"></i>Otomatis: Nominal × QTY</div>
                            </div>
                        </div>
                        <div class="mt-4 d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-outline-secondary px-4" onclick="batalEdit()">
                                <i class="bi bi-x-lg me-1"></i>Batal
                            </button>
                            <button type="submit" class="btn btn-warning px-5 shadow-sm fw-bold text-dark">
                                <i class="bi bi-pencil-square me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </div>

                </form>

            </div>{{-- end cardForm --}}

            {{-- ══════════════════════════════════════════
                 SUMMARY CARDS (hanya data Tersedia)
            ══════════════════════════════════════════ --}}
            <div class="row g-3 mb-4">
                <div class="col-12 col-sm-6">
                    <div class="card summary-card p-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="summary-icon bg-success bg-opacity-10">
                                <i class="bi bi-cash-stack text-success"></i>
                            </div>
                            <div class="min-width-0">
                                <div class="text-muted small fw-semibold">Total Nominal Pemasukan</div>
                                <div class="fw-bold fs-5 text-success text-truncate">
                                    Rp {{ number_format($totalNominal, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <div class="card summary-card p-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="summary-icon bg-primary bg-opacity-10">
                                <i class="bi bi-activity text-primary"></i>
                            </div>
                            <div class="min-width-0">
                                <div class="text-muted small fw-semibold">Total Aktivitas Pemasukan</div>
                                <div class="fw-bold fs-5 text-primary">
                                    {{ number_format($totalAktifitas, 0, ',', '.') }} transaksi
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══════════════════════════════════════════
                 LAPORAN: FILTER + TAB + TABEL
            ══════════════════════════════════════════ --}}
            <div class="card p-4">

                {{-- Judul + Download Excel --}}
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <h5 class="fw-bold text-success mb-0">
                        <i class="bi bi-table me-2"></i>Laporan Pemasukan
                    </h5>
                    <a href="{{ route('keuangan.pemasukan.export-excel', array_filter(['range' => $range, 'date_from' => $dateFrom, 'date_to' => $dateTo])) }}"
                       class="btn btn-success btn-sm px-4 shadow-sm fw-semibold">
                        <i class="bi bi-file-earmark-excel me-2"></i>Download Excel
                    </a>
                </div>

                {{-- ── Filter Bar ── --}}
                <form method="GET" action="{{ route('keuangan.pemasukan.index') }}" id="formFilter">
                    <div class="filter-bar mb-3">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                            <span class="text-muted small fw-semibold me-1">Filter:</span>
                            @foreach(['1hari' => '1 Hari', '1minggu' => '1 Minggu', '1bulan' => '1 Bulan', '1tahun' => '1 Tahun'] as $key => $label)
                                <button type="button"
                                        class="btn btn-range {{ $range === $key ? 'active' : '' }}"
                                        onclick="setRange('{{ $key }}')">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-2 date-range-row">
                            <span class="text-muted small fw-semibold">Atau pilih tanggal:</span>
                            <input type="date" name="date_from" id="inputDateFrom"
                                   class="form-control form-control-sm flex-fill" style="min-width:130px; max-width:180px;"
                                   value="{{ $dateFrom }}">
                            <span class="text-muted small">s/d</span>
                            <input type="date" name="date_to" id="inputDateTo"
                                   class="form-control form-control-sm flex-fill" style="min-width:130px; max-width:180px;"
                                   value="{{ $dateTo }}">
                            <button type="submit" class="btn btn-outline-success btn-sm px-3 fw-semibold">
                                <i class="bi bi-funnel me-1"></i>Terapkan
                            </button>
                            @if($range !== '1bulan' || $dateFrom || $dateTo)
                                <a href="{{ route('keuangan.pemasukan.index') }}"
                                   class="btn btn-outline-secondary btn-sm px-3">
                                    <i class="bi bi-x-circle me-1"></i>Reset
                                </a>
                            @endif
                        </div>
                        </div>
                        {{-- Pertahankan tab aktif saat filter diapply --}}
                        <input type="hidden" name="range" id="inputRange" value="{{ $range }}">
                        <input type="hidden" name="tab" value="{{ $tab }}">
                    </div>
                </form>

                {{-- Keterangan filter aktif --}}
                @php
                    $rangeLabel = ['1hari' => '1 Hari Terakhir', '1minggu' => '1 Minggu Terakhir', '1bulan' => '1 Bulan Terakhir', '1tahun' => '1 Tahun Terakhir', 'custom' => 'Custom'];
                @endphp
                <div class="text-muted small mb-3">
                    <i class="bi bi-calendar3 me-1"></i>
                    Menampilkan data:
                    @if($range === 'custom' && $dateFrom && $dateTo)
                        <strong>{{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }} — {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}</strong>
                    @else
                        <strong>{{ $rangeLabel[$range] ?? '1 Bulan Terakhir' }}</strong>
                    @endif
                </div>

                {{-- ── Tab Tersedia / Dihapus ── --}}
                <ul class="nav nav-tabs mb-3" id="tabPemasukan" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $tab === 'tersedia' ? 'active' : '' }} d-flex align-items-center gap-2"
                           href="{{ route('keuangan.pemasukan.index', array_filter(['tab' => 'tersedia', 'range' => $range, 'date_from' => $dateFrom, 'date_to' => $dateTo])) }}">
                            <i class="bi bi-check-circle{{ $tab === 'tersedia' ? '-fill' : '' }} text-success"></i>
                            Tersedia
                            <span class="badge bg-success ms-1">{{ $pemasukans->count() }}</span>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $tab === 'dihapus' ? 'active' : '' }} d-flex align-items-center gap-2"
                           href="{{ route('keuangan.pemasukan.index', array_filter(['tab' => 'dihapus', 'range' => $range, 'date_from' => $dateFrom, 'date_to' => $dateTo])) }}">
                            <i class="bi bi-trash{{ $tab === 'dihapus' ? '-fill' : '' }} text-danger"></i>
                            Dihapus
                            <span class="badge bg-danger ms-1">{{ $dihapusList->count() }}</span>
                        </a>
                    </li>
                </ul>

                {{-- ══ TAB: TERSEDIA ══ --}}
                <div class="{{ $tab === 'tersedia' ? '' : 'd-none' }}" id="panel-tersedia">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="tabel-tersedia">
                            <thead>
                                <tr>
                                    <th width="45">No</th>
                                    <th>Tanggal</th>
                                    <th>Jenis Biaya</th>
                                    <th>Detail</th>
                                    <th>Murid</th>
                                    <th>Keterangan</th>
                                    <th class="text-end">Nominal</th>
                                    <th class="text-center">QTY</th>
                                    <th class="text-end">Total</th>
                                    <th width="90" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pemasukans as $index => $p)
                                <tr class="{{ $index >= 20 ? 'row-extra row-extra-tersedia' : '' }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td class="text-muted small">
                                        {{ \Carbon\Carbon::parse($p->created_at)->format('d M Y') }}
                                        @if($p->edited_at)
                                            <span class="label-edited">
                                                <i class="bi bi-pencil-fill me-1" style="font-size:0.6rem;"></i>diedit pada: {{ \Carbon\Carbon::parse($p->edited_at)->format('d M Y, H:i') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $badgeMap = [
                                                'biaya_ppdb'     => ['bg-success',           'Biaya PPDB'],
                                                'donasi'         => ['bg-info text-dark',    'Donasi'],
                                                'bantuan_sosial' => ['bg-warning text-dark', 'Bantuan Sosial'],
                                                'lainnya'        => ['bg-secondary',         'Lainnya'],
                                            ];
                                            [$bc, $bl] = $badgeMap[$p->jenis_pemasukan] ?? ['bg-secondary', $p->jenis_pemasukan];
                                        @endphp
                                        <span class="badge {{ $bc }}">{{ $bl }}</span>
                                    </td>
                                    <td class="small">
                                        @if($p->jenis_pemasukan === 'biaya_ppdb') {{ $p->jenis_biaya_ppdb ?? '—' }}
                                        @elseif($p->jenis_pemasukan === 'lainnya') {{ $p->keterangan_lainnya ?? '—' }}
                                        @else <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($p->id_murid && isset($murids[$p->id_murid]))
                                            @php $m = $murids[$p->id_murid]; @endphp
                                            <div class="fw-semibold small">{{ $m->nama_lengkap }}</div>
                                            <div class="text-muted" style="font-size:0.72rem;">NIS: {{ $m->nis_baru ?? '—' }}</div>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td class="text-muted small">{{ $p->keterangan_biaya ?? '—' }}</td>
                                    <td class="text-end fw-semibold">Rp {{ number_format($p->nominal, 0, ',', '.') }}</td>
                                    <td class="text-center">{{ $p->qty }}</td>
                                    <td class="text-end fw-bold text-success">Rp {{ number_format($p->total, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        {{-- Tombol Edit --}}
                                        <button type="button"
                                                class="btn btn-sm btn-outline-warning border-0"
                                                title="Edit"
                                                onclick="bukaEdit({{ $p->id }})">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        {{-- Tombol Hapus (pindah ke tab Dihapus) --}}
                                        <form action="{{ route('keuangan.pemasukan.destroy', $p->id) }}"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Pindahkan data ini ke tab Dihapus?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger border-0" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-3 d-block mb-2 text-secondary"></i>
                                        Tidak ada data pemasukan pada periode ini.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($pemasukans->count() > 20)
                    <div class="text-center mt-2" id="wrapper-lihat-tersedia">
                        <button class="btn btn-outline-success btn-sm px-4" onclick="lihatSemua('tersedia')">
                            <i class="bi bi-chevron-down me-1"></i>Lihat Semua Data
                            ({{ $pemasukans->count() - 20 }} data lainnya)
                        </button>
                    </div>
                    @endif
                </div>

                {{-- ══ TAB: DIHAPUS ══ --}}
                <div class="{{ $tab === 'dihapus' ? '' : 'd-none' }}" id="panel-dihapus">
                    @if($tab === 'dihapus')
                    <div class="alert alert-warning border-0 py-2 mb-3 small">
                        <i class="bi bi-info-circle me-1"></i>
                        Data di sini telah dihapus dari laporan aktif. Anda dapat memulihkannya kembali ke tab <strong>Tersedia</strong>.
                    </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="tabel-dihapus">
                            <thead>
                                <tr>
                                    <th width="45">No</th>
                                    <th>Tanggal</th>
                                    <th>Jenis Biaya</th>
                                    <th>Detail</th>
                                    <th>Murid</th>
                                    <th>Keterangan</th>
                                    <th class="text-end">Nominal</th>
                                    <th class="text-center">QTY</th>
                                    <th class="text-end">Total</th>
                                    <th width="90" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dihapusList as $index => $p)
                                <tr class="{{ $index >= 20 ? 'row-extra row-extra-dihapus' : '' }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td class="text-muted small">
                                        {{ \Carbon\Carbon::parse($p->created_at)->format('d M Y') }}
                                        @if($p->edited_at)
                                            <span class="label-edited">
                                                <i class="bi bi-pencil-fill me-1" style="font-size:0.6rem;"></i>diedit pada: {{ \Carbon\Carbon::parse($p->edited_at)->format('d M Y, H:i') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            [$bc, $bl] = $badgeMap[$p->jenis_pemasukan] ?? ['bg-secondary', $p->jenis_pemasukan];
                                        @endphp
                                        <span class="badge {{ $bc }}">{{ $bl }}</span>
                                    </td>
                                    <td class="small">
                                        @if($p->jenis_pemasukan === 'biaya_ppdb') {{ $p->jenis_biaya_ppdb ?? '—' }}
                                        @elseif($p->jenis_pemasukan === 'lainnya') {{ $p->keterangan_lainnya ?? '—' }}
                                        @else <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($p->id_murid && isset($murids[$p->id_murid]))
                                            @php $m = $murids[$p->id_murid]; @endphp
                                            <div class="fw-semibold small">{{ $m->nama_lengkap }}</div>
                                            <div class="text-muted" style="font-size:0.72rem;">NIS: {{ $m->nis_baru ?? '—' }}</div>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td class="text-muted small">{{ $p->keterangan_biaya ?? '—' }}</td>
                                    <td class="text-end fw-semibold">Rp {{ number_format($p->nominal, 0, ',', '.') }}</td>
                                    <td class="text-center">{{ $p->qty }}</td>
                                    <td class="text-end fw-bold text-danger">Rp {{ number_format($p->total, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        {{-- Tombol Pulihkan --}}
                                        <form action="{{ route('keuangan.pemasukan.restore', $p->id) }}"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Pulihkan data ini ke tab Tersedia?')">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-success border-0" title="Pulihkan">
                                                <i class="bi bi-arrow-counterclockwise"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5 text-muted">
                                        <i class="bi bi-trash fs-3 d-block mb-2 text-secondary"></i>
                                        Tidak ada data yang dihapus pada periode ini.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($dihapusList->count() > 20)
                    <div class="text-center mt-2" id="wrapper-lihat-dihapus">
                        <button class="btn btn-outline-danger btn-sm px-4" onclick="lihatSemua('dihapus')">
                            <i class="bi bi-chevron-down me-1"></i>Lihat Semua Data Dihapus
                            ({{ $dihapusList->count() - 20 }} data lainnya)
                        </button>
                    </div>
                    @endif
                </div>

            </div>{{-- end card laporan --}}

        </div>{{-- end container-fluid --}}
    </div>{{-- end #content --}}
</div>{{-- end .wrapper --}}


{{-- ══════════════════════════════════════════════
     MODAL: PILIH MURID
══════════════════════════════════════════════ --}}
<div class="modal fade" id="modalPilihMurid" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-person-search me-2"></i>Pilih Murid</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3">
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="inputCariMurid" class="form-control border-start-0"
                               placeholder="Cari berdasarkan NIS, NISN, atau Nama...">
                    </div>
                    <div class="form-text">
                        <i class="bi bi-info-circle me-1"></i>Menampilkan 10 data teratas. Gunakan kolom pencarian untuk menemukan murid berdasarkan NIS / NISN / Nama.
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="modal-murid-table">
                        <thead>
                            <tr>
                                <th>NIS (Baru)</th>
                                <th>NISN</th>
                                <th>Nama Lengkap</th>
                                <th width="80" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="modal-murid-tbody">
                            <tr><td colspan="4" class="murid-loader"><i class="bi bi-hourglass-split me-2"></i>Memuat data...</td></tr>
                        </tbody>
                    </table>
                </div>
                <div id="murid-search-info" class="text-muted small text-end"></div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════
     MODAL: PILIH MURID (untuk form Edit)
══════════════════════════════════════════════ --}}
<div class="modal fade" id="modalPilihMuridEdit" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-person-search me-2"></i>Pilih / Ganti Murid</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3">
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="inputCariMuridEdit" class="form-control border-start-0"
                               placeholder="Cari berdasarkan NIS, NISN, atau Nama...">
                    </div>
                    <div class="form-text">
                        <i class="bi bi-info-circle me-1"></i>Menampilkan 10 data teratas. Ketik untuk mencari murid.
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>NIS (Baru)</th>
                                <th>NISN</th>
                                <th>Nama Lengkap</th>
                                <th width="80" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="modal-murid-edit-tbody">
                            <tr><td colspan="4" class="murid-loader"><i class="bi bi-hourglass-split me-2"></i>Memuat data...</td></tr>
                        </tbody>
                    </table>
                </div>
                <div id="murid-edit-search-info" class="text-muted small text-end"></div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const biayaDetailUrl = '{{ route("keuangan.pemasukan.biaya-detail") }}';
const searchMuridUrl = '{{ route("keuangan.pemasukan.search-murid") }}';

// Instance modal disimpan di variabel global agar bisa dipanggil dari luar ready()
let modalMuridInstance = null;

$(document).ready(function () {

    // ── Sidebar ────────────────────────────────────────────────────
    window.toggleSidebar = function () {
        if ($(window).width() <= 768) {
            $('#sidebar').toggleClass('show-mobile');
            $('#overlay').toggleClass('active');
        } else {
            $('#sidebar').toggleClass('inactive');
        }
    };
    $('#overlay').on('click', toggleSidebar);

    // ── Kalkulasi Total Otomatis ────────────────────────────────────
    function hitungTotal() {
        const nominal = parseInt($('#inputNominal').val()) || 0;
        const qty     = parseInt($('#inputQty').val())     || 0;
        $('#inputTotal').val(nominal * qty);
    }
    $('#inputNominal, #inputQty').on('input', hitungTotal);

    // ── Logika Form Bertahap ────────────────────────────────────────
    const $jenisBiaya      = $('#selectJenisBiaya');
    const $selectBiayaPpdb = $('#selectJenisBiayaPpdb');

    function resetForm() {
        $('#section-biaya-ppdb').addClass('d-none');
        $('#section-lainnya').addClass('d-none');
        $('#section-detail-form').addClass('d-none');
        $('#section-btn-pilih-murid').addClass('d-none');
        $('#section-akun-info').addClass('d-none');
        $selectBiayaPpdb.val('');
        $('#inputKetLainnya').val('');
        $('#inputNominal').val('').prop('readonly', false);
        $('#inputQty').val(1);
        $('#inputTotal').val('');
        clearMurid();
    }

    $jenisBiaya.on('change', function () {
        resetForm();
        const val = $(this).val();
        if (val === 'biaya_ppdb') {
            $('#section-biaya-ppdb').removeClass('d-none');
        } else if (val === 'lainnya') {
            $('#section-lainnya').removeClass('d-none');
            $('#section-detail-form').removeClass('d-none');
        } else if (val === 'donasi' || val === 'bantuan_sosial') {
            $('#section-detail-form').removeClass('d-none');
        }
    });

    // ── Pilih Jenis Biaya PPDB → auto-fill nominal & info rekening ──
    $selectBiayaPpdb.on('change', function () {
        const name = $(this).val();
        $('#section-akun-info').addClass('d-none');
        $('#section-btn-pilih-murid').addClass('d-none');
        clearMurid();
        $('#section-detail-form').addClass('d-none');
        $('#inputNominal').val('').prop('readonly', false);
        $('#inputQty').val(1);
        $('#inputTotal').val('');

        if (!name) return;

        $('#section-btn-pilih-murid').removeClass('d-none');

        $.get(biayaDetailUrl, { name }, function (res) {
            if (!res.found) return;
            $('#inputNominal').val(res.amount).prop('readonly', true);
            $('#inputQty').val(1);
            hitungTotal();

            if (res.account) {
                const acc = res.account;
                if (acc.is_qris) {
                    $('#akun-info-rekening').addClass('d-none');
                    $('#akun-info-qris').removeClass('d-none');
                    $('#akun-qris-bank').text(acc.bank_name);
                    $('#akun-qris-img').attr('src', acc.qris_image || '');
                } else {
                    $('#akun-info-qris').addClass('d-none');
                    $('#akun-info-rekening').removeClass('d-none');
                    $('#akun-bank-name').text(acc.bank_name);
                    $('#akun-rekening-number').text(acc.account_number || '—');
                    $('#akun-holder').text(acc.account_holder || '—');
                }
                $('#section-akun-info').removeClass('d-none');
            }
        });
    });

    // ── AJAX Cari Murid ─────────────────────────────────────────────
    let searchTimer = null;

    function loadMurid(keyword) {
        $('#modal-murid-tbody').html('<tr><td colspan="4" class="murid-loader"><i class="bi bi-hourglass-split me-2"></i>Memuat...</td></tr>');
        $.get(searchMuridUrl, { q: keyword }, function (res) {
            let html = '';
            if (res.data.length === 0) {
                html = '<tr><td colspan="4" class="text-center text-muted py-4"><i class="bi bi-person-x fs-3 d-block mb-2"></i>Murid tidak ditemukan.</td></tr>';
            } else {
                res.data.forEach(function (m) {
                    const nisBaru = m.nis_baru ?? '-';
                    const nisn    = m.nisn    ?? '-';
                    const nama    = escapeHtml(m.nama_lengkap);
                    html += `<tr>
                        <td>${nisBaru}</td>
                        <td>${nisn}</td>
                        <td class="fw-semibold">${nama}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-success px-3"
                                    onclick="pilihMurid(${m.id}, '${nama}', '${nisBaru}', '${nisn}')">
                                <i class="bi bi-check-lg me-1"></i>Pilih
                            </button>
                        </td>
                    </tr>`;
                });
            }
            $('#modal-murid-tbody').html(html);
            const info = keyword
                ? `Hasil pencarian: ${res.data.length} ditemukan.`
                : `Menampilkan ${res.data.length} dari ${res.total} murid aktif.`;
            $('#murid-search-info').text(info);
        }).fail(function () {
            $('#modal-murid-tbody').html('<tr><td colspan="4" class="text-center text-danger py-3"><i class="bi bi-exclamation-circle me-2"></i>Gagal memuat data.</td></tr>');
        });
    }

    // Inisialisasi modal instance sekali — simpan ke global
    const modalEl = document.getElementById('modalPilihMurid');
    modalMuridInstance = new bootstrap.Modal(modalEl);

    modalEl.addEventListener('show.bs.modal', function () {
        $('#inputCariMurid').val('');
        loadMurid('');
    });
    $('#inputCariMurid').on('input', function () {
        clearTimeout(searchTimer);
        const kw = $(this).val().trim();
        searchTimer = setTimeout(() => loadMurid(kw), 400);
    });

    hitungTotal();
});

// ── Buka Modal Murid ────────────────────────────────────────────────
function bukaModalMurid() {
    if (modalMuridInstance) modalMuridInstance.show();
}

// ── Pilih Murid ─────────────────────────────────────────────────────
function pilihMurid(id, nama, nis, nisn) {
    $('#inputIdMurid').val(id);
    $('#murid-nama-display').text(nama);
    $('#murid-nis-display').text(nis);
    $('#murid-nisn-display').text(nisn);
    $('#murid-terpilih').removeClass('d-none');
    $('#section-detail-form').removeClass('d-none');
    if (modalMuridInstance) modalMuridInstance.hide();
}

// ── Hapus Pilihan Murid ─────────────────────────────────────────────
function clearMurid() {
    $('#inputIdMurid').val('');
    $('#murid-terpilih').addClass('d-none');
    $('#murid-nama-display, #murid-nis-display, #murid-nisn-display').text('-');
    if ($('#selectJenisBiaya').val() === 'biaya_ppdb') {
        $('#section-detail-form').addClass('d-none');
    }
}

// ── Filter Range ────────────────────────────────────────────────────
function setRange(val) {
    $('#inputRange').val(val);
    $('#inputDateFrom').val('');
    $('#inputDateTo').val('');
    $('#formFilter').submit();
}

// ── Lihat Semua Data per Tab ────────────────────────────────────────
function lihatSemua(tipe) {
    const suffix = tipe === 'tersedia' ? 'tersedia' : 'dihapus';
    document.querySelectorAll('.row-extra-' + suffix).forEach(function (tr) {
        tr.classList.remove('row-extra');
        tr.style.display = '';
    });
    const wrapper = document.getElementById('wrapper-lihat-' + suffix);
    if (wrapper) wrapper.style.display = 'none';
}

// ── Escape HTML ─────────────────────────────────────────────────────
function escapeHtml(str) {
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(str));
    return d.innerHTML;
}

// ════════════════════════════════════════════════════════════════════
//  FITUR EDIT PEMASUKAN
// ════════════════════════════════════════════════════════════════════
const urlEditDataPemasukan = '{{ url("keuangan/pemasukan") }}/'; // + id + /edit-data
let modalMuridEditInstance = null;

// ── Buka form edit ────────────────────────────────────────────────
function bukaEdit(id) {
    $.getJSON(urlEditDataPemasukan + id + '/edit-data', function (data) {
        // Set action form edit
        $('#formEdit').attr('action', urlEditDataPemasukan + id + '/update');

        // Reset semua section edit
        resetFormEdit();

        // Isi jenis pemasukan → trigger change untuk tampilkan section
        $('#edit_selectJenisBiaya').val(data.jenis_pemasukan).trigger('change');

        // Isi field per jenis
        if (data.jenis_pemasukan === 'biaya_ppdb') {
            // Pilih opsi biaya ppdb
            $('#edit_selectJenisBiayaPpdb').val(data.jenis_biaya_ppdb);

            // Load info rekening via AJAX (sama seperti store)
            if (data.jenis_biaya_ppdb) {
                $.get(biayaDetailUrl, { name: data.jenis_biaya_ppdb }, function (res) {
                    if (res.found) {
                        // Nominal readonly karena diisi otomatis
                        $('#edit_inputNominal').val(res.amount).prop('readonly', true);
                        hitungTotalEdit();
                        if (res.account) {
                            tampilAkunEdit(res.account);
                        }
                    }
                });
            }

            // Tampilkan section btn pilih murid
            $('#edit_section-btn-pilih-murid').removeClass('d-none');

            // Preview murid jika ada
            if (data.murid) {
                $('#edit_inputIdMurid').val(data.murid.id);
                $('#edit_murid-nama-display').text(data.murid.nama_lengkap);
                $('#edit_murid-nis-display').text(data.murid.nis_baru ?? '-');
                $('#edit_murid-nisn-display').text(data.murid.nisn ?? '-');
                $('#edit_murid-terpilih').removeClass('d-none');
                $('#edit_section-detail-form').removeClass('d-none');
            }

        } else if (data.jenis_pemasukan === 'lainnya') {
            $('#edit_inputKetLainnya').val(data.keterangan_lainnya ?? '');

        }

        // Isi nominal, qty, total (jika bukan biaya_ppdb yang auto-fill)
        if (data.jenis_pemasukan !== 'biaya_ppdb') {
            $('#edit_inputNominal').val(data.nominal).prop('readonly', false);
        }
        $('#edit_inputQty').val(data.qty);
        $('#edit_inputTotal').val(data.total);
        $('#edit_inputKetBiaya').val(data.keterangan_biaya ?? '');

        // Toggle form
        $('#formPemasukan').addClass('d-none');
        $('#formEdit').removeClass('d-none');
        $('#editModeBanner').addClass('show');
        $('#editModeBannerText').text('Pemasukan: ' + (data.jenis_biaya_ppdb || data.keterangan_lainnya || data.jenis_pemasukan));
        $('#formTitle').html('<i class="bi bi-pencil-square me-2"></i>Edit Pemasukan');

        // Scroll ke form
        $('html, body').animate({ scrollTop: $('#cardForm').offset().top - 20 }, 400);

    }).fail(function () {
        alert('Gagal memuat data. Silakan coba lagi.');
    });
}

// ── Batal edit ────────────────────────────────────────────────────
function batalEdit() {
    $('#formEdit').addClass('d-none');
    $('#formPemasukan').removeClass('d-none');
    $('#editModeBanner').removeClass('show');
    $('#formTitle').html('<i class="bi bi-plus-circle me-2"></i>Input Pemasukan Baru');
    resetFormEdit();
}

// ── Reset semua field dan section form edit ───────────────────────
function resetFormEdit() {
    $('#edit_selectJenisBiaya').val('');
    $('#edit_section-biaya-ppdb').addClass('d-none');
    $('#edit_section-lainnya').addClass('d-none');
    $('#edit_section-detail-form').addClass('d-none');
    $('#edit_section-btn-pilih-murid').addClass('d-none');
    $('#edit_section-akun-info').addClass('d-none');
    $('#edit_selectJenisBiayaPpdb').val('');
    $('#edit_inputKetLainnya').val('');
    $('#edit_inputKetBiaya').val('');
    $('#edit_inputNominal').val('').prop('readonly', false);
    $('#edit_inputQty').val(1);
    $('#edit_inputTotal').val('');
    clearMuridEdit();
}

// ── Logika change jenis biaya (form edit) ─────────────────────────
$('#edit_selectJenisBiaya').on('change', function () {
    const val = $(this).val();
    // Reset section
    $('#edit_section-biaya-ppdb').addClass('d-none');
    $('#edit_section-lainnya').addClass('d-none');
    $('#edit_section-detail-form').addClass('d-none');
    $('#edit_section-btn-pilih-murid').addClass('d-none');
    $('#edit_section-akun-info').addClass('d-none');
    $('#edit_selectJenisBiayaPpdb').val('');
    $('#edit_inputKetLainnya').val('');
    $('#edit_inputNominal').val('').prop('readonly', false);
    $('#edit_inputQty').val(1);
    $('#edit_inputTotal').val('');
    clearMuridEdit();

    if (val === 'biaya_ppdb') {
        $('#edit_section-biaya-ppdb').removeClass('d-none');
    } else if (val === 'lainnya') {
        $('#edit_section-lainnya').removeClass('d-none');
        $('#edit_section-detail-form').removeClass('d-none');
    } else if (val === 'donasi' || val === 'bantuan_sosial') {
        $('#edit_section-detail-form').removeClass('d-none');
    }
});

// ── Pilih biaya PPDB di form edit ─────────────────────────────────
$('#edit_selectJenisBiayaPpdb').on('change', function () {
    const name = $(this).val();
    $('#edit_section-akun-info').addClass('d-none');
    $('#edit_section-btn-pilih-murid').addClass('d-none');
    clearMuridEdit();
    $('#edit_section-detail-form').addClass('d-none');
    $('#edit_inputNominal').val('').prop('readonly', false);
    $('#edit_inputQty').val(1);
    $('#edit_inputTotal').val('');

    if (!name) return;

    $('#edit_section-btn-pilih-murid').removeClass('d-none');

    $.get(biayaDetailUrl, { name }, function (res) {
        if (!res.found) return;
        $('#edit_inputNominal').val(res.amount).prop('readonly', true);
        $('#edit_inputQty').val(1);
        hitungTotalEdit();
        if (res.account) {
            tampilAkunEdit(res.account);
        }
    });
});

// ── Helper tampil info akun di form edit ──────────────────────────
function tampilAkunEdit(acc) {
    if (acc.is_qris) {
        $('#edit_akun-info-rekening').addClass('d-none');
        $('#edit_akun-info-qris').removeClass('d-none');
        $('#edit_akun-qris-bank').text(acc.bank_name);
        $('#edit_akun-qris-img').attr('src', acc.qris_image || '');
    } else {
        $('#edit_akun-info-qris').addClass('d-none');
        $('#edit_akun-info-rekening').removeClass('d-none');
        $('#edit_akun-bank-name').text(acc.bank_name);
        $('#edit_akun-rekening-number').text(acc.account_number || '—');
        $('#edit_akun-holder').text(acc.account_holder || '—');
    }
    $('#edit_section-akun-info').removeClass('d-none');
}

// ── Hitung total edit ─────────────────────────────────────────────
function hitungTotalEdit() {
    const nominal = parseInt($('#edit_inputNominal').val()) || 0;
    const qty     = parseInt($('#edit_inputQty').val())     || 0;
    $('#edit_inputTotal').val(nominal * qty);
}

// ── Modal murid untuk form edit ───────────────────────────────────
function bukaModalMuridEdit() {
    if (!modalMuridEditInstance) {
        modalMuridEditInstance = new bootstrap.Modal(document.getElementById('modalPilihMuridEdit'));
        document.getElementById('modalPilihMuridEdit').addEventListener('show.bs.modal', function () {
            $('#inputCariMuridEdit').val('');
            loadMuridEdit('');
        });
        $('#inputCariMuridEdit').on('input', function () {
            clearTimeout(window._editMuridTimer);
            const kw = $(this).val().trim();
            window._editMuridTimer = setTimeout(() => loadMuridEdit(kw), 400);
        });
    }
    modalMuridEditInstance.show();
}

function loadMuridEdit(keyword) {
    $('#modal-murid-edit-tbody').html('<tr><td colspan="4" class="murid-loader"><i class="bi bi-hourglass-split me-2"></i>Memuat...</td></tr>');
    $.get(searchMuridUrl, { q: keyword }, function (res) {
        let html = '';
        if (res.data.length === 0) {
            html = '<tr><td colspan="4" class="text-center text-muted py-4"><i class="bi bi-person-x fs-3 d-block mb-2"></i>Murid tidak ditemukan.</td></tr>';
        } else {
            res.data.forEach(function (m) {
                const nisBaru = m.nis_baru ?? '-';
                const nisn    = m.nisn    ?? '-';
                const nama    = escapeHtml(m.nama_lengkap);
                html += `<tr>
                    <td>${nisBaru}</td>
                    <td>${nisn}</td>
                    <td class="fw-semibold">${nama}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-success px-3"
                                onclick="pilihMuridEdit(${m.id}, '${nama}', '${nisBaru}', '${nisn}')">
                            <i class="bi bi-check-lg me-1"></i>Pilih
                        </button>
                    </td>
                </tr>`;
            });
        }
        $('#modal-murid-edit-tbody').html(html);
        const info = keyword
            ? `Hasil pencarian: ${res.data.length} ditemukan.`
            : `Menampilkan ${res.data.length} dari ${res.total} murid aktif.`;
        $('#murid-edit-search-info').text(info);
    }).fail(function () {
        $('#modal-murid-edit-tbody').html('<tr><td colspan="4" class="text-center text-danger py-3"><i class="bi bi-exclamation-circle me-2"></i>Gagal memuat data.</td></tr>');
    });
}

function pilihMuridEdit(id, nama, nis, nisn) {
    $('#edit_inputIdMurid').val(id);
    $('#edit_murid-nama-display').text(nama);
    $('#edit_murid-nis-display').text(nis);
    $('#edit_murid-nisn-display').text(nisn);
    $('#edit_murid-terpilih').removeClass('d-none');
    $('#edit_section-detail-form').removeClass('d-none');
    if (modalMuridEditInstance) modalMuridEditInstance.hide();
}

function clearMuridEdit() {
    $('#edit_inputIdMurid').val('');
    $('#edit_murid-terpilih').addClass('d-none');
    $('#edit_murid-nama-display, #edit_murid-nis-display, #edit_murid-nisn-display').text('-');
    if ($('#edit_selectJenisBiaya').val() === 'biaya_ppdb') {
        $('#edit_section-detail-form').addClass('d-none');
    }
}
</script>
</body>
</html>



