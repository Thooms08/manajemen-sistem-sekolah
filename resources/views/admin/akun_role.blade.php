<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Akun Role</title>
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
            --green-primary: #198754;
            --green-dark:    #0f5132;
            --green-light:   #f0faf5;
            --border:        #e2ebe6;
            --surface:       #ffffff;
            --text-main:     #1a2e25;
            --text-muted:    #6c8f7d;
            --shadow-sm:     0 2px 8px rgba(25,135,84,.08);
            --shadow-md:     0 6px 24px rgba(25,135,84,.12);
            --radius:        14px;
        }
        body { font-family: 'Inter', sans-serif; background: #f3f7f5; color: var(--text-main); }
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        #content { width: 100%; padding: 24px 30px; min-height: 100vh; }
        #sidebarCollapse {
            width: 42px; height: 42px; background: var(--green-primary);
            border: none; color: #fff; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
        }
        #sidebarCollapse:hover { background: var(--green-dark); }
        #overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 1040; }
        #overlay.active { display: block; }
        .card-panel {
            background: var(--surface); border: 1.5px solid var(--border);
            border-radius: var(--radius); box-shadow: var(--shadow-sm);
        }
        .table thead { background: #1a3a3a; color: #fff; }
        .table thead th { font-size: .82rem; font-weight: 600; letter-spacing: .3px; }
        .table tbody tr:hover { background: var(--green-light); }
        .form-control:focus, .form-select:focus {
            border-color: var(--green-primary) !important;
            box-shadow: 0 0 0 .2rem rgba(25,135,84,.2) !important;
        }
        /* Role card selectors */
        .role-selector { display: none; }
        .role-opt {
            display: flex; align-items: center; gap: 10px;
            padding: 12px 16px; border: 1.5px solid var(--border);
            border-radius: 10px; cursor: pointer; transition: .2s;
            background: var(--surface);
        }
        .role-opt:hover { border-color: var(--green-primary); background: var(--green-light); }
        .role-selector:checked + .role-opt {
            border-color: var(--green-primary);
            background: var(--green-light);
            box-shadow: 0 0 0 3px rgba(25,135,84,.15);
        }
        .role-selector:checked + .role-opt .role-check-icon { display: flex; }
        .role-check-icon {
            display: none; width: 20px; height: 20px; border-radius: 50%;
            background: var(--green-primary); color: #fff;
            align-items: center; justify-content: center; font-size: .7rem;
            flex-shrink: 0; margin-left: auto;
        }
        .step-badge {
            width: 28px; height: 28px; border-radius: 50%;
            background: var(--green-primary); color: #fff;
            display: inline-flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: .82rem; flex-shrink: 0;
        }
        .no-roles-msg {
            text-align: center; padding: 40px 20px; color: var(--text-muted);
        }
        .no-roles-msg i { font-size: 2.5rem; opacity: .35; margin-bottom: 10px; display: block; }
        @media (max-width: 768px) { #content { padding: 15px; } }
        /* Username feedback */
        .username-feedback {
            font-size: .8rem; margin-top: 6px; padding: 7px 12px;
            border-radius: 8px; display: none; align-items: center; gap: 7px;
        }
        .username-feedback.show { display: flex; }
        .username-feedback.feedback-invalid { background: #fff3cd; color: #856404; border: 1px solid #ffc107; }
        .username-feedback.feedback-taken   { background: #f8d7da; color: #842029; border: 1px solid #f5c2c7; }
        .username-feedback.feedback-ok      { background: #d1e7dd; color: #0a3622; border: 1px solid #a3cfbb; }
        .username-feedback.feedback-checking{ background: #e9ecef; color: #495057; border: 1px solid #dee2e6; }
    </style>
</head>
<body>
<div id="overlay"></div>
<div class="wrapper">
    @include('admin.sidebar')
    <div id="content">
        <div class="container-fluid px-0">

            {{-- TOP BAR --}}
            <div class="d-flex align-items-center justify-content-between mb-4 mt-1 flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <button type="button" id="sidebarCollapse" class="btn">
                        <i class="bi bi-list fs-4"></i>
                    </button>
                    <div>
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-person-gear text-success me-2"></i>Akun Role
                        </h5>
                        <small class="text-muted">Buat akun login untuk role yang telah dibuat</small>
                    </div>
                </div>
                <button class="btn btn-success px-4 fw-bold shadow-sm"
                    data-bs-toggle="modal" data-bs-target="#modalTambahAkun">
                    <i class="bi bi-person-plus me-2"></i>Buat Akun Baru
                </button>
            </div>

            {{-- ALERTS --}}
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">
                <i class="bi bi-check-circle-fill me-2"></i>{!! session('success') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4">
                <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Validasi gagal:</div>
                <ul class="mb-0 small ps-3">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            {{-- INFO BOX --}}
            @if($roles->isEmpty())
            <div class="alert border-0 shadow-sm mb-4" style="background:#fff3cd;">
                <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                Belum ada role yang dibuat. Silakan buat role terlebih dahulu di
                <a href="{{ route('admin.manajemen-role.index') }}" class="fw-bold text-warning">Manajemen Role</a>
                sebelum membuat akun.
            </div>
            @else
            <div class="alert border-0 shadow-sm mb-4" style="background:#e8f5e9; font-size:.88rem;">
                <i class="bi bi-info-circle-fill text-success me-2"></i>
                Klik <strong>Buat Akun Baru</strong>, pilih role, isi username dan password. Akun ini dapat digunakan untuk login ke sistem.
            </div>
            @endif

            {{-- TABEL AKUN --}}
            <div class="card-panel p-0 overflow-hidden">
                <div class="p-4 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="fw-bold" style="font-size:.92rem;">
                        <i class="bi bi-table me-2 text-success"></i>Daftar Akun
                        <span class="badge bg-success ms-2">{{ $akuns->count() }}</span>
                    </div>
                    <div class="input-group" style="max-width:300px;">
                        <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="searchAkun" class="form-control border-start-0"
                            placeholder="Cari username atau role...">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th width="50" class="ps-4">No</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Dibuat</th>
                                <th width="110" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="akunTableBody">
                            @forelse($akuns as $i => $u)
                            @php
                                $rd     = $u->role_data;
                                $warna  = $rd ? $rd->warna  : 'secondary';
                                $rNama  = $rd ? $rd->nama   : $u->role;
                            @endphp
                            <tr>
                                <td class="ps-4">{{ $i + 1 }}</td>
                                <td class="fw-bold">{{ $u->username }}</td>
                                <td>
                                    <span class="badge bg-{{ $warna }} rounded-pill px-3 py-2">
                                        <i class="bi bi-shield-check me-1"></i>{{ $rNama }}
                                    </span>
                                </td>
                                <td class="text-muted small">
                                    {{ $u->created_at->isoFormat('D MMM YYYY, HH:mm') }}
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-outline-success border-0" title="Edit"
                                            onclick="openEditModal({{ $u->id }}, '{{ addslashes($u->username) }}', '{{ addslashes($u->role) }}')">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <form action="{{ route('akun-role.destroy', $u->id) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('Hapus akun {{ addslashes($u->username) }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger border-0" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block mb-2 opacity-40"></i>
                                    Belum ada akun. Klik "Buat Akun Baru" untuk memulai.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>{{-- end card-panel --}}

        </div>{{-- end container --}}
    </div>{{-- end content --}}
</div>{{-- end wrapper --}}

{{-- ═══════════════════════════════════════════════════════
     MODAL: BUAT AKUN BARU
     Alur: Step 1 pilih role → Step 2 isi username + password
════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalTambahAkun" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('akun-role.store') }}" method="POST" id="formTambahAkun">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-person-plus me-2"></i>Buat Akun Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">

                    {{-- STEP 1: Pilih Role --}}
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="step-badge">1</span>
                        <div>
                            <div class="fw-bold" style="font-size:.9rem;">Pilih Role</div>
                            <div class="text-muted" style="font-size:.78rem;">Role menentukan hak akses pengguna di sistem</div>
                        </div>
                    </div>

                    @if($roles->isEmpty())
                    <div class="no-roles-msg border rounded-3 mb-4">
                        <i class="bi bi-shield-x"></i>
                        <div class="fw-bold mb-1">Belum ada role tersedia</div>
                        <small>
                            Buat role dulu di
                            <a href="{{ route('admin.manajemen-role.index') }}" target="_blank" class="text-success fw-bold">
                                Manajemen Role <i class="bi bi-box-arrow-up-right ms-1"></i>
                            </a>
                        </small>
                    </div>
                    @else
                    <div class="row g-2 mb-4" id="rolePicker">
                        @foreach($roles as $role)
                        <div class="col-sm-6 col-lg-4">
                            <input type="radio" name="role_slug" value="{{ $role->slug }}"
                                id="role_{{ $role->slug }}" class="role-selector" required>
                            <label for="role_{{ $role->slug }}" class="role-opt w-100">
                                <div style="width:36px;height:36px;border-radius:9px;
                                    background:var(--bs-{{ $role->warna }}-bg-subtle,#e9ecef);
                                    display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="bi bi-shield-check text-{{ $role->warna }}"></i>
                                </div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="fw-bold text-truncate" style="font-size:.85rem;">{{ $role->nama }}</div>
                                    @if($role->deskripsi)
                                    <div class="text-muted text-truncate" style="font-size:.72rem;">{{ $role->deskripsi }}</div>
                                    @else
                                    <span class="badge bg-{{ $role->warna }} bg-opacity-75" style="font-size:.65rem;">{{ $role->slug }}</span>
                                    @endif
                                </div>
                                <span class="role-check-icon"><i class="bi bi-check"></i></span>
                            </label>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <hr class="my-3">

                    {{-- STEP 2: Username & Password --}}
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="step-badge">2</span>
                        <div>
                            <div class="fw-bold" style="font-size:.9rem;">Data Login</div>
                            <div class="text-muted" style="font-size:.78rem;">Username dan password untuk akun ini</div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold small">
                                Username <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="username" id="tambah_username" class="form-control" required
                                maxlength="100" placeholder="Contoh: guru_budi, operator_keuangan..."
                                autocomplete="off">
                            <div class="username-feedback" id="usernameFeedback">
                                <i class="bi bi-info-circle-fill flex-shrink-0"></i>
                                <span id="usernameFeedbackText"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">
                                Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" name="password" class="form-control pwd-field"
                                    required minlength="6" placeholder="Min. 6 karakter" autocomplete="new-password">
                                <button type="button" class="btn btn-outline-secondary toggle-pwd">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">
                                Konfirmasi Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" class="form-control pwd-field"
                                    required minlength="6" placeholder="Ulangi password" autocomplete="new-password">
                                <button type="button" class="btn btn-outline-secondary toggle-pwd">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-12">
                            <div id="pwdMatchFeedback" class="small" style="display:none;"></div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-5 fw-bold"
                        {{ $roles->isEmpty() ? 'disabled' : '' }}>
                        <i class="bi bi-save me-2"></i>Simpan Akun
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     MODAL: EDIT AKUN
════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalEditAkun" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <form id="formEditAkun" method="POST">
                @csrf @method('PUT')
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-pencil-square me-2"></i>Edit Akun
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">

                    {{-- Pilih Role --}}
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="step-badge">1</span>
                        <div class="fw-bold" style="font-size:.9rem;">Ganti Role</div>
                    </div>
                    <div class="row g-2 mb-4" id="rolePickerEdit">
                        @foreach($roles as $role)
                        <div class="col-sm-6 col-lg-4">
                            <input type="radio" name="role_slug" value="{{ $role->slug }}"
                                id="edit_role_{{ $role->slug }}" class="role-selector">
                            <label for="edit_role_{{ $role->slug }}" class="role-opt w-100">
                                <div style="width:36px;height:36px;border-radius:9px;
                                    background:var(--bs-{{ $role->warna }}-bg-subtle,#e9ecef);
                                    display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="bi bi-shield-check text-{{ $role->warna }}"></i>
                                </div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="fw-bold text-truncate" style="font-size:.85rem;">{{ $role->nama }}</div>
                                    <span class="badge bg-{{ $role->warna }} bg-opacity-75" style="font-size:.65rem;">{{ $role->slug }}</span>
                                </div>
                                <span class="role-check-icon"><i class="bi bi-check"></i></span>
                            </label>
                        </div>
                        @endforeach
                    </div>

                    <hr class="my-3">

                    {{-- Username & Password --}}
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="step-badge">2</span>
                        <div class="fw-bold" style="font-size:.9rem;">Data Login</div>
                    </div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold small">
                                Username <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="username" id="edit_username" class="form-control"
                                required maxlength="100" autocomplete="off">
                        </div>
                        <div class="col-12">
                            <div class="alert py-2 mb-0" style="background:#fff3cd;font-size:.8rem;">
                                <i class="bi bi-lightbulb me-1 text-warning"></i>
                                Kosongkan password jika tidak ingin menggantinya.
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Password Baru</label>
                            <div class="input-group">
                                <input type="password" name="password" class="form-control pwd-field-edit"
                                    minlength="6" placeholder="Password baru (opsional)" autocomplete="new-password">
                                <button type="button" class="btn btn-outline-secondary toggle-pwd-edit">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Konfirmasi Password</label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" class="form-control pwd-field-edit"
                                    minlength="6" placeholder="Konfirmasi password baru" autocomplete="new-password">
                                <button type="button" class="btn btn-outline-secondary toggle-pwd-edit">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-5 fw-bold">
                        <i class="bi bi-save me-2"></i>Perbarui Akun
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

    // ── Sidebar toggle ─────────────────────────────────────────
    const sidebar     = document.getElementById('sidebar');
    const collapseBtn = document.getElementById('sidebarCollapse');
    const closeBtn    = document.getElementById('close-sidebar');
    const overlay     = document.getElementById('overlay');

    function toggleSidebar() {
        if (window.innerWidth <= 768) {
            sidebar.classList.toggle('show-mobile');
            overlay.classList.toggle('active');
        } else {
            sidebar.classList.toggle('inactive');
        }
    }
    if (collapseBtn) collapseBtn.addEventListener('click', toggleSidebar);
    if (closeBtn)    closeBtn.addEventListener('click', toggleSidebar);
    if (overlay)     overlay.addEventListener('click', toggleSidebar);

    // ── Toggle show/hide password (tambah) ─────────────────────
    document.querySelectorAll('.toggle-pwd').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const input = this.previousElementSibling;
            const icon  = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        });
    });

    // ── Toggle show/hide password (edit) ───────────────────────
    document.querySelectorAll('.toggle-pwd-edit').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const input = this.previousElementSibling;
            const icon  = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        });
    });

    // ── Realtime password match feedback (tambah) ──────────────
    const pwdField   = document.querySelector('#formTambahAkun input[name="password"]');
    const confirmFld = document.querySelector('#formTambahAkun input[name="password_confirmation"]');
    const feedback   = document.getElementById('pwdMatchFeedback');

    function checkPwdMatch() {
        if (!pwdField || !confirmFld || !feedback) return;
        const val  = pwdField.value;
        const conf = confirmFld.value;
        if (!conf) { feedback.style.display = 'none'; return; }
        feedback.style.display = 'block';
        if (val === conf) {
            feedback.innerHTML = '<span class="text-success"><i class="bi bi-check-circle me-1"></i>Password cocok</span>';
            confirmFld.classList.remove('is-invalid');
            confirmFld.classList.add('is-valid');
        } else {
            feedback.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle me-1"></i>Password tidak cocok</span>';
            confirmFld.classList.remove('is-valid');
            confirmFld.classList.add('is-invalid');
        }
    }
    if (pwdField)   pwdField.addEventListener('input', checkPwdMatch);
    if (confirmFld) confirmFld.addEventListener('input', checkPwdMatch);

    // ══════════════════════════════════════════════════════════
    // AJAX USERNAME VALIDATION — spasi + cek duplikat di DB
    // ══════════════════════════════════════════════════════════
    const usernameInput    = document.getElementById('tambah_username');
    const usernameFeedback = document.getElementById('usernameFeedback');
    const feedbackText     = document.getElementById('usernameFeedbackText');
    const feedbackIcon     = usernameFeedback ? usernameFeedback.querySelector('i') : null;
    const submitBtn        = document.querySelector('#formTambahAkun button[type="submit"]');

    // State flag: apakah username sedang invalid/taken
    let usernameIsValid = true;
    let debounceTimer   = null;

    function setFeedback(type, message) {
        if (!usernameFeedback) return;

        // Hapus semua kelas status sebelumnya
        usernameFeedback.classList.remove(
            'show', 'feedback-invalid', 'feedback-taken',
            'feedback-ok', 'feedback-checking'
        );
        usernameInput.classList.remove('is-invalid', 'is-valid');

        if (type === 'hidden') {
            return; // tidak tampilkan apa-apa
        }

        usernameFeedback.classList.add('show');
        feedbackText.textContent = message;

        if (type === 'invalid') {
            usernameFeedback.classList.add('feedback-invalid');
            feedbackIcon.className = 'bi bi-exclamation-triangle-fill flex-shrink-0';
            usernameInput.classList.add('is-invalid');
            usernameIsValid = false;
        } else if (type === 'taken') {
            usernameFeedback.classList.add('feedback-taken');
            feedbackIcon.className = 'bi bi-x-circle-fill flex-shrink-0';
            usernameInput.classList.add('is-invalid');
            usernameIsValid = false;
        } else if (type === 'ok') {
            usernameFeedback.classList.add('feedback-ok');
            feedbackIcon.className = 'bi bi-check-circle-fill flex-shrink-0';
            usernameInput.classList.add('is-valid');
            usernameIsValid = true;
        } else if (type === 'checking') {
            usernameFeedback.classList.add('feedback-checking');
            feedbackIcon.className = 'bi bi-hourglass-split flex-shrink-0';
            usernameIsValid = false; // sementara blok submit
        }

        // Tombol submit: disable jika invalid/taken/checking
        if (submitBtn) {
            submitBtn.disabled = (type !== 'ok');
        }
    }

    function validateUsername(value) {
        // 1. Kosong — sembunyikan feedback, enable submit
        if (value.trim() === '') {
            setFeedback('hidden');
            usernameIsValid = false;
            if (submitBtn) submitBtn.disabled = {{ $roles->isEmpty() ? 'true' : 'false' }};
            return;
        }

        // 2. Deteksi spasi — langsung tampilkan, tanpa AJAX
        if (value.includes(' ')) {
            setFeedback('invalid', 'Username tidak boleh mengandung spasi. Gunakan underscore (_) atau tanpa pemisah.');
            return;
        }

        // 3. Polling ke server (debounced 450ms)
        setFeedback('checking', 'Memeriksa ketersediaan username...');
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function () {
            fetch('{{ route("akun-role.check-username") }}?username=' + encodeURIComponent(value), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.status === 'taken') {
                    setFeedback('taken', data.message);
                } else if (data.status === 'available') {
                    setFeedback('ok', data.message);
                } else {
                    setFeedback('hidden');
                    usernameIsValid = true;
                    if (submitBtn) submitBtn.disabled = {{ $roles->isEmpty() ? 'true' : 'false' }};
                }
            })
            .catch(function () {
                // Jika request gagal, jangan blok submit
                setFeedback('hidden');
                usernameIsValid = true;
                if (submitBtn) submitBtn.disabled = {{ $roles->isEmpty() ? 'true' : 'false' }};
            });
        }, 450);
    }

    if (usernameInput) {
        usernameInput.addEventListener('input', function () {
            validateUsername(this.value);
        });

        // Reset saat modal ditutup
        document.getElementById('modalTambahAkun').addEventListener('hidden.bs.modal', function () {
            usernameInput.value = '';
            usernameInput.classList.remove('is-invalid', 'is-valid');
            setFeedback('hidden');
            usernameIsValid = true;
            if (submitBtn && !{{ $roles->isEmpty() ? 'true' : 'false' }}) {
                submitBtn.disabled = false;
            }
        });
    }

    // ── Cegah submit jika username tidak valid ──────────────────
    const formTambah = document.getElementById('formTambahAkun');
    if (formTambah) {
        formTambah.addEventListener('submit', function (e) {

            // Cek spasi sekali lagi di sisi klien
            const uname = document.getElementById('tambah_username').value;
            if (uname.includes(' ')) {
                e.preventDefault();
                Swal.fire({ icon: 'warning', title: 'Username tidak valid',
                    text: 'Username tidak boleh mengandung spasi.', confirmButtonColor: '#198754' });
                return;
            }

            // Cek flag username (taken / sedang checking)
            if (!usernameIsValid) {
                e.preventDefault();
                Swal.fire({ icon: 'error', title: 'Username bermasalah',
                    text: 'Silakan perbaiki username terlebih dahulu sebelum menyimpan.',
                    confirmButtonColor: '#198754' });
                return;
            }

            // Cek password
            const p  = this.querySelector('input[name="password"]').value;
            const pc = this.querySelector('input[name="password_confirmation"]').value;
            if (p !== pc) {
                e.preventDefault();
                Swal.fire({ icon: 'error', title: 'Password tidak cocok',
                    text: 'Pastikan password dan konfirmasi password sama.', confirmButtonColor: '#198754' });
                return;
            }

            // Cek role dipilih
            const roleChosen = this.querySelector('input[name="role_slug"]:checked');
            if (!roleChosen) {
                e.preventDefault();
                Swal.fire({ icon: 'warning', title: 'Pilih Role',
                    text: 'Silakan pilih role terlebih dahulu.', confirmButtonColor: '#198754' });
                return;
            }
        });
    }

    // ── AJAX Search tabel akun ──────────────────────────────────
    const searchInput = document.getElementById('searchAkun');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const keyword = this.value;
            fetch('{{ route("akun-role.search") }}?search=' + encodeURIComponent(keyword), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.text())
            .then(html => {
                document.getElementById('akunTableBody').innerHTML = html;
            });
        });
    }

    // ── Re-open modal tambah jika ada error validasi ───────────
    @if($errors->any() && !session('success'))
        new bootstrap.Modal(document.getElementById('modalTambahAkun')).show();
    @endif
});

// ── Buka modal edit ────────────────────────────────────────
function openEditModal(id, username, roleSlug) {
    const form = document.getElementById('formEditAkun');
    form.action = '/akun-role/' + id;

    document.getElementById('edit_username').value = username;

    // Reset semua radio edit
    document.querySelectorAll('#rolePickerEdit input[type="radio"]').forEach(r => r.checked = false);

    // Centang radio yang sesuai
    const target = document.getElementById('edit_role_' + roleSlug);
    if (target) target.checked = true;

    // Kosongkan password
    form.querySelectorAll('input[name="password"], input[name="password_confirmation"]').forEach(i => {
        i.value = '';
        i.type  = 'password';
    });

    new bootstrap.Modal(document.getElementById('modalEditAkun')).show();
}
</script>
</body>
</html>
