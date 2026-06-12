<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brosur | {{ $sekolah->nama_sekolah ?? 'Website Sekolah' }}</title>

    @if($sekolah && $sekolah->logo)
        <link rel="icon" type="image/png" href="{{ \App\Helpers\ImageHelper::url($sekolah->logo) }}">
    @endif

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --primary-green: #198754;
            --dark-green: #0b4629;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8faf9;
            color: #2d3436;
        }
        .navbar {
            padding: 12px 0;
            background: rgba(255,255,255,.95) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0,0,0,.06);
        }
        .page-hero {
            background: linear-gradient(135deg, #0b4629 0%, #198754 100%);
            color: #fff;
            padding: 64px 0 48px;
            text-align: center;
        }
        .page-hero h1 { font-size: 2.2rem; font-weight: 800; }
        .page-hero p  { opacity: .85; font-size: 1rem; }

        .brosur-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 12px rgba(0,0,0,.07);
            transition: transform .2s, box-shadow .2s;
            overflow: hidden;
        }
        .brosur-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 28px rgba(25,135,84,.15);
        }
        .brosur-icon {
            background: linear-gradient(135deg, #d1e7dd, #a3cfbb);
            height: 140px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3.5rem;
            color: var(--primary-green);
        }
        .brosur-body { padding: 20px; }
        .brosur-label { font-weight: 700; font-size: 1rem; color: #1a2e25; }
        .brosur-desc  { font-size: .83rem; color: #6c8f7d; line-height: 1.5; min-height: 36px; }
        .btn-lihat {
            background-color: var(--primary-green);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 8px 20px;
            font-weight: 600;
            font-size: .875rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background .2s, transform .15s;
            text-decoration: none;
        }
        .btn-lihat:hover { background-color: #146c43; color: #fff; transform: translateY(-1px); }
        .empty-state {
            text-align: center;
            padding: 80px 0;
            color: #aaa;
        }
        footer {
            background: #0b4629;
            color: rgba(255,255,255,.75);
            padding: 32px 0;
            font-size: .875rem;
            text-align: center;
        }
    </style>
</head>
<body>

    {{-- NAVBAR --}}
    <nav class="navbar navbar-expand-lg sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                @if($sekolah && $sekolah->logo)
                    <img src="{{ \App\Helpers\ImageHelper::url($sekolah->logo) }}" alt="Logo" width="36" class="me-2">
                @endif
                <span class="fw-bold text-success" style="font-size:15px;">{{ $sekolah->nama_sekolah ?? 'SEKOLAH' }}</span>
            </a>
            <a href="{{ route('home') }}" class="btn btn-sm btn-outline-success ms-auto">
                <i class="bi bi-house me-1"></i> Kembali ke Beranda
            </a>
        </div>
    </nav>

    {{-- HERO --}}
    <div class="page-hero">
        <div class="container">
            <h1><i class="bi bi-file-earmark-text me-2"></i>Brosur Sekolah</h1>
            <p>Unduh dan lihat brosur resmi {{ $sekolah->nama_sekolah ?? 'sekolah kami' }} di bawah ini.</p>
        </div>
    </div>

    {{-- KONTEN --}}
    <div class="container py-5">
        @if($brosurList->isEmpty())
            <div class="empty-state">
                <i class="bi bi-file-earmark-x" style="font-size:4rem; display:block; margin-bottom:16px;"></i>
                <h5>Belum Ada Brosur</h5>
                <p>Brosur belum tersedia saat ini. Silakan cek kembali nanti.</p>
                <a href="{{ route('home') }}" class="btn btn-outline-success mt-2">Kembali ke Beranda</a>
            </div>
        @else
            <div class="row g-4">
                @foreach($brosurList as $b)
                <div class="col-sm-6 col-lg-4">
                    <div class="brosur-card">
                        <div class="brosur-icon">
                            @php
                                $ext = pathinfo($b->path_file, PATHINFO_EXTENSION);
                                $icon = in_array(strtolower($ext), ['jpg','jpeg','png']) ? 'bi-file-earmark-image' : 'bi-file-earmark-pdf';
                            @endphp
                            <i class="bi {{ $icon }}"></i>
                        </div>
                        <div class="brosur-body">
                            <div class="brosur-label mb-1">{{ $b->label }}</div>
                            @if($b->deskripsi)
                                <div class="brosur-desc mb-3">{{ $b->deskripsi }}</div>
                            @else
                                <div class="brosur-desc mb-3 text-muted fst-italic">Tidak ada deskripsi</div>
                            @endif
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ Storage::url($b->path_file) }}" target="_blank" class="btn-lihat">
                                    <i class="bi bi-eye"></i> Lihat
                                </a>
                                <a href="{{ Storage::url($b->path_file) }}" download class="btn-lihat" style="background-color:#0f5132;">
                                    <i class="bi bi-download"></i> Unduh
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- FOOTER --}}
    <footer>
        <div class="container">
            <p class="mb-0">&copy; {{ date('Y') }} {{ $sekolah->nama_sekolah ?? 'Sekolah' }}. All Rights Reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
