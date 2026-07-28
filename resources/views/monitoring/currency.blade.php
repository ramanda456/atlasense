@extends('layouts.app')

@section('title', 'Nilai Tukar Mata Uang')
@section('page-title', 'Monitoring — Nilai Tukar & Analisis Kurs')

@section('content')
<div class="row g-4 mb-4">
    <!-- Currency Trend Analysis Widget -->
    <div class="col-md-12">
        <div class="as-card">
            <div class="as-card-header">
                <h6>📈 Dashboard Analisis Tren Nilai Tukar (vs USD)</h6>
            </div>
            <div class="as-card-body">
                <div class="mb-3">
                    <label class="form-label text-secondary small">Pilih Mata Uang Target:</label>
                    <select id="trendCurrencySelect" class="as-select">
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
                
                <div style="position: relative; height: 280px; width: 100%;">
                    <div id="chartLoading" class="position-absolute top-50 start-50 translate-middle text-center" style="display:none; z-index: 10;">
                        <span class="loading-spinner"></span>
                        <p class="text-muted small mt-2">Memuat tren kurs...</p>
                    </div>
                    <div id="chartErrorMessage" class="position-absolute top-50 start-50 translate-middle text-center text-danger small w-75" style="display:none; z-index: 20; background-color: var(--atlas-danger-light); padding: 12px; border: 1px solid var(--border-color); border-radius: 8px;">
                        ⚠️ Gagal mengambil data historis dari Frankfurter API. Pastikan Anda terhubung ke internet.
                    </div>
                    <canvas id="currencyTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="as-card mb-4">
    <div class="as-card-header">
        <h6>💱 Cari Mata Uang</h6>
    </div>
    <div class="as-card-body">
        <form method="GET" action="{{ route('monitoring.currency') }}" class="row g-3">
            <div class="col-md-9">
                <input type="text" name="search" class="as-input" placeholder="Cari berdasarkan kode mata uang (contoh: IDR, EUR, CNY)..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3 d-grid">
                <button type="submit" class="btn-as-primary">Cari Kurs</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-12">
        <div class="as-card">
            <div class="as-card-header">
                <h6>📋 Daftar Nilai Tukar Mata Uang Terhadap USD</h6>
            </div>
            <div class="as-card-body p-0">
                <div class="table-responsive">
                    <table class="as-table">
                        <thead>
                            <tr>
                                <th>Kode Mata Uang</th>
                                <th>1 USD Setara Dengan</th>
                                <th>Sumber Data</th>
                                <th>Terakhir Sinkronisasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rates as $rate)
                            <tr>
                                <td><span class="text-white font-weight-bold" style="font-size:1.1rem;"><code>{{ $rate->currency_code }}</code></span></td>
                                <td>
                                    <strong style="font-size: 1.1rem;">{{ number_format($rate->rate_to_usd, 4) }}</strong> {{ $rate->currency_code }}
                                </td>
                                <td>
                                    @if($rate->source === 'exchangerate')
                                        <span class="badge bg-primary">ExchangeRate-API (Realtime)</span>
                                    @else
                                        <span class="badge bg-purple" style="background:#8b5cf6;">Frankfurter API (Historical)</span>
                                    @endif
                                </td>
                                <td><small class="text-muted">{{ $rate->fetched_at ? $rate->fetched_at->diffForHumans() : '-' }}</small></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Data kurs mata uang tidak ditemukan. Harap jalankan sync command `php artisan atlasense:sync-currency`.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-3 d-flex justify-content-center">
    {{ $rates->links('pagination::bootstrap-5') }}
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    var trendChart = null;

    function loadCurrencyTrend(currencyCode) {
        $('#chartLoading').show();
        $('#chartErrorMessage').hide();
        $('#currencyTrendChart').css('opacity', 0.3);
        
        // Buat range tanggal 6 bulan ke belakang
        var endDate = new Date().toISOString().slice(0, 10);
        var startDate = new Date();
        startDate.setMonth(startDate.getMonth() - 6);
        var startDateStr = startDate.toISOString().slice(0, 10);

        var url = 'https://api.frankfurter.dev/v2/' + startDateStr + '..' + endDate + '?base=USD&symbols=' + currencyCode;

        $.ajax({
            url: url,
            method: 'GET',
            success: function(response) {
                $('#chartLoading').hide();
                $('#chartErrorMessage').hide();
                $('#currencyTrendChart').css('opacity', 1).show();
                
                var dates = [];
                var values = [];

                if (response.rates) {
                    Object.keys(response.rates).forEach(function(date) {
                        dates.push(date);
                        values.push(response.rates[date][currencyCode]);
                    });
                }

                // Render Chart
                var ctx = document.getElementById('currencyTrendChart').getContext('2d');
                
                if (trendChart) {
                    trendChart.destroy();
                }

                trendChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: dates,
                        datasets: [{
                            label: 'Nilai Tukar 1 USD ke ' + currencyCode,
                            data: values,
                            borderColor: '#06b6d4',
                            backgroundColor: 'rgba(6, 182, 212, 0.05)',
                            borderWidth: 2.5,
                            pointRadius: 0, // Sembunyikan titik dot agar clean
                            tension: 0.1,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                grid: { color: 'rgba(255, 255, 255, 0.05)' },
                                ticks: { color: '#94a3b8' }
                            },
                            x: {
                                grid: { color: 'rgba(255, 255, 255, 0.05)' },
                                ticks: { color: '#94a3b8', maxTicksLimit: 12 }
                            }
                        },
                        plugins: {
                            legend: {
                                labels: { color: '#e2e8f0' }
                            }
                        }
                    }
                });
            },
            error: function() {
                $('#chartLoading').hide();
                $('#currencyTrendChart').hide();
                $('#chartErrorMessage').show();
            }
        });
    }

    // Load trend awal (IDR)
    loadCurrencyTrend('IDR');

    // Handle currency trend selector change
    $('#trendCurrencySelect').on('change', function() {
        var code = $(this).val();
        loadCurrencyTrend(code);
    });
});
</script>
@endsection
