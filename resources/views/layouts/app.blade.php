<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Leave Management') – LMS</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary:     #2563eb;
            --primary-dk:  #1d4ed8;
            --sidebar-bg:  #0f172a;
            --sidebar-txt: #94a3b8;
            --sidebar-act: #ffffff;
            --sidebar-w:   260px;
            --topbar-h:    60px;
        }

        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            overflow-x: hidden;
        }

        /* ── Sidebar ── */
        #sidebar {
            position: fixed; top: 0; left: 0; height: 100vh;
            width: var(--sidebar-w); background: var(--sidebar-bg);
            z-index: 1040; display: flex; flex-direction: column;
            transition: transform .3s ease;
        }
        #sidebar .sidebar-brand {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #1e293b;
            display: flex; align-items: center; gap: .75rem;
        }
        #sidebar .sidebar-brand .logo-icon {
            width: 36px; height: 36px; background: var(--primary);
            border-radius: 8px; display: flex; align-items: center;
            justify-content: center; font-size: 1.1rem; color: #fff;
        }
        #sidebar .sidebar-brand .brand-text {
            font-size: .9rem; font-weight: 700; color: #fff; line-height: 1.2;
        }
        #sidebar .brand-text small { font-weight: 400; color: var(--sidebar-txt); font-size: .72rem; }

        #sidebar nav { flex: 1; padding: 1rem 0; overflow-y: auto; }
        #sidebar .nav-section {
            font-size: .65rem; font-weight: 600; color: #475569;
            text-transform: uppercase; letter-spacing: .08em;
            padding: .75rem 1.5rem .25rem;
        }
        #sidebar .nav-link {
            display: flex; align-items: center; gap: .75rem;
            padding: .55rem 1.5rem; color: var(--sidebar-txt);
            font-size: .855rem; font-weight: 500;
            border-radius: 0; transition: all .2s; border-left: 3px solid transparent;
            text-decoration: none;
        }
        #sidebar .nav-link i { font-size: 1rem; width: 20px; text-align: center; }
        #sidebar .nav-link:hover { color: #fff; background: rgba(255,255,255,.05); }
        #sidebar .nav-link.active {
            color: #fff; background: rgba(37,99,235,.2);
            border-left-color: var(--primary);
        }
        #sidebar .sidebar-footer {
            padding: 1rem 1.5rem; border-top: 1px solid #1e293b;
        }
        #sidebar .user-card {
            display: flex; align-items: center; gap: .75rem;
        }
        #sidebar .user-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: var(--primary); display: flex; align-items: center;
            justify-content: center; color: #fff; font-weight: 700; font-size: .85rem;
            flex-shrink: 0;
        }
        #sidebar .user-info { min-width: 0; }
        #sidebar .user-name { font-size: .82rem; font-weight: 600; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        #sidebar .user-role { font-size: .7rem; color: var(--sidebar-txt); text-transform: capitalize; }

        /* ── Topbar ── */
        #topbar {
            position: fixed; top: 0; left: var(--sidebar-w); right: 0;
            height: var(--topbar-h); background: #fff; z-index: 1030;
            display: flex; align-items: center; padding: 0 1.5rem;
            border-bottom: 1px solid #e2e8f0; gap: 1rem;
            transition: left .3s ease;
        }
        #topbar .page-title { font-size: 1rem; font-weight: 600; color: #1e293b; margin: 0; }
        #topbar .ms-auto { display: flex; align-items: center; gap: .75rem; }

        /* ── Main content ── */
        #main-content {
            margin-left: var(--sidebar-w);
            padding-top: var(--topbar-h);
            min-height: 100vh;
            transition: margin-left .3s ease;
        }
        .content-wrapper { padding: 1.75rem; }

        /* ── Desktop collapsed ── */
        body.sidebar-collapsed #sidebar { transform: translateX(-100%); }
        body.sidebar-collapsed #topbar  { left: 0; }
        body.sidebar-collapsed #main-content { margin-left: 0; }

        /* ── Overlay (mobile) ── */
        #sidebar-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,.45); z-index: 1039;
        }
        #sidebar-overlay.show { display: block; }

        /* ── Cards ── */
        .card { border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,.06); }
        .card-header { background: #fff; border-bottom: 1px solid #e2e8f0; padding: 1rem 1.25rem; border-radius: 12px 12px 0 0 !important; }
        .card-header h5, .card-header h6 { margin: 0; font-weight: 600; }

        /* ── Stat cards ── */
        .stat-card { border-radius: 12px; padding: 1.25rem; color: #fff; }
        .stat-card .stat-icon { font-size: 2rem; opacity: .8; }
        .stat-card .stat-value { font-size: 2rem; font-weight: 700; line-height: 1; }
        .stat-card .stat-label { font-size: .8rem; opacity: .85; margin-top: .25rem; }

        /* ── Tables ── */
        .table th { font-size: .78rem; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; color: #64748b; }
        .table td { font-size: .875rem; vertical-align: middle; }
        .table-hover tbody tr:hover { background: #f8fafc; }

        /* ── Badges ── */
        .badge { font-size: .72rem; padding: .35em .65em; }

        /* ── Forms ── */
        .form-label { font-size: .85rem; font-weight: 500; color: #374151; }
        .form-control, .form-select { font-size: .875rem; border-color: #d1d5db; border-radius: 8px; }
        .form-control:focus, .form-select:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37,99,235,.1); }

        /* ── Buttons ── */
        .btn { font-size: .855rem; font-weight: 500; border-radius: 8px; padding: .45rem .9rem; }
        .btn-primary { background: var(--primary); border-color: var(--primary); }
        .btn-primary:hover { background: var(--primary-dk); border-color: var(--primary-dk); }

        /* ── Alerts ── */
        .alert { border-radius: 10px; font-size: .875rem; }

        /* ── Progress bars ── */
        .progress { height: 8px; border-radius: 4px; }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.open { transform: translateX(0); }
            #topbar { left: 0; }
            #main-content { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- ── Overlay (mobile) ── --}}
<div id="sidebar-overlay"></div>

{{-- ── Sidebar ── --}}
<div id="sidebar">
    <div class="sidebar-brand">
        <div class="logo-icon"><i class="bi bi-calendar-check"></i></div>
        <div class="brand-text">
            PT Jaya Abadi<br>
            <small>System v1.0</small>
        </div>
    </div>

    <nav>
        @include('layouts.partials.sidebar-menu')
    </nav>

    <div class="sidebar-footer">
        <div class="user-card">
            <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</div>
            <div class="user-info">
                <div class="user-name">{{ Auth::user()->name }}</div>
                <div class="user-role">{{ ucfirst(Auth::user()->role) }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="mt-2">
            @csrf
            <button type="submit" class="btn btn-sm w-100 text-start text-danger px-0" style="background:none;border:none;">
                <i class="bi bi-box-arrow-right me-2"></i>Logout
            </button>
        </form>
    </div>
</div>

{{-- ── Topbar ── --}}
<div id="topbar">
    <button class="btn btn-sm" id="sidebarToggle" style="color:#64748b;">
        <i class="bi bi-list fs-5"></i>
    </button>
    <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
    <div class="ms-auto">
        <span class="badge bg-light text-dark border">
            <i class="bi bi-calendar3 me-1"></i>{{ now()->isoFormat('D MMMM YYYY') }}
        </span>
    </div>
</div>

{{-- ── Main Content ── --}}
<div id="main-content">
    <div class="content-wrapper">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-x-circle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const sidebar  = document.getElementById('sidebar');
    const overlay  = document.getElementById('sidebar-overlay');
    const isMobile = () => window.innerWidth <= 768;

    document.getElementById('sidebarToggle').addEventListener('click', () => {
        if (isMobile()) {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('show');
        } else {
            document.body.classList.toggle('sidebar-collapsed');
        }
    });

    overlay.addEventListener('click', () => {
        sidebar.classList.remove('open');
        overlay.classList.remove('show');
    });

    // Tutup sidebar mobile saat resize ke desktop
    window.addEventListener('resize', () => {
        if (!isMobile()) {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
        }
    });
</script>
@stack('scripts')
</body>
</html>