<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    @if(isset($sekolah->logo))
    <link rel="icon" type="image/png" href="{{ asset($sekolah->logo) }}">
    @else
    <link rel="icon" type="image/png" href="{{ asset('assets/img/default-favicon.png') }}">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f6;
            overflow-x: hidden;
        }

        .wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
        }

        #content {
            width: 100%;
            padding: 20px 30px;
            transition: all 0.3s;
        }

        /* Overlay Gelap saat Mobile Sidebar Terbuka */
        #overlay {
            display: none;
            position: fixed;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1040;
            top: 0;
            left: 0;
        }

        #overlay.active {
            display: block;
        }

        #sidebarCollapse {
            width: 45px;
            height: 45px;
            background: #198754;
            border: none;
            color: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(25, 135, 84, 0.2);
        }

        .welcome-card {
            background: linear-gradient(135deg, #198754 0%, #142d2d 100%);
            color: white;
            border: none;
            border-radius: 15px;
        }

        .clock-box {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(5px);
            border-radius: 10px;
            padding: 10px 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        @media (max-width: 768px) {
            #content {
                padding: 15px;
            }
        }
    </style>
</head>
<body>

    <div id="overlay"></div>

    <div class="wrapper">
        @include('dashboard_admin.sidebar_admin')

        <div id="content">
            <div class="container-fluid">
                
                <div class="d-flex align-items-center mb-4 mt-2">
                    <button type="button" id="sidebarCollapse" class="btn">
                        <i class="bi bi-list fs-4"></i>
                    </button>
                    <h5 class="ms-3 mb-0 fw-bold">Ringkasan Panel</h5>
                </div>

                <div class="card welcome-card p-4 mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="fw-bold mb-2">Hallo Admin, Selamat Datang</h2>
                            <p class="mb-0 opacity-75">Sistem Manajemen Sekolah Real-time.</p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <div class="clock-box text-white">
                                <i class="bi bi-clock-fill me-2"></i>
                                <span id="realtime-clock">Memuat...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const sidebar = document.getElementById('sidebar');
            const collapseBtn = document.getElementById('sidebarCollapse');
            const closeBtn = document.getElementById('close-sidebar');
            const overlay = document.getElementById('overlay');

            // Fungsi Buka / Tutup
            function toggleSidebar() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.toggle('show-mobile');
                    overlay.classList.toggle('active');
                } else {
                    sidebar.classList.toggle('inactive');
                }
            }

            collapseBtn.addEventListener('click', toggleSidebar);
            closeBtn.addEventListener('click', toggleSidebar);
            overlay.addEventListener('click', toggleSidebar);
        });

        // Jam Real-time
        function updateClock() {
            const now = new Date();
            const dateStr = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
            const timeStr = now.toLocaleTimeString('id-ID', { hour12: false });
            document.getElementById('realtime-clock').innerText = `${dateStr} – ${timeStr}`;
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>
</html>