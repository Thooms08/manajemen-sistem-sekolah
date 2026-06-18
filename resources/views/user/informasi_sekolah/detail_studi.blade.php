<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detail Prodi: {{ $studi->nama_studi }}</title>
    @if(isset($sekolah->logo))
    <link rel="icon" type="image/png" href="{{ \App\Helpers\ImageHelper::url($sekolah->logo) }}">
    @else
    <link rel="icon" type="image/png" href="{{ asset('assets/img/default-favicon.png') }}">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
    <style>
        :root { --primary: #198754; }
        body { font-family: 'Inter', sans-serif; background: #f4f7f6; }
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        #content { width: 100%; padding: 24px 30px; min-height: 100vh; transition: all 0.3s; }
        #sidebarCollapse { width: 45px; height: 45px; background: var(--primary); border: none; color: white; border-radius: 10px; }
        /* Section card */
        .sec { background: white; border-radius: 14px; box-shadow: 0 4px 18px rgba(0,0,0,.05); margin-bottom: 1.5rem; overflow: hidden; }
        .sec-head { padding: 14px 20px; border-bottom: 1px solid #f0f0f0; display: flex; align-items: center; justify-content: space-between; background: #fafafa; }
        .sec-head h6 { margin: 0; font-weight: 700; font-size: .93rem; }
        .sec-body { padding: 20px; }
        /* Kepala cards */
        .kepala-card { display: flex; align-items: center; gap: 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 12px 16px; margin-bottom: 10px; transition: .2s; }
        .kepala-card:hover { border-color: var(--primary); box-shadow: 0 2px 10px rgba(25,135,84,.08); }
        .kepala-avatar { width: 42px; height: 42px; border-radius: 50%; background: #e8f5e9; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .kepala-jabatan { font-size: .76rem; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: .4px; }
        .kepala-nama { font-size: .9rem; color: #222; }
        /* Kelas cards */
        .kelas-card { display: flex; align-items: center; gap: 12px; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 12px 16px; margin-bottom: 10px; transition: .2s; }
        .kelas-card:hover { border-color: #0dcaf0; }
        .kelas-icon { width: 38px; height: 38px; background: #e0f7fa; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        /* Catatan */
        .catatan-card { border: 1px solid #e9ecef; border-left: 4px solid var(--primary); border-radius: 8px; padding: 14px 18px; margin-bottom: 12px; background: #fff; }
        .catatan-judul { font-weight: 700; font-size: .9rem; margin-bottom: 6px; }
        .catatan-isi { font-size: .86rem; color: #555; white-space: pre-wrap; line-height: 1.6; }
        .catatan-meta { font-size: .76rem; color: #aaa; margin-top: 8px; }
        #isi_catatan, #edit_isi_catatan { font-family: 'Courier New', monospace; font-size: .88rem; min-height: 180px; resize: vertical; background: #fdfdfd; }
        input:focus, textarea:focus, select:focus { border-color: #198754 !important; box-shadow: 0 0 0 .2rem rgba(25,135,84,.25) !important; outline: none !important; }
        #overlay { display: none; position: fixed; width: 100vw; height: 100vh; background: rgba(0,0,0,.5); z-index: 1040; top: 0; left: 0; }
        #overlay.active { display: block; }
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

            {{-- Header --}}
            <div class="d-flex align-items-center justify-content-between mb-4 mt-1 flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <button id="sidebarCollapse" class="btn"><i class="bi bi-list fs-4"></i></button>
                    <div>
                        <a href="{{ route('informasi.index') }}" class="text-muted text-decoration-none small">
                            <i class="bi bi-arrow-left-circle me-1"></i>Kembali ke Informasi
                        </a>
                        <h4 class="mb-0 fw-bold text-success mt-1">{{ $studi->nama_studi }}</h4>
                        <p class="text-muted small mb-0">{{ $studi->deskripsi_studi ?? 'Program Studi Sekolah' }}</p>
                    </div>
                </div>
                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2" style="font-size:.85rem;">
                    <i class="bi bi-building me-1"></i>Program Studi
                </span>
            </div>

            {{-- Alert --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row g-4">

                {{-- KOLOM KIRI: Kepala Prodi --}}
                <div class="col-lg-6">
                    <div class="sec">
                        <div class="sec-head">
                            <h6><i class="bi bi-person-badge-fill me-2 text-success"></i>Kepala Program Studi</h6>
                            @if($__canCreate)<button class="btn btn-success btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalTambahKepala">
                                <i class="bi bi-plus-lg me-1"></i>Tambah
                            </button>@endif
                        </div>
                        <div class="sec-body">
                            @forelse($studi->kepalas as $kp)
                                <div class="kepala-card">
                                    <div class="kepala-avatar">
                                        <i class="bi {{ $kp->tipe === 'guru' ? 'bi-person-badge' : 'bi-person-workspace' }} text-success fs-5"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="kepala-jabatan">{{ $kp->jabatan }}</div>
                                        <div class="kepala-nama">{{ $kp->nama_kepala }}</div>
                                        <span class="badge {{ $kp->tipe === 'guru' ? 'bg-primary' : 'bg-warning text-dark' }} mt-1" style="font-size:.72rem;">
                                            {{ ucfirst($kp->tipe) }}
                                        </span>
                                    </div>
                                    <form action="{{ route('studi.kepala.destroy', [$studi->id, $kp->id]) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Hapus kepala prodi ini?')" title="Hapus">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            @empty
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-person-x fs-3 d-block mb-2"></i>
                                    Belum ada kepala program studi.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- KOLOM KANAN: Kelas --}}
                <div class="col-lg-6">
                    <div class="sec">
                        <div class="sec-head">
                            <h6>
                                <i class="bi bi-door-open-fill me-2 text-success"></i>Kelas dalam Program Studi
                                <span class="badge bg-success bg-opacity-10 text-success ms-1">{{ $studi->kelass->count() }}</span>
                            </h6>
                            @if($__canCreate)<button class="btn btn-success btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalTambahKelas">
                                <i class="bi bi-plus-lg me-1"></i>Tambah Kelas
                            </button>@endif
                        </div>
                        <div class="sec-body">
                            @forelse($studi->kelass as $sk)
                                <div class="kelas-card">
                                    <div class="kelas-icon">
                                        <i class="bi bi-mortarboard text-info fs-5"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold">{{ $sk->nama_kelas }}</div>
                                        <small class="text-muted">
                                            {{ $sk->kelas->murid->count() ?? 0 }} murid terdaftar
                                        </small>
                                    </div>
                                    <form action="{{ route('studi.kelas.destroy', [$studi->id, $sk->id]) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Lepaskan kelas ini dari program studi?')" title="Lepas">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            @empty
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-door-closed fs-3 d-block mb-2"></i>
                                    Belum ada kelas yang terdaftar.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>{{-- /row --}}

            {{-- CATATAN (full width) --}}
            <div class="sec">
                <div class="sec-head">
                    <h6>
                        <i class="bi bi-journal-text me-2 text-success"></i>Catatan Program Studi
                        <span class="text-muted fw-normal" style="font-size:.78rem; margin-left:6px;">Uji kompetensi, jadwal bimbingan, dll.</span>
                    </h6>
                    @if($__canCreate)<button class="btn btn-success btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalTambahCatatan">
                        <i class="bi bi-plus-lg me-1"></i>Tambah Catatan
                    </button>@endif
                </div>
                <div class="sec-body">
                    @forelse($studi->catatans as $ct)
                        <div class="catatan-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="catatan-judul">{{ $ct->judul }}</div>
                                <div class="d-flex gap-1 ms-3 flex-shrink-0">
                                    @if($__canEdit)<button class="btn btn-sm btn-outline-success border-0" onclick="openEditCatatan({{ $ct->id }})" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>@endif
                                    <form action="{{ route('studi.catatan.destroy', [$studi->id, $ct->id]) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Hapus catatan ini?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="catatan-isi">{{ $ct->isi }}</div>
                            <div class="catatan-meta"><i class="bi bi-clock me-1"></i>{{ $ct->updated_at->diffForHumans() }}</div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-journal-x fs-2 d-block mb-2"></i>
                            Belum ada catatan. Tambahkan jadwal uji kompetensi, bimbingan, atau informasi lainnya.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>

{{-- MODAL TAMBAH KEPALA --}}
<div class="modal fade" id="modalTambahKepala" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('studi.kepala.store', $studi->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-badge me-2"></i>Tambah Kepala Program Studi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Sumber <span class="text-danger">*</span></label>
                        <select name="tipe" id="tipe_kepala" class="form-select" required onchange="loadSumberKepala(this.value)">
                            <option value="">-- Pilih Tipe --</option>
                            <option value="guru">Guru</option>
                            <option value="staff">Staff</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Nama <span class="text-danger">*</span></label>
                        <select name="id_sumber" id="id_sumber_kepala" class="form-select select2-kepala" required disabled>
                            <option value="">-- Pilih Tipe Dulu --</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Jabatan <span class="text-muted fw-normal">(opsional)</span></label>
                        <input type="text" name="jabatan" class="form-control" value="Kepala Program Studi" maxlength="100" placeholder="Kepala Program Studi, Wakil Ketua, ...">
                    </div>
                    <div class="col-12">
                        <div class="alert alert-info py-2 mb-0" style="font-size:.82rem;">
                            <i class="bi bi-info-circle me-1"></i>
                            Satu guru/staff dapat menjadi kepala di lebih dari satu program studi.
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH KELAS --}}
<div class="modal fade" id="modalTambahKelas" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('studi.kelas.store', $studi->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-door-open me-2"></i>Tambah Kelas</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <label class="form-label fw-semibold small">Pilih Kelas <span class="text-danger">*</span>
                        <span class="text-muted fw-normal">(bisa pilih banyak)</span>
                    </label>
                    <select name="id_kelases[]" class="form-select select2-kelas" multiple required>
                        @foreach($kelasList as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                    <div class="form-text mt-2">
                        <i class="bi bi-info-circle me-1"></i>
                        Hanya kelas yang belum masuk program studi lain yang ditampilkan.
                        1 kelas hanya dapat masuk 1 program studi.
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4">Tambahkan Kelas</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH CATATAN --}}
<div class="modal fade" id="modalTambahCatatan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('studi.catatan.store', $studi->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-journal-plus me-2"></i>Tambah Catatan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Judul <span class="text-danger">*</span></label>
                        <input type="text" name="judul" class="form-control" placeholder="Jadwal Uji Kompetensi, Bimbingan Karir, ..." maxlength="255" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Isi Catatan <span class="text-danger">*</span></label>
                        <textarea name="isi" id="isi_catatan" class="form-control" rows="9"
                            placeholder="Tulis catatan di sini...&#10;&#10;Contoh:&#10;- Uji Kompetensi Teori: 10 Juli 2026&#10;- Uji Kompetensi Praktik: 15 Juli 2026&#10;- Bimbingan Karir: Setiap Rabu 13.00-14.00" required></textarea>
                        <div class="form-text"><i class="bi bi-keyboard me-1"></i>Teks bebas. Tekan Enter untuk baris baru.</div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4"><i class="bi bi-save me-1"></i>Simpan Catatan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL EDIT CATATAN --}}
<div class="modal fade" id="modalEditCatatan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <form id="formEditCatatan" method="POST">
                @csrf @method('PUT')
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Catatan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Judul <span class="text-danger">*</span></label>
                        <input type="text" name="judul" id="edit_judul_catatan" class="form-control" required maxlength="255">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Isi Catatan <span class="text-danger">*</span></label>
                        <textarea name="isi" id="edit_isi_catatan" class="form-control" rows="9" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4"><i class="bi bi-save me-1"></i>Perbarui</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function () {
    // Sidebar
    function toggleSidebar() {
        if ($(window).width() <= 768) {
            $('#sidebar').toggleClass('show-mobile');
            $('#overlay').toggleClass('active');
        } else {
            $('#sidebar').toggleClass('inactive');
        }
    }
    $('#sidebarCollapse, #close-sidebar, #overlay').on('click', toggleSidebar);

    // Select2 kelas (multiple)
    $('.select2-kelas').select2({
        theme: 'bootstrap-5', width: '100%',
        dropdownParent: $('#modalTambahKelas'),
        placeholder: 'Cari dan pilih kelas...',
    });

    // Select2 kepala
    $('#id_sumber_kepala').select2({
        theme: 'bootstrap-5', width: '100%',
        dropdownParent: $('#modalTambahKepala'),
        placeholder: '-- Pilih Tipe Dulu --',
    });

    // Reset modal kepala saat ditutup
    $('#modalTambahKepala').on('hidden.bs.modal', function () {
        $('#tipe_kepala').val('');
        $('#id_sumber_kepala').empty().append('<option value="">-- Pilih Tipe Dulu --</option>')
            .prop('disabled', true).trigger('change.select2');
    });
});

// Load sumber kepala berdasarkan tipe (AJAX)
function loadSumberKepala(tipe) {
    const $sel = $('#id_sumber_kepala');
    $sel.empty().append('<option value="">Memuat...</option>').prop('disabled', true);
    if (!tipe) { $sel.empty().append('<option value="">-- Pilih Tipe Dulu --</option>'); return; }

    $.get("{{ route('studi.sumber-by-tipe') }}", { tipe }, function (data) {
        $sel.empty().append('<option value="">-- Pilih --</option>');
        data.forEach(d => $sel.append(`<option value="${d.id}">${d.nama}</option>`));
        $sel.prop('disabled', false).trigger('change.select2');
    }).fail(() => {
        $sel.empty().append('<option value="">Gagal memuat</option>');
    });
}

// Open edit catatan modal
function openEditCatatan(catatanId) {
    const $form = $('#formEditCatatan');
    $form.attr('action', `/informasi/studi/{{ $studi->id }}/catatan/${catatanId}`);
    $('#edit_judul_catatan').val('');
    $('#edit_isi_catatan').val('');
    $('#modalEditCatatan').modal('show');

    $.get(`{{ url("informasi/studi/{$studi->id}/catatan") }}/${catatanId}`, function (d) {
        $('#edit_judul_catatan').val(d.judul);
        $('#edit_isi_catatan').val(d.isi);
    });
}
</script>
</body>
</html>
