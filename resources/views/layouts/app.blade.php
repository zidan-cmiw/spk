<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SPK Siswa Prestasi') - SMK Mandiri</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --primary-light: #eef2ff;
            --success: #10b981;
            --warning: #f59e0b;
            --sidebar-bg: #0f172a;
            --body-bg: #f8fafc;
            --card-border: #e2e8f0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--body-bg);
            color: #0f172a;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background: #0f172a;
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            box-shadow: 2px 0 12px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .sidebar-brand {
            padding: 22px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .sidebar-nav {
            padding: 16px 12px;
        }

        .nav-section-title {
            padding: 12px 14px 6px;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #64748b;
            font-weight: 700;
        }

        .sidebar-nav .nav-link {
            color: #94a3b8;
            font-weight: 600;
            font-size: 0.92rem;
            padding: 11px 14px;
            border-radius: 10px;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.2s ease;
        }

        .sidebar-nav .nav-link .nav-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-nav .nav-link i {
            font-size: 1.15rem;
        }

        .sidebar-nav .nav-link:hover {
            color: #ffffff;
            background-color: rgba(255,255,255,0.06);
        }

        .sidebar-nav .nav-link.active {
            color: #ffffff;
            background: #4f46e5;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4);
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
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
            padding: 24px 28px;
            flex-grow: 1;
        }

        /* Cards and Elements */
        .card {
            border: 1px solid var(--card-border);
            border-radius: 14px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.02);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--card-border);
            padding: 16px 20px;
            font-weight: 700;
        }

        .step-badge {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .table-custom th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 700;
            font-size: 0.85rem;
            border-top: none;
            padding: 12px 14px;
        }

        .table-custom td {
            vertical-align: middle;
            padding: 12px 14px;
            font-size: 0.92rem;
        }

        .input-card-hero {
            background: #ffffff;
            border: 2px solid #e0e7ff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(79, 70, 229, 0.06);
        }

        @media (max-width: 991px) {
            .sidebar {
                margin-left: -260px;
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
            <div style="background: #4f46e5; width: 40px; height: 40px; border-radius: 10px;" class="d-flex align-items-center justify-content-center text-white shadow-sm">
                <i class="bi bi-trophy-fill fs-5"></i>
            </div>
            <div>
                <h6 class="text-white fw-bold mb-0">SPK Prestasi</h6>
                <small class="text-muted" style="font-size: 0.75rem;">SMK Mandiri</small>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-title">Menu Utama</div>
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <div class="nav-left">
                    <i class="bi bi-house-door-fill"></i>
                    <span>Dashboard</span>
                </div>
            </a>

            <div class="nav-section-title">Tahapan Input & Nilai</div>
            
            <!-- Step 1: Input Siswa -->
            <a href="{{ route('students.index') }}" class="nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}">
                <div class="nav-left">
                    <i class="bi bi-person-plus-fill text-primary"></i>
                    <span>1. Input Siswa</span>
                </div>
                <span class="badge bg-primary rounded-pill">Input</span>
            </a>

            <!-- Step 2: Input Nilai -->
            <a href="{{ route('assessments.index') }}" class="nav-link {{ request()->routeIs('assessments.*') ? 'active' : '' }}">
                <div class="nav-left">
                    <i class="bi bi-pencil-square text-warning"></i>
                    <span>2. Input Nilai</span>
                </div>
                <span class="badge bg-warning text-dark rounded-pill">Nilai</span>
            </a>

            <!-- Step 3: Bobot AHP -->
            <a href="{{ route('ahp.index') }}" class="nav-link {{ request()->routeIs('ahp.*') ? 'active' : '' }}">
                <div class="nav-left">
                    <i class="bi bi-sliders2 text-info"></i>
                    <span>3. Bobot AHP</span>
                </div>
                <span class="badge bg-success rounded-pill">CR &lt; 0.1</span>
            </a>

            <!-- Step 4: Hasil Ranking -->
            <a href="{{ route('smart.index') }}" class="nav-link {{ request()->routeIs('smart.*') ? 'active' : '' }}">
                <div class="nav-left">
                    <i class="bi bi-award-fill text-success"></i>
                    <span>4. Hasil Ranking</span>
                </div>
                <span class="badge bg-danger rounded-pill">Juara</span>
            </a>

            <div class="nav-section-title">Menu Lainnya</div>
            <a href="{{ route('criteria.index') }}" class="nav-link {{ request()->routeIs('criteria.*') ? 'active' : '' }}">
                <div class="nav-left">
                    <i class="bi bi-list-check"></i>
                    <span>Kriteria Penilaian</span>
                </div>
            </a>
            <a href="{{ route('reports.print') }}" target="_blank" class="nav-link">
                <div class="nav-left">
                    <i class="bi bi-printer"></i>
                    <span>Cetak Laporan</span>
                </div>
            </a>
            <a href="{{ route('documentation.index') }}" class="nav-link {{ request()->routeIs('documentation.*') ? 'active' : '' }}">
                <div class="nav-left">
                    <i class="bi bi-file-earmark-text"></i>
                    <span>Panduan Model</span>
                </div>
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
                <div class="fw-bold fs-6 text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-mortarboard-fill text-primary"></i>
                    SPK Penilaian Prestasi Siswa SMK Mandiri
                </div>
            </div>

            <!-- Quick Action Shortcut Buttons -->
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('students.index') }}" class="btn btn-outline-primary btn-sm px-3 rounded-pill fw-semibold">
                    <i class="bi bi-person-plus me-1"></i> + Tambah Siswa
                </a>
                <a href="{{ route('assessments.index') }}" class="btn btn-primary btn-sm px-3 rounded-pill fw-semibold shadow-sm">
                    <i class="bi bi-pencil-fill me-1"></i> Input Nilai
                </a>
                <a href="{{ route('smart.index') }}" class="btn btn-warning btn-sm px-3 rounded-pill text-dark fw-bold shadow-sm">
                    <i class="bi bi-trophy-fill me-1"></i> Ranking Juara
                </a>
            </div>
        </header>

        <!-- Content Area -->
        <main class="content-area">
            <!-- Flash Notifications -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm py-2 px-3 mb-4 rounded-3" role="alert">
                    <i class="bi bi-check-circle-fill fs-5 me-2 text-success"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('warning'))
                <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center shadow-sm py-2 px-3 mb-4 rounded-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5 me-2 text-warning"></i>
                    <div>{{ session('warning') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center shadow-sm py-2 px-3 mb-4 rounded-3" role="alert">
                    <i class="bi bi-x-circle-fill fs-5 me-2 text-danger"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>
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
