<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru</title>
    @if(isset($sekolah->logo))
    <link rel="icon" type="image/png" href="{{ asset($sekolah->logo) }}">
    @else
    <link rel="icon" type="image/png" href="{{ asset('assets/img/default-favicon.png') }}">
    @endif

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-green: #198754;
            --dark-green: #142d2d;
            --soft-bg: #f4f7f6;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--soft-bg);
        }

        .wrapper { display: flex; width: 100%; }

        #content { width: 100%; padding: 30px; transition: all 0.3s; }

        #sidebarCollapse {
            width: 45px;
            height: 45px;
            background: var(--primary-green);
            border: none;
            color: white;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(25, 135, 84, 0.2);
        }

        /* Card Welcome */
        .welcome-card {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--dark-green) 100%);
            color: white;
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(25, 135, 84, 0.15);
        }

        .clock-box {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(5px);
            border-radius: 12px;
            padding: 12px 25px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Form Styling */
        .card { border: none; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.03); }
        
        .form-label { font-weight: 600; color: #444; margin-bottom: 8px; }
        
        .form-select, .form-control {
            border-radius: 10px;
            padding: 12px;
            border: 1px solid #e0e0e0;
        }

        .form-select:focus, .form-control:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.1);
        }

        .form-section { display: none; } /* Hidden until class selected */

        .btn-submit {
            background-color: #dc3545;
            border: none;
            padding: 12px 35px;
            border-radius: 50px;
            font-weight: 700;
            transition: 0.3s;
        }

        .btn-submit:hover {
            background-color: #bb2d3b;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
        }
    </style>
</head>
<body>

<div class="wrapper">
    @include('dashboard_guru.sidebar_guru')

    <div id="content">
        <div class="container-fluid">

            <div class="d-flex align-items-center mb-4">
                <button type="button" id="sidebarCollapse" class="btn">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <div class="ms-3">
                    <h4 class="mb-0 fw-bold text-dark">Pencatatan Pelanggaran</h4>
                    <p class="text-muted small mb-0">Kelola kedisiplinan murid secara real-time</p>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm mb-4 alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card mb-4">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-1 text-success d-none d-md-block">
                            <i class="bi bi-door-open-fill fs-1"></i>
                        </div>
                        <div class="col-md-11">
                            <label class="form-label text-success fw-bold">Pilih Kelas Murid</label>
                            <select class="form-select border-success shadow-none" id="kelasSelect">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card form-section border-top border-danger border-5 shadow-sm" id="formPelanggaran">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-danger bg-opacity-10 p-3 rounded-circle me-3">
                            <i class="bi bi-exclamation-octagon-fill text-danger fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-danger">Form Input Pelanggaran</h5>
                            <small class="text-muted">Pastikan data yang dimasukkan sudah sesuai dengan kejadian</small>
                        </div>
                    </div>

                    <form action="{{ route('pelanggaran.storeMurid') }}" method="POST">
                        @csrf
                        <div class="row g-4 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Tanggal Laporan</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-calendar-event"></i></span>
                                    <input type="text" class="form-control bg-light border-start-0" id="tanggalDisplay" readonly>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Pilih Murid</label>
                                <select name="id_murid" id="muridSelect" class="form-select" required>
                                    <option value="">-- Memuat Data Murid... --</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Jenis Pelanggaran</label>
                                <select name="id_aturan_pelanggaran" class="form-select" required>
                                    <option value="">-- Pilih Peraturan --</option>
                                    @foreach($aturans as $at)
                                        <option value="{{ $at->id }}">{{ $at->nama_pelanggaran }} ({{ $at->skor }} Poin)</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Keterangan Tambahan / Kronologi</label>
                            <textarea name="keterangan" class="form-control" rows="4" placeholder="Jelaskan secara singkat kronologi atau detail pelanggaran..."></textarea>
                        </div>

                        <hr class="my-4 opacity-50">

                        <div class="d-flex justify-content-between align-items-center">
                            <p class="text-muted small mb-0"><i class="bi bi-info-circle me-1"></i> Data yang disimpan akan otomatis terakumulasi dalam skor poin murid.</p>
                            <button type="submit" class="btn btn-submit text-white shadow">
                                <i class="bi bi-save2-fill me-2"></i>Simpan Laporan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card mt-5 border-0 shadow-sm overflow-hidden">
    <div class="card-header bg-white p-4 border-0">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="fw-bold mb-0 text-dark">Daftar Pelanggaran Terkini</h5>
                <p class="text-muted small mb-0">Riwayat pencatatan kedisiplinan murid</p>
            </div>
            <div class="col-md-6 mt-3 mt-md-0">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" id="searchViolation" class="form-control bg-light border-start-0 shadow-none" 
                           placeholder="Cari nama murid, kelas, atau jenis pelanggaran...">
                </div>
            </div>
        </div>
    </div>
    
    <div class="table-responsive px-4 pb-4">
        <table class="table table-hover align-middle" id="violationTable">
            <thead class="table-light">
                <tr>
                    <th class="py-3 border-0">Nama Murid</th>
                    <th class="py-3 border-0">Pelanggaran</th>
                    <th class="py-3 border-0 text-center">Poin</th>
                    <th class="py-3 border-0 text-center">Status</th>
                    <th class="py-3 border-0">Tanggal</th>
                </tr>
            </thead>
            <tbody id="violationTableBody">
                @forelse($riwayatPelanggaran as $rp)
                <tr class="violation-row"> <td class="student-name">
                        <div class="fw-bold text-dark">{{ $rp->nama_lengkap }}</div>
                        <small class="text-muted">NISN: {{ $rp->nisn }}</small>
                    </td>
                    <td class="violation-type">
                        <span class="badge bg-secondary bg-opacity-10 text-secondary mb-1">{{ $rp->nama_kelas ?? 'Tanpa Kelas' }}</span>
                        <div>{{ $rp->nama_pelanggaran }}</div>
                    </td>
                    <td class="text-center">
                        <span class="text-danger fw-bold">+{{ $rp->skor }}</span>
                    </td>
                    <td class="text-center">
                        @if($rp->status == 'pending')
                            <span class="badge bg-warning text-dark px-3 py-2">Pending</span>
                        @elseif($rp->status == 'konfirmasi')
                            <span class="badge bg-success px-3 py-2">Dikonfirmasi</span>
                        @else
                            <span class="badge bg-danger px-3 py-2">Ditolak</span>
                        @endif
                    </td>
                    <td>
                        <div class="small text-dark">{{ date('d M Y', strtotime($rp->created_at)) }}</div>
                    </td>
                </tr>
                @empty
                <tr id="noDataRow">
                    <td colspan="5" class="text-center py-5 text-muted">Belum ada data riwayat.</td>
                </tr>
                @endforelse
                <tr id="notFoundRow" style="display: none;">
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="bi bi-search fs-1 d-block mb-2"></i>
                        Data yang Anda cari tidak ditemukan.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
        </div>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // 1. AJAX: Ambil Murid per Kelas
    // Ubah bagian ini di dashboard_guru/pelanggaran.blade.php

document.getElementById('kelasSelect').addEventListener('change', function () {
    const classId = this.value;
    const muridSelect = document.getElementById('muridSelect');
    const formSection = document.getElementById('formPelanggaran');

    if (classId) {
        formSection.style.display = 'block';
        muridSelect.innerHTML = '<option value="">Sedang memuat...</option>';

        // MEMANGGIL ROUTE BARU
        fetch("{{ route('murid.getByKelas') }}?kelas_id=" + classId)
        .then(response => response.json())
        .then(data => {
                muridSelect.innerHTML = '<option value="">-- Pilih Nama Murid --</option>';
                if (data.length > 0) {
                    data.forEach(m => {
                        // Memasukkan data ke dropdown
                        muridSelect.innerHTML += `<option value="${m.id}">${m.nama_lengkap} (${m.nisn})</option>`;
                    });
                } else {
                    muridSelect.innerHTML = '<option value="">Tidak ada murid di kelas ini</option>';
                }
            })
            .catch(error => {
                muridSelect.innerHTML = '<option value="">Gagal memuat data</option>';
                console.error('Error:', error);
            });
    } else {
        formSection.style.display = 'none';
    }
});

    // 2. Real-time Clock & Date
    function updateClock() {
        const now = new Date();
        const options = { 
            weekday: 'long', 
            day: 'numeric', 
            month: 'long', 
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        };
        const clockStr = now.toLocaleDateString('id-ID', options).replace(/\./g, ':');
        const clockElement = document.getElementById('realtime-clock');
        if(clockElement) clockElement.innerText = clockStr;
    }
    
    // Set display date for form
    document.getElementById('tanggalDisplay').value = new Date().toLocaleDateString('id-ID', {
        weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
    });

    setInterval(updateClock, 1000);
    updateClock();

    // 3. Sidebar Toggle
    document.getElementById('sidebarCollapse')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('inactive');
    });
    let debounceTimer;
document.getElementById('searchViolation').addEventListener('input', function () {
    const keyword = this.value;
    const tableBody = document.getElementById('violationTableBody');

    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        // Efek loading sementara
        tableBody.style.opacity = '0.5';

        fetch("{{ route('guru.pelanggaran.search') }}?search=" + keyword)
            .then(response => response.text())
            .then(html => {
                tableBody.innerHTML = html;
                tableBody.style.opacity = '1';
            })
            .catch(error => {
                console.error('Error:', error);
                tableBody.style.opacity = '1';
            });
    }, 500); // Tunggu 0.5 detik setelah berhenti mengetik
});
</script>

</body>
</html>