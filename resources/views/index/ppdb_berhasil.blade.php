<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Berhasil</title>
    @if(isset($sekolah->logo))
    <link rel="icon" type="image/png" href="{{ asset($sekolah->logo) }}">
    @else
    <link rel="icon" type="image/png" href="{{ asset('assets/img/default-favicon.png') }}">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .success-card { max-width: 500px; text-align: center; background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .icon-box { font-size: 80px; color: #198754; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="success-card">
        <div class="icon-box">
            <i class="bi bi-check-circle-fill"></i>
        </div>
        <h2 class="fw-bold text-success">Pendaftaran Berhasil!</h2>
        <p class="text-muted mt-3">
            Data calon siswa telah kami terima dan saat ini dalam status <strong>PENDING</strong>.
        </p>
        <p class="text-muted">
            Mohon menunggu informasi selanjutnya. Admin akan melakukan konfirmasi data Anda segera.
        </p>
        <div class="mt-4">
            <a href="/" class="btn btn-outline-success px-4 rounded-pill">Kembali ke Beranda</a>
        </div>
    </div>
</body>
</html>