<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Data Murid</title>
    @if(isset($sekolah->logo))
    <link rel="icon" type="image/png" href="{{ \App\Helpers\ImageHelper::url($sekolah->logo) }}">
    @else
    <link rel="icon" type="image/png" href="{{ asset('assets/img/default-favicon.png') }}">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f4f7f6; }
        .wrapper { display: flex; width: 100%; }
        #content { width: 100%; padding: 20px 30px; transition: all 0.3s; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .table thead { background-color: #198754; color: white; }
        #sidebarCollapse { width: 40px; height: 40px; background: #198754; border: none; color: white; border-radius: 8px; }
        #overlay { display: none; position: fixed; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 1040; top: 0; left: 0; }
        #overlay.active { display: block; }

        /* Tab style */
        .nav-tabs .nav-link { color: #555; font-weight: 500; }
        .nav-tabs .nav-link.active { color: #198754; font-weight: 700; border-bottom: 3px solid #198754; }
        .nav-tabs .nav-link .badge { font-size: 0.7rem; }

        /* Modal Detail Style */
        .table-detail th { background-color: #f8f9fa; width: 35%; color: #555; font-size: 0.85rem; }
        .table-detail td { font-size: 0.85rem; color: #333; }
        .section-title { font-size: 0.9rem; font-weight: bold; color: #198754;
            border-bottom: 2px solid #eee; padding-bottom: 5px; margin-top: 15px; margin-bottom: 10px; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .spin { display: inline-block; animation: spin 0.8s linear infinite; }

        /* Info banner tampil default */
        .info-limit-banner { background: #e8f5e9; border: 1px solid #a5d6a7; border-radius: 10px;
            padding: 10px 16px; font-size: 0.85rem; color: #2e7d32; }
    </style>
</head>
<body>
    <div id="overlay"></div>
    <div class="wrapper">
        @include('admin.sidebar')
        <div id="content">
            <div class="container-fluid">
                {{-- Header --}}
                <div class="d-flex align-items-center justify-content-between mb-4 mt-2">
                    <div class="d-flex align-items-center">
                        <button type="button" id="sidebarCollapse" class="btn"><i class="bi bi-list fs-5"></i></button>
                        <h4 class="ms-3 mb-0 fw-bold text-success">Daftar Murid</h4>
                    </div>
                    <a href="{{ route('murid.create') }}" class="btn btn-success px-4 fw-bold shadow-sm">
                        <i class="bi bi-person-plus me-2"></i>+ Murid
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger border-0 shadow-sm mb-4">{{ session('error') }}</div>
                @endif

                {{-- ===== TAB NAVIGATION ===== --}}
                <ul class="nav nav-tabs mb-0" id="muridTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="tab-aktif-btn" data-bs-toggle="tab"
                                data-bs-target="#tab-aktif" type="button" role="tab">
                            <i class="bi bi-person-check me-1"></i>Murid Aktif
                            <span class="badge bg-success ms-1">{{ $muridsAktif->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="tab-nonaktif-btn" data-bs-toggle="tab"
                                data-bs-target="#tab-nonaktif" type="button" role="tab">
                            <i class="bi bi-person-dash me-1"></i>Murid Nonaktif
                            @if($muridsNonaktif->count() > 0)
                            <span class="badge bg-danger ms-1">{{ $muridsNonaktif->count() }}</span>
                            @endif
                        </button>
                    </li>
                </ul>

                <div class="tab-content">

                    {{-- =================== TAB AKTIF =================== --}}
                    <div class="tab-pane fade show active" id="tab-aktif" role="tabpanel">
                        <div class="card p-3 mb-3" style="border-radius: 0 12px 12px 12px;">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <span class="text-muted small">Kelola data siswa aktif.</span>
                                <div class="input-group" style="width: 320px;">
                                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                                    <input type="text" id="search-aktif" class="form-control border-start-0 ps-0" placeholder="Cari Nama, NISN, atau No. HP...">
                                </div>
                            </div>
                        </div>

                        <div class="card p-4" style="border-radius: 0 12px 12px 12px;">

                            {{-- Info banner: hanya tampil jika total > 20 --}}
                            @if($muridsAktif->count() > 20)
                            <div class="info-limit-banner d-flex align-items-center gap-2 mb-3" id="bannerAktif">
                                <i class="bi bi-info-circle-fill fs-5 flex-shrink-0"></i>
                                <div>
                                    Menampilkan <strong>20 dari {{ $muridsAktif->count() }} murid aktif</strong>.
                                    Gunakan pencarian untuk menemukan murid lainnya, atau
                                    <a href="#" id="lihatSemuaAktif" class="fw-bold text-success">Lihat Semua Data</a>.
                                </div>
                            </div>
                            @endif

                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>Nama Lengkap</th>
                                            <th>NISN</th>
                                            <th>Nomor HP</th>
                                            <th class="text-center">Dokumen</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="table-body-aktif">
                                        @forelse($muridsAktif as $index => $m)
                                        <tr class="{{ $index >= 20 ? 'd-none row-aktif-hidden' : 'row-aktif-visible' }}">
                                            <td class="fw-bold">{{ $m->nama_lengkap }}</td>
                                            <td>{{ $m->nisn }}</td>
                                            <td>{{ $m->no_hp }}</td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-outline-danger"
                                                        title="Download PDF Formulir Lengkap"
                                                        onclick="downloadPdf('{{ $m->uuid }}', '{{ addslashes($m->nama_lengkap) }}', this)">
                                                    <i class="bi bi-file-earmark-pdf-fill"></i>
                                                </button>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-outline-info"
                                                        title="Lihat Detail Berkas"
                                                        onclick="viewDetail('{{ $m->uuid }}')">
                                                    <i class="bi bi-person-vcard"></i>
                                                </button>
                                                <a href="{{ route('murid.edit', $m->uuid) }}" class="btn btn-sm btn-outline-success" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button class="btn btn-sm btn-outline-danger" title="Nonaktifkan"
                                                        onclick="bukaModalHapus('{{ $m->uuid }}', '{{ addslashes($m->nama_lengkap) }}')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr id="emptyAktif">
                                            <td colspan="5" class="text-center py-4 text-muted">Belum ada data murid aktif.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- =================== TAB NONAKTIF =================== --}}
                    <div class="tab-pane fade" id="tab-nonaktif" role="tabpanel">
                        <div class="card p-3 mb-3" style="border-radius: 0 12px 12px 12px;">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <span class="text-muted small">Data murid yang telah dinonaktifkan (pindahan, dikeluarkan, dll).</span>
                                <div class="input-group" style="width: 320px;">
                                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                                    <input type="text" id="search-nonaktif" class="form-control border-start-0 ps-0" placeholder="Cari Nama, NISN, atau No. HP...">
                                </div>
                            </div>
                        </div>

                        <div class="card p-4" style="border-radius: 0 12px 12px 12px;">

                            {{-- Info banner nonaktif --}}
                            @if($muridsNonaktif->count() > 20)
                            <div class="info-limit-banner d-flex align-items-center gap-2 mb-3" id="bannerNonaktif">
                                <i class="bi bi-info-circle-fill fs-5 flex-shrink-0"></i>
                                <div>
                                    Menampilkan <strong>20 dari {{ $muridsNonaktif->count() }} murid nonaktif</strong>.
                                    Gunakan pencarian atau
                                    <a href="#" id="lihatSemuaNonaktif" class="fw-bold text-success">Lihat Semua Data</a>.
                                </div>
                            </div>
                            @endif

                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>Nama Lengkap</th>
                                            <th>NISN</th>
                                            <th>Nomor HP</th>
                                            <th>Alasan Nonaktif</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="table-body-nonaktif">
                                        @forelse($muridsNonaktif as $index => $m)
                                        <tr class="{{ $index >= 20 ? 'd-none row-nonaktif-hidden' : 'row-nonaktif-visible' }}">
                                            <td class="fw-bold">{{ $m->nama_lengkap }}</td>
                                            <td>{{ $m->nisn }}</td>
                                            <td>{{ $m->no_hp }}</td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $m->alasan_nonaktif }}</span>
                                                @if($m->tanggal_nonaktif)
                                                <br><small class="text-muted">{{ \Carbon\Carbon::parse($m->tanggal_nonaktif)->format('d M Y') }}</small>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-outline-info"
                                                        title="Lihat Detail Berkas"
                                                        onclick="viewDetail('{{ $m->uuid }}')">
                                                    <i class="bi bi-person-vcard"></i>
                                                </button>
                                                @if($m->surat_pernyataan)
                                                <a href="{{ route('murid.download-surat', $m->uuid) }}" class="btn btn-sm btn-outline-secondary" title="Download Surat Pernyataan">
                                                    <i class="bi bi-file-earmark-arrow-down"></i>
                                                </a>
                                                @endif
                                                <form action="{{ route('murid.restore', $m->uuid) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Pulihkan ke Aktif"
                                                            onclick="return confirm('Pulihkan murid ini ke data aktif?')">
                                                        <i class="bi bi-arrow-counterclockwise"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">Belum ada data murid nonaktif.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>{{-- end tab-content --}}
            </div>
        </div>
    </div>

    {{-- ══ Toast Notifikasi ══ --}}
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999">
        <div id="toastNotif" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive">
            <div class="d-flex">
                <div class="toast-body fw-semibold" id="toastMsg"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    {{-- ══ Modal Nonaktifkan Murid ══ --}}
    <div class="modal fade" id="modalHapus" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form id="formNonaktif" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title fw-bold"><i class="bi bi-person-dash me-2"></i>Nonaktifkan Murid</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="text-muted mb-3">
                            Murid <strong id="namaHapus"></strong> akan dipindahkan ke data nonaktif. Data tidak akan dihapus permanen.
                        </p>

                        {{-- Alasan --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Alasan Nonaktif <span class="text-danger">*</span></label>
                            <select name="alasan_nonaktif" id="selectAlasan" class="form-select" required onchange="toggleAlasanLain(this)">
                                <option value="">-- Pilih Alasan --</option>
                                <option value="Pindahan">Pindahan</option>
                                <option value="Dikeluarkan">Dikeluarkan</option>
                                <option value="Mengundurkan Diri">Mengundurkan Diri</option>
                                <option value="Meninggal Dunia">Meninggal Dunia</option>
                                <option value="Lainnya">Lainnya...</option>
                            </select>
                        </div>
                        <div class="mb-3 d-none" id="inputAlasanLain">
                            <label class="form-label fw-semibold">Tulis Alasan <span class="text-danger">*</span></label>
                            <input type="text" name="alasan_nonaktif_lain" id="alasanLain" class="form-control" placeholder="Contoh: Mengikuti orangtua pindah kota...">
                        </div>

                        {{-- Upload surat --}}
                        <div class="mb-1">
                            <label class="form-label fw-semibold">Surat Pernyataan <span class="text-muted fw-normal">(opsional, maks. 2MB)</span></label>
                            <input type="file" name="surat_pernyataan" id="inputSurat" class="form-control"
                                   accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text text-muted">Format: PDF, JPG, PNG. Maksimal 2MB.</div>
                            <div id="suratError" class="text-danger small d-none">Ukuran file melebihi 2MB.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger px-4 fw-bold" id="btnKonfirmasiHapus">
                            <i class="bi bi-person-dash me-1"></i>Nonaktifkan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ══ Modal Detail Berkas ══ --}}
    <div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-person-vcard me-2"></i>Detail Berkas Murid
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4" id="detail-content">
                    {{-- diisi oleh JS --}}
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ── Sidebar ──────────────────────────────────────────────────────────
        const sidebar     = document.getElementById('sidebar');
        const collapseBtn = document.getElementById('sidebarCollapse');
        const overlay     = document.getElementById('overlay');
        const closeBtn    = document.getElementById('close-sidebar');

        function toggleSidebar() {
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('show-mobile');
                overlay.classList.toggle('active');
            } else {
                sidebar.classList.toggle('inactive');
            }
        }
        collapseBtn.onclick = toggleSidebar;
        if (closeBtn) closeBtn.onclick = toggleSidebar;
        overlay.onclick = toggleSidebar;

        // ── "Lihat Semua Data" — Tab Aktif ────────────────────────────────────
        const lihatSemuaAktif = document.getElementById('lihatSemuaAktif');
        if (lihatSemuaAktif) {
            lihatSemuaAktif.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelectorAll('.row-aktif-hidden').forEach(function (row) {
                    row.classList.remove('d-none');
                    row.classList.remove('row-aktif-hidden');
                    row.classList.add('row-aktif-visible');
                });
                const banner = document.getElementById('bannerAktif');
                if (banner) banner.remove();
            });
        }

        // ── "Lihat Semua Data" — Tab Nonaktif ────────────────────────────────
        const lihatSemuaNonaktif = document.getElementById('lihatSemuaNonaktif');
        if (lihatSemuaNonaktif) {
            lihatSemuaNonaktif.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelectorAll('.row-nonaktif-hidden').forEach(function (row) {
                    row.classList.remove('d-none');
                    row.classList.remove('row-nonaktif-hidden');
                    row.classList.add('row-nonaktif-visible');
                });
                const banner = document.getElementById('bannerNonaktif');
                if (banner) banner.remove();
            });
        }

        // ── AJAX Search Murid Aktif ───────────────────────────────────────────
        $('#search-aktif').on('keyup', function () {
            const val = $(this).val().trim();
            if (val === '') {
                // Kembalikan ke tampilan default (batalkan AJAX, reload baris lokal)
                location.reload();
                return;
            }
            $.ajax({
                type: 'GET',
                url: "{{ route('murid.search') }}",
                data: { search: val, tab: 'aktif' },
                success: function (data) { $('#table-body-aktif').html(data); },
                error: function (err) { console.error('Search error:', err); }
            });
        });

        // ── AJAX Search Murid Nonaktif ────────────────────────────────────────
        $('#search-nonaktif').on('keyup', function () {
            const val = $(this).val().trim();
            if (val === '') {
                location.reload();
                return;
            }
            $.ajax({
                type: 'GET',
                url: "{{ route('murid.search') }}",
                data: { search: val, tab: 'nonaktif' },
                success: function (data) { $('#table-body-nonaktif').html(data); },
                error: function (err) { console.error('Search error:', err); }
            });
        });

        // ── Modal Hapus / Nonaktifkan ─────────────────────────────────────────
        function bukaModalHapus(uuid, nama) {
            document.getElementById('namaHapus').textContent = nama;
            document.getElementById('formNonaktif').action = '/murid/' + uuid;

            // Reset form
            document.getElementById('selectAlasan').value = '';
            document.getElementById('inputAlasanLain').classList.add('d-none');
            document.getElementById('alasanLain').value = '';
            document.getElementById('inputSurat').value = '';
            document.getElementById('suratError').classList.add('d-none');

            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalHapus')).show();
        }

        function toggleAlasanLain(sel) {
            const box = document.getElementById('inputAlasanLain');
            const inp = document.getElementById('alasanLain');
            if (sel.value === 'Lainnya') {
                box.classList.remove('d-none');
                inp.required = true;
            } else {
                box.classList.add('d-none');
                inp.required = false;
                inp.value = '';
            }
        }

        // Validasi ukuran file surat (max 2MB)
        document.getElementById('inputSurat').addEventListener('change', function () {
            const errEl = document.getElementById('suratError');
            const btn   = document.getElementById('btnKonfirmasiHapus');
            if (this.files[0] && this.files[0].size > 2 * 1024 * 1024) {
                errEl.classList.remove('d-none');
                btn.disabled = true;
            } else {
                errEl.classList.add('d-none');
                btn.disabled = false;
            }
        });

        // Override submit form: jika alasan "Lainnya" salin ke field utama
        document.getElementById('formNonaktif').addEventListener('submit', function (e) {
            const sel = document.getElementById('selectAlasan');
            if (sel.value === 'Lainnya') {
                const alasanLain = document.getElementById('alasanLain').value.trim();
                if (!alasanLain) {
                    e.preventDefault();
                    document.getElementById('alasanLain').focus();
                    return;
                }
                // Ganti nilai select agar yang terkirim adalah teks lainnya
                sel.name = ''; // nonaktifkan select asli
                const hidden = document.createElement('input');
                hidden.type  = 'hidden';
                hidden.name  = 'alasan_nonaktif';
                hidden.value = alasanLain;
                this.appendChild(hidden);
            }
        });

        // ── Modal Detail Berkas ───────────────────────────────────────────────
        function viewDetail(uuid) {
            const content = document.getElementById('detail-content');
            content.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-success"></div>
                    <p class="mt-2 text-muted">Mengambil data murid...</p>
                </div>`;

            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalDetail')).show();

            fetch(`{{ url('murid') }}/${uuid}/detail`)
                .then(res => res.json())
                .then(data => {
                    const m        = data.murid        || {};
                    const o        = data.ortu         || {};
                    const w        = data.wali         || {};
                    const urls     = data.dokumen_urls  || {};
                    const settings = data.settings      || {};

                    function val(obj, key) {
                        const v = obj[key];
                        if (v === null || v === undefined || v === '')
                            return '<span class="text-muted">-</span>';
                        if (key.startsWith('penghasilan'))
                            return 'Rp ' + Number(v).toLocaleString('id-ID');
                        return v;
                    }

                    function buildTable(fields, sourceObj) {
                        if (!fields || fields.length === 0) return '';
                        let rows = '';
                        fields.forEach(f => {
                            rows += `<tr>
                                <th class="text-muted" style="width:38%;font-size:0.82rem;">${f.field_label}</th>
                                <td style="font-size:0.82rem;">${val(sourceObj, f.field_name)}</td>
                            </tr>`;
                        });
                        return `<table class="table table-sm table-bordered table-detail mb-3">${rows}</table>`;
                    }

                    function buildDokumenSection(fields) {
                        if (!fields || fields.length === 0) return '';
                        let items = '';
                        fields.forEach(f => {
                            const url = urls[f.field_name];
                            if (url) {
                                const isImg = ['pasfoto','ktp_ayah','ktp_ibu','ktp_wali','kartu_keluarga','akte_kelahiran'].includes(f.field_name);
                                const icon  = isImg ? 'bi-file-earmark-image-fill text-primary' : 'bi-file-earmark-pdf-fill text-danger';
                                items += `
                                    <div class="col-md-6 mb-2">
                                        <div class="border rounded p-2 d-flex align-items-center gap-2">
                                            <i class="bi ${icon} fs-5"></i>
                                            <div class="flex-grow-1 fw-semibold" style="font-size:0.82rem;">${f.field_label}</div>
                                            <a href="${url}" target="_blank" class="btn btn-outline-success btn-sm py-0 px-2"><i class="bi bi-eye"></i></a>
                                        </div>
                                    </div>`;
                            } else {
                                items += `
                                    <div class="col-md-6 mb-2">
                                        <div class="border rounded p-2 d-flex align-items-center gap-2 bg-light">
                                            <i class="bi bi-file-earmark-x text-secondary fs-5"></i>
                                            <div class="text-muted flex-grow-1" style="font-size:0.82rem;">
                                                ${f.field_label} <span class="badge bg-secondary ms-1">Tidak Diupload</span>
                                            </div>
                                        </div>
                                    </div>`;
                            }
                        });
                        return `<div class="row">${items}</div>`;
                    }

                    let html = '<div class="row g-3">';
                    html += '<div class="col-lg-6">';
                    if (settings.murid && settings.murid.length > 0) {
                        html += `<div class="section-title"><i class="bi bi-person-fill me-1"></i>Data Murid</div>`;
                        html += buildTable(settings.murid, m);
                    }
                    html += '</div>';
                    html += '<div class="col-lg-6">';
                    const ayahFields = (settings.ortu || []).filter(f => f.field_name.endsWith('_ayah'));
                    const ibuFields  = (settings.ortu || []).filter(f => f.field_name.endsWith('_ibu'));
                    if (ayahFields.length > 0) {
                        html += `<div class="section-title text-primary"><i class="bi bi-gender-male me-1"></i>Data Ayah</div>`;
                        html += buildTable(ayahFields, o);
                    }
                    if (ibuFields.length > 0) {
                        html += `<div class="section-title" style="color:#c2185b;"><i class="bi bi-gender-female me-1"></i>Data Ibu</div>`;
                        html += buildTable(ibuFields, o);
                    }
                    if (settings.wali && settings.wali.length > 0 && w && w.nama_wali) {
                        html += `<div class="section-title text-warning"><i class="bi bi-person-badge me-1"></i>Data Wali Murid</div>`;
                        html += buildTable(settings.wali, w);
                    }
                    html += '</div>';
                    if (settings.dokumen && settings.dokumen.length > 0) {
                        html += '<div class="col-12">';
                        html += `<div class="section-title"><i class="bi bi-folder-fill me-1"></i>Dokumen PPDB</div>`;
                        html += buildDokumenSection(settings.dokumen);
                        html += '</div>';
                    }
                    html += '</div>';
                    content.innerHTML = html;
                })
                .catch(err => {
                    content.innerHTML = `<div class="alert alert-danger">Gagal memuat detail: ${err.message}</div>`;
                });
        }

        // ── Download PDF ──────────────────────────────────────────────────────
        function downloadPdf(uuid, namaLengkap, btn) {
            const icon = btn.querySelector('i');
            btn.disabled = true;
            icon.className = 'bi bi-arrow-repeat spin';

            fetch(`{{ url('murid') }}/${uuid}/pdf`, { method: 'GET' })
                .then(response => {
                    if (!response.ok) throw new Error('Server error: ' + response.status);
                    return response.blob();
                })
                .then(blob => {
                    const url  = window.URL.createObjectURL(blob);
                    const a    = document.createElement('a');
                    a.href     = url;
                    a.download = 'Formulir_PPDB_' + namaLengkap.replace(/\s+/g, '_') + '.pdf';
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    window.URL.revokeObjectURL(url);
                    showToast('PDF berhasil didownload!', 'success');
                })
                .catch(err => {
                    showToast('Gagal download PDF: ' + err.message, 'danger');
                })
                .finally(() => {
                    btn.disabled = false;
                    icon.className = 'bi bi-file-earmark-pdf-fill';
                });
        }

        // ── Toast ─────────────────────────────────────────────────────────────
        function showToast(message, type) {
            const toastEl  = document.getElementById('toastNotif');
            const toastMsg = document.getElementById('toastMsg');
            toastEl.className = `toast align-items-center text-white border-0 bg-${type}`;
            toastMsg.textContent = message;
            bootstrap.Toast.getOrCreateInstance(toastEl, { delay: 3000 }).show();
        }

        // ── Pertahankan tab aktif setelah redirect ────────────────────────────
        (function () {
            const hash = window.location.hash;
            if (hash === '#nonaktif') {
                const trigger = document.getElementById('tab-nonaktif-btn');
                if (trigger) bootstrap.Tab.getOrCreateInstance(trigger).show();
            }
        })();
    </script>
</body>
</html>
