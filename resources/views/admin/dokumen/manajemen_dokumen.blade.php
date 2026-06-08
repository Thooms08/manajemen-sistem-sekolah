<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Dokumen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f4f7f6; }
        .wrapper { display: flex; width: 100%; }
        #content { width: 100%; padding: 20px 30px; transition: all 0.3s; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        #sidebarCollapse { width: 40px; height: 40px; background: #198754; border: none; color: white; border-radius: 8px; }
        
        /* Grid File Styling */
        .file-box { width: 140px; border: 1px solid #e0e0e0; border-radius: 12px; background: white; transition: 0.2s; cursor: pointer; position: relative; z-index: 1; }
        .file-box:hover { background-color: #f1f9f5; border-color: #198754; box-shadow: 0 4px 10px rgba(25,135,84,0.1); z-index: 50; }
        .file-icon { height: 70px; display: flex; align-items: center; justify-content: center; }
        .file-thumbnail { width: 100%; height: 70px; object-fit: cover; border-radius: 8px; }
        .file-name { font-size: 0.85rem; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; padding: 0 5px; }
        .dropdown-menu { min-width: 120px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); border: none; }
        .btn-kebab { position: absolute; top: 5px; right: 5px; z-index: 10; background: rgba(255,255,255,0.7); border-radius: 50%; }
        .search-box-wrapper { width: 300px; }
        .search-box-wrapper .input-group-text { border-radius: 8px 0 0 8px; border: 1px solid #ced4da; }
        .search-box-wrapper input { border-radius: 0 8px 8px 0; border: 1px solid #ced4da; }
        .search-box-wrapper input:focus { box-shadow: none; border-color: #198754; }
        #overlay { display: none; position: fixed; width: 100vw; height: 100vh; background: rgba(0, 0, 0, 0.5); z-index: 1040; top: 0; left: 0; }
        #overlay.active { display: block; }
        input:focus, textarea:focus, select:focus { border-color: #198754 !important; outline: none !important; box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25) !important;}
    </style>
</head>
<body>
    @include('loading')
<div id="overlay"></div>
    <div class="wrapper">
        @include('admin.sidebar')
        <div id="content">
            <div class="container-fluid">
                <div class="d-flex align-items-center justify-content-between mb-4 mt-2">
                    <div class="d-flex align-items-center">
                        <button type="button" id="sidebarCollapse" class="btn"><i class="bi bi-list fs-5"></i></button>
                        <h4 class="ms-3 mb-0 fw-bold text-success">Drive Sekolah</h4>
                    </div>
                    <div>
                        <button class="btn btn-outline-success fw-bold me-2" data-bs-toggle="modal" data-bs-target="#modalBuatFolder">
                            <i class="bi bi-folder-plus"></i> Folder Baru
                        </button>
                        <button class="btn btn-success fw-bold shadow-sm" onclick="document.getElementById('input-upload').click()">
                            <i class="bi bi-cloud-arrow-up"></i> Upload File
                        </button>
                        <form id="form-upload" action="{{ route('dokumen.file.store') }}" method="POST" enctype="multipart/form-data" class="d-none">
                            @csrf
                            <input type="hidden" name="parent_id" value="{{ isset($folder) ? $folder->id : '' }}">
                            <input type="file" id="input-upload" name="file" onchange="validateAndUpload(this)">
                        </form>
                    </div>
                </div>

                <nav aria-label="breadcrumb">
                    <div class="breadcrumb mb-4 p-3 bg-white rounded shadow-sm d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <span class="breadcrumb-item fw-bold text-success">
                                <i class="bi bi-hdd-fill me-1"></i> My Drive
                            </span>
                            @if(isset($folder))
                                <span class="breadcrumb-item active ms-2">{{ $folder->nama }}</span>
                            @endif
                        </div>

                        <div class="search-box-wrapper">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <input type="text" id="search-dokumen" class="form-control border-start-0" placeholder="Cari dokumen...">
                            </div>
                        </div>
                    </div>
                </nav>

                <div class="d-flex flex-wrap gap-3">
                    @if($items->isEmpty())
                        <div class="w-100 text-center text-muted py-5">
                            <i class="bi bi-folder-x display-1"></i>
                            <p class="mt-3">Belum ada file atau folder di sini.</p>
                        </div>
                    @else
                       @foreach($items as $item)
                            <div class="file-box p-2 text-center" onclick="handleItemClick(event, '{{ $item->tipe }}', '{{ $item->uuid }}', '{{ addslashes($item->nama) }}', '{{ $item->tipe === 'file' ? \URL::temporarySignedRoute('dokumen.view', now()->addMinutes(30), ['uuid' => $item->uuid]) : '' }}', '{{ $item->ekstensi }}')">
                                
                              <div class="dropdown btn-kebab" onclick="event.stopPropagation()"> 
                                    <button type="button" class="btn btn-sm btn-link text-dark" onclick="toggleDropdown(this)">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item small" href="#" onclick="openRenameModal('{{ $item->uuid }}', '{{ $item->nama }}')"><i class="bi bi-pencil me-2"></i>Rename</a></li>
                                        <li>
                                           <form action="{{ route('dokumen.destroy', $item->uuid) }}" method="POST" class="form-hapus">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger small"><i class="bi bi-trash me-2"></i>Hapus</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                                
                                <div class="file-icon mt-3">
                                    @if($item->tipe == 'folder')
                                        <i class="bi bi-folder-fill text-warning" style="font-size: 3.5rem;"></i>
                                    @else
                                        @php
                                            $ext = $item->ekstensi;
                                            $icon = 'bi-file-earmark-fill text-secondary';
                                            
                                            if(in_array($ext, ['pdf'])) $icon = 'bi-file-earmark-pdf-fill text-danger';
                                            elseif(in_array($ext, ['doc', 'docx'])) $icon = 'bi-file-earmark-word-fill text-primary';
                                            elseif(in_array($ext, ['xls', 'xlsx', 'csv'])) $icon = 'bi-file-earmark-excel-fill text-success';
                                            elseif(in_array($ext, ['mp3', 'wav'])) $icon = 'bi-file-earmark-music-fill text-info';
                                            $signedUrl = \URL::temporarySignedRoute('dokumen.view', now()->addMinutes(30), ['uuid' => $item->uuid]);
                                        @endphp
                                        
                                        @if(in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp']))
                                            <img src="{{ $signedUrl }}" class="file-thumbnail" alt="img">
                                        @elseif(in_array($ext, ['mp4', 'webm']))
                                            <video src="{{ $signedUrl }}" class="file-thumbnail" preload="metadata"></video>
                                        @else
                                            <i class="bi {{ $icon }}" style="font-size: 3.5rem;"></i>
                                        @endif
                                    @endif
                                </div>
                                <div class="file-name mt-2" title="{{ $item->nama }}">{{ $item->nama }}</div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

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
                    <label class="form-label fw-bold">Nama Folder</label>
                    <input type="text" name="nama" class="form-control" placeholder="Masukkan nama folder..." required autofocus>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">Buat</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modalRename" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form id="formRename" method="POST" class="modal-content border-0 shadow">
                @csrf @method('PUT')
                <div class="modal-header bg-success">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Ganti Nama</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <label class="form-label fw-bold">Nama Baru</label>
                    <input type="text" id="inputRename" name="nama" class="form-control" required>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modalPreview" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title text-truncate" id="previewTitle" style="max-width: 80%;">Preview</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0 text-center bg-light" id="previewContainer" style="min-height: 400px; display: flex; align-items: center; justify-content: center;">
                    </div>
                <div class="modal-footer bg-white">
                    <a href="#" id="btnDownload" class="btn btn-success rounded-pill px-4 fw-bold">
                        <i class="bi bi-download me-2"></i> Unduh File
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Upload File Validation (10MB Max)
        function validateAndUpload(input) {
            if (input.files && input.files[0]) {
                const fileSize = input.files[0].size / 1024 / 1024; // in MB
                if (fileSize > 10) {
                    alert('Gagal! Ukuran file maksimal adalah 10MB.');
                    input.value = '';
                    return;
                }

                showLoading();
                setTimeout(function() {
                    document.getElementById('form-upload').submit();
                }, 50);
            }
        }

        function handleItemClick(e, tipe, uuid, nama, url, ext) {
            if (e.target.closest('.btn-kebab')) {
                return; 
            }

            if (tipe === 'folder') {
                window.location.href = "{{ url('dokumen/folder') }}/" + uuid;
            } else {
                showPreview(nama, url, ext, uuid);
            }
        }

        // Buka Modal Rename
       function openRenameModal(uuid, namaLama) {
            $('#formRename').attr('action', "{{ url('dokumen/rename') }}/" + uuid);
            $('#inputRename').val(namaLama);
            new bootstrap.Modal(document.getElementById('modalRename')).show();
        }

        // Tampilkan Preview Berdasarkan Tipe
        function showPreview(nama, url, ext, uuid) {
            $('#previewTitle').text(nama + '.' + ext);
            $('#btnDownload').attr('href', "{{ url('dokumen/download') }}/" + uuid);
            
            const imageExts = ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp'];
            const videoExts = ['mp4', 'webm'];
            const audioExts = ['mp3', 'wav', 'ogg'];
            const officeExts = ['doc', 'docx', 'xls', 'xlsx', 'csv', 'ppt', 'pptx'];

            const mimeMap = {
                mp4: 'video/mp4', webm: 'video/webm',
                mp3: 'audio/mpeg', wav: 'audio/wav', ogg: 'audio/ogg'
            };

            let htmlContent;

            if (ext === 'pdf') {
                htmlContent = `<iframe src="${url}" width="100%" height="500px" style="border:none;"></iframe>`;
            } else if (imageExts.includes(ext)) {
                htmlContent = `<img src="${url}" class="img-fluid p-2" style="max-height:70vh;" alt="preview">`;
            } else if (videoExts.includes(ext)) {
                const mime = mimeMap[ext] || 'video/mp4';
                htmlContent = `<video controls style="width:100%;max-height:70vh;"><source src="${url}" type="${mime}">Browser Anda tidak mendukung tag video.</video>`;
            } else if (audioExts.includes(ext)) {
                const mime = mimeMap[ext] || 'audio/mpeg';
                htmlContent = `<div class="p-4 w-100"><audio controls style="width:80%;"><source src="${url}" type="${mime}">Browser Anda tidak mendukung tag audio.</audio></div>`;
            } else if (officeExts.includes(ext)) {
                htmlContent = `<div class="text-center p-5">
                    <i class="bi bi-file-earmark-text display-1 text-primary"></i>
                    <p class="mt-3 fw-semibold">${nama}.${ext}</p>
                    <p class="text-muted small">Pratinjau tidak tersedia untuk file Office.<br>Silakan unduh file untuk membukanya.</p>
                    <a href="{{ url('dokumen/download') }}/${uuid}" class="btn btn-primary mt-2">
                        <i class="bi bi-download me-2"></i>Unduh File
                    </a>
                </div>`;
            } else {
                htmlContent = `<div class="text-muted p-4">
                    <i class="bi bi-file-earmark-x display-1"></i>
                    <p class="mt-3">Pratinjau tidak tersedia untuk format <strong>.${ext}</strong></p>
                </div>`;
            }

            $('#previewContainer').html(htmlContent);
            new bootstrap.Modal(document.getElementById('modalPreview')).show();
        }

        function toggleDropdown(button) {
            const dropdown = bootstrap.Dropdown.getOrCreateInstance(button);
            dropdown.toggle();
        }
        $(document).ready(function() {
            $('#modalBuatFolder form, #formRename').on('submit', function() {
                showLoading();
                $(this).find('button[type="submit"]').prop('disabled', true).text('Memproses...');
            });
            $('.form-hapus').on('submit', function(e) {
                e.preventDefault();
                var form = this;
                if (confirm('Apakah Anda yakin ingin menghapus item ini?')) {
                    showLoading(); 
                    setTimeout(function() {
                        form.submit();
                    }, 50);
                }
            });
            $('#search-dokumen').on('keyup', function() {
                var searchTerm = $(this).val().toLowerCase();
                
                // Loop setiap file-box
                $('.file-box').each(function() {
                    var fileName = $(this).find('.file-name').text().toLowerCase();
                    
                    // Tampilkan jika cocok, sembunyikan jika tidak
                    if (fileName.includes(searchTerm)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
        });
        $(document).ready(function() {
    // --- TAMBAHKAN LOGIKA SIDEBAR DI SINI ---
    const sidebar = $('#sidebar');
    const overlay = $('#overlay');

    function toggleSidebar() {
        if ($(window).width() <= 768) {
            sidebar.toggleClass('show-mobile');
            overlay.toggleClass('active');
        } else {
            sidebar.toggleClass('inactive');
        }
    }
    $('#sidebarCollapse, #close-sidebar, #overlay').on('click', toggleSidebar);
    $('#search-dokumen').on('keyup', function() {
        var searchTerm = $(this).val().toLowerCase();
        
        $('.file-box').each(function() {
            var fileName = $(this).find('.file-name').text().toLowerCase();
            if (fileName.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
});
function showLoading() {
   $('#global-loading-overlay').show();
}
    </script>
</body>
</html>