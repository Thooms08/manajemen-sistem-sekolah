<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Guru</title>
    @if(isset($sekolah->logo))
    <link rel="icon" type="image/png" href="{{ asset($sekolah->logo) }}">
    @else
    <link rel="icon" type="image/png" href="{{ asset('assets/img/default-favicon.png') }}">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root { --primary-green: #198754; --bg-soft: #f4f7f6; }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg-soft); }
        .wrapper { display: flex; }
        #content { width: 100%; padding: 25px; }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .btn-success { background-color: var(--primary-green); border: none; border-radius: 10px; }
        .box-rekap { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 8px; font-weight: bold; color: white; margin: 3px; font-size: 12px; }
        .bg-hadir { background-color: #198754; } .bg-tidak-hadir { background-color: #dc3545; } .bg-libur { background-color: #fd7e14; } .bg-none { background-color: #dee2e6; color: #6c757d; }
        #sidebarCollapse { background: var(--primary-green); border: none; color: white; border-radius: 10px; padding: 8px 12px; }
        #calendarContainer { display: grid; grid-template-columns: repeat(7, 1fr); gap: 5px; max-width: 350px; margin: 0 auto; }
        
        /* Style Tambahan untuk Search */
        .search-container { position: relative; }
        .search-container i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--primary-green); }
        .search-input { padding-left: 45px !important; border-radius: 12px !important; }
    </style>
</head>
<body>

<div class="wrapper">
    @include('dashboard_guru.sidebar_guru')

    <div id="content">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center">
                    <button type="button" id="sidebarCollapse"><i class="bi bi-list fs-4"></i></button>
                    <h5 class="ms-3 mb-0 fw-bold">Manajemen Absensi</h5>
                </div>
                <button class="btn btn-outline-success fw-bold px-4" onclick="openArsipModal()">
                    <i class="bi bi-archive me-2"></i> Arsip Absensi
                </button>
            </div>

            @if($isMinggu)
                <div class="alert alert-warning border-0 shadow-sm">
                    <i class="bi bi-info-circle me-2"></i> Hari ini adalah hari Minggu. Input absensi dinonaktifkan.
                </div>
            @endif

            <div class="card p-4">
                <form action="{{ route('guru.absensi.store') }}" method="POST">
                    @csrf
                    <div class="row mb-4 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Pilih Kelas</label>
                            <select name="id_kelas" id="kelasSelect" class="form-select border-success shadow-none py-2">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-8 text-md-end mt-3">
                            <span class="badge bg-success-subtle text-success p-2 px-3 fs-6">
                                <i class="bi bi-calendar3 me-2"></i> {{ Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                            </span>
                        </div>
                    </div>

                    <div id="tableContainer" style="display:none">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Murid</th>
                                    <th width="200">Kehadiran</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody id="studentList"></tbody>
                        </table>
                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-success px-5 py-2 fw-bold" {{ $isMinggu ? 'disabled' : '' }}>
                                <i class="bi bi-save me-2"></i> Simpan Absensi
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalArsip" tabindex="-1" data-bs-focus="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold">Arsip Absensi Murid</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <select id="arsipKelas" class="form-select border-success shadow-none">
                            <option value="">Pilih Kelas</option>
                            @foreach($kelas as $k) <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option> @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select id="arsipBulan" class="form-select border-success shadow-none">
                            @for($i=1; $i<=12; $i++) <option value="{{ $i }}" {{ date('m') == $i ? 'selected' : '' }}>{{ Carbon\Carbon::create(null, $i)->translatedFormat('F') }}</option> @endfor
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select id="arsipTahun" class="form-select border-success shadow-none">
                            @for($i=date('Y'); $i>=2023; $i--) <option value="{{ $i }}">{{ $i }}</option> @endfor
                        </select>
                    </div>
                </div>

                <div class="mb-4 search-container" style="position: relative;">
                    <i class="bi bi-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #198754;"></i>
                    <input type="text" id="searchArsip" class="form-control shadow-none border-success" style="padding-left: 45px; border-radius: 12px;" placeholder="Cari nama murid...">
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Murid</th>
                                <th class="text-center">Hadir</th>
                                <th class="text-center">Alfa/Izin</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="arsipList">
                            <tr><td colspan="4" class="text-center text-muted py-4">Silakan pilih kelas terlebih dahulu</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalRekap" tabindex="-1" data-bs-focus="false">
    <div class="modal-dialog">
        <div class="modal-content border-0">
            <div class="modal-header bg-dark text-white">
                <h6 class="modal-title fw-bold" id="rekapName">Rekap Absensi</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="calendarContainer"></div>
                <div class="mt-4 small d-flex justify-content-center gap-3">
                    <span><i class="bi bi-square-fill text-success"></i> Hadir</span>
                    <span><i class="bi bi-square-fill text-danger"></i> Tidak Hadir</span>
                    <span><i class="bi bi-square-fill text-warning"></i> Libur</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Inisialisasi Modals
    const arsipModal = new bootstrap.Modal(document.getElementById('modalArsip'));
    const rekapModal = new bootstrap.Modal(document.getElementById('modalRekap'));

    function openArsipModal() { arsipModal.show(); }

    // 1. AJAX: Load Murid untuk Form Absensi Utama
    document.getElementById('kelasSelect').addEventListener('change', function() {
        const id = this.value;
        const container = document.getElementById('tableContainer');
        const list = document.getElementById('studentList');
        if(!id) { container.style.display = 'none'; return; }

        fetch(`{{ route('murid.getByKelas') }}?kelas_id=${id}`)
            .then(res => res.json())
            .then(data => {
                list.innerHTML = '';
                data.forEach(m => {
                    list.innerHTML += `<tr>
                        <td><span class="fw-bold">${m.nama_lengkap}</span><br><small class="text-muted">${m.nisn}</small></td>
                        <td>
                            <select name="absensi[${m.id}][status]" class="form-select rounded-pill shadow-none" {{ $isMinggu ? 'disabled' : '' }}>
                                <option value="hadir">Hadir</option>
                                <option value="tidak_hadir">Tidak Hadir</option>
                            </select>
                        </td>
                        <td><input type="text" name="absensi[${m.id}][keterangan]" class="form-control rounded-pill" placeholder="Catatan..." {{ $isMinggu ? 'disabled' : '' }}></td>
                    </tr>`;
                });
                container.style.display = 'block';
            });
    });

    function fetchArsipMurid() {
        const idKelas = document.getElementById('arsipKelas').value;
        const bulan = document.getElementById('arsipBulan').value;
        const tahun = document.getElementById('arsipTahun').value;
        const search = document.getElementById('searchArsip').value;
        const listContainer = document.getElementById('arsipList');

        if (!idKelas) return;

        listContainer.innerHTML = '<tr><td colspan="4" class="text-center py-3">Memuat data...</td></tr>';

        // Kirim semua parameter ke controller
        const params = new URLSearchParams({
            id_kelas: idKelas,
            bulan: bulan,
            tahun: tahun,
            search: search
        });

        fetch(`{{ route('guru.absensi.arsip') }}?${params.toString()}`)
            .then(res => res.json())
            .then(data => {
                listContainer.innerHTML = '';
                if (data.length === 0) {
                    listContainer.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">Data tidak ditemukan</td></tr>';
                    return;
                }
                
                data.forEach(m => {
                    listContainer.innerHTML += `
                        <tr>
                            <td><span class="fw-bold text-dark">${m.nama_lengkap}</span></td>
                            <td class="text-center"><span class="badge bg-success-subtle text-success">${m.total_hadir} Hari</span></td>
                            <td class="text-center"><span class="badge bg-danger-subtle text-danger">${m.total_tidak_hadir} Hari</span></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-success rounded-pill px-3" onclick="viewRekap(${m.id}, '${m.nama_lengkap}')">
                                    <i class="bi bi-calendar3"></i> Detail
                                </button>
                            </td>
                        </tr>`;
                });
            })
            .catch(err => {
                listContainer.innerHTML = '<tr><td colspan="4" class="text-center text-danger py-4">Gagal memuat data</td></tr>';
            });
    }

    // Listener untuk setiap perubahan filter
    document.getElementById('arsipKelas').addEventListener('change', fetchArsipMurid);
    document.getElementById('arsipBulan').addEventListener('change', fetchArsipMurid);
    document.getElementById('arsipTahun').addEventListener('change', fetchArsipMurid);

    // Search dengan Debounce
    let timer;
    document.getElementById('searchArsip').addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(fetchArsipMurid, 300);
    });

    // Fungsi Detail Kalender (Tetap sama)
    function viewRekap(id, nama) {
        const bulan = document.getElementById('arsipBulan').value;
        const tahun = document.getElementById('arsipTahun').value;
        const container = document.getElementById('calendarContainer');
        document.getElementById('rekapName').innerText = `Rekap ${nama} (${bulan}/${tahun})`;
        container.innerHTML = '<div class="text-center p-4"><div class="spinner-border text-success"></div></div>';

        fetch(`{{ route('guru.absensi.rekap') }}?id_murid=${id}&bulan=${bulan}&tahun=${tahun}`)
            .then(res => res.json())
            .then(data => {
                container.innerHTML = '';
                data.forEach(day => {
                    let color = 'bg-none';
                    if(day.status === 'hadir') color = 'bg-hadir';
                    else if(day.status === 'tidak_hadir') color = 'bg-tidak-hadir';
                    else if(day.status === 'libur') color = 'bg-libur';
                    container.innerHTML += `<div class="box-rekap ${color}">${day.tgl}</div>`;
                });
                rekapModal.show();
            });
    }

    document.getElementById('sidebarCollapse').onclick = () => document.getElementById('sidebar').classList.toggle('inactive');
</script>
</body>
</html>