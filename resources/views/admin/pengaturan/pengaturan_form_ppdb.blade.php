<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Form PPDB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary-green: #198754; }
        body { background-color: #f4f7f6; font-family: 'Inter', sans-serif; overflow-x: hidden; }
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        #content { width: 100%; padding: 20px 30px; transition: all 0.3s; min-height: 100vh; min-width: 0; }
        #sidebarCollapse {
            width: 42px; height: 42px; background: var(--primary-green);
            border: none; color: white; border-radius: 10px;
            box-shadow: 0 4px 10px rgba(25,135,84,0.2);
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        #overlay { display: none; position: fixed; width: 100vw; height: 100vh;
            background: rgba(0,0,0,0.5); z-index: 1040; top: 0; left: 0; }
        #overlay.active { display: block; }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }

        /* ── Field Item ── */
        .field-item {
            display: flex; justify-content: space-between; align-items: center;
            padding: 12px 15px; background: #f8f9fa;
            border-radius: 8px; margin-bottom: 10px; transition: all 0.3s;
            gap: 10px; flex-wrap: nowrap;
        }
        .field-item:hover { background: #e9ecef; }
        .field-item.inactive { opacity: 0.55; }
        .field-item.inactive:hover { opacity: 0.75; }
        .field-item .field-label { flex: 1; min-width: 0; }
        .field-item .field-label strong { display: block; word-break: break-word; line-height: 1.3; }

        /* ── Toggle Button ── */
        .toggle-btn {
            width: 38px; height: 38px; border-radius: 50%; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all 0.25s; border: none; font-size: 16px;
        }
        .toggle-btn.active  { background-color: #198754; color: white; }
        .toggle-btn.inactive { background-color: #dc3545; color: white; }
        .toggle-btn:hover { transform: scale(1.1); }

        /* ── Badge ── */
        .required-badge { font-size: 11px; padding: 2px 8px; border-radius: 10px; margin-left: 6px; white-space: nowrap; }
        .required-badge.required  { background-color: #ffc107; color: #000; }
        .required-badge.optional  { background-color: #6c757d; color: white; }

        /* ── Panduan card ── */
        .panduan-card { background-color: #eef7f4; border-radius: 12px; }
        .panduan-badge { width: 26px; height: 26px; font-size: 12px; flex-shrink: 0; }

        /* ── Responsive ── */
        @media (max-width: 991px) { #content { padding: 16px 18px; } }
        @media (max-width: 767px) {
            #content { padding: 12px 12px; }
            .page-header { flex-direction: column; align-items: flex-start !important; gap: 10px; }
            /* Field item: tombol tetap di kanan, label bisa wrap */
            .field-item { flex-wrap: wrap; }
            .field-item .field-label { flex: 1 1 100%; }
            .field-item .field-btns { margin-left: auto; }
            /* Tombol simpan full width */
            .btn-simpan { width: 100%; }
            /* Panduan */
            .panduan-col { flex: 0 0 100%; max-width: 100%; }
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
                <div class="d-flex align-items-center mb-4 mt-1 flex-wrap gap-2 page-header">
                    <button type="button" id="sidebarCollapse" class="btn">
                        <i class="bi bi-list fs-4"></i>
                    </button>
                    <div>
                        <h4 class="mb-0 fw-bold text-success">
                            <i class="bi bi-gear me-2"></i>Pengaturan Form PPDB
                        </h4>
                        <p class="text-muted small mb-0 d-none d-sm-block">Atur field yang tampil pada formulir pendaftaran murid baru</p>
                    </div>
                </div>
                <div class="card p-3 mb-4 border-0 shadow-sm panduan-card">
                    <h6 class="fw-bold text-success mb-3">
                        <i class="bi bi-info-circle-fill me-2"></i>Panduan Penggunaan Tombol
                    </h6>
                    <div class="row g-3">
                        <div class="col-12 col-md-4 panduan-col">
                            <div class="d-flex align-items-start gap-2">
                                <span class="badge bg-success rounded-circle d-inline-flex align-items-center justify-content-center panduan-badge" style="width:26px;height:26px;font-size:13px;">-</span>
                                <div>
                                    <strong class="d-block text-dark small">Tombol [ - ] (Aktif)</strong>
                                    <span class="text-muted" style="font-size:0.8rem;">Field sedang aktif di form. Klik untuk menyembunyikan.</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 panduan-col">
                            <div class="d-flex align-items-start gap-2">
                                <span class="badge bg-danger rounded-circle d-inline-flex align-items-center justify-content-center panduan-badge" style="width:26px;height:26px;font-size:13px;">+</span>
                                <div>
                                    <strong class="d-block text-dark small">Tombol [ + ] (Nonaktif)</strong>
                                    <span class="text-muted" style="font-size:0.8rem;">Field disembunyikan. Klik untuk memunculkan kembali.</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 panduan-col">
                            <div class="d-flex align-items-start gap-2">
                                <span class="badge bg-warning text-dark rounded-circle d-inline-flex align-items-center justify-content-center panduan-badge" style="width:26px;height:26px;font-size:11px;"><i class="bi bi-asterisk"></i></span>
                                <div>
                                    <strong class="d-block text-dark small">Tombol [ * ] (Wajib/Opsional)</strong>
                                    <span class="text-muted" style="font-size:0.8rem;">Hijau = <strong>Wajib</strong>, Merah = <strong>Opsional</strong>.</span>
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
                    <div class="card p-3 p-md-4 mb-4">
                        <h5 class="fw-bold text-success mb-3"><i class="bi bi-person me-2"></i>Data Murid</h5>
                        @foreach($muridFields as $field)
                            <div class="field-item {{ $field->is_active ? '' : 'inactive' }}">
                                <div class="field-label">
                                    <strong>{{ $field->field_label }}</strong>
                                    <span class="required-badge {{ $field->is_required ? 'required' : 'optional' }}">
                                        {{ $field->is_required ? 'Wajib' : 'Opsional' }}
                                    </span>
                                </div>
                                <div class="d-flex gap-2 field-btns">
                                    <button type="button" class="toggle-btn {{ $field->is_active ? 'active' : 'inactive' }}"
                                            onclick="toggleField(this, '{{ $field->field_name }}', 'is_active')"
                                            title="{{ $field->is_active ? 'Nonaktifkan field' : 'Aktifkan field' }}">
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
                    <div class="card p-3 p-md-4 mb-4">
                        <h5 class="fw-bold text-success mb-3"><i class="bi bi-people me-2"></i>Data Orang Tua</h5>
                        @foreach($ortuFields as $field)
                            <div class="field-item {{ $field->is_active ? '' : 'inactive' }}">
                                <div class="field-label">
                                    <strong>{{ $field->field_label }}</strong>
                                    <span class="required-badge {{ $field->is_required ? 'required' : 'optional' }}">
                                        {{ $field->is_required ? 'Wajib' : 'Opsional' }}
                                    </span>
                                </div>
                                <div class="d-flex gap-2 field-btns">
                                    <button type="button" class="toggle-btn {{ $field->is_active ? 'active' : 'inactive' }}"
                                            onclick="toggleField(this, '{{ $field->field_name }}', 'is_active')"
                                            title="{{ $field->is_active ? 'Nonaktifkan field' : 'Aktifkan field' }}">
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
                    <div class="card p-3 p-md-4 mb-4">
                        <h5 class="fw-bold text-success mb-3"><i class="bi bi-person-hearts me-2"></i>Data Wali</h5>
                        @foreach($waliFields as $field)
                            <div class="field-item {{ $field->is_active ? '' : 'inactive' }}">
                                <div class="field-label">
                                    <strong>{{ $field->field_label }}</strong>
                                    <span class="required-badge {{ $field->is_required ? 'required' : 'optional' }}">
                                        {{ $field->is_required ? 'Wajib' : 'Opsional' }}
                                    </span>
                                </div>
                                <div class="d-flex gap-2 field-btns">
                                    <button type="button" class="toggle-btn {{ $field->is_active ? 'active' : 'inactive' }}"
                                            onclick="toggleField(this, '{{ $field->field_name }}', 'is_active')"
                                            title="{{ $field->is_active ? 'Nonaktifkan field' : 'Aktifkan field' }}">
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
                    <div class="card p-3 p-md-4 mb-4">
                        <h5 class="fw-bold text-success mb-3"><i class="bi bi-file-earmark me-2"></i>Dokumen</h5>
                        @foreach($dokumenFields as $field)
                            <div class="field-item {{ $field->is_active ? '' : 'inactive' }}">
                                <div class="field-label">
                                    <strong>{{ $field->field_label }}</strong>
                                    <span class="required-badge {{ $field->is_required ? 'required' : 'optional' }}">
                                        {{ $field->is_required ? 'Wajib' : 'Opsional' }}
                                    </span>
                                </div>
                                <div class="d-flex gap-2 field-btns">
                                    <button type="button" class="toggle-btn {{ $field->is_active ? 'active' : 'inactive' }}"
                                            onclick="toggleField(this, '{{ $field->field_name }}', 'is_active')"
                                            title="{{ $field->is_active ? 'Nonaktifkan field' : 'Aktifkan field' }}">
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
                    <div class="card p-3 p-md-4 mb-4">
                        <h5 class="fw-bold text-success mb-3"><i class="bi bi-cash-stack me-2"></i>Biaya Pendaftaran</h5>
                        @if($biayas->count() > 0)
                            @foreach($biayas as $biaya)
                                <div class="field-item {{ $biaya->is_active ? '' : 'inactive' }}">
                                    <div class="field-label">
                                        <strong>{{ $biaya->name }}</strong>
                                        <span class="text-success fw-bold ms-2">Rp {{ number_format($biaya->amount, 0, ',', '.') }}</span>
                                        @if(!$biaya->is_active && $biaya->disabled_reason)
                                            <small class="text-danger d-block mt-1">
                                                <i class="bi bi-exclamation-circle me-1"></i>{{ $biaya->disabled_reason }}
                                            </small>
                                        @endif
                                    </div>
                                    <div class="field-btns">
                                        <button type="button" class="toggle-btn {{ $biaya->is_active ? 'active' : 'inactive' }}"
                                                onclick="toggleBiaya(this, '{{ $biaya->id }}')"
                                                title="{{ $biaya->is_active ? 'Nonaktifkan biaya' : 'Aktifkan biaya' }}">
                                            {{ $biaya->is_active ? '-' : '+' }}
                                        </button>
                                    </div>
                                    <input type="hidden" name="biaya_settings[{{ $biaya->id }}][is_active]" value="{{ $biaya->is_active ? '1' : '0' }}" class="biaya-active-input">
                                    <input type="hidden" name="biaya_settings[{{ $biaya->id }}][disabled_reason]" value="{{ $biaya->disabled_reason ?? '' }}" class="biaya-disabled-reason-input">
                                </div>
                            @endforeach
                        @else
                            <div class="alert alert-warning border-0 shadow-sm">
                                <i class="bi bi-exclamation-triangle me-2"></i>Belum ada biaya yang diatur.
                                <a href="{{ route('biaya-murid.index') }}" class="alert-link ms-1">Atur biaya →</a>
                            </div>
                        @endif
                    </div>

                    <div class="d-flex justify-content-end mt-2 mb-4">
                        <button type="submit" class="btn btn-success px-5 py-2 fw-semibold shadow-sm btn-simpan">
                            <i class="bi bi-check-circle me-2"></i>Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- DISABLED REASON MODALS FOR BIAYA -->
    @foreach($biayas as $biaya)
    <div class="modal fade" id="modalDisabledReason{{ $biaya->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-exclamation-triangle me-2"></i>Alasan Dinonaktifkan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        Biaya <strong>{{ $biaya->name }}</strong> akan dinonaktifkan dari form PPDB.
                        Mohon isi alasannya agar pendaftar mendapat informasi yang jelas.
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Alasan Dinonaktifkan <span class="text-danger">*</span></label>
                        <textarea id="disabledReasonInput{{ $biaya->id }}" class="form-control" rows="3"
                                  placeholder="Contoh: Pembayaran dilakukan mulai bulan depan">{{ $biaya->disabled_reason ?? '' }}</textarea>
                        <div class="form-text">Wajib diisi jika pembayaran dinonaktifkan.</div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light border px-4"
                            data-bs-dismiss="modal"
                            onclick="cancelDisableBiaya('{{ $biaya->id }}')">Batal</button>
                    <button type="button" class="btn btn-warning px-4 fw-semibold"
                            onclick="confirmDisableBiaya('{{ $biaya->id }}')">
                        <i class="bi bi-check-lg me-1"></i>Konfirmasi
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ── Sidebar Toggle ────────────────────────────────────────────────
        (function () {
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

            if (collapseBtn) collapseBtn.addEventListener('click', toggleSidebar);
            if (closeBtn)    closeBtn.addEventListener('click', function () {
                sidebar.classList.remove('show-mobile');
                overlay.classList.remove('active');
            });
            if (overlay)     overlay.addEventListener('click', toggleSidebar);
        })();

        // ── Toggle Field (aktif / wajib) ──────────────────────────────────
        function toggleField(btn, fieldName, type) {
            const fieldItem       = btn.closest('.field-item');
            const isActiveInput   = fieldItem.querySelector('.is-active-input');
            const isRequiredInput = fieldItem.querySelector('.is-required-input');
            const badge           = fieldItem.querySelector('.required-badge');

            if (type === 'is_active') {
                const isActive = isActiveInput.value === '1';
                isActiveInput.value = isActive ? '0' : '1';
                if (isActive) {
                    btn.classList.remove('active'); btn.classList.add('inactive');
                    btn.textContent = '+';
                    fieldItem.classList.add('inactive');
                } else {
                    btn.classList.remove('inactive'); btn.classList.add('active');
                    btn.textContent = '-';
                    fieldItem.classList.remove('inactive');
                }
            } else if (type === 'is_required') {
                const isRequired = isRequiredInput.value === '1';
                isRequiredInput.value = isRequired ? '0' : '1';
                if (isRequired) {
                    btn.classList.remove('active'); btn.classList.add('inactive');
                    badge.classList.remove('required'); badge.classList.add('optional');
                    badge.textContent = 'Opsional';
                } else {
                    btn.classList.remove('inactive'); btn.classList.add('active');
                    badge.classList.remove('optional'); badge.classList.add('required');
                    badge.textContent = 'Wajib';
                }
            }
        }

        // ── Toggle Biaya ──────────────────────────────────────────────────
        function toggleBiaya(btn, biayaId) {
            const fieldItem   = btn.closest('.field-item');
            const activeInput = fieldItem.querySelector('.biaya-active-input');
            const reasonInput = fieldItem.querySelector('.biaya-disabled-reason-input');
            const isActive    = activeInput.value === '1';

            if (isActive) {
                showDisabledReasonModal(biayaId);
            } else {
                activeInput.value = '1';
                reasonInput.value = '';
                btn.classList.remove('inactive'); btn.classList.add('active');
                btn.textContent = '-';
                fieldItem.classList.remove('inactive');
                const reasonDisplay = fieldItem.querySelector('.field-label .text-danger');
                if (reasonDisplay) reasonDisplay.remove();
            }
        }

        function showDisabledReasonModal(biayaId) {
            const el = document.getElementById('modalDisabledReason' + biayaId);
            if (el) new bootstrap.Modal(el).show();
        }

        function confirmDisableBiaya(biayaId) {
            const reasonTextarea = document.getElementById('disabledReasonInput' + biayaId);
            if (!reasonTextarea.value.trim()) {
                reasonTextarea.focus();
                reasonTextarea.classList.add('is-invalid');
                return;
            }
            reasonTextarea.classList.remove('is-invalid');

            const fieldItem         = document.querySelector(`input[name="biaya_settings[${biayaId}][is_active]"]`)?.closest('.field-item');
            const activeInput       = fieldItem?.querySelector('.biaya-active-input');
            const reasonHiddenInput = fieldItem?.querySelector('.biaya-disabled-reason-input');
            const btn               = fieldItem?.querySelector('.toggle-btn');

            if (activeInput)        activeInput.value = '0';
            if (reasonHiddenInput)  reasonHiddenInput.value = reasonTextarea.value.trim();

            if (btn) {
                btn.classList.remove('active'); btn.classList.add('inactive');
                btn.textContent = '+';
            }
            if (fieldItem) {
                fieldItem.classList.add('inactive');
                const labelDiv    = fieldItem.querySelector('.field-label');
                let reasonDisplay = labelDiv.querySelector('.text-danger');
                if (!reasonDisplay) {
                    reasonDisplay = document.createElement('small');
                    reasonDisplay.className = 'text-danger d-block mt-1';
                    labelDiv.appendChild(reasonDisplay);
                }
                reasonDisplay.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i>' + reasonTextarea.value.trim();
            }

            const modal = bootstrap.Modal.getInstance(document.getElementById('modalDisabledReason' + biayaId));
            if (modal) modal.hide();
        }

        function cancelDisableBiaya(biayaId) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalDisabledReason' + biayaId));
            if (modal) modal.hide();
        }
    </script>
</body>
</html>