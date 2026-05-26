<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        .search-box { border-radius: 8px; border: 1px solid #ddd; padding: 8px 15px; width: 300px; transition: 0.3s; }
        .search-box:focus { border-color: #198754; outline: none; box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.1); }
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
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="table-body">
                                @forelse($murids as $m)
                                <tr>
                                    <td class="fw-bold">{{ $m->nama_lengkap }}</td>
                                    <td>{{ $m->nisn }}</td>
                                    <td>{{ $m->no_hp }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('murid.edit', $m->id) }}" class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('murid.destroy', $m->id) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus murid ini?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Belum ada data murid.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar & Overlay Logic
        const sidebar = document.getElementById('sidebar');
        const collapseBtn = document.getElementById('sidebarCollapse');
        const overlay = document.getElementById('overlay');
        const closeBtn = document.getElementById('close-sidebar');

        function toggleSidebar() {
            if (window.innerWidth <= 768) { 
                sidebar.classList.toggle('show-mobile'); 
                overlay.classList.toggle('active'); 
            }
            else { sidebar.classList.toggle('inactive'); }
        }
        collapseBtn.onclick = toggleSidebar;
        if(closeBtn) closeBtn.onclick = toggleSidebar;
        overlay.onclick = toggleSidebar;

        // AJAX Search Logic
        $(document).ready(function() {
            $('#search-murid').on('keyup', function() {
                let value = $(this).val();
                
                $.ajax({
                    type: 'GET',
                    url: "{{ route('murid.search') }}",
                    data: { 'search': value },
                    success: function(data) {
                        $('#table-body').html(data);
                    },
                    error: function(err) {
                        console.log("Error AJAX:", err);
                    }
                });
            });
        });
    </script>
</body>
</html>