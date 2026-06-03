<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Data Murid</title>
    @if(isset($sekolah->logo))
    <link rel="icon" type="image/png" href="{{ asset($sekolah->logo) }}">
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

        /* Modal Detail Style — same as notif_ppdb */
        .table-detail th { background-color: #f8f9fa; width: 35%; color: #555; font-size: 0.85rem; }
        .table-detail td { font-size: 0.85rem; color: #333; }
        .section-title { font-size: 0.9rem; font-weight: bold; color: #198754;
            border-bottom: 2px solid #eee; padding-bottom: 5px; margin-top: 15px; margin-bottom: 10px; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .spin { display: inline-block; animation: spin 0.8s linear infinite; }
    </style>
</head>
<body>
    <div id="overlay"></div>
    <div class="wrapper">
        @include('dashboard_admin.sidebar_admin')
        <div id="content">
            <div class="container-fluid">
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

                <div class="card p-3 mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Kelola data siswa dan pendaftaran baru.</span>
                        <div class="input-group" style="width: 350px;">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="search-murid" class="form-control border-start-0 ps-0" placeholder="Cari Nama, NISN, atau No. HP...">
                        </div>
                    </div>
                </div>

                <div class="card p-4">
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
                            <tbody id="table-body">
                                @forelse($murids as $m)
                                <tr>
                                    <td class="fw-bold">{{ $m->nama_lengkap }}</td>
                                    <td>{{ $m->nisn }}</td>
                                    <td>{{ $m->no_hp }}</td>
                                    {{-- Kolom Dokumen: download PDF --}}
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-danger"
                                                title="Download PDF Formulir Lengkap"
                                                onclick="downloadPdf({{ $m->id }}, '{{ addslashes($m->nama_lengkap) }}', this)">
                                            <i class="bi bi-file-earmark-pdf-fill"></i>
                                        </button>
                                    </td>
                                    {{-- Kolom Aksi: berkas modal, edit, hapus --}}
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-info"
                                                title="Lihat Detail Berkas"
                                                onclick="viewDetail({{ $m->id }})">
                                            <i class="bi bi-person-vcard"></i>
                                        </button>
                                        <a href="{{ route('murid.edit', $m->id) }}" class="btn btn-sm btn-outline-success" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('murid.destroy', $m->id) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" title="Hapus"
                                                    onclick="return confirm('Hapus murid ini?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada data murid.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
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

        // ── AJAX Search ───────────────────────────────────────────────────────
        $(document).ready(function () {
            $('#search-murid').on('keyup', function () {
                $.ajax({
                    type: 'GET',
                    url: "{{ route('murid.search') }}",
                    data: { 'search': $(this).val() },
                    success: function (data) { $('#table-body').html(data); },
                    error:   function (err)  { console.log('Error AJAX:', err); }
                });
            });
        });

        // ── Modal Detail Berkas ───────────────────────────────────────────────
        function viewDetail(id) {
            const content = document.getElementById('detail-content');
            content.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-success"></div>
                    <p class="mt-2 text-muted">Mengambil data murid...</p>
                </div>`;

            bootstrap.Modal.getOrCreateInstance(
                document.getElementById('modalDetail')
            ).show();

            fetch(`{{ url('murid') }}/${id}/detail`)
                .then(res => res.json())
                .then(data => {
                    const m        = data.murid        || {};
                    const o        = data.ortu         || {};
                    const w        = data.wali         || {};
                    const urls     = data.dokumen_urls  || {};
                    const settings = data.settings      || {};

                    // ── helper: format nilai ──────────────────────────────
                    function val(obj, key) {
                        const v = obj[key];
                        if (v === null || v === undefined || v === '')
                            return '<span class="text-muted">-</span>';
                        if (key.startsWith('penghasilan'))
                            return 'Rp ' + Number(v).toLocaleString('id-ID');
                        return v;
                    }

                    // ── helper: tabel dinamis ─────────────────────────────
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

                    // ── helper: section dokumen ───────────────────────────
                    function buildDokumenSection(fields) {
                        if (!fields || fields.length === 0) return '';
                        let items = '';
                        fields.forEach(f => {
                            const url = urls[f.field_name];
                            if (url) {
                                const isImg = ['pasfoto','ktp_ayah','ktp_ibu','ktp_wali','kartu_keluarga','akte_kelahiran'].includes(f.field_name);
                                const icon  = isImg
                                    ? 'bi-file-earmark-image-fill text-primary'
                                    : 'bi-file-earmark-pdf-fill text-danger';
                                items += `
                                    <div class="col-md-6 mb-2">
                                        <div class="border rounded p-2 d-flex align-items-center gap-2">
                                            <i class="bi ${icon} fs-5"></i>
                                            <div class="flex-grow-1 fw-semibold" style="font-size:0.82rem;">${f.field_label}</div>
                                            <a href="${url}" target="_blank" class="btn btn-outline-success btn-sm py-0 px-2">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    </div>`;
                            } else {
                                items += `
                                    <div class="col-md-6 mb-2">
                                        <div class="border rounded p-2 d-flex align-items-center gap-2 bg-light">
                                            <i class="bi bi-file-earmark-x text-secondary fs-5"></i>
                                            <div class="text-muted flex-grow-1" style="font-size:0.82rem;">
                                                ${f.field_label}
                                                <span class="badge bg-secondary ms-1">Tidak Diupload</span>
                                            </div>
                                        </div>
                                    </div>`;
                            }
                        });
                        return `<div class="row">${items}</div>`;
                    }

                    let html = '<div class="row g-3">';

                    // Kolom kiri: Data Murid
                    html += '<div class="col-lg-6">';
                    if (settings.murid && settings.murid.length > 0) {
                        html += `<div class="section-title"><i class="bi bi-person-fill me-1"></i>Data Murid</div>`;
                        html += buildTable(settings.murid, m);
                    }
                    html += '</div>';

                    // Kolom kanan: Ortu + Wali
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

                    // Baris bawah: Dokumen (full width)
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
        // ── Download PDF via Fetch + Blob (tanpa buka tab baru) ─────────────
        function downloadPdf(id, namaLengkap, btn) {
            const icon = btn.querySelector('i');
            btn.disabled = true;
            icon.className = 'bi bi-arrow-repeat spin';

            // Pastikan global loading overlay TIDAK aktif saat download
            if (window.Loading) window.Loading.hide();

            fetch(`{{ url('murid') }}/${id}/pdf`, { method: 'GET' })
                .then(response => {
                    if (!response.ok) throw new Error('Server error: ' + response.status);
                    return response.blob();
                })
                .then(blob => {
                    const url   = window.URL.createObjectURL(blob);
                    const a     = document.createElement('a');
                    a.href      = url;
                    a.setAttribute('data-no-loading', ''); // cegah auto-intercept loading
                    a.download  = 'Formulir_PPDB_' + namaLengkap.replace(/\s+/g, '_') + '.pdf';
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
                    // Sembunyikan loading overlay jika masih tampil
                    if (window.Loading) window.Loading.hide();
                });
        }

        // ── Toast helper ──────────────────────────────────────────────────
        function showToast(message, type) {
            const toastEl  = document.getElementById('toastNotif');
            const toastMsg = document.getElementById('toastMsg');
            toastEl.className = `toast align-items-center text-white border-0 bg-${type}`;
            toastMsg.textContent = message;
            const bsToast = bootstrap.Toast.getOrCreateInstance(toastEl, { delay: 3000 });
            bsToast.show();
        }
    </script>
</body>
</html>
