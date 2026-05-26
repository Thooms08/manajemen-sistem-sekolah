<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PPDB Ditutup</title>
    @if(isset($sekolah->logo))
    <link rel="icon" type="image/png" href="{{ asset($sekolah->logo) }}">
    @else
    <link rel="icon" type="image/png" href="{{ asset('assets/img/default-favicon.png') }}">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { 
            background-color: #f8f9fa; 
            height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-family: 'Inter', sans-serif;
        }
        .close-card { 
            max-width: 500px; 
            background: white; 
            padding: 40px; 
            border-radius: 20px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.1); 
            text-align: center;
            border-top: 8px solid #dc3545; /* Warna merah indikator tutup */
        }
        .icon-box { 
            font-size: 60px; 
            color: #dc3545; 
            margin-bottom: 20px; 
        }
        .btn-home {
            background-color: #198754;
            color: white;
            border-radius: 50px;
            padding: 10px 30px;
            font-weight: bold;
            transition: 0.3s;
        }
        .btn-home:hover {
            background-color: #146c43;
            color: white;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

    <div class="close-card">
        <div class="icon-box">
            <i class="bi bi-door-closed"></i>
        </div>
        <h3 class="fw-bold text-dark">Pendaftaran Ditutup</h3>
        <p class="text-muted mt-3">
            Mohon maaf, saat ini pendaftaran siswa baru (PPDB) sedang **tidak aktif** atau sudah berakhir.
        </p>
        <p class="small text-secondary mb-4">
            Silakan hubungi pihak administrasi sekolah untuk informasi lebih lanjut mengenai jadwal pendaftaran berikutnya.
        </p>
        <hr>
        <a href="/" class="btn btn-home shadow-sm">
            <i class="bi bi-house-door me-2"></i> Kembali ke Beranda
        </a>
    </div>

</body>
</html>