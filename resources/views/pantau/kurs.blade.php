@extends('layouts.utama')

@section('title', 'Dampak Nilai Tukar')
@section('page-title', 'Dampak Nilai Tukar')

@section('content')
<!-- Currency Summary Cards -->
<div class="row g-3 mb-3" id="currencySummaryCards">
    <div class="col-md-3 col-6">
        <div class="neo-box p-3 text-center mb-0" style="min-height: auto;">
            <div class="text-secondary small fw-bold mb-1">Nilai Tukar Terkini</div>
            <div id="summaryCurrentRate" class="fw-bold" style="font-size: 1.5rem; color: var(--text-dark);">-</div>
            <div id="summaryCurrentLabel" class="text-secondary" style="font-size: 0.75rem;">1 USD = ? IDR</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="neo-box p-3 text-center mb-0" style="min-height: auto;">
            <div class="text-secondary small fw-bold mb-1">Perubahan Terakhir</div>
            <div id="summaryChange" class="fw-bold" style="font-size: 1.5rem;">-</div>
            <div class="text-secondary" style="font-size: 0.75rem;">Dibanding hari sebelumnya</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="neo-box p-3 text-center mb-0" style="min-height: auto;">
            <div class="text-secondary small fw-bold mb-1">Kurs Tertinggi</div>
            <div id="summaryHighest" class="fw-bold" style="font-size: 1.5rem; color: #C57D7D;">-</div>
            <div id="summaryHighestDate" class="text-secondary" style="font-size: 0.75rem;">-</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="neo-box p-3 text-center mb-0" style="min-height: auto;">
            <div class="text-secondary small fw-bold mb-1">Kurs Terendah</div>
            <div id="summaryLowest" class="fw-bold" style="font-size: 1.5rem; color: #92B48F;">-</div>
            <div id="summaryLowestDate" class="text-secondary" style="font-size: 0.75rem;">-</div>
        </div>
    </div>
</div>

<!-- Currency Analysis Dashboard Widget -->
<div class="neo-box">
    <div class="card-header-yellow">
        <h4 class="card-header-title">ANALISIS TREN HISTORIS KURS MATA UANG (vs USD)</h4>
    </div>
    
    <div class="mb-4">
        <label class="form-label fw-bold">Pilih Mata Uang Target:</label>
        <select id="currencyTargetSelector" class="neo-select m-0">
            <option value="IDR" selected>Rupiah Indonesia (IDR)</option>
            <option value="EUR">Euro Uni Eropa (EUR)</option>
            <option value="CNY">Yuan China (CNY)</option>
            <option value="AUD">Dolar Australia (AUD)</option>
            <option value="JPY">Yen Jepang (JPY)</option>
            <option value="SGD">Dolar Singapura (SGD)</option>
            <option value="GBP">Poundsterling Inggris (GBP)</option>
            <option value="INR">Rupee India (INR)</option>
            <option value="MYR">Ringgit Malaysia (MYR)</option>
            <option value="THB">Baht Thailand (THB)</option>
            <option value="TRY">Lira Turki (TRY)</option>
            <option value="BRL">Real Brasil (BRL)</option>
            <option value="CAD">Dolar Kanada (CAD)</option>
            <option value="KRW">Won Korea (KRW)</option>
            <option value="ZAR">Rand Afrika Selatan (ZAR)</option>
        </select>
    </div>
    
    <div style="position: relative; height: 300px; width: 100%;">
        <div id="chartLoadingIndicator" class="position-absolute top-50 start-50 translate-middle text-center" style="display:none; z-index: 20;">
            <div class="spinner-border text-dark spinner-border-sm" role="status"></div>
            <p class="text-secondary small mt-2 mb-0">Memuat tren kurs...</p>
        </div>
        <div id="chartErrorMessage" class="position-absolute top-50 start-50 translate-middle text-center text-danger small w-75" style="display:none; z-index: 20; background-color: var(--atlas-danger-light); padding: 12px; border: 1px solid var(--border-color); border-radius: 8px;">
            Data historis kurs belum tersedia. Silakan sinkronkan data terlebih dahulu.
        </div>
        <canvas id="currencyTrendChart"></canvas>
    </div>
</div>

<!-- Search Widget -->
<div class="neo-box">
    <div class="card-header-blue">
        <h4 class="card-header-title">CARI DATA KURS MATA UANG</h4>
    </div>
    <form method="GET" action="{{ route('monitoring.currency') }}" class="row g-3">
        <div class="col-md-9">
            <input type="text" name="search" class="neo-input m-0" placeholder="Masukkan kode mata uang (contoh: IDR, EUR, JPY)..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn-neo btn-neo-lime w-100 py-3 fw-bold">CARI KURS</button>
        </div>
    </form>
</div>

<!-- Rates Table -->
<div class="neo-table-wrapper">
    <table class="neo-table">
        <thead>
            <tr>
                <th>Base Currency</th>
                <th>Target Currency</th>
                <th>Nilai Tukar (Rate)</th>
                <th>Perubahan Kurs (%)</th>
                <th>Sumber Data</th>
                <th>Terakhir Sinkron</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rates as $rate)
            <tr>
                <td><strong>{{ $rate->base_currency }}</strong></td>
                <td><span class="neo-badge neo-badge-purple">{{ $rate->target_currency }}</span></td>
                <td><strong class="fs-5">{{ number_format($rate->rate, 4) }}</strong> {{ $rate->target_currency }}</td>
                <td>
                    @if($rate->change_percent > 0)
                        <span class="text-danger fw-bold">▲ +{{ number_format($rate->change_percent, 4) }}%</span>
                    @elseif($rate->change_percent < 0)
                        <span class="text-success fw-bold">▼ {{ number_format($rate->change_percent, 4) }}%</span>
                    @else
                        <span class="text-secondary fw-bold">0.00%</span>
                    @endif
                </td>
                <td>
                    <span class="badge bg-dark border border-dark text-white">{{ $rate->source }}</span>
                </td>
                <td><small class="text-secondary">{{ $rate->recorded_at ? $rate->recorded_at->diffForHumans() : '-' }}</small></td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-secondary py-4">Data kurs mata uang belum tersedia. Harap jalankan sync command di terminal.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="d-flex justify-content-center">
    {{ $rates->appends(request()->query())->links('pagination::bootstrap-5') }}
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    var trendChart = null;

    function renderCurrencyChart(targetCode) {
        $('#chartLoadingIndicator').show();
        $('#chartErrorMessage').hide();
        $('#currencyTrendChart').css('opacity', 0.3);

        $.ajax({
            url: '{{ route("monitoring.currency.chart") }}',
            method: 'GET',
            data: { target: targetCode },
            success: function(res) {
                $('#chartLoadingIndicator').hide();
                $('#currencyTrendChart').css('opacity', 1).show();

                if (!res.success || res.dates.length === 0) {
                    $('#currencyTrendChart').hide();
                    $('#chartErrorMessage').html('Data historis kurs untuk <strong>' + targetCode + '</strong> belum tersedia di database. Silakan jalankan <code>php artisan sync:kurs</code> terlebih dahulu.').show();
                    // Reset summary cards
                    $('#summaryCurrentRate, #summaryChange, #summaryHighest, #summaryLowest').text('-');
                    $('#summaryCurrentLabel').text('1 USD = ? ' + targetCode);
                    $('#summaryHighestDate, #summaryLowestDate').text('-');
                    return;
                }

                $('#chartErrorMessage').hide();

                // === Populate Summary Cards ===
                var rates = res.rates.map(Number);
                var lastRate = rates[rates.length - 1];
                var lastChange = res.changes.length > 0 ? Number(res.changes[res.changes.length - 1]) : 0;
                var maxRate = Math.max.apply(null, rates);
                var minRate = Math.min.apply(null, rates);
                var maxIdx = rates.indexOf(maxRate);
                var minIdx = rates.indexOf(minRate);

                // Nilai Tukar Terkini
                $('#summaryCurrentRate').text(Number(lastRate).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 4}));
                $('#summaryCurrentLabel').text('1 USD = ' + Number(lastRate).toLocaleString('id-ID', {maximumFractionDigits: 2}) + ' ' + targetCode);

                // Perubahan Terakhir
                if (lastChange > 0) {
                    $('#summaryChange').html('<span style="color:#C57D7D;">▲ +' + lastChange.toFixed(4) + '%</span>');
                } else if (lastChange < 0) {
                    $('#summaryChange').html('<span style="color:#92B48F;">▼ ' + lastChange.toFixed(4) + '%</span>');
                } else {
                    $('#summaryChange').html('<span class="text-secondary">0.00%</span>');
                }

                // Kurs Tertinggi & Terendah
                $('#summaryHighest').text(Number(maxRate).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 4}));
                $('#summaryHighestDate').text('Pada ' + res.dates[maxIdx]);
                $('#summaryLowest').text(Number(minRate).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 4}));
                $('#summaryLowestDate').text('Pada ' + res.dates[minIdx]);

                var ctx = document.getElementById('currencyTrendChart').getContext('2d');
                if (trendChart) {
                    trendChart.destroy();
                }

                trendChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: res.dates,
                        datasets: [{
                            label: 'Nilai Tukar 1 USD ke ' + targetCode,
                            data: res.rates,
                            borderColor: '#85A9C0',
                            backgroundColor: 'rgba(133, 169, 192, 0.1)',
                            borderWidth: 3,
                            pointRadius: 2,
                            pointBackgroundColor: '#85A9C0',
                            tension: 0.3,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                grid: { color: 'rgba(0,0,0,0.05)' },
                                ticks: { color: '#3E382F' }
                            },
                            x: {
                                grid: { color: 'rgba(0,0,0,0.05)' },
                                ticks: { color: '#3E382F', maxTicksLimit: 10 }
                            }
                        },
                        plugins: {
                            legend: {
                                labels: { color: '#3E382F', font: { family: 'Inter', weight: '600' } }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return targetCode + ': ' + Number(context.parsed.y).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 4});
                                    }
                                }
                            }
                        }
                    }
                });
            },
            error: function() {
                $('#chartLoadingIndicator').hide();
                $('#currencyTrendChart').hide();
                $('#chartErrorMessage').html('Gagal mengambil data historis kurs. Pastikan server Laravel berjalan.').show();
            }
        });
    }

    // Default load IDR
    renderCurrencyChart('IDR');

    // Trigger on change
    $('#currencyTargetSelector').on('change', function() {
        renderCurrencyChart($(this).val());
    });
});
</script>
@endsection

