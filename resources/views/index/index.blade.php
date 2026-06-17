<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $sekolah->nama_sekolah ?? 'Website Sekolah' }}</title>
    
    <link rel="icon" type="image/png" href="{{ \App\Helpers\ImageHelper::url($sekolah->logo) }}">

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
            scroll-behavior: smooth;
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

        /* HERO SLIDER */
        .carousel-item {
            height: 50vh; 
            min-height: 350px;
            background-color: #1a1a1a;
        }
        .carousel-item img {
            opacity: 0.5;
            object-fit: cover;
            height: 100%;
        }
        .carousel-caption {
            top: 50%;
            transform: translateY(-50%);
            bottom: auto;
            text-align: center;
            width: 100%;
            left: 0;
            right: 0;
        }
        .carousel-caption h1 {
            font-weight: 800;
            font-size: 3rem;
            text-shadow: 0 4px 12px rgba(0,0,0,0.5);
        }

        /* SEKSI PPDB */
        .section-cta {
            background-color: #ffffff;
            padding: 50px 0;
            border-bottom: 1px solid #f1f1f1;
        }
        .btn-ppdb {
            background-color: var(--primary-green);
            color: white !important;
            border-radius: 50px;
            padding: 16px 40px;
            font-weight: 700;
            font-size: 1.1rem;
            box-shadow: 0 10px 20px rgba(25, 135, 84, 0.2);
            transition: 0.3s;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 12px;
        }
        .btn-ppdb:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(25, 135, 84, 0.3);
            background-color: var(--dark-green);
        }

        /* CARD & SECTION STYLING */
        .section-title {
            font-weight: 800;
            font-size: 2.2rem;
            color: var(--dark-green);
            margin-bottom: 1.5rem;
        }
        .card { border-radius: 20px; border: none; transition: 0.4s; overflow: hidden; }
        .card:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(0,0,0,0.08); }
        .img-card-custom { height: 220px; object-fit: cover; }
        
        .program-box { 
            background: var(--soft-green); 
            border-radius: 24px; 
            padding: 40px 30px; 
            transition: 0.3s; 
            height: 100%;
        }
        .program-box:hover { background: var(--primary-green); color: white; }

        /* TABEL INFORMASI CUSTOM */
        .info-table th { background-color: var(--soft-green); color: var(--dark-green); border-bottom: 2px solid #e5e7eb;}
        .info-table td { border-bottom: 1px solid #e5e7eb; vertical-align: middle; }

        /* FOOTER */
        footer {
            background-color: #0f172a;
            color: #94a3b8;
            padding: 80px 0 30px;
            border-radius: 50px 50px 0 0;
        }
        footer h5 { color: white; font-weight: 700; }

        @media (max-width: 768px) {
            .carousel-caption h1 { font-size: 1.8rem; }
            .carousel-item { height: 40vh; }
        }
    </style>
</head>
<body>
    @include('loading')

    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="{{ \App\Helpers\ImageHelper::url($sekolah->logo) }}" alt="Logo" width="40" class="me-2">
                <span class="fw-bold text-success" style="font-size: 16px;">{{ $sekolah->nama_sekolah ?? 'SEKOLAH KAMI' }}</span>
            </a>
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="bi bi-list text-success fs-1"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="#">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="#profil">Tentang</a></li>
                    @if($adaDataInformasi)
                    <li class="nav-item"><a class="nav-link" href="#informasi">Informasi</a></li>
                    @endif
                    <li class="nav-item"><a class="nav-link" href="#prestasi">Prestasi</a></li>
                    <li class="nav-item"><a class="nav-link" href="#program">Program</a></li>
                    <li class="nav-item"><a class="nav-link" href="#artikel">Berita</a></li>
                    
                    <li class="nav-item ms-lg-3">
                        @auth
                            @php
                                $dashboardUrl = match(auth()->user()->role) {
                                    'admin' => url('admin'),
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

    <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
        <div class="carousel-inner">
            @foreach($kegiatan as $key => $item)
            <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                <img src="{{ \App\Helpers\ImageHelper::url($item->foto_kegiatan) }}" class="d-block w-100" alt="Slide">
                <div class="carousel-caption">
                    <div class="container">
                        <span class="badge bg-success mb-3 px-3 py-2 rounded-pill shadow-sm">Update Kegiatan</span>
                        <h1>{{ $item->label_foto }}</h1>
                        <p class="fs-5 opacity-75 d-none d-md-block">{{ Str::limit($item->deskripsi_foto, 100) }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <section class="section-cta">
        <div class="container text-center">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h3 class="fw-bold mb-3">Siap Bergabung Bersama Kami?</h3>
                    <p class="text-muted mb-4 fs-5">Pendaftaran Peserta Didik Baru (PPDB) tahun ajaran 2026/2027 telah dibuka secara online.</p>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="{{ route('ppdb.index') }}" class="btn btn-ppdb">
                            Daftar PPDB Online Sekarang <i class="bi bi-arrow-right-circle"></i>
                        </a>
                        <a href="{{ route('brosur.publik') }}" class="btn btn-ppdb" style="background-color:#0f5132;">
                            <i class="bi bi-file-earmark-text me-2"></i>Lihat Brosur
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="profil" class="py-5">
        <div class="container py-5">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="text-success fw-bold text-uppercase mb-2 d-block">Mengenal Sekolah</span>
                    <h2 class="section-title">Lingkungan Belajar yang Nyaman dan Inspiratif</h2>
                    <p class="text-muted fs-5 lh-lg">{{ $sekolah->deskripsi }}</p>
                    <div class="mt-4">
                        <span class="badge bg-success-subtle text-success border border-success p-2 px-3 rounded-pill fw-bold">
                            <i class="bi bi-patch-check-fill me-1"></i> Terakreditasi: {{ $sekolah->akreditasi }}
                        </span>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="position-relative">
                        <img src="{{ \App\Helpers\ImageHelper::url($sekolah->foto_sekolah) }}" class="img-fluid rounded-5 shadow-lg" alt="Gedung Sekolah" style="width: 100%; height: 400px; object-fit: cover;">
                        <div class="bg-success position-absolute bottom-0 start-0 p-4 m-4 rounded-4 text-white d-none d-md-block shadow">
                            <h4 class="fw-bold mb-0">Unggul & Cerdas</h4>
                            <small>Membangun Karakter Bangsa</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- SECTION INFORMASI SEKOLAH --}}
    @if($adaDataInformasi)
    <section id="informasi" class="py-5 bg-light rounded-5 mx-2 mx-md-4">
        <div class="container py-5">
            <div class="text-center mb-5">
                <span class="text-success fw-bold text-uppercase d-block mb-2">Data Sekolah</span>
                <h2 class="section-title">Informasi Sekolah</h2>
                <p class="text-muted">Statistik dan fasilitas {{ $sekolah->nama_sekolah ?? 'sekolah kami' }}</p>
            </div>

            {{-- Tabel statistik — selalu tampil jika ada setidaknya satu data --}}
            <div class="row justify-content-center mb-5">
                <div class="col-lg-10">
                    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                        <div class="card-header bg-success text-white py-3 px-4">
                            <h6 class="mb-0 fw-bold"><i class="bi bi-bar-chart-fill me-2"></i>Statistik & Data Umum</h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table info-table mb-0">
                                <tbody>

                                    {{-- Kepala Sekolah (hanya jika ada) --}}
                                    @if($infoSekolah && $infoSekolah->nama_kepala_sekolah)
                                    <tr>
                                        <th class="ps-4 py-3" style="width:42%">
                                            <i class="bi bi-person-fill-gear me-2 text-success"></i>Kepala Sekolah
                                        </th>
                                        <td class="py-3 fw-semibold text-dark">
                                            @if($infoSekolah->foto_kepala_sekolah)
                                                <img src="{{ \App\Helpers\ImageHelper::url($infoSekolah->foto_kepala_sekolah) }}"
                                                    class="rounded-circle me-2 shadow-sm"
                                                    style="width:34px;height:34px;object-fit:cover;vertical-align:middle;">
                                            @endif
                                            {{ $infoSekolah->nama_kepala_sekolah }}
                                        </td>
                                    </tr>
                                    @endif

                                    {{-- Total Murid Aktif --}}
                                    @if($jumlah_murid > 0)
                                    <tr>
                                        <th class="ps-4 py-3">
                                            <i class="bi bi-people-fill me-2 text-success"></i>Total Murid Aktif
                                        </th>
                                        <td class="py-3 fw-semibold text-dark">
                                            {{ number_format($jumlah_murid) }} siswa
                                        </td>
                                    </tr>
                                    @endif

                                    {{-- Total Guru — diambil dari tabel guru, bukan InfoSekolah --}}
                                    @if($jumlah_guru_tampil > 0)
                                    <tr>
                                        <th class="ps-4 py-3">
                                            <i class="bi bi-person-badge-fill me-2 text-success"></i>Total Guru
                                        </th>
                                        <td class="py-3 fw-semibold text-dark">
                                            {{ number_format($jumlah_guru_tampil) }} orang
                                        </td>
                                    </tr>
                                    @endif

                                    {{-- Total Staff — diambil dari tabel staff, bukan InfoSekolah --}}
                                    @if($jumlah_staff_tampil > 0)
                                    <tr>
                                        <th class="ps-4 py-3">
                                            <i class="bi bi-person-workspace me-2 text-success"></i>Total Staff
                                        </th>
                                        <td class="py-3 fw-semibold text-dark">
                                            {{ number_format($jumlah_staff_tampil) }} orang
                                        </td>
                                    </tr>
                                    @endif

                                    {{-- Jumlah Kelas --}}
                                    @if($jumlah_kelas > 0)
                                    <tr>
                                        <th class="ps-4 py-3">
                                            <i class="bi bi-door-open-fill me-2 text-success"></i>Jumlah Kelas
                                        </th>
                                        <td class="py-3 fw-semibold text-dark">
                                            {{ number_format($jumlah_kelas) }} kelas
                                        </td>
                                    </tr>
                                    @endif

                                    {{-- Fasilitas (hanya jika ada) --}}
                                    @if($infoSekolah && $infoSekolah->fasilitas)
                                    <tr>
                                        <th class="ps-4 py-3 align-top pt-4">
                                            <i class="bi bi-building-fill-check me-2 text-success"></i>Fasilitas
                                        </th>
                                        <td class="py-3">
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach(array_filter(array_map('trim', explode(',', $infoSekolah->fasilitas))) as $fas)
                                                    <span class="badge bg-success-subtle text-success border border-success px-3 py-2 rounded-pill">
                                                        <i class="bi bi-check-circle-fill me-1"></i>{{ $fas }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                    @endif

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Program Studi: tampil seperti Program Unggulan (kotak-kotak) --}}
            @if($studiList->isNotEmpty())
            <div class="text-center mb-4">
                <span class="text-success fw-bold text-uppercase d-block mb-2">Akademik</span>
                <h3 class="fw-bold" style="color: var(--dark-green);">Program Studi</h3>
                <p class="text-muted">Pilihan program studi yang tersedia di {{ $sekolah->nama_sekolah ?? 'sekolah kami' }}</p>
            </div>
            <div class="row g-4">
                @foreach($studiList as $s)
                <div class="col-md-4">
                    <div class="program-box shadow-sm text-center">
                        <i class="bi bi-mortarboard-fill text-success fs-1 mb-3"></i>
                        <h5 class="fw-bold">{{ $s->nama_studi }}</h5>
                        <p class="small mb-0 opacity-75">{{ $s->deskripsi_studi ?? '' }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

        </div>
    </section>
    @endif

    <section id="prestasi" class="py-5">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="section-title">Prestasi Siswa Kami</h2>
                <p class="text-muted">Bangga atas pencapaian akademik dan non-akademik siswa kami.</p>
            </div>
            <div class="row g-4">
                @foreach($prestasi->take(3) as $pres)
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border">
                        @if($pres->foto_prestasi && $pres->foto_prestasi !== '-')
                            <img src="{{ \App\Helpers\ImageHelper::url($pres->foto_prestasi) }}" class="card-img-top img-card-custom" alt="Prestasi">
                        @endif
                        <div class="card-body p-4">
                            <h5 class="fw-bold">{{ $pres->judul_prestasi }}</h5>
                            <p class="text-muted small mb-0">{{ Str::limit($pres->deskripsi_prestasi, 100) }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="program" class="py-5 bg-light rounded-5 mx-2 mx-md-4">
        <div class="container py-5">
            <h2 class="section-title text-center mb-5">Program Unggulan</h2>
            <div class="row g-4">
                @foreach($programs as $prog)
                <div class="col-md-4">
                    <div class="program-box shadow-sm text-center">
                        <i class="bi bi-stars text-success fs-1 mb-3"></i>
                        <h5 class="fw-bold">{{ $prog->nama_program }}</h5>
                        <p class="small mb-0 opacity-75">{{ $prog->deskripsi_program }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="artikel" class="py-5">
        <div class="container py-5">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <h2 class="section-title mb-0">Artikel Sekolah</h2>
                <a href="/artikel" class="btn btn-outline-success rounded-pill px-4 fw-bold">Lihat Semua</a>
            </div>
            <div class="row g-4">
                @foreach($artikels->take(3) as $art)
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border">
                        @if($art->foto_artikel)
                            <img src="{{ \App\Helpers\ImageHelper::url($art->foto_artikel) }}" class="card-img-top img-card-custom" alt="Berita">
                        @endif
                        <div class="card-body p-4">
                            <small class="text-success fw-bold d-block mb-2">{{ $art->created_at->format('d M Y') }}</small>
                            <h5 class="fw-bold mb-3">{{ $art->judul }}</h5>
                            <p class="text-muted small mb-4">{{ Str::limit($art->teaser, 110) }}</p>
                            <a href="{{ route('artikel.show', $art->slug) }}" class="text-success fw-bold text-decoration-none">Selengkapnya <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center mb-4">
                        <img src="{{ \App\Helpers\ImageHelper::url($sekolah->logo) }}" width="50" class="me-3 bg-white p-1 rounded-circle">
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