@extends('layouts.utama')

@section('title', 'Pemantauan Cuaca')
@section('page-title', 'Pemantauan Cuaca')

@section('content')
<!-- Map Widget -->
<div class="neo-box">
    <div class="card-header-blue">
        <h4 class="card-header-title">PETA KONDISI ANGIN & RISIKO BADAI DUNIA</h4>
    </div>
    <div id="weatherMap" style="height: 380px; border-radius: 6px; border: 1px solid var(--border-color); box-shadow: 0 1px 3px rgba(168,155,133,0.1);"></div>
    <div class="d-flex flex-wrap gap-3 mt-3 justify-content-center">
        <div><span class="d-inline-block rounded-circle" style="width: 12px; height: 12px; background-color: var(--atlas-land);"></span> Risiko Rendah (&lt; 30%)</div>
        <div><span class="d-inline-block rounded-circle" style="width: 12px; height: 12px; background-color: var(--atlas-sand);"></span> Risiko Sedang (30% - 60%)</div>
        <div><span class="d-inline-block rounded-circle" style="width: 12px; height: 12px; background-color: var(--atlas-danger);"></span> Risiko Tinggi (&gt; 60%)</div>
    </div>
</div>

<!-- Search Widget -->
<div class="neo-box">
    <div class="card-header-yellow">
        <h4 class="card-header-title">CARI DATA CUACA NEGARA</h4>
    </div>
    <form method="GET" action="{{ route('monitoring.weather') }}" class="row g-3">
        <div class="col-md-9">
            <input type="text" name="search" class="neo-input m-0" placeholder="Masukkan nama atau kode negara..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn-neo btn-neo-lime w-100 py-3 fw-bold">CARI CUACA</button>
        </div>
    </form>
</div>

<!-- Weather Table -->
<div class="neo-table-wrapper">
    <table class="neo-table">
        <thead>
            <tr>
                <th>Negara</th>
                <th>Suhu (°C)</th>
                <th>Kelembapan (%)</th>
                <th>Kecepatan Angin</th>
                <th>Curah Hujan (mm)</th>
                <th>Kondisi Cuaca</th>
                <th>Risiko Badai (Storm Risk)</th>
                <th>Waktu Sinkron</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($weatherData as $wd)
            <tr>
                <td>
                    @if($wd->negara?->flag_url)
                        <img src="{{ $wd->negara->flag_url }}" alt="Flag" style="width: 24px; border: 1px solid #000; margin-right: 8px;">
                    @endif
                    <strong class="text-dark">{{ $wd->negara?->name ?? '-' }}</strong>
                </td>
                <td>{{ $wd->temperature ?? 'N/A' }} °C</td>
                <td>{{ $wd->humidity ?? 'N/A' }} %</td>
                <td>
                    {{ $wd->wind_speed ?? 'N/A' }} km/jam
                    @if(($wd->wind_speed ?? 0) > 25)
                        <span class="text-danger" title="Angin Kencang! (Penalti +15 diterapkan)">⚠️</span>
                    @endif
                </td>
                <td>{{ $wd->precipitation ?? '0' }} mm</td>
                <td>
                    <span class="neo-badge neo-badge-blue">{{ $wd->condition ?? 'Cerah' }}</span>
                </td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="border border-dark border-1" style="width: 60px; height: 10px; background: #E2E8F0; overflow: hidden;">
                            <div style="width: {{ $wd->storm_risk }}%; height: 100%; background: {{ $wd->storm_risk >= 60 ? 'var(--neo-pink)' : ($wd->storm_risk >= 30 ? 'var(--neo-yellow)' : 'var(--neo-lime)') }};"></div>
                        </div>
                        <span class="fw-bold">{{ $wd->storm_risk }}%</span>
                    </div>
                </td>
                <td><small class="text-secondary">{{ $wd->observed_at ? $wd->observed_at->diffForHumans() : '-' }}</small></td>
                <td class="text-center">
                    <a href="{{ route('countries.show', $wd->negara?->code) }}" class="btn-neo btn-neo-yellow py-1 px-2 small" style="font-size: 0.75rem;">Detail</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center text-secondary py-4">Data cuaca belum tersedia. Harap jalankan sync command di terminal.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="d-flex justify-content-center">
    {{ $weatherData->appends(request()->query())->links('pagination::bootstrap-5') }}
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    var map = L.map('weatherMap').setView([15, 20], 2);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var weatherPoints = @json($allWeather);
    
    weatherPoints.forEach(function(item) {
        if (item.negara && item.negara.latitude && item.negara.longitude) {
            var color = 'var(--neo-lime)';
            if (item.storm_risk >= 60) {
                color = 'var(--neo-pink)';
            } else if (item.storm_risk >= 30) {
                color = 'var(--neo-yellow)';
            }

            var markerIcon = L.divIcon({
                className: 'weather-marker',
                html: '<div class="weather-marker-dot" style="background-color:' + color + ';"></div>',
                iconSize: [14, 14], iconAnchor: [7, 7]
            });

            L.marker([item.negara.latitude, item.negara.longitude], {icon: markerIcon}).addTo(map)
                .bindPopup(
                    '<strong>🌤️ ' + item.negara.name + '</strong><br>' +
                    'Cuaca: <strong>' + (item.condition || 'Cerah') + '</strong><br>' +
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
