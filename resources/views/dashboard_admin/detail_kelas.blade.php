<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Kelas {{ $kelas->nama_kelas }}</title>
    @if(isset($sekolah->logo))
    <link rel="icon" type="image/png" href="{{ asset($sekolah->logo) }}">
    @else
    <link rel="icon" type="image/png" href="{{ asset('assets/img/default-favicon.png') }}">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f4f7f6; }
        .wrapper { display: flex; width: 100%; }
        #content { width: 100%; padding: 20px 30px; }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .btn-success { background-color: #198754; border: none; }
        .student-row:hover { background-color: #f8f9fa; }
    </style>
</head>
<body>
    <div class="wrapper">
        @include('dashboard_admin.sidebar_admin')

        <div id="content">
            <div class="container-fluid">
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('kelas.index') }}" class="text-success"><i class="bi bi-arrow-left"></i>Kembali</a></li>
                        <li class="breadcrumb-item active">{{ $kelas->nama_kelas }}</li>
                    </ol>
                </nav>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="fw-bold text-success"><i class="bi bi-door-open me-2"></i>Kelas: {{ $kelas->nama_kelas }}</h3>
                    <button class="btn btn-success px-4" data-bs-toggle="modal" data-bs-target="#modalTambahMurid">
                        <i class="bi bi-person-plus me-2"></i>+ Tambah Murid
                    </button>
                </div>

                @if(session('success'))
                    <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('success') }}</div>
                @endif

                <div class="card p-4">
                    <h6 class="fw-bold mb-4">Daftar Murid Terdaftar</h6>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Murid</th>
                                    <th>NISN</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kelas->murid as $index => $m)
                                <tr class="student-row">
                                    <td>{{ $index + 1 }}</td>
                                    <td class="fw-bold">{{ $m->nama_lengkap }}</td>
                                    <td>{{ $m->nisn }}</td>
                                    <td class="text-center">
                                        <form action="{{ route('kelas.removeStudent', $m->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Keluarkan murid ini?')">
                                                <i class="bi bi-x-circle me-1"></i> Keluarkan
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center p-5 text-muted">Belum ada murid di kelas ini.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTambahMurid" tabindex="-1">
        <div class="modal-dialog modal-md">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold">Pilih Murid Untuk Kelas Ini</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" id="searchMurid" class="form-control" placeholder="Cari nama atau nisn...">
                    </div>
                    <div style="max-height: 400px; overflow-y: auto;">
                        @foreach($muridTersedia as $mt)
                        <div class="student-select-item p-3 border rounded mb-2 d-flex justify-content-between align-items-center" data-name="{{ strtolower($mt->nama_murid) }} {{ $mt->nisn }}">
                            <div>
                                <div class="fw-bold">{{ $mt->nama_lengkap }}</div>
                                <small class="text-muted">NISN: {{ $mt->nisn }}</small>
                            </div>
                            <form action="{{ route('kelas.addStudent') }}" method="POST">
                                @csrf
                                <input type="hidden" name="id_kelas" value="{{ $kelas->id }}">
                                <input type="hidden" name="id_murid" value="{{ $mt->id }}">
                                <button type="submit" class="btn btn-sm btn-success">Pilih</button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('searchMurid').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let items = document.querySelectorAll('.student-select-item');
            items.forEach(item => {
                let text = item.getAttribute('data-name');
                item.style.display = text.includes(filter) ? 'flex' : 'none';
            });
        });
    </script>
</body>
</html>