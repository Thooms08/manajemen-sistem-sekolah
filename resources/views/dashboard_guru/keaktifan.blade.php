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
        :root { --primary-green: #198754; --soft-bg: #f4f7f6; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--soft-bg); }
        .wrapper { display: flex; width: 100%; }
        #content { width: 100%; padding: 30px; }
        #sidebarCollapse { width: 45px; height: 45px; background: var(--primary-green); border: none; color: white; border-radius: 12px; }
        .upload-box { border: 2px dashed var(--primary-green); border-radius: 15px; padding: 30px; text-align: center; cursor: pointer; background: white; }
        .checklist-box { max-height: 250px; overflow-y: auto; background: white; border-radius: 10px; border: 1px solid #e0e0e0; padding: 15px; }
        .card { border: none; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.03); }
        .status-icon { font-size: 1.2rem; }
    </style>
</head>
<body>

<div class="wrapper">
    @include('dashboard_guru.sidebar_guru')

    <div id="content">
        <div class="container-fluid">
            <div class="d-flex align-items-center mb-4">
                <button type="button" id="sidebarCollapse" class="btn"><i class="bi bi-list fs-4"></i></button>
                <div class="ms-3">
                    <h4 class="mb-0 fw-bold text-dark">Keaktifan</h4>
                    <p class="text-muted small mb-0">Input & Pantau Kedisiplinan Murid</p>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm mb-4 alert-dismissible fade show">
                    {{ session('success') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card p-4 mb-4">
                <h5 class="fw-bold mb-4 text-success"><i class="bi bi-plus-circle me-2"></i>Input Keaktifan Baru</h5>
                <form action="{{ route('guru.keaktifan.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-4">
                        <div class="col-md-5">
                            <label class="form-label">Pilih Kelas</label>
                            <select class="form-select border-success" name="id_kelas" id="kelasSelect" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="keaktifanForm" style="display:none">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Kegiatan</label>
                                <input type="text" name="nama_keaktifan" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Keterangan</label>
                                <input type="text" name="keterangan" class="form-control">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Pilih Murid yang <span class="text-danger fw-bold">TIDAK MELAKUKAN</span> Keaktifan</label>
                            <div class="checklist-box" id="studentContainer">
                                </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="upload-box" id="uploadTrigger">
                                    <i class="bi bi-camera fs-2 text-success"></i>
                                    <p class="mb-0" id="fileName">Upload Foto Dokumentasi</p>
                                    <input type="file" name="foto" id="fotoInput" class="d-none">
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-success px-5 rounded-pill shadow">Simpan Data</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4 row">
                    <div class="col-md-6">
                        <h5 class="fw-bold mb-0">Riwayat Keaktifan Murid</h5>
                    </div>
                    <div class="col-md-4">
                        <form action="{{ route('guru.keaktifan') }}" method="GET" class="input-group">
                            <input type="date" name="tanggal" class="form-control" value="{{ $filterTanggal }}">
                            <button class="btn btn-success" type="submit"><i class="bi bi-filter"></i> Filter</button>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Murid</th>
                                <th>Kelas</th>
                                <th>Kegiatan</th>
                                <th class="text-center">Keaktifan</th>
                                <th>Tanggal</th>
                                <th class="text-center">Aksi</th> 
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($riwayat as $r)
                            <tr>
                                <td>{{ $r->nama_lengkap }}</td>
                                <td>{{ $r->nama_kelas }}</td>
                                <td>{{ $r->nama_keaktifan }}</td>
                                <td class="text-center">
                                    @if($r->is_active)
                                        <i class="bi bi-check-circle-fill text-success status-icon"></i>
                                    @else
                                        <i class="bi bi-x-circle-fill text-danger status-icon"></i>
                                    @endif
                                </td>
                                <td>{{ date('d/m/Y', strtotime($r->tanggal)) }}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-success border-0" onclick="editKeaktifan({{ $r->keaktifan_id }})">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">Tidak ada data keaktifan pada tanggal ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="formEdit" method="POST">
            @csrf @method('PUT')
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold">Edit Status Keaktifan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Kegiatan</label>
                        <input type="text" name="nama_keaktifan" id="editNama" class="form-control" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Daftar Murid (Ceklis yang <span class="text-danger">TIDAK AKTIF</span>)</label>
                        <div class="checklist-box border p-3" id="editStudentContainer" style="max-height: 300px; overflow-y: auto;">
                            </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Keterangan</label>
                        <textarea name="keterangan" id="editKeterangan" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // AJAX: Load Murid per Kelas
    document.getElementById('kelasSelect').addEventListener('change', function () {
        const classId = this.value;
        const form = document.getElementById('keaktifanForm');
        const container = document.getElementById('studentContainer');

        if (classId) {
            form.style.display = 'block';
            container.innerHTML = 'Memuat...';
            fetch(`{{ route('murid.getByKelas') }}?kelas_id=${classId}`)
                .then(res => res.json())
                .then(data => {
                    container.innerHTML = '';
                    data.forEach(m => {
                        container.innerHTML += `
                            <div class="form-check py-1">
                                <input class="form-check-input" type="checkbox" name="murid_ids[]" value="${m.id}" id="m${m.id}">
                                <label class="form-check-label" for="m${m.id}">${m.nama_lengkap} (${m.nisn})</label>
                            </div>`;
                    });
                });
        } else { form.style.display = 'none'; }
    });

    // Upload Trigger
    document.getElementById('uploadTrigger').onclick = () => document.getElementById('fotoInput').click();
    document.getElementById('fotoInput').onchange = function() {
        if(this.files.length > 0) document.getElementById('fileName').innerText = this.files[0].name;
    };
    function editKeaktifan(id) {
    const container = document.getElementById('editStudentContainer');
    const form = document.getElementById('formEdit');
    
    // Set Action URL
    form.action = `/keaktifan_guru/${id}`;
    
    container.innerHTML = 'Memuat data...';
    
    // Buka Modal
    const modal = new bootstrap.Modal(document.getElementById('modalEdit'));
    modal.show();

    fetch(`/keaktifan_guru/${id}/edit`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('editNama').value = data.keaktifan.nama_keaktifan;
            document.getElementById('editKeterangan').value = data.keaktifan.keterangan || '';
            
            container.innerHTML = '';
            data.murids.forEach(m => {
                // Cek apakah murid ini sebelumnya dichecklist (tidak aktif)
                const checked = data.tidakAktifIds.includes(m.id) ? 'checked' : '';
                
                container.innerHTML += `
                    <div class="form-check py-1">
                        <input class="form-check-input" type="checkbox" name="murid_ids[]" value="${m.id}" id="edit_m${m.id}" ${checked}>
                        <label class="form-check-label" for="edit_m${m.id}">${m.nama_lengkap}</label>
                    </div>`;
            });
        });
}
</script>
</body>
</html>