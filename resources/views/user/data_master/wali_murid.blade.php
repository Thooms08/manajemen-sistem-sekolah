<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Wali Murid</title>
        @include('favicon')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f7f6; overflow-x: hidden; }
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        #content { width: 100%; padding: 25px; transition: all 0.3s; min-height: 100vh; }
        #overlay { display: none; position: fixed; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 1040; top: 0; left: 0; }
        #overlay.active { display: block; }
        #sidebarCollapse { width: 45px; height: 45px; background: #198754; border: none; color: white; border-radius: 10px; box-shadow: 0 4px 10px rgba(25,135,84,0.2); }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .search-box { border-radius: 10px; border: 1px solid #e0e0e0; padding: 10px 15px; transition: 0.3s; }
        .search-box:focus { border-color: #198754; box-shadow: 0 0 0 0.25rem rgba(25,135,84,0.1); outline: none; }
        .table thead { background-color: #f8f9fa; border-bottom: 2px solid #198754; }
        .table th { font-weight: 600; color: #444; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; }
        .nav-tabs { flex-wrap: wrap; gap: 4px; }
        .nav-tabs .nav-link { color: #6c757d; font-weight: 500; border-radius: 8px 8px 0 0; white-space: nowrap; }
        .nav-tabs .nav-link.active { color: #198754; border-bottom-color: #fff; font-weight: 600; }
        .nav-tabs .nav-link:hover { color: #198754; }
        .row-hidden { display: none; }
        .wali-card-mobile { display: none; }
        .wali-card-item {
            background: #fff; border-radius: 12px; padding: 14px 16px;
            margin-bottom: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border-left: 4px solid #198754;
        }
        .wali-card-item.nonaktif { border-left-color: #dc3545; }
        .wali-card-item .wc-name { font-weight: 700; font-size: 0.97rem; color: #1a3a3a; }
        .wali-card-item .wc-meta { font-size: 0.8rem; color: #6c757d; margin-top: 3px; }
        .wali-card-item .wc-actions { display: flex; gap: 6px; margin-top: 10px; flex-wrap: wrap; }
        .wali-card-item .wc-actions .btn { flex: 1; font-size: 0.8rem; min-width: 80px; }
        @media (max-width: 991px) { #content { padding: 16px 18px; } }
        @media (max-width: 767px) {
            #content { padding: 12px 12px; }
            .page-header { flex-direction: column; align-items: flex-start !important; gap: 10px; }
            .search-row { flex-direction: column !important; }
            .search-row .col-md-6 { width: 100%; }
            .table-wali-desktop { display: none !important; }
            .wali-card-mobile { display: block; }
            .nav-tabs .nav-link { font-size: 0.82rem; padding: 6px 10px; }
            .card { border-radius: 12px; }
        }
        @media (max-width: 575px) {
            .card { padding: 1rem !important; }
            .wali-card-item { padding: 12px 13px; }
            .wali-card-item .wc-actions .btn { flex: 1 1 100%; }
        }
    </style>
</head>
<body>
@php
    $__canCreate = can('data_wali', 'create');
    $__canEdit   = can('data_wali', 'edit');
    $__canDelete = can('data_wali', 'delete');
@endphp

    <div id="overlay"></div>
    <div class="wrapper">
        @include('user.sidebar')

        <div id="content">
            <div class="container-fluid">

                {{-- Header --}}
                <div class="d-flex align-items-center justify-content-between mb-4 mt-2">
                    <div class="d-flex align-items-center">
                        <button type="button" id="sidebarCollapse" class="btn">
                            <i class="bi bi-list fs-4"></i>
                        </button>
                        <h4 class="ms-3 mb-0 fw-bold text-success">Data Wali Murid</h4>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('success') }}</div>
                @endif

                {{-- Search Bar --}}
                <div class="card p-3 p-md-4 mb-4">
                    <div class="row align-items-center search-row g-2">
                        <div class="col-md-6">
                            <p class="text-muted small mb-0">
                                Cari berdasarkan Nama Murid, Nama Wali, Hubungan, Pekerjaan, atau No. HP
                            </p>
                        </div>
                        <div class="col-md-6 mt-2 mt-md-0">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" id="search-input"
                                       class="form-control search-box border-start-0"
                                       placeholder="Ketik kata kunci...">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tab + Tabel --}}
                <div class="card p-3 p-md-4">

                    {{-- Tab Nav --}}
                    <ul class="nav nav-tabs mb-3" id="waliTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="tab-aktif-btn" data-bs-toggle="tab"
                                    data-bs-target="#tab-aktif" type="button" role="tab"
                                    data-tab="aktif">
                                <i class="bi bi-person-check me-1"></i>
                                Aktif
                                <span class="badge bg-success ms-1">{{ $dataAktif->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-nonaktif-btn" data-bs-toggle="tab"
                                    data-bs-target="#tab-nonaktif" type="button" role="tab"
                                    data-tab="nonaktif">
                                <i class="bi bi-person-dash me-1"></i>
                                Nonaktif
                                <span class="badge bg-danger ms-1">{{ $dataNonaktif->count() }}</span>
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">

                        {{-- Tab Aktif --}}
                        <div class="tab-pane fade show active" id="tab-aktif" role="tabpanel">
                            <div class="table-responsive table-wali-desktop">
                                <table class="table align-middle">
                                    <thead>
                                        <tr>
                                            <th>Nama Murid</th>
                                            <th>Nama Wali</th>
                                            <th>Hubungan</th>
                                            <th>Pekerjaan Wali</th>
                                            <th>No. HP Murid</th>
                                        </tr>
                                    </thead>
                                    <tbody id="table-body-aktif">
                                        @forelse($dataAktif as $index => $row)
                                        <tr class="{{ $index >= 10 ? 'row-extra-wali-aktif row-hidden' : '' }}">
                                            <td class="fw-bold text-dark">{{ $row->nama_lengkap }}</td>
                                            <td>{{ $row->wali->nama_wali ?? '-' }}</td>
                                            <td>
                                                @if($row->wali->hubungan_wali ?? null)
                                                    <span class="badge bg-warning bg-opacity-15 text-warning-emphasis px-2">
                                                        {{ $row->wali->hubungan_wali }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ $row->wali->pekerjaan_wali ?? '-' }}</td>
                                            <td>
                                                <span class="badge bg-success bg-opacity-10 text-success px-3">
                                                    {{ $row->no_hp ?? '-' }}
                                                </span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                <i class="bi bi-person-badge fs-3 d-block mb-2 text-secondary"></i>
                                                Belum ada data wali murid aktif
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="wali-card-mobile" id="mobile-wali-aktif">
                                @forelse($dataAktif as $index => $row)
                                <div class="wali-card-item {{ $index >= 10 ? 'row-extra-wali-aktif row-hidden' : '' }}">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="wc-name">{{ $row->nama_lengkap }}</div>
                                        <span class="badge bg-success">Aktif</span>
                                    </div>
                                    <div class="wc-meta"><i class="bi bi-person me-1"></i>Wali: {{ $row->wali->nama_wali ?? '-' }}</div>
                                    <div class="wc-meta"><i class="bi bi-diagram-3 me-1"></i>Hubungan: {{ $row->wali->hubungan_wali ?? '-' }}</div>
                                    <div class="wc-meta"><i class="bi bi-briefcase me-1"></i>Pekerjaan: {{ $row->wali->pekerjaan_wali ?? '-' }}</div>
                                    <div class="wc-meta"><i class="bi bi-telephone me-1"></i>No. HP: {{ $row->no_hp ?? '-' }}</div>
                                </div>
                                @empty
                                <div class="text-center py-5 text-muted">
                                    <i class="bi bi-person-badge fs-3 d-block mb-2 text-secondary"></i>
                                    Belum ada data wali murid aktif
                                </div>
                                @endforelse
                            </div>

                            {{-- Tombol Lihat Semua (Aktif) --}}
                            @if($dataAktif->count() > 10)
                            <div class="text-center mt-2" id="btn-lihat-semua-wali-aktif">
                                <button class="btn btn-outline-success btn-sm px-4"
                                        onclick="lihatSemua('wali-aktif', this)">
                                    <i class="bi bi-chevron-down me-1"></i>Lihat Semua Data
                                    ({{ $dataAktif->count() - 10 }} data lainnya)
                                </button>
                            </div>
                            @endif
                        </div>

                        {{-- Tab Nonaktif --}}
                        <div class="tab-pane fade" id="tab-nonaktif" role="tabpanel">
                            <div class="table-responsive table-wali-desktop">
                                <table class="table align-middle">
                                    <thead>
                                        <tr>
                                            <th>Nama Murid</th>
                                            <th>Nama Wali</th>
                                            <th>Hubungan</th>
                                            <th>Pekerjaan Wali</th>
                                            <th>No. HP Murid</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="table-body-nonaktif">
                                        @forelse($dataNonaktif as $index => $row)
                                        <tr class="{{ $index >= 10 ? 'row-extra-wali-nonaktif row-hidden' : '' }}">
                                            <td class="fw-bold text-dark">{{ $row->nama_lengkap }}</td>
                                            <td>{{ $row->wali->nama_wali ?? '-' }}</td>
                                            <td>
                                                @if($row->wali->hubungan_wali ?? null)
                                                    <span class="badge bg-warning bg-opacity-15 text-warning-emphasis px-2">
                                                        {{ $row->wali->hubungan_wali }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ $row->wali->pekerjaan_wali ?? '-' }}</td>
                                            <td>
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary px-3">
                                                    {{ $row->no_hp ?? '-' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-danger bg-opacity-10 text-danger px-2">
                                                    <i class="bi bi-slash-circle me-1"></i>Nonaktif
                                                </span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">
                                                <i class="bi bi-person-x fs-3 d-block mb-2 text-secondary"></i>
                                                Tidak ada data wali murid nonaktif
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="wali-card-mobile" id="mobile-wali-nonaktif">
                                @forelse($dataNonaktif as $index => $row)
                                <div class="wali-card-item nonaktif {{ $index >= 10 ? 'row-extra-wali-nonaktif row-hidden' : '' }}">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="wc-name">{{ $row->nama_lengkap }}</div>
                                        <span class="badge bg-danger">Nonaktif</span>
                                    </div>
                                    <div class="wc-meta"><i class="bi bi-person me-1"></i>Wali: {{ $row->wali->nama_wali ?? '-' }}</div>
                                    <div class="wc-meta"><i class="bi bi-diagram-3 me-1"></i>Hubungan: {{ $row->wali->hubungan_wali ?? '-' }}</div>
                                    <div class="wc-meta"><i class="bi bi-briefcase me-1"></i>Pekerjaan: {{ $row->wali->pekerjaan_wali ?? '-' }}</div>
                                    <div class="wc-meta"><i class="bi bi-telephone me-1"></i>No. HP: {{ $row->no_hp ?? '-' }}</div>
                                </div>
                                @empty
                                <div class="text-center py-5 text-muted">
                                    <i class="bi bi-person-x fs-3 d-block mb-2 text-secondary"></i>
                                    Tidak ada data wali murid nonaktif
                                </div>
                                @endforelse
                            </div>

                            {{-- Tombol Lihat Semua (Nonaktif) --}}
                            @if($dataNonaktif->count() > 10)
                            <div class="text-center mt-2" id="btn-lihat-semua-wali-nonaktif">
                                <button class="btn btn-outline-danger btn-sm px-4"
                                        onclick="lihatSemua('wali-nonaktif', this)">
                                    <i class="bi bi-chevron-down me-1"></i>Lihat Semua Data
                                    ({{ $dataNonaktif->count() - 10 }} data lainnya)
                                </button>
                            </div>
                            @endif
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            // Sidebar Toggle
            const sidebar = $('#sidebar');
            const overlay = $('#overlay');

            function toggleSidebar() {
                if ($(window).width() <= 768) {
                    sidebar.toggleClass('show-mobile');
                    overlay.toggleClass('active');
                } else {
                    sidebar.toggleClass('inactive');
                }
            }

            $('#sidebarCollapse, #close-sidebar, #overlay').on('click', toggleSidebar);

            // Lacak tab aktif saat ini
            let currentTab = 'aktif';
            $('[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
                currentTab = $(this).data('tab');
                // Jalankan ulang pencarian dengan tab baru
                doSearch($('#search-input').val());
            });

            // AJAX Search
            function doSearch(keyword) {
                const targetBody = currentTab === 'nonaktif' ? '#table-body-nonaktif' : '#table-body-aktif';
                $.ajax({
                    type: 'GET',
                    url: "{{ route('wali-murid.search') }}",
                    data: { search: keyword, tab: currentTab },
                    success: function (data) {
                        $(targetBody).html(data);
                        // Sembunyikan tombol lihat semua saat sedang mencari
                        if (keyword.length > 0) {
                            $('#btn-lihat-semua-wali-aktif, #btn-lihat-semua-wali-nonaktif').hide();
                        } else {
                            $('#btn-lihat-semua-wali-aktif, #btn-lihat-semua-wali-nonaktif').show();
                        }
                    }
                });
            }

            $('#search-input').on('keyup', function () {
                doSearch($(this).val());
            });
        });

        function lihatSemua(key, btn) {
            $('.row-extra-' + key).removeClass('row-hidden');
            $(btn).closest('div').hide();
        }
    </script>
</body>
</html>
