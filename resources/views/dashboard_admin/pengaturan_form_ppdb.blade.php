<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Form PPDB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f4f7f6; }
        .wrapper { display: flex; width: 100%; }
        #content { width: 100%; padding: 20px 30px; }
        .card { border: none; border-radius: 15px; }
        .field-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 15px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 10px;
            transition: all 0.3s;
        }
        .field-item:hover { background: #e9ecef; }
        .field-item.inactive { opacity: 0.5; }
        .field-item.inactive:hover { opacity: 0.7; }
        .toggle-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            font-size: 18px;
        }
        .toggle-btn.active {
            background-color: #198754;
            color: white;
        }
        .toggle-btn.inactive {
            background-color: #dc3545;
            color: white;
        }
        .toggle-btn:hover { transform: scale(1.1); }
        .required-badge {
            font-size: 12px;
            padding: 2px 8px;
            border-radius: 10px;
            margin-left: 8px;
        }
        .required-badge.required {
            background-color: #ffc107;
            color: #000;
        }
        .required-badge.optional {
            background-color: #6c757d;
            color: white;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        @include('dashboard_admin.sidebar_admin')
        <div id="content">
            <div class="container-fluid">
                <h4 class="fw-bold text-success mb-4">
                    <i class="bi bi-gear me-2"></i>Pengaturan Form PPDB
                </h4>
                <div class="card p-3 mb-4 border-0 shadow-sm" style="background-color: #eef7f4;">
            <h6 class="fw-bold text-success mb-3">
                <i class="bi bi-info-circle-fill me-2"></i>Panduan Penggunaan Tombol
            </h6>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="d-flex align-items-start">
                        <span class="badge bg-success rounded-circle d-inline-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px; font-size: 14px;">-</span>
                        <div>
                            <strong class="d-block text-dark">Tombol [ - ] (Aktif)</strong>
                            <span class="text-muted small">Menandakan field sedang aktif/muncul di form. Klik untuk menyembunyikan field.</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-start">
                        <span class="badge bg-danger rounded-circle d-inline-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px; font-size: 14px;">+</span>
                        <div>
                            <strong class="d-block text-dark">Tombol [ + ] (Nonaktif)</strong>
                            <span class="text-muted small">Menandakan field sedang disembunyikan. Klik untuk memunculkan kembali field.</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-start">
                        <span class="badge bg-warning text-dark rounded-circle d-inline-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px; font-size: 12px;"><i class="bi bi-asterisk"></i></span>
                        <div>
                            <strong class="d-block text-dark">Tombol [ * ] (Wajib/Opsional)</strong>
                            <span class="text-muted small">Mengubah sifat field. Jika tombol berwarna hijau, field <strong>Wajib</strong> diisi. Jika merah, statusnya <strong>Opsional</strong>.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('admin.pengaturan-form-ppdb.update') }}" method="POST">
                    @csrf

                    <!-- Data Murid -->
                    <div class="card p-4 mb-4">
                        <h5 class="fw-bold text-success mb-3"><i class="bi bi-person me-2"></i>Data Murid</h5>
                        @foreach($muridFields as $field)
                            <div class="field-item {{ $field->is_active ? '' : 'inactive' }}">
                                <div>
                                    <strong>{{ $field->field_label }}</strong>
                                    <span class="required-badge {{ $field->is_required ? 'required' : 'optional' }}">
                                        {{ $field->is_required ? 'Wajib' : 'Opsional' }}
                                    </span>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="toggle-btn {{ $field->is_active ? 'active' : 'inactive' }}" 
                                            onclick="toggleField(this, '{{ $field->field_name }}', 'is_active')">
                                        {{ $field->is_active ? '-' : '+' }}
                                    </button>
                                    <button type="button" class="toggle-btn {{ $field->is_required ? 'active' : 'inactive' }}" 
                                            onclick="toggleField(this, '{{ $field->field_name }}', 'is_required')"
                                            title="Set sebagai wajib/opsional">
                                        <i class="bi bi-asterisk"></i>
                                    </button>
                                </div>
                                <input type="hidden" name="settings[{{ $field->field_name }}][is_active]" value="{{ $field->is_active ? '1' : '0' }}" class="is-active-input">
                                <input type="hidden" name="settings[{{ $field->field_name }}][is_required]" value="{{ $field->is_required ? '1' : '0' }}" class="is-required-input">
                            </div>
                        @endforeach
                    </div>

                    <!-- Data Orang Tua -->
                    <div class="card p-4 mb-4">
                        <h5 class="fw-bold text-success mb-3"><i class="bi bi-people me-2"></i>Data Orang Tua</h5>
                        @foreach($ortuFields as $field)
                            <div class="field-item {{ $field->is_active ? '' : 'inactive' }}">
                                <div>
                                    <strong>{{ $field->field_label }}</strong>
                                    <span class="required-badge {{ $field->is_required ? 'required' : 'optional' }}">
                                        {{ $field->is_required ? 'Wajib' : 'Opsional' }}
                                    </span>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="toggle-btn {{ $field->is_active ? 'active' : 'inactive' }}" 
                                            onclick="toggleField(this, '{{ $field->field_name }}', 'is_active')">
                                        {{ $field->is_active ? '-' : '+' }}
                                    </button>
                                    <button type="button" class="toggle-btn {{ $field->is_required ? 'active' : 'inactive' }}" 
                                            onclick="toggleField(this, '{{ $field->field_name }}', 'is_required')"
                                            title="Set sebagai wajib/opsional">
                                        <i class="bi bi-asterisk"></i>
                                    </button>
                                </div>
                                <input type="hidden" name="settings[{{ $field->field_name }}][is_active]" value="{{ $field->is_active ? '1' : '0' }}" class="is-active-input">
                                <input type="hidden" name="settings[{{ $field->field_name }}][is_required]" value="{{ $field->is_required ? '1' : '0' }}" class="is-required-input">
                            </div>
                        @endforeach
                    </div>

                    <!-- Data Wali -->
                    <div class="card p-4 mb-4">
                        <h5 class="fw-bold text-success mb-3"><i class="bi bi-person-hearts me-2"></i>Data Wali</h5>
                        @foreach($waliFields as $field)
                            <div class="field-item {{ $field->is_active ? '' : 'inactive' }}">
                                <div>
                                    <strong>{{ $field->field_label }}</strong>
                                    <span class="required-badge {{ $field->is_required ? 'required' : 'optional' }}">
                                        {{ $field->is_required ? 'Wajib' : 'Opsional' }}
                                    </span>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="toggle-btn {{ $field->is_active ? 'active' : 'inactive' }}" 
                                            onclick="toggleField(this, '{{ $field->field_name }}', 'is_active')">
                                        {{ $field->is_active ? '-' : '+' }}
                                    </button>
                                    <button type="button" class="toggle-btn {{ $field->is_required ? 'active' : 'inactive' }}" 
                                            onclick="toggleField(this, '{{ $field->field_name }}', 'is_required')"
                                            title="Set sebagai wajib/opsional">
                                        <i class="bi bi-asterisk"></i>
                                    </button>
                                </div>
                                <input type="hidden" name="settings[{{ $field->field_name }}][is_active]" value="{{ $field->is_active ? '1' : '0' }}" class="is-active-input">
                                <input type="hidden" name="settings[{{ $field->field_name }}][is_required]" value="{{ $field->is_required ? '1' : '0' }}" class="is-required-input">
                            </div>
                        @endforeach
                    </div>

                    <!-- Dokumen -->
                    <div class="card p-4 mb-4">
                        <h5 class="fw-bold text-success mb-3"><i class="bi bi-file-earmark me-2"></i>Dokumen</h5>
                        @foreach($dokumenFields as $field)
                            <div class="field-item {{ $field->is_active ? '' : 'inactive' }}">
                                <div>
                                    <strong>{{ $field->field_label }}</strong>
                                    <span class="required-badge {{ $field->is_required ? 'required' : 'optional' }}">
                                        {{ $field->is_required ? 'Wajib' : 'Opsional' }}
                                    </span>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="toggle-btn {{ $field->is_active ? 'active' : 'inactive' }}" 
                                            onclick="toggleField(this, '{{ $field->field_name }}', 'is_active')">
                                        {{ $field->is_active ? '-' : '+' }}
                                    </button>
                                    <button type="button" class="toggle-btn {{ $field->is_required ? 'active' : 'inactive' }}" 
                                            onclick="toggleField(this, '{{ $field->field_name }}', 'is_required')"
                                            title="Set sebagai wajib/opsional">
                                        <i class="bi bi-asterisk"></i>
                                    </button>
                                </div>
                                <input type="hidden" name="settings[{{ $field->field_name }}][is_active]" value="{{ $field->is_active ? '1' : '0' }}" class="is-active-input">
                                <input type="hidden" name="settings[{{ $field->field_name }}][is_required]" value="{{ $field->is_required ? '1' : '0' }}" class="is-required-input">
                            </div>
                        @endforeach
                    </div>

                    <!-- Biaya -->
                    <div class="card p-4 mb-4">
                        <h5 class="fw-bold text-success mb-3"><i class="bi bi-cash-stack me-2"></i>Biaya Pendaftaran</h5>
                        @if($biayas->count() > 0)
                            @foreach($biayas as $biaya)
                                <div class="field-item">
                                    <div>
                                        <strong>{{ $biaya->name }}</strong>
                                        <span class="text-success fw-bold ms-2">Rp {{ number_format($biaya->amount, 0, ',', '.') }}</span>
                                    </div>
                                    <button type="button" class="toggle-btn active" 
                                            onclick="toggleBiaya(this, '{{ $biaya->id }}')">
                                        -
                                    </button>
                                    <input type="hidden" name="biaya_settings[{{ $biaya->id }}][is_active]" value="1" class="biaya-active-input">
                                </div>
                            @endforeach
                        @else
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>Belum ada biaya yang diatur.
                            </div>
                        @endif
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success px-5 py-2">
                            <i class="bi bi-check-circle me-2"></i>SIMPAN PENGATURAN
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleField(btn, fieldName, type) {
            const fieldItem = btn.closest('.field-item');
            const isActiveInput = fieldItem.querySelector('.is-active-input');
            const isRequiredInput = fieldItem.querySelector('.is-required-input');
            const badge = fieldItem.querySelector('.required-badge');
            
            if (type === 'is_active') {
                const isActive = isActiveInput.value === '1';
                isActiveInput.value = isActive ? '0' : '1';
                
                if (isActive) {
                    btn.classList.remove('active');
                    btn.classList.add('inactive');
                    btn.textContent = '+';
                    fieldItem.classList.add('inactive');
                } else {
                    btn.classList.remove('inactive');
                    btn.classList.add('active');
                    btn.textContent = '-';
                    fieldItem.classList.remove('inactive');
                }
            } else if (type === 'is_required') {
                const isRequired = isRequiredInput.value === '1';
                isRequiredInput.value = isRequired ? '0' : '1';
                
                if (isRequired) {
                    btn.classList.remove('active');
                    btn.classList.add('inactive');
                    badge.classList.remove('required');
                    badge.classList.add('optional');
                    badge.textContent = 'Opsional';
                } else {
                    btn.classList.remove('inactive');
                    btn.classList.add('active');
                    badge.classList.remove('optional');
                    badge.classList.add('required');
                    badge.textContent = 'Wajib';
                }
            }
        }

        function toggleBiaya(btn, biayaId) {
            const fieldItem = btn.closest('.field-item');
            const activeInput = fieldItem.querySelector('.biaya-active-input');
            
            const isActive = activeInput.value === '1';
            activeInput.value = isActive ? '0' : '1';
            
            if (isActive) {
                btn.classList.remove('active');
                btn.classList.add('inactive');
                btn.textContent = '+';
                fieldItem.classList.add('inactive');
            } else {
                btn.classList.remove('inactive');
                btn.classList.add('active');
                btn.textContent = '-';
                fieldItem.classList.remove('inactive');
            }
        }
    </script>
</body>
</html>