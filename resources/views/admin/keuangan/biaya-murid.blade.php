<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kelola Biaya Murid</title>
    @if(isset($sekolah->logo))
    <link rel="icon" type="image/png" href="{{ \App\Helpers\ImageHelper::url($sekolah->logo) }}">
    @else
    <link rel="icon" type="image/png" href="{{ asset('assets/img/default-favicon.png') }}">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary-green: #198754; }
        body { font-family: 'Inter', sans-serif; background-color: #f4f7f6; overflow-x: hidden; }
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        #content { width: 100%; padding: 20px 30px; transition: all 0.3s; min-height: 100vh; min-width: 0; }
        #sidebarCollapse { width: 45px; height: 45px; background: var(--primary-green); border: none; color: white; border-radius: 10px; box-shadow: 0 4px 10px rgba(25,135,84,0.2); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .card  { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .table thead { background-color: var(--primary-green); color: white; }
        .table thead th { font-size: 0.82rem; letter-spacing: 0.4px; font-weight: 600; white-space: nowrap; }
        #overlay { display: none; position: fixed; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 1040; top: 0; left: 0; }
        #overlay.active { display: block; }
        input:focus, textarea:focus, select:focus { border-color: #198754 !important; outline: none !important; box-shadow: 0 0 0 0.2rem rgba(25,135,84,0.25) !important; }
        .form-label { font-weight: 600; font-size: 0.85rem; }
        .badge-rekening { background-color: #e8f5e9; color: #1b5e20; border: 1px solid #a5d6a7; }
        .badge-cash     { background-color: #fff3e0; color: #e65100; border: 1px solid #ffcc80; }
        .badge-qris     { background-color: #e3f2fd; color: #0d47a1; border: 1px solid #90caf9; }
        .name-ok   { border-color: #198754 !important; }
        .name-fail { border-color: #dc3545 !important; }
        /* Mobile card */
        .biaya-card-mobile { display: none; }
        .biaya-card-item { background: #fff; border-radius: 12px; padding: 14px 16px; margin-bottom: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border-left: 4px solid var(--primary-green); }
        .biaya-card-item .bc-name   { font-weight: 700; font-size: 0.95rem; color: #1a3a3a; }
        .biaya-card-item .bc-nominal{ font-size: 0.88rem; color: #198754; font-weight: 600; margin-top: 3px; }
        .biaya-card-item .bc-meta   { font-size: 0.8rem; color: #6c757d; margin-top: 3px; }
        .biaya-card-item .bc-actions{ display: flex; gap: 8px; margin-top: 10px; }
        .biaya-card-item .bc-actions .btn { flex: 1; font-size: 0.82rem; }
        /* Responsive */
        @media (max-width: 991px) { #content { padding: 16px 18px; } }
        @media (max-width: 767px) {
            #content { padding: 12px 12px; }
            .page-header { flex-direction: column; align-items: flex-start !important; gap: 10px; }
            .page-header .btn-group-action { width: 100%; display: flex; gap: 8px; }
            .page-header .btn-group-action .btn { flex: 1; font-size: 0.82rem; }
            .table-biaya-desktop { display: none !important; }
            .biaya-card-mobile { display: block; }
        }
    </style>
</head>
<body>
<div id="overlay"></div>
<div class="wrapper">
    @include('admin.sidebar')

    <div id="content">
        <div class="container-fluid">

            {{-- ══ Header ══ --}}
            <div class="d-flex align-items-center justify-content-between mb-4 mt-2 flex-wrap gap-2 page-header">
                <div class="d-flex align-items-center">
                    <button type="button" id="sidebarCollapse" class="btn" onclick="toggleSidebar()">
                        <i class="bi bi-list fs-4"></i>
                    </button>
                    <div class="ms-3">
                        <h4 class="mb-0 fw-bold text-success">
                            <i class="bi bi-cash-stack me-2"></i>Kelola Biaya Murid
                        </h4>
                        <p class="text-muted small mb-0 d-none d-sm-block">Atur biaya PPDB dan pembayaran murid</p>
                    </div>
                </div>
                <div class="d-flex gap-2 flex-wrap btn-group-action">
                    <a href="{{ route('akun-pembayaran.index') }}" class="btn btn-outline-success shadow-sm">
                        <i class="bi bi-bank me-1"></i>Akun Rekening
                    </a>
                    <button class="btn btn-success shadow-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#modalTambahBiaya">
                        <i class="bi bi-plus-circle me-1"></i>Tambah Biaya
                    </button>
                </div>
            </div>

            {{-- ══ Alert ══ --}}
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
                    <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Gagal menyimpan:</div>
                    <ul class="mb-0 small ps-3">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- ══ Info kosong akun ══ --}}
            @if($accounts->isEmpty())
                <div class="alert alert-warning border-0 shadow-sm mb-4">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Belum ada akun rekening/QRIS yang terdaftar.
                    <a href="{{ route('akun-pembayaran.index') }}" class="alert-link fw-bold ms-1">Tambah sekarang →</a>
                </div>
            @endif

            {{-- ══ Tabel Biaya ══ --}}
            <div class="card p-4">

                {{-- Info ringkas --}}
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <p class="text-muted small mb-0">
                        Daftar biaya yang digunakan pada proses PPDB dan pembayaran murid.
                        Total <strong>{{ $biayas->count() }}</strong> biaya terdaftar.
                    </p>
                </div>

                {{-- DESKTOP TABLE --}}
                <div class="table-responsive table-biaya-desktop">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>Nama Biaya</th>
                                <th class="text-end">Nominal</th>
                                <th>Metode Pembayaran</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($biayas as $i => $b)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td class="fw-semibold">{{ $b->name }}</td>
                                <td class="text-end fw-semibold">
                                    Rp {{ number_format($b->amount, 0, ',', '.') }}
                                </td>
                                <td>
                                    @if($b->account)
                                        @if($b->account->is_qris)
                                            <span class="badge badge-qris px-2 py-1">
                                                <i class="bi bi-qr-code me-1"></i>QRIS
                                                — {{ $b->account->bank_name }}
                                            </span>
                                        @else
                                            <span class="badge badge-rekening px-2 py-1">
                                                <i class="bi bi-bank me-1"></i>{{ $b->account->bank_name }}
                                                ({{ $b->account->account_number ?? '—' }})
                                            </span>
                                        @endif
                                    @else
                                        <span class="badge badge-cash px-2 py-1">
                                            <i class="bi bi-cash me-1"></i>Cash / Tunai
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-success border-0"
                                            title="Edit"
                                            onclick="bukaModalEdit({{ $b->id }},'{{ addslashes($b->name) }}','{{ $b->amount }}','{{ $b->account_id ?? '' }}')">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <form action="{{ route('biaya-murid.destroy', $b->id) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Hapus biaya ini? Tindakan tidak dapat dibatalkan.')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger border-0" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-cash-stack fs-3 d-block mb-2 text-secondary"></i>
                                    Belum ada biaya yang terdaftar.
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalTambahBiaya">
                                            Tambah Biaya Pertama
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- MOBILE CARD LIST --}}
                <div class="biaya-card-mobile">
                    @forelse($biayas as $i => $b)
                    <div class="biaya-card-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="bc-name">{{ $b->name }}</div>
                            @if($b->account)
                                @if($b->account->is_qris)
                                    <span class="badge badge-qris px-2">QRIS</span>
                                @else
                                    <span class="badge badge-rekening px-2">Bank</span>
                                @endif
                            @else
                                <span class="badge badge-cash px-2">Cash</span>
                            @endif
                        </div>
                        <div class="bc-nominal">
                            <i class="bi bi-cash me-1"></i>Rp {{ number_format($b->amount, 0, ',', '.') }}
                        </div>
                        @if($b->account)
                        <div class="bc-meta">
                            <i class="bi bi-bank me-1"></i>{{ $b->account->bank_name }}
                            @if(!$b->account->is_qris && $b->account->account_number)
                                ({{ $b->account->account_number }})
                            @endif
                            @if(!$b->account->is_qris && $b->account->account_holder)
                                — a.n. {{ $b->account->account_holder }}
                            @endif
                        </div>
                        @endif
                        <div class="bc-actions">
                            <button class="btn btn-outline-success btn-sm"
                                    onclick="bukaModalEdit({{ $b->id }},'{{ addslashes($b->name) }}','{{ $b->amount }}','{{ $b->account_id ?? '' }}')">
                                <i class="bi bi-pencil-square me-1"></i>Edit
                            </button>
                            <form action="{{ route('biaya-murid.destroy', $b->id) }}" method="POST" class="flex-fill"
                                  onsubmit="return confirm('Hapus biaya ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-danger btn-sm w-100">
                                    <i class="bi bi-trash me-1"></i>Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-cash-stack fs-3 d-block mb-2 text-secondary"></i>
                        Belum ada biaya yang terdaftar.
                        <div class="mt-2">
                            <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalTambahBiaya">
                                Tambah Biaya Pertama
                            </button>
                        </div>
                    </div>
                    @endforelse
                </div>

            </div>

        </div>{{-- end container --}}
    </div>{{-- end #content --}}
</div>{{-- end .wrapper --}}


{{-- ══════════════════════════════════════════════
     MODAL: TAMBAH BIAYA
══════════════════════════════════════════════ --}}
<div class="modal fade" id="modalTambahBiaya" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('biaya-murid.store') }}" method="POST" id="formTambahBiaya" novalidate>
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Biaya Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">

                    {{-- Nama Biaya --}}
                    <div class="mb-3">
                        <label class="form-label">Nama Biaya <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="tambahNama" class="form-control"
                               placeholder="Contoh: Biaya Pendaftaran, SPP Bulan Juli..."
                               autocomplete="off" required>
                        <div id="tambahNamaFeedback" class="small mt-1" style="display:none;"></div>
                    </div>

                    {{-- Nominal --}}
                    <div class="mb-3">
                        <label class="form-label">Nominal (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" id="tambahNominal" class="form-control"
                               placeholder="0" min="1" required>
                    </div>

                    {{-- Metode Pembayaran --}}
                    <div class="mb-1">
                        <label class="form-label">Metode Pembayaran</label>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="tambahIsCash" onchange="toggleAkunTambah(this)">
                            <label class="form-check-label" for="tambahIsCash">
                                Cash / Tunai <span class="text-muted small">(tidak melalui rekening)</span>
                            </label>
                        </div>
                        <div id="tambahAkunWrapper">
                            <select name="account_id" id="tambahAkunSelect" class="form-select">
                                <option value="">— Tanpa rekening (Cash / Tunai) —</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">
                                        @if($acc->is_qris)
                                            QRIS — {{ $acc->bank_name }}
                                        @else
                                            {{ $acc->bank_name }}
                                            @if($acc->account_number) ({{ $acc->account_number }}) @endif
                                            @if($acc->account_holder) — a.n. {{ $acc->account_holder }} @endif
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @if($accounts->isEmpty())
                                <div class="form-text text-warning">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    Belum ada akun rekening.
                                    <a href="{{ route('akun-pembayaran.index') }}" target="_blank">Tambah sekarang</a>
                                </div>
                            @else
                                <div class="form-text text-muted">
                                    <i class="bi bi-info-circle me-1"></i>Pilih rekening/QRIS tujuan pembayaran, atau biarkan kosong untuk Cash.
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4 shadow-sm" id="btnTambahSubmit">
                        <i class="bi bi-save me-2"></i>Simpan Biaya
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════
     MODAL: EDIT BIAYA (satu modal, diisi via JS)
══════════════════════════════════════════════ --}}
<div class="modal fade" id="modalEditBiaya" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content border-0 shadow">
            <form id="formEditBiaya" method="POST" novalidate>
                @csrf @method('PUT')
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-pencil-square me-2"></i>Edit Biaya
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">

                    {{-- Nama Biaya --}}
                    <div class="mb-3">
                        <label class="form-label">Nama Biaya <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editNama" class="form-control"
                               autocomplete="off" required>
                        <div id="editNamaFeedback" class="small mt-1" style="display:none;"></div>
                    </div>

                    {{-- Nominal --}}
                    <div class="mb-3">
                        <label class="form-label">Nominal (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" id="editNominal" class="form-control" min="0" required>
                    </div>

                    {{-- Metode Pembayaran --}}
                    <div class="mb-1">
                        <label class="form-label">Metode Pembayaran</label>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="editIsCash" onchange="toggleAkunEdit(this)">
                            <label class="form-check-label" for="editIsCash">
                                Cash / Tunai <span class="text-muted small">(tidak melalui rekening)</span>
                            </label>
                        </div>
                        <div id="editAkunWrapper">
                            <select name="account_id" id="editAkunSelect" class="form-select">
                                <option value="">— Tanpa rekening (Cash / Tunai) —</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">
                                        @if($acc->is_qris)
                                            QRIS — {{ $acc->bank_name }}
                                        @else
                                            {{ $acc->bank_name }}
                                            @if($acc->account_number) ({{ $acc->account_number }}) @endif
                                            @if($acc->account_holder) — a.n. {{ $acc->account_holder }} @endif
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text text-muted">
                                <i class="bi bi-info-circle me-1"></i>Pilih rekening/QRIS, atau biarkan kosong untuk Cash.
                            </div>
                        </div>
                    </div>

                    {{-- ID tersembunyi untuk cek duplikat nama --}}
                    <input type="hidden" id="editBiayaId" value="">

                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4 shadow-sm">
                        <i class="bi bi-save me-2"></i>Perbarui Biaya
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const CSRF      = document.querySelector('meta[name="csrf-token"]').content;
const CHECK_URL = '{{ route("biaya-murid.check-name") }}';

// ── Sidebar Toggle ────────────────────────────────────────
window.toggleSidebar = function () {
    if ($(window).width() <= 768) {
        $('#sidebar').toggleClass('show-mobile');
        $('#overlay').toggleClass('active');
    } else {
        $('#sidebar').toggleClass('inactive');
    }
};
$('#overlay').on('click', toggleSidebar);
$('#sidebarCollapse').on('click', toggleSidebar);

// ─────────────────────────────────────────────────────────
// Helper: cek nama biaya secara realtime (AJAX)
// ─────────────────────────────────────────────────────────
function attachNameCheck(inputEl, feedbackEl, excludeId = null) {
    let timer;
    inputEl.addEventListener('input', function () {
        clearTimeout(timer);
        const name = this.value.trim();
        feedbackEl.style.display = 'none';
        this.classList.remove('name-ok', 'name-fail');

        if (!name) return;

        timer = setTimeout(() => {
            fetch(CHECK_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                },
                body: JSON.stringify({ name, exclude_id: excludeId }),
            })
            .then(r => r.json())
            .then(data => {
                if (data.exists) {
                    feedbackEl.textContent = '⚠ Nama biaya ini sudah terdaftar.';
                    feedbackEl.className   = 'small mt-1 text-danger';
                    feedbackEl.style.display = 'block';
                    inputEl.classList.add('name-fail');
                } else {
                    feedbackEl.textContent   = '✓ Nama tersedia.';
                    feedbackEl.className     = 'small mt-1 text-success';
                    feedbackEl.style.display = 'block';
                    inputEl.classList.add('name-ok');
                }
            })
            .catch(() => {});
        }, 350);
    });
}

// ── Attach ke form tambah ─────────────────────────────────
attachNameCheck(
    document.getElementById('tambahNama'),
    document.getElementById('tambahNamaFeedback')
);

// ── Validasi form tambah sebelum submit ───────────────────
document.getElementById('formTambahBiaya').addEventListener('submit', function (e) {
    const nameInput = document.getElementById('tambahNama');
    if (nameInput.classList.contains('name-fail')) {
        e.preventDefault();
        nameInput.focus();
        return false;
    }
});

// ── Reset modal tambah saat ditutup ───────────────────────
document.getElementById('modalTambahBiaya').addEventListener('hidden.bs.modal', function () {
    document.getElementById('formTambahBiaya').reset();
    const fb = document.getElementById('tambahNamaFeedback');
    fb.style.display = 'none';
    document.getElementById('tambahNama').classList.remove('name-ok', 'name-fail');
    document.getElementById('tambahAkunWrapper').style.display = 'block';
    document.getElementById('tambahIsCash').checked = false;
});

// ─────────────────────────────────────────────────────────
// Toggle rekening: form Tambah
// ─────────────────────────────────────────────────────────
function toggleAkunTambah(cb) {
    const wrapper = document.getElementById('tambahAkunWrapper');
    const select  = document.getElementById('tambahAkunSelect');
    if (cb.checked) {
        wrapper.style.display = 'none';
        select.value = '';
    } else {
        wrapper.style.display = 'block';
    }
}

// ─────────────────────────────────────────────────────────
// Toggle rekening: form Edit
// ─────────────────────────────────────────────────────────
function toggleAkunEdit(cb) {
    const wrapper = document.getElementById('editAkunWrapper');
    const select  = document.getElementById('editAkunSelect');
    if (cb.checked) {
        wrapper.style.display = 'none';
        select.value = '';
    } else {
        wrapper.style.display = 'block';
    }
}

// ─────────────────────────────────────────────────────────
// Buka modal Edit — isi nilai dari data baris tabel
// ─────────────────────────────────────────────────────────
function bukaModalEdit(id, nama, nominal, accountId) {
    // Set action form
    document.getElementById('formEditBiaya').action = '/biaya-murid/' + id;
    document.getElementById('editBiayaId').value    = id;

    // Isi field
    const namaEl    = document.getElementById('editNama');
    namaEl.value    = nama;
    namaEl.classList.remove('name-ok', 'name-fail');
    document.getElementById('editNamaFeedback').style.display = 'none';

    document.getElementById('editNominal').value = nominal;

    // Set rekening/QRIS atau cash
    const selectEl = document.getElementById('editAkunSelect');
    const isCashCb = document.getElementById('editIsCash');
    const wrapper  = document.getElementById('editAkunWrapper');

    if (!accountId || accountId === '') {
        isCashCb.checked      = true;
        wrapper.style.display = 'none';
        selectEl.value        = '';
    } else {
        isCashCb.checked      = false;
        wrapper.style.display = 'block';
        selectEl.value        = accountId;
    }

    // Attach name check dengan exclude ID agar nama sendiri tidak dianggap duplikat
    // Re-attach setiap buka modal agar exclude_id selalu update
    const feedbackEl = document.getElementById('editNamaFeedback');
    const oldEl      = namaEl.cloneNode(true); // hapus listener lama
    namaEl.parentNode.replaceChild(oldEl, namaEl);
    attachNameCheck(
        document.getElementById('editNama'),
        feedbackEl,
        id
    );

    // Buka modal
    new bootstrap.Modal(document.getElementById('modalEditBiaya')).show();
}

// ── Validasi form edit sebelum submit ─────────────────────
document.getElementById('formEditBiaya').addEventListener('submit', function (e) {
    const nameInput = document.getElementById('editNama');
    if (nameInput.classList.contains('name-fail')) {
        e.preventDefault();
        nameInput.focus();
        return false;
    }
});
</script>
</body>
</html>
