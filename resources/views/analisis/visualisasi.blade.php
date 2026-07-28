@extends('layouts.utama')

@section('title', 'Visualisasi Data')
@section('page-title', 'Visualisasi Data')

@section('content')
<div class="neo-box">
    <div class="card-header-yellow">
        <h4 class="card-header-title text-dark">PILIH NEGARA UNTUK ANALISIS TREN</h4>
    </div>
    <form method="GET" action="{{ route('analysis.visualization') }}">
        <div class="row g-3">
            <div class="col-md-9">
                <select name="country_id" class="neo-select m-0" required>
                    <option value="">-- Pilih Negara --</option>
                    @foreach($countries as $c)
                        <option value="{{ $c->id }}" {{ $selectedCountry?->id == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->code }})</option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-3">
                <button type="submit" class="btn-neo btn-neo-lime w-100 py-3 fw-bold">TAMPILKAN TREN</button>
            </div>
        </div>
    </form>
</div>

@if($selectedCountry)
<div class="row g-4">
    <!-- GDP and Inflation Trend -->
    <div class="col-md-6">
        <div class="neo-box">
            <div class="card-header-blue">
                <h5 class="card-header-title">TREN GDP & INFLASI HISTORIS (WORLD BANK)</h5>
            </div>
            <div style="position: relative; height: 260px; width: 100%;">
                <canvas id="econTrendChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Risk Trend -->
    <div class="col-md-6">
        <div class="neo-box">
            <div class="card-header-pink">
                <h5 class="card-header-title text-dark">RIWAYAT SKOR RISIKO RANTAI PASOK</h5>
            </div>
            <div style="position: relative; height: 260px; width: 100%;">
                <canvas id="riskTrendChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Currency rate trend if code exists -->
    @if($selectedCountry->currency_code)
    <div class="col-md-12">
        <div class="neo-box">
            <div class="card-header-purple">
                <h5 class="card-header-title">TREN HISTORI KURS KEPATUHAN ({{ $selectedCountry->currency_code }} / USD)</h5>
            </div>
            <div style="position: relative; height: 260px; width: 100%;">
                <canvas id="currencyHistoryChart"></canvas>
            </div>
        </div>
    </div>
    @endif
</div>
@endif
@endsection

@section('scripts')
@if($selectedCountry)
<script>
$(document).ready(function() {
    // 1. GDP & Inflation Chart
    var econCtx = document.getElementById('econTrendChart').getContext('2d');
    var econData = @json($economicData);
    
    var labelYears = econData.map(item => item.year);
    var gdpData = econData.map(item => item.gdp ? item.gdp / 1000000000 : 0); // GDP dalam Miliar USD
    var inflationData = econData.map(item => item.inflation || 0);

    new Chart(econCtx, {
        type: 'line',
        data: {
            labels: labelYears,
            datasets: [
                {
                    label: 'GDP (Miliar USD)',
                    data: gdpData,
                    borderColor: '#38BDF8',
                    backgroundColor: 'rgba(56, 189, 248, 0.05)',
                    borderWidth: 3,
                    yAxisID: 'y'
                },
                {
                    label: 'Inflasi (%)',
                    data: inflationData,
                    borderColor: '#F472B6',
                    backgroundColor: 'rgba(244, 114, 182, 0.05)',
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

    // 2. Risk Score Trend Chart
    var riskCtx = document.getElementById('riskTrendChart').getContext('2d');
    var riskData = @json($riskData);
    
    var labelRisks = riskData.map((item, idx) => 'Hitung #' + (idx+1));
    var scores = riskData.map(item => item.total_score);

    new Chart(riskCtx, {
        type: 'line',
        data: {
            labels: labelRisks,
            datasets: [{
                label: 'Skor Risiko Terbobot',
                data: scores,
                borderColor: '#FBBF24',
                backgroundColor: 'rgba(251, 191, 36, 0.1)',
                borderWidth: 3,
                tension: 0.2,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    min: 0,
                    max: 100,
                    grid: { color: 'rgba(0,0,0,0.05)' },
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

    // 3. Currency Rate Trend Chart
    @if($selectedCountry->currency_code)
        var currCtx = document.getElementById('currencyHistoryChart').getContext('2d');
        var currData = @json($currencyData);
        
        var labelDates = currData.map(item => item.rate_date ? item.rate_date.slice(0,10) : '');
        var rates = currData.map(item => item.rate);

        new Chart(currCtx, {
            type: 'line',
            data: {
                labels: labelDates,
                datasets: [{
                    label: 'Nilai Tukar 1 USD ke {{ $selectedCountry->currency_code }}',
                    data: rates,
                    borderColor: '#C084FC',
                    backgroundColor: 'rgba(192, 132, 252, 0.05)',
                    borderWidth: 3,
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { color: '#000' }
                    },
                    x: {
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { color: '#000', maxTicksLimit: 12 }
                    }
                },
                plugins: {
                    legend: {
                        labels: { color: '#000', font: { family: 'Space Grotesk', weight: '600' } }
                    }
                }
            }
        });
    @endif
});
</script>
@endif
@endsection
