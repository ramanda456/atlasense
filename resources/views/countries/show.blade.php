@extends('layouts.app')

@section('title', 'Detail Negara ' . $country->name)
@section('page-title', 'Detail Intelijen Negara: ' . $country->name)

@section('content')
<div class="row g-4 mb-4">
    <!-- Profil Negara -->
    <div class="col-md-8">
        <div class="as-card h-100">
            <div class="as-card-body d-flex align-items-center gap-4">
                @if($country->flag)
                    <img src="{{ $country->flag }}" alt="Flag" style="width: 120px; border-radius: 8px; border: 1px solid var(--border-light);">
                @else
                    <span style="font-size: 4rem;">🏳️</span>
                @endif
                <div>
                    <h3 class="text-white mb-2">{{ $country->name }} ({{ $country->code }})</h3>
                    <p class="text-secondary mb-1">🌍 Wilayah: <strong>{{ $country->region }}</strong> | Ibu Kota: <strong>{{ $country->capital ?? 'N/A' }}</strong></p>
                    <p class="text-muted small mb-0">Populasi: {{ $country->population ? number_format($country->population) : 'N/A' }} | Luas: {{ $country->area ? number_format($country->area) . ' km²' : 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Watchlist & Aksi -->
    <div class="col-md-4">
        <div class="as-card h-100 d-flex flex-column justify-content-center align-items-center p-4">
            <button class="btn-as-primary w-100 mb-3" id="btnToggleWatchlist" data-code="{{ $country->code }}">
                ⭐ Toggle Watchlist
            </button>
            <p class="text-muted text-center small mb-0">Klik tombol di atas untuk menambah/menghapus negara dari watchlist Anda.</p>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Risk Meter -->
    <div class="col-md-4">
        <div class="as-card h-100 text-center py-4">
            <div class="as-card-header">
                <h6>🎯 Tingkat Risiko Akhir</h6>
            </div>
            <div class="as-card-body d-flex flex-column justify-content-center align-items-center">
                <div class="score-display mb-3" style="font-size: 4rem;">{{ $riskData['risk_score'] }}</div>
                
                @if($riskData['risk_status'] === 'High Risk')
                    <span class="risk-badge bg-danger">HIGH RISK</span>
                @elseif($riskData['risk_status'] === 'Medium Risk')
                    <span class="risk-badge bg-warning text-dark">MEDIUM RISK</span>
                @else
                    <span class="risk-badge bg-success">LOW RISK</span>
                @endif

                <p class="text-secondary mt-3 small">
                    Skor didasarkan pada model risiko terbobot:<br>
                    <strong>Sentimen 40%</strong>, <strong>Cuaca 30%</strong>, <strong>Inflasi 20%</strong>, dan <strong>Kurs 10%</strong>.
                </p>
            </div>
        </div>
    </div>

    <!-- Breakdown Details -->
    <div class="col-md-8">
        <div class="as-card h-100">
            <div class="as-card-header">
                <h6>📊 Rincian Indikator Risiko</h6>
            </div>
            <div class="as-card-body">
                <div class="row g-3">
                    <!-- Weather -->
                    <div class="col-md-6">
                        <div class="p-3 rounded" style="background: var(--bg-secondary); border: 1px solid var(--border-color);">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-white small">🌤️ Risiko Cuaca (30%)</span>
                                <strong class="text-info">{{ $riskData['breakdown']['weather']['score'] }}</strong>
                            </div>
                            @if($weather)
                                <small class="text-secondary d-block">Suhu: {{ $weather->temperature }}°C | Angin: {{ $weather->wind_speed }} km/j</small>
                                <small class="text-muted">{{ $weather->description }} | Storm Risk: {{ $weather->storm_risk }}%</small>
                            @else
                                <small class="text-muted">Data cuaca belum disinkronkan.</small>
                            @endif
                        </div>
                    </div>

                    <!-- Economics -->
                    <div class="col-md-6">
                        <div class="p-3 rounded" style="background: var(--bg-secondary); border: 1px solid var(--border-color);">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-white small">📈 Risiko Inflasi (20%)</span>
                                <strong class="text-warning">{{ $riskData['breakdown']['inflation']['score'] }}</strong>
                            </div>
                            @if($economics->count() > 0)
                                <small class="text-secondary d-block">Inflasi Terbaru: {{ $economics->last()->inflation }}%</small>
                                <small class="text-muted">GDP: USD {{ number_format($economics->last()->gdp / 1e9, 2) }} Miliar</small>
                            @else
                                <small class="text-muted">Data ekonomi belum disinkronkan.</small>
                            @endif
                        </div>
                    </div>

                    <!-- Currency -->
                    <div class="col-md-6">
                        <div class="p-3 rounded" style="background: var(--bg-secondary); border: 1px solid var(--border-color);">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-white small">💱 Risiko Kurs (10%)</span>
                                <strong class="text-success">{{ $riskData['breakdown']['currency']['score'] }}</strong>
                            </div>
                            @if($currencyRate)
                                <small class="text-secondary d-block">1 USD = {{ number_format($currencyRate->rate_to_usd, 2) }} {{ $country->currency_code }}</small>
                            @else
                                <small class="text-muted">Data kurs belum disinkronkan.</small>
                            @endif
                        </div>
                    </div>

                    <!-- Sentiment -->
                    <div class="col-md-6">
                        <div class="p-3 rounded" style="background: var(--bg-secondary); border: 1px solid var(--border-color);">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-white small">📰 Risiko Sentimen Berita (40%)</span>
                                <strong class="text-danger">{{ $riskData['breakdown']['sentiment']['score'] }}</strong>
                            </div>
                            <small class="text-secondary d-block">Total Berita Diproses: {{ $riskData['breakdown']['sentiment']['total_news'] }}</small>
                            <small class="text-muted">Positif: {{ $riskData['breakdown']['sentiment']['positive_count'] }} | Negatif: {{ $riskData['breakdown']['sentiment']['negative_count'] }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Port Map -->
    <div class="col-md-7">
        <div class="as-card">
            <div class="as-card-header">
                <h6>🗺️ Pelabuhan Logistik & Peta Geospatial</h6>
            </div>
            <div class="as-card-body p-2">
                <div id="countryMap" style="height: 380px;"></div>
            </div>
        </div>
    </div>

    <!-- History Chart -->
    <div class="col-md-5">
        <div class="as-card">
            <div class="as-card-header">
                <h6>📈 Tren Perekonomian (GDP & Inflasi)</h6>
            </div>
            <div class="as-card-body">
                <div class="chart-container">
                    <canvas id="countryEconChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- News Feed -->
    <div class="col-md-12">
        <div class="as-card">
            <div class="as-card-header">
                <h6>📰 Analisis Sentimen Berita Logistik</h6>
            </div>
            <div class="as-card-body">
                <div class="row g-3">
                    @forelse($news as $item)
                        <div class="col-md-6">
                            <div class="p-3 rounded h-100" style="background: var(--bg-secondary); border: 1px solid var(--border-color);">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="text-white small mb-0">{{ $item->title }}</h6>
                                    @if($item->sentiment === 'Positive')
                                        <span class="badge bg-success">Positif</span>
                                    @elseif($item->sentiment === 'Negative')
                                        <span class="badge bg-danger">Negatif</span>
                                    @else
                                        <span class="badge bg-secondary">Netral</span>
                                    @endif
                                </div>
                                <p class="text-muted small mb-1">{{ Str::limit($item->description, 160) }}</p>
                                <small class="text-secondary" style="font-size: 0.7rem;">🕒 Diambil: {{ $item->fetched_at ? $item->fetched_at->diffForHumans() : '-' }}</small>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center text-muted py-4">Belum ada berita tercatat untuk negara ini.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Map Setup
    var map = L.map('countryMap').setView([{{ $country->latitude ?? 0 }}, {{ $country->longitude ?? 0 }}], 5);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 18,
    }).addTo(map);

    // Marker Negara
    L.marker([{{ $country->latitude ?? 0 }}, {{ $country->longitude ?? 0 }}]).addTo(map)
        .bindPopup('<strong>{{ $country->name }}</strong><br>Titik Pusat Negara')
        .openPopup();

    // Marker Pelabuhan
    @foreach($ports as $port)
        var portIcon = L.divIcon({
            className: 'port-marker',
            html: '<div style="background-color:#06b6d4; width:10px; height:10px; border-radius:50%; border:2px solid white; box-shadow: 0 0 4px rgba(0,0,0,0.5);"></div>',
            iconSize: [10, 10], iconAnchor: [5, 5]
        });
        L.marker([{{ $port->latitude }}, {{ $port->longitude }}], {icon: portIcon}).addTo(map)
            .bindPopup('🚢 <strong>{{ $port->name }}</strong><br>Code: {{ $port->code }}');
    @endforeach

    // Watchlist Action
    $('#btnToggleWatchlist').on('click', function() {
        var code = $(this).data('code');
        $.post('/watchlist/toggle', { country_code: code }, function(response) {
            alert(response.message);
        });
    });

    // Chart.js Economic History
    var ctx = document.getElementById('countryEconChart').getContext('2d');
    var years = [@foreach($economics as $e) "{{ $e->year }}", @endforeach];
    var gdpData = [@foreach($economics as $e) "{{ ($e->gdp / 1e9) }}", @endforeach]; // GDP in Billions USD
    var inflData = [@foreach($economics as $e) "{{ $e->inflation }}", @endforeach];

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: years,
            datasets: [
                {
                    label: 'GDP (Miliar USD)',
                    data: gdpData,
                    backgroundColor: 'rgba(6, 182, 212, 0.5)',
                    borderColor: '#06b6d4',
                    borderWidth: 1,
                    yAxisID: 'y'
                },
                {
                    label: 'Inflasi (%)',
                    data: inflData,
                    type: 'line',
                    borderColor: '#f59e0b',
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    tension: 0.3,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    type: 'linear',
                    position: 'left',
                    grid: { color: 'rgba(255,255,255,0.05)' },
                    ticks: { color: '#94a3b8' }
                },
                y1: {
                    type: 'linear',
                    position: 'right',
                    grid: { drawOnChartArea: false },
                    ticks: { color: '#94a3b8' }
                },
                x: {
                    grid: { color: 'rgba(255,255,255,0.05)' },
                    ticks: { color: '#94a3b8' }
                }
            },
            plugins: {
                legend: { labels: { color: '#e2e8f0' } }
            }
        }
    });
});
</script>
@endsection
