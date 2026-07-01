<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Catatan Saya</title>
    @if(isset($sekolah->logo))
    <link rel="icon" type="image/png" href="{{ \App\Helpers\ImageHelper::url($sekolah->logo) }}">
    @else
    <link rel="icon" type="image/png" href="{{ asset('assets/img/default-favicon.png') }}">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --green-primary:#198754; --green-dark:#0f5132; --green-light:#f0faf5;
            --border:#e2ebe6; --surface:#ffffff; --text-main:#1a2e25; --text-muted:#6c8f7d;
            --shadow-sm:0 2px 8px rgba(25,135,84,.08); --shadow-md:0 6px 24px rgba(25,135,84,.12);
            --radius:14px;
        }
        body { font-family:'Inter',sans-serif; background:#f3f7f5; color:var(--text-main); margin:0; }
        .topbar {
            background:linear-gradient(135deg,#0f5132,#198754);
            padding:0 28px; height:64px; display:flex; align-items:center;
            justify-content:space-between; position:sticky; top:0; z-index:100;
            box-shadow:0 2px 12px rgba(15,81,50,.25);
        }
        .topbar-brand { color:#fff; font-weight:800; font-size:1.05rem; display:flex; align-items:center; gap:10px; text-decoration:none; }
        .user-pill {
            background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.25);
            border-radius:50px; padding:6px 14px; color:#fff; font-size:.82rem; font-weight:600;
            display:flex; align-items:center; gap:7px;
        }
        .btn-logout-top {
            background:rgba(255,77,77,.15); border:1px solid rgba(255,77,77,.3);
            color:#ffaaaa; border-radius:8px; padding:6px 14px; font-size:.82rem;
            font-weight:600; cursor:pointer; transition:.2s; text-decoration:none;
            display:flex; align-items:center; gap:6px;
        }
        .btn-logout-top:hover { background:rgba(255,77,77,.3); color:#fff; }
        .main-content { max-width:1100px; margin:0 auto; padding:28px 24px; }
        .card-panel { background:var(--surface); border:1.5px solid var(--border); border-radius:var(--radius); box-shadow:var(--shadow-sm); }
        .catatan-card {
            background:var(--surface); border:1.5px solid var(--border); border-radius:var(--radius);
            padding:20px 22px; transition:box-shadow .2s, border-color .2s; height:100%;
        }
        .catatan-card:hover { box-shadow:var(--shadow-sm); border-color:#b5d5c5; }
        .label-badge {
            display:inline-flex; align-items:center; gap:5px; background:var(--green-light);
            color:var(--green-primary); font-size:.75rem; font-weight:700; padding:4px 12px;
            border-radius:50px; border:1px solid #c3e6cb;
        }
        .catatan-isi { font-size:.88rem; color:#444; line-height:1.65; white-space:pre-wrap; word-break:break-word; }
        .catatan-time { font-size:.73rem; color:var(--text-muted); }
        .btn-aksi { background:transparent; border:none; padding:4px 8px; border-radius:6px; cursor:pointer; transition:background .15s; }
        .btn-aksi:hover { background:var(--green-light); }
        .filter-bar { background:var(--surface); border:1px solid var(--border); border-radius:10px; padding:14px 20px; }
        .form-control:focus,.form-select:focus { border-color:var(--green-primary)!important; box-shadow:0 0 0 .2rem rgba(25,135,84,.2)!important; }
        .empty-state { text-align:center; padding:60px 20px; color:var(--text-muted); }
        .empty-state i { font-size:3.5rem; opacity:.35; margin-bottom:12px; display:block; }

        /* --- RESPONSIVE MOBILE LOGIC --- */
        @media (max-width: 768px) {
            .main-content { padding: 16px; }
            .topbar { padding: 0 14px; height: 56px; }
            .topbar-brand span { display: none; }
            .user-pill { padding: 5px 10px; font-size: .75rem; }
            .user-pill i { font-size: 1rem; }
            .btn-logout-top { padding: 5px 10px; }
            .btn-logout-top i { font-size: 1rem; }
            .catatan-card { padding: 14px 16px; }
            .filter-bar { padding: 12px 14px; }
            .empty-state { padding: 40px 16px; }
            .empty-state i { font-size: 2.8rem; }
            .modal-body { padding: 1rem !important; }
            .modal-footer { padding: .75rem 1rem !important; }
        }
        @media (max-width: 576px) {
            .topbar { padding: 0 12px; }
            .topbar-brand { font-size: .95rem; }
            .catatan-card { padding: 12px 14px; }
            .catatan-isi { font-size: .84rem; line-height: 1.55; }
            .label-badge { font-size: .7rem; padding: 3px 10px; }
            .btn-aksi { padding: 3px 6px; }
            .btn-aksi i { font-size: .9rem; }
            .filter-bar { padding: 10px 12px; }
            .filter-btn { font-size: .78rem; padding: 4px 10px; }
            .empty-state { padding: 32px 12px; }
            .empty-state i { font-size: 2.4rem; }
        }
    </style>
</head>
<body>

<nav class="topbar">
    <a href="{{ route('user.dashboard') }}" class="topbar-brand">
        <i class="bi bi-arrow-left-circle"></i><span>Kembali ke Dashboard</span>
    </a>
    <div class="d-flex align-items-center gap-3">
        <div class="user-pill">
            <i class="bi bi-person-circle"></i>{{ auth()->user()->username }}
        </div>
        <form action="{{ route('logout') }}" method="POST" id="logout-form-catatan">
            @csrf
            <button type="button" class="btn-logout-top" onclick="confirmLogout()">
                <i class="bi bi-box-arrow-right"></i>
            </button>
        </form>
    </div>
</nav>

<div class="main-content">

    {{-- Header --}}
    <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center justify-content-between mb-4 gap-3">
        <div>
            <h5 class="mb-0 fw-bold"><i class="bi bi-journal-text text-success me-2"></i>Catatan Saya</h5>
            <small class="text-muted">Kelola catatan pribadi Anda</small>
        </div>
        <button class="btn btn-success px-4 fw-bold shadow-sm w-100 w-sm-auto align-self-start align-self-sm-center"
            data-bs-toggle="modal" data-bs-target="#modalTambahCatatan">
            <i class="bi bi-plus-circle me-2"></i>Tambah Catatan
        </button>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4">
        <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Gagal:</div>
        <ul class="mb-0 small ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Filter --}}
    <div class="filter-bar mb-4 d-flex flex-column flex-md-row align-items-stretch align-items-md-center gap-3">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <i class="bi bi-filter text-muted"></i>
            <button class="btn btn-sm btn-success px-3 filter-btn active" data-filter="semua">Semua</button>
            @foreach($catatans->pluck('label')->unique() as $lbl)
            <button class="btn btn-sm btn-outline-success px-3 filter-btn" data-filter="{{ $lbl }}">{{ $lbl }}</button>
            @endforeach
        </div>
        <span class="ms-md-auto text-muted" style="font-size:.82rem;">{{ $catatans->count() }} catatan</span>
    </div>

    {{-- Grid --}}
    @if($catatans->count() > 0)
    <div class="row g-3" id="catatanGrid">
        @foreach($catatans as $c)
        <div class="col-md-6 col-lg-4 catatan-item" data-label="{{ $c->label }}">
            <div class="catatan-card">
                <div class="d-flex align-items-start justify-content-between mb-2 gap-2">
                    <span class="label-badge"><i class="bi bi-tag-fill"></i>{{ $c->label }}</span>
                    <div class="d-flex gap-1">
                        <button class="btn-aksi text-success" title="Edit"
                            onclick="openEdit('{{ $c->uuid }}','{{ addslashes($c->label) }}','{{ addslashes($c->catatan) }}')">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <form action="{{ route('user.catatan.destroy', $c->uuid) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Hapus catatan ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-aksi text-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </div>
                <p class="catatan-isi mb-3">{{ $c->catatan }}</p>
                <div class="catatan-time">
                    <i class="bi bi-clock me-1"></i>{{ $c->created_at->isoFormat('D MMM YYYY, HH:mm') }}
                    @if($c->updated_at->ne($c->created_at))
                    <span class="ms-2 text-warning"><i class="bi bi-pencil-fill"></i> diedit</span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="empty-state">
        <i class="bi bi-journal-plus"></i>
        <div class="fw-bold mb-2">Belum ada catatan</div>
        <button class="btn btn-success px-4" data-bs-toggle="modal" data-bs-target="#modalTambahCatatan">
            <i class="bi bi-plus-circle me-2"></i>Buat Catatan Pertama
        </button>
    </div>
    @endif
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="modalTambahCatatan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('user.catatan.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-journal-plus me-2"></i>Tambah Catatan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Label / Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="label" class="form-control" required maxlength="100"
                            placeholder="Contoh: Perencanaan Kegiatan, Rapat, Tugas, Ide..."
                            list="labelSuggest">
                        <datalist id="labelSuggest">
                            <option value="Perencanaan Kegiatan">
                            <option value="Rapat"><option value="Tugas">
                            <option value="Ide"><option value="Pengumuman">
                            <option value="Catatan Pribadi"><option value="Laporan">
                        </datalist>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Isi Catatan <span class="text-danger">*</span></label>
                        <textarea name="catatan" class="form-control" rows="6" required maxlength="5000"
                            placeholder="Tulis catatan Anda di sini..." id="isiTambah"></textarea>
                        <div class="d-flex justify-content-end">
                            <span id="charCountTambah" class="form-text">0 / 5000</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-5 fw-bold">
                        <i class="bi bi-save me-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
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
                        <label class="form-label fw-semibold small">Label / Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="label" id="editLabel" class="form-control" required maxlength="100">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Isi Catatan <span class="text-danger">*</span></label>
                        <textarea name="catatan" id="editIsi" class="form-control" rows="6" required maxlength="5000"></textarea>
                        <div class="d-flex justify-content-end">
                            <span id="charCountEdit" class="form-text">0 / 5000</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-5 fw-bold">
                        <i class="bi bi-save me-2"></i>Perbarui
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Counter tambah
    var isiTambah = document.getElementById('isiTambah');
    if (isiTambah) {
        isiTambah.addEventListener('input', function(){
            document.getElementById('charCountTambah').textContent = this.value.length + ' / 5000';
        });
    }

    // Filter
    document.querySelectorAll('.filter-btn').forEach(function(btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.filter-btn').forEach(function(b) {
                b.classList.remove('btn-success','active');
                b.classList.add('btn-outline-success');
            });
            this.classList.add('btn-success','active');
            this.classList.remove('btn-outline-success');
            var f = this.dataset.filter;
            document.querySelectorAll('.catatan-item').forEach(function(item) {
                item.style.display = (f==='semua' || item.dataset.label===f) ? '' : 'none';
            });
        });
    });
});

function openEdit(uuid, label, catatan) {
    document.getElementById('formEditCatatan').action = '/user/catatan/' + uuid;
    document.getElementById('editLabel').value  = label;
    document.getElementById('editIsi').value    = catatan;
    document.getElementById('charCountEdit').textContent = catatan.length + ' / 5000';
    new bootstrap.Modal(document.getElementById('modalEditCatatan')).show();
}

function confirmLogout() {
    Swal.fire({
        title:'Yakin ingin log out?', icon:'warning',
        showCancelButton:true, confirmButtonColor:'#198754', cancelButtonColor:'#d33',
        confirmButtonText:'Ya, Log Out', cancelButtonText:'Batal', reverseButtons:true
    }).then(function(r) { if(r.isConfirmed) document.getElementById('logout-form-catatan').submit(); });
}
</script>
</body>
</html>
