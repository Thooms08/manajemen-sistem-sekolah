<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $artikel->judul }} | {{ $sekolah->nama_sekolah ?? 'Website Sekolah' }}</title>
    
    <link rel="icon" type="image/png" href="{{ asset($sekolah->logo) }}">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --primary-green: #198754;
            --dark-green: #0b4629;
            --soft-green: #f0fdf4;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #2d3436;
            background-color: #ffffff;
        }

        /* NAVBAR GLASSMORPHISM */
        .navbar {
            padding: 12px 0;
            background: rgba(255, 255, 255, 0.9) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        .nav-link {
            font-weight: 600;
            font-size: 0.95rem;
            color: #4b5563 !important;
            margin: 0 8px;
        }
        .btn-login {
            border: 2px solid var(--primary-green);
            color: var(--primary-green) !important;
            border-radius: 12px;
            padding: 7px 18px;
            font-weight: 700;
            transition: 0.3s;
        }
        .btn-login:hover {
            background-color: var(--primary-green);
            color: white !important;
        }

        /* CONTENT STYLING */
        .article-title {
            font-weight: 800;
            font-size: 2.8rem;
            color: var(--dark-green);
            line-height: 1.2;
        }
        .img-detail-custom {
            width: 100%;
            max-height: 500px;
            object-fit: cover;
            border-radius: 24px;
        }
        .article-content {
            font-size: 1.15rem;
            line-height: 1.8;
            color: #374151;
        }

        /* FOOTER */
        footer {
            background-color: #0f172a;
            color: #94a3b8;
            padding: 80px 0 30px;
            border-radius: 50px 50px 0 0;
        }
        footer h5 { color: white; font-weight: 700; }

        @media (max-width: 768px) {
            .article-title { font-size: 2rem; }
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="/">
                <img src="{{ asset($sekolah->logo) }}" alt="Logo" width="40" class="me-2">
                <span class="fw-bold text-success" style="font-size: 16px;">{{ $sekolah->nama_sekolah ?? 'SEKOLAH KAMI' }}</span>
            </a>
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="bi bi-list text-success fs-1"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="/">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="/#profil">Tentang</a></li>
                    @if($infoSekolah)
                    <li class="nav-item"><a class="nav-link" href="/#informasi">Informasi</a></li>
                    @endif
                    <li class="nav-item"><a class="nav-link" href="/#prestasi">Prestasi</a></li>
                    <li class="nav-item"><a class="nav-link" href="/#program">Program</a></li>
                    <li class="nav-item"><a class="nav-link" href="/#artikel">Berita</a></li>
                    
                    <li class="nav-item ms-lg-3">
                        @auth
                            @php
                                $dashboardUrl = match(auth()->user()->role) {
                                    'admin' => url('dashboard_admin'),
                                    'guru'  => url('dashboard_guru'),
                                    default => url('/'),
                                };
                            @endphp
                            <a class="btn btn-success shadow-sm fw-bold" style="border-radius: 12px; padding: 8px 20px;" href="{{ $dashboardUrl }}">
                                <i class="bi bi-speedometer2 me-1"></i> Dashboard
                            </a>
                        @else
                            <a class="btn btn-login shadow-sm" href="{{ route('login') }}">
                                <i class="bi bi-person-circle me-1"></i> Log In
                            </a>
                        @endauth
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-5 my-4">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <a href="/#artikel" class="btn btn-light rounded-pill px-4 fw-bold text-success mb-4">
                    <i class="bi bi-arrow-left me-2"></i> Kembali ke Beranda
                </a>

                <div class="d-flex align-items-center gap-3 mb-3">
                    <span class="badge bg-success px-3 py-2 rounded-pill shadow-sm">Artikel Sekolah</span>
                    <small class="text-muted fw-semibold"><i class="bi bi-calendar3 me-1"></i> {{ $artikel->created_at->format('d M Y') }}</small>
                </div>

                <h1 class="article-title mb-4">{{ $artikel->judul }}</h1>

                <p class="fs-5 text-muted fw-normal mb-4 border-start border-4 border-success ps-3 italic">
                    {{ $artikel->teaser }}
                </p>

                @if($artikel->foto_artikel)
                    <div class="mb-5 shadow-sm rounded-4 overflow-hidden">
                        <img src="{{ asset($artikel->foto_artikel) }}" class="img-detail-custom" alt="{{ $artikel->judul }}">
                    </div>
                @endif

                <div class="article-content lh-lg text-justify">
                    {!! nl2br(e($artikel->deskripsi)) !!}
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center mb-4">
                        <img src="{{ asset($sekolah->logo) }}" width="50" class="me-3 bg-white p-1 rounded-circle">
                        <h5 class="mb-0">{{ $sekolah->nama_sekolah }}</h5>
                    </div>
                    <p class="small lh-lg">{{ $sekolah->deskripsi }}</p>
                    <div class="d-flex gap-3 mt-4">
                        <a href="#" class="text-white fs-4"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-white fs-4"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-white fs-4"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>
                <div class="col-lg-4">
                    <h5>Informasi Kontak</h5>
                    <ul class="list-unstyled mt-4">
                        <li class="mb-3 d-flex align-items-start"><i class="bi bi-geo-alt-fill text-success me-3 fs-5"></i> {{ $sekolah->alamat }}</li>
                        <li class="mb-3 d-flex align-items-start"><i class="bi bi-telephone-fill text-success me-3 fs-5"></i> {{ $sekolah->no_hp }}</li>
                        <li class="mb-3 d-flex align-items-start"><i class="bi bi-envelope-fill text-success me-3 fs-5"></i> {{ $sekolah->email }}</li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h5>Lokasi Kami</h5>
                    <div class="rounded-4 overflow-hidden shadow-lg mt-4" style="height: 220px;">
                        {!! $sekolah->tautan_google_maps !!}
                    </div>
                </div>
            </div>
            <div class="border-top border-secondary mt-5 pt-4 text-center">
                <p class="mb-0 small opacity-50">&copy; {{ date('Y') }} {{ $sekolah->nama_sekolah }}. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>