<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Dokumen</title>
        @include('favicon')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary-green: #198754; }
        body { background-color: #f4f7f6; font-family: 'Inter', sans-serif; overflow-x: hidden; }
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        #content { width: 100%; padding: 20px 30px; transition: all 0.3s; min-height: 100vh; min-width: 0; }

        /* Sidebar button */
        #sidebarCollapse {
            width: 40px; height: 40px; background: var(--primary-green);
            border: none; color: white; border-radius: 8px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        #overlay { display: none; position: fixed; width: 100vw; height: 100vh;
            background: rgba(0,0,0,0.5); z-index: 1040; top: 0; left: 0; }
        #overlay.active { display: block; }

        /* ── File Grid — responsive columns ── */
        .file-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
            gap: 12px;
        }
        .file-box {
            width: 100%; border: 1px solid #e0e0e0; border-radius: 12px;
            background: white; transition: 0.2s; cursor: pointer;
            position: relative; z-index: 1;
        }
        .file-box:hover {
            background-color: #f1f9f5; border-color: var(--primary-green);
            box-shadow: 0 4px 10px rgba(25,135,84,0.12); z-index: 50;
        }
        .file-icon { height: 70px; display: flex; align-items: center; justify-content: center; }
        .file-thumbnail { width: 100%; height: 70px; object-fit: cover; border-radius: 8px; }
        .file-name {
            font-size: 0.82rem; font-weight: 500;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            padding: 0 6px; color: #374151;
        }
        .btn-kebab {
            position: absolute; top: 5px; right: 5px; z-index: 10;
            background: rgba(255,255,255,0.8); border-radius: 50%;
        }
        .dropdown-menu { min-width: 130px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border: none; border-radius: 10px; }

        /* ── Breadcrumb / toolbar bar ── */
        .toolbar-bar {
            background: #fff; border-radius: 12px; padding: 12px 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05); margin-bottom: 1.25rem;
            display: flex; align-items: center; flex-wrap: wrap; gap: 10px;
        }
        .search-box-wrapper { flex: 1 1 180px; max-width: 320px; }
        .search-box-wrapper .input-group-text { border-radius: 8px 0 0 8px; }
        .search-box-wrapper input { border-radius: 0 8px 8px 0; }
        .search-box-wrapper input:focus { box-shadow: none; border-color: var(--primary-green); }

        input:focus, textarea:focus, select:focus {
            border-color: var(--primary-green) !important;
            outline: none !important;
            box-shadow: 0 0 0 0.2rem rgba(25,135,84,0.2) !important;
        }

        /* Empty state */
        .empty-state { grid-column: 1 / -1; text-align: center; padding: 3rem 1rem; color: #9ca3af; }
        .empty-state i { font-size: 3.5rem; display: block; margin-bottom: 0.75rem; }

        /* ── Responsive ── */
        @media (min-width: 576px) {
            .file-grid { grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 14px; }
        }
        @media (min-width: 768px) {
            .file-grid { grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 16px; }
        }
        @media (max-width: 991px) {
            #content { padding: 16px 18px; }
        }
        @media (max-width: 767px) {
            #content { padding: 12px 12px; }
            /* Header stack vertikal */
            .page-header { flex-direction: column; align-items: flex-start !important; }
            .page-header .btn-group-action { width: 100%; display: flex; gap: 8px; }
            .page-header .btn-group-action .btn { flex: 1; }
            /* Search wrapper full width */
            .search-box-wrapper { max-width: 100%; }
            /* Toolbar bar stack */
            .toolbar-bar { flex-direction: column; align-items: stretch; }
        }
    </style>
</head>
<body>
    @include('loading')
    <div id="overlay"></div>
    <div class="wrapper">
        @include('admin.sidebar')
        <div id="content">
            <div class="container-fluid">

                {{-- Header --}}
                <div class="d-flex align-items-center justify-content-between mb-4 mt-1 flex-wrap gap-2 page-header">
                    <div class="d-flex align-items-center gap-3">
                        <button type="button" id="sidebarCollapse" class="btn">
                            <i class="bi bi-list fs-5"></i>
                        </button>
                        <div>
                            <h4 class="mb-0 fw-bold text-success">Drive Sekolah</h4>
                            <p class="text-muted small mb-0 d-none d-sm-block">Kelola file dan folder dokumen sekolah</p>
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap btn-group-action">
                        <button class="btn btn-outline-success fw-semibold" data-bs-toggle="modal" data-bs-target="#modalBuatFolder">
                            <i class="bi bi-folder-plus me-1"></i>Folder Baru
                        </button>
                        <button class="btn btn-success fw-semibold shadow-sm" onclick="document.getElementById('input-upload').click()">
                            <i class="bi bi-cloud-arrow-up me-1"></i>Upload File
                        </button>
                        <form id="form-upload" action="{{ route('dokumen.file.store') }}" method="POST" enctype="multipart/form-data" class="d-none">
                            @csrf
                            <input type="hidden" name="parent_id" value="{{ isset($folder) ? $folder->id : '' }}">
                            <input type="file" id="input-upload" name="file" onchange="validateAndUpload(this)">
                        </form>
                    </div>
                </div>

                {{-- Toolbar: breadcrumb + search --}}
                <div class="toolbar-bar">
                    <div class="d-flex align-items-center gap-2 flex-grow-1">
                        <i class="bi bi-hdd-fill text-success fs-5"></i>
                        <span class="fw-semibold text-success">My Drive</span>
                        @if(isset($folder))
                            <i class="bi bi-chevron-right text-muted small"></i>
                            <span class="text-dark fw-semibold">{{ $folder->nama }}</span>
                        @endif
                    </div>
                    <div class="search-box-wrapper">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text" id="search-dokumen" class="form-control border-start-0 ps-0" placeholder="Cari dokumen...">
                        </div>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success border-0 shadow-sm mb-3 py-2">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    </div>
                @endif

                {{-- File Grid --}}
                <div class="file-grid" id="file-grid-container">
                    @if($items->isEmpty())
                        <div class="empty-state">
                            <i class="bi bi-folder-x"></i>
                            <p class="fw-semibold">Belum ada file atau folder di sini.</p>
                            <p class="small">Buat folder baru atau upload file untuk memulai.</p>
                        </div>
                    @else
                        @foreach($items as $item)
                        <div class="file-box p-2 text-center"
                             onclick="handleItemClick(event, '{{ $item->tipe }}', '{{ $item->uuid }}', '{{ addslashes($item->nama) }}', '{{ $item->tipe === 'file' ? \URL::temporarySignedRoute('dokumen.view', now()->addMinutes(30), ['uuid' => $item->uuid]) : '' }}', '{{ $item->ekstensi }}')">

                            <div class="dropdown btn-kebab" onclick="event.stopPropagation()">
                                <button type="button" class="btn btn-sm btn-link text-dark p-1" onclick="toggleDropdown(this)">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item small" href="#" onclick="openRenameModal('{{ $item->uuid }}', '{{ $item->nama }}')">
                                            <i class="bi bi-pencil me-2 text-success"></i>Rename
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <form action="{{ route('dokumen.destroy', $item->uuid) }}" method="POST" class="form-hapus">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger small">
                                                <i class="bi bi-trash me-2"></i>Hapus
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>

                            <div class="file-icon mt-3">
                                @if($item->tipe == 'folder')
                                    <i class="bi bi-folder-fill text-warning" style="font-size: 3rem;"></i>
                                @else
                                    @php
                                        $ext = $item->ekstensi;
                                        $icon = 'bi-file-earmark-fill text-secondary';
                                        if(in_array($ext, ['pdf'])) $icon = 'bi-file-earmark-pdf-fill text-danger';
                                        elseif(in_array($ext, ['doc','docx'])) $icon = 'bi-file-earmark-word-fill text-primary';
                                        elseif(in_array($ext, ['xls','xlsx','csv'])) $icon = 'bi-file-earmark-excel-fill text-success';
                                        elseif(in_array($ext, ['mp3','wav'])) $icon = 'bi-file-earmark-music-fill text-info';
                                        $signedUrl = \URL::temporarySignedRoute('dokumen.view', now()->addMinutes(30), ['uuid' => $item->uuid]);
                                    @endphp
                                    @if(in_array($ext, ['png','jpg','jpeg','gif','svg','webp']))
                                        <img src="{{ $signedUrl }}" class="file-thumbnail" alt="img">
                                    @elseif(in_array($ext, ['mp4','webm']))
                                        <video src="{{ $signedUrl }}" class="file-thumbnail" preload="metadata"></video>
                                    @else
                                        <i class="bi {{ $icon }}" style="font-size: 3rem;"></i>
                                    @endif
                                @endif
                            </div>
                            <div class="file-name mt-2 pb-2" title="{{ $item->nama }}">{{ $item->nama }}</div>
                        </div>
                        @endforeach
                    @endif
                </div>

            </div>
        </div>
    </div>

    {{-- Modal: Buat Folder --}}
    <div class="modal fade" id="modalBuatFolder" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('dokumen.folder.store') }}" method="POST" class="modal-content border-0 shadow">
                @csrf
                <input type="hidden" name="parent_id" value="{{ isset($folder) ? $folder->id : '' }}">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-folder-plus me-2"></i>Buat Folder Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <label class="form-label fw-semibold">Nama Folder <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control" placeholder="Contoh: Dokumen 2025" required autofocus>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4 fw-semibold">Buat Folder</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: Rename --}}
    <div class="modal fade" id="modalRename" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form id="formRename" method="POST" class="modal-content border-0 shadow">
                @csrf @method('PUT')
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Ganti Nama</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <label class="form-label fw-semibold">Nama Baru <span class="text-danger">*</span></label>
                    <input type="text" id="inputRename" name="nama" class="form-control" required>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4 fw-semibold">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: Preview File --}}
    <div class="modal fade" id="modalPreview" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-dark text-white">
                    <h6 class="modal-title text-truncate fw-semibold" id="previewTitle" style="max-width:80%;">Preview</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0 text-center bg-light d-flex align-items-center justify-content-center"
                     id="previewContainer" style="min-height: 350px;">
                </div>
                <div class="modal-footer bg-white">
                    <a href="#" id="btnDownload" class="btn btn-success px-4 fw-semibold">
                        <i class="bi bi-download me-2"></i>Unduh File
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    $(document).ready(function () {

        // ── Sidebar Toggle ────────────────────────────────────────
        function toggleSidebar() {
            if ($(window).width() <= 768) {
                $('#sidebar').toggleClass('show-mobile');
                $('#overlay').toggleClass('active');
            } else {
                $('#sidebar').toggleClass('inactive');
            }
        }
        $('#sidebarCollapse, #close-sidebar, #overlay').on('click', toggleSidebar);

        // ── Search ────────────────────────────────────────────────
        $('#search-dokumen').on('keyup', function () {
            const q = $(this).val().toLowerCase();
            $('.file-box').each(function () {
                const name = $(this).find('.file-name').text().toLowerCase();
                $(this).toggle(name.includes(q));
            });
        });

        // ── Form submit handlers ──────────────────────────────────
        $('#modalBuatFolder form, #formRename').on('submit', function () {
            showLoading();
            $(this).find('button[type="submit"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Memproses...');
        });

        $('.form-hapus').on('submit', function (e) {
            e.preventDefault();
            const form = this;
            if (confirm('Apakah Anda yakin ingin menghapus item ini? Tindakan ini tidak dapat dibatalkan.')) {
                showLoading();
                setTimeout(() => form.submit(), 50);
            }
        });

    });

    // ── Upload Validation ─────────────────────────────────────────
    function validateAndUpload(input) {
        if (input.files && input.files[0]) {
            if (input.files[0].size / 1024 / 1024 > 10) {
                alert('Gagal! Ukuran file maksimal adalah 10MB.');
                input.value = '';
                return;
            }
            showLoading();
            setTimeout(() => document.getElementById('form-upload').submit(), 50);
        }
    }

    // ── Item Click Handler ────────────────────────────────────────
    function handleItemClick(e, tipe, uuid, nama, url, ext) {
        if (e.target.closest('.btn-kebab')) return;
        if (tipe === 'folder') {
            window.location.href = "{{ url('dokumen/folder') }}/" + uuid;
        } else {
            showPreview(nama, url, ext, uuid);
        }
    }

    // ── Rename Modal ──────────────────────────────────────────────
    function openRenameModal(uuid, namaLama) {
        $('#formRename').attr('action', "{{ url('dokumen/rename') }}/" + uuid);
        $('#inputRename').val(namaLama);
        new bootstrap.Modal(document.getElementById('modalRename')).show();
    }

    // ── Preview Modal ─────────────────────────────────────────────
    function showPreview(nama, url, ext, uuid) {
        $('#previewTitle').text(nama + (ext ? '.' + ext : ''));
        $('#btnDownload').attr('href', "{{ url('dokumen/download') }}/" + uuid);

        const imgExts    = ['png','jpg','jpeg','gif','svg','webp'];
        const videoExts  = ['mp4','webm'];
        const audioExts  = ['mp3','wav','ogg'];
        const officeExts = ['doc','docx','xls','xlsx','csv','ppt','pptx'];
        const mimeMap    = { mp4:'video/mp4', webm:'video/webm', mp3:'audio/mpeg', wav:'audio/wav', ogg:'audio/ogg' };

        let html;
        if (ext === 'pdf') {
            html = `<iframe src="${url}" width="100%" style="height:70vh;border:none;"></iframe>`;
        } else if (imgExts.includes(ext)) {
            html = `<img src="${url}" class="img-fluid p-2" style="max-height:70vh;" alt="preview">`;
        } else if (videoExts.includes(ext)) {
            html = `<video controls style="width:100%;max-height:70vh;"><source src="${url}" type="${mimeMap[ext]||'video/mp4'}">Browser tidak mendukung.</video>`;
        } else if (audioExts.includes(ext)) {
            html = `<div class="p-4 w-100 text-center"><audio controls style="width:90%;"><source src="${url}" type="${mimeMap[ext]||'audio/mpeg'}">Browser tidak mendukung.</audio></div>`;
        } else if (officeExts.includes(ext)) {
            html = `<div class="text-center p-5">
                <i class="bi bi-file-earmark-text display-1 text-primary"></i>
                <p class="mt-3 fw-semibold">${nama}.${ext}</p>
                <p class="text-muted small">Pratinjau tidak tersedia untuk file Office.</p>
                <a href="{{ url('dokumen/download') }}/${uuid}" class="btn btn-primary mt-1 px-4">
                    <i class="bi bi-download me-2"></i>Unduh File
                </a></div>`;
        } else {
            html = `<div class="text-muted p-5">
                <i class="bi bi-file-earmark-x display-1"></i>
                <p class="mt-3">Pratinjau tidak tersedia untuk format <strong>.${ext}</strong></p></div>`;
        }

        $('#previewContainer').html(html);
        new bootstrap.Modal(document.getElementById('modalPreview')).show();
    }

    function toggleDropdown(button) {
        bootstrap.Dropdown.getOrCreateInstance(button).toggle();
    }

    function showLoading() {
        $('#global-loading-overlay').show();
    }
    </script>
</body>
</html>
