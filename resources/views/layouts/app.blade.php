<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="AtlaSense - Platform Intelijen Risiko Rantai Pasok Global. Monitoring risiko secara real-time.">
    <title>@yield('title', 'AtlaSense') — Supply Chain Risk Intelligence</title>

    <!-- Leaflet.js CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- AtlaSense Custom CSS (Dark Theme) -->
    <link rel="stylesheet" href="{{ asset('css/atlasense.css') }}">

    @yield('styles')
</head>
<body>

    <!-- ============================================
         SIDEBAR NAVIGATION
         ============================================ -->
    <aside class="sidebar" id="sidebar">
        <!-- Brand -->
        <div class="sidebar-brand">
            <div class="brand-icon">🌐</div>
            <div class="brand-text">
                <h5>AtlaSense</h5>
                <small>Risk Intelligence</small>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="sidebar-nav">
            <!-- Group: Ringkasan -->
            <div class="sidebar-group-label">Ringkasan</div>
            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="icon">📊</span> Dashboard
            </a>

            <!-- Group: Intelijen Global -->
            <div class="sidebar-group-label">Intelijen Global</div>
            <a href="{{ route('countries.index') }}" class="sidebar-link {{ request()->routeIs('countries.*') ? 'active' : '' }}">
                <span class="icon">🌍</span> Data Negara
            </a>
            <a href="{{ route('analysis.risk') }}" class="sidebar-link {{ request()->routeIs('analysis.risk') ? 'active' : '' }}">
                <span class="icon">⚠️</span> Analisis Risiko
            </a>
            <a href="{{ route('analysis.visualization') }}" class="sidebar-link {{ request()->routeIs('analysis.visualization') ? 'active' : '' }}">
                <span class="icon">📈</span> Visualisasi Data
            </a>
            <a href="{{ route('countries.compare') }}" class="sidebar-link {{ request()->routeIs('countries.compare') ? 'active' : '' }}">
                <span class="icon">🔄</span> Perbandingan Negara
            </a>

            <!-- Group: Monitoring -->
            <div class="sidebar-group-label">Monitoring</div>
            <a href="{{ route('monitoring.weather') }}" class="sidebar-link {{ request()->routeIs('monitoring.weather') ? 'active' : '' }}">
                <span class="icon">🌤️</span> Monitoring Cuaca
            </a>
            <a href="{{ route('monitoring.currency') }}" class="sidebar-link {{ request()->routeIs('monitoring.currency') ? 'active' : '' }}">
                <span class="icon">💱</span> Nilai Tukar
            </a>
            <a href="{{ route('monitoring.ports') }}" class="sidebar-link {{ request()->routeIs('monitoring.ports') ? 'active' : '' }}">
                <span class="icon">🚢</span> Pelabuhan
            </a>
            <a href="{{ route('monitoring.news') }}" class="sidebar-link {{ request()->routeIs('monitoring.news') ? 'active' : '' }}">
                <span class="icon">📰</span> Berita
            </a>

            <!-- Group: Administrator (admin only) -->
            @if(Auth::user() && Auth::user()->role === 'admin')
            <div class="sidebar-group-label">Administrator</div>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="icon">🛡️</span> Admin Dashboard
            </a>
            <a href="{{ route('admin.users') }}" class="sidebar-link {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                <span class="icon">👥</span> Manajemen User
            </a>
            <a href="{{ route('admin.ports') }}" class="sidebar-link {{ request()->routeIs('admin.ports') ? 'active' : '' }}">
                <span class="icon">⚓</span> Manajemen Ports
            </a>
            <a href="{{ route('admin.words') }}" class="sidebar-link {{ request()->routeIs('admin.words') ? 'active' : '' }}">
                <span class="icon">📖</span> Kamus Sentimen
            </a>
            <a href="{{ route('admin.articles') }}" class="sidebar-link {{ request()->routeIs('admin.articles') ? 'active' : '' }}">
                <span class="icon">📰</span> Artikel Analisis
            </a>
            <a href="{{ route('admin.api-logs') }}" class="sidebar-link {{ request()->routeIs('admin.api-logs') ? 'active' : '' }}">
                <span class="icon">📋</span> API Logs
            </a>
            @endif
        </nav>

        <!-- Footer User Info -->
        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}</div>
                <div>
                    <div class="user-name">{{ Auth::user()->name ?? 'User' }}</div>
                    <div class="user-role">{{ Auth::user()->role ?? 'user' }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-as-outline" style="width: 100%; text-align: center; font-size: 0.75rem; padding: 5px;">
                    🚪 Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- ============================================
         MAIN CONTENT AREA
         ============================================ -->
    <div class="main-content">
        <!-- Top Navbar -->
        <header class="top-navbar">
            <div class="page-title">@yield('page-title', 'Dashboard')</div>
            <div class="navbar-actions">
                <button class="btn-as-outline" data-bs-toggle="modal" data-bs-target="#watchlistModal" style="font-size: 0.75rem; padding: 4px 12px;">
                    ⭐ Watchlist
                </button>
            </div>
        </header>

        <!-- Content -->
        <main class="content-area">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="as-footer">
            AtlaSense &copy; {{ date('Y') }} — Global Supply Chain Risk Intelligence Platform | Project Final
        </footer>
    </div>

    <!-- ============================================
         MODAL WATCHLIST
         ============================================ -->
    <div class="modal fade" id="watchlistModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);">
                <div class="modal-header" style="border-bottom: 1px solid var(--border-color);">
                    <h6 class="modal-title">⭐ Watchlist Negara Favorit</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="watchlistContent">
                    <p class="text-center" style="color: var(--text-muted);">Memuat watchlist...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================
         JAVASCRIPT LIBRARIES
         ============================================ -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <script>
    // Global CSRF setup untuk AJAX
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // Watchlist modal handler
    $('#watchlistModal').on('show.bs.modal', function() {
        $.ajax({
            url: '/api/watchlist',
            method: 'GET',
            dataType: 'json',
            success: function(watchlist) {
                if (watchlist.length === 0) {
                    $('#watchlistContent').html('<p class="text-center" style="color:var(--text-muted);padding:20px;">Belum ada negara di watchlist.</p>');
                    return;
                }
                var html = '';
                watchlist.forEach(function(code) {
                    html += '<div class="watchlist-item d-flex justify-content-between align-items-center">';
                    html += '<strong>' + code + '</strong>';
                    html += '<div>';
                    html += '<a href="/negara/' + code + '" class="btn-as-outline" style="font-size:0.7rem;padding:3px 10px;margin-right:4px;">📊 Detail</a>';
                    html += '<button class="btn-as-danger btn-remove-wl" data-code="' + code + '" style="font-size:0.7rem;padding:3px 10px;">🗑️</button>';
                    html += '</div></div>';
                });
                $('#watchlistContent').html(html);
            }
        });
    });

    $(document).on('click', '.btn-remove-wl', function() {
        var code = $(this).data('code');
        $.post('/watchlist/toggle', { country_code: code }, function() {
            $('#watchlistModal').trigger('show.bs.modal');
        });
    });
    </script>

    @yield('scripts')

</body>
</html>
