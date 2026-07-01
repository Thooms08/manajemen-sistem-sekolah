<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Catatan — {{ $pemilik->username }}</title>
    @if(isset($sekolah->logo))
    <link rel="icon" type="image/png" href="{{ \App\Helpers\ImageHelper::url($sekolah->logo) }}">
    @else
    <link rel="icon" type="image/png" href="{{ asset('assets/img/default-favicon.png') }}">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --green-primary: #198754;
            --green-dark:    #0f5132;
            --green-light:   #f0faf5;
            --border:        #e2ebe6;
            --surface:       #ffffff;
            --text-main:     #1a2e25;
            --text-muted:    #6c8f7d;
            --shadow-sm:     0 2px 8px rgba(25,135,84,.08);
            --shadow-md:     0 6px 24px rgba(25,135,84,.12);
            --radius:        14px;
        }
        body { font-family: 'Inter', sans-serif; background: #f3f7f5; color: var(--text-main); }
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        #content { width: 100%; padding: 15px 15px; min-height: 100vh; transition: all 0.3s; }
        #sidebarCollapse {
            width: 42px; height: 42px;
            background: var(--green-primary); border: none; color: #fff;
            border-radius: 10px; display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        #sidebarCollapse:hover { background: var(--green-dark); }
        #overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 1040; }
        #overlay.active { display: block; }

        /* ── Owner Banner ── */
        .owner-banner {
            background: linear-gradient(135deg, #0f5132, #198754);
            border-radius: var(--radius);
            padding: 16px 18px;
            color: #fff;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .owner-avatar {
            width: 56px; height: 56px; border-radius: 14px;
            background: rgba(255,255,255,.15);
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 1.5rem; flex-shrink: 0;
            border: 2px solid rgba(255,255,255,.3);
        }
        .owner-role-badge {
            background: rgba(255,255,255,.2);
            border: 1px solid rgba(255,255,255,.3);
            border-radius: 50px;
            padding: 3px 12px; font-size: .75rem; font-weight: 600;
        }

        /* ── Catatan Card ── */
        .catatan-card {
            background: var(--surface);
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            padding: 20px 22px;
            transition: box-shadow .2s, border-color .2s;
            height: 100%;
        }
        .catatan-card:hover { box-shadow: var(--shadow-sm); border-color: #b5d5c5; }
        .label-badge {
            display: inline-flex; align-items: center; gap: 5px;
            background: var(--green-light); color: var(--green-primary);
            font-size: .75rem; font-weight: 700;
            padding: 4px 12px; border-radius: 50px;
            border: 1px solid #c3e6cb;
        }
        .catatan-isi {
            font-size: .88rem; color: #444; line-height: 1.65;
            white-space: pre-wrap; word-break: break-word;
        }
        .catatan-time {
            font-size: .73rem; color: var(--text-muted);
        }
        .btn-aksi-catatan {
            background: transparent; border: none; padding: 4px 8px;
            border-radius: 6px; cursor: pointer; transition: background .15s;
            line-height: 1;
        }
        .btn-aksi-catatan:hover { background: var(--green-light); }

        /* ── Filter & Search ── */
        .filter-bar {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: 10px; padding: 14px 16px;
        }
        .catatan-card-footer {
            display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;
        }
        .empty-state {
            text-align: center; padding: 60px 20px; color: var(--text-muted);
        }
        .empty-state i { font-size: 3.5rem; opacity: .35; margin-bottom: 12px; display: block; }

        .form-control:focus {
            border-color: var(--green-primary) !important;
            box-shadow: 0 0 0 .2rem rgba(25,135,84,.2) !important;
        }

        @media (min-width: 768px) {
            #content { padding: 24px 30px; }
            .owner-banner {
                padding: 22px 28px;
                flex-direction: row;
                align-items: center;
                gap: 18px;
            }
            .catatan-card { padding: 20px 22px; }
            .filter-bar { padding: 14px 20px; }
        }

        @media (max-width: 576px) {
            .top-bar { flex-wrap: wrap; align-items: flex-start !important; }
            .filter-bar .filter-btn { width: 100%; }
            .input-group { max-width: 100% !important; }
            .catatan-card-footer {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
<div id="overlay"></div>
<div class="wrapper">
    @include('user.sidebar')

    <div id="content">
        <div class="container-fluid px-0">

            {{-- ── TOP BAR ── --}}
            <div class="top-bar d-flex align-items-center gap-3 mb-4 mt-1">
                <button type="button" id="sidebarCollapse" class="btn">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <div>
                    <a href="{{ route('catatan.index') }}" class="text-muted text-decoration-none small">
                        <i class="bi bi-arrow-left-circle me-1"></i>Kembali ke Semua Catatan
                    </a>
                    <h5 class="mb-0 fw-bold mt-1">
                        <i class="bi bi-journal-text text-success me-2"></i>
                        Catatan dari <span class="text-success">{{ $pemilik->username }}</span>
                    </h5>
                </div>
            </div>

            {{-- ── ALERT ── --}}
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            {{-- ── OWNER BANNER ── --}}
            <div class="owner-banner mb-4 align-items-center align-items-md-start text-center text-sm-start">
                <div class="owner-avatar">{{ strtoupper(substr($pemilik->username, 0, 1)) }}</div>
                <div class="w-100">
                    <div class="fw-bold fs-5">{{ $pemilik->username }}</div>
                    <div class="d-flex align-items-center justify-content-center justify-content-sm-start gap-2 mt-1 flex-wrap">
                        <span class="owner-role-badge">
                            <i class="bi bi-shield-check me-1"></i>{{ $pemilik->role ?? $pemilik->rules ?? 'user' }}
                        </span>
                        <span class="owner-role-badge">
                            <i class="bi bi-journal-text me-1"></i>{{ $catatans->count() }} catatan
                        </span>
                    </div>
                </div>
            </div>

            {{-- ── FILTER & SEARCH ── --}}
            <div class="filter-bar mb-4 d-flex flex-column flex-md-row gap-3 justify-content-between align-items-stretch align-items-md-center">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="text-muted small fw-semibold me-1"><i class="bi bi-filter me-1"></i>Filter:</span>
                    <button class="btn btn-sm btn-success px-3 filter-btn active" data-filter="semua">Semua</button>
                    @foreach($catatans->pluck('label')->unique() as $lbl)
                    <button class="btn btn-sm btn-outline-success px-3 filter-btn" data-filter="{{ $lbl }}">
                        {{ $lbl }}
                    </button>
                    @endforeach
                </div>

                <div class="input-group shrink-0" style="max-width: 320px; width: 100%;">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="searchCatatan" class="form-control border-start-0 ps-0"
                        placeholder="Cari isi catatan...">
                </div>
            </div>

            {{-- ── CATATAN GRID ── --}}
            @if($catatans->count() > 0)
            <div class="row g-3" id="catatanContainer">
                @foreach($catatans as $c)
                <div class="col-md-6 col-lg-4 catatan-item" data-label="{{ $c->label }}"
                    data-isi="{{ strtolower($c->catatan) }}">
                    <div class="catatan-card">
                        {{-- Header --}}
                        <div class="d-flex align-items-start justify-content-between mb-2 gap-2">
                            <span class="label-badge">
                                <i class="bi bi-tag-fill"></i>{{ $c->label }}
                            </span>
                            {{-- Admin bisa hapus catatan siapapun --}}
                            <form action="{{ route('catatan.destroy', $c->uuid) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Hapus catatan ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-aksi-catatan text-danger" title="Hapus catatan ini">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>

                        {{-- Isi catatan --}}
                        <p class="catatan-isi mb-3">{{ $c->catatan }}</p>

                        {{-- Footer --}}
                        <div class="catatan-card-footer">
                            <div class="catatan-time">
                                <i class="bi bi-clock me-1"></i>
                                {{ $c->created_at->isoFormat('D MMM YYYY, HH:mm') }}
                                @if($c->updated_at->ne($c->created_at))
                                    <span class="ms-2 text-warning" title="Sudah diedit">
                                        <i class="bi bi-pencil-fill"></i> diedit
                                    </span>
                                @endif
                            </div>
                            {{-- Label milik user --}}
                            <span class="badge bg-light text-muted border" style="font-size:.7rem;">
                                <i class="bi bi-person me-1"></i>{{ $pemilik->username }}
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Tidak ada hasil filter --}}
            <div class="empty-state" id="emptyFilter" style="display:none;">
                <i class="bi bi-search"></i>
                <div class="fw-bold mb-1">Tidak ditemukan</div>
                <small>Tidak ada catatan yang cocok dengan filter atau pencarian.</small>
            </div>

            @else
            <div class="empty-state">
                <i class="bi bi-journal-x"></i>
                <div class="fw-bold mb-1">Belum ada catatan</div>
                <small>Pengguna ini belum membuat catatan apapun.</small>
            </div>
            @endif

        </div>{{-- end container --}}
    </div>{{-- end content --}}
</div>{{-- end wrapper --}}


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Sidebar Toggle ─────────────────────────────────────────────
    const sidebar     = document.getElementById('sidebar');
    const collapseBtn = document.getElementById('sidebarCollapse');
    const closeBtn    = document.getElementById('close-sidebar');
    const overlay     = document.getElementById('overlay');

    function toggleSidebar() {
        if (window.innerWidth <= 768) {
            sidebar.classList.toggle('show-mobile');
            overlay.classList.toggle('active');
        } else {
            sidebar.classList.toggle('inactive');
        }
    }
    if (collapseBtn) collapseBtn.addEventListener('click', toggleSidebar);
    if (closeBtn)    closeBtn.addEventListener('click', toggleSidebar);
    if (overlay)     overlay.addEventListener('click', toggleSidebar);

    // ── Filter Label ───────────────────────────────────────────────
    let activeFilter = 'semua';

    document.querySelectorAll('.filter-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.filter-btn').forEach(b => {
                b.classList.remove('btn-success', 'active');
                b.classList.add('btn-outline-success');
            });
            this.classList.add('btn-success', 'active');
            this.classList.remove('btn-outline-success');

            activeFilter = this.dataset.filter;
            applyFilters();
        });
    });

    // ── Search isi catatan ─────────────────────────────────────────
    const searchInput = document.getElementById('searchCatatan');
    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
    }

    function applyFilters() {
        const keyword = searchInput ? searchInput.value.toLowerCase() : '';
        let visible = 0;

        document.querySelectorAll('.catatan-item').forEach(function (item) {
            const labelMatch  = activeFilter === 'semua' || item.dataset.label === activeFilter;
            const searchMatch = !keyword || item.dataset.isi.includes(keyword);

            if (labelMatch && searchMatch) {
                item.style.display = '';
                visible++;
            } else {
                item.style.display = 'none';
            }
        });

        const emptyFilter = document.getElementById('emptyFilter');
        const container   = document.getElementById('catatanContainer');
        if (emptyFilter && container) {
            if (visible === 0) {
                emptyFilter.style.display = '';
                container.style.display = 'none';
            } else {
                emptyFilter.style.display = 'none';
                container.style.display = '';
            }
        }
    }
});
</script>
</body>
</html>
