@extends('layouts.utama')

@section('title', $country->name)
@section('page-title', 'Detail Analisis: ' . $country->name)

@section('content')
@if(session('success'))
    <div class="neo-alert neo-alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="neo-alert neo-alert-danger">{{ session('error') }}</div>
@endif

<div class="row g-4 mb-4">
    <!-- Country Info Card -->
    <div class="col-md-4">
        <div class="neo-box h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center justify-content-between mb-4">
                    @if($country->flag_url)
                        <img src="{{ $country->flag_url }}" alt="Flag" style="width: 80px; border: 1px solid var(--border-color); box-shadow: 0 1px 3px rgba(168,155,133,0.1); border-radius: 4px;">
                    @endif
                    <button id="btnWatchlist" class="btn-neo {{ $isWatched ? 'btn-neo-pink' : 'btn-neo-white' }}" style="padding: 6px 12px;">
                        <span>⭐</span> {{ $isWatched ? 'Pantau Aktif' : 'Pantau' }}
                    </button>
                </div>

                <h3 class="fw-bold mb-1">{{ $country->name }}</h3>
                <p class="text-secondary small mb-3">Nama Resmi: <span class="fw-bold">{{ $country->official_name ?? $country->name }}</span></p>

                <div class="border-top border-dark pt-3 d-flex flex-column gap-2 small">
                    <div>Kode ISO: <span class="fw-bold">{{ $country->code }} / {{ $country->cca3 ?? '-' }}</span></div>
                    <div>Ibu Kota: <span class="fw-bold">{{ $country->capital ?? '-' }}</span></div>
                    <div>Wilayah: <span class="fw-bold">{{ $country->region }} ({{ $country->subregion ?? '-' }})</span></div>
                    <div>Mata Uang: <span class="fw-bold">{{ $country->currency_code }} - {{ $country->currency_name ?? '-' }}</span></div>
                    <div>Bahasa: <span class="fw-bold">{{ $country->language ?? '-' }}</span></div>
                    <div>Populasi: <span class="fw-bold">{{ number_format($country->population ?? 0) }} jiwa</span></div>
                    <div>Koordinat: <span class="fw-bold">{{ $country->latitude ?? '-' }}, {{ $country->longitude ?? '-' }}</span></div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top border-dark">
                <form action="{{ route('analysis.calculate', $country->code) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-neo btn-neo-lime w-100 fw-bold py-2">HITUNG ULANG RISIKO</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Threat Score Card -->
    <div class="col-md-8">
        <div class="neo-box h-100" style="background-color: #ffffff;">
            <div class="card-header-pink">
                <h4 class="card-header-title text-dark">STATUS RISIKO RANTAI PASOK SAAT INI</h4>
            </div>

            @if($country->latestRisk)
                <div class="row align-items-center mb-4">
                    <div class="col-md-6 text-center border-end border-dark border-2">
                        <span class="text-secondary small text-uppercase fw-bold">Skor Risiko Terbobot</span>
                        <h1 class="display-2 fw-bold my-1">{{ $country->latestRisk->total_score }}</h1>
                        <span class="neo-badge {{ $country->latestRisk->risk_level === 'Tinggi' ? 'neo-badge-pink' : ($country->latestRisk->risk_level === 'Sedang' ? 'neo-badge-yellow' : 'neo-badge-lime') }} fs-5 py-2 px-4">
                            RISIKO {{ strtoupper($country->latestRisk->risk_level) }}
                        </span>
                        <div class="text-secondary small mt-3">Dihitung pada: {{ $country->latestRisk->calculated_at ? $country->latestRisk->calculated_at->translatedFormat('d M Y, H:i') : '-' }}</div>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3 text-center">BREAKDOWN NORMALISASI KOMPONEN</h6>
                        <div class="d-flex flex-column gap-2 px-3">
                            <div>
                                <div class="d-flex justify-content-between small fw-bold mb-1">
                                    <span>🌤️ Cuaca (Bobot 25%)</span>
                                    <span>{{ $country->latestRisk->weather_score }} / 100</span>
                                </div>
                                <div class="progress border border-dark border-2 rounded-0" style="height: 12px; background: #fff;">
                                    <div class="progress-bar bg-warning border-end border-dark" role="progressbar" style="width: {{ $country->latestRisk->weather_score }}%;"></div>
                                </div>
                            </div>
                            <div>
                                <div class="d-flex justify-content-between small fw-bold mb-1">
                                    <span>📈 Inflasi (Bobot 25%)</span>
                                    <span>{{ $country->latestRisk->inflation_score }} / 100</span>
                                </div>
                                <div class="progress border border-dark border-2 rounded-0" style="height: 12px; background: #fff;">
                                    <div class="progress-bar bg-danger border-end border-dark" role="progressbar" style="width: {{ $country->latestRisk->inflation_score }}%;"></div>
                                </div>
                            </div>
                            <div>
                                <div class="d-flex justify-content-between small fw-bold mb-1">
                                    <span>💸 Mata Uang (Bobot 20%)</span>
                                    <span>{{ $country->latestRisk->currency_score }} / 100</span>
                                </div>
                                <div class="progress border border-dark border-2 rounded-0" style="height: 12px; background: #fff;">
                                    <div class="progress-bar bg-info border-end border-dark" role="progressbar" style="width: {{ $country->latestRisk->currency_score }}%;"></div>
                                </div>
                            </div>
                            <div>
                                <div class="d-flex justify-content-between small fw-bold mb-1">
                                    <span>📰 Sentimen Berita (Bobot 30%)</span>
                                    <span>{{ $country->latestRisk->news_score }} / 100</span>
                                </div>
                                <div class="progress border border-dark border-2 rounded-0" style="height: 12px; background: #fff;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $country->latestRisk->news_score }}%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <h5 class="text-secondary">Belum ada perhitungan risiko untuk negara ini.</h5>
                    <p class="text-secondary small">Klik tombol "Hitung Ulang Risiko" untuk memproses data dari API eksternal.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Economic Trend Chart -->
    <div class="col-md-6">
        <div class="neo-box">
            <div class="card-header-yellow">
                <h4 class="card-header-title">RIWAYAT PERKEMBANGAN EKONOMI (GDP & INFLASI)</h4>
            </div>
            <div style="position: relative; height: 280px; width: 100%;">
                <canvas id="economicChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Map & Ports -->
    <div class="col-md-6">
        <div class="neo-box">
            <div class="card-header-blue">
                <h4 class="card-header-title">PELABUHAN & PETA</h4>
            </div>
            <div id="countryMap" class="mb-3" style="height: 200px; border-radius: 6px; border: 1px solid var(--border-color); box-shadow: 0 1px 3px rgba(168,155,133,0.1);"></div>
            
            <h6 class="fw-bold mb-2">Daftar Pelabuhan Logistik:</h6>
            <ul class="list-group rounded-3">
                @forelse($country->ports as $port)
                    <li class="list-group-item mb-1 small fw-bold bg-white" style="border: 1px solid var(--border-color); box-shadow: 0 1px 3px rgba(168, 155, 133, 0.1); border-radius: 6px;">
                        {{ $port->name }} (<code>{{ $port->unlocode ?? $port->code }}</code>) - {{ $port->city ?? 'Laut' }}
                    </li>
                @empty
                    <li class="list-group-item text-secondary small" style="border: 1px solid var(--border-color); border-radius: 6px;">Tidak ada data pelabuhan untuk negara ini.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Climate and weather detail -->
    <div class="col-md-6">
        <div class="neo-box">
            <div class="card-header-purple">
                <h4 class="card-header-title text-dark">DATA CUACA REAL-TIME SAAT INI</h4>
            </div>
            @if($country->latestWeather)
                <div class="table-responsive neo-table-wrapper border-0 mb-0">
                    <table class="neo-table">
                        <tbody>
                            <tr>
                                <td><strong>Temperatur Aktual</strong></td>
                                <td>{{ $country->latestWeather->temperature }} °C (Terasa seperti {{ $country->latestWeather->apparent_temperature ?? 'N/A' }} °C)</td>
                            </tr>
                            <tr>
                                <td><strong>Kelembapan Udara</strong></td>
                                <td>{{ $country->latestWeather->humidity ?? 'N/A' }} %</td>
                            </tr>
                            <tr>
                                <td><strong>Kecepatan & Hembusan Angin</strong></td>
                                <td>{{ $country->latestWeather->wind_speed }} km/jam (Hembusan {{ $country->latestWeather->wind_gust ?? 'N/A' }} km/jam)</td>
                            </tr>
                            <tr>
                                <td><strong>Curah & Peluang Hujan</strong></td>
                                <td>{{ $country->latestWeather->precipitation }} mm (Peluang {{ $country->latestWeather->precipitation_probability ?? 'N/A' }} %)</td>
                            </tr>
                            <tr>
                                <td><strong>Kondisi Cuaca</strong></td>
                                <td><span class="neo-badge neo-badge-blue">{{ $country->latestWeather->condition ?? 'Cerah' }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Tingkat Risiko Badai (Storm Risk)</strong></td>
                                <td><strong>{{ $country->latestWeather->storm_risk }} %</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center text-secondary mb-0 py-3">Belum ada data cuaca realtime. Silakan jalankan hitung ulang risiko.</p>
            @endif
        </div>
    </div>

    <!-- Related News -->
    <div class="col-md-6">
        <div class="neo-box">
            <div class="card-header-lime">
                <h4 class="card-header-title">INTELIJEN BERITA TERBARU NEGARA</h4>
            </div>
            <div class="d-flex flex-column gap-3">
                @forelse($news as $article)
                    <div class="p-3 bg-white" style="border: 1px solid var(--border-color); box-shadow: 0 1px 3px rgba(168, 155, 133, 0.1); border-radius: 6px;">
                        <span class="neo-badge {{ $article->sentiment === 'Positif' ? 'neo-badge-lime' : ($article->sentiment === 'Negatif' ? 'neo-badge-pink' : 'neo-badge-blue') }} mb-2">
                            {{ $article->sentiment }}
                        </span>
                        <h6 class="fw-bold mb-1">{{ $article->title }}</h6>
                        <div class="text-secondary small">{{ $article->source }} • {{ $article->published_at ? $article->published_at->diffForHumans() : '-' }}</div>
                    </div>
                @empty
                    <p class="text-center text-secondary mb-0 py-3">Belum ada berita ter-cache untuk negara ini.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // 1. Toggle Watchlist (Daftar Pantauan)
    $('#btnWatchlist').on('click', function() {
        showLoader();
        $.ajax({
            url: "{{ route('watchlist.toggle') }}",
            method: 'POST',
            data: {
                country_id: "{{ $country->id }}"
            },
            success: function(res) {
                hideLoader();
                if (res.success) {
                    if (res.status === 'added') {
                        $('#btnWatchlist').removeClass('btn-neo-white').addClass('btn-neo-pink').html('<span>⭐</span> Pantau Aktif');
                    } else {
                        $('#btnWatchlist').removeClass('btn-neo-pink').addClass('btn-neo-white').html('<span>⭐</span> Pantau');
                    }
                }
            },
            error: function() {
                hideLoader();
                alert('Gagal mengubah daftar pantauan.');
            }
        });
    });

    // 2. Leaflet Map
    @if($country->latitude && $country->longitude)
        var map = L.map('countryMap').setView([{{ $country->latitude }}, {{ $country->longitude }}], 5);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        L.marker([{{ $country->latitude }}, {{ $country->longitude }}]).addTo(map)
            .bindPopup('<strong>{{ $country->name }}</strong><br>Centerpoint')
            .openPopup();

        // Tambah marker pelabuhan jika ada koordinat
        @foreach($country->ports as $port)
            @if($port->latitude && $port->longitude)
                L.circleMarker([{{ $port->latitude }}, {{ $port->longitude }}], {
                    radius: 8,
                    fillColor: '#38BDF8',
                    color: '#000',
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 0.8
                }).addTo(map).bindPopup('🚢 <strong>{{ $port->name }}</strong>');
            @endif
        @endforeach
    @endif

    // 3. Chart.js
    var ctxEcon = document.getElementById('economicChart').getContext('2d');
    var economicHistory = @json($country->economics);

    var labelYears = economicHistory.map(item => item.year);
    var gdpData = economicHistory.map(item => item.gdp ? item.gdp / 1000000000 : 0); // GDP dalam Miliar USD
    var inflationData = economicHistory.map(item => item.inflation || 0);

    new Chart(ctxEcon, {
        type: 'line',
        data: {
            labels: labelYears,
            datasets: [
                {
                    label: 'GDP (Miliar USD)',
                    data: gdpData,
                    borderColor: '#38BDF8',
                    backgroundColor: 'rgba(56, 189, 248, 0.1)',
                    borderWidth: 3,
                    yAxisID: 'y'
                },
                {
                    label: 'Inflasi (%)',
                    data: inflationData,
                    borderColor: '#F472B6',
                    backgroundColor: 'rgba(244, 114, 182, 0.1)',
                    borderWidth: 3,
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
                    display: true,
                    position: 'left',
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: { color: '#000' }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: { drawOnChartArea: false },
                    ticks: { color: '#000' }
                },
                x: {
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: { color: '#000' }
                }
            },
            plugins: {
                legend: {
                    labels: { color: '#000', font: { family: 'Space Grotesk', weight: '600' } }
                }
            }
        }
    });
});
</script>
@endsection
