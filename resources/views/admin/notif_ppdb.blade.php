<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Notifikasi & Pengaturan PPDB</title>
    @if(isset($sekolah->logo))
    <link rel="icon" type="image/png" href="{{ \App\Helpers\ImageHelper::url($sekolah->logo) }}">
    @else
    <link rel="icon" type="image/png" href="{{ asset('assets/img/default-favicon.png') }}">
    @endif
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root { --primary-green: #198754; --bg-soft: #f4f7f6; }
        body { background-color: var(--bg-soft); font-family: 'Inter', sans-serif; }
        .wrapper { display: flex; }
        #content { width: 100%; padding: 25px; transition: all 0.3s; }
        
        /* Notif Card Style */
        .card-notif { border: none; border-radius: 12px; transition: 0.3s; border-left: 5px solid var(--primary-green); background: white; }
        .card-notif:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
        .badge-pending { background-color: #fff3cd; color: #856404; font-weight: bold; border-radius: 50px; }
        .badge-unpaid { background-color: #f8d7da; color: #842029; font-weight: bold; border-radius: 50px; }
        
        /* Button Style */
        .btn-confirm { background-color: var(--primary-green); color: white; border: none; }
        .btn-confirm:hover { background-color: #146c43; color: white; }
        .btn-reject { background-color: #dc3545; color: white; border: none; }
        .btn-reject:hover { background-color: #b02a37; color: white; }
        #sidebarCollapse { background: var(--primary-green); border: none; color: white; border-radius: 10px; padding: 8px 12px; }
        
        /* Modal Detail Style */
        .table-detail th { background-color: #f8f9fa; width: 35%; color: #555; font-size: 0.85rem; }
        .table-detail td { font-size: 0.85rem; color: #333; }
        .section-title { font-size: 0.9rem; font-weight: bold; color: var(--primary-green); border-bottom: 2px solid #eee; padding-bottom: 5px; margin-top: 15px; margin-bottom: 10px; }
        input:focus, textarea:focus, select:focus { border-color: #198754 !important; outline: none !important; box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25) !important;}
        
        /* Toggle Transition */
        .transition { transition: all 0.4s ease; }

        .btn-toggle-wrapper { text-align: right; }

        @media (max-width: 768px) {
            #content { padding: 15px 12px; }
            .card { padding: 15px !important; }
            h4.fw-bold { font-size: 1.2rem; }
            .btn-toggle-wrapper { text-align: center; width: 100%; margin-top: 5px; }
            .btn-toggle-wrapper button { width: 100%; font-size: 1rem; padding: 10px 15px; }
            
            /* Susunan Modal Footer di Mobile */
            .modal-footer { display: flex; flex-direction: column-reverse; gap: 8px; }
            .modal-footer button { width: 100%; margin: 0 !important; }
            
            /* Proporsionalitas Tabel Modal */
            .table-detail th, .table-detail td { font-size: 0.8rem; padding: 6px; }
            .table-detail th { width: 45%; }
            .section-title { font-size: 0.85rem; }
            
            /* Ukuran Badge */
            .badge-pending, .badge-unpaid { padding: 8px 12px !important; font-size: 0.75rem; }
        }
        
        /* Overlay Sidebar Mobile */
        #overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 1040; }
        #overlay.active { display: block; }
    </style>
</head>
<body>

<div id="overlay"></div>
<div class="wrapper">
    @include('admin.sidebar')

    <div id="content">
        <div class="container-fluid">
            <div class="d-flex align-items-md-center justify-content-between mb-4 flex-column flex-md-row gap-3">
                <div class="d-flex align-items-center">
                    <button type="button" id="sidebarCollapse" class="flex-shrink-0"><i class="bi bi-list fs-4"></i></button>
                    <div class="ms-3">
                        <h4 class="mb-0 fw-bold">Manajemen PPDB</h4>
                        <p class="text-muted small mb-0">Kelola pendaftaran siswa baru</p>
                    </div>
                </div>

                <div class="btn-toggle-wrapper">
                    <button id="btnTogglePPDB" class="btn btn-lg rounded-pill px-4 fw-bold shadow-sm transition">
                        <span id="ppdbStatusText"><i class="spinner-border spinner-border-sm me-2"></i>Memuat...</span>
                    </button>
                </div>
            </div>

            <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 15px;">
                <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-bell-fill me-2 text-warning"></i>Pendaftaran Pending</h5>
                <div id="notif-container">
                    <div class="text-center py-5">
                        <div class="spinner-border text-success" role="status"></div>
                        <p class="mt-2 text-muted">Memeriksa pendaftaran baru...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-person-vcard me-2"></i>Berkas Pendaftaran Lengkap</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="detail-content">
                </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi + Pembayaran Cash -->
<div class="modal fade" id="modalKonfirmasi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" style="max-width:560px;">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-cash-coin me-2"></i>Konfirmasi & Pembayaran</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-warning py-2 mb-3" style="font-size:0.85rem;">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    Pendaftar ini memiliki biaya yang dibayarkan secara <strong>tunai di sekolah</strong>.
                    Masukkan nominal yang diterima dari calon siswa.
                </div>

                <!-- Container biaya cash — diisi JS -->
                <div id="cashBiayaList"></div>

                <hr class="my-3">
                <div class="d-flex justify-content-between fw-bold text-success">
                    <span>Total Tagihan</span>
                    <span id="totalTagihan">Rp 0</span>
                </div>
                <div class="d-flex justify-content-between fw-bold mt-1">
                    <span>Total Dibayar</span>
                    <span id="totalDibayar">Rp 0</span>
                </div>
                <div id="ringkasanPembayaran" class="mt-2 text-center fw-bold" style="font-size:1rem;"></div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success rounded-pill px-4" id="btnKonfirmasiAkhir">
                    <i class="bi bi-check-circle me-1"></i> Konfirmasi Pendaftaran
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tolak Pendaftaran -->
<div class="modal fade" id="modalTolak" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" style="max-width:500px;">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-x-circle me-2"></i>Tolak Pendaftaran</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-danger py-2 mb-3" style="font-size:0.85rem;">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    Tindakan ini akan menolak pendaftaran atas nama <strong id="rejectNama"></strong>.
                    Data akan tetap tersimpan dengan status <strong>ditolak</strong>.
                </div>
                <input type="hidden" id="rejectMuridId">
                <div class="mb-3">
                    <label for="alasanTolak" class="form-label fw-semibold" style="font-size:0.9rem;">
                        Alasan Penolakan <span class="text-danger">*</span>
                    </label>
                    <textarea
                        id="alasanTolak"
                        class="form-control"
                        rows="4"
                        maxlength="1000"
                        placeholder="Tuliskan alasan penolakan pendaftaran ini..."></textarea>
                    <div class="invalid-feedback" id="alasanTolakError"></div>
                    <div class="form-text text-muted">Alasan ini akan tersimpan di database sebagai catatan admin.</div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger rounded-pill px-4" id="btnTolakAkhir">
                    <i class="bi bi-x-circle me-1"></i> Tolak Pendaftaran
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const btnToggle = document.getElementById('btnTogglePPDB');
    const statusText = document.getElementById('ppdbStatusText');

    // --- 1. FUNGSI STATUS PPDB (BUKA/TUTUP) ---
    function checkPPDBStatus() {
        fetch("{{ route('admin.ppdb.status') }}")
            .then(res => res.json())
            .then(data => {
                updateButtonUI(data.isOpen);
            });
    }

    function updateButtonUI(isOpen) {
        if (isOpen) {
            btnToggle.className = "btn btn-danger btn-lg rounded-pill px-4 fw-bold shadow-sm transition";
            statusText.innerHTML = '<i class="bi bi-x-circle me-2"></i> Tutup PPDB';
        } else {
            btnToggle.className = "btn btn-success btn-lg rounded-pill px-4 fw-bold shadow-sm transition";
            statusText.innerHTML = '<i class="bi bi-check-circle me-2"></i> Buka PPDB';
        }
    }

    btnToggle.onclick = function() {
        const action = statusText.innerText.includes('Tutup') ? 'MENUTUP' : 'MEMBUKA';
        if (!confirm(`Konfirmasi: Apakah Anda yakin ingin ${action} akses PPDB Online bagi publik?`)) return;

        fetch("{{ route('admin.ppdb.toggle') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                updateButtonUI(data.isOpen);
            }
        });
    };

    // --- 2. FUNGSI BADGE NOTIFIKASI SIDEBAR ---
    function updateBadge() {
        fetch("{{ route('admin.ppdb.count') }}")
            .then(res => res.json())
            .then(data => {
                const badge = document.getElementById('ppdb-badge');
                if (badge) {
                    if (data.count > 0) {
                        badge.innerText = data.count;
                        badge.style.display = 'inline-block';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            });
    }

    // --- 3. FUNGSI DAFTAR PENDAFTAR PENDING ---
    function fetchNotifications() {
        fetch("{{ route('admin.ppdb.data') }}")
            .then(res => res.json())
            .then(resp => {
                const data        = resp.murid        || [];
                const hasCash     = resp.hasCashBiaya || false;
                const container   = document.getElementById('notif-container');
                container.innerHTML = '';

                if (data.length === 0) {
                    container.innerHTML = `
                        <div class="text-center py-5">
                            <i class="bi bi-check2-circle text-success" style="font-size: 3rem;"></i>
                            <p class="mt-3 text-muted">Semua pendaftaran telah diproses.</p>
                        </div>`;
                    return;
                }

                data.forEach(m => {
                    const date = new Date(m.created_at).toLocaleDateString('id-ID', {
                        day: 'numeric', month: 'long', year: 'numeric'
                    });

                    // Label tambahan jika ada biaya cash yang belum dibayar
                    const cashBadge = hasCash
                        ? `<span class="badge badge-unpaid px-3 py-2 ms-1">
                               <i class="bi bi-cash me-1"></i>Pembayaran Belum Lunas
                           </span>`
                        : '';

                    container.innerHTML += `
                        <div class="card card-notif p-3 mb-3 shadow-sm" id="row-${m.id}">
                            <div class="d-flex justify-content-between align-items-md-center flex-column flex-md-row gap-3">
                                <div>
                                    <h6 class="fw-bold mb-1 text-success">${m.nama_lengkap}</h6>
                                    <small class="text-muted d-block mb-2">
                                        <i class="bi bi-person-vcard"></i> NISN: ${m.nisn ?? '-'} <span class="d-none d-md-inline">|</span><br class="d-md-none">
                                        <i class="bi bi-calendar3"></i> ${date}
                                    </small>
                                    <div class="d-flex flex-wrap gap-2 mt-1">
                                        <span class="badge badge-pending px-3 py-2">PENDING VERIFIKASI</span>
                                        ${cashBadge}
                                    </div>
                                </div>
                                <div class="d-grid d-md-flex gap-2 mt-1 mt-md-0">
                                    <button class="btn btn-outline-success btn-sm px-3 rounded-pill" onclick="viewDetail(${m.id})">
                                        <i class="bi bi-eye"></i> Detail Berkas
                                    </button>
                                    <button class="btn btn-confirm btn-sm px-3 rounded-pill" onclick="confirmPPDB(${m.id}, ${hasCash})">
                                        <i class="bi bi-check-circle"></i> Konfirmasi
                                    </button>
                                    <button class="btn btn-reject btn-sm px-3 rounded-pill" onclick="openRejectModal(${m.id}, '${m.nama_lengkap.replace(/'/g, "\\'")}')">
                                        <i class="bi bi-x-circle"></i> Tolak
                                    </button>
                                </div>
                            </div>
                        </div>`;
                });
            });
    }

    // --- 4. FUNGSI MODAL DETAIL LENGKAP (dinamis berdasarkan form settings aktif) ---
    function viewDetail(id) {
        const content = document.getElementById('detail-content');
        content.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-success"></div><p class="mt-2">Mengambil berkas pendaftar...</p></div>';

        const myModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalDetail'));
        myModal.show();

        fetch(`{{ url('admin/ppdb-notifications/detail') }}/${id}`)
            .then(res => res.json())
            .then(data => {
                const m       = data.murid;
                const o       = data.ortu       || {};
                const w       = data.wali       || {};
                const urls    = data.dokumen_urls || {};
                const settings = data.settings   || {};

                // ── Helper: ambil nilai dari objek, format angka jika perlu ──
                function val(obj, key) {
                    const v = obj[key];
                    if (v === null || v === undefined || v === '') return '<span class="text-muted">-</span>';
                    // Format rupiah untuk field penghasilan
                    if (key.startsWith('penghasilan')) {
                        return 'Rp ' + Number(v).toLocaleString('id-ID');
                    }
                    return v;
                }

                // ── Helper: buat tabel dinamis dari settings + sumber data ──
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

                // ── Helper: buat section dokumen ──
                function buildDokumenSection(fields) {
                    if (!fields || fields.length === 0) return '';
                    let items = '';
                    let hasAny = false;
                    fields.forEach(f => {
                        const url = urls[f.field_name];
                        if (url) {
                            hasAny = true;
                            // Tentukan apakah file PDF atau gambar berdasarkan ekstensi di URL
                            const isPdf = url.toLowerCase().includes('.pdf') || f.field_name.includes('surat') || f.field_name.includes('ijazah') || f.field_name.includes('transkip');
                            const icon  = isPdf ? 'bi-file-earmark-pdf-fill text-danger' : 'bi-file-earmark-image-fill text-primary';
                            items += `
                                <div class="col-md-6 mb-2">
                                    <div class="border rounded p-2 d-flex align-items-center gap-2">
                                        <i class="bi ${icon} fs-5"></i>
                                        <div class="flex-grow-1 overflow-hidden">
                                            <div class="fw-semibold" style="font-size:0.82rem;">${f.field_label}</div>
                                        </div>
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
                                        <div class="flex-grow-1">
                                            <div class="text-muted" style="font-size:0.82rem;">${f.field_label} <span class="badge bg-secondary">Tidak Diupload</span></div>
                                        </div>
                                    </div>
                                </div>`;
                        }
                    });
                    return `<div class="row">${items}</div>`;
                }

                let html = '<div class="row g-3">';

                // ══ KOLOM KIRI: Data Murid ══
                html += '<div class="col-lg-6">';
                if (settings.murid && settings.murid.length > 0) {
                    html += `<div class="section-title"><i class="bi bi-person-fill me-1"></i>Data Murid</div>`;
                    html += buildTable(settings.murid, m);
                }
                html += '</div>';

                // ══ KOLOM KANAN: Ortu + Wali ══
                html += '<div class="col-lg-6">';

                // Pisahkan field ortu berdasarkan ayah/ibu
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

                // ══ BARIS BAWAH: Dokumen (full width) ══
                if (settings.dokumen && settings.dokumen.length > 0) {
                    html += '<div class="col-12">';
                    html += `<div class="section-title"><i class="bi bi-folder-fill me-1"></i>Dokumen Pendaftaran</div>`;
                    html += buildDokumenSection(settings.dokumen);
                    html += '</div>';
                }

                // ══ BUKTI PEMBAYARAN (full width, load async) ══
                html += `
                    <div class="col-12">
                        <div class="section-title" style="color:#0d6efd;">
                            <i class="bi bi-receipt me-1"></i>Bukti Pembayaran
                        </div>
                        <div id="bukti-pembayaran-container-${id}">
                            <div class="text-center py-3">
                                <div class="spinner-border spinner-border-sm text-primary"></div>
                                <span class="ms-2 text-muted" style="font-size:0.85rem;">Memuat bukti pembayaran...</span>
                            </div>
                        </div>
                    </div>`;

                html += '</div>';
                content.innerHTML = html;

                // Load bukti pembayaran setelah HTML dirender
                loadBuktiPembayaran(id);
            })
            .catch(err => {
                content.innerHTML = `<div class="alert alert-danger">Gagal memuat detail: ${err.message}</div>`;
            });
    }

    // --- 5. FUNGSI KONFIRMASI ---
    // hasCash = true  → tampilkan modal pembayaran cash dulu
    // hasCash = false → langsung konfirmasi
    let _confirmId = null;

    function confirmPPDB(id, hasCash) {
        _confirmId = id;

        if (!hasCash) {
            // Tidak ada biaya cash — langsung konfirmasi
            if (!confirm('Penting: Data yang dikonfirmasi akan masuk ke database siswa aktif. Lanjutkan?')) return;
            doConfirm(id, []);
            return;
        }

        // Ada biaya cash — tampilkan modal pembayaran
        const listEl = document.getElementById('cashBiayaList');
        listEl.innerHTML = '<div class="text-center py-3"><div class="spinner-border spinner-border-sm text-success"></div></div>';

        document.getElementById('totalTagihan').textContent    = 'Rp 0';
        document.getElementById('totalDibayar').textContent    = 'Rp 0';
        document.getElementById('ringkasanPembayaran').innerHTML = '';

        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalKonfirmasi'));
        modal.show();

        fetch("{{ route('admin.ppdb.cashBiayas') }}")
            .then(res => res.json())
            .then(biayas => {
                let html = '';
                let totalTagihan = 0;

                biayas.forEach(b => {
                    totalTagihan += parseFloat(b.amount);
                    html += `
                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-semibold">${b.name}</span>
                                <span class="text-success fw-bold">Rp ${Number(b.amount).toLocaleString('id-ID')}</span>
                            </div>
                            <label class="form-label mb-1" style="font-size:0.82rem;">Nominal Diterima (Rp)</label>
                            <input type="number" min="0"
                                class="form-control cash-konfirmasi-input"
                                data-amount="${b.amount}"
                                data-id="${b.id}"
                                placeholder="Masukkan nominal uang diterima"
                                oninput="hitungKonfirmasi()">
                            <div class="cash-konfirmasi-result mt-1" style="font-size:0.82rem;"></div>
                        </div>`;
                });

                listEl.innerHTML = html || '<p class="text-muted">Tidak ada biaya cash.</p>';
                document.getElementById('totalTagihan').textContent =
                    'Rp ' + totalTagihan.toLocaleString('id-ID');
                hitungKonfirmasi();
            });
    }

    function hitungKonfirmasi() {
        const inputs  = document.querySelectorAll('.cash-konfirmasi-input');
        let totalTagihan = 0;
        let totalDibayar = 0;

        inputs.forEach(input => {
            const tagihan = parseFloat(input.dataset.amount) || 0;
            const dibayar = parseFloat(input.value) || 0;
            totalTagihan += tagihan;
            totalDibayar += dibayar;

            const resultEl = input.nextElementSibling;
            if (dibayar <= 0) {
                resultEl.innerHTML = '';
            } else if (dibayar >= tagihan) {
                const kembalian = dibayar - tagihan;
                resultEl.innerHTML = `<span class="text-success"><i class="bi bi-check-circle me-1"></i>Kembalian: <strong>Rp ${kembalian.toLocaleString('id-ID')}</strong></span>`;
            } else {
                const kurang = tagihan - dibayar;
                resultEl.innerHTML = `<span class="text-danger"><i class="bi bi-exclamation-circle me-1"></i>Kekurangan: <strong>Rp ${kurang.toLocaleString('id-ID')}</strong></span>`;
            }
        });

        document.getElementById('totalDibayar').textContent =
            'Rp ' + totalDibayar.toLocaleString('id-ID');

        const ringkasan = document.getElementById('ringkasanPembayaran');
        const selisih   = totalDibayar - totalTagihan;
        if (totalDibayar <= 0) {
            ringkasan.innerHTML = '';
        } else if (selisih >= 0) {
            ringkasan.innerHTML = `<span class="text-success"><i class="bi bi-check-circle-fill me-1"></i>Total Kembalian: Rp ${selisih.toLocaleString('id-ID')}</span>`;
        } else {
            ringkasan.innerHTML = `<span class="text-danger"><i class="bi bi-x-circle-fill me-1"></i>Total Kekurangan: Rp ${Math.abs(selisih).toLocaleString('id-ID')}</span>`;
        }
    }

    document.getElementById('btnKonfirmasiAkhir').onclick = function () {
        if (!_confirmId) return;
        if (!confirm('Pastikan pembayaran sudah diterima. Konfirmasi pendaftaran ini?')) return;

        // Kumpulkan data pembayaran per biaya
        const payments = [];
        document.querySelectorAll('.cash-konfirmasi-input').forEach(input => {
            payments.push({
                biaya_id: input.dataset.id,
                dibayar : parseFloat(input.value) || 0
            });
        });

        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalKonfirmasi')).hide();
        doConfirm(_confirmId, payments);
    };

    function doConfirm(id, payments) {
        fetch(`{{ url('admin/ppdb-notifications/confirm') }}/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ payments })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const row = document.getElementById(`row-${id}`);
                row.style.transform = 'translateX(100px)';
                row.style.opacity   = '0';
                setTimeout(() => {
                    row.remove();
                    updateBadge();
                    fetchNotifications();
                }, 400);
            } else {
                alert('Gagal mengkonfirmasi. Silakan coba lagi.');
            }
        });
    }

    // INIT
    document.addEventListener('DOMContentLoaded', () => {
        checkPPDBStatus();
        updateBadge();
        fetchNotifications();
        
        // Polling real-time
        setInterval(updateBadge, 10000); 
        setInterval(fetchNotifications, 60000); // 1 menit sekali

        // Tombol submit tolak
        document.getElementById('btnTolakAkhir').onclick = function () {
            const id     = document.getElementById('rejectMuridId').value;
            const alasan = document.getElementById('alasanTolak').value.trim();

            if (!alasan) {
                document.getElementById('alasanTolakError').textContent = 'Alasan penolakan wajib diisi.';
                document.getElementById('alasanTolak').classList.add('is-invalid');
                return;
            }

            doReject(id, alasan);
        };

        document.getElementById('alasanTolak').addEventListener('input', function () {
            this.classList.remove('is-invalid');
            document.getElementById('alasanTolakError').textContent = '';
        });
    });
    
    // ── Sidebar toggle ────────────────────────────────────────────────────────
    const sidebar     = document.getElementById('sidebar');
    const collapseBtn = document.getElementById('sidebarCollapse');
    const closeBtn    = document.getElementById('close-sidebar');
    const overlay     = document.getElementById('overlay');

    function toggleSidebar() {
        if (window.innerWidth <= 768) {
            if (sidebar) sidebar.classList.toggle('show-mobile');
            if (overlay) overlay.classList.toggle('active');
        } else {
            if (sidebar) sidebar.classList.toggle('inactive');
        }
    }
    if (collapseBtn) collapseBtn.onclick = toggleSidebar;
    if (closeBtn) closeBtn.onclick    = toggleSidebar;
    if (overlay) overlay.onclick     = toggleSidebar;

    // --- 6. FUNGSI BUKTI PEMBAYARAN ---
    function loadBuktiPembayaran(muridId) {
        fetch(`{{ url('admin/ppdb-notifications/bukti') }}/${muridId}`)
            .then(res => res.json())
            .then(bukti => {
                const container = document.getElementById(`bukti-pembayaran-container-${muridId}`);
                if (!container) return;

                if (!bukti || bukti.length === 0) {
                    container.innerHTML = `
                        <div class="alert alert-secondary py-2 mb-0" style="font-size:0.85rem;">
                            <i class="bi bi-info-circle me-1"></i>
                            Tidak ada bukti pembayaran yang diupload oleh pendaftar.
                        </div>`;
                    return;
                }

                let rows = '';
                bukti.forEach(b => {
                    const sizeKb   = b.file_size ? (b.file_size / 1024).toFixed(1) + ' KB' : '-';
                    const isImage  = /\.(jpg|jpeg|png|webp|gif)$/i.test(b.file_name);
                    const isPdf    = /\.pdf$/i.test(b.file_name);
                    const icon     = isPdf
                        ? 'bi-file-earmark-pdf-fill text-danger'
                        : (isImage ? 'bi-file-earmark-image-fill text-primary' : 'bi-file-earmark-fill text-secondary');

                    rows += `
                        <div class="col-md-6 mb-2">
                            <div class="border rounded p-2 d-flex align-items-center gap-2">
                                <i class="bi ${icon} fs-5 flex-shrink-0"></i>
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="fw-semibold text-truncate" style="font-size:0.82rem;" title="${b.file_name}">
                                        ${b.nama_biaya ? b.nama_biaya : b.file_name}
                                    </div>
                                    <small class="text-muted">${b.file_name} &bull; ${sizeKb}</small>
                                </div>
                                <a href="${b.url}" target="_blank" class="btn btn-outline-primary btn-sm py-0 px-2 flex-shrink-0" title="Lihat bukti">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="${b.url}" download class="btn btn-outline-secondary btn-sm py-0 px-2 flex-shrink-0" title="Unduh">
                                    <i class="bi bi-download"></i>
                                </a>
                            </div>
                        </div>`;
                });

                container.innerHTML = `<div class="row">${rows}</div>`;
            })
            .catch(() => {
                const container = document.getElementById(`bukti-pembayaran-container-${muridId}`);
                if (container) {
                    container.innerHTML = `<div class="alert alert-warning py-2" style="font-size:0.85rem;">
                        <i class="bi bi-exclamation-triangle me-1"></i>Gagal memuat bukti pembayaran.</div>`;
                }
            });
    }

    // --- 7. FUNGSI TOLAK PENDAFTARAN ---
    let _rejectId = null;

    function openRejectModal(id, nama) {
        _rejectId = id;
        document.getElementById('rejectMuridId').value  = id;
        document.getElementById('rejectNama').textContent = nama;
        document.getElementById('alasanTolak').value    = '';
        document.getElementById('alasanTolak').classList.remove('is-invalid');
        document.getElementById('alasanTolakError').textContent = '';

        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalTolak')).show();
    }

    function doReject(id, alasan) {
        const btn = document.getElementById('btnTolakAkhir');
        btn.disabled    = true;
        btn.innerHTML   = '<span class="spinner-border spinner-border-sm me-1"></span>Memproses...';

        fetch(`{{ url('admin/ppdb-notifications/reject') }}/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ alasan_tolak: alasan })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled  = false;
            btn.innerHTML = '<i class="bi bi-x-circle me-1"></i> Tolak Pendaftaran';

            if (data.success) {
                bootstrap.Modal.getOrCreateInstance(document.getElementById('modalTolak')).hide();

                const row = document.getElementById(`row-${id}`);
                if (row) {
                    row.style.transform = 'translateX(-100px)';
                    row.style.opacity   = '0';
                    setTimeout(() => {
                        row.remove();
                        updateBadge();
                    }, 400);
                }
            } else {
                alert(data.message || 'Gagal menolak pendaftaran. Silakan coba lagi.');
            }
        })
        .catch(() => {
            btn.disabled  = false;
            btn.innerHTML = '<i class="bi bi-x-circle me-1"></i> Tolak Pendaftaran';
            alert('Terjadi kesalahan jaringan. Silakan coba lagi.');
        });
    }
</script>
</body>
</html>