<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- SEO: Title & Meta --}}
    <title>{{ $sekolah->nama_sekolah ?? 'Website Sekolah' }} | Sekolah Terbaik</title>
    <meta name="description" content="{{ Str::limit($sekolah->deskripsi ?? 'Website resmi sekolah. Informasi PPDB, prestasi, program unggulan, dan berita terbaru.', 155) }}">
    <meta name="keywords" content="{{ $sekolah->nama_sekolah ?? 'sekolah' }}, PPDB, pendaftaran siswa baru, sekolah unggulan, informasi sekolah">
    <meta name="robots" content="index, follow">
    <meta name="author" content="{{ $sekolah->nama_sekolah ?? 'Website Sekolah' }}">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- SEO: Open Graph (WhatsApp / Facebook share) --}}
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $sekolah->nama_sekolah ?? 'Website Sekolah' }}">
    <meta property="og:description" content="{{ Str::limit($sekolah->deskripsi ?? 'Website resmi sekolah.', 155) }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ \App\Helpers\ImageHelper::url($sekolah->foto_sekolah ?? $sekolah->logo) }}">
    <meta property="og:locale" content="id_ID">

    {{-- SEO: Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $sekolah->nama_sekolah ?? 'Website Sekolah' }}">
    <meta name="twitter:description" content="{{ Str::limit($sekolah->deskripsi ?? 'Website resmi sekolah.', 155) }}">
    <meta name="twitter:image" content="{{ \App\Helpers\ImageHelper::url($sekolah->foto_sekolah ?? $sekolah->logo) }}">

    {{-- SEO: Schema.org JSON-LD --}}
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "School",
        "name": "{{ $sekolah->nama_sekolah ?? 'Website Sekolah' }}",
        "description": "{{ addslashes(Str::limit($sekolah->deskripsi ?? '', 200)) }}",
        "address": {
            "@@type": "PostalAddress",
            "streetAddress": "{{ addslashes($sekolah->alamat ?? '') }}"
        },
        "telephone": "{{ $sekolah->no_hp ?? '' }}",
        "email": "{{ $sekolah->email ?? '' }}",
        "url": "{{ url('/') }}"
    }
    </script>

    <link rel="icon" type="image/png" href="{{ \App\Helpers\ImageHelper::url($sekolah->logo) }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --primary-green: #198754;
            --dark-green: #0b4629;
            --soft-green: #f0fdf4;
        }

        *, *::before, *::after { box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #2d3436;
            background-color: #ffffff;
            overflow-x: hidden;
        }

        /* ─── NAVBAR ─────────────────────────────────── */
        .navbar {
            padding: 10px 0;
            background: rgba(255, 255, 255, 0.93) !important;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0,0,0,0.06);
            transition: box-shadow 0.3s;
        }
        .navbar.scrolled { box-shadow: 0 4px 20px rgba(0,0,0,0.08); }

        .navbar-brand img { width: 38px; height: 38px; object-fit: contain; }
        .navbar-brand span { font-size: 15px; }

        .nav-link {
            font-weight: 600;
            font-size: 0.9rem;
            color: #4b5563 !important;
            padding: 6px 10px !important;
            border-radius: 8px;
            transition: color 0.2s, background 0.2s;
        }
        .nav-link:hover { color: var(--primary-green) !important; background: var(--soft-green); }

        /* Hamburger — hapus outline, border, shadow bawaan Bootstrap */
        .navbar-toggler {
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            padding: 4px 6px;
            background: transparent !important;
        }
        .navbar-toggler:focus,
        .navbar-toggler:active,
        .navbar-toggler:focus-visible {
            outline: none !important;
            box-shadow: none !important;
            border: none !important;
        }

        /* Ikon hamburger dengan animasi rotasi */
        .hamburger-icon {
            font-size: 1.7rem;
            color: var(--primary-green);
            display: inline-block;
            transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.2s;
        }
        /* Saat navbar terbuka, ikon berputar 180° dan ganti ke X */
        .navbar-toggler[aria-expanded="true"] .hamburger-icon {
            transform: rotate(180deg);
        }

        .btn-login {
            border: 2px solid var(--primary-green);
            color: var(--primary-green) !important;
            border-radius: 12px;
            padding: 7px 18px;
            font-weight: 700;
            font-size: 0.88rem;
            transition: background 0.25s, color 0.25s;
            white-space: nowrap;
        }
        .btn-login:hover { background-color: var(--primary-green); color: white !important; }

        /* ─── HERO SLIDER ────────────────────────────── */
        .hero-carousel .carousel-item {
            height: 60vh;
            min-height: 320px;
            max-height: 680px;
            background-color: #111;
        }
        .hero-carousel .carousel-item img {
            opacity: 0.45;
            object-fit: cover;
            width: 100%;
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
            padding: 0 1rem;
        }
        .carousel-caption h1 {
            font-weight: 800;
            font-size: clamp(1.4rem, 4vw, 3rem);
            text-shadow: 0 4px 14px rgba(0,0,0,0.55);
            line-height: 1.2;
        }
        .carousel-caption p {
            font-size: clamp(0.85rem, 2vw, 1.1rem);
        }

        /* ─── SECTION CTA (PPDB) ─────────────────────── */
        .section-cta {
            background-color: #fff;
            padding: 48px 0;
            border-bottom: 1px solid #f1f1f1;
        }
        .section-cta h3 { font-size: clamp(1.2rem, 3vw, 1.6rem); }
        .section-cta p  { font-size: clamp(0.9rem, 2vw, 1.05rem); }

        .btn-ppdb {
            background-color: var(--primary-green);
            color: white !important;
            border-radius: 50px;
            padding: 13px 30px;
            font-weight: 700;
            font-size: clamp(0.88rem, 2vw, 1rem);
            box-shadow: 0 8px 20px rgba(25, 135, 84, 0.25);
            transition: transform 0.25s, box-shadow 0.25s, background 0.25s;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            white-space: nowrap;
        }
        .btn-ppdb:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 28px rgba(25, 135, 84, 0.3);
            background-color: var(--dark-green);
        }

        /* ─── SECTION TITLES & CARDS ─────────────────── */
        .section-title {
            font-weight: 800;
            font-size: clamp(1.5rem, 4vw, 2.2rem);
            color: var(--dark-green);
            line-height: 1.2;
        }
        .section-label {
            font-size: 0.78rem;
            letter-spacing: 0.08em;
        }

        .card {
            border-radius: 18px;
            border: none;
            transition: transform 0.35s, box-shadow 0.35s;
            overflow: hidden;
        }
        .card:hover { transform: translateY(-8px); box-shadow: 0 18px 40px rgba(0,0,0,0.09); }
        .img-card-custom {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }

        .program-box {
            background: var(--soft-green);
            border-radius: 20px;
            padding: 36px 24px;
            transition: background 0.3s, color 0.3s;
            height: 100%;
        }
        .program-box:hover { background: var(--primary-green); color: white; }
        .program-box:hover .text-success { color: white !important; }
        .program-box:hover p { opacity: 0.85; }

        /* ─── TABEL INFORMASI ────────────────────────── */
        .info-table th {
            background-color: var(--soft-green);
            color: var(--dark-green);
            border-bottom: 2px solid #e5e7eb;
            font-size: 0.9rem;
        }
        .info-table td {
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
            font-size: 0.9rem;
        }

        /* ─── FOTO SEKOLAH (PROFIL) ──────────────────── */
        .foto-sekolah {
            width: 100%;
            height: clamp(250px, 40vw, 400px);
            object-fit: cover;
        }

        /* ─── FOOTER ─────────────────────────────────── */
        footer {
            background-color: #0f172a;
            color: #94a3b8;
            padding: clamp(48px, 8vw, 80px) 0 30px;
            border-radius: 40px 40px 0 0;
        }
        footer h5 { color: white; font-weight: 700; font-size: 1rem; }
        footer p, footer li { font-size: 0.88rem; }
        footer .social-link {
            color: #94a3b8;
            font-size: 1.4rem;
            transition: color 0.2s;
        }
        footer .social-link:hover { color: #fff; }

        /* ─── RESPONSIVE ─────────────────────────────── */
        @media (max-width: 991.98px) {
            .navbar-collapse {
                background: rgba(255,255,255,0.98);
                border-radius: 16px;
                padding: 12px 16px;
                margin-top: 8px;
                box-shadow: 0 8px 24px rgba(0,0,0,0.1);
            }
            .navbar-nav .nav-item { border-bottom: 1px solid #f3f4f6; }
            .navbar-nav .nav-item:last-child { border-bottom: none; }
            .nav-link { padding: 10px 8px !important; }
            .btn-login, .btn.btn-success { width: 100%; text-align: center; margin-top: 8px; }
        }

        @media (max-width: 767.98px) {
            .hero-carousel .carousel-item { height: 48vh; min-height: 260px; }
            .section-cta { padding: 36px 0; }
            .btn-ppdb { padding: 11px 22px; }
            #profil .col-lg-6:last-child { margin-top: 0; }
            .foto-sekolah { height: 220px; }
            .img-card-custom { height: 180px; }
            .program-box { padding: 28px 18px; }
            footer { border-radius: 28px 28px 0 0; }
        }

        @media (max-width: 575.98px) {
            .hero-carousel .carousel-item { height: 42vh; min-height: 220px; }
            .navbar-brand span { display: none; }
            .carousel-caption h1 { font-size: 1.25rem; }
            .section-cta .d-flex { flex-direction: column; align-items: stretch; }
            .btn-ppdb { width: 100%; justify-content: center; }
        }
    </style>
</head>
<body>
    @include('loading')

    {{-- ═══════════════════════════════════════════════════════
         NAVBAR — sticky glassmorphism + hamburger animasi
    ═══════════════════════════════════════════════════════ --}}
    <header>
        <nav class="navbar navbar-expand-lg sticky-top" aria-label="Navigasi utama">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}" aria-label="{{ $sekolah->nama_sekolah ?? 'Beranda' }}">
                    <img src="{{ \App\Helpers\ImageHelper::url($sekolah->logo) }}"
                         alt="Logo {{ $sekolah->nama_sekolah ?? 'Sekolah' }}"
                         width="38" height="38">
                    <span class="fw-bold text-success">{{ $sekolah->nama_sekolah ?? 'SEKOLAH KAMI' }}</span>
                </a>

                {{-- Hamburger: outline dihapus, ikon berputar saat open/close --}}
                <button class="navbar-toggler"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#navbarNav"
                        aria-controls="navbarNav"
                        aria-expanded="false"
                        aria-label="Toggle navigasi">
                    <i class="hamburger-icon bi bi-list"></i>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto align-items-lg-center">
                        <li class="nav-item"><a class="nav-link" href="{{ url('/') }}">Beranda</a></li>
                        <li class="nav-item"><a class="nav-link" href="#profil">Tentang</a></li>
                        @if($adaDataInformasi)
                        <li class="nav-item"><a class="nav-link" href="#informasi">Informasi</a></li>
                        @endif
                        <li class="nav-item"><a class="nav-link" href="#prestasi">Prestasi</a></li>
                        <li class="nav-item"><a class="nav-link" href="#program">Program</a></li>
                        <li class="nav-item"><a class="nav-link" href="#artikel">Berita</a></li>

                        <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                            @auth
                                @php
                                    $dashboardUrl = match(auth()->user()->role) {
                                        'admin' => url('admin'),
                                        default => url('/'),
                                    };
                                @endphp
                                <a class="btn btn-success fw-bold"
                                   style="border-radius:12px;padding:8px 20px;font-size:0.88rem;"
                                   href="{{ $dashboardUrl }}">
                                    <i class="bi bi-speedometer2 me-1"></i> Dashboard
                                </a>
                            @else
                                <a class="btn btn-login" href="{{ route('login') }}">
                                    <i class="bi bi-person-circle me-1"></i> Log In
                                </a>
                            @endauth
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main>

        {{-- ═══════════════════════════════════════════════════════
             HERO SLIDER
        ═══════════════════════════════════════════════════════ --}}
        <section aria-label="Galeri kegiatan sekolah">
            <div id="heroCarousel" class="carousel slide carousel-fade hero-carousel" data-bs-ride="carousel">
                <div class="carousel-inner">
                    @foreach($kegiatan as $key => $item)
                    <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                        <img src="{{ \App\Helpers\ImageHelper::url($item->foto_kegiatan) }}"
                             class="d-block w-100"
                             alt="{{ $item->label_foto ?? 'Kegiatan sekolah' }}"
                             loading="{{ $key == 0 ? 'eager' : 'lazy' }}">
                        <div class="carousel-caption">
                            <div class="container">
                                <span class="badge bg-success mb-3 px-3 py-2 rounded-pill shadow-sm" style="font-size:0.78rem;">Update Kegiatan</span>
                                <h1>{{ $item->label_foto }}</h1>
                                <p class="opacity-75 d-none d-md-block mb-0">{{ Str::limit($item->deskripsi_foto, 100) }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @if($kegiatan->count() > 1)
                <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
                @endif
            </div>
        </section>

        {{-- ═══════════════════════════════════════════════════════
             SEKSI PPDB CTA
        ═══════════════════════════════════════════════════════ --}}
        <section class="section-cta" aria-label="Pendaftaran Peserta Didik Baru">
            <div class="container text-center">
                <div class="row justify-content-center">
                    <div class="col-lg-7 col-md-10">
                        <h2 class="fw-bold mb-2" style="font-size:clamp(1.2rem,3vw,1.5rem);">Siap Bergabung Bersama Kami?</h2>
                        <p class="text-muted mb-4" style="font-size:clamp(0.88rem,2vw,1rem);">
                            Pendaftaran Peserta Didik Baru (PPDB) tahun ajaran 2026/2027 telah dibuka secara online.
                        </p>
                        <div class="d-flex flex-wrap justify-content-center gap-3">
                            <a href="{{ route('ppdb.index') }}" class="btn btn-ppdb">
                                Daftar PPDB Online Sekarang <i class="bi bi-arrow-right-circle"></i>
                            </a>
                            <a href="{{ route('brosur.publik') }}" class="btn btn-ppdb" style="background-color:#0f5132;">
                                <i class="bi bi-file-earmark-text"></i> Lihat Brosur
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ═══════════════════════════════════════════════════════
             PROFIL SEKOLAH
        ═══════════════════════════════════════════════════════ --}}
        <section id="profil" class="py-5" aria-labelledby="heading-profil">
            <div class="container py-4 py-md-5">
                <div class="row align-items-center g-4 g-lg-5">
                    <div class="col-lg-6">
                        <span class="text-success fw-bold text-uppercase section-label mb-2 d-block">Mengenal Sekolah</span>
                        <h2 id="heading-profil" class="section-title mb-3">Lingkungan Belajar yang Nyaman dan Inspiratif</h2>
                        <p class="text-muted lh-lg" style="font-size:clamp(0.9rem,2vw,1rem);">{{ $sekolah->deskripsi }}</p>
                        <div class="mt-3">
                            <span class="badge bg-success-subtle text-success border border-success p-2 px-3 rounded-pill fw-bold" style="font-size:0.82rem;">
                                <i class="bi bi-patch-check-fill me-1"></i> Terakreditasi: {{ $sekolah->akreditasi }}
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="position-relative">
                            <img src="{{ \App\Helpers\ImageHelper::url($sekolah->foto_sekolah) }}"
                                 class="img-fluid rounded-5 shadow-lg foto-sekolah"
                                 alt="Gedung {{ $sekolah->nama_sekolah ?? 'Sekolah' }}"
                                 loading="lazy">
                            <div class="bg-success position-absolute bottom-0 start-0 p-3 m-3 rounded-4 text-white d-none d-md-block shadow">
                                <h4 class="fw-bold mb-0" style="font-size:1rem;">Unggul & Cerdas</h4>
                                <small style="font-size:0.78rem;">Membangun Karakter Bangsa</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ═══════════════════════════════════════════════════════
             INFORMASI SEKOLAH
        ═══════════════════════════════════════════════════════ --}}
        @if($adaDataInformasi)
        <section id="informasi" class="py-5 bg-light rounded-5 mx-2 mx-md-4" aria-labelledby="heading-informasi">
            <div class="container py-4 py-md-5">
                <div class="text-center mb-4 mb-md-5">
                    <span class="text-success fw-bold text-uppercase section-label d-block mb-2">Data Sekolah</span>
                    <h2 id="heading-informasi" class="section-title">Informasi Sekolah</h2>
                    <p class="text-muted" style="font-size:0.95rem;">Statistik dan fasilitas {{ $sekolah->nama_sekolah ?? 'sekolah kami' }}</p>
                </div>

                <div class="row justify-content-center mb-4 mb-md-5">
                    <div class="col-lg-10">
                        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                            <div class="card-header bg-success text-white py-3 px-4">
                                <h3 class="mb-0 fw-bold" style="font-size:0.95rem;"><i class="bi bi-bar-chart-fill me-2"></i>Statistik &amp; Data Umum</h3>
                            </div>
                            <div class="table-responsive">
                                <table class="table info-table mb-0">
                                    <tbody>
                                        @if($infoSekolah && $infoSekolah->nama_kepala_sekolah)
                                        <tr>
                                            <th class="ps-4 py-3" style="width:45%">
                                                <i class="bi bi-person-fill-gear me-2 text-success"></i>Kepala Sekolah
                                            </th>
                                            <td class="py-3 fw-semibold text-dark">
                                                @if($infoSekolah->foto_kepala_sekolah)
                                                    <img src="{{ \App\Helpers\ImageHelper::url($infoSekolah->foto_kepala_sekolah) }}"
                                                         class="rounded-circle me-2 shadow-sm"
                                                         style="width:32px;height:32px;object-fit:cover;vertical-align:middle;"
                                                         alt="Foto Kepala Sekolah"
                                                         loading="lazy">
                                                @endif
                                                {{ $infoSekolah->nama_kepala_sekolah }}
                                            </td>
                                        </tr>
                                        @endif
                                        @if($jumlah_murid > 0)
                                        <tr>
                                            <th class="ps-4 py-3"><i class="bi bi-people-fill me-2 text-success"></i>Total Murid Aktif</th>
                                            <td class="py-3 fw-semibold text-dark">{{ number_format($jumlah_murid) }} siswa</td>
                                        </tr>
                                        @endif
                                        @if($jumlah_guru_tampil > 0)
                                        <tr>
                                            <th class="ps-4 py-3"><i class="bi bi-person-badge-fill me-2 text-success"></i>Total Guru</th>
                                            <td class="py-3 fw-semibold text-dark">{{ number_format($jumlah_guru_tampil) }} orang</td>
                                        </tr>
                                        @endif
                                        @if($jumlah_staff_tampil > 0)
                                        <tr>
                                            <th class="ps-4 py-3"><i class="bi bi-person-workspace me-2 text-success"></i>Total Staff</th>
                                            <td class="py-3 fw-semibold text-dark">{{ number_format($jumlah_staff_tampil) }} orang</td>
                                        </tr>
                                        @endif
                                        @if($jumlah_kelas > 0)
                                        <tr>
                                            <th class="ps-4 py-3"><i class="bi bi-door-open-fill me-2 text-success"></i>Jumlah Kelas</th>
                                            <td class="py-3 fw-semibold text-dark">{{ number_format($jumlah_kelas) }} kelas</td>
                                        </tr>
                                        @endif
                                        @if($infoSekolah && $infoSekolah->fasilitas)
                                        <tr>
                                            <th class="ps-4 py-3 align-top pt-4"><i class="bi bi-building-fill-check me-2 text-success"></i>Fasilitas</th>
                                            <td class="py-3">
                                                <div class="d-flex flex-wrap gap-2">
                                                    @foreach(array_filter(array_map('trim', explode(',', $infoSekolah->fasilitas))) as $fas)
                                                        <span class="badge bg-success-subtle text-success border border-success px-3 py-2 rounded-pill" style="font-size:0.78rem;">
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

                @if($studiList->isNotEmpty())
                <div class="text-center mb-4">
                    <span class="text-success fw-bold text-uppercase section-label d-block mb-2">Akademik</span>
                    <h3 class="fw-bold" style="color:var(--dark-green);font-size:clamp(1.2rem,3vw,1.6rem);">Program Studi</h3>
                    <p class="text-muted" style="font-size:0.92rem;">Pilihan program studi yang tersedia di {{ $sekolah->nama_sekolah ?? 'sekolah kami' }}</p>
                </div>
                <div class="row g-3 g-md-4">
                    @foreach($studiList as $s)
                    <div class="col-sm-6 col-md-4">
                        <div class="program-box shadow-sm text-center">
                            <i class="bi bi-mortarboard-fill text-success fs-1 mb-3" aria-hidden="true"></i>
                            <h4 class="fw-bold" style="font-size:1rem;">{{ $s->nama_studi }}</h4>
                            <p class="small mb-0 opacity-75">{{ $s->deskripsi_studi ?? '' }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </section>
        @endif

        {{-- ═══════════════════════════════════════════════════════
             PRESTASI SISWA
        ═══════════════════════════════════════════════════════ --}}
        <section id="prestasi" class="py-5" aria-labelledby="heading-prestasi">
            <div class="container py-4 py-md-5">
                <div class="text-center mb-4 mb-md-5">
                    <h2 id="heading-prestasi" class="section-title">Prestasi Siswa Kami</h2>
                    <p class="text-muted" style="font-size:0.95rem;">Bangga atas pencapaian akademik dan non-akademik siswa kami.</p>
                </div>
                <div class="row g-3 g-md-4">
                    @foreach($prestasi->take(3) as $pres)
                    <div class="col-sm-6 col-md-4">
                        <article class="card h-100 shadow-sm border">
                            @if($pres->foto_prestasi && $pres->foto_prestasi !== '-')
                                <img src="{{ \App\Helpers\ImageHelper::url($pres->foto_prestasi) }}"
                                     class="card-img-top img-card-custom"
                                     alt="{{ $pres->judul_prestasi }}"
                                     loading="lazy">
                            @endif
                            <div class="card-body p-3 p-md-4">
                                <h3 class="fw-bold" style="font-size:1rem;">{{ $pres->judul_prestasi }}</h3>
                                <p class="text-muted small mb-0">{{ Str::limit($pres->deskripsi_prestasi, 100) }}</p>
                            </div>
                        </article>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ═══════════════════════════════════════════════════════
             PROGRAM UNGGULAN
        ═══════════════════════════════════════════════════════ --}}
        <section id="program" class="py-5 bg-light rounded-5 mx-2 mx-md-4" aria-labelledby="heading-program">
            <div class="container py-4 py-md-5">
                <h2 id="heading-program" class="section-title text-center mb-4 mb-md-5">Program Unggulan</h2>
                <div class="row g-3 g-md-4">
                    @foreach($programs as $prog)
                    <div class="col-sm-6 col-md-4">
                        <div class="program-box shadow-sm text-center">
                            <i class="bi bi-stars text-success fs-1 mb-3" aria-hidden="true"></i>
                            <h3 class="fw-bold" style="font-size:1rem;">{{ $prog->nama_program }}</h3>
                            <p class="small mb-0 opacity-75">{{ $prog->deskripsi_program }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ═══════════════════════════════════════════════════════
             ARTIKEL / BERITA
        ═══════════════════════════════════════════════════════ --}}
        <section id="artikel" class="py-5" aria-labelledby="heading-artikel">
            <div class="container py-4 py-md-5">
                <div class="d-flex justify-content-between align-items-center mb-4 mb-md-5 flex-wrap gap-3">
                    <h2 id="heading-artikel" class="section-title mb-0">Artikel Sekolah</h2>
                    <a href="{{ url('/artikel') }}" class="btn btn-outline-success rounded-pill px-4 fw-bold" style="font-size:0.88rem;">Lihat Semua</a>
                </div>
                <div class="row g-3 g-md-4">
                    @foreach($artikels->take(3) as $art)
                    <div class="col-sm-6 col-md-4">
                        <article class="card h-100 shadow-sm border">
                            @if($art->foto_artikel)
                                <img src="{{ \App\Helpers\ImageHelper::url($art->foto_artikel) }}"
                                     class="card-img-top img-card-custom"
                                     alt="{{ $art->judul }}"
                                     loading="lazy">
                            @endif
                            <div class="card-body p-3 p-md-4 d-flex flex-column">
                                <small class="text-success fw-bold d-block mb-2" style="font-size:0.78rem;">
                                    <time datetime="{{ $art->created_at->format('Y-m-d') }}">{{ $art->created_at->format('d M Y') }}</time>
                                </small>
                                <h3 class="fw-bold mb-2" style="font-size:1rem;">{{ $art->judul }}</h3>
                                <p class="text-muted small mb-3 flex-grow-1">{{ Str::limit($art->teaser, 110) }}</p>
                                <a href="{{ route('artikel.show', $art->slug) }}"
                                   class="text-success fw-bold text-decoration-none mt-auto"
                                   style="font-size:0.88rem;">
                                    Selengkapnya <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </article>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

    </main>{{-- /main --}}

    {{-- ═══════════════════════════════════════════════════════
         FOOTER
    ═══════════════════════════════════════════════════════ --}}
    <footer aria-label="Informasi kontak dan lokasi sekolah">
        <div class="container">
            <div class="row g-4 g-lg-5">
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex align-items-center mb-3 gap-3">
                        <img src="{{ \App\Helpers\ImageHelper::url($sekolah->logo) }}"
                             width="46" height="46"
                             class="bg-white p-1 rounded-circle"
                             alt="Logo {{ $sekolah->nama_sekolah }}">
                        <h2 class="mb-0 h5">{{ $sekolah->nama_sekolah }}</h2>
                    </div>
                    <p class="small lh-lg">{{ Str::limit($sekolah->deskripsi, 150) }}</p>
                    <div class="d-flex gap-3 mt-3">
                        <a href="#" class="social-link" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="social-link" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="social-link" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <h3 class="h5 mb-3">Informasi Kontak</h3>
                    <address class="list-unstyled mt-3" style="font-style:normal;">
                        <p class="mb-3 d-flex align-items-start gap-2 small">
                            <i class="bi bi-geo-alt-fill text-success fs-6 flex-shrink-0 mt-1" aria-hidden="true"></i>
                            <span>{{ $sekolah->alamat }}</span>
                        </p>
                        <p class="mb-3 d-flex align-items-center gap-2 small">
                            <i class="bi bi-telephone-fill text-success fs-6 flex-shrink-0" aria-hidden="true"></i>
                            <a href="tel:{{ $sekolah->no_hp }}" class="text-secondary text-decoration-none">{{ $sekolah->no_hp }}</a>
                        </p>
                        <p class="mb-0 d-flex align-items-center gap-2 small">
                            <i class="bi bi-envelope-fill text-success fs-6 flex-shrink-0" aria-hidden="true"></i>
                            <a href="mailto:{{ $sekolah->email }}" class="text-secondary text-decoration-none">{{ $sekolah->email }}</a>
                        </p>
                    </address>
                </div>
                <div class="col-lg-4 col-md-12">
                    <h3 class="h5 mb-3">Lokasi Kami</h3>
                    <div class="rounded-4 overflow-hidden shadow-lg mt-3" style="height:200px;">
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
    <script>
        // Navbar shadow saat scroll
        window.addEventListener('scroll', function () {
            document.querySelector('.navbar').classList.toggle('scrolled', window.scrollY > 10);
        }, { passive: true });

        // Hamburger: ganti ikon bi-list ↔ bi-x saat toggle dengan animasi rotasi
        const navbarToggler = document.querySelector('.navbar-toggler');
        const hamburgerIcon = navbarToggler ? navbarToggler.querySelector('.hamburger-icon') : null;

        if (navbarToggler && hamburgerIcon) {
            navbarToggler.addEventListener('click', function () {
                const isExpanded = this.getAttribute('aria-expanded') === 'true';
                // aria-expanded di-update oleh Bootstrap setelah animasi,
                // jadi kita pakai class navbarNav untuk mendeteksi state saat ini
                if (hamburgerIcon.classList.contains('bi-x')) {
                    hamburgerIcon.classList.replace('bi-x', 'bi-list');
                } else {
                    hamburgerIcon.classList.replace('bi-list', 'bi-x');
                }
            });
        }

        // Active nav-link berdasarkan anchor yang sedang terlihat (IntersectionObserver)
        const sections = document.querySelectorAll('section[id]');
        const navLinks = document.querySelectorAll('.navbar-nav .nav-link[href^="#"]');

        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        navLinks.forEach(link => {
                            link.classList.remove('active', 'text-success');
                            if (link.getAttribute('href') === '#' + entry.target.id) {
                                link.classList.add('active', 'text-success');
                            }
                        });
                    }
                });
            }, { threshold: 0.35 });
            sections.forEach(s => observer.observe(s));
        }
    </script>
</body>
</html>
