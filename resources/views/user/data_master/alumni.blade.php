<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Data Alumni Murid</title>
    @if(isset($sekolah->logo))
    <link rel="icon" type="image/png" href="{{ \App\Helpers\ImageHelper::url($sekolah->logo) }}">
    @else
    <link rel="icon" type="image/png" href="{{ asset('assets/img/default-favicon.png') }}">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f4f7f6; overflow-x: hidden; }
        .wrapper { display: flex; width: 100%; }
        #content { width: 100%; padding: 20px 30px; transition: all 0.3s; min-height: 100vh; min-width: 0; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .table thead { background-color: #198754; color: white; }
        .table thead th { white-space: nowrap; }
        #sidebarCollapse { width: 40px; height: 40px; background: #198754; border: none; color: white; border-radius: 8px; flex-shrink: 0; }
        #overlay { display: none; position: fixed; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 1040; top: 0; left: 0; }
        #overlay.active { display: block; }
        select:focus, input:focus { border-color: #198754 !important; box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25) !important; }

        .alumni-card-mobile { display: none; }
        .alumni-card-item { background: #fff; border-radius: 12px; padding: 12px 14px; margin-bottom: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border-left: 4px solid #198754; }
        .alumni-card-item .aci-name { font-weight: 700; color: #1f2937; }
        .alumni-card-item .aci-meta { font-size: 0.8rem; color: #6c757d; margin-top: 3px; }
        .alumni-card-item .aci-actions { display: flex; gap: 8px; margin-top: 10px; }
        .alumni-card-item .aci-actions .btn { flex: 1; font-size: 0.8rem; }

        @media (max-width: 991px) {
            #content { padding: 16px 18px; }
        }
        @media (max-width: 767px) {
            #content { padding: 12px 12px; }
            .page-header { flex-direction: column; align-items: flex-start !important; gap: 10px; }
            .filter-bar-wrapper { flex-direction: column; align-items: stretch !important; }
            .filter-group { width: 100%; }
            .filter-group .form-select, .filter-group .input-group { width: 100%; }
            .table-alumni-desktop { display: none !important; }
            .alumni-card-mobile { display: block; }
        }
    </style>
</head>
<body>
@php
    $__canCreate = can('data_alumni', 'create');
    $__canEdit   = can('data_alumni', 'edit');
    $__canDelete = can('data_alumni', 'delete');
@endphp

    <div id="overlay"></div>
    <div class="wrapper">
        @include('user.sidebar')
        <div id="content">
            <div class="container-fluid">
                <div class="d-flex align-items-center justify-content-between mb-4 mt-2 flex-wrap gap-2 page-header">
                    <div class="d-flex align-items-center">
                        <button type="button" id="sidebarCollapse" class="btn"><i class="bi bi-list fs-5"></i></button>
                        <h4 class="ms-3 mb-0 fw-bold text-success">Data Alumni Murid</h4>
                    </div>
                </div>

                <div class="card p-3 mb-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 filter-bar-wrapper">
                        <span class="text-muted small">Direktori khusus siswa yang telah dinyatakan lulus beserta berkas digital (Read-Only).</span>
                        <div class="d-flex gap-2 flex-wrap filter-group">
                            <select id="filter-tahun" class="form-select" style="width: 170px;">
                                <option value="">-- Semua Tahun --</option>
                                @foreach($years as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                            <div class="input-group" style="min-width: 240px; width: 320px;">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                                <input type="text" id="search-alumni" class="form-control border-start-0 ps-0" placeholder="Cari Nama, NISN, atau NIS Baru...">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card p-4">
                    <div class="table-responsive table-alumni-desktop">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>NISN</th>
                                    <th>NIS Baru</th>
                                    <th>Nama Murid</th>
                                    <th class="text-center">Tahun Lulus</th>
                                    <th class="text-center">Surat Kelulusan</th>
                                    <th class="text-center">Ijazah</th>
                                    <th class="text-center">Raport</th>
                                </tr>
                            </thead>
                            <tbody id="table-body">
                                @if($alumnis->isEmpty())
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-3">Data alumni tidak ditemukan</td>
                                    </tr>
                                @else
                                    @foreach($alumnis as $alumni)
                                        @php
                                            $kelulusanUuid = $alumni->kelulusan->uuid ?? '';
                                        @endphp
                                        <tr>
                                            <td>{{ $alumni->nisn }}</td>
                                            <td>{{ $alumni->nis_baru ?? '-' }}</td>
                                            <td>{{ $alumni->nama_lengkap }}</td>
                                            <td class="text-center">{{ $alumni->kelulusan->tahun_lulus ?? '-' }}</td>
                                            <td class="text-center">
                                                @if(!empty($alumni->kelulusan->surat_kelulusan) && $kelulusanUuid)
                                                    <a href="{{ route('kelulusan.view.surat', $kelulusanUuid) }}" target="_blank" class="btn btn-sm btn-outline-success">
                                                        <i class="bi bi-file-earmark-check-fill"></i>
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if(!empty($alumni->kelulusan->ijazah) && $kelulusanUuid)
                                                    <a href="{{ route('kelulusan.view.ijazah', $kelulusanUuid) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-file-earmark-pdf"></i>
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if(!empty($alumni->kelulusan->raport) && $kelulusanUuid)
                                                    <a href="{{ route('kelulusan.view.raport', $kelulusanUuid) }}" target="_blank" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-file-earmark-text"></i>
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="alumni-card-mobile">
                    @foreach($alumnis as $alumni)
                    @php
                        $kelulusanUuid = $alumni->kelulusan->uuid ?? '';
                    @endphp
                    <div class="alumni-card-item">
                        <div class="aci-name">{{ $alumni->nama_lengkap }}</div>
                        <div class="aci-meta"><i class="bi bi-person-badge me-1"></i>NISN: {{ $alumni->nisn }}</div>
                        <div class="aci-meta"><i class="bi bi-calendar2-week me-1"></i>Tahun Lulus: {{ $alumni->kelulusan->tahun_lulus ?? '-' }}</div>
                        <div class="aci-actions">
                            @if(!empty($alumni->kelulusan->surat_kelulusan) && $kelulusanUuid)
                                <a href="{{ route('kelulusan.view.surat', $kelulusanUuid) }}" target="_blank" class="btn btn-outline-success">
                                    <i class="bi bi-file-earmark-check-fill me-1"></i>Surat
                                </a>
                            @endif
                            @if(!empty($alumni->kelulusan->ijazah) && $kelulusanUuid)
                                <a href="{{ route('kelulusan.view.ijazah', $kelulusanUuid) }}" target="_blank" class="btn btn-outline-primary">
                                    <i class="bi bi-file-earmark-pdf me-1"></i>Ijazah
                                </a>
                            @endif
                            @if(!empty($alumni->kelulusan->raport) && $kelulusanUuid)
                                <a href="{{ route('kelulusan.view.raport', $kelulusanUuid) }}" target="_blank" class="btn btn-outline-danger">
                                    <i class="bi bi-file-earmark-text me-1"></i>Raport
                                </a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ── Sidebar Responsive Logic ──────────────────────────────────────────
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
        collapseBtn.onclick = toggleSidebar;
        if (closeBtn) closeBtn.onclick = toggleSidebar;
        overlay.onclick = toggleSidebar;

        // ── AJAX Real-time Search & Dropdown Filter ───────────────────────────
        $(document).ready(function () {
            function hitungFilterAlumni() {
                let pencarianTeks = $('#search-alumni').val();
                let filterTahun    = $('#filter-tahun').val();

                $.ajax({
                    type: 'GET',
                    url: "{{ route('alumni.search') }}",
                    data: { 
                        'search': pencarianTeks,
                        'tahun': filterTahun
                    },
                    success: function (data) { 
                        $('#table-body').html(data); 
                    },
                    error: function (err) { 
                        console.log('Error AJAX Search Alumni:', err); 
                    }
                });
            }

            // Gabungkan listener keyup teks input & perubahan dropdown tahun lulus
            $('#search-alumni').on('keyup', hitungFilterAlumni);
            $('#filter-tahun').on('change', hitungFilterAlumni);
        });
    </script>
</body>
</html>