<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Sekolah</title>
    @if(isset($sekolah->logo))
    <link rel="icon" type="image/png" href="{{ asset($sekolah->logo) }}">
    @else
    <link rel="icon" type="image/png" href="{{ asset('assets/img/default-favicon.png') }}">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f4f7f6; }
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        #content { width: 100%; padding: 20px; transition: all 0.3s; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .btn-success { background-color: #198754; border: none; }
        .btn-success:hover { background-color: #146c43; }
        .table thead { background-color: #1a3a3a; color: white; }
        #overlay { display: none; position: fixed; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 1040; top: 0; left: 0; }
        #overlay.active { display: block; }
    </style>
</head>
<body>
    <div id="overlay"></div>
    <div class="wrapper">
        @include('dashboard_admin.sidebar_admin')

        <div id="content">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex align-items-center">
                        <button type="button" id="sidebarCollapse" class="btn btn-success me-3"><i class="bi bi-list"></i></button>
                        <h4 class="fw-bold mb-0 text-success">Kelola Profile Sekolah</h4>
                    </div>
                    @if($profiles->count() == 0)
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambah">
                        <i class="bi bi-plus-lg me-2"></i>Tambah Profile
                    </button>
                    @endif
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Logo</th>
                                    <th>Sekolah & NIS</th>
                                    <th>Kontak</th>
                                    <th>Akreditasi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($profiles as $p)
                                <tr>
                                    <td><img src="{{ asset($p->logo) }}" width="50" class="rounded"></td>
                                    <td>
                                        <div class="fw-bold">{{ $p->nama_sekolah }}</div>
                                        <small class="text-muted">NIS: {{ $p->nis }}</small>
                                    </td>
                                    <td>
                                        <small><i class="bi bi-telephone text-success"></i> {{ $p->no_hp }}</small><br>
                                        <small><i class="bi bi-envelope text-success"></i> {{ $p->email }}</small>
                                    </td>
                                    <td><span class="badge bg-success">{{ $p->akreditasi }}</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $p->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('profile-sekolah.destroy', $p->id) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus data ini?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                <div class="modal fade" id="modalEdit{{ $p->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <form action="{{ route('profile-sekolah.update', $p->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf @method('PUT')
                                            <div class="modal-content">
                                                <div class="modal-header bg-success text-white">
                                                    <h5 class="modal-title">Edit Profile Sekolah</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label">Nama Sekolah</label>
                                                            <input type="text" name="nama_sekolah" class="form-control" value="{{ $p->nama_sekolah }}" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">NIS</label>
                                                            <input type="text" name="nis" class="form-control" value="{{ $p->nis }}" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-bold">Logo Sekolah</label>
                                                            <div id="preview-container-logo-{{ $p->id }}" class="mb-2 p-2 border rounded bg-light text-center">
                                                                @if($p->logo)
                                                                    <img src="{{ asset($p->logo) }}" class="img-thumbnail mb-2" style="height: 100px;">
                                                                    <button type="button" class="btn btn-sm btn-danger d-block mx-auto" onclick="ajaxDeleteImage('{{ $p->id }}', 'logo')">
                                                                        <i class="bi bi-x-circle me-1"></i> Hapus Logo
                                                                    </button>
                                                                @else
                                                                    <small class="text-muted d-block py-3">Belum ada logo</small>
                                                                @endif
                                                            </div>
                                                            <input type="file" name="logo" class="form-control">
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label class="form-label fw-bold">Foto Sekolah Utama</label>
                                                            <div id="preview-container-foto-{{ $p->id }}" class="mb-2 p-2 border rounded bg-light text-center">
                                                                @if($p->foto_sekolah)
                                                                    <img src="{{ asset($p->foto_sekolah) }}" class="img-thumbnail mb-2" style="height: 100px; width: 100%; object-fit: cover;">
                                                                    <button type="button" class="btn btn-sm btn-danger d-block mx-auto" onclick="ajaxDeleteImage('{{ $p->id }}', 'foto_sekolah')">
                                                                        <i class="bi bi-x-circle me-1"></i> Hapus Foto
                                                                    </button>
                                                                @else
                                                                    <small class="text-muted d-block py-3">Belum ada foto sekolah</small>
                                                                @endif
                                                            </div>
                                                            <input type="file" name="foto_sekolah" class="form-control">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Email</label>
                                                            <input type="email" name="email" class="form-control" value="{{ $p->email }}" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Nomor HP</label>
                                                            <input type="text" name="no_hp" class="form-control" value="{{ $p->no_hp }}">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Akreditasi</label>
                                                            <input type="text" name="akreditasi" class="form-control" value="{{ $p->akreditasi }}">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Google Maps Link</label>
                                                            <input type="text" name="tautan_google_maps" class="form-control" value="{{ $p->tautan_google_maps }}">
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label">Deskripsi</label>
                                                            <textarea name="deskripsi" class="form-control" rows="3">{{ $p->deskripsi }}</textarea>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label">Alamat</label>
                                                            <textarea name="alamat" class="form-control" rows="2">{{ $p->alamat }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-success w-100">Simpan Perubahan</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('profile-sekolah.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">Tambah Profile Sekolah</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body row g-3">
                        <div class="col-md-6"><label>Nama Sekolah</label><input type="text" name="nama_sekolah" class="form-control" required></div>
                        <div class="col-md-6"><label>NIS</label><input type="text" name="nis" class="form-control" required></div>
                        <div class="col-md-6"><label>Logo</label><input type="file" name="logo" class="form-control" required></div>
                        <div class="col-md-6"><label>Foto Sekolah</label><input type="file" name="foto_sekolah" class="form-control" required></div>
                        <div class="col-md-6"><label>Email</label><input type="email" name="email" class="form-control" required></div>
                        <div class="col-md-6"><label>No HP</label><input type="text" name="no_hp" class="form-control"></div>
                        <div class="col-md-6"><label>Akreditasi</label><input type="text" name="akreditasi" class="form-control"></div>
                        <div class="col-md-6"><label>Maps Link</label><input type="text" name="tautan_google_maps" class="form-control"></div>
                        <div class="col-12"><label>Deskripsi</label><textarea name="deskripsi" class="form-control" rows="3"></textarea></div>
                        <div class="col-12"><label>Alamat</label><textarea name="alamat" class="form-control" rows="2"></textarea></div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success w-100">Simpan Data</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function ajaxDeleteImage(id, type) {
    if (confirm('Apakah Anda yakin ingin menghapus ' + type.replace('_', ' ') + ' ini?')) {
        // Tentukan kontainer mana yang akan diupdate berdasarkan type
        const containerId = type === 'logo' ? `preview-container-logo-${id}` : `preview-container-foto-${id}`;
        const container = document.getElementById(containerId);

        fetch(`/profile-sekolah/delete-image/${id}?type=${type}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Ganti isi kontainer pratinjau menjadi kosong
                container.innerHTML = `<small class="text-muted d-block py-3">Berhasil dihapus. Silakan unggah baru jika diperlukan.</small>`;
                
                // Opsional: Tampilkan notifikasi kecil
                alert(data.message);
            } else {
                alert('Gagal menghapus: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus gambar.');
        });
    }
}
        const sidebar = document.getElementById('sidebar');
        const collapseBtn = document.getElementById('sidebarCollapse');
        const closeBtn = document.getElementById('close-sidebar');
        const overlay = document.getElementById('overlay');

        function toggle() {
            if (window.innerWidth <= 768) { sidebar.classList.toggle('show-mobile'); overlay.classList.toggle('active'); }
            else { sidebar.classList.toggle('inactive'); }
        }
        collapseBtn.onclick = toggle; closeBtn.onclick = toggle; overlay.onclick = toggle;
    </script>
</body>
</html>