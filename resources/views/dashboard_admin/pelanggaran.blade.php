<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pelanggaran Murid</title>
    @if(isset($sekolah->logo))
    <link rel="icon" type="image/png" href="{{ asset($sekolah->logo) }}">
    @else
    <link rel="icon" type="image/png" href="{{ asset('assets/img/default-favicon.png') }}">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root { --primary-green: #198754; --soft-green: #f8faf9; }
        body { background-color: var(--soft-green); font-family: 'Plus Jakarta Sans', sans-serif; }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .table thead { background-color: #1a3a3a; color: white; }
        .wrapper { display: flex; }
        #content { width: 100%; padding: 25px; }
        .skor-tinggi { background-color: #f8d7da !important; }
        .skor-mendekati { background-color: #fff3cd !important; }
        .skor-sedang { background-color: #e2f0d9 !important; }
        .modal-header { border-bottom: none; }
        .modal-footer { border-top: none; }
    </style>
</head>
<body>

<div class="wrapper">
    @include('dashboard_admin.sidebar_admin')

    <div id="content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-success"><i class="bi bi-exclamation-triangle-fill me-2"></i>Sistem Pelanggaran Murid</h4>
            <div class="gap-2 d-flex">
                <button class="btn btn-success fw-bold px-4 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#modalDaftarAturan">
                    <i class="bi bi-journal-text me-2"></i>Peraturan Pelanggaran
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-4 alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm mb-4 alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

       <div class="card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold m-0 text-dark">Riwayat Pelanggaran Terkini</h5>
        <div class="input-group" style="max-width: 300px;">
            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
            <input type="text" id="ajaxSearchRiwayat" class="form-control border-start-0 ps-0" placeholder="Cari Murid atau Pelanggaran...">
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>NISN</th>
                    <th>Nama Murid</th>
                    <th>Pelanggaran</th>
                    <th>Keterangan</th>
                    <th class="text-center">Skor</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody id="pelanggaranTableBody">
                @foreach($pelanggaranMurid as $p)
                @php
                    $rowColor = '';
                    if($p->skor >= 50) $rowColor = 'skor-tinggi';
                    elseif($p->skor >= 25) $rowColor = 'skor-mendekati';
                    elseif($p->skor >= 10) $rowColor = 'skor-sedang';
                    
                    // Logika sembunyikan baris > 5
                    $isHidden = $loop->index >= 5 ? 'd-none extra-row' : '';
                @endphp
                <tr class="{{ $rowColor }} {{ $isHidden }}">
                    <td class="fw-bold">{{ $p->nisn }}</td>
                    <td>{{ $p->nama_lengkap }}</td>
                    <td><span class="badge bg-dark">{{ $p->nama_pelanggaran }}</span></td>
                    <td><small class="text-muted">{{ $p->keterangan ?? '-' }}</small></td>
                    <td class="text-center fw-bold">{{ $p->skor }}</td>
                    <td class="text-center">
                        <form action="{{ route('pelanggaran.destroy', $p->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm text-danger" onclick="return confirm('Hapus catatan ini?')"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if(count($pelanggaranMurid) > 5)
    <div class="text-center mt-3">
        <button id="btnToggleRows" class="btn btn-sm btn-outline-success rounded-pill px-4 fw-bold">
            <i class="bi bi-chevron-down me-1"></i> Lihat Semua
        </button>
    </div>
    @endif
</div>

        <div class="card p-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold m-0 text-danger"><i class="bi bi-bar-chart-fill me-2"></i>Grafik Akumulasi Skor Murid</h5>
        <div class="small text-muted">Data berdasarkan skor kumulatif setiap murid</div>
    </div>
    
    <div style="position: relative; height:400px; width:100%">
        <canvas id="skorChart"></canvas>
    </div>
    <div class="d-flex gap-3 justify-content-center mt-4">
        <div class="small"><span class="badge bg-danger">●</span> SP 3 (>100)</div>
        <div class="small"><span class="badge bg-warning text-dark">●</span> Peringatan (75-99)</div>
        <div class="small"><span class="badge bg-info text-dark">●</span> Pembinaan (<75)
    </div>
</div>
    </div>
</div>

<div class="modal fade" id="modalDaftarAturan" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-list-check me-2"></i>Daftar Peraturan & Skor</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('pelanggaran.storeAturan') }}" method="POST" class="row g-3 mb-4 p-3 bg-light rounded-3 border">
                    @csrf
                    <div class="col-md-7">
                        <label class="form-label small fw-bold">Nama Pelanggaran Baru</label>
                        <input type="text" name="nama_pelanggaran" class="form-control" placeholder="Contoh: Terlambat" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Skor</label>
                        <input type="number" name="skor" class="form-control" placeholder="Poin" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-success w-100"><i class="bi bi-save"></i></button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Pelanggaran</th>
                                <th width="100" class="text-center">Skor</th>
                                <th width="120" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($aturans as $at)
                            <tr>
                                <td>{{ $at->nama_pelanggaran }}</td>
                                <td class="text-center fw-bold">{{ $at->skor }}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalEditAturan{{ $at->id }}"><i class="bi bi-pencil"></i></button>
                                    <form action="{{ route('pelanggaran.destroyAturan', $at->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus aturan ini?')"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@foreach($aturans as $at)
<div class="modal fade" id="modalEditAturan{{ $at->id }}" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <form action="{{ route('pelanggaran.updateAturan', $at->id) }}" method="POST" class="modal-content shadow-lg">
            @csrf @method('PUT')
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Edit Aturan</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="small fw-bold">Nama Pelanggaran</label>
                    <input type="text" name="nama_pelanggaran" class="form-control" value="{{ $at->nama_pelanggaran }}" required>
                </div>
                <div class="mb-3">
                    <label class="small fw-bold">Skor</label>
                    <input type="number" name="skor" class="form-control" value="{{ $at->skor }}" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success w-100">Update</button>
            </div>
        </form>
    </div>
</div>
@endforeach
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('sidebarCollapse')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('inactive');
    });
    const searchInput = document.getElementById('ajaxSearchRiwayat');
    const tableBody = document.getElementById('pelanggaranTableBody');

    searchInput.addEventListener('keyup', function() {
        let keyword = searchInput.value;
        tableBody.style.opacity = '0.5';

        fetch("{{ route('pelanggaran.ajaxSearch') }}?search=" + keyword)
            .then(response => response.text())
            .then(data => {
                tableBody.innerHTML = data;
                tableBody.style.opacity = '1';
            })
            .catch(error => {
                console.error('Error:', error);
                tableBody.style.opacity = '1';
            });
    });
    const btnToggle = document.getElementById('btnToggleRows');
    
    btnToggle?.addEventListener('click', function() {
        const extraRows = document.querySelectorAll('.extra-row');
        const isHidden = extraRows[0].classList.contains('d-none');

        if (isHidden) {
            // Tampilkan semua
            extraRows.forEach(row => row.classList.remove('d-none'));
            this.innerHTML = '<i class="bi bi-chevron-up me-1"></i> Sembunyikan';
        } else {
            // Sembunyikan kembali
            extraRows.forEach(row => row.classList.add('d-none'));
            this.innerHTML = '<i class="bi bi-chevron-down me-1"></i> Lihat Semua';
        }
    });
    const dataSkor = @json($akumulasiSkor);
    const labels = dataSkor.map(item => item.nama_lengkap);
    const scores = dataSkor.map(item => item.total_skor);
    const backgroundColors = dataSkor.map(item => {
        if (item.total_skor >= 100) return 'rgba(220, 53, 69, 0.8)'; 
        if (item.total_skor >= 75) return 'rgba(255, 193, 7, 0.8)';  
        return 'rgba(13, 202, 240, 0.8)';                           
    });

    const borderColors = dataSkor.map(item => {
        if (item.total_skor >= 100) return '#dc3545';
        if (item.total_skor >= 75) return '#ffc107';
        return '#0dcaf0';
    });

    // Inisialisasi Chart.js
    const ctx = document.getElementById('skorChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Skor Pelanggaran',
                data: scores,
                backgroundColor: backgroundColors,
                borderColor: borderColors,
                borderWidth: 1,
                borderRadius: 8, // Membuat ujung batang agak bulat (modern)
                barThickness: 40 // Lebar batang
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Poin Pelanggaran'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Nama Murid'
                    }
                }
            },
            plugins: {
                legend: {
                    display: false // Sembunyikan label dataset karena warna sudah mewakili status
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += context.parsed.y + ' Poin';
                            return label;
                        }
                    }
                }
            }
        }
    });
</script>
</body>
</html>