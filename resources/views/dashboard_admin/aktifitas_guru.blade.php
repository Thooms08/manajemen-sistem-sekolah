<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivitas Guru</title>
    @if(isset($sekolah->logo))
    <link rel="icon" type="image/png" href="{{ asset($sekolah->logo) }}">
    @else
    <link rel="icon" type="image/png" href="{{ asset('assets/img/default-favicon.png') }}">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root { --admin-green: #198754; --soft-bg: #f4f7f6; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--soft-bg); }
        .wrapper { display: flex; }
        #content { width: 100%; padding: 30px; transition: all 0.3s; }
        .card { border: none; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.03); }
        .btn-filter { border-radius: 10px; font-weight: 600; padding: 10px 20px; transition: 0.3s; }
        .btn-filter.active { background-color: var(--admin-green) !important; color: white !important; }
        #sidebarCollapse { width: 45px; height: 45px; background: var(--admin-green); border: none; color: white; border-radius: 12px; }
        .chart-container { position: relative; height: 60vh; width: 100%; }
        @media (max-width: 768px) { .chart-container { height: 400px; } }
    </style>
</head>
<body>

<div class="wrapper">
    @include('dashboard_admin.sidebar_admin')

    <div id="content">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center">
                    <button type="button" id="sidebarCollapse"><i class="bi bi-list fs-4"></i></button>
                    <div class="ms-3">
                        <h4 class="mb-0 fw-bold">Monitoring Aktivitas Guru</h4>
                        <p class="text-muted small mb-0">Statistik persentase rekapitulasi guru harian</p>
                    </div>
                </div>
            </div>

            <div class="card p-3 mb-4">
                <div class="d-flex flex-wrap gap-2">
                    <button class="btn btn-light btn-filter border" onclick="updateChart('1week', this)">1 Minggu</button>
                    <button class="btn btn-light btn-filter border active" onclick="updateChart('1month', this)">1 Bulan</button>
                    <button class="btn btn-light btn-filter border" onclick="updateChart('1year', this)">1 Tahun</button>
                </div>
            </div>

            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-bar-chart-fill me-2 text-success"></i>Grafik Keaktifan Guru</h5>
                    <div class="small text-muted" id="rangeLabel">Range: 30 Hari Terakhir</div>
                </div>
                <div class="chart-container">
                    <canvas id="teacherChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
 <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let teacherChart;
    const ctx = document.getElementById('teacherChart').getContext('2d');

    // Fungsi Inisialisasi Chart
    function initChart(data) {
        teacherChart = new Chart(ctx, {
            type: 'bar',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: { callback: (value) => value + '%' }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (context) => `Keaktifan: ${context.raw}%`
                        }
                    }
                }
            },
            plugins: [{
                id: 'valueLabel',
                afterDraw: (chart) => {
                    const ctx = chart.ctx;
                    chart.data.datasets.forEach((dataset, i) => {
                        const meta = chart.getDatasetMeta(i);
                        meta.data.forEach((bar, index) => {
                            const data = dataset.data[index];
                            ctx.fillStyle = '#000';
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'bottom';
                            ctx.font = 'bold 12px Arial';
                            ctx.fillText(data + '%', bar.x, bar.y - 5);
                        });
                    });
                }
            }]
        });
    }

    // Fungsi Update Chart via AJAX
    function updateChart(range, btn) {
        // Update UI Button
        document.querySelectorAll('.btn-filter').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        // Update Label
        const labels = {'1week': '7 Hari Terakhir', '1month': '30 Hari Terakhir', '1year': '1 Tahun Terakhir'};
        document.getElementById('rangeLabel').innerText = `Range: ${labels[range]}`;

        fetch(`{{ route('admin.aktifitas.data') }}?range=${range}`)
            .then(res => res.json())
            .then(data => {
                if (teacherChart) {
                    teacherChart.data = data;
                    teacherChart.update();
                } else {
                    initChart(data);
                }
            });
    }

    // Load Default (1 Bulan)
    document.addEventListener('DOMContentLoaded', () => {
        const defaultBtn = document.querySelector('.btn-filter.active');
        updateChart('1month', defaultBtn);
    });

    // Sidebar Hamburger Menu Logic
    document.getElementById('sidebarCollapse').onclick = () => {
        document.getElementById('sidebar').classList.toggle('inactive');
    };
</script>
</body>
</html>