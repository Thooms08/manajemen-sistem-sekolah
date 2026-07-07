<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manajemen Role & Hak Akses</title>
        @include('favicon')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #198754; }
        body { font-family: 'Inter', sans-serif; background: #f4f7f6; overflow-x: hidden; }
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        #content { width: 100%; padding: 24px 30px; min-height: 100vh; min-width: 0; }
        #sidebarCollapse { width: 45px; height: 45px; background: var(--primary); border: none; color: white; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .card { border: none; border-radius: 14px; box-shadow: 0 4px 18px rgba(0,0,0,.05); }
        input:focus, textarea:focus, select:focus { border-color: #198754 !important; box-shadow: 0 0 0 .2rem rgba(25,135,84,.25) !important; outline: none !important; }
        /* Role card */
        .role-card { border-radius: 12px; border: 1.5px solid #e2e8f0; background: white; padding: 20px; transition: .2s; position: relative; height: 100%; }
        .role-card:hover { border-color: var(--primary); box-shadow: 0 4px 16px rgba(25,135,84,.1); }
        .role-card.system-role { border-style: dashed; }
        .role-badge { font-size: .78rem; font-weight: 600; padding: 4px 12px; border-radius: 50px; }
        .system-label { position: absolute; top: 12px; right: 12px; font-size: .7rem; color: #aaa; }
        .perm-count { font-size: .82rem; color: #666; }
        .role-action-group { display: flex; gap: .5rem; flex-wrap: wrap; }
        .role-action-group > * { flex: 1 1 auto; min-width: 0; }
        /* Warna opsi */
        .warna-opt { width: 26px; height: 26px; border-radius: 50%; display: inline-block; border: 3px solid transparent; cursor: pointer; transition: .15s; }
        .warna-opt.selected, .warna-opt:hover { border-color: #333; transform: scale(1.15); }
        #overlay { display: none; position: fixed; width: 100vw; height: 100vh; background: rgba(0,0,0,.5); z-index: 1040; top: 0; left: 0; }
        #overlay.active { display: block; }
        .modal-footer { flex-wrap: wrap; gap: .5rem; }
        @media (max-width: 991px) { #content { padding: 16px 18px; } }
        @media (max-width: 767px) {
            #content { padding: 12px 12px; }
            .page-header { flex-direction: column; align-items: flex-start !important; gap: 10px; }
            .page-header .btn-tambah { width: 100%; }
            .role-card .d-flex.align-items-center.gap-2 { flex-wrap: wrap; }
            .role-action-group { flex-direction: column; }
            .role-action-group > * { width: 100%; }
            .modal-footer .btn { width: 100%; }
            .modal-body { padding: 1rem !important; }
        }
    </style>
</head>
<body>
<div id="overlay"></div>
<div class="wrapper">
    @include('user.sidebar')
    <div id="content">
        <div class="container-fluid">

            {{-- Header --}}
            <div class="d-flex align-items-center justify-content-between mb-4 mt-1 flex-wrap gap-3 page-header">
                <div class="d-flex align-items-center gap-3">
                    <button id="sidebarCollapse" class="btn"><i class="bi bi-list fs-4"></i></button>
                    <div>
                        <h4 class="mb-0 fw-bold text-success">Manajemen Role & Hak Akses</h4>
                        <p class="text-muted small mb-0 d-none d-sm-block">Buat role dinamis dan atur modul apa saja yang bisa diakses.</p>
                    </div>
                </div>
                <button class="btn btn-success px-4 fw-bold btn-tambah" data-bs-toggle="modal" data-bs-target="#modalTambahRole">
                    <i class="bi bi-plus-circle me-2"></i>Buat Role Baru
                </button>
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

            {{-- Info Box --}}
            <div class="alert alert-success border-0 shadow-sm mb-4" style="font-size:.88rem;">
                <i class="bi bi-info-circle-fill me-2"></i>
                <strong>Cara kerja:</strong> Buat role (contoh: <em>Guru Wali Kelas</em>), lalu klik
                <strong>Atur Hak Akses</strong> untuk menentukan modul mana yang bisa diakses. Role ini nantinya akan dipakai saat membuat akun pengguna.
                Role berlabel <span class="badge bg-secondary">Sistem</span> tidak dapat dihapus.
            </div>

            {{-- Role Cards --}}
            <div class="row g-3">
                @forelse($roles as $role)
                <div class="col-12 col-sm-6 col-xl-4">
                    <div class="role-card {{ $role->is_system ? 'system-role' : '' }} h-100">
                        @if($role->is_system)
                            <span class="system-label"><i class="bi bi-lock-fill me-1"></i>Sistem</span>
                        @endif

                        <div class="d-flex align-items-start gap-3 mb-3">
                            <div style="width:44px;height:44px;border-radius:10px;background:var(--bs-{{ $role->warna }}-bg-subtle, #e2e8f0);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="bi bi-shield-check fs-5 text-{{ $role->warna }}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="fw-bold">{{ $role->nama }}</span>
                                    <span class="badge bg-{{ $role->warna }} role-badge">{{ $role->slug }}</span>
                                </div>
                                <p class="text-muted small mb-0 mt-1">{{ $role->deskripsi ?? '-' }}</p>
                            </div>
                        </div>

                        {{-- Preview permission count --}}
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-key text-muted"></i>
                            <span class="perm-count">
                                @if($role->permissions_count > 0)
                                    <strong>{{ $role->permissions_count }}</strong> modul dikonfigurasi
                                @else
                                    <span class="text-danger">Belum ada hak akses diatur</span>
                                @endif
                            </span>
                            <button class="btn btn-link btn-sm p-0 ms-1 text-muted" onclick="previewPermissions('{{ $role->uuid }}')" title="Lihat ringkasan">
                                <i class="bi bi-eye-fill"></i>
                            </button>
                        </div>

                        {{-- Aksi --}}
                        <div class="role-action-group">
                            @if($role->isAdmin())
                                {{-- Role admin: semua tombol dikunci --}}
                                <button class="btn btn-secondary btn-sm px-3 flex-grow-1" disabled title="Hak akses Administrator tidak dapat diubah">
                                    <i class="bi bi-shield-lock me-1"></i>Terlindungi
                                </button>
                            @else
                                <a href="{{ route('admin.manajemen-role.permissions', $role->uuid) }}" class="btn btn-success btn-sm px-3 flex-grow-1">
                                    <i class="bi bi-key me-1"></i>Atur Hak Akses
                                </a>
                            @endif

                            @if(!$role->is_system)
                                {{-- Tombol Edit --}}
                                <button class="btn btn-outline-secondary btn-sm px-3"
                                    onclick="openEditRole('{{ $role->uuid }}')" title="Edit Role">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                {{-- Tombol Hapus dengan SweetAlert --}}
                                <form id="form-hapus-{{ $role->uuid }}"
                                    action="{{ route('admin.manajemen-role.destroy', $role->uuid) }}"
                                    method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="button"
                                        class="btn btn-outline-danger btn-sm px-3"
                                        title="Hapus Role"
                                        onclick="konfirmasiHapusRole(
                                            '{{ $role->uuid }}',
                                            '{{ addslashes($role->nama) }}',
                                            {{ $role->permissions_count }}
                                        )">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @elseif(!$role->isAdmin())
                                {{-- Role sistem non-admin: kunci edit & hapus --}}
                                <span class="btn btn-outline-secondary btn-sm px-3 disabled" title="Role sistem tidak dapat diedit">
                                    <i class="bi bi-lock"></i>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-shield-x fs-2 d-block mb-2"></i>
                        Belum ada role. Klik "Buat Role Baru" untuk memulai.
                    </div>
                </div>
                @endforelse
            </div>

        </div>
    </div>
</div>

{{-- MODAL TAMBAH ROLE --}}
<div class="modal fade" id="modalTambahRole" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('admin.manajemen-role.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-shield-plus me-2"></i>Buat Role Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Nama Role <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" required maxlength="100"
                            placeholder="Contoh: Guru Wali Kelas, Staff TU, Operator Keuangan...">
                        <div class="form-text">Nama akan otomatis diubah jadi slug (contoh: guru_wali_kelas)</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Deskripsi</label>
                        <input type="text" name="deskripsi" class="form-control" maxlength="255"
                            placeholder="Apa yang bisa dilakukan role ini...">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Warna Label <span class="text-danger">*</span></label>
                        <div class="d-flex gap-2 mt-1 flex-wrap">
                            @foreach(['primary'=>'#0d6efd','secondary'=>'#6c757d','success'=>'#198754','danger'=>'#dc3545','warning'=>'#ffc107','info'=>'#0dcaf0','dark'=>'#212529'] as $w => $hex)
                                <label class="mb-0" title="{{ $w }}">
                                    <input type="radio" name="warna" value="{{ $w }}" class="d-none warna-radio" {{ $w === 'secondary' ? 'checked' : '' }}>
                                    <span class="warna-opt {{ $w === 'secondary' ? 'selected' : '' }}" style="background:{{ $hex }};" onclick="selectWarna(this)"></span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="alert alert-warning py-2 mb-0" style="font-size:.82rem;">
                            <i class="bi bi-lightbulb me-1"></i>
                            Setelah membuat role, jangan lupa klik <strong>Atur Hak Akses</strong> untuk menentukan modul yang bisa diakses.
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4">Buat Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL EDIT ROLE --}}
<div class="modal fade" id="modalEditRole" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content border-0 shadow">
            <form id="formEditRole" method="POST">
                @csrf @method('PUT')
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Role</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Nama Role <span class="text-danger">*</span></label>
                        <input type="text" name="nama" id="edit_nama_role" class="form-control" required maxlength="100">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Deskripsi</label>
                        <input type="text" name="deskripsi" id="edit_deskripsi_role" class="form-control" maxlength="255">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Warna Label <span class="text-danger">*</span></label>
                        <div class="d-flex gap-2 mt-1 flex-wrap" id="edit-warna-wrapper">
                            @foreach(['primary'=>'#0d6efd','secondary'=>'#6c757d','success'=>'#198754','danger'=>'#dc3545','warning'=>'#ffc107','info'=>'#0dcaf0','dark'=>'#212529'] as $w => $hex)
                                <label class="mb-0" title="{{ $w }}">
                                    <input type="radio" name="warna" value="{{ $w }}" class="d-none edit-warna-radio">
                                    <span class="warna-opt" data-warna="{{ $w }}" style="background:{{ $hex }};" onclick="selectWarnaEdit(this)"></span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL PREVIEW PERMISSION --}}
<div class="modal fade" id="modalPreview" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-eye me-2"></i>Ringkasan Hak Akses</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="previewBody">
                <div class="text-center py-4"><div class="spinner-border text-success"></div></div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function () {
    function toggleSidebar() {
        if ($(window).width() <= 768) { $('#sidebar').toggleClass('show-mobile'); $('#overlay').toggleClass('active'); }
        else { $('#sidebar').toggleClass('inactive'); }
    }
    $('#sidebarCollapse, #close-sidebar, #overlay').on('click', toggleSidebar);
});

// Pilih warna (modal tambah)
function selectWarna(el) {
    el.closest('.d-flex').querySelectorAll('.warna-opt').forEach(e => e.classList.remove('selected'));
    el.classList.add('selected');
    el.previousElementSibling.checked = true;
}

// Pilih warna (modal edit)
function selectWarnaEdit(el) {
    document.querySelectorAll('#edit-warna-wrapper .warna-opt').forEach(e => e.classList.remove('selected'));
    el.classList.add('selected');
    el.previousElementSibling.checked = true;
}

// Buka modal edit role
function openEditRole(uuid) {
    $.get(`{{ url('admin/manajemen-role') }}/${uuid}/data`, function (d) {
        $('#formEditRole').attr('action', `/admin/manajemen-role/${uuid}`);
        $('#edit_nama_role').val(d.nama);
        $('#edit_deskripsi_role').val(d.deskripsi || '');

        // Set warna
        document.querySelectorAll('#edit-warna-wrapper .warna-opt').forEach(function(el) {
            el.classList.remove('selected');
            if (el.dataset.warna === d.warna) {
                el.classList.add('selected');
                el.previousElementSibling.checked = true;
            }
        });

        new bootstrap.Modal(document.getElementById('modalEditRole')).show();
    });
}

// Konfirmasi hapus role dengan SweetAlert
function konfirmasiHapusRole(uuid, nama, jumlahModul) {
    const pesanModul = jumlahModul > 0
        ? `<br><small class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>${jumlahModul} konfigurasi hak akses juga akan ikut terhapus.</small>`
        : '';

    Swal.fire({
        title: 'Hapus Role?',
        html: `Anda akan menghapus role <strong>"${nama}"</strong>.${pesanModul}<br><br>Aksi ini <strong>tidak dapat dibatalkan</strong>.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="bi bi-trash me-1"></i>Ya, Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        focusCancel: true,
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('form-hapus-' + uuid).submit();
        }
    });
}

// Preview permission
function previewPermissions(uuid) {
    const $body = $('#previewBody');
    $body.html('<div class="text-center py-4"><div class="spinner-border text-success"></div></div>');
    new bootstrap.Modal(document.getElementById('modalPreview')).show();

    $.get(`{{ url('admin/manajemen-role') }}/${uuid}/summary`, function (res) {
        const role    = res.role;
        const summary = res.summary;

        if (!summary.length) {
            // Cek apakah role admin — jika ya, tampilkan pesan terlindungi
            const isAdmin = role.slug === 'admin';
            $body.html(`
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-key-fill fs-2 d-block mb-2"></i>
                    ${isAdmin
                        ? `<span class="text-danger fw-semibold">Role Administrator memiliki akses penuh ke seluruh sistem dan tidak dapat diubah.</span>`
                        : `Role <strong>${role.nama}</strong> belum memiliki hak akses yang dikonfigurasi.
                           <br><a href="/admin/manajemen-role/${role.uuid}/permissions" class="btn btn-success btn-sm mt-3">
                               <i class="bi bi-key me-1"></i>Atur Sekarang
                           </a>`
                    }
                </div>`);
            return;
        }

        // Kelompokkan per group
        const groups = {};
        summary.forEach(s => {
            if (!groups[s.group]) groups[s.group] = [];
            groups[s.group].push(s);
        });

        const aksiLabel = { view: 'Lihat', create: 'Tambah', edit: 'Edit', delete: 'Hapus' };
        const aksiColor = { view: 'info', create: 'success', edit: 'warning', delete: 'danger' };

        let html = `<div class="mb-3 d-flex align-items-center gap-2">
            <span class="badge bg-${role.warna} px-3 py-2">${role.slug}</span>
            <strong>${role.nama}</strong>
            <span class="text-muted small">— ${summary.length} modul dikonfigurasi</span>
        </div>`;

        if (role.slug === 'admin') {
            html += `<div class="alert alert-danger py-2 mb-3" style="font-size:.82rem;">
                <i class="bi bi-shield-lock me-1"></i>
                Role ini memiliki <strong>akses penuh</strong> ke seluruh sistem dan tidak dapat diubah.
            </div>`;
        }

        for (const [group, items] of Object.entries(groups)) {
            html += `<div class="fw-bold text-muted mb-2 mt-3" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.5px;">${group}</div>`;
            items.forEach(item => {
                const aksiBadges = item.aksi.map(a =>
                    `<span class="badge bg-${aksiColor[a] || 'secondary'} bg-opacity-10 text-${aksiColor[a] || 'secondary'} border border-${aksiColor[a] || 'secondary'} border-opacity-25 px-2">${aksiLabel[a] || a}</span>`
                ).join(' ');

                html += `<div class="d-flex align-items-center gap-3 py-2 border-bottom">
                    <i class="bi ${item.icon} text-success" style="width:20px;"></i>
                    <span class="flex-grow-1" style="font-size:.88rem;">${item.modul}</span>
                    <div class="d-flex gap-1">${aksiBadges}</div>
                </div>`;
            });
        }

        $body.html(html);
    });
}
</script>
</body>
</html>
