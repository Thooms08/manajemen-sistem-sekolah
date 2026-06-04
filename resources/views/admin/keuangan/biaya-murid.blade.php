<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kelola Biaya Murid</title>
    @if(isset($sekolah->logo))
    <link rel="icon" type="image/png" href="{{ asset($sekolah->logo) }}">
    @else
    <link rel="icon" type="image/png" href="{{ asset('assets/img/default-favicon.png') }}">
    @endif

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root { --primary-green: #198754; --dark-green: #0b4629; --soft-green: #f4f7f6; }
        body { background-color: var(--soft-green); font-family: 'Plus Jakarta Sans', sans-serif; }
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        #content { width: 100%; padding: 20px; }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .btn-success { background-color: var(--primary-green); border: none; }
        .table thead { background-color: #1a3a3a; color: white; }
        input:focus, textarea:focus, select:focus { border-color: #198754 !important; outline: none !important; box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25) !important;}
    </style>
</head>
<body>

<div class="wrapper">
    @include('admin.sidebar')

    <div id="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <button type="button" id="sidebarCollapse" class="btn btn-success me-3"><i class="bi bi-list"></i></button>
                    <h4 class="fw-bold text-success mb-0">Kelola Biaya Murid</h4>
                </div>
                <div>
                    <a href="{{ route('akun-pembayaran.index') }}" class="btn btn-outline-success me-2">Atur Akun Rekening</a>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAturNominal">Atur Nominal Biaya</button>
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
                                <th>Nama Biaya</th>
                                <th>Nominal</th>
                                <th>Rekening</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($biayas as $i => $b)
                            <tr>
                                <td class="ps-3">{{ $i + 1 }}</td>
                                <td class="fw-bold text-dark">{{ $b->name }}</td>
                                <td>Rp {{ number_format($b->amount,0,',','.') }}</td>
                                <td>{{ $b->account ? $b->account->bank_name . ' (' . ($b->account->account_number ?? '-') . ')' : '-' }}</td>
                                <td class="text-center">
                                    <button class="btn btn-outline-success btn-sm me-1" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $b->id }}"><i class="bi bi-pencil-square"></i></button>
                                    <form action="{{ route('biaya-murid.destroy', $b->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm" onclick="return confirm('Hapus biaya ini?')"><i class="bi bi-trash3-fill"></i></button>
                                    </form>
                                </td>
                            </tr>

                            <!-- EDIT MODAL PER BARIS -->
                            <div class="modal fade" id="modalEdit{{ $b->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        <form action="{{ route('biaya-murid.update', $b->id) }}" method="POST">
                                            @csrf @method('PUT')
                                            <div class="modal-header bg-success text-white">
                                                <h5 class="modal-title fw-bold">Edit: {{ $b->name }}</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-4 row g-3">
                                                <div class="col-12"><label class="form-label small fw-bold">Nama Biaya</label>
                                                    <input type="text" name="name" class="form-control" value="{{ $b->name }}" required>
                                                </div>
                                                <div class="col-12"><label class="form-label small fw-bold">Nominal</label>
                                                    <input type="number" name="amount" class="form-control" value="{{ $b->amount }}" required>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox" id="editCash{{ $b->id }}" name="is_cash" value="1" {{ $b->account_id === null ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="editCash{{ $b->id }}">Cash/Tunai</label>
                                                    </div>
                                                    <div class="form-group" id="editAccountGroup{{ $b->id }}">
                                                        <label class="form-label small fw-bold">Pilih Rekening (opsional)</label>
                                                        <select name="account_id" class="form-select" id="editAccountSelect{{ $b->id }}" {{ $b->account_id === null ? 'disabled' : '' }}>
                                                            <option value="">- Pilih -</option>
                                                            @foreach($accounts as $acc)
                                                                <option value="{{ $acc->id }}" {{ $b->account_id == $acc->id ? 'selected' : '' }}>{{ $acc->bank_name }} ({{ $acc->account_number ?? 'QRIS' }})</option>
                                                            @endforeach
                                                        </select>
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

                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL ATUR NOMINAL (BATCH) -->
<div class="modal fade" id="modalAturNominal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('biaya-murid.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold">Atur Nominal Biaya</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Biaya Pendaftaran</label>
                            <input type="text" name="fee_name[]" class="form-control fee-name-input" value="Biaya Pendaftaran" readonly data-fee-index="0">
                            <input type="hidden" name="fee_account[]" data-fee-index="0" value="">
                            <div class="fee-name-alert text-danger small mt-1" style="display:none;"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nominal</label>
                            <input type="number" name="fee_amount[]" class="form-control" data-fee-index="0">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Biaya Bangunan</label>
                            <input type="text" name="fee_name[]" class="form-control fee-name-input" value="Biaya Bangunan" readonly data-fee-index="1">
                            <input type="hidden" name="fee_account[]" data-fee-index="1" value="">
                            <div class="fee-name-alert text-danger small mt-1" style="display:none;"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nominal</label>
                            <input type="number" name="fee_amount[]" class="form-control" data-fee-index="1">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Biaya SPP / Bulan</label>
                            <input type="text" name="fee_name[]" class="form-control fee-name-input" value="Biaya SPP" readonly data-fee-index="2">
                            <input type="hidden" name="fee_account[]" data-fee-index="2" value="">
                            <div class="fee-name-alert text-danger small mt-1" style="display:none;"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nominal</label>
                            <input type="number" name="fee_amount[]" class="form-control" data-fee-index="2">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Biaya Atribut / Seragam</label>
                            <input type="text" name="fee_name[]" class="form-control fee-name-input" value="Biaya Atribut Seragam" readonly data-fee-index="3">
                            <input type="hidden" name="fee_account[]" data-fee-index="3" value="">
                            <div class="fee-name-alert text-danger small mt-1" style="display:none;"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nominal</label>
                            <input type="number" name="fee_amount[]" class="form-control" data-fee-index="3">
                        </div>
                    </div>

                    <hr>
                    <div id="otherFeesContainer">
                        <div class="row g-3 mb-3 other-fee-row">
                            <div class="col-md-6">
                                <input type="text" name="fee_name[]" class="form-control fee-name-input" placeholder="Nama biaya lainnya">
                                <input type="hidden" name="fee_account[]" value="">
                                <div class="fee-name-alert text-danger small mt-1" style="display:none;"></div>
                            </div>
                            <div class="col-md-5">
                                <input type="number" name="fee_amount[]" class="form-control" placeholder="Nominal">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-outline-success btn-add-row">+</button>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="modeSingle" name="mode" value="single">
                            <label class="form-check-label" for="modeSingle">Setiap biaya satu akun rekening (satu rekening untuk semua biaya)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="modeIndividual" name="mode" value="individual">
                            <label class="form-check-label" for="modeIndividual">Setiap biaya memiliki masing-masing akun rekening</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="modeIndividualCash" name="mode" value="individual_cash">
                            <label class="form-check-label" for="modeIndividualCash">Masing-masing setiap biaya dibayar tunai/cash</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="modeAllCash" name="mode" value="all_cash">
                            <label class="form-check-label" for="modeAllCash">Semua biaya dibayar tunai/cash</label>
                        </div>
                    </div>

                    <div id="singleAccountSelect" style="display:none;" class="mb-3">
                        <label class="form-label small fw-bold">Pilih Rekening untuk Semua Biaya</label>
                        <select class="form-select" name="single_account_id">
                            <option value="">- Pilih Rekening -</option>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->bank_name }} ({{ $acc->account_number ?? 'QRIS' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="individualAccountMapping" style="display:none;">
                        <div class="small text-muted mb-3">Pilih rekening untuk masing-masing biaya (centang lalu pilih rekening)</div>
                        <div id="mappingContainer">
                            <!-- Populated dynamically by JavaScript -->
                        </div>
                    </div>

                    <div id="individualCashMapping" style="display:none;">
                        <div class="small text-muted mb-3">Centang biaya yang dibayar tunai/cash</div>
                        <div id="cashMappingContainer">
                            <!-- Populated dynamically by JavaScript -->
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
    // add other fee rows
    document.addEventListener('click', function(e){
        if (e.target && e.target.classList.contains('btn-add-row')){
            const container = document.getElementById('otherFeesContainer');
            const newRow = document.createElement('div');
            newRow.className = 'row g-3 mb-3 other-fee-row';
            newRow.innerHTML = `\
                <div class="col-md-6">\
                    <input type="text" name="fee_name[]" class="form-control fee-name-input" placeholder="Nama biaya lainnya">\
                    <input type="hidden" name="fee_account[]" value="">\
                    <div class="fee-name-alert text-danger small mt-1" style="display:none;"></div>\
                </div>\
                <div class="col-md-5">\
                    <input type="number" name="fee_amount[]" class="form-control" placeholder="Nominal">\
                </div>\
                <div class="col-md-1">\
                    <button type="button" class="btn btn-outline-danger btn-remove-row">-</button>\
                </div>`;
            container.appendChild(newRow);
            // Rebuild mapping jika mode individual aktif
            if (document.getElementById('modeIndividual').checked) {
                buildIndividualMapping();
            }
            if (document.getElementById('modeIndividualCash').checked) {
                buildIndividualCashMapping();
            }
            // Attach fee name check listener to new input
            const newNameInput = newRow.querySelector('.fee-name-input');
            attachFeeNameCheckListener(newNameInput);
        }
        if (e.target && e.target.classList.contains('btn-remove-row')){
            e.target.closest('.other-fee-row').remove();
            // Rebuild mapping jika mode individual aktif
            if (document.getElementById('modeIndividual').checked) {
                buildIndividualMapping();
            }
            if (document.getElementById('modeIndividualCash').checked) {
                buildIndividualCashMapping();
            }
        }
    });

    // Function to attach fee name check listener
    function attachFeeNameCheckListener(input) {
        let debounceTimer;
        input.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const name = this.value.trim();
            const alertDiv = this.parentElement.querySelector('.fee-name-alert');
            
            if (!name) {
                alertDiv.style.display = 'none';
                alertDiv.textContent = '';
                return;
            }
            
            debounceTimer = setTimeout(() => {
                fetch('{{ route("biaya-murid.check-name") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ name: name })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        alertDiv.textContent = 'Biaya ini sudah di atur';
                        alertDiv.style.display = 'block';
                    } else {
                        alertDiv.style.display = 'none';
                        alertDiv.textContent = '';
                    }
                })
                .catch(error => {
                    console.error('Error checking fee name:', error);
                });
            }, 300); // 300ms debounce
        });
    }

    // Attach fee name check listeners to existing inputs on page load
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.fee-name-input').forEach(input => {
            attachFeeNameCheckListener(input);
        });
    });

    // Check existing fee names when modal is opened
    const modalAturNominal = document.getElementById('modalAturNominal');
    if (modalAturNominal) {
        modalAturNominal.addEventListener('shown.bs.modal', function() {
            document.querySelectorAll('.fee-name-input').forEach(input => {
                const name = input.value.trim();
                const alertDiv = input.parentElement.querySelector('.fee-name-alert');
                
                if (name) {
                    fetch('{{ route("biaya-murid.check-name") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ name: name })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            alertDiv.textContent = 'Biaya ini sudah di atur';
                            alertDiv.style.display = 'block';
                        } else {
                            alertDiv.style.display = 'none';
                            alertDiv.textContent = '';
                        }
                    })
                    .catch(error => {
                        console.error('Error checking fee name:', error);
                    });
                }
            });
        });
    }

    // Function to build individual mapping dynamically
    function buildIndividualMapping() {
        const container = document.getElementById('mappingContainer');
        container.innerHTML = '';
        
        const feeNames = document.querySelectorAll('input[name="fee_name[]"]');
        const feeAmounts = document.querySelectorAll('input[name="fee_amount[]"]');
        const feeAccounts = document.querySelectorAll('input[name="fee_account[]"]');
        
        feeNames.forEach((nameInput, idx) => {
            const name = nameInput.value.trim();
            const amountInput = feeAmounts[idx];
            const amount = amountInput ? amountInput.value : '';
            const accountInput = feeAccounts[idx];
            
            // Only show mapping row if fee name is filled AND amount is filled and not zero
            if (name && amount && amount !== '' && amount !== '0') {
                const row = document.createElement('div');
                row.className = 'row g-3 align-items-center mb-2';
                row.setAttribute('data-fee-idx', idx);
                
                row.innerHTML = `\
                    <div class="col-md-4">\
                        <div class="form-check">\
                            <input class="form-check-input map-check" type="checkbox" id="mapCheck${idx}" data-index="${idx}">\
                            <label class="form-check-label" for="mapCheck${idx}">${name}</label>\
                        </div>\
                    </div>\
                    <div class="col-md-8">\
                        <select class="form-select map-select" data-index="${idx}" style="display:none;">\
                            <option value="">- Pilih Rekening -</option>\
                            @foreach($accounts as $acc)\
                                <option value="{{ $acc->id }}">{{ $acc->bank_name }} ({{ $acc->account_number ?? 'QRIS' }})</option>\
                            @endforeach\
                        </select>\
                        <div class="small text-success map-info" style="display:none;"></div>\
                    </div>\
                `;
                
                container.appendChild(row);
                
                // Attach event listeners
                const checkbox = row.querySelector('.map-check');
                const select = row.querySelector('.map-select');
                const info = row.querySelector('.map-info');
                
                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        select.style.display = 'block';
                    } else {
                        select.style.display = 'none';
                        select.value = '';
                        info.style.display = 'none';
                        accountInput.value = '';
                    }
                });
                
                select.addEventListener('change', function() {
                    accountInput.value = this.value;
                    const text = this.options[this.selectedIndex].text;
                    if (text && this.value) {
                        info.innerText = 'Biaya ini akan menggunakan rekening: ' + text;
                        info.style.display = 'block';
                    } else {
                        info.style.display = 'none';
                    }
                });
            }
        });
    }

    // mode toggles
    const modeSingle = document.getElementById('modeSingle');
    const modeIndividual = document.getElementById('modeIndividual');
    const modeIndividualCash = document.getElementById('modeIndividualCash');
    const modeAllCash = document.getElementById('modeAllCash');
    const singleAccountSelect = document.getElementById('singleAccountSelect');
    const individualAccountMapping = document.getElementById('individualAccountMapping');
    const individualCashMapping = document.getElementById('individualCashMapping');

    modeSingle.addEventListener('change', function(){
        if (this.checked){
            modeIndividual.checked = false;
            modeIndividualCash.checked = false;
            modeAllCash.checked = false;
            singleAccountSelect.style.display = 'block';
            individualAccountMapping.style.display = 'none';
            individualCashMapping.style.display = 'none';
        } else {
            singleAccountSelect.style.display = 'none';
        }
    });
    
    modeIndividual.addEventListener('change', function(){
        if (this.checked){
            modeSingle.checked = false;
            modeIndividualCash.checked = false;
            modeAllCash.checked = false;
            individualAccountMapping.style.display = 'block';
            singleAccountSelect.style.display = 'none';
            individualCashMapping.style.display = 'none';
            buildIndividualMapping();
        } else {
            individualAccountMapping.style.display = 'none';
        }
    });

    modeIndividualCash.addEventListener('change', function(){
        if (this.checked){
            modeSingle.checked = false;
            modeIndividual.checked = false;
            modeAllCash.checked = false;
            individualCashMapping.style.display = 'block';
            singleAccountSelect.style.display = 'none';
            individualAccountMapping.style.display = 'none';
            buildIndividualCashMapping();
        } else {
            individualCashMapping.style.display = 'none';
        }
    });

    modeAllCash.addEventListener('change', function(){
        if (this.checked){
            modeSingle.checked = false;
            modeIndividual.checked = false;
            modeIndividualCash.checked = false;
            singleAccountSelect.style.display = 'none';
            individualAccountMapping.style.display = 'none';
            individualCashMapping.style.display = 'none';
        } else {
            // No need to show anything when unchecked
        }
    });

    // Real-time update: rebuild mapping when amount fields change
    document.querySelectorAll('input[name="fee_amount[]"]').forEach(input => {
        input.addEventListener('input', function() {
            if (modeIndividual.checked) {
                buildIndividualMapping();
            }
            if (modeIndividualCash.checked) {
                buildIndividualCashMapping();
            }
        });
    });

    // Real-time update: rebuild mapping when name fields change (for other fees)
    document.querySelectorAll('input[name="fee_name[]"]').forEach(input => {
        input.addEventListener('input', function() {
            if (modeIndividual.checked) {
                buildIndividualMapping();
            }
            if (modeIndividualCash.checked) {
                buildIndividualCashMapping();
            }
        });
    });

    // Function to build individual cash mapping dynamically
    function buildIndividualCashMapping() {
        const container = document.getElementById('cashMappingContainer');
        container.innerHTML = '';
        
        const feeNames = document.querySelectorAll('input[name="fee_name[]"]');
        const feeAmounts = document.querySelectorAll('input[name="fee_amount[]"]');
        const feeAccounts = document.querySelectorAll('input[name="fee_account[]"]');
        
        feeNames.forEach((nameInput, idx) => {
            const name = nameInput.value.trim();
            const amountInput = feeAmounts[idx];
            const amount = amountInput ? amountInput.value : '';
            const accountInput = feeAccounts[idx];
            
            // Only show mapping row if fee name is filled AND amount is filled and not zero
            if (name && amount && amount !== '' && amount !== '0') {
                const row = document.createElement('div');
                row.className = 'row g-3 align-items-center mb-2';
                row.setAttribute('data-fee-idx', idx);
                
                row.innerHTML = `\
                    <div class="col-md-12">\
                        <div class="form-check">\
                            <input class="form-check-input cash-check" type="checkbox" id="cashCheck${idx}" data-index="${idx}">\
                            <label class="form-check-label" for="cashCheck${idx}">${name} - Rp ${parseInt(amount).toLocaleString('id-ID')}</label>\
                        </div>\
                    </div>\
                `;
                
                container.appendChild(row);
                
                // Attach event listeners
                const checkbox = row.querySelector('.cash-check');
                
                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        // Set account_id to empty string to indicate cash payment
                        accountInput.value = '';
                    } else {
                        // Reset to empty
                        accountInput.value = '';
                    }
                });
            }
        });
    }

    // Form submission handler
    const modalForm = document.querySelector('#modalAturNominal form');
    if (modalForm) {
        modalForm.addEventListener('submit', function(e) {
            // Check if individual mode is active
            if (modeIndividual.checked) {
                // Count how many checkboxes are checked
                const checkedBoxes = document.querySelectorAll('.map-check:checked');
                if (checkedBoxes.length === 1) {
                    e.preventDefault();
                    e.stopPropagation();
                    alert('Harap pilih minimal 2 biaya untuk mode "Setiap biaya memiliki masing-masing akun rekening". Jika hanya 1 biaya, gunakan mode "Setiap biaya satu akun rekening" saja.');
                    return false;
                }
            }
            
            // Check if individual cash mode is active
            if (modeIndividualCash.checked) {
                // Count how many checkboxes are checked
                const checkedBoxes = document.querySelectorAll('.cash-check:checked');
                if (checkedBoxes.length === 1) {
                    e.preventDefault();
                    e.stopPropagation();
                    alert('Harap pilih minimal 2 biaya untuk mode "Masing-masing setiap biaya dibayar tunai/cash". Jika hanya 1 biaya, gunakan mode "Semua biaya dibayar tunai/cash" saja.');
                    return false;
                }
            }
            
            // Update fee_account[] based on mode
            const feeNames = document.querySelectorAll('input[name="fee_name[]"]');
            const singleAccountId = document.querySelector('select[name="single_account_id"]').value;
            const isSingleMode = modeSingle.checked;
            const isAllCashMode = modeAllCash.checked;
            const isIndividualCashMode = modeIndividualCash.checked;
            
            feeNames.forEach((nameInput, idx) => {
                const accountInput = document.querySelectorAll('input[name="fee_account[]"]')[idx];
                const name = nameInput.value.trim();
                
                if (isSingleMode && name) {
                    // Gunakan single account untuk semua biaya
                    accountInput.value = singleAccountId;
                } else if (isAllCashMode && name) {
                    // Set semua account_id ke null untuk cash payment
                    accountInput.value = '';
                } else if (isIndividualCashMode && name) {
                    // Nilai sudah di-update via cash checkbox change listener
                    // Tidak perlu diubah lagi di sini
                } else if (!isSingleMode && !isAllCashMode && !isIndividualCashMode && name) {
                    // Nilai sudah di-update via select change listener
                    // Tidak perlu diubah lagi di sini
                }
            });
        }, false);
    }

    // Edit modal cash checkbox logic
    document.querySelectorAll('input[name="is_cash"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const id = this.id.replace('editCash', '');
            const accountSelect = document.getElementById('editAccountSelect' + id);
            if (this.checked) {
                accountSelect.disabled = true;
                accountSelect.value = '';
            } else {
                accountSelect.disabled = false;
            }
        });
    });
</script>

</body>
</html>