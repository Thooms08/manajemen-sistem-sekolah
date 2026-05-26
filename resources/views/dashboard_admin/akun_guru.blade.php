<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Akun Guru</title>
     @if(isset($sekolah->logo))
    <link rel="icon" type="image/png" href="{{ asset($sekolah->logo) }}">
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

        /* Badge Styling */
        .badge-user {
            background-color: rgba(25, 135, 84, 0.1);
            color: var(--primary-green);
            border: 1px solid rgba(25, 135, 84, 0.2);
        }

        #overlay { 
            display: none; 
            position: fixed; 
            width: 100vw; 
            height: 100vh; 
            background: rgba(0,0,0,0.5); 
            z-index: 1040; 
            top: 0; 
            left: 0; 
        }
        #overlay.active { display: block; }

        @media (max-width: 768px) {
            #sidebar { margin-left: -250px; }
            #sidebar.active { margin-left: 0; }
        }
    </style>
</head>
<body>

<div id="overlay"></div>

<div class="wrapper">
    @include('dashboard_admin.sidebar_admin')

    <div id="content">
        <div class="container-fluid">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <button type="button" id="sidebarCollapse" class="btn btn-success me-3">
                        <i class="bi bi-list"></i>
                    </button>
                    <h4 class="fw-bold text-success mb-0">Kelola Akun Login Guru</h4>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card p-4">
                <div class="row mb-4">
                    <div class="col-md-5 ms-auto">
                        <div class="input-group">
                            <span class="input-group-text border-end-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text" id="searchGuru" class="form-control border-start-0 ps-0" placeholder="Cari nama guru atau username...">
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th width="60" class="ps-3">No</th>
                                <th>Nama Guru</th>
                                <th>Username Akun</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="guruTableBody">
                            @foreach($gurus as $index => $g)
                            <tr>
                                <td class="ps-3">{{ $index + 1 }}</td>
                                <td class="fw-bold text-dark">{{ $g->nama_guru }}</td>
                                <td>
                                    @if($g->id_user)
                                        <span class="badge badge-user px-3 py-2 rounded-pill">
                                            <i class="bi bi-person-badge-fill me-1"></i> {{ $g->username }}
                                        </span>
                                    @else
                                        <span class="text-muted small"><em>Belum memiliki akun</em></span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if(!$g->id_user)
                                        <button class="btn btn-success btn-sm px-3 rounded-pill" data-bs-toggle="modal" data-bs-target="#modalTambah{{ $g->id_guru }}">
                                            <i class="bi bi-person-plus-fill me-1"></i> Buat Akun
                                        </button>
                                    @else
                                        <button class="btn btn-outline-success btn-sm me-1" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $g->id_user }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <form action="{{ route('akun-guru.destroy', $g->id_user) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-outline-danger btn-sm" onclick="return confirm('Hapus akun login guru ini?')">
                                                <i class="bi bi-trash3-fill"></i>
                                            </button>
                                        </form>
                                    @endif
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

@foreach($gurus as $g)
    {{-- MODAL BUAT AKUN --}}
    @if(!$g->id_user)
    <div class="modal fade" id="modalTambah{{ $g->id_guru }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('akun-guru.store') }}" method="POST">
                @csrf
                <input type="hidden" name="id_guru" value="{{ $g->id_guru }}">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title fw-bold">Buat Akun: {{ $g->nama_guru }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Username</label>
                            <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Password</label>
                            <div class="input-group">
                                <input type="password" name="password" class="form-control password-field" placeholder="Min. 6 karakter" required>
                                <span class="input-group-text toggle-password"><i class="bi bi-eye"></i></span>
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-bold">Konfirmasi Password</label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" class="form-control password-field" placeholder="Ulangi password" required>
                                <span class="input-group-text toggle-password"><i class="bi bi-eye"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="submit" class="btn btn-success w-100 fw-bold py-2">Simpan Akun</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- MODAL EDIT AKUN --}}
    @if($g->id_user)
    <div class="modal fade" id="modalEdit{{ $g->id_user }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('akun-guru.update', $g->id_user) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title fw-bold">Edit Akun: {{ $g->nama_guru }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Username</label>
                            <input type="text" name="username" class="form-control" value="{{ $g->username }}" required>
                        </div>
                        <div class="mb-0 text-muted small mb-2"><em>Kosongkan password jika tidak ingin mengganti</em></div>
                        <div class="input-group mb-2">
                            <input type="password" name="password" class="form-control password-field" placeholder="Password baru">
                            <span class="input-group-text toggle-password"><i class="bi bi-eye"></i></span>
                        </div>
                        <div class="input-group">
                            <input type="password" name="password_confirmation" class="form-control password-field" placeholder="Konfirmasi password baru">
                            <span class="input-group-text toggle-password"><i class="bi bi-eye"></i></span>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="submit" class="btn btn-success w-100 fw-bold py-2">Update Akun</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif
@endforeach

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // 1. Sidebar Toggle Logic
    const sidebar = document.getElementById('sidebar');
    const collapseBtn = document.getElementById('sidebarCollapse');
    const overlay = document.getElementById('overlay');

    function toggleSidebar() {
        if (window.innerWidth <= 768) {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        } else {
            sidebar.classList.toggle('inactive');
        }
    }

    collapseBtn.addEventListener('click', toggleSidebar);
    overlay.addEventListener('click', toggleSidebar);

    // 2. AJAX Search Logic
    document.getElementById('searchGuru').addEventListener('keyup', function() {
        let query = this.value;
        let tableBody = document.getElementById('guruTableBody');

        fetch(`{{ route('akun-guru.search') }}?query=${query}`, {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.text())
        .then(data => {
            tableBody.innerHTML = data;
        })
        .catch(error => console.error('Error Search AJAX:', error));
    });

    // 3. Show/Hide Password Logic (Event Delegation)
    document.addEventListener('click', function(e) {
        if (e.target.closest('.toggle-password')) {
            const btn = e.target.closest('.toggle-password');
            const input = btn.parentElement.querySelector('.password-field');
            const icon = btn.querySelector('i');
            
            if (input.type === "password") {
                input.type = "text";
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                input.type = "password";
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        }
    });
</script>

</body>
</html>