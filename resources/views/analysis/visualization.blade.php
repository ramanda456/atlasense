@extends('layouts.app')

@section('title', 'Visualisasi Data Global')
@section('page-title', 'Intelijen Global — Visualisasi Peta Risiko')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-12">
        <div class="as-card">
            <div class="as-card-header">
                <h6>🗺️ Sebaran Risiko Rantai Pasok Global (Peta Interaktif)</h6>
                <div class="d-flex gap-3 text-secondary small">
                    <div><span style="display:inline-block; width:12px; height:12px; border-radius:50%; background:#ef4444; margin-right:4px;"></span> Tinggi (>=65)</div>
                    <div><span style="display:inline-block; width:12px; height:12px; border-radius:50%; background:#f59e0b; margin-right:4px;"></span> Sedang (35-64)</div>
                    <div><span style="display:inline-block; width:12px; height:12px; border-radius:50%; background:#10b981; margin-right:4px;"></span> Rendah (<35)</div>
                </div>
            </div>
            <div class="as-card-body p-2">
                <div id="visualMap" style="height: 520px;"></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-12">
        <div class="as-card">
            <div class="as-card-header">
                <h6>📊 Grafik Perbandingan Risiko Tiap Negara</h6>
            </div>
            <div class="as-card-body">
                <div style="position: relative; height: 350px;">
                    <canvas id="globalRiskBarChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Inisialisasi Peta
    var map = L.map('visualMap').setView([20, 0], 2);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 18,
    }).addTo(map);

    var countriesData = @json($visualizationData);
    
    countriesData.forEach(function(country) {
        if (country.latitude && country.longitude) {
            // Tentukan warna marker
            var color = '#10b981'; // Green
            if (country.risk_score >= 65) {
                color = '#ef4444'; // Red
            } else if (country.risk_score >= 35) {
                color = '#f59e0b'; // Orange
            }

            var markerIcon = L.divIcon({
                className: 'risk-marker',
                html: '<div style="background-color:' + color + '; width:14px; height:14px; border-radius:50%; border:2px solid white; box-shadow: 0 0 6px rgba(0,0,0,0.5);"></div>',
                iconSize: [14, 14], iconAnchor: [7, 7]
            });

            L.marker([country.latitude, country.longitude], {icon: markerIcon}).addTo(map)
                .bindPopup(
                    '<strong>' + country.name + ' (' + country.code + ')</strong><br>' +
                    'Risk Score: <strong>' + country.risk_score + '</strong><br>' +
                    'Status: <span style="font-weight:600; color:' + color + ';">' + country.risk_status + '</span><br><br>' +
                    '<small style="color:#94a3b8;">Breakdown Score:</small><br>' +
                    '<small>🌤️ Cuaca: ' + country.weather_score + '</small><br>' +
                    '<small>📈 Inflasi: ' + country.inflation_score + '</small><br>' +
                    '<small>📰 Sentimen: ' + country.sentiment_score + '</small><br><br>' +
                    '<a href="/negara/' + country.code + '" class="btn-as-outline" style="font-size:0.7rem; padding:2px 8px; display:inline-block; width:100%; text-align:center;">📊 Buka Detail</a>'
                );
        }
    });

    // Bar Chart
    var labels = [];
    var scores = [];
    var backgroundColors = [];
    
    // Sort and take top 15 highest risk countries for chart display
    var sorted = [...countriesData].sort((a,b) => b.risk_score - a.risk_score).slice(0, 15);
    
    sorted.forEach(function(item) {
        labels.push(item.name);
        scores.push(item.risk_score);
        if (item.risk_score >= 65) {
            backgroundColors.push('#ef4444');
        } else if (item.risk_score >= 35) {
            backgroundColors.push('#f59e0b');
        } else {
            backgroundColors.push('#10b981');
        }
    });

    var ctx = document.getElementById('globalRiskBarChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Skor Risiko Rantai Pasok',
                data: scores,
                backgroundColor: backgroundColors,
                borderColor: 'transparent',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    grid: { color: 'rgba(255,255,255,0.05)' },
                    ticks: { color: '#94a3b8' }
                },
                x: {
                    grid: { color: 'rgba(255,255,255,0.05)' },
                    ticks: { color: '#94a3b8', autoSkip: false, maxRotation: 45, minRotation: 45 }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
});
</script>
@endsection
