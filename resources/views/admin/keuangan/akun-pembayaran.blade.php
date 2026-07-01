<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Akun Pembayaran</title>
    @if(isset($sekolah->logo))
    <link rel="icon" type="image/png" href="{{ \App\Helpers\ImageHelper::url($sekolah->logo) }}">
    @else
    <link rel="icon" type="image/png" href="{{ asset('assets/img/default-favicon.png') }}">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary-green: #198754; --dark-green: #0b4629; }
        body { background-color: #f4f7f6; font-family: 'Inter', sans-serif; overflow-x: hidden; }
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        #content { width: 100%; padding: 20px 30px; transition: all 0.3s; min-height: 100vh; min-width: 0; }
        #sidebarCollapse { width: 42px; height: 42px; background: var(--primary-green); border: none; color: white; border-radius: 10px; box-shadow: 0 4px 10px rgba(25,135,84,0.2); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .table thead { background-color: #1a3a3a; color: white; }
        .table thead th { font-size: 0.82rem; font-weight: 600; white-space: nowrap; }
        #overlay { display: none; position: fixed; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 1040; top: 0; left: 0; }
        #overlay.active { display: block; }
        input:focus, textarea:focus, select:focus { border-color: var(--primary-green) !important; outline: none !important; box-shadow: 0 0 0 0.2rem rgba(25,135,84,0.25) !important; }
        /* Mobile card */
        .akun-card-mobile { display: none; }
        .akun-card-item { background: #fff; border-radius: 12px; padding: 14px 16px; margin-bottom: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border-left: 4px solid var(--primary-green); }
        .akun-card-item .ac-title { font-weight: 700; font-size: 0.95rem; color: #1a3a3a; }
        .akun-card-item .ac-meta  { font-size: 0.8rem; color: #6c757d; margin-top: 3px; }
        .akun-card-item .ac-actions { display: flex; gap: 8px; margin-top: 10px; }
        .akun-card-item .ac-actions .btn { flex: 1; font-size: 0.82rem; }
        /* Responsive */
        @media (max-width: 991px) { #content { padding: 16px 18px; } }
        @media (max-width: 767px) {
            #content { padding: 12px 12px; }
            .page-header { flex-direction: column; align-items: flex-start !important; gap: 10px; }
            .page-header .btn-tambah { width: 100%; }
            .table-akun-desktop { display: none !important; }
            .akun-card-mobile { display: block; }
        }
    </style>
</head>
<body>
<div id="overlay"></div>
<div class="wrapper">
    @include('admin.sidebar')
    <div id="content">
        <div class="container-fluid">

            {{-- Header --}}
            <div class="d-flex align-items-center justify-content-between mb-4 mt-2 flex-wrap gap-2 page-header">
                <div class="d-flex align-items-center gap-3">
                    <button type="button" id="sidebarCollapse" class="btn"><i class="bi bi-list fs-4"></i></button>
                    <div>
                        <h4 class="mb-0 fw-bold text-success">Kelola Akun Pembayaran</h4>
                        <p class="text-muted small mb-0 d-none d-sm-block">Atur rekening bank dan QRIS untuk pembayaran</p>
                    </div>
                </div>
                <button class="btn btn-success fw-semibold shadow-sm btn-tambah" data-bs-toggle="modal" data-bs-target="#modalAturPembayaran">
                    <i class="bi bi-wallet2 me-2"></i>Atur Akun Rekening
                </button>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card p-3 p-md-4">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <p class="text-muted small mb-0">Total <strong>{{ $accounts->count() }}</strong> akun terdaftar.</p>
                </div>

                {{-- DESKTOP TABLE --}}
                <div class="table-responsive table-akun-desktop">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th width="50" class="ps-3">No</th>
                                <th>Tipe</th>
                                <th>Nama Bank / Provider</th>
                                <th>No. Rekening</th>
                                <th>Nama Pemilik</th>
                                <th>QRIS</th>
                                <th width="120" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($accounts as $i => $acc)
                            <tr>
                                <td class="ps-3">{{ $i + 1 }}</td>
                                <td>
                                    @if($acc->is_qris)
                                        <span class="badge bg-primary bg-opacity-10 text-primary">QRIS</span>
                                    @else
                                        <span class="badge bg-success bg-opacity-10 text-success">Bank</span>
                                    @endif
                                </td>
                                <td class="fw-semibold">{{ $acc->bank_name }}</td>
                                <td class="text-muted">{{ $acc->account_number ?? '-' }}</td>
                                <td>{{ $acc->account_holder ?? '-' }}</td>
                                <td>
                                    @if($acc->qris_image)
                                        <a href="{{ asset($acc->qris_image) }}" target="_blank" class="text-success text-decoration-none">
                                            <i class="bi bi-qr-code me-1"></i>Lihat QRIS
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-success border-0 btn-edit"
                                        data-id="{{ $acc->id }}" data-bank="{{ $acc->bank_name }}"
                                        data-account="{{ $acc->account_number }}" data-holder="{{ $acc->account_holder }}"
                                        data-isqris="{{ $acc->is_qris }}" data-qris="{{ $acc->qris_image }}" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <form action="{{ route('akun-pembayaran.destroy', $acc->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Hapus akun ini?')" title="Hapus">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-wallet2 fs-2 d-block mb-2 opacity-25"></i>
                                    Belum ada akun pembayaran.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- MOBILE CARD LIST --}}
                <div class="akun-card-mobile">
                    @forelse($accounts as $i => $acc)
                    <div class="akun-card-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="ac-title">{{ $acc->bank_name }}</div>
                            @if($acc->is_qris)
                                <span class="badge bg-primary bg-opacity-10 text-primary">QRIS</span>
                            @else
                                <span class="badge bg-success bg-opacity-10 text-success">Bank</span>
                            @endif
                        </div>
                        @if(!$acc->is_qris)
                        <div class="ac-meta"><i class="bi bi-credit-card me-1"></i>{{ $acc->account_number ?? '-' }}</div>
                        <div class="ac-meta"><i class="bi bi-person me-1"></i>{{ $acc->account_holder ?? '-' }}</div>
                        @endif
                        @if($acc->qris_image)
                        <div class="ac-meta">
                            <a href="{{ asset($acc->qris_image) }}" target="_blank" class="text-success text-decoration-none">
                                <i class="bi bi-qr-code me-1"></i>Lihat QRIS
                            </a>
                        </div>
                        @endif
                        <div class="ac-actions">
                            <button class="btn btn-outline-success btn-sm btn-edit"
                                data-id="{{ $acc->id }}" data-bank="{{ $acc->bank_name }}"
                                data-account="{{ $acc->account_number }}" data-holder="{{ $acc->account_holder }}"
                                data-isqris="{{ $acc->is_qris }}" data-qris="{{ $acc->qris_image }}">
                                <i class="bi bi-pencil-square me-1"></i>Edit
                            </button>
                            <form action="{{ route('akun-pembayaran.destroy', $acc->id) }}" method="POST" class="flex-fill">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-danger btn-sm w-100" onclick="return confirm('Hapus akun ini?')">
                                    <i class="bi bi-trash3 me-1"></i>Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-wallet2 fs-2 d-block mb-2 opacity-25"></i>
                        Belum ada akun pembayaran.
                    </div>
                    @endforelse
                </div>

            </div>{{-- end card --}}
        </div>
    </div>
</div>

{{-- Modal: Tambah Akun --}}
<div class="modal fade" id="modalAturPembayaran" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('akun-pembayaran.store') }}" method="POST" enctype="multipart/form-data" id="formAtur">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-wallet2 me-2"></i>Atur Akun Rekening</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Nama Bank / Provider <span class="text-danger">*</span></label>
                        <input type="text" name="bank_name" class="form-control" placeholder="Contoh: Mandiri, BRI, Dana" required>
                        <div class="form-text">Ketik "QRIS" untuk otomatis mengaktifkan mode QRIS.</div>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="isQris" name="is_qris">
                            <label class="form-check-label" for="isQris">Gunakan QRIS (nonaktifkan no. rekening & pemilik)</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Nomor Rekening</label>
                        <input type="text" name="account_number" class="form-control" id="accountNumber">
                        <div class="text-danger small mt-1" id="accountNumberError" style="display:none;"></div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Nama Pemilik Rekening</label>
                        <input type="text" name="account_holder" class="form-control" id="accountHolder">
                    </div>
                    <div class="col-12" id="qrisUpload" style="display:none;">
                        <label class="form-label fw-semibold small">Upload Gambar QRIS <span class="text-muted fw-normal">(maks 2MB)</span></label>
                        <input type="file" name="qris_image" accept="image/*" class="form-control" id="qrisImage">
                        <div class="text-danger small mt-1" id="qrisError" style="display:none;"></div>
                        <div class="mt-3" id="qrisPreview" style="display:none;">
                            <img id="qrisPreviewImg" src="" alt="QRIS Preview" style="max-width:180px; border-radius:8px; border:1px solid #ddd; padding:4px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4 fw-semibold">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Edit Akun --}}
<div class="modal fade" id="modalEditPembayaran" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content border-0 shadow">
            <form action="#" method="POST" enctype="multipart/form-data" id="formEdit">
                @csrf @method('PUT')
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Akun Pembayaran</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Nama Bank / Provider <span class="text-danger">*</span></label>
                        <input type="text" name="bank_name" class="form-control" id="editBankName" required>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="editIsQris" name="is_qris">
                            <label class="form-check-label" for="editIsQris">Gunakan QRIS</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Nomor Rekening</label>
                        <input type="text" name="account_number" class="form-control" id="editAccountNumber">
                        <div class="text-danger small mt-1" id="editAccountNumberError" style="display:none;"></div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Nama Pemilik Rekening</label>
                        <input type="text" name="account_holder" class="form-control" id="editAccountHolder">
                    </div>
                    <div class="col-12" id="editQrisUpload" style="display:none;">
                        <label class="form-label fw-semibold small">Upload Gambar QRIS <span class="text-muted fw-normal">(maks 2MB)</span></label>
                        <input type="file" name="qris_image" accept="image/*" class="form-control" id="editQrisImage">
                        <div class="text-danger small mt-1" id="editQrisError" style="display:none;"></div>
                        <div class="small mt-2" id="currentQris"></div>
                        <div class="mt-3" id="editQrisPreview" style="display:none;">
                            <img id="editQrisPreviewImg" src="" alt="QRIS Preview" style="max-width:180px; border-radius:8px; border:1px solid #ddd; padding:4px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4 fw-semibold">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function () {
    // Sidebar
    function toggleSidebar() {
        if ($(window).width() <= 768) { $('#sidebar').toggleClass('show-mobile'); $('#overlay').toggleClass('active'); }
        else { $('#sidebar').toggleClass('inactive'); }
    }
    $('#sidebarCollapse, #close-sidebar, #overlay').on('click', toggleSidebar);
});

function debounce(fn, wait) { let t; return function(...a){ clearTimeout(t); t = setTimeout(()=>fn.apply(this,a), wait); }; }

// QRIS toggle — add modal
function setAddModalQris(enabled) {
    document.getElementById('isQris').checked = enabled;
    document.getElementById('qrisUpload').style.display = enabled ? 'block' : 'none';
    document.getElementById('accountNumber').disabled = enabled;
    document.getElementById('accountHolder').disabled = enabled;
    if (!enabled) { document.getElementById('qrisError').style.display = 'none'; document.getElementById('qrisImage').value=''; }
}
document.getElementById('isQris').addEventListener('change', function(){ setAddModalQris(this.checked); });

const bankNameInput = document.querySelector('#modalAturPembayaran input[name="bank_name"]');
if (bankNameInput) {
    bankNameInput.addEventListener('input', debounce(function(e){
        if ((e.target.value||'').toLowerCase().includes('qris')) setAddModalQris(true);
        else if (!document.getElementById('isQris').checked) setAddModalQris(false);
    }, 250));
}

function validateImageSize(inputEl, errorEl) {
    errorEl.style.display = 'none';
    if (!inputEl.files || !inputEl.files[0]) return true;
    if (inputEl.files[0].size > 2*1024*1024) { errorEl.innerText='Ukuran file melebihi 2MB.'; errorEl.style.display='block'; inputEl.value=null; return false; }
    return true;
}
function attachPreview(inputId, previewId, previewImgId) {
    document.getElementById(inputId).addEventListener('change', function(){
        validateImageSize(this, document.getElementById(inputId.replace('Image','Error').replace('editQris','editQris')));
        const p=document.getElementById(previewId), img=document.getElementById(previewImgId);
        if(this.files&&this.files[0]){ const r=new FileReader(); r.onload=e=>{img.src=e.target.result;p.style.display='block';}; r.readAsDataURL(this.files[0]); } else p.style.display='none';
    });
}
attachPreview('qrisImage','qrisPreview','qrisPreviewImg');
attachPreview('editQrisImage','editQrisPreview','editQrisPreviewImg');

// Edit modal populate
document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', function(){
        const id=this.dataset.id, bank=this.dataset.bank, account=this.dataset.account, holder=this.dataset.holder;
        const isqris=this.dataset.isqris=='1'||this.dataset.isqris=='true', qris=this.dataset.qris;
        document.getElementById('editBankName').value=bank;
        document.getElementById('editAccountNumber').value=account||'';
        document.getElementById('editAccountHolder').value=holder||'';
        const form=document.getElementById('formEdit');
        form.action=`/akun-pembayaran/${id}`; form.dataset.excludeId=id;
        // toggle qris
        document.getElementById('editIsQris').checked=isqris;
        document.getElementById('editQrisUpload').style.display=isqris?'block':'none';
        document.getElementById('editAccountNumber').disabled=isqris;
        document.getElementById('editAccountHolder').disabled=isqris;
        document.getElementById('currentQris').innerHTML=qris?`<a href="{{ asset('') }}\${qris}" target="_blank">QRIS saat ini</a>`:'';
        new bootstrap.Modal(document.getElementById('modalEditPembayaran')).show();
    });
});
document.getElementById('editIsQris').addEventListener('change', function(){
    document.getElementById('editQrisUpload').style.display=this.checked?'block':'none';
    document.getElementById('editAccountNumber').disabled=this.checked;
    document.getElementById('editAccountHolder').disabled=this.checked;
    if(!this.checked){document.getElementById('editQrisError').style.display='none';document.getElementById('editQrisImage').value='';}
});
document.getElementById('editBankName').addEventListener('input', debounce(function(e){
    const v=(e.target.value||'').toLowerCase();
    document.getElementById('editIsQris').checked=v.includes('qris');
    document.getElementById('editIsQris').dispatchEvent(new Event('change'));
},250));

// Account number duplicate check
const checkUrl="{{ route('akun-pembayaran.checkNumber') }}";
function checkAccountNumber(number, excludeId, cb){
    if(!number||!number.trim()){cb({exists:false});return;}
    fetch(checkUrl+'?account_number='+encodeURIComponent(number)+(excludeId?'&exclude_id='+excludeId:''))
        .then(r=>r.json()).then(cb).catch(()=>cb({exists:false}));
}
document.getElementById('accountNumber').addEventListener('input', debounce(function(e){
    const errEl=document.getElementById('accountNumberError'), submitBtn=document.querySelector('#formAtur button[type="submit"]');
    checkAccountNumber(e.target.value, null, res=>{
        errEl.style.display=res.exists?'block':'none';
        if(res.exists) errEl.innerText=res.message||'Nomor rekening sudah digunakan.';
        if(submitBtn) submitBtn.disabled=!!res.exists;
    });
},300));
const editAccEl=document.getElementById('editAccountNumber'), editAccErr=document.getElementById('editAccountNumberError');
if(editAccEl){
    editAccEl.addEventListener('input', debounce(function(e){
        const form=document.getElementById('formEdit'), excl=form?form.dataset.excludeId:null;
        checkAccountNumber(e.target.value, excl, res=>{
            editAccErr.style.display=res.exists?'block':'none';
            if(res.exists) editAccErr.innerText=res.message||'Nomor rekening sudah digunakan.';
            const s=document.querySelector('#formEdit button[type="submit"]');
            if(s) s.disabled=!!res.exists;
        });
    },300));
}
</script>
</body>
</html>
