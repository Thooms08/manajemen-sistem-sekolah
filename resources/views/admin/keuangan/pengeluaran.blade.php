<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengeluaran Keuangan</title>
        @include('favicon')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root { --primary-red: #dc3545; }
        body { font-family: 'Inter', sans-serif; background-color: #f4f7f6; overflow-x: hidden; }
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        #content { width: 100%; padding: 20px 30px; transition: all 0.3s; min-height: 100vh; min-width: 0; }
        #sidebarCollapse { width: 45px; height: 45px; background: var(--primary-red); border: none; color: white; border-radius: 10px; box-shadow: 0 4px 10px rgba(220,53,69,0.2); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .table thead { background-color: var(--primary-red); color: white; }
        .table thead th { font-size: 0.82rem; letter-spacing: 0.4px; font-weight: 600; white-space: nowrap; }
        #overlay { display: none; position: fixed; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 1040; top: 0; left: 0; }
        #overlay.active { display: block; }
        input:focus, textarea:focus, select:focus { border-color: #dc3545 !important; outline: none !important; box-shadow: 0 0 0 0.2rem rgba(220,53,69,0.15) !important; }

        /* Summary cards */
        .summary-card { border-radius: 12px; border: none; transition: transform 0.2s; }
        .summary-card:hover { transform: translateY(-2px); }
        .summary-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0; }

        /* Form labels */
        .form-label { font-weight: 600; font-size: 0.85rem; }
        .hint-text { font-size: 0.75rem; color: #6c757d; margin-top: 4px; }

        /* Baris pengeluaran */
        .row-pengeluaran { background: #fff; border: 1px solid #dee2e6; border-radius: 12px; padding: 16px; margin-bottom: 12px; position: relative; transition: box-shadow 0.2s; }
        .row-pengeluaran:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .row-pengeluaran .row-number { font-size: 0.72rem; font-weight: 700; color: #dc3545; background: #fde8ea; padding: 2px 8px; border-radius: 20px; }
        .btn-hapus-row { position: absolute; top: 10px; right: 12px; background: none; border: none; color: #dc3545; font-size: 1.2rem; cursor: pointer; opacity: 0.7; transition: opacity 0.2s; }
        .btn-hapus-row:hover { opacity: 1; }

        /* Mode edit */
        .edit-mode-banner { background: #fde8ea; border-radius: 8px; padding: 8px 14px; font-size: 0.83rem; color: #842029; font-weight: 600; display: none; }
        .edit-mode-banner.show { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

        /* Upload foto */
        .upload-area { border: 2px dashed #dee2e6; border-radius: 10px; padding: 14px; background: #fafafa; transition: border-color 0.2s; }
        .upload-area:hover { border-color: #dc3545; }
        .foto-list { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; }
        .foto-item { position: relative; width: 80px; height: 80px; }
        .foto-item img { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 2px solid #dee2e6; cursor: pointer; transition: border-color 0.2s; display: block; }
        .foto-item img:hover { border-color: #dc3545; }
        .foto-item .btn-hapus-foto { position: absolute; top: -6px; right: -6px; width: 20px; height: 20px; background: #dc3545; border: none; border-radius: 50%; color: #fff; font-size: 0.7rem; line-height: 1; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 2; padding: 0; }
        .foto-item .btn-hapus-foto:hover { background: #b02a37; }
        .foto-item .badge-existing { position: absolute; bottom: 2px; left: 2px; font-size: 0.6rem; background: rgba(0,0,0,0.55); color: #fff; border-radius: 3px; padding: 1px 4px; }

        /* Filter bar */
        .filter-bar { background: #fff; border-radius: 12px; padding: 14px 18px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .btn-range { font-size: 0.8rem; padding: 5px 14px; border-radius: 20px; font-weight: 600; }
        .btn-range.active { background: var(--primary-red); color: #fff; border-color: var(--primary-red); }
        .btn-range:not(.active) { background: #fff; color: #495057; border-color: #dee2e6; }
        .btn-range:not(.active):hover { background: #fde8ea; border-color: var(--primary-red); color: var(--primary-red); }

        /* Nav tabs */
        .nav-tabs .nav-link { color: #6c757d; font-weight: 500; border-radius: 8px 8px 0 0; }
        .nav-tabs .nav-link.active { color: #dc3545; font-weight: 600; border-bottom-color: #fff; }
        .nav-tabs .nav-link:hover { color: #dc3545; }

        /* Baris tersembunyi */
        .row-extra { display: none; }

        /* Ikon bukti di tabel */
        .btn-lihat-bukti { background: none; border: none; color: #6c757d; font-size: 1.4rem; cursor: pointer; transition: color 0.2s, transform 0.15s; padding: 0; }
        .btn-lihat-bukti:hover { color: #dc3545; transform: scale(1.15); }
        .btn-lihat-bukti-none { color: #ccc; font-size: 1.1rem; }

        /* Label diedit */
        .label-edited { font-size: 0.68rem; color: #6c757d; font-style: italic; display: block; margin-top: 2px; }

        /* Modal galeri bukti */
        #modalGaleriBukti .modal-body { background: #1a1a2e; border-radius: 0 0 12px 12px; }
        .galeri-grid { display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; padding: 8px; }
        .galeri-grid img { max-height: 220px; max-width: 100%; border-radius: 8px; cursor: pointer; border: 2px solid transparent; transition: border-color 0.2s, transform 0.2s; object-fit: contain; background: #000; }
        .galeri-grid img:hover { border-color: #dc3545; transform: scale(1.03); }
        .galeri-empty { color: #aaa; text-align: center; padding: 30px; }

        /* ── Responsive ── */
        @media (max-width: 991px) {
            #content { padding: 16px 18px; }
        }
        @media (max-width: 767px) {
            #content { padding: 12px 12px; }
            /* Filter bar: tanggal stack */
            .filter-bar .date-range-row { flex-direction: column; align-items: stretch !important; }
            .filter-bar .date-range-row input[type="date"] { width: 100% !important; max-width: 100% !important; }
            .filter-bar .date-range-row .btn { width: 100%; }
            /* Form multi-baris: kolom nominal/qty stack */
            .row-pengeluaran .row.g-3 .col-md-3,
            .row-pengeluaran .row.g-3 .col-md-2 { flex: 0 0 50%; max-width: 50%; }
            /* Tabel font lebih kecil */
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
                <h4 class="mb-0 fw-bold text-danger">
                    <i class="bi bi-arrow-up-circle me-2"></i>Pengeluaran Keuangan
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
                 FORM INPUT / EDIT PENGELUARAN
            ══════════════════════════════════════════ --}}
            <div class="card p-4 mb-4" id="cardForm">
                {{-- Banner mode edit --}}
                <div class="edit-mode-banner mb-3" id="editModeBanner">
                    <i class="bi bi-pencil-square"></i>
                    <span id="editModeBannerText">Mode Edit</span>
                    <button type="button" class="btn btn-sm btn-outline-danger ms-auto" onclick="batalEdit()">
                        <i class="bi bi-x-lg me-1"></i>Batal Edit
                    </button>
                </div>

                <h5 class="fw-bold text-danger mb-3" id="formTitle">
                    <i class="bi bi-plus-circle me-2"></i>Input Pengeluaran Baru
                </h5>

                {{-- ── FORM STORE (Multiple) ── --}}
                <form action="{{ route('keuangan.pengeluaran.store') }}" method="POST"
                      enctype="multipart/form-data" id="formStore">
                    @csrf
                    <div id="rows-container">
                        {{-- Baris pertama (template) --}}
                        <div class="row-pengeluaran card-form-inner" data-index="0">
                            <span class="row-number">Pengeluaran #1</span>
                            <button type="button" class="btn-hapus-row d-none" onclick="hapusRow(this)" title="Hapus baris">
                                <i class="bi bi-x-circle-fill"></i>
                            </button>
                            <div class="row g-3 mt-1">
                                {{-- Jenis Pengeluaran --}}
                                <div class="col-md-4">
                                    <label class="form-label">Jenis Pengeluaran <span class="text-danger">*</span></label>
                                    <select name="rows[0][jenis_pengeluaran]" class="form-select select-jenis" required
                                            onchange="handleJenisChange(this)">
                                        <option value="">-- Pilih Jenis --</option>
                                        <option value="operasional">Operasional</option>
                                        <option value="gaji_staff">Gaji Staff</option>
                                        <option value="gaji_guru">Gaji Guru</option>
                                        <option value="lainnya">Lainnya</option>
                                    </select>
                                </div>
                                {{-- Keterangan Lainnya --}}
                                <div class="col-md-4 section-lainnya d-none">
                                    <label class="form-label">Keterangan Jenis <span class="text-danger">*</span></label>
                                    <input type="text" name="rows[0][keterangan_lainnya]" class="form-control"
                                           placeholder="Contoh: Biaya Konsumsi Rapat...">
                                </div>
                                {{-- Nama --}}
                                <div class="col-md-4">
                                    <label class="form-label">Nama Pengeluaran <span class="text-danger">*</span></label>
                                    <input type="text" name="rows[0][nama_pengeluaran]" class="form-control"
                                           placeholder="Contoh: Beli ATK..." required>
                                </div>
                                {{-- Nominal --}}
                                <div class="col-md-3">
                                    <label class="form-label">Nominal (Rp) <span class="text-danger">*</span></label>
                                    <input type="number" name="rows[0][nominal]" class="form-control input-nominal"
                                           placeholder="0" min="1" required oninput="hitungTotal(this)">
                                </div>
                                {{-- QTY --}}
                                <div class="col-md-2">
                                    <label class="form-label">QTY <span class="text-danger">*</span></label>
                                    <input type="number" name="rows[0][qty]" class="form-control input-qty"
                                           placeholder="1" min="1" value="1" required oninput="hitungTotal(this)">
                                </div>
                                {{-- Total --}}
                                <div class="col-md-3">
                                    <label class="form-label">Total (Rp)</label>
                                    <input type="number" name="rows[0][total]" class="form-control input-total bg-light"
                                           placeholder="0" readonly>
                                    <div class="hint-text"><i class="bi bi-info-circle me-1"></i>Otomatis: Nominal × QTY</div>
                                </div>
                                {{-- Tombol Tambah --}}
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-danger btn-sm px-3 fw-semibold"
                                            onclick="tambahRow()">
                                        <i class="bi bi-plus-lg me-1"></i>Tambah
                                    </button>
                                </div>
                                {{-- Deskripsi --}}
                                <div class="col-12">
                                    <label class="form-label">Deskripsi <span class="text-muted fw-normal small">(Opsional)</span></label>
                                    <textarea name="rows[0][deskripsi]" class="form-control" rows="2"
                                              placeholder="Keterangan tambahan tentang pengeluaran ini..."></textarea>
                                </div>
                                {{-- Upload Bukti Foto --}}
                                <div class="col-12">
                                    <label class="form-label">Bukti Foto <span class="text-muted fw-normal small">(Opsional, maks 2MB/foto)</span></label>
                                    <div class="upload-area">
                                        <div class="foto-list" id="foto-list-0"></div>
                                        <button type="button" class="btn btn-sm btn-outline-secondary mt-2"
                                                onclick="document.getElementById('input-foto-0').click()">
                                            <i class="bi bi-image me-1"></i>Pilih Foto
                                        </button>
                                        <input type="file" id="input-foto-0" accept="image/*" class="d-none"
                                               onchange="tambahSatuFoto(this, 0)">
                                        <div class="hint-text mt-1">
                                            <i class="bi bi-info-circle me-1"></i>Format: JPG, PNG, WEBP. Klik "×" untuk hapus foto.
                                        </div>
                                    </div>
                                    <div id="foto-inputs-0"></div>
                                </div>
                            </div>
                        </div>{{-- end row-pengeluaran --}}
                    </div>
                    <div class="mt-3 text-end" id="btnStoreWrapper">
                        <button type="submit" class="btn btn-danger px-5 shadow-sm fw-bold">
                            <i class="bi bi-save me-2"></i>Simpan Semua Pengeluaran
                        </button>
                    </div>
                </form>

                {{-- ── FORM UPDATE (Single Edit) ── --}}
                <form action="" method="POST" enctype="multipart/form-data"
                      id="formEdit" class="d-none">
                    @csrf
                    @method('POST')
                    {{-- Field dihapus saat submit edit --}}
                    <input type="hidden" name="deleted_bukti_ids" id="editDeletedBuktiIds">

                    <div class="row-pengeluaran card-form-inner" id="editRowWrapper">
                        {{-- Jenis Pengeluaran --}}
                        <div class="row g-3 mt-1">
                            <div class="col-md-4">
                                <label class="form-label">Jenis Pengeluaran <span class="text-danger">*</span></label>
                                <select name="jenis_pengeluaran" id="edit_jenis" class="form-select" required
                                        onchange="handleEditJenisChange(this)">
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="operasional">Operasional</option>
                                    <option value="gaji_staff">Gaji Staff</option>
                                    <option value="gaji_guru">Gaji Guru</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="col-md-4" id="edit_section_lainnya" style="display:none;">
                                <label class="form-label">Keterangan Jenis <span class="text-danger">*</span></label>
                                <input type="text" name="keterangan_lainnya" id="edit_keterangan_lainnya"
                                       class="form-control" placeholder="Contoh: Biaya Konsumsi Rapat...">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Nama Pengeluaran <span class="text-danger">*</span></label>
                                <input type="text" name="nama_pengeluaran" id="edit_nama"
                                       class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Nominal (Rp) <span class="text-danger">*</span></label>
                                <input type="number" name="nominal" id="edit_nominal"
                                       class="form-control" min="1" required oninput="hitungTotalEdit()">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">QTY <span class="text-danger">*</span></label>
                                <input type="number" name="qty" id="edit_qty"
                                       class="form-control" min="1" value="1" required oninput="hitungTotalEdit()">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Total (Rp)</label>
                                <input type="number" name="total" id="edit_total"
                                       class="form-control bg-light" readonly>
                                <div class="hint-text"><i class="bi bi-info-circle me-1"></i>Otomatis: Nominal × QTY</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Deskripsi <span class="text-muted fw-normal small">(Opsional)</span></label>
                                <textarea name="deskripsi" id="edit_deskripsi" class="form-control" rows="2"
                                          placeholder="Keterangan tambahan..."></textarea>
                            </div>

                            {{-- Bukti foto (existing + upload baru) --}}
                            <div class="col-12">
                                <label class="form-label">Bukti Foto <span class="text-muted fw-normal small">(Opsional)</span></label>
                                <div class="upload-area">
                                    {{-- Preview foto existing + baru --}}
                                    <div class="foto-list" id="edit_foto_list"></div>
                                    {{-- Tombol pilih foto baru --}}
                                    <button type="button" class="btn btn-sm btn-outline-secondary mt-2"
                                            onclick="document.getElementById('edit_input_foto').click()">
                                        <i class="bi bi-image me-1"></i>Pilih Foto
                                    </button>
                                    <input type="file" id="edit_input_foto" accept="image/*" class="d-none"
                                           onchange="tambahFotoEdit(this)">
                                    <div class="hint-text mt-1">
                                        <i class="bi bi-info-circle me-1"></i>Format: JPG, PNG, WEBP. Maks 2MB per foto. Klik "×" pada foto untuk menghapus.
                                    </div>
                                </div>
                                {{-- Hidden inputs foto baru akan di-inject via JS --}}
                                <div id="edit_new_foto_inputs"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-secondary px-4" onclick="batalEdit()">
                            <i class="bi bi-x-lg me-1"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-warning px-5 shadow-sm fw-bold text-dark">
                            <i class="bi bi-pencil-square me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            {{-- ══════════════════════════════════════════
                 SUMMARY CARDS
            ══════════════════════════════════════════ --}}
            <div class="row g-3 mb-4">
                <div class="col-12 col-sm-6">
                    <div class="card summary-card p-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="summary-icon bg-danger bg-opacity-10">
                                <i class="bi bi-cash-stack text-danger"></i>
                            </div>
                            <div class="min-width-0">
                                <div class="text-muted small fw-semibold">Total Nominal Pengeluaran</div>
                                <div class="fw-bold fs-5 text-danger text-truncate">
                                    Rp {{ number_format($totalNominal, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <div class="card summary-card p-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="summary-icon bg-warning bg-opacity-10">
                                <i class="bi bi-activity text-warning"></i>
                            </div>
                            <div class="min-width-0">
                                <div class="text-muted small fw-semibold">Total Aktivitas Pengeluaran</div>
                                <div class="fw-bold fs-5 text-warning">
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

                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <h5 class="fw-bold text-danger mb-0">
                        <i class="bi bi-table me-2"></i>Laporan Pengeluaran
                    </h5>
                    <a href="{{ route('keuangan.pengeluaran.export-excel', array_filter(['range' => $range, 'date_from' => $dateFrom, 'date_to' => $dateTo])) }}"
                       class="btn btn-danger btn-sm px-4 shadow-sm fw-semibold">
                        <i class="bi bi-file-earmark-excel me-2"></i>Download Excel
                    </a>
                </div>

                {{-- Filter Bar --}}
                <form method="GET" action="{{ route('keuangan.pengeluaran.index') }}" id="formFilter">
                    <div class="filter-bar mb-3">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                            <span class="text-muted small fw-semibold me-1">Filter:</span>
                            @foreach(['1hari' => '1 Hari', '1minggu' => '1 Minggu', '1bulan' => '1 Bulan', '1tahun' => '1 Tahun'] as $key => $label)
                                <button type="button"
                                        class="btn btn-range {{ $range === $key ? 'active' : '' }}"
                                        onclick="setRange('{{ $key }}')">{{ $label }}</button>
                            @endforeach
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-2 date-range-row">
                            <span class="text-muted small fw-semibold">Atau pilih tanggal:</span>
                            <input type="date" name="date_from" id="inputDateFrom"
                                   class="form-control form-control-sm flex-fill" style="min-width:130px; max-width:180px;" value="{{ $dateFrom }}">
                            <span class="text-muted small">s/d</span>
                            <input type="date" name="date_to" id="inputDateTo"
                                   class="form-control form-control-sm flex-fill" style="min-width:130px; max-width:180px;" value="{{ $dateTo }}">
                            <button type="submit" class="btn btn-outline-danger btn-sm px-3 fw-semibold">
                                <i class="bi bi-funnel me-1"></i>Terapkan
                            </button>
                            @if($range !== '1bulan' || $dateFrom || $dateTo)
                                <a href="{{ route('keuangan.pengeluaran.index') }}"
                                   class="btn btn-outline-secondary btn-sm px-3">
                                    <i class="bi bi-x-circle me-1"></i>Reset
                                </a>
                            @endif
                        </div>
                        <input type="hidden" name="range" id="inputRange" value="{{ $range }}">
                        <input type="hidden" name="tab" value="{{ $tab }}">
                    </div>
                </form>

                @php
                    $rangeLabel = ['1hari' => '1 Hari Terakhir', '1minggu' => '1 Minggu Terakhir', '1bulan' => '1 Bulan Terakhir', '1tahun' => '1 Tahun Terakhir', 'custom' => 'Custom'];
                @endphp
                <div class="text-muted small mb-3">
                    <i class="bi bi-calendar3 me-1"></i>Menampilkan data:
                    @if($range === 'custom' && $dateFrom && $dateTo)
                        <strong>{{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }} — {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}</strong>
                    @else
                        <strong>{{ $rangeLabel[$range] ?? '1 Bulan Terakhir' }}</strong>
                    @endif
                </div>

                {{-- Tab --}}
                <ul class="nav nav-tabs mb-3" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link {{ $tab === 'tersedia' ? 'active' : '' }} d-flex align-items-center gap-2"
                           href="{{ route('keuangan.pengeluaran.index', array_filter(['tab' => 'tersedia', 'range' => $range, 'date_from' => $dateFrom, 'date_to' => $dateTo])) }}">
                            <i class="bi bi-check-circle{{ $tab === 'tersedia' ? '-fill' : '' }} text-danger"></i>
                            Tersedia <span class="badge bg-danger ms-1">{{ $pengeluarans->count() }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $tab === 'dihapus' ? 'active' : '' }} d-flex align-items-center gap-2"
                           href="{{ route('keuangan.pengeluaran.index', array_filter(['tab' => 'dihapus', 'range' => $range, 'date_from' => $dateFrom, 'date_to' => $dateTo])) }}">
                            <i class="bi bi-trash{{ $tab === 'dihapus' ? '-fill' : '' }} text-secondary"></i>
                            Dihapus <span class="badge bg-secondary ms-1">{{ $dihapusList->count() }}</span>
                        </a>
                    </li>
                </ul>

                @php
                    $badgeMap = [
                        'operasional' => ['bg-primary',           'Operasional'],
                        'gaji_staff'  => ['bg-warning text-dark', 'Gaji Staff'],
                        'gaji_guru'   => ['bg-info text-dark',    'Gaji Guru'],
                        'lainnya'     => ['bg-secondary',         'Lainnya'],
                    ];
                @endphp

                {{-- ══ TAB TERSEDIA ══ --}}
                <div class="{{ $tab === 'tersedia' ? '' : 'd-none' }}" id="panel-tersedia">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th width="40">No</th>
                                    <th>Tanggal</th>
                                    <th>Jenis</th>
                                    <th>Nama Pengeluaran</th>
                                    <th>Deskripsi</th>
                                    <th class="text-end">Nominal</th>
                                    <th class="text-center">QTY</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-center" width="60">Bukti</th>
                                    <th class="text-center" width="110">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pengeluarans as $index => $p)
                                <tr class="{{ $index >= 20 ? 'row-extra row-extra-tersedia' : '' }}"
                                    id="row-tersedia-{{ $p->id }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td class="text-muted small">
                                        {{ \Carbon\Carbon::parse($p->created_at)->format('d M Y') }}
                                        @if($p->edited_at)
                                            <span class="label-edited">
                                                <i class="bi bi-pencil-fill me-1" style="font-size:0.6rem;"></i>diedit {{ \Carbon\Carbon::parse($p->edited_at)->format('d M Y, H:i') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @php [$bc, $bl] = $badgeMap[$p->jenis_pengeluaran] ?? ['bg-secondary', $p->jenis_pengeluaran]; @endphp
                                        <span class="badge {{ $bc }}">{{ $bl }}</span>
                                        @if($p->jenis_pengeluaran === 'lainnya' && $p->keterangan_lainnya)
                                            <div class="text-muted" style="font-size:0.72rem;">{{ $p->keterangan_lainnya }}</div>
                                        @endif
                                    </td>
                                    <td class="fw-semibold small">{{ $p->nama_pengeluaran }}</td>
                                    <td class="text-muted small" style="max-width:140px; white-space:normal;">{{ $p->deskripsi ?? '—' }}</td>
                                    <td class="text-end fw-semibold small">Rp {{ number_format($p->nominal, 0, ',', '.') }}</td>
                                    <td class="text-center small">{{ $p->qty }}</td>
                                    <td class="text-end fw-bold text-danger small">Rp {{ number_format($p->total, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        @if($p->buktiPengeluaran->isNotEmpty())
                                            @php
                                                $buktiJson = $p->buktiPengeluaran->map(fn($b) => route('keuangan.pengeluaran.bukti', $b->id))->toJson();
                                            @endphp
                                            <button type="button" class="btn-lihat-bukti"
                                                    onclick="lihatGaleriBukti({{ $buktiJson }})"
                                                    title="{{ $p->buktiPengeluaran->count() }} foto bukti">
                                                <i class="bi bi-images"></i>
                                            </button>
                                        @else
                                            <span class="btn-lihat-bukti-none" title="Tidak ada bukti">
                                                <i class="bi bi-image text-muted"></i>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {{-- Tombol Edit --}}
                                        <button type="button"
                                                class="btn btn-sm btn-outline-warning border-0"
                                                title="Edit"
                                                onclick="bukaEdit({{ $p->id }})">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        {{-- Tombol Hapus --}}
                                        <form action="{{ route('keuangan.pengeluaran.destroy', $p->id) }}"
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
                                        Tidak ada data pengeluaran pada periode ini.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($pengeluarans->count() > 20)
                    <div class="text-center mt-2" id="wrapper-lihat-tersedia">
                        <button class="btn btn-outline-danger btn-sm px-4" onclick="lihatSemua('tersedia')">
                            <i class="bi bi-chevron-down me-1"></i>Lihat Semua ({{ $pengeluarans->count() - 20 }} lainnya)
                        </button>
                    </div>
                    @endif
                </div>

                {{-- ══ TAB DIHAPUS ══ --}}
                <div class="{{ $tab === 'dihapus' ? '' : 'd-none' }}" id="panel-dihapus">
                    @if($tab === 'dihapus')
                    <div class="alert alert-warning border-0 py-2 mb-3 small">
                        <i class="bi bi-info-circle me-1"></i>
                        Data di sini telah dihapus dari laporan aktif. Anda dapat memulihkan atau menghapus permanen.
                    </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th width="40">No</th>
                                    <th>Tanggal</th>
                                    <th>Jenis</th>
                                    <th>Nama Pengeluaran</th>
                                    <th>Deskripsi</th>
                                    <th class="text-end">Nominal</th>
                                    <th class="text-center">QTY</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-center" width="60">Bukti</th>
                                    <th class="text-center" width="110">Aksi</th>
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
                                                <i class="bi bi-pencil-fill me-1" style="font-size:0.6rem;"></i>diedit {{ \Carbon\Carbon::parse($p->edited_at)->format('d M Y, H:i') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @php [$bc, $bl] = $badgeMap[$p->jenis_pengeluaran] ?? ['bg-secondary', $p->jenis_pengeluaran]; @endphp
                                        <span class="badge {{ $bc }}">{{ $bl }}</span>
                                        @if($p->jenis_pengeluaran === 'lainnya' && $p->keterangan_lainnya)
                                            <div class="text-muted" style="font-size:0.72rem;">{{ $p->keterangan_lainnya }}</div>
                                        @endif
                                    </td>
                                    <td class="fw-semibold small">{{ $p->nama_pengeluaran }}</td>
                                    <td class="text-muted small" style="max-width:140px; white-space:normal;">{{ $p->deskripsi ?? '—' }}</td>
                                    <td class="text-end fw-semibold small">Rp {{ number_format($p->nominal, 0, ',', '.') }}</td>
                                    <td class="text-center small">{{ $p->qty }}</td>
                                    <td class="text-end fw-bold text-secondary small">Rp {{ number_format($p->total, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        @if($p->buktiPengeluaran->isNotEmpty())
                                            @php
                                                $buktiJson = $p->buktiPengeluaran->map(fn($b) => route('keuangan.pengeluaran.bukti', $b->id))->toJson();
                                            @endphp
                                            <button type="button" class="btn-lihat-bukti"
                                                    onclick="lihatGaleriBukti({{ $buktiJson }})"
                                                    title="{{ $p->buktiPengeluaran->count() }} foto bukti">
                                                <i class="bi bi-images"></i>
                                            </button>
                                        @else
                                            <span class="btn-lihat-bukti-none"><i class="bi bi-image text-muted"></i></span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <form action="{{ route('keuangan.pengeluaran.restore', $p->id) }}"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Pulihkan data ini?')">
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
                        <button class="btn btn-outline-secondary btn-sm px-4" onclick="lihatSemua('dihapus')">
                            <i class="bi bi-chevron-down me-1"></i>Lihat Semua ({{ $dihapusList->count() - 20 }} lainnya)
                        </button>
                    </div>
                    @endif
                </div>

            </div>{{-- end card laporan --}}

        </div>{{-- end container-fluid --}}
    </div>{{-- end #content --}}
</div>{{-- end .wrapper --}}


{{-- ══════════════════════════════════
     MODAL: GALERI BUKTI FOTO
══════════════════════════════════ --}}
<div class="modal fade" id="modalGaleriBukti" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="background:#1a1a2e; color:#fff;">
                <h6 class="modal-title fw-bold">
                    <i class="bi bi-images me-2"></i>Bukti Pengeluaran
                    <span id="galeriBuktiCount" class="badge bg-danger ms-2"></span>
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3" style="background:#1a1a2e;">
                <div class="galeri-grid" id="galeriBuktiGrid">
                    <div class="galeri-empty"><i class="bi bi-hourglass-split me-2"></i>Memuat...</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL: Preview Foto Full --}}
<div class="modal fade" id="modalFotoFull" tabindex="-1" style="z-index:1060;">
    <div class="modal-dialog modal-dialog-centered" style="max-width:90vw;">
        <div class="modal-content border-0 bg-transparent shadow-none">
            <div class="modal-body p-0 text-center position-relative">
                <button type="button" class="btn btn-light btn-sm position-absolute top-0 end-0 m-2 rounded-circle"
                        data-bs-dismiss="modal" style="z-index:10;">
                    <i class="bi bi-x-lg"></i>
                </button>
                <img id="fotoFullImg" src="" alt="Bukti"
                     class="img-fluid rounded shadow" style="max-height:90vh; max-width:90vw;">
            </div>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ── Konstanta URL ────────────────────────────────────────────────────
const urlEditData   = '{{ url("keuangan/pengeluaran") }}/';   // + id + /edit-data
const urlUpdateBase = '{{ url("keuangan/pengeluaran") }}/';   // + id + /update

// ── Sidebar ──────────────────────────────────────────────────────────
window.toggleSidebar = function () {
    if ($(window).width() <= 768) {
        $('#sidebar').toggleClass('show-mobile');
        $('#overlay').toggleClass('active');
    } else {
        $('#sidebar').toggleClass('inactive');
    }
};
$('#overlay').on('click', toggleSidebar);

// ════════════════════════════════════════════════════════════════════
//  FORM STORE: MULTIPLE BARIS
// ════════════════════════════════════════════════════════════════════
let rowCount = 1;

// Tiap index baris punya array File-nya sendiri
const fotoFiles = {};   // fotoFiles[idx] = [ File, File, ... ]

function tambahRow() {
    const idx = rowCount;
    fotoFiles[idx] = [];

    const html = `
    <div class="row-pengeluaran card-form-inner" data-index="${idx}">
        <span class="row-number">Pengeluaran #${idx + 1}</span>
        <button type="button" class="btn-hapus-row" onclick="hapusRow(this)" title="Hapus baris">
            <i class="bi bi-x-circle-fill"></i>
        </button>
        <div class="row g-3 mt-1">
            <div class="col-md-4">
                <label class="form-label">Jenis Pengeluaran <span class="text-danger">*</span></label>
                <select name="rows[${idx}][jenis_pengeluaran]" class="form-select select-jenis" required
                        onchange="handleJenisChange(this)">
                    <option value="">-- Pilih Jenis --</option>
                    <option value="operasional">Operasional</option>
                    <option value="gaji_staff">Gaji Staff</option>
                    <option value="gaji_guru">Gaji Guru</option>
                    <option value="lainnya">Lainnya</option>
                </select>
            </div>
            <div class="col-md-4 section-lainnya d-none">
                <label class="form-label">Keterangan Jenis <span class="text-danger">*</span></label>
                <input type="text" name="rows[${idx}][keterangan_lainnya]" class="form-control"
                       placeholder="Contoh: Biaya Konsumsi Rapat...">
            </div>
            <div class="col-md-4">
                <label class="form-label">Nama Pengeluaran <span class="text-danger">*</span></label>
                <input type="text" name="rows[${idx}][nama_pengeluaran]" class="form-control"
                       placeholder="Contoh: Beli ATK..." required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Nominal (Rp) <span class="text-danger">*</span></label>
                <input type="number" name="rows[${idx}][nominal]" class="form-control input-nominal"
                       placeholder="0" min="1" required oninput="hitungTotal(this)">
            </div>
            <div class="col-md-2">
                <label class="form-label">QTY <span class="text-danger">*</span></label>
                <input type="number" name="rows[${idx}][qty]" class="form-control input-qty"
                       placeholder="1" min="1" value="1" required oninput="hitungTotal(this)">
            </div>
            <div class="col-md-3">
                <label class="form-label">Total (Rp)</label>
                <input type="number" name="rows[${idx}][total]" class="form-control input-total bg-light"
                       placeholder="0" readonly>
                <div class="hint-text"><i class="bi bi-info-circle me-1"></i>Otomatis: Nominal × QTY</div>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="button" class="btn btn-outline-danger btn-sm px-3 fw-semibold"
                        onclick="tambahRow()">
                    <i class="bi bi-plus-lg me-1"></i>Tambah
                </button>
            </div>
            <div class="col-12">
                <label class="form-label">Deskripsi <span class="text-muted fw-normal small">(Opsional)</span></label>
                <textarea name="rows[${idx}][deskripsi]" class="form-control" rows="2"
                          placeholder="Keterangan tambahan..."></textarea>
            </div>
            <div class="col-12">
                <label class="form-label">Bukti Foto <span class="text-muted fw-normal small">(Opsional, maks 2MB/foto)</span></label>
                <div class="upload-area">
                    <div class="foto-list" id="foto-list-${idx}"></div>
                    <button type="button" class="btn btn-sm btn-outline-secondary mt-2"
                            onclick="document.getElementById('input-foto-${idx}').click()">
                        <i class="bi bi-image me-1"></i>Pilih Foto
                    </button>
                    <input type="file" id="input-foto-${idx}" accept="image/*" class="d-none"
                           onchange="tambahSatuFoto(this, ${idx})">
                    <div class="hint-text mt-1">
                        <i class="bi bi-info-circle me-1"></i>Format: JPG, PNG, WEBP. Klik "×" untuk hapus foto.
                    </div>
                </div>
                <div id="foto-inputs-${idx}"></div>
            </div>
        </div>
    </div>`;

    $('#rows-container').append(html);
    fotoFiles[idx] = [];
    rowCount++;
    updateRowNumbers();
}

function hapusRow(btn) {
    $(btn).closest('.row-pengeluaran').remove();
    updateRowNumbers();
}

function updateRowNumbers() {
    const rows = $('#rows-container .row-pengeluaran');
    rows.each(function (i) {
        $(this).find('.row-number').text('Pengeluaran #' + (i + 1));
        $(this).find('.btn-hapus-row').toggleClass('d-none', rows.length === 1 && i === 0);
    });
}

function hitungTotal(inputEl) {
    const row     = $(inputEl).closest('.row-pengeluaran');
    const nominal = parseInt(row.find('.input-nominal').val()) || 0;
    const qty     = parseInt(row.find('.input-qty').val())     || 1;
    row.find('.input-total').val(nominal * qty);
}

function handleJenisChange(selectEl) {
    const row      = $(selectEl).closest('.row-pengeluaran');
    const secLain  = row.find('.section-lainnya');
    const inputKet = secLain.find('input');
    if ($(selectEl).val() === 'lainnya') {
        secLain.removeClass('d-none');
        inputKet.prop('required', true);
    } else {
        secLain.addClass('d-none');
        inputKet.prop('required', false).val('');
    }
}

// ── Upload foto baris store (satu per klik, akumulasi) ────────────────
function tambahSatuFoto(inputEl, idx) {
    const file = inputEl.files[0];
    if (!file) return;

    // Validasi 2MB
    if (file.size > 2 * 1024 * 1024) {
        alert('Ukuran foto "' + file.name + '" melebihi 2MB. Silakan pilih foto yang lebih kecil.');
        inputEl.value = '';
        return;
    }

    if (!fotoFiles[idx]) fotoFiles[idx] = [];
    fotoFiles[idx].push(file);
    inputEl.value = '';  // reset agar bisa pilih file sama lagi

    renderFotoPreview(idx);
    syncFotoInputs(idx);
}

function renderFotoPreview(idx) {
    const list = document.getElementById('foto-list-' + idx);
    if (!list) return;
    list.innerHTML = '';

    (fotoFiles[idx] || []).forEach(function (file, i) {
        const reader = new FileReader();
        reader.onload = function (e) {
            const wrapper = document.createElement('div');
            wrapper.className = 'foto-item';

            const img = document.createElement('img');
            img.src = e.target.result;
            img.title = file.name;
            img.onclick = function () { praviewFotoBlob(e.target.result); };

            const btnX = document.createElement('button');
            btnX.type = 'button';
            btnX.className = 'btn-hapus-foto';
            btnX.innerHTML = '×';
            btnX.title = 'Hapus foto ini';
            btnX.onclick = function () { hapusFotoStore(idx, i); };

            wrapper.appendChild(img);
            wrapper.appendChild(btnX);
            list.appendChild(wrapper);
        };
        reader.readAsDataURL(file);
    });
}

function hapusFotoStore(idx, i) {
    fotoFiles[idx].splice(i, 1);
    renderFotoPreview(idx);
    syncFotoInputs(idx);
}

/**
 * Sinkronisasi array File ke hidden <input type="file"> yang bisa dikirim form.
 * Trick: gunakan DataTransfer untuk membuat FileList baru dari array File.
 */
function syncFotoInputs(idx) {
    const container = document.getElementById('foto-inputs-' + idx);
    if (!container) return;
    container.innerHTML = '';

    const files = fotoFiles[idx] || [];
    if (files.length === 0) return;

    const input  = document.createElement('input');
    input.type   = 'file';
    input.name   = 'bukti_foto[' + idx + '][]';
    input.accept = 'image/*';
    input.multiple = true;
    input.style.display = 'none';

    const dt = new DataTransfer();
    files.forEach(f => dt.items.add(f));
    input.files = dt.files;

    container.appendChild(input);
}

// Inisialisasi baris pertama
fotoFiles[0] = [];


// ════════════════════════════════════════════════════════════════════
//  FORM EDIT (SINGLE)
// ════════════════════════════════════════════════════════════════════
let editDeletedIds = [];   // id bukti yang akan dihapus
let editNewFiles   = [];   // File[] baru yang akan diupload

function bukaEdit(id) {
    $.getJSON(urlEditData + id + '/edit-data', function (data) {
        // Set action form
        $('#formEdit').attr('action', urlUpdateBase + id + '/update');

        // Isi field
        $('#edit_jenis').val(data.jenis_pengeluaran).trigger('change');
        $('#edit_keterangan_lainnya').val(data.keterangan_lainnya || '');
        $('#edit_nama').val(data.nama_pengeluaran);
        $('#edit_deskripsi').val(data.deskripsi || '');
        $('#edit_nominal').val(data.nominal);
        $('#edit_qty').val(data.qty);
        $('#edit_total').val(data.total);

        // Reset state foto
        editDeletedIds = [];
        editNewFiles   = [];
        $('#editDeletedBuktiIds').val('');
        syncEditNewFotoInputs();

        // Render foto existing
        renderEditFotoList(data.bukti || []);

        // Toggle tampilan form
        $('#formStore').addClass('d-none');
        $('#btnStoreWrapper').addClass('d-none');
        $('#formEdit').removeClass('d-none');
        $('#editModeBanner').addClass('show');
        $('#editModeBannerText').text('Mode Edit — Pengeluaran: ' + data.nama_pengeluaran);
        $('#formTitle').html('<i class="bi bi-pencil-square me-2"></i>Edit Pengeluaran');

        // Scroll ke form
        $('html, body').animate({ scrollTop: $('#cardForm').offset().top - 20 }, 400);
    }).fail(function () {
        alert('Gagal memuat data. Silakan coba lagi.');
    });
}

function batalEdit() {
    $('#formStore').removeClass('d-none');
    $('#btnStoreWrapper').removeClass('d-none');
    $('#formEdit').addClass('d-none');
    $('#editModeBanner').removeClass('show');
    $('#formTitle').html('<i class="bi bi-plus-circle me-2"></i>Input Pengeluaran Baru');
    editDeletedIds = [];
    editNewFiles   = [];
}

function handleEditJenisChange(selectEl) {
    const val = $(selectEl).val();
    if (val === 'lainnya') {
        $('#edit_section_lainnya').show();
        $('#edit_keterangan_lainnya').prop('required', true);
    } else {
        $('#edit_section_lainnya').hide();
        $('#edit_keterangan_lainnya').prop('required', false).val('');
    }
}

function hitungTotalEdit() {
    const nominal = parseInt($('#edit_nominal').val()) || 0;
    const qty     = parseInt($('#edit_qty').val())     || 1;
    $('#edit_total').val(nominal * qty);
}

// Render daftar foto di form edit
function renderEditFotoList(existingList) {
    const container = document.getElementById('edit_foto_list');
    container.innerHTML = '';

    // Foto existing (dari DB)
    existingList.forEach(function (item) {
        const wrapper = document.createElement('div');
        wrapper.className = 'foto-item';
        wrapper.dataset.buktiId = item.id;

        const img = document.createElement('img');
        img.src   = item.url;
        img.title = 'Foto bukti';
        img.onclick = function () { praviewFotoBlob(item.url); };

        const badge = document.createElement('span');
        badge.className = 'badge-existing';
        badge.textContent = 'ada';

        const btnX = document.createElement('button');
        btnX.type = 'button';
        btnX.className = 'btn-hapus-foto';
        btnX.innerHTML = '×';
        btnX.title = 'Hapus foto ini';
        btnX.onclick = function () {
            editDeletedIds.push(item.id);
            $('#editDeletedBuktiIds').val(editDeletedIds.join(','));
            wrapper.remove();
        };

        wrapper.appendChild(img);
        wrapper.appendChild(badge);
        wrapper.appendChild(btnX);
        container.appendChild(wrapper);
    });

    // Render foto baru yang sudah terpilih
    renderEditNewFotos();
}

function renderEditNewFotos() {
    // Hapus foto-baru yang sudah dirender sebelumnya dari foto-list
    document.querySelectorAll('#edit_foto_list .foto-item-new').forEach(el => el.remove());

    editNewFiles.forEach(function (file, i) {
        const reader = new FileReader();
        reader.onload = function (e) {
            const wrapper = document.createElement('div');
            wrapper.className = 'foto-item foto-item-new';

            const img = document.createElement('img');
            img.src   = e.target.result;
            img.title = file.name;
            img.onclick = function () { praviewFotoBlob(e.target.result); };

            const btnX = document.createElement('button');
            btnX.type = 'button';
            btnX.className = 'btn-hapus-foto';
            btnX.innerHTML = '×';
            btnX.title = 'Batalkan foto ini';
            btnX.onclick = function () {
                editNewFiles.splice(i, 1);
                syncEditNewFotoInputs();
                renderEditNewFotos();
            };

            wrapper.appendChild(img);
            wrapper.appendChild(btnX);
            document.getElementById('edit_foto_list').appendChild(wrapper);
        };
        reader.readAsDataURL(file);
    });
}

function tambahFotoEdit(inputEl) {
    const file = inputEl.files[0];
    if (!file) return;

    if (file.size > 2 * 1024 * 1024) {
        alert('Ukuran foto "' + file.name + '" melebihi 2MB. Silakan pilih foto yang lebih kecil.');
        inputEl.value = '';
        return;
    }

    editNewFiles.push(file);
    inputEl.value = '';
    syncEditNewFotoInputs();
    renderEditNewFotos();
}

function syncEditNewFotoInputs() {
    const container = document.getElementById('edit_new_foto_inputs');
    container.innerHTML = '';
    if (editNewFiles.length === 0) return;

    const input    = document.createElement('input');
    input.type     = 'file';
    input.name     = 'bukti_foto_baru[]';
    input.accept   = 'image/*';
    input.multiple = true;
    input.style.display = 'none';

    const dt = new DataTransfer();
    editNewFiles.forEach(f => dt.items.add(f));
    input.files = dt.files;
    container.appendChild(input);
}


// ════════════════════════════════════════════════════════════════════
//  MODAL GALERI BUKTI
// ════════════════════════════════════════════════════════════════════
let modalGaleri = null;

function lihatGaleriBukti(urlList) {
    const grid = document.getElementById('galeriBuktiGrid');
    document.getElementById('galeriBuktiCount').textContent = urlList.length + ' foto';

    if (urlList.length === 0) {
        grid.innerHTML = '<div class="galeri-empty"><i class="bi bi-image me-2"></i>Tidak ada bukti foto.</div>';
    } else {
        grid.innerHTML = '';
        urlList.forEach(function (url) {
            const img = document.createElement('img');
            img.src   = url;
            img.alt   = 'Bukti';
            img.onclick = function () { bukaFotoFull(url); };
            grid.appendChild(img);
        });
    }

    if (!modalGaleri) {
        modalGaleri = new bootstrap.Modal(document.getElementById('modalGaleriBukti'));
    }
    modalGaleri.show();
}

function bukaFotoFull(url) {
    document.getElementById('fotoFullImg').src = url;
    const modalFull = new bootstrap.Modal(document.getElementById('modalFotoFull'), { backdrop: false });
    modalFull.show();
}

function praviewFotoBlob(src) {
    document.getElementById('fotoFullImg').src = src;
    const modalFull = new bootstrap.Modal(document.getElementById('modalFotoFull'), { backdrop: false });
    modalFull.show();
}


// ════════════════════════════════════════════════════════════════════
//  FILTER & TABEL
// ════════════════════════════════════════════════════════════════════
function setRange(val) {
    $('#inputRange').val(val);
    $('#inputDateFrom').val('');
    $('#inputDateTo').val('');
    $('#formFilter').submit();
}

function lihatSemua(tipe) {
    document.querySelectorAll('.row-extra-' + tipe).forEach(function (tr) {
        tr.classList.remove('row-extra');
        tr.style.display = '';
    });
    const wrapper = document.getElementById('wrapper-lihat-' + tipe);
    if (wrapper) wrapper.style.display = 'none';
}
</script>
</body>
</html>
