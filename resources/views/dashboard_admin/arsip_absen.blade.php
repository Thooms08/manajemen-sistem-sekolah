<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arsip Absensi Murid</title>
    @if(isset($sekolah->logo))
    <link rel="icon" type="image/png" href="{{ asset($sekolah->logo) }}">
    @else
    <link rel="icon" type="image/png" href="{{ asset('assets/img/default-favicon.png') }}">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root { --admin-green: #198754; --bg-light: #f8f9fa; }
        body { background-color: var(--bg-light); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .wrapper { display: flex; }
        #content { width: 100%; padding: 30px; }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .filter-section { background: white; padding: 20px; border-radius: 15px; margin-bottom: 25px; }
        .box-tgl { width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; border-radius: 10px; font-weight: 600; color: white; margin: 4px; font-size: 14px; }
        .bg-hadir { background-color: #198754; } 
        .bg-alfa { background-color: #dc3545; } 
        .bg-libur { background-color: #fd7e14; } 
        .bg-empty { background-color: #e9ecef; color: #adb5bd; }
        #calendarGrid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 5px; max-width: 350px; margin: 0 auto; }
        #sidebarCollapse { background: var(--admin-green); color: white; border-radius: 10px; border: none; padding: 10px; }
    </style>
</head>
<body>

<div class="wrapper">
    @include('dashboard_admin.sidebar_admin')

    <div id="content">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center">
                    <button type="button" id="sidebarCollapse"><i class="bi bi-list fs-4"></i></button>
                    <h4 class="ms-3 mb-0 fw-bold text-dark">Arsip Absensi Murid</h4>
                </div>
            </div>

            <div class="filter-section shadow-sm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small text-muted">Kelas</label>
                        <select id="filterKelas" class="form-select border-success shadow-none">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small text-muted">Bulan</label>
                        <select id="filterBulan" class="form-select border-success shadow-none">
                            @for($i=1; $i<=12; $i++)
                                <option value="{{ $i }}" {{ date('m') == $i ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create(null, $i)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small text-muted">Tahun</label>
                        <select id="filterTahun" class="form-select border-success shadow-none">
                            @for($i=date('Y'); $i>=2024; $i--)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-semibold small text-muted">Cari Nama Murid</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-success text-success"><i class="bi bi-search"></i></span>
                            <input type="text" id="searchName" class="form-control border-success border-start-0 shadow-none" placeholder="Ketik nama...">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-success text-white">
                            <tr>
                                <th class="py-3 ps-4">Nama Murid</th>
                                <th class="py-3 text-center">Hadir</th>
                                <th class="py-3 text-center">Alfa</th>
                                <th class="py-3 text-center">Rekap Absen</th>
                            </tr>
                        </thead>
                        <tbody id="muridTableBody">
                            <tr><td colspan="4" class="text-center py-5 text-muted">Silakan pilih kelas terlebih dahulu</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalRekap" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold" id="rekapTitle">Rekap Absensi</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center pb-5">
                <div id="calendarGrid" class="mb-4"></div>
                <div class="d-flex justify-content-center gap-3 small">
                    <span><i class="bi bi-circle-fill text-success"></i> Hadir</span>
                    <span><i class="bi bi-circle-fill text-danger"></i> Alfa</span>
                    <span><i class="bi bi-circle-fill text-warning"></i> Libur</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const muridTable = document.getElementById('muridTableBody');
    const modalRekap = new bootstrap.Modal(document.getElementById('modalRekap'));

    function loadData() {
        const idKelas = document.getElementById('filterKelas').value;
        const bulan = document.getElementById('filterBulan').value;
        const tahun = document.getElementById('filterTahun').value;
        const search = document.getElementById('searchName').value;

        if (!idKelas) return;

        muridTable.innerHTML = '<tr><td colspan="4" class="text-center py-5"><div class="spinner-border text-success"></div></td></tr>';

        fetch(`{{ route('admin.arsip.murid') }}?id_kelas=${idKelas}&bulan=${bulan}&tahun=${tahun}&search=${search}`)
            .then(res => res.json())
            .then(data => {
                muridTable.innerHTML = '';
                if (data.length === 0) {
                    muridTable.innerHTML = '<tr><td colspan="4" class="text-center py-5 text-muted">Data tidak ditemukan</td></tr>';
                    return;
                }
                data.forEach(m => {
                    muridTable.innerHTML += `
                        <tr>
                            <td class="ps-4 fw-bold text-dark">${m.nama_lengkap} <br> <small class="text-muted fw-normal">${m.nisn}</small></td>
                            <td class="text-center"><span class="badge bg-success-subtle text-success px-3">${m.total_hadir} Hari</span></td>
                            <td class="text-center"><span class="badge bg-danger-subtle text-danger px-3">${m.total_alfa} Hari</span></td>
                            <td class="text-center">
                                <button class="btn btn-outline-success btn-sm rounded-pill px-3" onclick="showCalendar(${m.id}, '${m.nama_lengkap}')">
                                    <i class="bi bi-calendar3 me-1"></i> Lihat Rekap
                                </button>
                            </td>
                        </tr>`;
                });
            });
    }

    // Trigger Load Data
    ['change', 'input'].forEach(evt => {
        ['filterKelas', 'filterBulan', 'filterTahun', 'searchName'].forEach(id => {
            document.getElementById(id).addEventListener(evt, () => {
                if(evt === 'input' && id === 'searchName') {
                    clearTimeout(window.searchTimer);
                    window.searchTimer = setTimeout(loadData, 500);
                } else {
                    loadData();
                }
            });
        });
    });

    
    function showCalendar(idMurid, nama) {
        const bulan = document.getElementById('filterBulan').value;
        const tahun = document.getElementById('filterTahun').value;
        const grid = document.getElementById('calendarGrid');
        
        document.getElementById('rekapTitle').innerText = `Rekap ${nama}`;
        grid.innerHTML = '<div class="spinner-border text-success"></div>';
        modalRekap.show();

        fetch(`{{ route('admin.arsip.rekap') }}?id_murid=${idMurid}&bulan=${bulan}&tahun=${tahun}`)
            .then(res => res.json())
            .then(data => {
                grid.innerHTML = '';
                data.forEach(day => {
                    let colorClass = 'bg-empty';
                    if(day.status === 'hadir') colorClass = 'bg-hadir';
                    if(day.status === 'tidak_hadir') colorClass = 'bg-alfa';
                    if(day.status === 'libur') colorClass = 'bg-libur';

                    grid.innerHTML += `<div class="box-tgl ${colorClass}">${day.tgl}</div>`;
                });
            });
    }

    // Hamburger Menu Logic
    document.getElementById('sidebarCollapse').onclick = () => {
        document.getElementById('sidebar').classList.toggle('inactive');
    };
</script>

</body>
</html>