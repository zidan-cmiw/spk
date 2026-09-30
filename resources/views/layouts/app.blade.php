<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - SPK Penilaian Prestasi Siswa (AHP + SMART)</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --primary: #4338ca;
            --primary-dark: #3730a3;
            --primary-light: #e0e7ff;
            --secondary: #0ea5e9;
            --accent: #f59e0b;
            --success: #10b981;
            --sidebar-bg: #0f172a;
            --sidebar-active: #1e293b;
            --body-bg: #f8fafc;
            --card-border: #e2e8f0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--body-bg);
            color: #1e293b;
            min-height: 100vh;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 270px;
            background: linear-gradient(180deg, #0f172a 0%, #1e1b4b 100%);
            color: #e2e8f0;
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            box-shadow: 4px 0 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }

        .sidebar-brand {
            padding: 24px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .sidebar-brand h5 {
            font-weight: 700;
            font-size: 1.15rem;
            letter-spacing: -0.5px;
            margin: 0;
            color: #ffffff;
        }

        .sidebar-brand span {
            font-size: 0.75rem;
            color: #94a3b8;
        }

        .nav-section-title {
            padding: 16px 20px 6px;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            font-weight: 700;
        }

        .sidebar-nav {
            padding: 12px 14px;
        }

        .sidebar-nav .nav-link {
            color: #94a3b8;
            font-weight: 500;
            font-size: 0.92rem;
            padding: 10px 14px;
            border-radius: 10px;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s ease;
        }

        .sidebar-nav .nav-link i {
            font-size: 1.15rem;
            width: 22px;
        }

        .sidebar-nav .nav-link:hover {
            color: #ffffff;
            background-color: rgba(255,255,255,0.06);
            transform: translateX(3px);
        }

        .sidebar-nav .nav-link.active {
            color: #ffffff;
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35);
        }

        /* Main Content */
        .main-content {
            margin-left: 270px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .top-navbar {
            background: #ffffff;
            border-bottom: 1px solid var(--card-border);
            padding: 14px 28px;
            position: sticky;
            top: 0;
            z-index: 990;
        }

        .content-area {
            padding: 28px;
            flex-grow: 1;
        }

        /* Cards and Elements */
        .card {
            border: 1px solid var(--card-border);
            border-radius: 14px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02), 0 4px 12px rgba(0,0,0,0.03);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            box-shadow: 0 6px 18px rgba(0,0,0,0.05);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--card-border);
            padding: 16px 20px;
            font-weight: 600;
        }

        .stat-card {
            border-radius: 14px;
            padding: 22px;
            position: relative;
            overflow: hidden;
        }

        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .badge-consistent {
            background: #dcfce7;
            color: #15803d;
            font-weight: 600;
            border: 1px solid #bbf7d0;
        }

        .badge-inconsistent {
            background: #fee2e2;
            color: #b91c1c;
            font-weight: 600;
            border: 1px solid #fecaca;
        }

        .table-custom th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-top: none;
        }

        .table-custom td {
            vertical-align: middle;
            font-size: 0.92rem;
        }

        .flowchart-box {
            background: #ffffff;
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            padding: 12px 16px;
            text-align: center;
            font-weight: 600;
            font-size: 0.88rem;
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        }

        .flowchart-arrow {
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            font-size: 1.3rem;
            padding: 6px 0;
        }

        @media (max-width: 991px) {
            .sidebar {
                margin-left: -270px;
            }
            .sidebar.show {
                margin-left: 0;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand d-flex align-items-center gap-3">
            <div style="background: linear-gradient(135deg, #6366f1, #3b82f6); width: 42px; height: 42px; border-radius: 10px;" class="d-flex align-items-center justify-content-center text-white shadow-sm">
                <i class="bi bi-award-fill fs-5"></i>
            </div>
            <div>
                <h5>SPK Prestasi</h5>
                <span>SMK Mandiri (AHP + SMART)</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-title">Menu Utama</div>
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            <div class="nav-section-title">Data Master</div>
            <a href="{{ route('criteria.index') }}" class="nav-link {{ request()->routeIs('criteria.*') ? 'active' : '' }}">
                <i class="bi bi-sliders"></i> Kriteria & Bobot
            </a>
            <a href="{{ route('students.index') }}" class="nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Siswa (Alternatif)
            </a>

            <div class="nav-section-title">Proses Penilaian SPK</div>
            <a href="{{ route('ahp.index') }}" class="nav-link {{ request()->routeIs('ahp.*') ? 'active' : '' }}">
                <i class="bi bi-diagram-3"></i> Modul AHP (Bobot & CR)
            </a>
            <a href="{{ route('assessments.index') }}" class="nav-link {{ request()->routeIs('assessments.*') ? 'active' : '' }}">
                <i class="bi bi-pencil-square"></i> Input Penilaian Siswa
            </a>
            <a href="{{ route('smart.index') }}" class="nav-link {{ request()->routeIs('smart.*') ? 'active' : '' }}">
                <i class="bi bi-trophy"></i> Perangkingan SMART
            </a>

            <div class="nav-section-title">Laporan & Referensi</div>
            <a href="{{ route('reports.print') }}" target="_blank" class="nav-link">
                <i class="bi bi-printer"></i> Cetak Laporan
            </a>
            <a href="{{ route('documentation.index') }}" class="nav-link {{ request()->routeIs('documentation.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i> Dokumentasi Model
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <header class="top-navbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-light d-lg-none" id="sidebarToggle">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                        <i class="bi bi-journal-check me-1"></i> Herdiana dkk. (Juni 2024)
                    </span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                        <i class="bi bi-check-circle me-1"></i> Terintegrasi AHP + SMART
                    </span>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <span class="text-muted small d-none d-md-inline">
                    <i class="bi bi-calendar-event me-1"></i> Periode: <strong>2024/2025 (Ganjil)</strong>
                </span>
                <a href="{{ route('smart.index') }}" class="btn btn-primary btn-sm px-3 shadow-sm rounded-pill">
                    <i class="bi bi-bar-chart-fill me-1"></i> Lihat Ranking Siswa
                </a>
            </div>
        </header>

        <!-- Content Area -->
        <main class="content-area">
            <!-- Flash Notifications -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill fs-5 me-2 text-success"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('warning'))
                <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5 me-2 text-warning"></i>
                    <div>{{ session('warning') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
                    <i class="bi bi-x-circle-fill fs-5 me-2 text-danger"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <div class="fw-bold mb-1"><i class="bi bi-exclamation-circle me-1"></i> Terjadi kesalahan validasi:</div>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-top py-3 px-4 text-center text-muted small">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                <span>&copy; {{ date('Y') }} SPK Penilaian Prestasi Siswa SMK Mandiri &mdash; Integrasi Metode AHP & SMART.</span>
                <span>Framework: <strong>Laravel 13</strong> | PHP 8.5 | MySQL</span>
            </div>
        </footer>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('show');
        });
    </script>
    @yield('scripts')
</body>
</html>
