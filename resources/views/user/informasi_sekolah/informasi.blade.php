<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Informasi Sekolah</title>
        @include('favicon')

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-green: #198754;
            --dark-green: #146c43;
            --light-bg: #f4f7f6;
        }
        body { font-family: 'Inter', sans-serif; background-color: var(--light-bg); overflow-x: hidden; }
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        #content { width: 100%; padding: 20px 30px; transition: all 0.3s; min-height: 100vh; }
        #sidebarCollapse {
            width: 45px; height: 45px; background: var(--primary-green); border: none;
            color: white; border-radius: 10px; box-shadow: 0 4px 10px rgba(25,135,84,0.2);
            display: flex; align-items: center; justify-content: center; transition: 0.3s;
        }
        #sidebarCollapse:hover { background: var(--dark-green); transform: scale(1.05); }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); background: #fff; }
        .nav-pills .nav-link { color: #555; font-weight: 600; padding: 10px 16px; border-radius: 10px; transition: 0.3s; font-size: 0.85rem; }
        .nav-pills .nav-link.active { background-color: var(--primary-green); box-shadow: 0 4px 10px rgba(25,135,84,0.3); }
        .table thead { background-color: #f8f9fa; }
        .tab-pane.fade:not(.show) { display: none; }
        .img-thumbnail-custom { width: 80px; height: 60px; object-fit: cover; border-radius: 8px; }
        #overlay { display: none; position: fixed; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 1040; top: 0; left: 0; }
        #overlay.active { display: block; }
        input:focus, textarea:focus, select:focus { border-color: #198754 !important; outline: none !important; box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25) !important;}
        .foto-kepala-preview { width: 120px; height: 120px; object-fit: cover; border-radius: 12px; border: 3px solid #dee2e6; }
        @media (max-width: 768px) { #content { padding: 15px; } }
    </style>
</head>
<body>
@php
    $__canCreate = can('kelola_informasi', 'create');
    $__canEdit   = can('kelola_informasi', 'edit');
    $__canDelete = can('kelola_informasi', 'delete');
@endphp


<div id="overlay"></div>
<div class="wrapper">
    @include('user.sidebar')

    <div id="content">
        <div class="container-fluid">

            <div class="d-flex align-items-center justify-content-between mb-4 mt-2">
                <div class="d-flex align-items-center">
                    <button type="button" id="sidebarCollapse" class="btn">
                        <i class="bi bi-list fs-4"></i>
                    </button>
                    <div class="ms-3">
                        <h4 class="mb-0 fw-bold text-success">Kelola Informasi</h4>
                        <p class="text-muted small mb-0">Update konten informasi sekolah Anda</p>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- DROPDOWN NAVIGATION --}}
<div class="card p-2 mb-4 shadow-sm border-0 z-3 position-relative"> <div class="dropdown">
        <button class="btn btn-outline-success dropdown-toggle w-100 fw-bold py-2 d-flex justify-content-between align-items-center" type="button" id="dropdownTabButton" data-bs-toggle="dropdown" aria-expanded="false" data-bs-display="static">
            <span id="dropdownTabContent"><i class="bi bi-camera me-2"></i> Kegiatan Akademik & Ekstrakurikuler</span>
        </button>
        <ul class="dropdown-menu w-100 shadow border-0 mt-1" id="custom-dropdown-tabs">
            <li>
                <button class="dropdown-item active py-2" type="button" data-tab-target="#tab-kegiatan" onclick="switchTab(this, '#tab-kegiatan', 'Kegiatan', 'bi-camera')">
                    <i class="bi bi-camera me-2 text-success"></i> Kegiatan Akademik & Ekstrakurikuler
                </button>
            </li>
            <li>
                <button class="dropdown-item py-2" type="button" data-tab-target="#tab-program" onclick="switchTab(this, '#tab-program', 'Program', 'bi-mortarboard')">
                    <i class="bi bi-mortarboard me-2 text-success"></i> Program Sekolah
                </button>
            </li>
            <li>
                <button class="dropdown-item py-2" type="button" data-tab-target="#tab-studi" onclick="switchTab(this, '#tab-studi', 'Program Studi', 'bi-building')">
                    <i class="bi bi-building me-2 text-success"></i> Program Studi
                </button>
            </li>
            <li>
                <button class="dropdown-item py-2" type="button" data-tab-target="#tab-prestasi" onclick="switchTab(this, '#tab-prestasi', 'Prestasi', 'bi-trophy')">
                    <i class="bi bi-trophy me-2 text-success"></i> Prestasi Akademik & Non-Akademik
                </button>
            </li>
            <li>
                <button class="dropdown-item py-2" type="button" data-tab-target="#tab-artikel" onclick="switchTab(this, '#tab-artikel', 'Artikel', 'bi-newspaper')">
                    <i class="bi bi-newspaper me-2 text-success"></i> Artikel
                </button>
            </li>
            <li>
                <button class="dropdown-item py-2" type="button" data-tab-target="#tab-info" onclick="switchTab(this, '#tab-info', 'Informasi Lainnya', 'bi-info-circle')">
                    <i class="bi bi-info-circle me-2 text-success"></i> Informasi Lainnya
                </button>
            </li>
            <li>
                <button class="dropdown-item py-2" type="button" data-tab-target="#tab-brosur" onclick="switchTab(this, '#tab-brosur', 'Brosur', 'bi-file-earmark-text')">
                    <i class="bi bi-file-earmark-text me-2 text-success"></i> Brosur
                </button>
            </li>
        </ul>
    </div>
</div>

            <div class="tab-content" id="pills-tabContent">

                {{-- TAB KEGIATAN --}}
                <div class="tab-pane fade show active" id="tab-kegiatan">
                    <div class="card p-4">
                        <h5 class="fw-bold mb-3">Tambah Dokumentasi Kegiatan</h5>
                        <form action="{{ route('kegiatan.store') }}" method="POST" enctype="multipart/form-data" class="row g-3 mb-4">
                            @csrf
                            <div class="col-md-4">
                                <label class="form-label">Label Foto</label>
                                <input type="text" name="label_foto" class="form-control" required placeholder="Nama kegiatan">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Foto Kegiatan</label>
                                <input type="file" name="foto_kegiatan" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Deskripsi (Opsional)</label>
                                <input type="text" name="deskripsi_foto" class="form-control" placeholder="Keterangan singkat">
                            </div>
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-success px-4">Simpan Kegiatan</button>
                            </div>
                        </form>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr><th>Foto</th><th>Label</th><th>Aksi</th></tr>
                                </thead>
                                <tbody>
                                    @foreach($kegiatan as $k)
                                    <tr>
                                        <td><img src="{{ \App\Helpers\ImageHelper::url($k->foto_kegiatan) }}" class="img-thumbnail-custom shadow-sm"></td>
                                        <td>{{ $k->label_foto }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-success me-1"
                                                onclick="editKegiatan('{{ $k->id }}', '{{ $k->label_foto }}', '{{ $k->deskripsi_foto }}', '{{ $k->foto_kegiatan }}')">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <form action="{{ route('kegiatan.destroy', $k->id) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus data ini?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- TAB PROGRAM --}}
                <div class="tab-pane fade" id="tab-program">
                    <div class="card p-4">
                        <h5 class="fw-bold mb-3">Tambah Program Sekolah</h5>
                        <form action="{{ route('program.store') }}" method="POST" class="row g-3 mb-4">
                            @csrf
                            <div class="col-md-6">
                                <label class="form-label">Nama Program</label>
                                <input type="text" name="nama_program" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Deskripsi Singkat (Max 150 Karakter)</label>
                                <input type="text" name="deskripsi_program" class="form-control" maxlength="150">
                            </div>
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-success px-4">Simpan Program</button>
                            </div>
                        </form>
                        <hr>
                        <table class="table">
                            <thead><tr><th>Nama Program</th><th>Deskripsi</th><th>Aksi</th></tr></thead>
                            <tbody>
                                @foreach($programs as $p)
                                <tr>
                                    <td class="fw-bold">{{ $p->nama_program }}</td>
                                    <td>{{ $p->deskripsi_program }}</td>
                                    <td>
                                        <a href="{{ route('program.detail', $p->id) }}" class="btn btn-sm btn-outline-primary mb-1" title="Detail Program">
                                            <i class="bi bi-folder2-open"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-success mb-1"
                                            onclick="editProgram('{{ $p->id }}', '{{ $p->nama_program }}', '{{ $p->deskripsi_program }}')">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <form action="{{ route('program.destroy', $p->id) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus program ini?')"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB PRESTASI --}}
                <div class="tab-pane fade" id="tab-prestasi">
                    <div class="card p-4">
                        <h5 class="fw-bold mb-3">Tambah Prestasi Siswa</h5>
                        <form action="{{ route('prestasi.store') }}" method="POST" enctype="multipart/form-data" class="row g-3 mb-4">
                            @csrf
                            <div class="col-md-6">
                                <label class="form-label">Judul Prestasi</label>
                                <input type="text" name="judul_prestasi" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Foto-foto Prestasi</label>
                                <input type="file" name="foto_prestasi" class="form-control" accept="image/*"  multiple required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Deskripsi Prestasi</label>
                                <textarea name="deskripsi_prestasi" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-success px-4">Simpan Prestasi</button>
                            </div>
                        </form>
                        
                        <hr>
                        
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Foto</th>
                                        <th>Judul Prestasi</th>
                                        <th>Deskripsi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($prestasi as $pres)
                                    <tr>
                                        <td>
                                            @if($pres->foto_prestasi && $pres->foto_prestasi !== '-')
                                                <img src="{{ \App\Helpers\ImageHelper::url($pres->foto_prestasi) }}" 
                                                    width="80" height="60" 
                                                    class="rounded object-fit-cover shadow-sm" alt="Foto Prestasi">
                                            @else
                                                <span class="text-muted small">Tidak ada foto</span>
                                            @endif
                                        </td>
                                        <td class="fw-bold">{{ $pres->judul_prestasi }}</td>
                                        <td>{{ Str::limit($pres->deskripsi_prestasi, 50) }}</td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('prestasi.detail', $pres->id) }}" class="btn btn-sm btn-outline-primary" title="Detail Prestasi">
                                                    <i class="bi bi-folder2-open"></i>
                                                </a>
                                                <button class="btn btn-sm btn-outline-success"
                                                        data-id="{{ $pres->id }}"
                                                        data-judul="{{ $pres->judul_prestasi }}"
                                                        data-desc="{{ $pres->deskripsi_prestasi }}"
                                                        data-foto="{{ $pres->foto_prestasi }}"
                                                        onclick="editPrestasi(this)"
                                                        title="Edit Prestasi">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                                <form action="{{ route('prestasi.destroy', $pres->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus prestasi ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Prestasi">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {{-- TAB ARTIKEL --}}
               <div class="tab-pane fade" id="tab-artikel">
                    <div class="card p-4">
                        <h5 class="fw-bold mb-3">Tulis Artikel Baru</h5>
                        <form action="{{ route('artikel.store') }}" method="POST" enctype="multipart/form-data" class="row g-3">
                            @csrf
                            <div class="col-md-8">
                                <label class="form-label">Judul Artikel</label>
                                <input type="text" name="judul_artikel" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Teaser</label>
                                <input type="text" name="teaser" class="form-control">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Isi Artikel</label>
                                <textarea name="deskripsi" class="form-control" rows="5" required></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Foto Artikel</label>
                                <input type="file" name="foto_artikel" class="form-control" accept="image/jpg,image/jpeg,image/png">
                                <small class="text-muted">Format: JPG, JPEG, PNG. Maks 2MB.</small>
                            </div>
                            <div class="col-12 text-center pt-3">
                                <button type="submit" class="btn btn-success btn-lg px-5 shadow">Publikasikan</button>
                            </div>
                        </form>
                        <hr class="my-5">
                        <h5 class="fw-bold mb-3">Daftar Artikel Terpublikasi</h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="150">Thumbnail</th>
                                        <th>Judul & Ringkasan</th>
                                        <th width="150">Tanggal</th>
                                        <th width="100" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($artikels as $art)
                                    <tr>
                                       <td>
                                            @if($art->foto_artikel)
                                                <img src="{{ \App\Helpers\ImageHelper::url($art->foto_artikel) }}" 
                                                    class="img-thumbnail-custom shadow-sm" 
                                                    style="width: 120px; height: 80px; object-fit: cover;">
                                            @else
                                                <div class="bg-secondary text-white d-flex align-items-center justify-content-center rounded" 
                                                    style="width: 120px; height: 80px; font-size: 0.7rem;">Tanpa Foto</div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $art->judul }}</div>
                                            <small class="text-muted d-block">{{ Str::limit($art->teaser, 70) }}</small>
                                           <span class="badge bg-light text-success border mt-1">{{ $art->foto_artikel ? '1' : '0' }} Foto</span>
                                        </td>
                                        <td><small class="text-muted"><i class="bi bi-calendar3 me-1"></i> {{ $art->created_at->format('d M Y') }}</small></td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-outline-success mb-1"
                                                    data-id="{{ $art->id }}"
                                                    data-judul="{{ $art->judul }}"
                                                    data-teaser="{{ $art->teaser }}"
                                                    data-desc="{{ $art->deskripsi }}"
                                                    data-foto="{{ $art->foto_artikel }}"
                                                    onclick="editArtikel(this)">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <form action="{{ route('artikel.destroy', $art->id) }}" method="POST" onsubmit="return confirm('Hapus artikel ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <i class="bi bi-journal-x display-4 d-block mb-2"></i>
                                            Belum ada artikel yang diterbitkan.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- TAB PROGRAM STUDI (BARU) --}}
                <div class="tab-pane fade" id="tab-studi">
                    <div class="card p-4">
                        <h5 class="fw-bold mb-3">Tambah Program Studi</h5>
                        <form action="{{ route('studi.store') }}" method="POST" class="row g-3 mb-4">
                            @csrf
                            <div class="col-md-5">
                                <label class="form-label">Nama Program Studi <span class="text-danger">*</span></label>
                                <input type="text" name="nama_studi" class="form-control" required placeholder="Contoh: Teknik Komputer dan Jaringan">
                            </div>
                            <div class="col-md-7">
                                <label class="form-label">Deskripsi Singkat Program Studi</label>
                                <input type="text" name="deskripsi_studi" class="form-control" placeholder="Deskripsi singkat program studi...">
                            </div>
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-success px-4">
                                    <i class="bi bi-plus-circle me-1"></i> Simpan Program Studi
                                </button>
                            </div>
                        </form>
                        <hr>
                        <h6 class="fw-bold mb-3 text-muted">Daftar Program Studi</h6>
                        @if($studiList->isEmpty())
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-book display-5 d-block mb-2"></i>
                                Belum ada program studi yang ditambahkan.
                            </div>
                        @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40">#</th>
                                        <th>Nama Program Studi</th>
                                        <th>Deskripsi</th>
                                        <th width="120" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($studiList as $i => $s)
                                    <tr>
                                        <td class="text-muted">{{ $i + 1 }}</td>
                                        <td class="fw-bold">{{ $s->nama_studi }}</td>
                                        <td class="text-muted">{{ $s->deskripsi_studi ?? '-' }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('studi.detail', $s->id) }}" class="btn btn-sm btn-outline-primary me-1" title="Detail Program Studi">
                                                <i class="bi bi-folder2-open"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-success me-1"
                                                onclick="editStudi('{{ $s->id }}', '{{ addslashes($s->nama_studi) }}', '{{ addslashes($s->deskripsi_studi) }}')">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <form action="{{ route('studi.destroy', $s->id) }}" method="POST" class="d-inline"
                                                onsubmit="return confirm('Hapus program studi ini?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- TAB INFORMASI LAINNYA (BARU) --}}
                <div class="tab-pane fade" id="tab-info">
                    <div class="card p-4">
                        <h5 class="fw-bold mb-1">Informasi Lainnya</h5>
                        <p class="text-muted small mb-4">Data ini akan tampil di halaman profil sekolah. Jika sudah ada data sebelumnya, form ini akan memperbaruinya.</p>

                        <form action="{{ route('info.sekolah.save') }}" method="POST" enctype="multipart/form-data" class="row g-4">
                            @csrf

                            {{-- Jumlah Guru --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Jumlah Guru <span class="text-muted fw-normal small">(Opsional — jika kosong, dihitung otomatis dari data guru)</span></label>
                                <div class="input-group">
                                    <input type="number" name="jumlah_guru" class="form-control" min="0"
                                        value="{{ $infoSekolah->jumlah_guru ?? '' }}" placeholder="Contoh: 45">
                                </div>
                                <small class="text-muted">Isi jika ingin override angka yang dihitung dari tabel Data Guru.</small>
                            </div>

                            {{-- Jumlah Staff --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Jumlah Staff <span class="text-muted fw-normal small">(Opsional — jika kosong, dihitung otomatis dari data staff)</span></label>
                                <div class="input-group">
                                    <input type="number" name="jumlah_staff" class="form-control" min="0"
                                        value="{{ $infoSekolah->jumlah_staff ?? '' }}" placeholder="Contoh: 12">
                                </div>
                                <small class="text-muted">Isi jika ingin override angka yang dihitung dari tabel Data Staff.</small>
                            </div>

                            {{-- Nama Kepala Sekolah --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nama Kepala Sekolah <span class="text-muted fw-normal small">(Opsional)</span></label>
                                <div class="input-group">
                                    <input type="text" name="nama_kepala_sekolah" class="form-control"
                                        value="{{ $infoSekolah->nama_kepala_sekolah ?? '' }}" placeholder="Nama lengkap kepala sekolah">
                                </div>
                            </div>

                            {{-- Foto Kepala Sekolah --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Foto Kepala Sekolah <span class="text-muted fw-normal">(Opsional)</span></label>
                                <input type="file" name="foto_kepala_sekolah" class="form-control" accept="image/jpg,image/jpeg,image/png"
                                    onchange="previewFotoKepala(event)">
                                <small class="text-muted">Format: JPG, JPEG, PNG. Maks 2MB.</small>
                            </div>

                            {{-- Preview Foto Kepala Sekolah --}}
                            <div class="col-12">
                                @if($infoSekolah && $infoSekolah->foto_kepala_sekolah)
                                <div class="d-flex align-items-center gap-3 p-3 border rounded bg-light">
                                    <img id="preview-kepala" src="{{ \App\Helpers\ImageHelper::url($infoSekolah->foto_kepala_sekolah) }}"
                                        class="foto-kepala-preview shadow-sm" alt="Foto Kepala Sekolah" style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%;">
                                    <div>
                                        <p class="mb-1 fw-semibold">Foto saat ini</p>
                                        <small class="text-muted">Upload foto baru untuk mengganti.</small>
                                    </div>
                                </div>
                                @else
                                <div id="preview-kepala-wrapper" class="d-none">
                                    <div class="d-flex align-items-center gap-3 p-3 border rounded bg-light">
                                        <img id="preview-kepala" src="" class="foto-kepala-preview shadow-sm" alt="Preview" style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%;">
                                        <div><p class="mb-0 text-muted small">Preview foto baru</p></div>
                                    </div>
                                </div>
                                @endif
                            </div>

                            {{-- Fasilitas Sekolah (BARU) --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold">Fasilitas Sekolah <span class="text-muted fw-normal">(Opsional)</span></label>
                                <textarea name="fasilitas" class="form-control" rows="3" placeholder="Contoh: Kantin, Perpustakaan, Lab Komputer, Lapangan Basket">{{ $infoSekolah->fasilitas ?? '' }}</textarea>
                                <small class="text-muted">Pisahkan setiap fasilitas dengan tanda koma (,)</small>
                            </div>

                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-success px-5 py-2 shadow-sm">
                                    <i class="bi bi-save me-2"></i> Simpan Informasi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- TAB BROSUR --}}
                <div class="tab-pane fade" id="tab-brosur">
                    <div class="card p-4">
                        <h5 class="fw-bold mb-1">Upload Brosur</h5>
                        <p class="text-muted small mb-4">Upload file brosur (PDF / gambar). Pengunjung dapat melihat dan mengunduh brosur dari halaman publik.</p>

                        {{-- Form Upload --}}
                        <form action="{{ route('brosur.store') }}" method="POST" enctype="multipart/form-data" class="row g-3 mb-4">
                            @csrf
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Label Brosur <span class="text-danger">*</span></label>
                                <input type="text" name="label" class="form-control" required placeholder="Contoh: Brosur PPDB 2026">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">File Brosur <span class="text-danger">*</span></label>
                                <input type="file" name="file_brosur" id="input-brosur-file" class="form-control" required accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">Format: PDF, JPG, PNG. Maks <strong>10 MB</strong>.</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Deskripsi <span class="text-muted fw-normal">(Opsional)</span></label>
                                <input type="text" name="deskripsi" class="form-control" placeholder="Keterangan singkat brosur">
                            </div>
                            {{-- Preview area --}}
                            <div class="col-12 d-none" id="brosur-preview-area">
                                <div class="border rounded p-3 bg-light d-flex align-items-center gap-3">
                                    <i class="bi bi-file-earmark-check-fill text-success fs-3"></i>
                                    <div>
                                        <div class="fw-semibold" id="brosur-preview-name">—</div>
                                        <small class="text-muted" id="brosur-preview-size">—</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-success px-4">
                                    <i class="bi bi-upload me-1"></i> Upload Brosur
                                </button>
                            </div>
                        </form>

                        <hr>

                        {{-- Daftar Brosur --}}
                        <h6 class="fw-bold mb-3 text-muted">Daftar Brosur</h6>
                        @if($brosurList->isEmpty())
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-file-earmark-x display-5 d-block mb-2"></i>
                                Belum ada brosur yang diupload.
                            </div>
                        @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40">#</th>
                                        <th>Label</th>
                                        <th>Deskripsi</th>
                                        <th width="90">Format</th>
                                        <th width="70" class="text-center">File</th>
                                        <th width="130" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($brosurList as $i => $b)
                                    @php $ext = strtolower(pathinfo($b->path_file, PATHINFO_EXTENSION)); @endphp
                                    <tr>
                                        <td class="text-muted">{{ $i + 1 }}</td>
                                        <td class="fw-bold">{{ $b->label }}</td>
                                        <td class="text-muted">{{ $b->deskripsi ?? '-' }}</td>
                                        <td>
                                            <span class="badge {{ $ext === 'pdf' ? 'bg-danger' : 'bg-info text-dark' }}">
                                                {{ strtoupper($ext) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ Storage::url($b->path_file) }}" target="_blank"
                                               class="btn btn-sm btn-outline-secondary" title="Lihat">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-outline-success me-1"
                                                onclick="editBrosur('{{ $b->id }}', '{{ addslashes($b->label) }}', '{{ addslashes($b->deskripsi) }}')"
                                                title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <form action="{{ route('brosur.destroy', $b->id) }}" method="POST" class="d-inline"
                                                onsubmit="return confirm('Hapus brosur ini?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>

            </div>{{-- end tab-content --}}
        </div>
    </div>
</div>

{{-- MODAL EDIT BROSUR --}}
<div class="modal fade" id="modalEditBrosur" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formEditBrosur" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow">
            @csrf @method('PUT')
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Brosur</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 row g-3">
                <div class="col-12">
                    <label class="form-label fw-bold">Label Brosur <span class="text-danger">*</span></label>
                    <input type="text" name="label" id="edit_brosur_label" class="form-control" required>
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">Ganti File Brosur <span class="text-muted fw-normal">(Opsional)</span></label>
                    <input type="file" name="file_brosur" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                    <small class="text-muted">Kosongkan jika tidak ingin mengganti file. Maks 10 MB.</small>
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">Deskripsi</label>
                    <input type="text" name="deskripsi" id="edit_brosur_deskripsi" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success px-4">
                    <i class="bi bi-save me-1"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT KEGIATAN --}}
<div class="modal fade" id="modalEditKegiatan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formEditKegiatan" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow">
            @csrf @method('PUT')
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold">Edit Kegiatan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 row g-3">
                <div class="col-12">
                    <label class="form-label fw-bold">Label Foto</label>
                    <input type="text" name="label_foto" id="edit_kegiatan_label" class="form-control" required>
                </div>
                
                {{-- Container untuk Preview Foto Lama --}}
                <div class="col-12 d-none" id="container-preview-kegiatan">
                    <label class="form-label fw-bold">Foto Saat Ini</label>
                    <div>
                        <img id="preview-foto-kegiatan" src="" alt="Preview Kegiatan" class="rounded border shadow-sm" style="width: 150px; height: 100px; object-fit: cover;">
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold">Ganti Foto/Biarkan jika tidak ingin ganti</label>
                    <input type="file" name="foto_kegiatan" class="form-control">
                </div>
                
                <div class="col-12">
                    <label class="form-label fw-bold">Deskripsi</label>
                    <input type="text" name="deskripsi_foto" id="edit_kegiatan_desc" class="form-control">
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="submit" class="btn btn-success w-100">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT PROGRAM --}}
<div class="modal fade" id="modalEditProgram" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formEditProgram" method="POST" class="modal-content border-0 shadow">
            @csrf @method('PUT')
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold">Edit Program</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 row g-3">
                <div class="col-12">
                    <label class="form-label fw-bold">Nama Program</label>
                    <input type="text" name="nama_program" id="edit_program_nama" class="form-control" required>
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">Deskripsi</label>
                    <input type="text" name="deskripsi_program" id="edit_program_desc" class="form-control">
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="submit" class="btn btn-success w-100">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT PRESTASI --}}
<div class="modal fade" id="modalEditPrestasi" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formEditPrestasi" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow">
            @csrf @method('PUT')
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold">Edit Prestasi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 row g-3">
                <div class="col-12">
                    <label class="form-label fw-bold">Judul Prestasi</label>
                    <input type="text" name="judul_prestasi" id="edit_prestasi_judul" class="form-control" required>
                </div>
                
                {{-- Container untuk Preview Foto Lama --}}
                <div class="col-12 d-none" id="container-preview-prestasi">
                    <label class="form-label fw-bold">Foto Saat Ini</label>
                    <div>
                        <img id="preview-foto-prestasi" src="" alt="Preview Prestasi" class="rounded border shadow-sm" style="width: 150px; height: 100px; object-fit: cover;">
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold">Ganti Foto/Biarkan jika tidak ingin ganti)</label>
                    <input type="file" name="foto_prestasi" class="form-control">
                </div>
                
                <div class="col-12">
                    <label class="form-label fw-bold">Deskripsi</label>
                    <textarea name="deskripsi_prestasi" id="edit_prestasi_desc" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="submit" class="btn btn-success w-100 py-2 shadow-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT PROGRAM STUDI (BARU) --}}
<div class="modal fade" id="modalEditStudi" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formEditStudi" method="POST" class="modal-content border-0 shadow">
            @csrf @method('PUT')
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-book me-2"></i>Edit Program Studi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 row g-3">
                <div class="col-12">
                    <label class="form-label fw-bold">Nama Program Studi</label>
                    <input type="text" name="nama_studi" id="edit_studi_nama" class="form-control" required>
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">Deskripsi Singkat</label>
                    <textarea name="deskripsi_studi" id="edit_studi_desc" class="form-control" rows="3"
                        placeholder="Deskripsi singkat program studi..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="submit" class="btn btn-success w-100">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT ARTIKEL --}}
<div class="modal fade" id="modalEditArtikel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="formEditArtikel" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow">
            @csrf @method('PUT')
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold">Edit Artikel</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-bold">Judul Artikel</label>
                    <input type="text" name="judul_artikel" id="edit_artikel_judul" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Teaser</label>
                    <input type="text" name="teaser" id="edit_artikel_teaser" class="form-control">
                </div>
                
                {{-- Container untuk Preview Foto Lama --}}
                <div class="col-12 d-none" id="container-preview-artikel">
                    <label class="form-label fw-bold">Foto Saat Ini</label>
                    <div>
                        <img id="preview-foto-artikel" src="" alt="Preview Artikel" class="rounded border shadow-sm" style="width: 150px; height: 100px; object-fit: cover;">
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold">Ganti Foto/Biarkan jika tidak ingin ganti</label>
                    <input type="file" name="foto_artikel" class="form-control" accept="image/jpg,image/jpeg,image/png">
                    <small class="text-muted">Format: JPG, JPEG, PNG. Maks 2MB.</small>
                </div>
                
                <div class="col-12">
                    <label class="form-label fw-bold">Isi Artikel</label>
                    <textarea name="deskripsi" id="edit_artikel_desc" class="form-control" rows="5" required></textarea>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="submit" class="btn btn-success w-100 py-2 shadow-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // 1. Set Tab Aktif dari LocalStorage
    const activeTabTarget = localStorage.getItem('activeTab') || '#tab-kegiatan';
    const matchingItem = document.querySelector(`button[data-tab-target="${activeTabTarget}"]`);
    if (matchingItem) {
        matchingItem.click(); // Memicu fungsi switchTab() secara otomatis
    }

    // 2. Logika Sidebar Toggle
    const sidebar = document.getElementById('sidebar');
    const collapseBtn = document.getElementById('sidebarCollapse');
    const closeBtn = document.getElementById('close-sidebar'); // Berada di file include
    const overlay = document.getElementById('overlay');

    function toggleSidebar() {
        if (window.innerWidth <= 768) {
            // Tambahkan pengecekan if (sidebar) untuk mencegah error jika elemen belum dimuat
            if (sidebar) sidebar.classList.toggle('show-mobile');
            if (overlay) overlay.classList.toggle('active');
        } else {
            if (sidebar) sidebar.classList.toggle('inactive');
        }
    }

    if (collapseBtn) collapseBtn.addEventListener('click', toggleSidebar);
    if (closeBtn) closeBtn.addEventListener('click', toggleSidebar);
    if (overlay) overlay.addEventListener('click', toggleSidebar);
});

// ===== ARTIKEL =====
function addPhotoRow() {
    const wrapper = document.getElementById('artikel-photo-wrapper');
    const div = document.createElement('div');
    div.className = 'row g-2 mb-2 align-items-center';
    div.innerHTML = `
        <div class="col-md-5"><input type="file" name="foto_artikel[]" class="form-control"></div>
        <div class="col-md-5"><input type="text" name="sumber_foto[]" class="form-control" placeholder="Sumber Foto"></div>
        <div class="col-md-2"><button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="this.closest('.row').remove()"><i class="bi bi-trash"></i></button></div>
    `;
    wrapper.appendChild(div);
}

// ===== ARTIKEL =====
function editArtikel(btn) {
    // 1. Ambil data dari atribut tombol
    const id = btn.getAttribute('data-id');
    const judul = btn.getAttribute('data-judul');
    const teaser = btn.getAttribute('data-teaser');
    const desc = btn.getAttribute('data-desc');
    const foto = btn.getAttribute('data-foto');

    // 2. Isi ke dalam form modal
    document.getElementById('formEditArtikel').action = `/informasi/artikel/${id}`;
    document.getElementById('edit_artikel_judul').value = judul;
    document.getElementById('edit_artikel_teaser').value = teaser;
    document.getElementById('edit_artikel_desc').value = desc;

    // 3. Logika Preview Foto Lama
    const containerPreview = document.getElementById('container-preview-artikel');
    const imgPreview = document.getElementById('preview-foto-artikel');

    if (foto) {
        containerPreview.classList.remove('d-none');
        // Sesuaikan path jika perlu (misal: imgPreview.src = `/assets/${foto}`)
        imgPreview.src = `/${foto}`; 
    } else {
        containerPreview.classList.add('d-none');
        imgPreview.src = '';
    }

    // 4. Tampilkan Modal
    new bootstrap.Modal(document.getElementById('modalEditArtikel')).show();
}

function ajaxHapusFotoArtikel(fotoId) {
    if (confirm('Hapus foto ini dari server?')) {
        fetch(`/informasi/artikel/foto/${fotoId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        }).then(r => r.json()).then(() => {
            const wrapper = document.getElementById(`wrapper-foto-art-${fotoId}`);
            if (wrapper) wrapper.remove();
        });
    }
}

function cancelEditArtikel() { location.reload(); }

// ===== KEGIATAN =====
function editKegiatan(id, label, desc, foto) {
    document.getElementById('formEditKegiatan').action = `/informasi/kegiatan/${id}`;
    document.getElementById('edit_kegiatan_label').value = label;
    document.getElementById('edit_kegiatan_desc').value = desc;

    // Logika Preview Foto Lama
    const containerPreview = document.getElementById('container-preview-kegiatan');
    const imgPreview = document.getElementById('preview-foto-kegiatan');

    if (foto) {
        containerPreview.classList.remove('d-none');
        imgPreview.src = `/${foto}`;
    } else {
        containerPreview.classList.add('d-none');
        imgPreview.src = '';
    }

    new bootstrap.Modal(document.getElementById('modalEditKegiatan')).show();
}

// ===== PROGRAM =====
function editProgram(id, nama, desc) {
    document.getElementById('formEditProgram').action = `/informasi/program/${id}`;
    document.getElementById('edit_program_nama').value = nama;
    document.getElementById('edit_program_desc').value = desc;
    new bootstrap.Modal(document.getElementById('modalEditProgram')).show();
}

// ===== PRESTASI =====
function editPrestasi(btn) {
    // 1. Ambil data dari atribut tombol
    const id = btn.getAttribute('data-id');
    const judul = btn.getAttribute('data-judul');
    const desc = btn.getAttribute('data-desc');
    const foto = btn.getAttribute('data-foto'); // Langsung berupa string nama file / path

    // 2. Isi ke dalam form modal
    document.getElementById('formEditPrestasi').action = `/informasi/prestasi/${id}`;
    document.getElementById('edit_prestasi_judul').value = judul;
    document.getElementById('edit_prestasi_desc').value = desc;

    // 3. Logika Preview Foto Lama
    const containerPreview = document.getElementById('container-preview-prestasi');
    const imgPreview = document.getElementById('preview-foto-prestasi');

    if (foto) {
        containerPreview.classList.remove('d-none');
        imgPreview.src = `/${foto}`; 
    } else {
        containerPreview.classList.add('d-none');
        imgPreview.src = '';
    }

    // 4. Tampilkan Modal
    new bootstrap.Modal(document.getElementById('modalEditPrestasi')).show();
}

// ===== PROGRAM STUDI (BARU) =====
function editStudi(id, nama, desc) {
    document.getElementById('formEditStudi').action = `/informasi/studi/${id}`;
    document.getElementById('edit_studi_nama').value = nama;
    document.getElementById('edit_studi_desc').value = desc;
    new bootstrap.Modal(document.getElementById('modalEditStudi')).show();
}

// ===== PREVIEW FOTO KEPALA SEKOLAH (BARU) =====
function previewFotoKepala(event) {
    const file = event.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function (e) {
        const existing = document.getElementById('preview-kepala');
        if (existing) {
            existing.src = e.target.result;
        }
        const wrapper = document.getElementById('preview-kepala-wrapper');
        if (wrapper) {
            wrapper.classList.remove('d-none');
            document.getElementById('preview-kepala').src = e.target.result;
        }
    };
    reader.readAsDataURL(file);
}

// --- LOGIKA UPDATE TEKS DROPDOWN ---
function switchTab(element, targetTabId, text, icon) {
    const content = document.getElementById('dropdownTabContent');
    if (content) {
        content.innerHTML = `<i class="bi ${icon} me-2"></i> ${text}`;
    }
    document.querySelectorAll('#custom-dropdown-tabs .dropdown-item').forEach(item => {
        item.classList.remove('active');
    });
    element.classList.add('active');
    document.querySelectorAll('.tab-content .tab-pane').forEach(pane => {
        pane.classList.remove('show', 'active');
    });
    
    const targetPane = document.querySelector(targetTabId);
    if (targetPane) {
        targetPane.classList.add('show', 'active');
    }
    
    // Simpan posisi tab saat ini agar tidak tereset saat reload
    localStorage.setItem('activeTab', targetTabId);
}

// --- VALIDASI UKURAN GAMBAR (MAKS 2MB) SECARA GLOBAL, KECUALI BROSUR (10MB) ---
document.addEventListener('change', function(event) {
    if (event.target.type === 'file') {
        const files = event.target.files;
        const isBrosur = event.target.name === 'file_brosur';
        const maxSize  = isBrosur ? 10 * 1024 * 1024 : 2 * 1024 * 1024;
        const label    = isBrosur ? '10MB' : '2MB';

        for (let i = 0; i < files.length; i++) {
            if (files[i].size > maxSize) {
                alert(`Ukuran file "${files[i].name}" terlalu besar!\nMaksimal yang diperbolehkan adalah ${label}.`);
                event.target.value = '';
                return;
            }
        }

        // Preview info file brosur
        if (isBrosur && files.length > 0) {
            const area = document.getElementById('brosur-preview-area');
            const name = document.getElementById('brosur-preview-name');
            const size = document.getElementById('brosur-preview-size');
            if (area && name && size) {
                area.classList.remove('d-none');
                name.textContent = files[0].name;
                size.textContent = (files[0].size / 1024).toFixed(1) + ' KB';
            }
        }
    }
});

// --- EDIT BROSUR ---
function editBrosur(id, label, deskripsi) {
    document.getElementById('edit_brosur_label').value     = label;
    document.getElementById('edit_brosur_deskripsi').value = deskripsi || '';
    document.getElementById('formEditBrosur').action       = `/informasi/brosur/${id}`;
    new bootstrap.Modal(document.getElementById('modalEditBrosur')).show();
}
</script>
</body>
</html>