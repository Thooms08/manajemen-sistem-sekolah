<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Wali Murid </title>
    @if(isset($sekolah->logo))
    <link rel="icon" type="image/png" href="{{ asset($sekolah->logo) }}">
    @else
    <link rel="icon" type="image/png" href="{{ asset('assets/img/default-favicon.png') }}">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f7f6; overflow-x: hidden; }
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        #content { width: 100%; padding: 25px; transition: all 0.3s; min-height: 100vh; }
        #overlay { display: none; position: fixed; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 1040; top: 0; left: 0; }
        #overlay.active { display: block; }
        #sidebarCollapse { width: 45px; height: 45px; background: #198754; border: none; color: white; border-radius: 10px; box-shadow: 0 4px 10px rgba(25,135,84,0.2); }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .search-box { border-radius: 10px; border: 1px solid #e0e0e0; padding: 10px 15px; transition: 0.3s; }
        .search-box:focus { border-color: #198754; box-shadow: 0 0 0 0.25rem rgba(25,135,84,0.1); outline: none; }
        .table thead { background-color: #f8f9fa; border-bottom: 2px solid #198754; }
        .table th { font-weight: 600; color: #444; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; }
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
                        <h4 class="ms-3 mb-0 fw-bold text-success">Data Wali Murid</h4>
                    </div>
                </div>

                <div class="card p-3 mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <p class="text-muted small mb-0">Cari berdasarkan Nama Murid, Ayah, Ibu, atau No. HP</p>
                        </div>
                        <div class="col-md-6 mt-2 mt-md-0">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                                <input type="text" id="search-input" class="form-control search-box border-start-0" placeholder="Ketik kata kunci...">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card p-4">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Nama Murid</th>
                                    <th>Nama Ayah</th>
                                    <th>Nama Ibu</th>
                                    <th>No. HP</th>
                                </tr>
                            </thead>
                            <tbody id="table-body">
                                @forelse($data as $row)
                                <tr>
                                    <td class="fw-bold text-dark">{{ $row->nama_lengkap }}</td>
                                    <td>{{ $row->wali->nama_ayah ?? '-' }}</td>
                                    <td>{{ $row->wali->nama_ibu ?? '-' }}</td>
                                    <td><span class="badge bg-success bg-opacity-10 text-success px-3">{{ $row->no_hp }}</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Belum ada data wali murid</td>
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
        $(document).ready(function() {
            // Hamburger Menu Logic
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

            // AJAX Search Logic
            $('#search-input').on('keyup', function() {
                let value = $(this).val();
                
                $.ajax({
                    type: 'GET',
                    url: "{{ route('wali-murid.search') }}",
                    data: { 'search': value },
                    success: function(data) {
                        $('#table-body').html(data);
                    }
                });
            });
        });
    </script>
</body>
</html>