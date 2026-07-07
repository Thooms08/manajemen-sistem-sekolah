<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Sekolah</title>
        @include('favicon')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --green-primary: #198754;
            --green-dark:    #0f5132;
            --green-soft:    #d1e7dd;
            --green-light:   #f0faf5;
            --surface:       #ffffff;
            --bg:            #f3f7f5;
            --border:        #e2ebe6;
            --text-main:     #1a2e25;
            --text-muted:    #6c8f7d;
            --shadow-sm:     0 2px 8px rgba(25,135,84,.08);
            --shadow-md:     0 6px 24px rgba(25,135,84,.12);
            --radius:        14px;
            --radius-sm:     8px;
        }

        * { box-sizing: border-box; }

        body {
            background-color: var(--bg);
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-main);
        }

        /* ── Layout ─────────────────────────────── */
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        #content  { width: 100%; padding: 24px; transition: all .3s; }

        /* ── Overlay ─────────────────────────────── */
        #overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 1040; }
        #overlay.active { display: block; }

        /* ── Page header ─────────────────────────── */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
            flex-wrap: wrap;
            gap: 12px;
        }
        .page-title {
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--green-primary);
            margin: 0;
            letter-spacing: -.3px;
        }

        /* ── Buttons ─────────────────────────────── */
        .btn-success { background-color: var(--green-primary); border: none; font-weight: 600; }
        .btn-success:hover, .btn-success:focus { background-color: #146c43; }
        .btn-add {
            background: linear-gradient(135deg, #198754, #0f5132);
            color: #fff;
            border: none;
            border-radius: var(--radius-sm);
            padding: 9px 20px;
            font-weight: 600;
            font-size: .875rem;
            transition: opacity .2s, transform .15s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-add:hover { opacity: .9; transform: translateY(-1px); color: #fff; }

        /* ── Empty state ─────────────────────────── */
        .empty-state {
            background: var(--surface);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            padding: 64px 32px;
            text-align: center;
        }
        .empty-icon {
            width: 72px; height: 72px;
            background: var(--green-light);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 18px;
            font-size: 2rem;
            color: var(--green-primary);
        }

        /* ── Profile card ── */
        .profile-card {
            background: var(--surface);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
            overflow: hidden;
            transition: box-shadow .25s, transform .2s;
            display: flex;
            flex-direction: column;
        }
        .profile-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .profile-hero {
            position: relative;
            height: 240px;
            background: linear-gradient(135deg, #0f5132 0%, #198754 60%, #34d399 100%);
            overflow: hidden;
            flex-shrink: 0;
        }
        .profile-hero img.hero-foto {
            width: 100%; height: 100%;
            object-fit: cover;
            opacity: .55;
        }
        .profile-hero .hero-overlay {
            position: absolute; inset: 0;
            background: linear-gradient(to top, rgba(15,81,50,.75) 0%, transparent 55%);
        }
        .profile-hero .acredit-badge {
            position: absolute;
            top: 18px; right: 20px;
            background: rgba(255,255,255,.18);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,.3);
            color: #fff;
            font-size: .75rem;
            font-weight: 700;
            letter-spacing: .8px;
            text-transform: uppercase;
            padding: 5px 14px;
            border-radius: 20px;
        }
        .profile-logo-wrap {
            position: absolute;
            bottom: -40px; left: 32px;
            width: 88px; height: 88px;
            background: #fff;
            border-radius: 50%;
            border: 4px solid #fff;
            box-shadow: 0 4px 16px rgba(0,0,0,.18);
            overflow: hidden;
            display: flex; align-items: center; justify-content: center;
        }
        .profile-logo-wrap img { width: 100%; height: 100%; object-fit: cover; }
        .profile-logo-wrap .logo-placeholder { font-size: 2.2rem; color: var(--green-primary); }

        .profile-body { padding: 56px 32px 24px; flex: 1; }
        .profile-name { font-size: 1.3rem; font-weight: 700; color: var(--text-main); margin-bottom: 3px; }
        .profile-nis { font-size: .82rem; color: var(--text-muted); font-weight: 500; letter-spacing: .3px; }
        .profile-divider { border: none; border-top: 1px solid var(--border); margin: 18px 0; }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
        }
        .info-row {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: .825rem;
            color: var(--text-main);
        }
        .info-row .info-icon {
            width: 32px; height: 32px; flex-shrink: 0;
            background: var(--green-light);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: var(--green-primary);
            font-size: .88rem;
        }
        .info-row .info-text { line-height: 1.4; padding-top: 6px; word-break: break-all; }
        .profile-desc {
            font-size: .83rem;
            color: var(--text-muted);
            line-height: 1.6;
            margin-top: 16px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .profile-footer {
            padding: 16px 32px;
            background: var(--green-light);
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .btn-icon {
            width: 38px; height: 38px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: var(--radius-sm);
            font-size: .9rem;
            transition: background .2s, color .2s;
            border: none;
            cursor: pointer;
        }
        .btn-icon-edit { background: #fff; color: var(--green-primary); border: 1px solid var(--border); }
        .btn-icon-edit:hover { background: var(--green-primary); color: #fff; border-color: var(--green-primary); }
        .btn-icon-delete { background: #fff; color: #dc3545; border: 1px solid #f5c6cb; }
        .btn-icon-delete:hover { background: #dc3545; color: #fff; border-color: #dc3545; }

        /* ═══════════════════════════════════════════
           MODAL - Kustomisasi Visual Bawaan Bootstrap
        ═══════════════════════════════════════════ */
        .modal-content {
            border: none;
            border-radius: var(--radius);
            box-shadow: 0 24px 64px rgba(0,0,0,.22);
        }
        .modal-header {
            background: linear-gradient(135deg, #0f5132, #198754);
            color: #fff;
            border-bottom: none;
            padding: 20px 28px;
        }
        .modal-title { font-weight: 700; font-size: 1.1rem; }
        .modal-footer {
            padding: 16px 28px;
            border-top: 1px solid var(--border);
            background: #fafafa;
        }

        .split-section-label {
            font-size: .75rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 12px;
            border-bottom: 1px solid var(--border);
            padding-bottom: 8px;
        }

        /* ── Image preview ── */
        .img-preview-box {
            background: #fff;
            border: 2px dashed var(--border);
            border-radius: var(--radius-sm);
            padding: 12px;
            text-align: center;
            transition: border-color .2s;
        }
        .img-preview-box:hover { border-color: var(--green-primary); }
        .img-preview-box img {
            width: 100%;
            height: 120px;
            object-fit: contain;
            border-radius: 6px;
            margin-bottom: 10px;
            display: block;
        }
        .img-preview-box img.img-cover { object-fit: cover; }
        .img-preview-box .no-img {
            font-size: .78rem;
            color: var(--text-muted);
            padding: 18px 0;
            display: block;
            line-height: 1.8;
        }

        /* ── Form ── */
        .form-label { font-size: .85rem; font-weight: 600; color: var(--text-main); margin-bottom: 6px; }
        .form-control {
            font-size: .875rem;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border);
            padding: 10px 14px;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-control:focus {
            border-color: var(--green-primary);
            box-shadow: 0 0 0 3px rgba(25,135,84,.12);
        }

        /* ── Save button ── */
        .btn-save {
            background: linear-gradient(135deg, #198754, #0f5132);
            color: #fff;
            border: none;
            border-radius: var(--radius-sm);
            padding: 11px 32px;
            font-weight: 700;
            font-size: .875rem;
            width: 100%;
            transition: opacity .2s;
        }
        .btn-save:hover { opacity: .9; color: #fff; }

        /* ── Responsive ── */
        @media (max-width: 991.98px) {
            .info-grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 767.98px) {
            #content { padding: 16px; }
            .profile-hero { height: 180px; }
            .profile-logo-wrap { width: 72px; height: 72px; bottom: -30px; left: 20px; }
            .profile-body { padding: 44px 20px 20px; }
            .profile-footer { padding: 14px 20px; }
            .info-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
@php
    $__canCreate = can('profile_sekolah', 'create');
    $__canEdit   = can('profile_sekolah', 'edit');
    $__canDelete = can('profile_sekolah', 'delete');
@endphp

    <div id="overlay"></div>
    <div class="wrapper">
        @include('user.sidebar')

        <div id="content">
            <div class="container-fluid">

                {{-- ── Page header ── --}}
                <div class="page-header">
                    <div class="d-flex align-items-center gap-3">
                        <button type="button" id="sidebarCollapse" class="btn btn-success">
                            <i class="bi bi-list"></i>
                        </button>
                        <h4 class="page-title">Kelola Profile Sekolah</h4>
                    </div>
                    @if($profiles->count() == 0)
                    @if($__canCreate)<button class="btn-add" data-bs-toggle="modal" data-bs-target="#modalTambah">
                        <i class="bi bi-plus-lg"></i> Tambah Profile
                    </button>@endif
                    @endif
                </div>

                {{-- Alert ditangani via SweetAlert di script --}}

                {{-- ── Profile cards or empty state ── --}}
                @if($profiles->count() == 0)
                <div class="empty-state">
                    <div class="empty-icon"><i class="bi bi-building"></i></div>
                    <h5 class="fw-700 mb-1">Belum Ada Profile Sekolah</h5>
                    <p class="text-muted mb-4" style="font-size:.875rem">Tambahkan profil sekolah untuk ditampilkan di website.</p>
                    @if($__canCreate)<button class="btn-add" data-bs-toggle="modal" data-bs-target="#modalTambah">
                        <i class="bi bi-plus-lg"></i> Tambah Profile Sekarang
                    </button>@endif
                </div>
                @else
                <div class="row g-4">
                    @foreach($profiles as $p)
                    <div class="col-12">
                        <div class="profile-card">

                            {{-- Hero --}}
                            <div class="profile-hero">
                                @if($p->foto_sekolah)
                                    <img class="hero-foto" src="{{ \App\Helpers\ImageHelper::url($p->foto_sekolah) }}" alt="Foto Sekolah">
                                @endif
                                <div class="hero-overlay"></div>
                                @if($p->akreditasi)
                                    <span class="acredit-badge"><i class="bi bi-patch-check-fill me-1"></i>Akreditasi {{ $p->akreditasi }}</span>
                                @endif
                                <div class="profile-logo-wrap">
                                    @if($p->logo)
                                        <img src="{{ \App\Helpers\ImageHelper::url($p->logo) }}" alt="Logo">
                                    @else
                                        <span class="logo-placeholder"><i class="bi bi-building"></i></span>
                                    @endif
                                </div>
                            </div>

                            {{-- Body --}}
                            <div class="profile-body">
                                <div class="profile-name">{{ $p->nama_sekolah }}</div>
                                <div class="profile-nis">NIS: {{ $p->nis }}</div>
                                <hr class="profile-divider">
                                <div class="info-grid">
                                    <div class="info-row">
                                        <span class="info-icon"><i class="bi bi-envelope-fill"></i></span>
                                        <span class="info-text">{{ $p->email }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-icon"><i class="bi bi-telephone-fill"></i></span>
                                        <span class="info-text">{{ $p->no_hp ?: '-' }}</span>
                                    </div>
                                    @if($p->alamat)
                                    <div class="info-row">
                                        <span class="info-icon"><i class="bi bi-geo-alt-fill"></i></span>
                                        <span class="info-text">{{ Str::limit($p->alamat, 100) }}</span>
                                    </div>
                                    @endif
                                </div>
                                @if($p->deskripsi)
                                <p class="profile-desc">{{ $p->deskripsi }}</p>
                                @endif
                            </div>

                            {{-- Footer --}}
                            <div class="profile-footer">
                                <button class="btn-icon btn-icon-edit" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $p->uuid }}" title="Edit">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <form action="{{ route('profile-sekolah.destroy', $p->uuid) }}" method="POST" class="d-inline" id="formHapus{{ $p->uuid }}">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn-icon btn-icon-delete" onclick="hapusProfile('formHapus{{ $p->uuid }}', '{{ addslashes($p->nama_sekolah) }}')" title="Hapus">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- ── Modal Edit ── --}}
                    {{-- DIKEMBALIKAN KE BOOTSTRAP MODAL-DIALOG-SCROLLABLE & GRID SYSTEM --}}
                    <div class="modal fade" id="modalEdit{{ $p->uuid }}" tabindex="-1">
                       <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                            <form action="{{ route('profile-sekolah.update', $p->uuid) }}" method="POST" enctype="multipart/form-data" class="w-100">
                                @csrf @method('PUT')
                                <div class="modal-content">

                                    <div class="modal-header">
                                        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Profile Sekolah</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body p-4">
                                        <div class="row g-4">
                                            
                                            {{-- LEFT: Upload Gambar --}}
                                            <div class="col-lg-4">
                                                <div class="bg-light p-3 rounded border h-100">
                                                    <div class="split-section-label">Media Sekolah</div>
                                                    
                                                    <div class="mb-4">
                                                        <label class="form-label d-block text-muted small">Logo Sekolah</label>
                                                        <div id="preview-container-logo-{{ $p->uuid }}" class="img-preview-box mb-2">
                                                            @if($p->logo)
                                                                <img src="{{ \App\Helpers\ImageHelper::url($p->logo) }}" alt="Logo">
                                                                <button type="button" class="btn btn-sm btn-danger d-block w-100 mt-1" onclick="ajaxDeleteImage('{{ $p->uuid }}', 'logo')">
                                                                    <i class="bi bi-x-circle me-1"></i>Hapus Logo
                                                                </button>
                                                            @else
                                                                <span class="no-img"><i class="bi bi-image" style="font-size:1.6rem;opacity:.35"></i><br>Belum ada logo</span>
                                                            @endif
                                                        </div>
                                                        <input type="file" name="logo" class="form-control form-control-sm" accept="image/jpeg,image/png,image/jpg">
                                                    </div>

                                                    <div>
                                                        <label class="form-label d-block text-muted small">Foto Sekolah</label>
                                                        <div id="preview-container-foto-{{ $p->uuid }}" class="img-preview-box mb-2">
                                                            @if($p->foto_sekolah)
                                                                <img class="img-cover" src="{{ \App\Helpers\ImageHelper::url($p->foto_sekolah) }}" alt="Foto">
                                                                <button type="button" class="btn btn-sm btn-danger d-block w-100 mt-1" onclick="ajaxDeleteImage('{{ $p->uuid }}', 'foto_sekolah')">
                                                                    <i class="bi bi-x-circle me-1"></i>Hapus Foto
                                                                </button>
                                                            @else
                                                                <span class="no-img"><i class="bi bi-image" style="font-size:1.6rem;opacity:.35"></i><br>Belum ada foto</span>
                                                            @endif
                                                        </div>
                                                        <input type="file" name="foto_sekolah" class="form-control form-control-sm" accept="image/jpeg,image/png,image/jpg">
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- RIGHT: Form Fields --}}
                                            <div class="col-lg-8">
                                                <div class="split-section-label">Informasi Utama</div>
                                                <div class="row g-3 mb-4">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Nama Sekolah</label>
                                                        <input type="text" name="nama_sekolah" class="form-control" value="{{ $p->nama_sekolah }}" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">NIS</label>
                                                        <input type="text" name="nis" class="form-control" value="{{ $p->nis }}" required>
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
                                                </div>

                                                <div class="split-section-label">Detail Lanjutan</div>
                                                <div class="row g-3">
                                                    <div class="col-12">
                                                        <label class="form-label">Deskripsi</label>
                                                        <textarea name="deskripsi" class="form-control" rows="4">{{ $p->deskripsi }}</textarea>
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label">Alamat Lengkap</label>
                                                        <textarea name="alamat" class="form-control" rows="3">{{ $p->alamat }}</textarea>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="submit" class="btn-save w-auto">
                                            <i class="bi bi-check-lg me-2"></i>Simpan Perubahan
                                        </button>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>

                    @endforeach
                </div>
                @endif

            </div>
        </div>
    </div>

    {{-- ── Modal Tambah ── --}}
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <form action="{{ route('profile-sekolah.store') }}" method="POST" enctype="multipart/form-data" class="w-100">
                @csrf
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Tambah Profile Sekolah</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body p-4">
                        <div class="row g-4">
                            
                            {{-- LEFT --}}
                            <div class="col-lg-4">
                                <div class="bg-light p-3 rounded border h-100">
                                    <div class="split-section-label">Media Sekolah</div>
                                    
                                    <div class="mb-4">
                                        <label class="form-label d-block text-muted small">Logo Sekolah</label>
                                        <div class="img-preview-box mb-2" id="tambah-preview-logo">
                                            <span class="no-img"><i class="bi bi-image" style="font-size:1.6rem;opacity:.35"></i><br>Unggah logo</span>
                                        </div>
                                        <input type="file" name="logo" id="tambah-input-logo" class="form-control form-control-sm" accept="image/jpeg,image/png,image/jpg" required>
                                        <div id="feedback-tambah-logo" class="text-danger small mt-1"></div>
                                    </div>
                                    <div>
                                        <label class="form-label d-block text-muted small">Foto Sekolah</label>
                                        <div class="img-preview-box mb-2" id="tambah-preview-foto">
                                            <span class="no-img"><i class="bi bi-image" style="font-size:1.6rem;opacity:.35"></i><br>Unggah foto</span>
                                        </div>
                                        <input type="file" name="foto_sekolah" id="tambah-input-foto" class="form-control form-control-sm" accept="image/jpeg,image/png,image/jpg" required>
                                        <div id="feedback-tambah-foto" class="text-danger small mt-1"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- RIGHT --}}
                            <div class="col-lg-8">
                                <div class="split-section-label">Informasi Utama</div>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Nama Sekolah</label>
                                        <input type="text" name="nama_sekolah" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">NIS</label>
                                        <input type="text" name="nis" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">No HP</label>
                                        <input type="text" name="no_hp" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Akreditasi</label>
                                        <input type="text" name="akreditasi" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Maps Link</label>
                                        <input type="text" name="tautan_google_maps" class="form-control">
                                    </div>
                                </div>

                                <div class="split-section-label">Detail Lanjutan</div>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Deskripsi</label>
                                        <textarea name="deskripsi" class="form-control" rows="4"></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Alamat</label>
                                        <textarea name="alamat" class="form-control" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn-save w-auto">
                            <i class="bi bi-check-lg me-2"></i>Simpan Data
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const MAX_SIZE = 2 * 1024 * 1024; // 2 MB

        function validateFileSize(inputEl, feedbackId) {
            const file = inputEl.files[0];
            let feedbackEl = document.getElementById(feedbackId);
            if (!feedbackEl) {
                feedbackEl = document.createElement('div');
                feedbackEl.id = feedbackId;
                feedbackEl.className = 'text-danger small mt-1';
                inputEl.insertAdjacentElement('afterend', feedbackEl);
            }
            if (file && file.size > MAX_SIZE) {
                feedbackEl.textContent = `Ukuran file terlalu besar (${(file.size / 1024 / 1024).toFixed(2)} MB). Maksimal 2 MB.`;
                inputEl.value = '';
                return false;
            }
            feedbackEl.textContent = '';
            return true;
        }

        function bindImagePreview(inputEl, containerEl, isCover = false, feedbackId = null) {
            inputEl.addEventListener('change', function () {
                const file = this.files[0];
                if (!file) return;
                if (feedbackId && !validateFileSize(inputEl, feedbackId)) {
                    containerEl.innerHTML = `<span class="no-img"><i class="bi bi-exclamation-triangle text-danger" style="font-size:1.6rem;"></i><br><span class="text-danger small">File terlalu besar</span></span>`;
                    return;
                }
                const reader = new FileReader();
                reader.onload = function (e) {
                    containerEl.innerHTML = `<img src="${e.target.result}" 
                        style="width:100%; height:120px; object-fit:${isCover ? 'cover' : 'contain'}; 
                               border-radius:6px; margin-bottom:8px; display:block;" 
                        alt="Preview">
                        <small class="text-muted d-block text-center" style="font-size:.75rem;">${file.name}</small>`;
                    const fb = feedbackId ? document.getElementById(feedbackId) : null;
                    if (fb) fb.textContent = '';
                };
                reader.readAsDataURL(file);
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            const tambahModalEl = document.getElementById('modalTambah');
            if (tambahModalEl) {
                tambahModalEl.addEventListener('shown.bs.modal', function () {
                    bindImagePreview(document.getElementById('tambah-input-logo'), document.getElementById('tambah-preview-logo'), false, 'feedback-tambah-logo');
                    bindImagePreview(document.getElementById('tambah-input-foto'), document.getElementById('tambah-preview-foto'), true, 'feedback-tambah-foto');
                });

                tambahModalEl.querySelector('form').addEventListener('submit', function (e) {
                    const logoInput = document.getElementById('tambah-input-logo');
                    const fotoInput = document.getElementById('tambah-input-foto');
                    let valid = true;
                    if (logoInput.files[0] && logoInput.files[0].size > MAX_SIZE) { validateFileSize(logoInput, 'feedback-tambah-logo'); valid = false; }
                    if (fotoInput.files[0] && fotoInput.files[0].size > MAX_SIZE) { validateFileSize(fotoInput, 'feedback-tambah-foto'); valid = false; }
                    if (!valid) { e.preventDefault(); Swal.fire({ icon: 'error', title: 'Ukuran file terlalu besar', text: 'Logo dan foto sekolah maksimal 2 MB.', confirmButtonColor: '#198754' }); }
                });

                tambahModalEl.addEventListener('hidden.bs.modal', function () {
                    document.getElementById('tambah-preview-logo').innerHTML = `<span class="no-img"><i class="bi bi-image" style="font-size:1.6rem;opacity:.35"></i><br>Unggah logo</span>`;
                    document.getElementById('tambah-preview-foto').innerHTML = `<span class="no-img"><i class="bi bi-image" style="font-size:1.6rem;opacity:.35"></i><br>Unggah foto</span>`;
                    ['feedback-tambah-logo','feedback-tambah-foto'].forEach(id => { const el = document.getElementById(id); if (el) el.textContent = ''; });
                });
            }

            document.querySelectorAll('[id^="modalEdit"]').forEach(function (modalEl) {
                const id = modalEl.id.replace('modalEdit', '');
                modalEl.addEventListener('shown.bs.modal', function () {
                    const inputLogo = modalEl.querySelector('input[name="logo"]');
                    const inputFoto = modalEl.querySelector('input[name="foto_sekolah"]');
                    const containerLogo = document.getElementById('preview-container-logo-' + id);
                    const containerFoto = document.getElementById('preview-container-foto-' + id);
                    if (inputLogo && containerLogo) bindImagePreview(inputLogo, containerLogo, false, `feedback-edit-logo-${id}`);
                    if (inputFoto && containerFoto) bindImagePreview(inputFoto, containerFoto, true, `feedback-edit-foto-${id}`);
                });

                const editForm = modalEl.querySelector('form');
                if (editForm) {
                    editForm.addEventListener('submit', function (e) {
                        const inputLogo = modalEl.querySelector('input[name="logo"]');
                        const inputFoto = modalEl.querySelector('input[name="foto_sekolah"]');
                        let valid = true;
                        if (inputLogo && inputLogo.files[0] && inputLogo.files[0].size > MAX_SIZE) { validateFileSize(inputLogo, `feedback-edit-logo-${id}`); valid = false; }
                        if (inputFoto && inputFoto.files[0] && inputFoto.files[0].size > MAX_SIZE) { validateFileSize(inputFoto, `feedback-edit-foto-${id}`); valid = false; }
                        if (!valid) { e.preventDefault(); Swal.fire({ icon: 'error', title: 'Ukuran file terlalu besar', text: 'Logo dan foto sekolah maksimal 2 MB.', confirmButtonColor: '#198754' }); }
                    });
                }
            });

            @if(session('success'))
                Swal.fire({ icon: 'success', title: 'Berhasil', text: @json(session('success')), confirmButtonColor: '#198754', timer: 3000, timerProgressBar: true });
            @endif
            @if(session('error'))
                Swal.fire({ icon: 'error', title: 'Gagal', text: @json(session('error')), confirmButtonColor: '#198754' });
            @endif
            @if($errors->any())
                Swal.fire({ icon: 'error', title: 'Validasi Gagal', html: `<ul class="text-start small mb-0 ps-3">{!! implode('', array_map(fn($e) => '<li>'.$e.'</li>', $errors->all())) !!}</ul>`, confirmButtonColor: '#198754' });
            @endif
        });

        function ajaxDeleteImage(id, type) {
            const label = type === 'logo' ? 'Logo Sekolah' : 'Foto Sekolah';
            Swal.fire({
                title: `Hapus ${label}?`,
                text: 'Gambar akan dihapus permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
            }).then(result => {
                if (!result.isConfirmed) return;
                const containerId = type === 'logo' ? `preview-container-logo-${id}` : `preview-container-foto-${id}`;
                const container = document.getElementById(containerId);
                fetch(`/profile-sekolah/delete-image/${id}?type=${type}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        container.innerHTML = `<span class="no-img"><i class="bi bi-image" style="font-size:1.6rem;opacity:.35"></i><br>Gambar dihapus.<br>Unggah baru jika diperlukan.</span>`;
                        Swal.fire({ icon: 'success', title: 'Berhasil', text: `${label} berhasil dihapus.`, confirmButtonColor: '#198754', timer: 2000, timerProgressBar: true });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: data.message, confirmButtonColor: '#198754' });
                    }
                })
                .catch(() => { Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan saat menghapus gambar.', confirmButtonColor: '#198754' }); });
            });
        }

        function hapusProfile(formId, namaSekolah) {
            Swal.fire({
                title: 'Hapus Profile?',
                html: `Data profile <strong>${namaSekolah}</strong> beserta semua gambar akan dihapus permanen.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
            }).then(result => { if (result.isConfirmed) document.getElementById(formId).submit(); });
        }

        const sidebar     = document.getElementById('sidebar');
        const collapseBtn = document.getElementById('sidebarCollapse');
        const closeBtn    = document.getElementById('close-sidebar');
        const overlay     = document.getElementById('overlay');

        function toggle() {
            if (window.innerWidth <= 768) { sidebar.classList.toggle('show-mobile'); overlay.classList.toggle('active'); }
            else { sidebar.classList.toggle('inactive'); }
        }
        collapseBtn.onclick = toggle;
        closeBtn.onclick    = toggle;
        overlay.onclick     = toggle;
    </script>
</body>
</html>