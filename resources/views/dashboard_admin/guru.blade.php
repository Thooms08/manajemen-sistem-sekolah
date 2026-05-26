<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Guru</title>
    @if(isset($sekolah->logo))
    <link rel="icon" type="image/png" href="{{ asset($sekolah->logo) }}">
    @else
    <link rel="icon" type="image/png" href="{{ asset('assets/img/default-favicon.png') }}">
    @endif
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root { --primary-green: #198754; --dark-green: #146c43; }
        body { font-family: 'Inter', sans-serif; background-color: #f4f7f6; overflow-x: hidden; }
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        #content { width: 100%; padding: 20px 30px; transition: all 0.3s; min-height: 100vh; }
        #sidebarCollapse { width: 45px; height: 45px; background: var(--primary-green); border: none; color: white; border-radius: 10px; box-shadow: 0 4px 10px rgba(25,135,84,0.2); display: flex; align-items: center; justify-content: center; }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .table thead { background-color: var(--primary-green); color: white; }
        #overlay { display: none; position: fixed; width: 100vw; height: 100vh; background: rgba(0, 0, 0, 0.5); z-index: 1040; top: 0; left: 0; }
        #overlay.active { display: block; }
        .search-box-wrapper { max-width: 400px; }
        @media (max-width: 768px) { #content { padding: 15px; } }
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
                        <button type="button" id="sidebarCollapse" class="btn"><i class="bi bi-list fs-4"></i></button>
                        <h4 class="ms-3 mb-0 fw-bold text-success">Data Guru & Staff</h4>
                    </div>
                    <button class="btn btn-success px-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahGuru">
                        <i class="bi bi-person-plus me-2"></i>Tambah Guru
                    </button>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card p-3 mb-4 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <p class="text-muted small mb-0">Kelola data tenaga pendidik secara efisien.</p>
                        <div class="input-group search-box-wrapper">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="search-guru" class="form-control border-start-0" placeholder="Cari Nama, Email, atau WhatsApp...">
                        </div>
                    </div>
                </div>

                <div class="card p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th width="50">No</th>
                                    <th>Nama Guru</th>
                                    <th>Email</th>
                                    <th>WhatsApp</th>
                                    <th>Alamat</th>
                                    <th width="120" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="table-body">
                                @forelse($gurus as $index => $g)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="fw-bold">{{ $g->nama_guru }}</td>
                                    <td>{{ $g->email }}</td>
                                    <td>{{ $g->no_whatsapp }}</td>
                                    <td>{{ Str::limit($g->alamat, 40) }}</td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary border-0" 
                                                onclick="openEditModal('{{ $g->id }}', '{{ $g->nama_guru }}', '{{ $g->email }}', '{{ $g->no_whatsapp }}', '{{ $g->alamat }}')">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <form action="{{ route('guru.destroy', $g->id) }}" method="POST" onsubmit="return confirm('Hapus data guru ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger border-0">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada data guru.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTambahGuru" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('guru.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title fw-bold">Tambah Guru Baru</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold small">Nama Guru</label>
                            <input type="text" name="nama_guru" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Nomor WhatsApp</label>
                            <input type="text" name="no_whatsapp" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-success w-100 py-2 shadow">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditGuru" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form id="formEditGuru" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold">Edit Data Guru</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold small">Nama Guru</label>
                            <input type="text" name="nama_guru" id="edit_nama" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Email</label>
                            <input type="email" name="email" id="edit_email" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Nomor WhatsApp</label>
                            <input type="text" name="no_whatsapp" id="edit_whatsapp" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Alamat</label>
                            <textarea name="alamat" id="edit_alamat" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-primary w-100 py-2 shadow">Perbarui Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar & Hamburger Logic
        $(document).ready(function() {
            const sidebar = $('#sidebar');
            const overlay = $('#overlay');

            function toggleSidebar() {
                if ($(window).width() <= 768) {
                    sidebar.toggleClass('show-mobile');
                    overlay.toggleClass('active');
                } else {
                    sidebar.toggleClass('inactive');
                }
            }

            $('#sidebarCollapse, #close-sidebar, #overlay').on('click', toggleSidebar);

            // AJAX Search Guru
            $('#search-guru').on('keyup', function() {
                let value = $(this).val();
                
                $.ajax({
                    type: 'GET',
                    url: "{{ route('guru.search') }}",
                    data: { 'search': value },
                    success: function(data) {
                        $('#table-body').html(data);
                    }
                });
            });
        });

        // Edit Modal Logic
        const editModal = new bootstrap.Modal(document.getElementById('modalEditGuru'));
        function openEditModal(id, nama, email, whatsapp, alamat) {
            document.getElementById('formEditGuru').action = `/guru/${id}`;
            document.getElementById('edit_nama').value = nama;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_whatsapp').value = whatsapp;
            document.getElementById('edit_alamat').value = alamat;
            editModal.show();
        }
    </script>
</body>
</html>