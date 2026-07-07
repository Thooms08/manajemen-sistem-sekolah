<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Ortu Murid</title>
        @include('favicon')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f7f6; overflow-x: hidden; }
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        #content { width: 100%; padding: 25px; transition: all 0.3s; min-height: 100vh; min-width: 0; }
        #overlay { display: none; position: fixed; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 1040; top: 0; left: 0; }
        #overlay.active { display: block; }
        #sidebarCollapse { width: 45px; height: 45px; background: #198754; border: none; color: white; border-radius: 10px; box-shadow: 0 4px 10px rgba(25,135,84,0.2); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .search-box { border-radius: 10px; border: 1px solid #e0e0e0; padding: 10px 15px; transition: 0.3s; }
        .search-box:focus { border-color: #198754; box-shadow: 0 0 0 0.25rem rgba(25,135,84,0.1); outline: none; }
        .table thead { background-color: #f8f9fa; border-bottom: 2px solid #198754; }
        .table th { font-weight: 600; color: #444; text-transform: uppercase; font-size: 0.82rem; letter-spacing: 0.5px; white-space: nowrap; }
        .nav-tabs { flex-wrap: wrap; gap: 4px; }
        .nav-tabs .nav-link { color: #6c757d; font-weight: 500; border-radius: 8px 8px 0 0; font-size: 0.9rem; white-space: nowrap; }
        .nav-tabs .nav-link.active { color: #198754; border-bottom-color: #fff; font-weight: 600; }
        .nav-tabs .nav-link:hover { color: #198754; }
        .row-hidden { display: none; }

        /* Mobile card */
        .ortu-card-mobile { display: none; }
        .ortu-card-item {
            background: #fff; border-radius: 12px; padding: 14px 16px;
            margin-bottom: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border-left: 4px solid #198754;
        }
        .ortu-card-item.nonaktif { border-left-color: #dc3545; }
        .ortu-card-item .oc-name  { font-weight: 700; font-size: 0.97rem; color: #1a3a3a; }
        .ortu-card-item .oc-meta  { font-size: 0.8rem; color: #6c757d; margin-top: 3px; }

        /* ── Responsive ── */
        @media (max-width: 991px) {
            #content { padding: 16px 18px; }
        }
        @media (max-width: 767px) {
            #content { padding: 12px 12px; }
            .page-header { flex-direction: column; align-items: flex-start !important; gap: 10px; }
            .search-row { flex-direction: column !important; }
            .search-row .col-md-6 { width: 100%; }
            .table-ortu-desktop { display: none !important; }
            .ortu-card-mobile { display: block; }
            .nav-tabs .nav-link { font-size: 0.82rem; padding: 6px 10px; }
            .card { border-radius: 12px; }
        }
        @media (max-width: 575px) {
            .card { padding: 1rem !important; }
            .ortu-card-item { padding: 12px 13px; }
        }
    </style>
</head>
<body>
@php
    $__canCreate = can('data_ortu', 'create');
    $__canEdit   = can('data_ortu', 'edit');
    $__canDelete = can('data_ortu', 'delete');
@endphp

    <div id="overlay"></div>
    <div class="wrapper">
        @include('user.sidebar')

        <div id="content">
            <div class="container-fluid">

                {{-- Header --}}
                <div class="d-flex align-items-center justify-content-between mb-4 mt-2 flex-wrap gap-2 page-header">
                    <div class="d-flex align-items-center">
                        <button type="button" id="sidebarCollapse" class="btn">
                            <i class="bi bi-list fs-4"></i>
                        </button>
                        <h4 class="ms-3 mb-0 fw-bold text-success">Data Ortu Murid</h4>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('success') }}</div>
                @endif

                {{-- Search Bar --}}
                <div class="card p-3 p-md-4 mb-4">
                    <div class="row align-items-center search-row g-2">
                        <div class="col-12 col-md-6">
                            <p class="text-muted small mb-0">
                                Cari berdasarkan Nama Murid, Nama Ayah, Nama Ibu, atau No. HP
                            </p>
                        </div>
                        <div class="col-12 col-md-6">
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
                    <ul class="nav nav-tabs mb-3" id="ortuTab" role="tablist">
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
                            {{-- DESKTOP TABLE --}}
                            <div class="table-responsive table-ortu-desktop">
                                <table class="table align-middle">
                                    <thead>
                                        <tr>
                                            <th>Nama Murid</th>
                                            <th>Nama Ayah</th>
                                            <th>Nama Ibu</th>
                                            <th>No. HP</th>
                                        </tr>
                                    </thead>
                                    <tbody id="table-body-aktif">
                                        @forelse($dataAktif as $index => $row)
                                        <tr class="{{ $index >= 10 ? 'row-extra-ortu-aktif row-hidden' : '' }}">
                                            <td class="fw-bold text-dark">{{ $row->nama_lengkap }}</td>
                                            <td>{{ $row->ortu->nama_ayah ?? '-' }}</td>
                                            <td>{{ $row->ortu->nama_ibu ?? '-' }}</td>
                                            <td>
                                                <span class="badge bg-success bg-opacity-10 text-success px-3">
                                                    {{ $row->no_hp ?? '-' }}
                                                </span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-muted">
                                                <i class="bi bi-people fs-3 d-block mb-2 text-secondary"></i>
                                                Belum ada data ortu murid aktif
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            {{-- MOBILE CARD LIST --}}
                            <div class="ortu-card-mobile" id="mobile-ortu-aktif">
                                @forelse($dataAktif as $index => $row)
                                <div class="ortu-card-item {{ $index >= 10 ? 'row-extra-ortu-aktif row-hidden' : '' }}">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="oc-name">{{ $row->nama_lengkap }}</div>
                                        <span class="badge bg-success">Aktif</span>
                                    </div>
                                    <div class="oc-meta"><i class="bi bi-person me-1"></i>Ayah: {{ $row->ortu->nama_ayah ?? '-' }}</div>
                                    <div class="oc-meta"><i class="bi bi-person me-1"></i>Ibu: {{ $row->ortu->nama_ibu ?? '-' }}</div>
                                    <div class="oc-meta"><i class="bi bi-telephone me-1"></i>{{ $row->no_hp ?? '-' }}</div>
                                </div>
                                @empty
                                <div class="text-center py-5 text-muted">
                                    <i class="bi bi-people fs-3 d-block mb-2 text-secondary"></i>
                                    Belum ada data ortu murid aktif
                                </div>
                                @endforelse
                            </div>

                            {{-- Tombol Lihat Semua (Aktif) --}}
                            @if($dataAktif->count() > 10)
                            <div class="text-center mt-2" id="btn-lihat-semua-ortu-aktif">
                                <button class="btn btn-outline-success btn-sm px-4"
                                        onclick="lihatSemua('ortu-aktif', this)">
                                    <i class="bi bi-chevron-down me-1"></i>Lihat Semua Data
                                    ({{ $dataAktif->count() - 10 }} data lainnya)
                                </button>
                            </div>
                            @endif
                        </div>

                        {{-- Tab Nonaktif --}}
                        <div class="tab-pane fade" id="tab-nonaktif" role="tabpanel">
                            {{-- DESKTOP TABLE --}}
                            <div class="table-responsive table-ortu-desktop">
                                <table class="table align-middle">
                                    <thead>
                                        <tr>
                                            <th>Nama Murid</th>
                                            <th>Nama Ayah</th>
                                            <th>Nama Ibu</th>
                                            <th>No. HP</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="table-body-nonaktif">
                                        @forelse($dataNonaktif as $index => $row)
                                        <tr class="{{ $index >= 10 ? 'row-extra-ortu-nonaktif row-hidden' : '' }}">
                                            <td class="fw-bold text-dark">{{ $row->nama_lengkap }}</td>
                                            <td>{{ $row->ortu->nama_ayah ?? '-' }}</td>
                                            <td>{{ $row->ortu->nama_ibu ?? '-' }}</td>
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
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                <i class="bi bi-people fs-3 d-block mb-2 text-secondary"></i>
                                                Tidak ada data ortu murid nonaktif
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            {{-- MOBILE CARD LIST --}}
                            <div class="ortu-card-mobile" id="mobile-ortu-nonaktif">
                                @forelse($dataNonaktif as $index => $row)
                                <div class="ortu-card-item nonaktif {{ $index >= 10 ? 'row-extra-ortu-nonaktif row-hidden' : '' }}">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="oc-name">{{ $row->nama_lengkap }}</div>
                                        <span class="badge bg-danger">Nonaktif</span>
                                    </div>
                                    <div class="oc-meta"><i class="bi bi-person me-1"></i>Ayah: {{ $row->ortu->nama_ayah ?? '-' }}</div>
                                    <div class="oc-meta"><i class="bi bi-person me-1"></i>Ibu: {{ $row->ortu->nama_ibu ?? '-' }}</div>
                                    <div class="oc-meta"><i class="bi bi-telephone me-1"></i>{{ $row->no_hp ?? '-' }}</div>
                                </div>
                                @empty
                                <div class="text-center py-5 text-muted">
                                    <i class="bi bi-people fs-3 d-block mb-2 text-secondary"></i>
                                    Tidak ada data ortu murid nonaktif
                                </div>
                                @endforelse
                            </div>

                            {{-- Tombol Lihat Semua (Nonaktif) --}}
                            @if($dataNonaktif->count() > 10)
                            <div class="text-center mt-2" id="btn-lihat-semua-ortu-nonaktif">
                                <button class="btn btn-outline-danger btn-sm px-4"
                                        onclick="lihatSemua('ortu-nonaktif', this)">
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
                // Jalankan ulang pencarian saat pindah tab
                doSearch($('#search-input').val());
            });

            // AJAX Search
            function doSearch(keyword) {
                const targetBody = currentTab === 'nonaktif' ? '#table-body-nonaktif' : '#table-body-aktif';
                $.ajax({
                    type: 'GET',
                    url: "{{ route('ortu-murid.search') }}",
                    data: { search: keyword, tab: currentTab },
                    success: function (data) {
                        $(targetBody).html(data);
                        // Sembunyikan tombol lihat semua saat sedang mencari
                        if (keyword.length > 0) {
                            $('#btn-lihat-semua-ortu-aktif, #btn-lihat-semua-ortu-nonaktif').hide();
                        } else {
                            $('#btn-lihat-semua-ortu-aktif, #btn-lihat-semua-ortu-nonaktif').show();
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
