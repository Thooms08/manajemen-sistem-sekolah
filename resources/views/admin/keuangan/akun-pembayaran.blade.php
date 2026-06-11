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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root { 
            --primary-green: #198754; 
            --dark-green: #0b4629;
            --soft-green: #f4f7f6; 
        }
        
        body { 
            background-color: var(--soft-green); 
            font-family: 'Plus Jakarta Sans', sans-serif; 
        }

        .wrapper { display: flex; width: 100%; align-items: stretch; }
        
        #content { width: 100%; padding: 20px; transition: all 0.3s; }
        
        .card { 
            border: none; 
            border-radius: 15px; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.05); 
        }

        .btn-success { background-color: var(--primary-green); border: none; }
        .btn-success:hover { background-color: var(--dark-green); }

        .table thead { 
            background-color: #1a3a3a; 
            color: white; 
        }

        .input-group-text { 
            cursor: pointer; 
            background: white; 
        }

        input:focus, textarea:focus, select:focus { border-color: #198754 !important; outline: none !important; box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25) !important;}

        @media (max-width: 768px) {
            #sidebar { margin-left: -250px; }
            #sidebar.active { margin-left: 0; }
        }
    </style>
</head>
<body>

<div class="wrapper">
    @include('admin.sidebar')

    <div id="content">
        <div class="container-fluid">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <button type="button" id="sidebarCollapse" class="btn btn-success me-3">
                        <i class="bi bi-list"></i>
                    </button>
                    <h4 class="fw-bold text-success mb-0">Kelola Akun Pembayaran</h4>
                </div>
                <div>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAturPembayaran">
                        <i class="bi bi-wallet2 me-1"></i> Atur Akun Rekening
                    </button>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th width="60" class="ps-3">No</th>
                                <th>Tipe</th>
                                <th>Nama Bank / Provider</th>
                                <th>No. Rekening</th>
                                <th>Nama Pemilik</th>
                                <th>QRIS</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($accounts as $i => $acc)
                            <tr>
                                <td class="ps-3">{{ $i + 1 }}</td>
                                <td>{{ $acc->is_qris ? 'QRIS' : 'Bank' }}</td>
                                <td class="fw-bold">{{ $acc->bank_name }}</td>
                                <td>{{ $acc->account_number ?? '-' }}</td>
                                <td>{{ $acc->account_holder ?? '-' }}</td>
                                <td>
                                    @if($acc->qris_image)
                                        <a href="{{ asset($acc->qris_image) }}" target="_blank">Lihat QRIS</a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-outline-success btn-sm me-1 btn-edit" 
                                        data-id="{{ $acc->id }}"
                                        data-bank="{{ $acc->bank_name }}"
                                        data-account="{{ $acc->account_number }}"
                                        data-holder="{{ $acc->account_holder }}"
                                        data-isqris="{{ $acc->is_qris }}"
                                        data-qris="{{ $acc->qris_image }}"
                                        >
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <form action="{{ route('akun-pembayaran.destroy', $acc->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm" onclick="return confirm('Hapus data pembayaran ini?')">
                                            <i class="bi bi-trash3-fill"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL ATUR PEMBAYARAN -->
<div class="modal fade" id="modalAturPembayaran" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('akun-pembayaran.store') }}" method="POST" enctype="multipart/form-data" id="formAtur">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold">Atur Akun Rekening</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 row g-3">
                    <div class="col-12">
                        <label class="form-label fw-bold small">Nama Bank / Provider</label>
                        <input type="text" name="bank_name" class="form-control" placeholder="Contoh: Mandiri, BRI, Dana" required>
                        <div class="form-text">Anda juga dapat memilih untuk menggunakan QRIS. Jika memilih QRIS, isi kolom QRIS dan kosongkan nomor rekening.</div>
                    </div>

                    <div class="col-12 form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="isQris" name="is_qris">
                        <label class="form-check-label" for="isQris">Gunakan QRIS (disable no. rekening & pemilik)</label>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold small">Nomor Rekening</label>
                        <input type="text" name="account_number" class="form-control" id="accountNumber">
                        <div class="text-danger small mt-1" id="accountNumberError" style="display:none;"></div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold small">Nama Pemilik Rekening</label>
                        <input type="text" name="account_holder" class="form-control" id="accountHolder">
                    </div>

                    <div class="col-12" id="qrisUpload" style="display:none;">
                        <label class="form-label fw-bold small">Upload Gambar QRIS (maks 2MB)</label>
                        <input type="file" name="qris_image" accept="image/*" class="form-control" id="qrisImage">
                        <div class="text-danger small mt-1" id="qrisError" style="display:none;"></div>
                        <div class="mt-3" id="qrisPreview" style="display:none;">
                            <img id="qrisPreviewImg" src="" alt="QRIS Preview" style="max-width: 200px; border-radius: 8px; border: 1px solid #ddd; padding: 5px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success w-25 py-2">SIMPAN</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDIT (DIPOPULATE VIA JS) -->
<div class="modal fade" id="modalEditPembayaran" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="#" method="POST" enctype="multipart/form-data" id="formEdit">
                @csrf
                @method('PUT')
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold">Edit Akun Pembayaran</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 row g-3">
                    <div class="col-12">
                        <label class="form-label fw-bold small">Nama Bank / Provider</label>
                        <input type="text" name="bank_name" class="form-control" id="editBankName" required>
                    </div>
                    <div class="col-12 form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="editIsQris" name="is_qris">
                        <label class="form-check-label" for="editIsQris">Gunakan QRIS (disable no. rekening & pemilik)</label>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold small">Nomor Rekening</label>
                        <input type="text" name="account_number" class="form-control" id="editAccountNumber">
                        <div class="text-danger small mt-1" id="editAccountNumberError" style="display:none;"></div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold small">Nama Pemilik Rekening</label>
                        <input type="text" name="account_holder" class="form-control" id="editAccountHolder">
                    </div>

                    <div class="col-12" id="editQrisUpload" style="display:none;">
                        <label class="form-label fw-bold small">Upload Gambar QRIS (maks 2MB)</label>
                        <input type="file" name="qris_image" accept="image/*" class="form-control" id="editQrisImage">
                        <div class="text-danger small mt-1" id="editQrisError" style="display:none;"></div>
                        <div class="small mt-2" id="currentQris"></div>
                        <div class="mt-3" id="editQrisPreview" style="display:none;">
                            <img id="editQrisPreviewImg" src="" alt="QRIS Preview" style="max-width: 200px; border-radius: 8px; border: 1px solid #ddd; padding: 5px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success w-25 py-2">SIMPAN</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Sidebar toggle (same logic as other views)
    const sidebar = document.getElementById('sidebar');
    const collapseBtn = document.getElementById('sidebarCollapse');
    collapseBtn.addEventListener('click', function() {
        if (window.innerWidth <= 768) {
            sidebar.classList.toggle('active');
        } else {
            sidebar.classList.toggle('inactive');
        }
    });

    // QRIS toggle for add modal + real-time detection from bank name
    const isQrisCheckbox = document.getElementById('isQris');
    const qrisUpload = document.getElementById('qrisUpload');
    const accountNumber = document.getElementById('accountNumber');
    const accountHolder = document.getElementById('accountHolder');
    const qrisImage = document.getElementById('qrisImage');
    const qrisError = document.getElementById('qrisError');
    const bankNameInput = document.querySelector('#modalAturPembayaran input[name="bank_name"]');

    function setAddModalQris(enabled) {
        isQrisCheckbox.checked = enabled;
        if (enabled) {
            qrisUpload.style.display = 'block';
            accountNumber.disabled = true;
            accountHolder.disabled = true;
        } else {
            qrisUpload.style.display = 'none';
            accountNumber.disabled = false;
            accountHolder.disabled = false;
            qrisError.style.display = 'none';
            qrisImage.value = '';
        }
    }

    isQrisCheckbox.addEventListener('change', function() { setAddModalQris(this.checked); });

    // Debounce helper
    function debounce(fn, wait){
        let t; return function(...a){ clearTimeout(t); t = setTimeout(()=>fn.apply(this,a), wait); }
    }

    // If user types 'qris' in bank name, enable QRIS automatically
    if (bankNameInput) {
        bankNameInput.addEventListener('input', debounce(function(e){
            const v = (e.target.value || '').toLowerCase();
            if (v.includes('qris')) {
                setAddModalQris(true);
            } else if (!isQrisCheckbox.checked) {
                setAddModalQris(false);
            }
        }, 250));
    }

    // Client-side image size validation (2MB)
    function validateImageSize(inputEl, errorEl) {
        errorEl.style.display = 'none';
        if (!inputEl.files || !inputEl.files[0]) return true;
        const f = inputEl.files[0];
        if (f.size > 2 * 1024 * 1024) {
            errorEl.innerText = 'Ukuran file melebihi 2MB.';
            errorEl.style.display = 'block';
            inputEl.value = null;
            return false;
        }
        return true;
    }

    qrisImage.addEventListener('change', function() { 
        validateImageSize(this, qrisError);
        const preview = document.getElementById('qrisPreview');
        const previewImg = document.getElementById('qrisPreviewImg');
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(this.files[0]);
        } else {
            preview.style.display = 'none';
        }
    });

    // EDIT modal population
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const bank = this.getAttribute('data-bank');
            const account = this.getAttribute('data-account');
            const holder = this.getAttribute('data-holder');
            const isqris = this.getAttribute('data-isqris') == '1' || this.getAttribute('data-isqris') == 'true';
            const qris = this.getAttribute('data-qris');

            document.getElementById('editBankName').value = bank;
            document.getElementById('editAccountNumber').value = account || '';
            document.getElementById('editAccountHolder').value = holder || '';
            document.getElementById('editIsQris').checked = isqris;

            const editQrisUpload = document.getElementById('editQrisUpload');
            const editAccountNumber = document.getElementById('editAccountNumber');
            const editAccountHolder = document.getElementById('editAccountHolder');
            const currentQris = document.getElementById('currentQris');

            function setEditModalQris(enabled){
                document.getElementById('editIsQris').checked = enabled;
                if (enabled) {
                    editQrisUpload.style.display = 'block';
                    editAccountNumber.disabled = true;
                    editAccountHolder.disabled = true;
                } else {
                    editQrisUpload.style.display = 'none';
                    editAccountNumber.disabled = false;
                    editAccountHolder.disabled = false;
                }
            }

            setEditModalQris(isqris);

            if (qris) {
                currentQris.innerHTML = `<a href="{{ asset('') }}\${qris}" target="_blank">QRIS saat ini</a>`;
            } else {
                currentQris.innerHTML = '';
            }

            // set form action
            const form = document.getElementById('formEdit');
            form.action = `/akun-pembayaran/${id}`;
            form.dataset.excludeId = id;

            // show modal
            var myModal = new bootstrap.Modal(document.getElementById('modalEditPembayaran'));
            myModal.show();
        });
    });

    // edit qris toggle
    const editIsQris = document.getElementById('editIsQris');
    const editQrisImage = document.getElementById('editQrisImage');
    const editQrisError = document.getElementById('editQrisError');
    editIsQris.addEventListener('change', function() {
        const editQrisUpload = document.getElementById('editQrisUpload');
        if (this.checked) {
            editQrisUpload.style.display = 'block';
            document.getElementById('editAccountNumber').disabled = true;
            document.getElementById('editAccountHolder').disabled = true;
        } else {
            editQrisUpload.style.display = 'none';
            document.getElementById('editAccountNumber').disabled = false;
            document.getElementById('editAccountHolder').disabled = false;
            editQrisError.style.display = 'none';
            editQrisImage.value = '';
        }
    });
    editQrisImage.addEventListener('change', function() { 
        validateImageSize(this, editQrisError);
        const preview = document.getElementById('editQrisPreview');
        const previewImg = document.getElementById('editQrisPreviewImg');
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(this.files[0]);
        } else {
            preview.style.display = 'none';
        }
    });
    
    // real-time detection for edit modal bank name
    const editBankInput = document.getElementById('editBankName');
    if (editBankInput) {
        editBankInput.addEventListener('input', debounce(function(e){
            const v = (e.target.value || '').toLowerCase();
            const should = v.includes('qris');
            // toggle the checkbox and fields accordingly
            document.getElementById('editIsQris').checked = should;
            const evt = new Event('change');
            document.getElementById('editIsQris').dispatchEvent(evt);
        }, 250));
    }

    // AJAX real-time duplicate check for account numbers
    const checkUrl = "{{ route('akun-pembayaran.checkNumber') }}";
    const formAtur = document.getElementById('formAtur');
    const formAturSubmit = formAtur.querySelector('button[type="submit"]');
    const accountNumberError = document.getElementById('accountNumberError');

    function checkAccountNumber(number, excludeId, cb){
        if (!number || number.trim() === '') { cb(false); return; }
        fetch(checkUrl + '?account_number=' + encodeURIComponent(number) + (excludeId ? '&exclude_id=' + excludeId : ''))
            .then(r => r.json())
            .then(data => cb(data))
            .catch(err => { console.error('Check failed', err); cb({exists:false}); });
    }

    // add listener for add modal
    document.getElementById('accountNumber').addEventListener('input', debounce(function(e){
        const v = e.target.value;
        checkAccountNumber(v, null, function(res){
            if (res.exists) {
                accountNumberError.innerText = res.message || 'Nomor rekening ini sudah digunakan. Silakan gunakan nomor rekening lain.';
                accountNumberError.style.display = 'block';
                formAturSubmit.disabled = true;
            } else {
                accountNumberError.style.display = 'none';
                formAturSubmit.disabled = false;
            }
        });
    }, 300));

    // add listener for edit modal
    const editAccountNumberEl = document.getElementById('editAccountNumber');
    const editAccountNumberError = document.getElementById('editAccountNumberError');
    if (editAccountNumberEl){
        editAccountNumberEl.addEventListener('input', debounce(function(e){
            const v = e.target.value;
            const form = document.getElementById('formEdit');
            const excludeId = form ? form.dataset.excludeId : null;
            checkAccountNumber(v, excludeId, function(res){
                if (res.exists) {
                    editAccountNumberError.innerText = res.message || 'Nomor rekening ini sudah digunakan. Silakan gunakan nomor rekening lain.';
                    editAccountNumberError.style.display = 'block';
                    // disable edit submit
                    const submit = document.querySelector('#formEdit button[type="submit"]');
                    if (submit) submit.disabled = true;
                } else {
                    editAccountNumberError.style.display = 'none';
                    const submit = document.querySelector('#formEdit button[type="submit"]');
                    if (submit) submit.disabled = false;
                }
            });
        }, 300));
    }

</script>

</body>
</html>
