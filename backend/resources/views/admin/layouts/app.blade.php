<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') — DIMVES</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-sidebar: #0f172a;
            --bg-sidebar-hover: #1e293b;
            --bg-main: #f0f4f8;
            --bg-card: #ffffff;
            --accent: #06b6d4;
            --accent-dark: #0891b2;
            --accent-glow: rgba(6, 182, 212, 0.15);
            --text-muted-soft: #94a3b8;
            --sidebar-width: 260px;
            --topbar-height: 60px;
            --border-radius: 14px;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-main);
            margin: 0;
            color: #1e293b;
        }

        /* ── SIDEBAR ── */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--bg-sidebar);
            display: flex;
            flex-direction: column;
            z-index: 1000;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar-brand {
            padding: 24px 20px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }

        .sidebar-brand .brand-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .sidebar-brand .brand-icon {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: white;
            box-shadow: 0 4px 14px rgba(6,182,212,0.4);
        }

        .sidebar-brand .brand-text {
            line-height: 1.2;
        }

        .sidebar-brand .brand-name {
            font-size: 17px;
            font-weight: 800;
            color: #f1f5f9;
            letter-spacing: 0.5px;
        }

        .sidebar-brand .brand-sub {
            font-size: 10px;
            font-weight: 500;
            color: var(--text-muted-soft);
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .sidebar-section-label {
            padding: 20px 20px 6px;
            font-size: 10px;
            font-weight: 600;
            color: #475569;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .sidebar-nav {
            padding: 0 12px;
            flex: 1;
        }

        .sidebar-nav .nav-item {
            margin-bottom: 2px;
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 11px 14px;
            border-radius: 10px;
            color: #94a3b8;
            font-size: 13.5px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
            position: relative;
        }

        .sidebar-nav .nav-link:hover {
            background: var(--bg-sidebar-hover);
            color: #e2e8f0;
        }

        .sidebar-nav .nav-link.active {
            background: var(--accent-glow);
            color: var(--accent);
            font-weight: 600;
        }

        .sidebar-nav .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 22px;
            background: var(--accent);
            border-radius: 0 3px 3px 0;
        }

        .sidebar-nav .nav-link .nav-icon {
            width: 18px;
            text-align: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid rgba(255,255,255,0.06);
        }

        .live-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            background: rgba(6,182,212,0.1);
            border: 1px solid rgba(6,182,212,0.2);
            border-radius: 10px;
        }

        .live-dot {
            width: 8px;
            height: 8px;
            background: #22c55e;
            border-radius: 50%;
            animation: pulse-dot 2s infinite;
            flex-shrink: 0;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; box-shadow: 0 0 0 0 rgba(34,197,94,0.4); }
            50% { opacity: 0.8; box-shadow: 0 0 0 4px rgba(34,197,94,0); }
        }

        .live-badge span {
            font-size: 11.5px;
            font-weight: 600;
            color: var(--accent);
            letter-spacing: 0.5px;
        }

        /* ── MAIN CONTENT ── */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            height: var(--topbar-height);
            background: white;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .topbar-title {
            font-size: 15px;
            font-weight: 600;
            color: #1e293b;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .topbar-time {
            font-size: 12.5px;
            color: #64748b;
            font-weight: 500;
        }

        .main-content {
            flex: 1;
            padding: 28px;
        }

        /* ── CARDS ── */
        .card-panel {
            background: var(--bg-card);
            border-radius: var(--border-radius);
            border: none;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 4px 16px rgba(0,0,0,0.04);
            transition: box-shadow 0.2s, transform 0.2s;
        }

        .card-panel:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.06), 0 8px 24px rgba(0,0,0,0.06);
        }

        /* ── ALERTS ── */
        .alert-panel {
            border-radius: 10px;
            border: none;
            font-size: 13.5px;
            font-weight: 500;
        }

        /* ── BADGES ── */
        .badge-status {
            font-size: 11px;
            font-weight: 600;
            padding: 5px 11px;
            border-radius: 20px;
            letter-spacing: 0.3px;
        }

        /* ── BUTTONS ── */
        .btn-accent {
            background: var(--accent);
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 9px;
            padding: 9px 18px;
            font-size: 13.5px;
            transition: all 0.2s;
        }

        .btn-accent:hover {
            background: var(--accent-dark);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(6,182,212,0.35);
        }

        /* ── TABLES ── */
        .table-panel thead th {
            background: #f8fafc;
            font-size: 11.5px;
            font-weight: 600;
            color: #64748b;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            border-bottom: 1px solid #e2e8f0;
            padding: 13px 16px;
        }

        .table-panel tbody td {
            padding: 14px 16px;
            font-size: 13.5px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .table-panel tbody tr:hover td {
            background: #f8fafc;
        }

        .table-panel tbody tr:last-child td {
            border-bottom: none;
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <a class="brand-logo" href="{{ route('admin.dashboard') }}">
                <div class="brand-icon">
                    <i class="fa-solid fa-utensils"></i>
                </div>
                <div class="brand-text">
                    <div class="brand-name">DIMVES</div>
                    <div class="brand-sub">Admin Panel</div>
                </div>
            </a>
        </div>

        <p class="sidebar-section-label">Menu Utama</p>

        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                       href="{{ route('admin.dashboard') }}">
                        <span class="nav-icon"><i class="fa-solid fa-gauge-high"></i></span>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}"
                       href="{{ route('admin.orders.index') }}">
                        <span class="nav-icon"><i class="fa-solid fa-fire-burner"></i></span>
                        Kitchen Display
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.menus.*') ? 'active' : '' }}"
                       href="{{ route('admin.menus.index') }}">
                        <span class="nav-icon"><i class="fa-solid fa-book-open"></i></span>
                        Kelola Menu & Stok
                    </a>
                </li>
            </ul>

            <p class="sidebar-section-label" style="padding-top:16px;">Analitik</p>

            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"
                       href="{{ route('admin.reports.index') }}">
                        <span class="nav-icon"><i class="fa-solid fa-chart-line"></i></span>
                        Laporan Omzet
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.vouchers.*') ? 'active' : '' }}"
                       href="{{ route('admin.vouchers.index') }}">
                        <span class="nav-icon"><i class="fa-solid fa-ticket"></i></span>
                        Voucher & Promo
                    </a>
                </li>
            </ul>
        </nav>

        <div class="sidebar-footer">
            <div class="live-badge">
                <div class="live-dot"></div>
                <span>LIVE MONITOR AKTIF</span>
            </div>
        </div>
    </aside>

    <!-- MAIN WRAPPER -->
    <div class="main-wrapper">
        <!-- TOPBAR -->
        <header class="topbar">
            <span class="topbar-title">@yield('page-title', 'Admin Panel')</span>
            <div class="topbar-right">
                <span class="topbar-time" id="topbar-clock"></span>
                <span class="badge bg-light text-secondary border" style="font-size:11px; font-weight:600; padding:5px 10px; border-radius:8px;">
                    <i class="fa-solid fa-user-tie me-1"></i> Administrator
                </span>
            </div>
        </header>

        <!-- PAGE CONTENT -->
        <main class="main-content">
            @if(session('success'))
                <div class="alert alert-success alert-panel alert-dismissible fade show mb-4 shadow-sm" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-panel alert-dismissible fade show mb-4 shadow-sm" role="alert">
                    <i class="fa-solid fa-circle-xmark me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Live clock topbar
        function updateClock() {
            const now = new Date();
            const options = { weekday: 'short', day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit', second: '2-digit' };
            document.getElementById('topbar-clock').textContent = now.toLocaleDateString('id-ID', options);
        }
        updateClock();
        setInterval(updateClock, 1000);
    </script>
    @yield('scripts')
</body>
</html>

