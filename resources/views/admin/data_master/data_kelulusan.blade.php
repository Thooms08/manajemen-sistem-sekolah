<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Data Kelulusan Murid</title>
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
        input:focus, textarea:focus, select:focus { border-color: #198754 !important; outline: none !important; box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25) !important;}
    </style>
</head>
<body>
    <div id="overlay"></div>
    <div class="wrapper">
        @include('admin.sidebar')
        <div id="content">
            <div class="container-fluid">
                <div class="d-flex align-items-center justify-content-between mb-4 mt-2">
                    <div class="d-flex align-items-center">
                        <button type="button" id="sidebarCollapse" class="btn"><i class="bi bi-list fs-5"></i></button>
                        <h4 class="ms-3 mb-0 fw-bold text-success">Data Kelulusan Murid</h4>
                    </div>
                </div>

                <div class="card p-3 mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Kelola status kelulusan siswa beserta pengunggahan berkas ijazah, raport, & surat kelulusan.</span>
                        <div class="input-group" style="width: 350px;">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="search-kelulusan" class="form-control border-start-0 ps-0" placeholder="Cari Nama, NISN, atau NIS Baru...">
                        </div>
                    </div>
                </div>

                <div class="card p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>NISN</th>
                                    <th>NIS Baru</th>
                                    <th>Nama Lengkap</th>
                                    <th>Kelas</th>
                                    <th>Status</th>
                                    <th class="text-center">Ijazah</th>
                                    <th class="text-center">Raport</th>
                                    <th class="text-center">Surat Kelulusan</th> <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="table-body">
                                @if($murids->isEmpty())
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">Data tidak ditemukan</td>
                                    </tr>
                                @else
                                    @foreach($murids as $murid)
                                        <tr>
                                            <td>{{ $murid->nisn }}</td>
                                            <td>{{ $murid->nis_baru ?? '-' }}</td>
                                            <td>{{ $murid->nama_lengkap }}</td>
                                            <td>{{ $murid->kelas->pluck('nama_kelas')->implode(', ') ?: '-' }}</td>
                                            <td>
                                                @if(($murid->kelulusan->status ?? '') == 'lulus')
                                                    <span class="badge bg-success">Lulus</span>
                                                @elseif(($murid->kelulusan->status ?? '') == 'tidak lulus')
                                                    <span class="badge bg-danger">Tidak Lulus</span>
                                                @else
                                                    <span class="badge bg-secondary">Belum Diatur</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if(!empty($murid->kelulusan->ijazah))
                                                    <a href="{{ route('kelulusan.view.ijazah', $murid->kelulusan->uuid) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-file-earmark-pdf"></i></a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if(!empty($murid->kelulusan->raport))
                                                    <a href="{{ route('kelulusan.view.raport', $murid->kelulusan->uuid) }}" target="_blank" class="btn btn-sm btn-outline-danger"><i class="bi bi-file-earmark-text"></i></a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if(!empty($murid->kelulusan->surat_kelulusan) && !empty($murid->kelulusan->uuid))
                                                    <a href="{{ route('kelulusan.view.surat', $murid->kelulusan->uuid) }}" target="_blank" class="btn btn-sm btn-outline-success">
                                                        <i class="bi bi-file-earmark-check-fill"></i>
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3" 
                                                    onclick="openEditKelulusan(this, '{{ $murid->kelulusan->uuid ?? '' }}')"
                                                    data-ijazah="{{ (!empty($murid->kelulusan->ijazah) && !empty($murid->kelulusan->uuid)) ? route('kelulusan.view.ijazah', $murid->kelulusan->uuid) : '' }}"
                                                    data-raport="{{ (!empty($murid->kelulusan->raport) && !empty($murid->kelulusan->uuid)) ? route('kelulusan.view.raport', $murid->kelulusan->uuid) : '' }}"
                                                    data-surat="{{ (!empty($murid->kelulusan->surat_kelulusan) && !empty($murid->kelulusan->uuid)) ? route('kelulusan.view.surat', $murid->kelulusan->uuid) : '' }}">
                                                    <i class="bi bi-pencil-square"></i> Edit
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ Modal Edit Kelulusan ══ --}}
    <div class="modal fade" id="modalEditKelulusan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <form id="formEditKelulusan" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="edit-id">
                
                <input type="hidden" name="hapus_ijazah" id="hapus-ijazah" value="0">
                <input type="hidden" name="hapus_raport" id="hapus-raport" value="0">
                <input type="hidden" name="hapus_surat_kelulusan" id="hapus-surat" value="0">

                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Kelulusan</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3 bg-light p-3 rounded">
                            <div class="row g-2">
                                <div class="col-6 small"><strong>Nama:</strong> <span id="view-nama">-</span></div>
                                <div class="col-6 small"><strong>Kelas:</strong> <span id="view-kelas">-</span></div>
                                <div class="col-6 small"><strong>NISN:</strong> <span id="view-nisn">-</span></div>
                                <div class="col-6 small"><strong>NIS Baru:</strong> <span id="view-nisbaru">-</span></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status Kelulusan</label>
                            <select id="edit-status" name="status" class="form-select" required>
                                <option value="" disabled selected>-- Pilih Status --</option>
                                <option value="lulus">Lulus</option>
                                <option value="tidak lulus">Tidak Lulus</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tahun Lulus</label>
                            <input type="number" id="edit-tahun" name="tahun_lulus" class="form-select text-start" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">File Ijazah <small class="text-muted">(Max 5MB - PDF/JPG)</small></label>
                            <input type="file" id="edit-ijazah" name="ijazah" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            
                            <div id="container-old-ijazah" class="mt-2 small d-none p-2 bg-light rounded d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted">Berkas saat ini: </span>
                                    <a id="link-old-ijazah" href="#" target="_blank" class="text-primary fw-bold text-decoration-none">
                                        <i class="bi bi-file-earmark-pdf-fill"></i> Lihat Berkas
                                    </a>
                                </div>
                                <button type="button" class="btn btn-sm btn-link text-danger text-decoration-none fw-bold p-0" onclick="hapusPratinjauBerkas('ijazah')">
                                    <i class="bi bi-trash3-fill"></i> Hapus Berkas
                                </button>
                            </div>
                            <div id="live-preview-ijazah" class="mt-2 d-none">
                                <img id="img-preview-ijazah" src="#" alt="Preview Ijazah" class="img-thumbnail" style="max-height: 120px;">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">File Raport <small class="text-muted">(Max 10MB - PDF/JPG)</small></label>
                            <input type="file" id="edit-raport" name="raport" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            
                            <div id="container-old-raport" class="mt-2 small d-none p-2 bg-light rounded d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted">Berkas saat ini: </span>
                                    <a id="link-old-raport" href="#" target="_blank" class="text-danger fw-bold text-decoration-none">
                                        <i class="bi bi-file-earmark-text-fill"></i> Lihat Berkas
                                    </a>
                                </div>
                                <button type="button" class="btn btn-sm btn-link text-danger text-decoration-none fw-bold p-0" onclick="hapusPratinjauBerkas('raport')">
                                    <i class="bi bi-trash3-fill"></i> Hapus Berkas
                                </button>
                            </div>
                            <div id="live-preview-raport" class="mt-2 d-none">
                                <img id="img-preview-raport" src="#" alt="Preview Raport" class="img-thumbnail" style="max-height: 120px;">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Surat Kelulusan <small class="text-muted">(Max 5MB - PDF/JPG)</small></label>
                            <input type="file" id="edit-surat-kelulusan" name="surat_kelulusan" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            
                            <div id="container-old-surat" class="mt-2 small d-none p-2 bg-light rounded d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted">Berkas saat ini: </span>
                                    <a id="link-old-surat" href="#" target="_blank" class="text-success fw-bold text-decoration-none">
                                        <i class="bi bi-file-earmark-check-fill"></i> Lihat Surat
                                    </a>
                                </div>
                                <button type="button" class="btn btn-sm btn-link text-danger text-decoration-none fw-bold p-0" onclick="hapusPratinjauBerkas('surat')">
                                    <i class="bi bi-trash3-fill"></i> Hapus Berkas
                                </button>
                            </div>
                            <div id="live-preview-surat" class="mt-2 d-none">
                                <img id="img-preview-surat" src="#" alt="Preview Surat" class="img-thumbnail" style="max-height: 120px;">
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
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

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ── Sidebar Responsive Logic ──────────────────────────────────────────
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

        // ── AJAX Real-time Search ────────────────────────────────────────────
        $(document).ready(function () {
            $('#search-kelulusan').on('keyup', function () {
                $.ajax({
                    type: 'GET',
                    url: "{{ route('kelulusan.search') }}",
                    data: { 'search': $(this).val() },
                    success: function (data) { $('#table-body').html(data); },
                    error:   function (err)  { console.log('Error AJAX Search:', err); }
                });
            });
        });

        // ── Buka Modal Edit & Ambil Data Lama ─────────────────────────────────
       // ── Buka Modal Edit & Ambil Data Lama (Menggunakan UUID) ─────────────────
        function openEditKelulusan(element, uuid) {
            if (!uuid) {
                showToast('Data kelulusan untuk murid ini belum valid atau belum diatur!', 'danger');
                return;
            }

            const oldIjazahUrl = $(element).data('ijazah');
            const oldRaportUrl = $(element).data('raport');
            const oldSuratUrl  = $(element).data('surat');

            $.ajax({
                type: 'GET',
                url: `{{ url('data-kelulusan') }}/${uuid}/edit`,
                success: function (data) {
                    // #edit-id sekarang akan menampung nilai UUID untuk dioper ke action submit update
                    $('#edit-id').val(data.uuid);
                    $('#view-nama').text(data.nama_lengkap);
                    $('#view-kelas').text(data.kelas);
                    $('#view-nisn').text(data.nisn);
                    $('#view-nisbaru').text(data.nis_baru);
                    $('#edit-status').val(data.status);
                    $('#edit-tahun').val(data.tahun_lulus);
                    
                    // Reset input file & live preview unggahan baru
                    $('#edit-ijazah').val('');
                    $('#edit-raport').val('');
                    $('#edit-surat-kelulusan').val('');
                    $('#live-preview-ijazah').addClass('d-none');
                    $('#live-preview-raport').addClass('d-none');
                    $('#live-preview-surat').addClass('d-none');

                    // Reset flag penanda hapus berkas ke awal (0 = tidak dihapus)
                    $('#hapus-ijazah').val('0');
                    $('#hapus-raport').val('0');
                    $('#hapus-surat').val('0');

                    // Set Preview Berkas Lama Ijazah
                    if (oldIjazahUrl) {
                        $('#link-old-ijazah').attr('href', oldIjazahUrl);
                        $('#container-old-ijazah').removeClass('d-none').addClass('d-flex');
                    } else {
                        $('#container-old-ijazah').removeClass('d-flex').addClass('d-none');
                    }

                    // Set Preview Berkas Lama Raport
                    if (oldRaportUrl) {
                        $('#link-old-raport').attr('href', oldRaportUrl);
                        $('#container-old-raport').removeClass('d-none').addClass('d-flex');
                    } else {
                        $('#container-old-raport').removeClass('d-flex').addClass('d-none');
                    }

                    // Set Preview Berkas Lama Surat Kelulusan
                    if (oldSuratUrl) {
                        $('#link-old-surat').attr('href', oldSuratUrl);
                        $('#container-old-surat').removeClass('d-none').addClass('d-flex');
                    } else {
                        $('#container-old-surat').removeClass('d-flex').addClass('d-none');
                    }

                    bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEditKelulusan')).show();
                },
                error: function(err) {
                    showToast('Gagal memuat data kelulusan murid!', 'danger');
                }
            });
        }

        // ── Fungsi Aksi Hapus Berkas Lama di Sisi Client ───────────────────────
        function hapusPratinjauBerkas(type) {
            let label = type === 'ijazah' ? 'Ijazah' : (type === 'raport' ? 'Raport' : 'Surat Kelulusan');
            if (confirm(`Apakah Anda yakin ingin menghapus berkas ${label} saat ini?`)) {
                // Set flag menjadi 1 agar controller tahu berkas harus dihapus dari storage
                $(`#hapus-${type}`).val('1');
                
                // Sembunyikan container berkas lama dari modal
                $(`#container-old-${type}`).removeClass('d-flex').addClass('d-none');
                
                showToast(`Berkas ${label} ditandai untuk dihapus. Klik Simpan Perubahan untuk menerapkan.`, 'warning');
            }
        }

        // ── Helper Live Preview Berkas Baru Berupa Gambar ─────────────────────
        function bacaURLPreview(input, imgID, containerID) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                if (file.type.match('image.*')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $(`#${imgID}`).attr('src', e.target.result);
                        $(`#${containerID}`).removeClass('d-none');
                    }
                    reader.readAsDataURL(file);
                } else {
                    $(`#${containerID}`).addClass('d-none');
                }
            }
        }

        // ── Validasi Ukuran File Real-Time Sisi Klien ──────────────────────────
        $('#edit-ijazah').on('change', function() {
            if(this.files[0] && this.files[0].size > 5 * 1024 * 1024) { 
                showToast('Gagal! File Ijazah maksimal berukuran 5MB.', 'danger');
                this.value = ''; 
                $('#live-preview-ijazah').addClass('d-none');
            } else {
                $('#hapus-ijazah').val('0'); 
                bacaURLPreview(this, 'img-preview-ijazah', 'live-preview-ijazah');
            }
        });

        $('#edit-raport').on('change', function() {
            if(this.files[0] && this.files[0].size > 10 * 1024 * 1024) { 
                showToast('Gagal! File Raport maksimal berukuran 10MB.', 'danger');
                this.value = ''; 
                $('#live-preview-raport').addClass('d-none');
            } else {
                $('#hapus-raport').val('0'); 
                bacaURLPreview(this, 'img-preview-raport', 'live-preview-raport');
            }
        });

        // Validasi Surat Kelulusan Max 5MB
        $('#edit-surat-kelulusan').on('change', function() {
            if(this.files[0] && this.files[0].size > 5 * 1024 * 1024) { 
                showToast('Gagal! File Surat Kelulusan maksimal berukuran 5MB.', 'danger');
                this.value = ''; 
                $('#live-preview-surat').addClass('d-none');
            } else {
                $('#hapus-surat').val('0'); 
                bacaURLPreview(this, 'img-preview-surat', 'live-preview-surat');
            }
        });

        // ── Submit Update via AJAX Form Data ─────────────────────────────────
        $('#formEditKelulusan').on('submit', function(e) {
            e.preventDefault();
            const id = $('#edit-id').val();
            const formData = new FormData(this);

            // Disable tombol simpan & tampilkan spinner agar tidak double-submit
            const $btn = $(this).find('button[type="submit"]');
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...');

            $('#global-loading-overlay').fadeIn();

            $.ajax({
                type: 'POST',
                url: `{{ url('data-kelulusan') }}/${id}/update`,
                data: formData,
                contentType: false,
                processData: false,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    if(response.success) {
                        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEditKelulusan')).hide();
                        showToast(response.message, 'success');
                        // Reload tabel agar data terbaru langsung tampil
                        $('#search-kelulusan').trigger('keyup');
                    }
                },
                error: function(xhr) {
                    let msg = 'Gagal menyimpan data!';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    } else if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join(', ');
                    }
                    showToast(msg, 'danger');
                },
                complete: function() {
                    // Re-enable tombol simpan
                    $btn.prop('disabled', false).html('Simpan Perubahan');

                    $('#global-loading-overlay').fadeOut();
                }
            });
        });

        // ── Toast Helper ─────────────────────────────────────────────────────
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