@extends('layouts.app')

@section('title', 'Monitoring Cuaca')
@section('page-title', 'Monitoring — Cuaca Global')

@section('content')
<div class="row g-4 mb-4">
    <!-- Peta Interaktif Cuaca -->
    <div class="col-md-12">
        <div class="as-card">
            <div class="as-card-header">
                <h6>🗺️ Peta Cuaca & Kecepatan Angin Dunia</h6>
                <div class="d-flex gap-3 text-secondary small">
                    <div><span style="display:inline-block; width:12px; height:12px; border-radius:50%; background:#ef4444; margin-right:4px;"></span> Badai Ekstrem (>=60%)</div>
                    <div><span style="display:inline-block; width:12px; height:12px; border-radius:50%; background:#f59e0b; margin-right:4px;"></span> Hujan/Angin Sedang (30-59%)</div>
                    <div><span style="display:inline-block; width:12px; height:12px; border-radius:50%; background:#10b981; margin-right:4px;"></span> Cerah/Normal (<30%)</div>
                </div>
            </div>
            <div class="as-card-body p-2">
                <div id="weatherMap" style="height: 380px;"></div>
            </div>
        </div>
    </div>
</div>

<div class="as-card mb-4">
    <div class="as-card-header">
        <h6>🌤️ Cari Data Cuaca</h6>
    </div>
    <div class="as-card-body">
        <form method="GET" action="{{ route('monitoring.weather') }}" class="row g-3">
            <div class="col-md-9">
                <input type="text" name="search" class="as-input" placeholder="Cari nama atau kode negara..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3 d-grid">
                <button type="submit" class="btn-as-primary">Cari Cuaca</button>
            </div>
        </form>
    </div>
</div>

<div class="as-card">
    <div class="as-card-header">
        <h6>📋 Status Cuaca Terkini & Risiko Badai</h6>
    </div>
    <div class="as-card-body p-0">
        <div class="table-responsive">
            <table class="as-table">
                <thead>
                    <tr>
                        <th>Negara</th>
                        <th>Suhu (°C)</th>
                        <th>Kelembapan (%)</th>
                        <th>Kecepatan Angin (km/jam)</th>
                        <th>Precipitation (Curah Hujan)</th>
                        <th>Status Cuaca</th>
                        <th>Risiko Badai (Storm Risk)</th>
                        <th>Waktu Sinkronisasi</th>
                        <th style="width: 100px; text-align: center;">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($weatherData as $wd)
                    <tr>
                        <td>
                            @if($wd->country->flag)
                                <img src="{{ $wd->country->flag }}" alt="Flag" style="width: 24px; margin-right: 8px; border-radius: 2px;">
                            @endif
                            <strong class="text-white">{{ $wd->country->name }}</strong>
                        </td>
                        <td>{{ $wd->temperature ?? 'N/A' }} °C</td>
                        <td>{{ $wd->humidity ?? 'N/A' }} %</td>
                        <td>
                            {{ $wd->wind_speed ?? 'N/A' }} km/jam
                            @if(($wd->wind_speed ?? 0) > 25)
                                <span class="text-danger ms-1" title="Angin Kencang (Penalti +15 diterapkan)">⚠️</span>
                            @endif
                        </td>
                        <td>{{ $wd->precipitation ?? '0' }} mm</td>
                        <td>
                            <span class="text-secondary">{{ $wd->description ?? 'Unknown' }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="bar-container" style="width: 60px; height: 6px; background: var(--bg-input); border-radius: 3px; overflow: hidden;">
                                    <div class="bar-fill" style="width: {{ $wd->storm_risk }}%; height: 100%; background: {{ $wd->storm_risk >= 60 ? 'var(--accent-red)' : ($wd->storm_risk >= 30 ? 'var(--accent-yellow)' : 'var(--accent-green)') }};"></div>
                                </div>
                                <span>{{ $wd->storm_risk }}%</span>
                            </div>
                        </td>
                        <td><small class="text-muted">{{ $wd->fetched_at ? $wd->fetched_at->diffForHumans() : '-' }}</small></td>
                        <td class="text-center">
                            <a href="{{ route('countries.show', $wd->country->code) }}" class="btn-as-outline" style="font-size:0.7rem; padding:3px 8px;">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">Data cuaca tidak ditemukan. Harap jalankan sync command `php artisan atlasense:sync-weather`.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3 d-flex justify-content-center">
    {{ $weatherData->links('pagination::bootstrap-5') }}
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    var map = L.map('weatherMap').setView([15, 20], 2);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 18,
    }).addTo(map);

    var weatherPoints = @json($allWeather);
    
    weatherPoints.forEach(function(item) {
        if (item.country.latitude && item.country.longitude) {
            // Tentukan warna marker risiko cuaca
            var color = '#10b981'; // Cerah/Normal
            if (item.storm_risk >= 60) {
                color = '#ef4444'; // Merah (Badai Ekstrem)
            } else if (item.storm_risk >= 30) {
                color = '#f59e0b'; // Jingga (Hujan/Angin Kencang)
            }

            var markerIcon = L.divIcon({
                className: 'weather-marker',
                html: '<div style="background-color:' + color + '; width:14px; height:14px; border-radius:50%; border:2px solid white; box-shadow: 0 0 6px rgba(0,0,0,0.5);"></div>',
                iconSize: [14, 14], iconAnchor: [7, 7]
            });

            L.marker([item.country.latitude, item.country.longitude], {icon: markerIcon}).addTo(map)
                .bindPopup(
                    '<strong>🌤️ ' + item.country.name + '</strong><br>' +
                    'Cuaca: <strong>' + (item.description || 'Unknown') + '</strong><br>' +
                    'Suhu: ' + (item.temperature || 'N/A') + ' °C<br>' +
                    'Kecepatan Angin: ' + (item.wind_speed || 'N/A') + ' km/jam<br>' +
                    'Curah Hujan: ' + (item.precipitation || '0') + ' mm<br>' +
                    'Storm Risk: <strong>' + item.storm_risk + '%</strong>'
                );
        }
    });
});
</script>
@endsection
