<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hak Akses: {{ $role->nama }}</title>
    @if(isset($sekolah->logo))
    <link rel="icon" type="image/png" href="{{ \App\Helpers\ImageHelper::url($sekolah->logo) }}">
    @else
    <link rel="icon" type="image/png" href="{{ asset('assets/img/default-favicon.png') }}">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #198754; }
        body { font-family: 'Inter', sans-serif; background: #f4f7f6; }
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        #content { width: 100%; padding: 24px 30px; min-height: 100vh; }
        #sidebarCollapse { width: 45px; height: 45px; background: var(--primary); border: none; color: white; border-radius: 10px; }
        /* Permission table */
        .group-header { background: #f0fdf4; padding: 10px 16px; border-radius: 8px; font-weight: 700; font-size: .82rem; letter-spacing: .4px; text-transform: uppercase; color: var(--primary); margin-bottom: 4px; display: flex; align-items: center; justify-content: space-between; }
        .perm-row { display: flex; align-items: center; padding: 10px 16px; border: 1px solid #f0f0f0; border-radius: 8px; margin-bottom: 6px; background: white; gap: 12px; transition: .15s; }
        .perm-row:hover { border-color: #c3e6cb; background: #f9fff9; }
        .perm-modul-name { flex: 1; font-size: .88rem; }
        .perm-actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .aksi-check { display: none; }
        .aksi-label {
            padding: 4px 12px; border-radius: 50px; font-size: .78rem; font-weight: 600;
            cursor: pointer; border: 1.5px solid #dee2e6; color: #666; background: white;
            transition: .15s; user-select: none;
        }
        .aksi-label:hover { border-color: var(--primary); color: var(--primary); }
        .aksi-check:checked + .aksi-label { color: white; border-color: transparent; }
        .aksi-check[data-aksi="view"]:checked + .aksi-label    { background: #0dcaf0; }
        .aksi-check[data-aksi="create"]:checked + .aksi-label  { background: var(--primary); }
        .aksi-check[data-aksi="edit"]:checked + .aksi-label    { background: #ffc107; color: #333 !important; }
        .aksi-check[data-aksi="delete"]:checked + .aksi-label  { background: #dc3545; }
        .aksi-check[data-aksi="view"]:not(:checked) + .aksi-label    { border-color: #0dcaf0; color: #0dcaf0; }
        .aksi-check[data-aksi="create"]:not(:checked) + .aksi-label  { border-color: var(--primary); color: var(--primary); }
        .aksi-check[data-aksi="edit"]:not(:checked) + .aksi-label    { border-color: #ffc107; color: #856404; }
        .aksi-check[data-aksi="delete"]:not(:checked) + .aksi-label  { border-color: #dc3545; color: #dc3545; }
        .sticky-save { position: sticky; bottom: 0; background: white; border-top: 1px solid #e9ecef; padding: 16px 24px; margin: 0 -30px; z-index: 10; }
        #overlay { display: none; position: fixed; width: 100vw; height: 100vh; background: rgba(0,0,0,.5); z-index: 1040; top: 0; left: 0; }
        #overlay.active { display: block; }
        @media (max-width: 768px) { #content { padding: 15px; } .sticky-save { margin: 0 -15px; } }
    </style>
</head>
<body>
<div id="overlay"></div>
<div class="wrapper">
    @include('admin.sidebar')
    <div id="content">
        <div class="container-fluid">

            {{-- Header --}}
            <div class="d-flex align-items-center justify-content-between mb-4 mt-1 flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <button id="sidebarCollapse" class="btn"><i class="bi bi-list fs-4"></i></button>
                    <div>
                        <a href="{{ route('admin.manajemen-role.index') }}" class="text-muted text-decoration-none small">
                            <i class="bi bi-arrow-left-circle me-1"></i>Kembali ke Manajemen Role
                        </a>
                        <h4 class="mb-0 fw-bold text-success mt-1">
                            Hak Akses:
                            <span class="badge bg-{{ $role->warna }} px-3">{{ $role->nama }}</span>
                        </h4>
                        <p class="text-muted small mb-0">Centang aksi yang diizinkan untuk setiap modul.</p>
                    </div>
                </div>
                {{-- Tombol pilih/hapus semua --}}
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-success btn-sm px-3" onclick="checkAll()">
                        <i class="bi bi-check-all me-1"></i>Pilih Semua
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" onclick="uncheckAll()">
                        <i class="bi bi-x-square me-1"></i>Hapus Semua
                    </button>
                </div>
            </div>

            {{-- Legend --}}
            <div class="d-flex gap-2 flex-wrap mb-4">
                <span style="font-size:.8rem;color:#666;">Keterangan aksi:</span>
                <span class="badge" style="background:#0dcaf0;font-size:.78rem;">Lihat</span>
                <span class="badge bg-success" style="font-size:.78rem;">Tambah</span>
                <span class="badge" style="background:#ffc107;color:#333;font-size:.78rem;">Edit</span>
                <span class="badge bg-danger" style="font-size:.78rem;">Hapus</span>
                <span class="text-muted ms-2" style="font-size:.8rem;">— Klik tombol untuk mengaktifkan/menonaktifkan</span>
            </div>

            <form action="{{ route('admin.manajemen-role.permissions.save', $role->uuid) }}" method="POST" id="formPermissions">
                @csrf

                @php
                    // Kelompokkan dengan mempertahankan key string asli modul
                    // collect()->groupBy() mereset key menjadi integer, sehingga
                    // kita gunakan array biasa agar name="permissions[kode_modul][]" tetap benar
                    $groups = [];
                    foreach ($modules as $modulKey => $modulConf) {
                        $groups[$modulConf['group']][$modulKey] = $modulConf;
                    }
                    $aksiLabels = ['view'=>'Lihat','create'=>'Tambah','edit'=>'Edit','delete'=>'Hapus'];
                @endphp

                @foreach($groups as $groupName => $groupModules)
                    <div class="mb-4">
                        <div class="group-header">
                            <span><i class="bi bi-folder me-2"></i>{{ $groupName }}</span>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-success btn-sm py-0 px-2" style="font-size:.72rem;"
                                    onclick="checkGroup('{{ Str::slug($groupName) }}')">Pilih Semua</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2" style="font-size:.72rem;"
                                    onclick="uncheckGroup('{{ Str::slug($groupName) }}')">Hapus</button>
                            </div>
                        </div>

                        @foreach($groupModules as $modulKey => $modulConf)
                            @php
                                $savedAksi = (array) $saved->get($modulKey, []);
                            @endphp
                            <div class="perm-row" data-group="{{ Str::slug($groupName) }}">
                                <i class="bi {{ $modulConf['icon'] }} text-success flex-shrink-0" style="width:20px;"></i>
                                <div class="perm-modul-name">{{ $modulConf['label'] }}</div>
                                <div class="perm-actions">
                                    @foreach($modulConf['aksi'] as $aksi)
                                        <label class="mb-0">
                                            <input type="checkbox"
                                                class="aksi-check"
                                                name="permissions[{{ $modulKey }}][]"
                                                value="{{ $aksi }}"
                                                data-aksi="{{ $aksi }}"
                                                {{ in_array($aksi, (array) $savedAksi) ? 'checked' : '' }}>
                                            <span class="aksi-label">{{ $aksiLabels[$aksi] ?? $aksi }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach

                {{-- Sticky save bar --}}
                <div class="sticky-save">
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="text-muted small" id="selectedCount">
                            <i class="bi bi-key me-1"></i>
                            <span id="countNum">0</span> aksi dipilih
                        </span>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.manajemen-role.index') }}" class="btn btn-light border px-4">Batal</a>
                            <button type="submit" class="btn btn-success px-5 fw-bold">
                                <i class="bi bi-save me-2"></i>Simpan Hak Akses
                            </button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function () {
    function toggleSidebar() {
        if ($(window).width() <= 768) { $('#sidebar').toggleClass('show-mobile'); $('#overlay').toggleClass('active'); }
        else { $('#sidebar').toggleClass('inactive'); }
    }
    $('#sidebarCollapse, #close-sidebar, #overlay').on('click', toggleSidebar);

    updateCount();
    $(document).on('change', '.aksi-check', updateCount);
});

function updateCount() {
    const cnt = document.querySelectorAll('.aksi-check:checked').length;
    document.getElementById('countNum').textContent = cnt;
}

function checkAll() {
    document.querySelectorAll('.aksi-check').forEach(el => el.checked = true);
    updateCount();
}

function uncheckAll() {
    document.querySelectorAll('.aksi-check').forEach(el => el.checked = false);
    updateCount();
}

function checkGroup(group) {
    document.querySelectorAll(`.perm-row[data-group="${group}"] .aksi-check`).forEach(el => el.checked = true);
    updateCount();
}

function uncheckGroup(group) {
    document.querySelectorAll(`.perm-row[data-group="${group}"] .aksi-check`).forEach(el => el.checked = false);
    updateCount();
}
</script>
</body>
</html>
