<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak — 403</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --green-primary: #198754;
            --green-dark:    #0f5132;
            --green-light:   #f0faf5;
            --border:        #e2ebe6;
            --text-main:     #1a2e25;
            --text-muted:    #6c8f7d;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f0faf5 0%, #e8f5e9 50%, #f3f7f5 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .error-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 8px 40px rgba(25,135,84,.14);
            padding: 48px 40px;
            max-width: 520px;
            width: 100%;
            text-align: center;
            border: 1px solid var(--border);
            position: relative;
            overflow: hidden;
        }

        /* Strip hijau di atas card */
        .error-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--green-dark), var(--green-primary), #52b788);
            border-radius: 20px 20px 0 0;
        }

        .error-icon-wrap {
            width: 90px; height: 90px;
            border-radius: 50%;
            background: #fff3f3;
            border: 3px solid #ffcdd2;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 24px;
            position: relative;
        }
        .error-icon-wrap i {
            font-size: 2.6rem;
            color: #e53935;
        }

        .error-code {
            font-size: 4rem;
            font-weight: 800;
            line-height: 1;
            background: linear-gradient(135deg, var(--green-dark), var(--green-primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
            letter-spacing: -2px;
        }

        .error-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 12px;
        }

        .error-message {
            font-size: 0.9rem;
            color: var(--text-muted);
            line-height: 1.7;
            margin-bottom: 32px;
            padding: 0 8px;
        }

        .error-message strong {
            color: #e53935;
            font-weight: 600;
        }

        .divider {
            border: none;
            border-top: 1px solid var(--border);
            margin: 0 0 28px;
        }

        .btn-back {
            background: linear-gradient(135deg, var(--green-dark), var(--green-primary));
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 12px 28px;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: opacity .2s, transform .15s;
            box-shadow: 0 4px 14px rgba(25,135,84,.3);
        }
        .btn-back:hover {
            opacity: .9;
            transform: translateY(-1px);
            color: #fff;
        }

        .btn-dashboard {
            background: transparent;
            color: var(--green-primary);
            border: 1.5px solid var(--green-primary);
            border-radius: 12px;
            padding: 11px 24px;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background .2s, color .2s;
        }
        .btn-dashboard:hover {
            background: var(--green-light);
            color: var(--green-dark);
        }

        .action-row {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .badge-403 {
            display: inline-block;
            background: #fff3e0;
            color: #e65100;
            border: 1px solid #ffcc80;
            border-radius: 50px;
            padding: 4px 16px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        @media (max-width: 480px) {
            .error-card { padding: 36px 20px; }
            .error-code { font-size: 3rem; }
            .error-title { font-size: 1.1rem; }
            .action-row { flex-direction: column; }
            .btn-back, .btn-dashboard { width: 100%; justify-content: center; }
        }
    </style>
</head>
<body>
    <div class="error-card">

        <div class="error-icon-wrap">
            <i class="bi bi-shield-lock-fill"></i>
        </div>

        <div class="badge-403">Error 403</div>

        <div class="error-code">403</div>

        <h1 class="error-title">Akses Ditolak</h1>

        <p class="error-message">
            @if(!empty($exception) && $exception->getMessage())
                {{ $exception->getMessage() }}
            @else
                Anda <strong>tidak memiliki izin</strong> untuk mengakses halaman atau melakukan
                aksi ini. Silakan hubungi Administrator jika Anda merasa ini keliru.
            @endif
        </p>

        <hr class="divider">

        <div class="action-row">
            <a href="javascript:history.back()" class="btn-back">
                <i class="bi bi-arrow-left-circle"></i>
                Kembali
            </a>
            @auth
                @if(auth()->user()->role === 'admin' || (auth()->user()->rules ?? '') === 'admin')
                    <a href="{{ route('admin.home') }}" class="btn-dashboard">
                        <i class="bi bi-speedometer2"></i>
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('user.dashboard') }}" class="btn-dashboard">
                        <i class="bi bi-house-door"></i>
                        Beranda
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" class="btn-dashboard">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Login
                </a>
            @endauth
        </div>

    </div>
</body>
</html>
