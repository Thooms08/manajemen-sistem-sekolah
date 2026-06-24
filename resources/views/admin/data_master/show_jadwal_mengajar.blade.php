<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Mengajar — {{ $hariDipilih }}</title>
    @if(isset($sekolah->logo))
    <link rel="icon" type="image/png" href="{{ \App\Helpers\ImageHelper::url($sekolah->logo) }}">
    @else
    <link rel="icon" type="image/png" href="{{ asset('assets/img/default-favicon.png') }}">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f1f5f9; }
        .wrapper { display: flex; align-items: stretch; }
        #content { flex: 1; padding: 24px 32px; min-height: 100vh; }
        #sidebarCollapse { width: 42px; height: 42px; background: #198754; border: none; color: #fff; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
        #overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 1040; }
        #overlay.active { display: block; }

        /* ── Navigasi hari ── */
        .hari-nav { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 20px; }
        .hari-nav a {
            padding: 8px 18px; border-radius: 50px; font-size: 0.88rem;
            font-weight: 600; text-decoration: none; border: 2px solid #e2e8f0;
            background: #fff; color: #475569; transition: all .15s;
        }
        .hari-nav a:hover { border-color: #198754; color: #198754; }
        .hari-nav a.aktif { background: #198754; border-color: #198754; color: #fff; }
        .hari-nav a.hari-ini-badge::after {
            content: ' •'; color: #fbbf24; font-size: 1rem; vertical-align: middle;
        }

        /* ── Tombol download ── */
        .btn-dl-pdf { background: #dc2626; color: #fff; border: none; border-radius: 10px; padding: 9px 20px; font-size: 0.92rem; font-weight: 600; }
        .btn-dl-png { background: #2563eb; color: #fff; border: none; border-radius: 10px; padding: 9px 20px; font-size: 0.92rem; font-weight: 600; }
        .btn-dl-pdf:hover { background: #b91c1c; color: #fff; }
        .btn-dl-png:hover { background: #1d4ed8; color: #fff; }

        /* ── POSTER ── */
        #poster {
            background: #fff; border-radius: 18px; overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,.10);
            max-width: 860px; margin: 0 auto;
        }

        /* ── KOP ── */
        .poster-kop {
            background: #14532d; padding: 26px 32px;
            display: flex; align-items: center; gap: 18px;
        }
        .poster-kop .logo-img {
            width: 66px; height: 66px; border-radius: 50%; flex-shrink: 0;
            border: 3px solid rgba(255,255,255,.35);
            object-fit: contain; background: #fff; padding: 4px;
        }
        .poster-kop .logo-box {
            width: 66px; height: 66px; border-radius: 50%; flex-shrink: 0;
            background: rgba(255,255,255,.15);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.8rem; color: rgba(255,255,255,.7);
        }
        .poster-kop .kop-info { flex: 1; }
        .poster-kop .kop-nama { font-size: 1.35rem; font-weight: 800; color: #fff; line-height: 1.2; }
        .poster-kop .kop-alamat { font-size: 0.83rem; color: rgba(255,255,255,.65); margin-top: 3px; }
        .poster-kop .kop-hari {
            background: rgba(255,255,255,.15); border: 1.5px solid rgba(255,255,255,.3);
            border-radius: 12px; padding: 8px 20px; text-align: center;
            flex-shrink: 0;
        }
        .poster-kop .kop-hari .kop-hari-label { font-size: 0.72rem; color: rgba(255,255,255,.6); text-transform: uppercase; letter-spacing: 1px; }
        .poster-kop .kop-hari .kop-hari-nama  { font-size: 1.4rem; font-weight: 800; color: #fff; line-height: 1.1; }
        .poster-kop .kop-hari .kop-tanggal    { font-size: 0.78rem; color: rgba(255,255,255,.7); margin-top: 2px; }

        .stripe { display: flex; height: 5px; }
        .stripe span { flex: 1; }

        /* ── BODY POSTER ── */
        .poster-body { padding: 24px 28px; }
        .poster-body .judul-hari {
            font-size: 1rem; font-weight: 700; color: #374151;
            margin-bottom: 16px; display: flex; align-items: center; gap: 8px;
        }
        .poster-body .judul-hari .dot {
            width: 12px; height: 12px; border-radius: 50%; display: inline-block;
        }

        /* ── KARTU SESI ── */
        .sesi-list { display: flex; flex-direction: column; gap: 10px; }
        .sesi-card {
            background: #f8fafc; border-radius: 12px; padding: 14px 18px;
            display: grid; grid-template-columns: 100px 1fr auto;
            align-items: center; gap: 16px;
            border: 1.5px solid #e2e8f0;
        }
        .sesi-jam { text-align: center; background: #dcfce7; border-radius: 10px; padding: 8px 6px; }
        .sesi-jam .jam-besar { font-size: 1.15rem; font-weight: 800; color: #14532d; line-height: 1.1; }
        .sesi-jam .jam-sep   { font-size: 0.68rem; color: #6b7280; margin: 1px 0; }
        .sesi-info .mapel    { font-size: 1.05rem; font-weight: 700; color: #1e293b; }
        .sesi-info .guru     { font-size: 0.86rem; color: #475569; margin-top: 3px; }
        .sesi-meta { display: flex; flex-direction: column; align-items: flex-end; gap: 5px; }
        .tag-kelas { background: #ede9fe; color: #4c1d95; border-radius: 8px; padding: 4px 12px; font-size: 0.82rem; font-weight: 700; white-space: nowrap; }
        .tag-ruang { background: #fef9c3; color: #78350f; border-radius: 8px; padding: 3px 10px; font-size: 0.78rem; white-space: nowrap; }

        .kosong-hari {
            text-align: center; padding: 40px 20px;
            color: #94a3b8; font-size: 0.95rem;
        }
        .kosong-hari i { font-size: 2.5rem; display: block; margin-bottom: 10px; }

        /* ── FOOTER POSTER ── */
        .poster-footer {
            border-top: 1px solid #e2e8f0; padding: 12px 28px;
            display: flex; justify-content: space-between; align-items: center;
            flex-wrap: wrap; gap: 8px; background: #f8fafc;
        }
        .stat-chip { background: #fff; border: 1px solid #e2e8f0; border-radius: 50px; padding: 4px 14px; font-size: 0.8rem; font-weight: 600; color: #374151; }
        .cetak-info { font-size: 0.73rem; color: #94a3b8; }

        /* ── Warna dot per hari ── */
        .dot-senin  { background: #2563eb; }
        .dot-selasa { background: #7c3aed; }
        .dot-rabu   { background: #065f46; }
        .dot-kamis  { background: #92400e; }
        .dot-jumat  { background: #991b1b; }
        .dot-sabtu  { background: #0e7490; }

        @media (max-width: 600px) {
            .sesi-card { grid-template-columns: 84px 1fr; }
            .sesi-meta { display: none; }
            #content { padding: 14px; }
            .poster-kop { flex-wrap: wrap; }
        }
        @media print {
            .no-print, #sidebar, #overlay { display: none !important; }
            body { background: #fff; }
            #content { padding: 0; }
            #poster { box-shadow: none; border-radius: 0; max-width: 100%; }
        }
    </style>
</head>
<body>
<div id="overlay"></div>
<div class="wrapper">
    @include('admin.sidebar')

    <div id="content">
        <div style="max-width: 900px; margin: 0 auto;">

            {{-- ── Breadcrumb ── --}}
            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-3 no-print">
                <div class="d-flex align-items-center gap-3">
                    <button id="sidebarCollapse" class="btn p-0"><i class="bi bi-list fs-4"></i></button>
                    <div>
                        <h5 class="mb-0 fw-bold text-success">Jadwal Mengajar</h5>
                        <p class="mb-0 text-muted" style="font-size:.8rem;">
                            Jadwal hari ini · {{ now()->translatedFormat('l, d F Y') }}
                        </p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn-dl-pdf btn" onclick="unduhPDF(event)">
                        <i class="bi bi-file-earmark-pdf me-1"></i>PDF
                    </button>
                    <button class="btn-dl-png btn" onclick="unduhPNG(event)">
                        <i class="bi bi-image me-1"></i>PNG
                    </button>
                </div>
            </div>

            {{-- ── Navigasi hari ── --}}
            <div class="hari-nav no-print">
                @foreach($validHari as $h)
                @php
                    $url = route('jadwal-mengajar.poster', array_filter([
                        'hari'         => $h,
                        'filter_guru'  => request('filter_guru'),
                        'filter_kelas' => request('filter_kelas'),
                    ]));
                    $isAktif  = $h === $hariDipilih;
                    $isHariIni = $h === $hariHariIni;
                @endphp
                <a href="{{ $url }}"
                   class="{{ $isAktif ? 'aktif' : '' }} {{ $isHariIni && !$isAktif ? 'hari-ini-badge' : '' }}">
                    {{ $h }}
                    @if($isHariIni)<span style="color:#fbbf24;font-size:0.7rem;vertical-align:super;">Hari ini</span>@endif
                </a>
                @endforeach
            </div>

            {{-- ── Filter guru & kelas ── --}}
            <div class="mb-3 no-print">
                <form method="GET" action="{{ route('jadwal-mengajar.poster') }}"
                      class="d-flex flex-wrap gap-2 align-items-end">
                    <input type="hidden" name="hari" value="{{ $hariDipilih }}">
                    <div>
                        <label class="form-label fw-semibold mb-1" style="font-size:.8rem;">Guru</label>
                        <select name="filter_guru" class="form-select form-select-sm" style="min-width:160px;">
                            <option value="">Semua Guru</option>
                            @foreach($guruList as $g)
                                <option value="{{ $g->id }}" {{ request('filter_guru')==$g->id?'selected':'' }}>
                                    {{ $g->nama_guru }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label fw-semibold mb-1" style="font-size:.8rem;">Kelas</label>
                        <select name="filter_kelas" class="form-select form-select-sm" style="min-width:130px;">
                            <option value="">Semua Kelas</option>
                            @foreach($kelasList as $k)
                                <option value="{{ $k->id }}" {{ request('filter_kelas')==$k->id?'selected':'' }}>
                                    {{ $k->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success btn-sm px-4 fw-semibold">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                    <a href="{{ route('jadwal-mengajar.poster', ['hari' => $hariDipilih]) }}"
                       class="btn btn-outline-secondary btn-sm px-3" title="Reset filter">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </form>
            </div>

            {{-- ════════════════════════
                 POSTER (yang di-capture)
            ════════════════════════ --}}
            <div id="poster">

                {{-- KOP --}}
                <div class="poster-kop">
                    @if(isset($sekolah->logo) && $sekolah->logo)
                        <img src="{{ \App\Helpers\ImageHelper::url($sekolah->logo) }}" class="logo-img" alt="Logo">
                    @else
                        <div class="logo-box"><i class="bi bi-building"></i></div>
                    @endif
                    <div class="kop-info">
                        <div class="kop-nama">{{ $sekolah->nama_sekolah ?? 'NAMA SEKOLAH' }}</div>
                        <div class="kop-alamat">
                            <i class="bi bi-geo-alt-fill me-1"></i>{{ $sekolah->alamat ?? '' }}
                        </div>
                    </div>
                    <div class="kop-hari">
                        <div class="kop-hari-label">Jadwal</div>
                        <div class="kop-hari-nama">{{ $hariDipilih }}</div>
                        <div class="kop-tanggal">{{ now()->translatedFormat('d F Y') }}</div>
                    </div>
                </div>

                {{-- Stripe --}}
                <div class="stripe">
                    @foreach(['#22c55e','#16a34a','#15803d','#166534','#14532d'] as $c)
                        <span style="background:{{ $c }}"></span>
                    @endforeach
                </div>

                {{-- Daftar sesi --}}
                <div class="poster-body">
                    @php
                        $dotClass = [
                            'Senin'=>'dot-senin','Selasa'=>'dot-selasa','Rabu'=>'dot-rabu',
                            'Kamis'=>'dot-kamis','Jumat'=>'dot-jumat','Sabtu'=>'dot-sabtu',
                        ];
                    @endphp
                    <div class="judul-hari">
                        <span class="dot {{ $dotClass[$hariDipilih] ?? 'dot-senin' }}"></span>
                        Jadwal Pelajaran Hari {{ $hariDipilih }}
                        <span class="text-muted fw-normal" style="font-size:.82rem;">
                            ({{ $jadwalHariIni->count() }} sesi)
                        </span>
                    </div>

                    @if($jadwalHariIni->count() > 0)
                    <div class="sesi-list">
                        @foreach($jadwalHariIni as $j)
                        <div class="sesi-card">
                            <div class="sesi-jam">
                                <div class="jam-besar">{{ substr($j->jam_mulai,0,5) }}</div>
                                <div class="jam-sep">s/d</div>
                                <div class="jam-besar">{{ substr($j->jam_selesai,0,5) }}</div>
                            </div>
                            <div class="sesi-info">
                                <div class="mapel">{{ $j->mapel->nama_mapel ?? '-' }}</div>
                                <div class="guru">
                                    <i class="bi bi-person-fill me-1"></i>{{ $j->guru->nama_guru ?? '-' }}
                                </div>
                            </div>
                            <div class="sesi-meta">
                                <span class="tag-kelas">{{ $j->kelas->nama_kelas ?? '-' }}</span>
                                @if($j->ruangan)
                                    <span class="tag-ruang">
                                        <i class="bi bi-door-open me-1"></i>{{ $j->ruangan }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="kosong-hari">
                        <i class="bi bi-calendar-x"></i>
                        Tidak ada jadwal pelajaran pada hari <strong>{{ $hariDipilih }}</strong>
                    </div>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="poster-footer">
                    <div class="d-flex flex-wrap gap-2">
                        <span class="stat-chip">
                            <i class="bi bi-list-check me-1 text-success"></i>
                            {{ $jadwalHariIni->count() }} Sesi
                        </span>
                        <span class="stat-chip">
                            <i class="bi bi-person-badge me-1 text-primary"></i>
                            {{ $jadwalHariIni->pluck('id_guru')->unique()->count() }} Guru
                        </span>
                        <span class="stat-chip">
                            <i class="bi bi-mortarboard me-1 text-warning"></i>
                            {{ $jadwalHariIni->pluck('id_kelas')->unique()->count() }} Kelas
                        </span>
                    </div>
                    <div class="cetak-info">
                        Dicetak {{ now()->translatedFormat('d F Y, H:i') }} WIB
                    </div>
                </div>

            </div>{{-- end #poster --}}
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
    $('#sidebarCollapse, #close-sidebar, #overlay').on('click', function () {
        if ($(window).width() <= 768) {
            $('#sidebar').toggleClass('show-mobile');
            $('#overlay').toggleClass('active');
        } else {
            $('#sidebar').toggleClass('inactive');
        }
    });

    async function captureCanvas() {
        return await html2canvas(document.getElementById('poster'), {
            scale: 2, useCORS: true, backgroundColor: '#ffffff', logging: false
        });
    }

    async function unduhPNG(e) {
        const btn = e.currentTarget;
        const ori = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        try {
            const canvas = await captureCanvas();
            const a = document.createElement('a');
            a.download = 'jadwal-{{ $hariDipilih }}.png';
            a.href = canvas.toDataURL('image/png');
            a.click();
        } catch(err) { alert('Gagal membuat PNG.'); }
        btn.disabled = false; btn.innerHTML = ori;
    }

    async function unduhPDF(e) {
        const btn = e.currentTarget;
        const ori = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        try {
            const { jsPDF } = window.jspdf;
            const canvas = await captureCanvas();
            // Dimensi poster
            const iW = canvas.width, iH = canvas.height;
            // Selalu portrait A4 (lebar = 210mm), tinggi menyesuaikan rasio
            const pdf  = new jsPDF('p', 'mm', 'a4');
            const pW   = 210;
            const pH   = (iH / iW) * pW;  // tinggi proporsional
            const a4H  = 297;

            if (pH <= a4H) {
                // Muat dalam 1 halaman, rata tengah vertikal
                const marginY = (a4H - pH) / 2;
                pdf.addImage(canvas.toDataURL('image/png'), 'PNG', 0, marginY, pW, pH);
            } else {
                // Lebih dari 1 halaman — potong per halaman
                let y = 0;
                const slicePx = (a4H / pW) * iW;
                while (y < iH) {
                    if (y > 0) pdf.addPage();
                    const tmp = document.createElement('canvas');
                    tmp.width  = iW;
                    tmp.height = Math.min(slicePx, iH - y);
                    tmp.getContext('2d').drawImage(canvas, 0, y, iW, tmp.height, 0, 0, iW, tmp.height);
                    const sliceH = (tmp.height / iW) * pW;
                    pdf.addImage(tmp.toDataURL('image/png'), 'PNG', 0, 0, pW, sliceH);
                    y += slicePx;
                }
            }
            pdf.save('jadwal-{{ $hariDipilih }}.pdf');
        } catch(err) { alert('Gagal membuat PDF.'); }
        btn.disabled = false; btn.innerHTML = ori;
    }
</script>
</body>
</html>
