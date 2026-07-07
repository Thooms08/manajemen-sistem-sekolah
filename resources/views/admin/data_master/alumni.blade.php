<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Data Alumni Murid</title>
        @include('favicon')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f4f7f6; }
        .wrapper { display: flex; width: 100%; }
        #content { width: 100%; padding: 15px 15px; transition: all 0.3s; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .table thead { background-color: #198754; color: white; }
        #sidebarCollapse { width: 40px; height: 40px; background: #198754; border: none; color: white; border-radius: 8px; }
        #overlay { display: none; position: fixed; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 1040; top: 0; left: 0; }
        #overlay.active { display: block; }
        select:focus, input:focus { border-color: #198754 !important; outline: none !important; box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25) !important; }

        @media (min-width: 768px) {
            #content { padding: 20px 30px; }
        }

        /* Proportional spacing and responsive tables */
        .table th, .table td { padding: 12px 10px; }
        .text-nowrap-custom { white-space: nowrap; }
    </style>
</head>
<body>
    <div id="overlay"></div>
    <div class="wrapper">
        @include('admin.sidebar')
        <div id="content">
            <div class="container-fluid">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 mt-2 gap-3">
                    <div class="d-flex align-items-center">
                        <button type="button" id="sidebarCollapse" class="btn"><i class="bi bi-list fs-5"></i></button>
                        <h4 class="ms-3 mb-0 fw-bold text-success fs-5 fs-md-4">Data Alumni Murid</h4>
                    </div>
                </div>

                <div class="card p-3 mb-4">
                    <div class="d-flex flex-column flex-lg-row gap-3 justify-content-between align-items-lg-center align-items-stretch">
                        <span class="text-muted small col-lg-6 col-xl-7 p-0">Direktori khusus siswa yang telah dinyatakan lulus beserta berkas digital (Read-Only).</span>
                        <div class="d-flex flex-column flex-sm-row gap-2 col-lg-6 col-xl-5 p-0 justify-content-lg-end" style="width: 100%; max-width: 500px;">
                            <select id="filter-tahun" class="form-select flex-sm-grow-0" style="min-width: 150px;">
                                <option value="">-- Semua Tahun --</option>
                                @foreach($years as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                            <div class="input-group flex-sm-grow-1" style="max-width: 350px;">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                                <input type="text" id="search-alumni" class="form-control border-start-0 ps-0" placeholder="Cari Nama, NISN, atau NIS Baru...">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card p-4">
                    <div class="table-responsive border rounded-3">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="text-nowrap-custom">NISN</th>
                                    <th class="text-nowrap-custom">NIS Baru</th>
                                    <th>Nama Murid</th>
                                    <th class="text-center text-nowrap-custom">Tahun Lulus</th>
                                    <th class="text-center text-nowrap-custom">Surat Kelulusan</th>
                                    <th class="text-center text-nowrap-custom">Ijazah</th>
                                    <th class="text-center text-nowrap-custom">Raport</th>
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
                                            <td class="text-nowrap-custom">{{ $alumni->nisn }}</td>
                                            <td class="text-nowrap-custom">{{ $alumni->nis_baru ?? '-' }}</td>
                                            <td class="fw-medium text-dark">{{ $alumni->nama_lengkap }}</td>
                                            <td class="text-center text-nowrap-custom">{{ $alumni->kelulusan->tahun_lulus ?? '-' }}</td>
                                            <td class="text-center text-nowrap-custom">
                                                @if(!empty($alumni->kelulusan->surat_kelulusan) && $kelulusanUuid)
                                                    <a href="{{ route('kelulusan.view.surat', $kelulusanUuid) }}" target="_blank" class="btn btn-sm btn-outline-success" title="Lihat Surat Kelulusan">
                                                        <i class="bi bi-file-earmark-check-fill"></i>
                                                    </a>
                                                @else
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center text-nowrap-custom">
                                                @if(!empty($alumni->kelulusan->ijazah) && $kelulusanUuid)
                                                    <a href="{{ route('kelulusan.view.ijazah', $kelulusanUuid) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Lihat Ijazah">
                                                        <i class="bi bi-file-earmark-pdf"></i>
                                                    </a>
                                                @else
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center text-nowrap-custom">
                                                @if(!empty($alumni->kelulusan->raport) && $kelulusanUuid)
                                                    <a href="{{ route('kelulusan.view.raport', $kelulusanUuid) }}" target="_blank" class="btn btn-sm btn-outline-danger" title="Lihat Raport">
                                                        <i class="bi bi-file-earmark-text"></i>
                                                    </a>
                                                @else
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
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