<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Catatan</title>
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
        #content { width: 100%; padding: 15px 15px; min-height: 100vh; transition: all 0.3s; }
        #sidebarCollapse {
            width: 42px; height: 42px;
            background: var(--green-primary); border: none; color: #fff;
            border-radius: 10px; display: flex; align-items: center; justify-content: center;
        }
        #sidebarCollapse:hover { background: var(--green-dark); }
        #overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 1040; }
        #overlay.active { display: block; }

        /* ── User Card (tampilan admin) ── */
        .user-card {
            background: var(--surface);
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            padding: 16px 18px;
            cursor: pointer;
            transition: transform .2s, box-shadow .2s, border-color .2s;
            text-decoration: none;
            color: var(--text-main);
            display: block;
        }
        .user-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
            border-color: var(--green-primary);
            color: var(--text-main);
        }
        .user-avatar {
            width: 52px; height: 52px; border-radius: 14px;
            background: var(--green-light);
            color: var(--green-primary);
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 1.3rem; flex-shrink: 0;
        }
        .catatan-count-badge {
            background: var(--green-primary); color: #fff;
            font-size: .72rem; font-weight: 700;
            padding: 3px 10px; border-radius: 50px;
        }
        .preview-label {
            font-size: .73rem; font-weight: 700; letter-spacing: .5px;
            text-transform: uppercase; color: var(--text-muted);
        }
        .preview-text {
            font-size: .82rem; color: #555;
            display: -webkit-box; -webkit-line-clamp: 2;
            -webkit-box-orient: vertical; overflow: hidden;
        }

        /* ── Catatan Card (tampilan user) ── */
        .catatan-card {
            background: var(--surface);
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            padding: 16px 18px;
            transition: box-shadow .2s, border-color .2s;
            position: relative;
        }
        .catatan-card:hover { box-shadow: var(--shadow-sm); border-color: #b5d5c5; }
        .label-badge {
            display: inline-flex; align-items: center; gap: 5px;
            background: var(--green-light); color: var(--green-primary);
            font-size: .75rem; font-weight: 700;
            padding: 4px 12px; border-radius: 50px;
            border: 1px solid #c3e6cb;
        }
        .catatan-isi {
            font-size: .88rem; color: #444; line-height: 1.65;
            white-space: pre-wrap; word-break: break-word;
        }
        .catatan-time {
            font-size: .73rem; color: var(--text-muted);
        }
        .btn-aksi-catatan {
            background: transparent; border: none; padding: 4px 8px;
            border-radius: 6px; cursor: pointer; transition: background .15s;
            line-height: 1;
        }
        .btn-aksi-catatan:hover { background: var(--green-light); }

        /* ── Form Tambah ── */
        .form-card {
            background: var(--surface);
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            padding: 20px;
        }
        .form-control:focus {
            border-color: var(--green-primary) !important;
            box-shadow: 0 0 0 .25rem rgba(25,135,84,.2) !important;
        }

        /* ── Empty State ── */
        .empty-state {
            text-align: center; padding: 60px 20px; color: var(--text-muted);
        }
        .empty-state i { font-size: 3.5rem; opacity: .35; margin-bottom: 12px; display: block; }

        /* ── Filter bar ── */
        .filter-bar {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: 10px; padding: 14px 20px;
        }

        @media (min-width: 768px) {
            #content { padding: 24px 30px; }
            .user-card { padding: 22px 24px; }
            .catatan-card { padding: 20px 22px; }
            .form-card { padding: 24px; }
        }
    </style>
</head>
<body>
<div id="overlay"></div>
<div class="wrapper">
    @include('admin.sidebar')

    <div id="content">
        <div class="container-fluid px-0">

            {{-- ── TOP BAR ── --}}
            <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center justify-content-between mb-4 mt-1 gap-3">
                <div class="d-flex align-items-center gap-3">
                    <button type="button" id="sidebarCollapse" class="btn flex-shrink-0">
                        <i class="bi bi-list fs-4"></i>
                    </button>
                    <div>
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-journal-text text-success me-2"></i>Catatan
                        </h5>
                        <small class="text-muted">
                            @auth
                                @if(auth()->user()->role === 'admin')
                                    Ringkasan catatan semua pengguna
                                @else
                                    Catatan pribadi Anda
                                @endif
                            @endauth
                        </small>
                    </div>
                </div>

                @auth
                    {{-- Admin juga bisa tambah catatan sendiri --}}
                    <button class="btn btn-success px-4 fw-bold shadow-sm w-100 w-sm-auto flex-shrink-0 align-self-start align-self-sm-center" data-bs-toggle="modal" data-bs-target="#modalTambahCatatan">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Catatan Saya
                    </button>
                @endauth
            </div>

            {{-- ── ALERT ── --}}
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
            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4">
                <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Validasi gagal:</div>
                <ul class="mb-0 small ps-3">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @auth

            {{-- ════════════════════════════════════════
                 TAMPILAN ADMIN — Card per User + Catatan Sendiri
            ════════════════════════════════════════ --}}
            @if(auth()->user()->role === 'admin')

                {{-- Info --}}
                <div class="alert border-0 shadow-sm mb-4" style="background:#e8f5e9; font-size:.88rem;">
                    <i class="bi bi-info-circle-fill text-success me-2"></i>
                    Klik kartu pengguna untuk melihat seluruh catatan yang mereka buat.
                    Gunakan tombol <strong>Tambah Catatan Saya</strong> di atas untuk membuat catatan pribadi Anda sendiri.
                </div>

                {{-- Catatan Admin Sendiri --}}
                @php
                    $catatanAdmin = \App\Models\DataMaster\Catatan::where('id_user', auth()->id())->latest()->get();
                @endphp
                @if($catatanAdmin->count() > 0)
                <div class="mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                        <div class="fw-bold" style="font-size:.88rem;">
                            <i class="bi bi-person-circle text-success me-2"></i>
                            Catatan Saya
                            <span class="badge bg-success ms-2">{{ $catatanAdmin->count() }}</span>
                        </div>
                    </div>
                    <div class="row g-3" id="catatanAdminContainer">
                        @foreach($catatanAdmin as $c)
                        <div class="col-md-6 col-lg-4 catatan-item" data-label="{{ $c->label }}">
                            <div class="catatan-card h-100">
                                <div class="d-flex align-items-start justify-content-between mb-2 gap-2">
                                    <span class="label-badge">
                                        <i class="bi bi-tag-fill"></i>{{ $c->label }}
                                    </span>
                                    <div class="d-flex gap-1 flex-shrink-0">
                                        <button class="btn-aksi-catatan text-success" title="Edit"
                                            onclick="openEditCatatan('{{ $c->uuid }}', '{{ addslashes($c->label) }}', '{{ addslashes($c->catatan) }}')">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <form action="{{ route('catatan.destroy', $c->uuid) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('Hapus catatan ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-aksi-catatan text-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <p class="catatan-isi mb-3">{{ $c->catatan }}</p>
                                <div class="catatan-time">
                                    <i class="bi bi-clock me-1"></i>
                                    {{ $c->created_at->isoFormat('D MMM YYYY, HH:mm') }}
                                    @if($c->updated_at->ne($c->created_at))
                                        <span class="ms-2 text-warning" title="Sudah diedit">
                                            <i class="bi bi-pencil-fill"></i> diedit
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <hr class="my-4">
                </div>
                @endif

                {{-- Filter / Search catatan user lain --}}
                <div class="fw-bold mb-3" style="font-size:.88rem;">
                    <i class="bi bi-people text-success me-2"></i>Catatan Pengguna Lain
                </div>
                <div class="filter-bar mb-4 d-flex flex-column flex-md-row gap-3 justify-content-between align-items-stretch align-items-md-center">
                    <div class="d-flex align-items-center gap-2 flex-grow-1" style="max-width: 400px;">
                        <i class="bi bi-search text-muted flex-shrink-0"></i>
                        <input type="text" id="searchUserCard" class="form-control border-0 shadow-none p-0 flex-grow-1"
                            style="background:transparent;"
                            placeholder="Cari nama pengguna...">
                    </div>
                    <span class="text-muted small flex-shrink-0" style="font-size:.82rem;">
                        <i class="bi bi-people-fill me-1"></i>{{ isset($usersWithCatatan) ? $usersWithCatatan->count() : 0 }} pengguna memiliki catatan
                    </span>
                </div>

                {{-- User Cards --}}
                @if(isset($usersWithCatatan) && $usersWithCatatan->count() > 0)
                <div class="row g-3" id="userCardContainer">
                    @foreach($usersWithCatatan as $u)
                    <div class="col-sm-6 col-lg-4 col-xl-3 user-card-col" data-name="{{ strtolower($u->username) }}">
                        <a href="{{ route('catatan.by-user', $u->id) }}" class="user-card">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="user-avatar flex-shrink-0">
                                    {{ strtoupper(substr($u->username, 0, 1)) }}
                                </div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="fw-bold text-truncate" title="{{ $u->username }}">{{ $u->username }}</div>
                                    <div class="mt-1">
                                        <span class="catatan-count-badge text-nowrap">
                                            <i class="bi bi-journal-text me-1"></i>{{ $u->catatans_count }} catatan
                                        </span>
                                    </div>
                                </div>
                                <i class="bi bi-chevron-right text-muted flex-shrink-0"></i>
                            </div>

                            {{-- Preview catatan terbaru --}}
                            @if($u->catatans->first())
                            @php $preview = $u->catatans->first(); @endphp
                            <div style="border-top: 1px solid var(--border); padding-top: 12px;">
                                <div class="preview-label mb-1">Catatan terbaru</div>
                                <span class="label-badge mb-2">
                                    <i class="bi bi-tag-fill"></i>{{ $preview->label }}
                                </span>
                                <p class="preview-text mt-1 mb-0">{{ $preview->catatan }}</p>
                                <div class="catatan-time mt-2">
                                    <i class="bi bi-clock me-1"></i>{{ $preview->created_at->diffForHumans() }}
                                </div>
                            </div>
                            @endif
                        </a>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="empty-state">
                    <i class="bi bi-journal-x"></i>
                    <div class="fw-bold mb-1">Belum ada catatan dari pengguna manapun</div>
                    <small>Catatan akan muncul di sini setelah pengguna membuat catatan pertama mereka.</small>
                </div>
                @endif

            {{-- ════════════════════════════════════════
                 TAMPILAN USER — Input & Kelola Catatan Sendiri
            ════════════════════════════════════════ --}}
            @else

                {{-- Filter Label --}}
                <div class="filter-bar mb-4 d-flex flex-column flex-md-row gap-3 justify-content-between align-items-stretch align-items-md-center">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="text-muted small fw-semibold me-1"><i class="bi bi-filter me-1"></i>Filter:</span>
                        <button class="btn btn-sm btn-success px-3 filter-btn active" data-filter="semua">Semua</button>
                        @if(isset($catatans))
                            @foreach($catatans->pluck('label')->unique() as $lbl)
                            <button class="btn btn-sm btn-outline-success px-3 filter-btn" data-filter="{{ $lbl }}">
                                {{ $lbl }}
                            </button>
                            @endforeach
                        @endif
                    </div>
                    <span class="text-muted small flex-shrink-0" style="font-size:.82rem;">
                        <i class="bi bi-journal-text me-1"></i>{{ isset($catatans) ? $catatans->count() : 0 }} catatan
                    </span>
                </div>

                @if(isset($catatans) && $catatans->count() > 0)
                <div class="row g-3" id="catatanContainer">
                    @foreach($catatans as $c)
                    <div class="col-md-6 col-lg-4 catatan-item" data-label="{{ $c->label }}">
                        <div class="catatan-card h-100">
                            {{-- Header --}}
                            <div class="d-flex align-items-start justify-content-between mb-2 gap-2">
                                <span class="label-badge">
                                    <i class="bi bi-tag-fill"></i>{{ $c->label }}
                                </span>
                                <div class="d-flex gap-1 flex-shrink-0">
                                    <button class="btn-aksi-catatan text-success"
                                        title="Edit"
                                        onclick="openEditCatatan('{{ $c->uuid }}', '{{ addslashes($c->label) }}', '{{ addslashes($c->catatan) }}')">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <form action="{{ route('catatan.destroy', $c->uuid) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Hapus catatan ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-aksi-catatan text-danger" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            {{-- Isi --}}
                            <p class="catatan-isi mb-3">{{ $c->catatan }}</p>
                            {{-- Waktu --}}
                            <div class="catatan-time">
                                <i class="bi bi-clock me-1"></i>
                                {{ $c->created_at->isoFormat('D MMM YYYY, HH:mm') }}
                                @if($c->updated_at->ne($c->created_at))
                                    <span class="ms-2 text-warning" title="Sudah diedit">
                                        <i class="bi bi-pencil-fill"></i> diedit
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="empty-state">
                    <i class="bi bi-journal-plus"></i>
                    <div class="fw-bold mb-1">Belum ada catatan</div>
                    <small class="d-block mb-3">Klik tombol "Tambah Catatan" untuk mulai mencatat.</small>
                    <button class="btn btn-success px-4" data-bs-toggle="modal" data-bs-target="#modalTambahCatatan">
                        <i class="bi bi-plus-circle me-2"></i>Buat Catatan Pertama
                    </button>
                </div>
                @endif

            @endif
            {{-- end role check --}}

            @endauth

        </div>{{-- end container --}}
    </div>{{-- end content --}}
</div>{{-- end wrapper --}}


{{-- ══════════════════════════════════════════════════════
     MODAL: TAMBAH CATATAN (admin & user)
══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalTambahCatatan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('catatan.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-journal-plus me-2"></i>Tambah Catatan Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold small">
                            Label / Kategori <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="label" class="form-control" required maxlength="100"
                            placeholder="Contoh: Perencanaan Kegiatan, Rapat, Tugas, Ide, dll."
                            list="labelSuggestions">
                        <datalist id="labelSuggestions">
                            <option value="Perencanaan Kegiatan">
                            <option value="Rapat">
                            <option value="Tugas">
                            <option value="Ide">
                            <option value="Pengumuman">
                            <option value="Catatan Pribadi">
                            <option value="Laporan">
                        </datalist>
                        <div class="form-text">Ketik label bebas atau pilih dari saran di atas.</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">
                            Isi Catatan <span class="text-danger">*</span>
                        </label>
                        <textarea name="catatan" class="form-control" rows="6" required maxlength="5000"
                            placeholder="Tulis catatan Anda di sini..."></textarea>
                        <div class="form-text d-flex justify-content-between">
                            <span>Maksimal 5.000 karakter.</span>
                            <span id="charCount">0 / 5000</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-5 fw-bold">
                        <i class="bi bi-save me-2"></i>Simpan Catatan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     MODAL: EDIT CATATAN
══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalEditCatatan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <form id="formEditCatatan" method="POST">
                @csrf @method('PUT')
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-pencil-square me-2"></i>Edit Catatan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold small">
                            Label / Kategori <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="label" id="edit_label" class="form-control" required maxlength="100"
                            list="labelSuggestionsEdit">
                        <datalist id="labelSuggestionsEdit">
                            <option value="Perencanaan Kegiatan">
                            <option value="Rapat">
                            <option value="Tugas">
                            <option value="Ide">
                            <option value="Pengumuman">
                            <option value="Catatan Pribadi">
                            <option value="Laporan">
                        </datalist>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">
                            Isi Catatan <span class="text-danger">*</span>
                        </label>
                        <textarea name="catatan" id="edit_catatan" class="form-control" rows="6"
                            required maxlength="5000"></textarea>
                        <div class="d-flex justify-content-end">
                            <span id="editCharCount" class="form-text">0 / 5000</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-5 fw-bold">
                        <i class="bi bi-save me-2"></i>Perbarui Catatan
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

    // ── Sidebar Toggle ─────────────────────────────────────────────
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

    // ── Karakter counter (tambah) ──────────────────────────────────
    const textareaTambah = document.querySelector('#modalTambahCatatan textarea[name="catatan"]');
    const charCount      = document.getElementById('charCount');
    if (textareaTambah && charCount) {
        textareaTambah.addEventListener('input', function () {
            charCount.textContent = this.value.length + ' / 5000';
        });
    }

    // ── Karakter counter (edit) ────────────────────────────────────
    const textareaEdit   = document.getElementById('edit_catatan');
    const editCharCount  = document.getElementById('editCharCount');
    if (textareaEdit && editCharCount) {
        textareaEdit.addEventListener('input', function () {
            editCharCount.textContent = this.value.length + ' / 5000';
        });
    }

    // ── Filter label (tampilan user) ───────────────────────────────
    document.querySelectorAll('.filter-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.filter-btn').forEach(b => {
                b.classList.remove('btn-success', 'active');
                b.classList.add('btn-outline-success');
            });
            this.classList.add('btn-success', 'active');
            this.classList.remove('btn-outline-success');

            const filter = this.dataset.filter;
            document.querySelectorAll('.catatan-item').forEach(function (item) {
                if (filter === 'semua' || item.dataset.label === filter) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });

    // ── Search user card (tampilan admin) ──────────────────────────
    const searchInput = document.getElementById('searchUserCard');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const keyword = this.value.toLowerCase();
            document.querySelectorAll('.user-card-col').forEach(function (col) {
                const name = col.dataset.name || '';
                col.style.display = name.includes(keyword) ? '' : 'none';
            });
        });
    }

    // ── Buka modal edit jika ada error validasi (re-open) ─────────
    @if($errors->any() && old('_edit_uuid'))
        const editModal = new bootstrap.Modal(document.getElementById('modalEditCatatan'));
        document.getElementById('formEditCatatan').action = '/catatan/{{ old("_edit_uuid") }}';
        document.getElementById('edit_label').value   = '{{ old("label") }}';
        document.getElementById('edit_catatan').value = '{{ old("catatan") }}';
        editModal.show();
    @endif
});

// ── Buka modal edit dari tombol ─────────────────────────────────
function openEditCatatan(uuid, label, catatan) {
    const modal = new bootstrap.Modal(document.getElementById('modalEditCatatan'));
    document.getElementById('formEditCatatan').action = '/catatan/' + uuid;
    document.getElementById('edit_label').value    = label;
    document.getElementById('edit_catatan').value  = catatan;

    // Update counter
    const editCharCount = document.getElementById('editCharCount');
    if (editCharCount) editCharCount.textContent = catatan.length + ' / 5000';

    modal.show();
}
</script>
</body>
</html>
