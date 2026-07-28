<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') — AtlaSense</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- Atlas custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/final.css') }}">
    
    @yield('styles')
</head>
<body>

    <!-- Loading Overlay -->
    <div id="loaderOverlay" class="neo-loader-overlay">
        <div class="neo-loader-box">
            <div class="spinner-border text-dark mb-3" style="width: 3rem; height: 3rem; border-width: 4px;" role="status"></div>
            <h4 class="fw-bold">Sedang Memproses Data</h4>
            <p class="text-secondary small mb-0">Mohon tunggu beberapa saat...</p>
        </div>
    </div>

    <!-- Top Header -->
    <header class="atlas-header">
        <div class="container-fluid d-flex align-items-center justify-content-between py-2 px-4">
            <div class="d-flex align-items-center gap-3">
                <div class="atlas-brand">
                    <h2 class="fw-bold mb-0 text-dark" style="font-size: 1.5rem; letter-spacing: -0.5px;">AtlaSense</h2>
                    <span class="text-secondary small" style="font-size: 0.75rem;">Supply Chain Global AtlaSense</span>
                </div>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <div class="px-3 py-1 text-secondary" style="border: 1px solid var(--border-color); background-color: #FFFDF9; border-radius: 6px; font-size: 0.8rem; box-shadow: 0 1px 3px rgba(168,155,133,0.1);">
                    {{ now()->translatedFormat('d M Y') }}
                </div>
                <!-- User Area -->
                <div class="d-flex align-items-center gap-3 border-start ps-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.85rem;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="overflow-hidden d-none d-md-block" style="max-width: 140px; line-height: 1.2;">
                            <div class="fw-bold text-truncate small" style="color: var(--text-dark);">{{ auth()->user()->name }}</div>
                            <div class="text-secondary" style="font-size: 0.7rem;">{{ auth()->user()->role === 'admin' ? 'Administrator' : 'Pengguna' }}</div>
                        </div>
                    </div>
                    <form action="{{ url('/logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-link text-danger fw-bold p-0 border-0 text-decoration-none" style="font-size: 0.85rem;">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Top Navigation Bar -->
    <nav class="atlas-navbar">
        <div class="container-fluid px-4 d-flex flex-wrap align-items-center gap-2">
            <!-- Dasbor Utama -->
            <a href="{{ route('dashboard') }}" class="atlas-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i> Dasbor Utama
            </a>
            
            <!-- Intelijen Global Dropdown -->
            <div class="dropdown">
                <button class="atlas-nav-link dropdown-toggle {{ request()->routeIs('countries.index') || request()->routeIs('countries.show') || request()->routeIs('countries.compare') || request()->routeIs('analysis.risk') || request()->routeIs('analysis.visualization') || request()->routeIs('watchlists.index') ? 'active' : '' }}" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-globe2"></i> Intelijen Global
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item {{ request()->routeIs('countries.index') || request()->routeIs('countries.show') ? 'active' : '' }}" href="{{ route('countries.index') }}">Daftar Negara</a></li>
                    <li><a class="dropdown-item {{ request()->routeIs('countries.compare') ? 'active' : '' }}" href="{{ route('countries.compare') }}">Perbandingan Negara</a></li>
                    <li><a class="dropdown-item {{ request()->routeIs('analysis.risk') ? 'active' : '' }}" href="{{ route('analysis.risk') }}">Analisis Risiko</a></li>
                    <li><a class="dropdown-item {{ request()->routeIs('analysis.visualization') ? 'active' : '' }}" href="{{ route('analysis.visualization') }}">Visualisasi Data</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item {{ request()->routeIs('watchlists.index') ? 'active' : '' }}" href="{{ route('watchlists.index') }}">Daftar Pantauan</a></li>
                </ul>
            </div>
            
            <!-- Monitoring Dropdown -->
            <div class="dropdown">
                <button class="atlas-nav-link dropdown-toggle {{ request()->routeIs('monitoring.*') ? 'active' : '' }}" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-speedometer2"></i> Monitoring
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item {{ request()->routeIs('monitoring.weather') ? 'active' : '' }}" href="{{ route('monitoring.weather') }}">Pemantauan Cuaca</a></li>
                    <li><a class="dropdown-item {{ request()->routeIs('monitoring.currency') ? 'active' : '' }}" href="{{ route('monitoring.currency') }}">Dampak Nilai Tukar</a></li>
                    <li><a class="dropdown-item {{ request()->routeIs('monitoring.ports') ? 'active' : '' }}" href="{{ route('monitoring.ports') }}">Lokasi Pelabuhan</a></li>
                    <li><a class="dropdown-item {{ request()->routeIs('monitoring.news') ? 'active' : '' }}" href="{{ route('monitoring.news') }}">Intelijen Berita</a></li>
                </ul>
            </div>
            
            <!-- Administrator Dropdown -->
            @if(auth()->user()->isAdmin())
                <div class="dropdown">
                    <button class="atlas-nav-link dropdown-toggle {{ request()->routeIs('admin.*') ? 'active' : '' }}" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-shield-lock-fill"></i> Administrator
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Dasbor Admin</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item {{ request()->routeIs('admin.users') ? 'active' : '' }}" href="{{ route('admin.users') }}">Kelola Pengguna</a></li>
                        <li><a class="dropdown-item {{ request()->routeIs('admin.ports') ? 'active' : '' }}" href="{{ route('admin.ports') }}">Kelola Pelabuhan</a></li>
                        <li><a class="dropdown-item {{ request()->routeIs('admin.articles') ? 'active' : '' }}" href="{{ route('admin.articles') }}">Kelola Artikel</a></li>
                        <li><a class="dropdown-item {{ request()->routeIs('admin.words') ? 'active' : '' }}" href="{{ route('admin.words') }}">Kamus Sentimen</a></li>
                        <li><a class="dropdown-item {{ request()->routeIs('admin.api-logs') ? 'active' : '' }}" href="{{ route('admin.api-logs') }}">Log Panggilan API</a></li>
                    </ul>
                </div>
            @endif
        </div>
    </nav>

    <div class="neo-wrapper">
        <!-- Main Content -->
        <main class="neo-main">
            <header class="neo-topbar">
                <div>
                    <h2 class="neo-topbar-title">@yield('page-title')</h2>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="px-3 py-1 text-secondary" style="border: 1px solid var(--border-color); background-color: #FFFDF9; border-radius: 6px; font-size: 0.8rem; box-shadow: 0 1px 3px rgba(168,155,133,0.1);">
                        {{ now()->translatedFormat('d M Y') }}
                    </div>
                    <span class="neo-badge neo-badge-lime">Online</span>
                </div>
            </header>

            @yield('content')
        </main>
    </div>

    <!-- jQuery & Bootstrap 5 JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Setup CSRF header for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Global loader toggle
        function showLoader() {
            $('#loaderOverlay').css('display', 'flex');
        }
        function hideLoader() {
            $('#loaderOverlay').hide();
        }

        // Show loader on form submits
        $('form').on('submit', function() {
            // Except for logout or toggle forms
            if (!$(this).attr('action').includes('logout') && !$(this).attr('action').includes('toggle')) {
                showLoader();
            }
        });
    </script>

    @yield('scripts')
</body>
</html>
