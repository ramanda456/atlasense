@extends('layouts.utama')

@section('title', 'Dasbor Utama')
@section('page-title', 'Dasbor Utama')

@section('content')
<div class="row g-4 mb-4">
    <!-- Stat Cards -->
    <div class="col-md-3">
        <div class="neo-box neo-box-hover text-center" style="background-color: var(--atlas-land-light); color: var(--text-primary);">
            <h4 class="fw-bold m-0">NEGARA</h4>
            <h1 class="display-4 fw-bold my-2">{{ $negaraCount }}</h1>
            <span class="small text-secondary">Terkoneksi ke sistem</span>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="neo-box neo-box-hover text-center" style="background-color: var(--atlas-ocean-light); color: var(--text-primary);">
            <h4 class="fw-bold m-0">PELABUHAN</h4>
            <h1 class="display-4 fw-bold my-2">{{ $pelabuhanCount }}</h1>
            <span class="small text-secondary">World Port Index</span>
        </div>
    </div>

    <div class="col-md-3">
        <div class="neo-box neo-box-hover text-center" style="background-color: var(--atlas-danger-light); color: var(--text-primary);">
            <h4 class="fw-bold m-0">BERITA LOGISTIK</h4>
            <h1 class="display-4 fw-bold my-2">{{ $beritaCount }}</h1>
            <span class="small text-secondary opacity-75">Tersimpan di cache</span>
        </div>
    </div>

    <div class="col-md-3">
        <div class="neo-box neo-box-hover text-center" style="background-color: var(--atlas-sand-light); color: var(--text-primary);">
            <h4 class="fw-bold m-0">PANTAUAN</h4>
            <h1 class="display-4 fw-bold my-2">{{ $pantauanCount }}</h1>
            <span class="small text-secondary">Negara Anda pantau</span>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Top Risks & Recent News -->
    <div class="col-md-8">
        <!-- Top Risks -->
        <div class="neo-box">
            <div class="card-header-pink">
                <h4 class="card-header-title text-dark">NEGARA DENGAN RISIKO TERTINGGI</h4>
            </div>
            <div class="neo-table-wrapper border-0 box-shadow-none m-0">
                <table class="neo-table">
                    <thead>
                        <tr>
                            <th>Negara</th>
                            <th>Skor Risiko</th>
                            <th>Level Risiko</th>
                            <th>Waktu Analisis</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topRisks as $risk)
                        <tr>
                            <td>
                                @if($risk->negara?->flag_url)
                                    <img src="{{ $risk->negara->flag_url }}" alt="Flag" style="width: 24px; border: 1px solid #000; margin-right: 8px;">
                                @endif
                                <strong class="text-dark">{{ $risk->negara?->name ?? '-' }}</strong>
                            </td>
                            <td><strong class="fs-5">{{ $risk->total_score }}</strong> / 100</td>
                            <td>
                                <span class="neo-badge {{ $risk->risk_level === 'Tinggi' ? 'neo-badge-pink' : ($risk->risk_level === 'Sedang' ? 'neo-badge-yellow' : 'neo-badge-lime') }}">
                                    {{ $risk->risk_level }}
                                </span>
                            </td>
                            <td><small class="text-secondary">{{ $risk->created_at ? $risk->created_at->diffForHumans() : '-' }}</small></td>
                            <td class="text-center">
                                <a href="{{ route('countries.show', $risk->negara?->code) }}" class="btn-neo btn-neo-blue py-1 px-2 small" style="font-size: 0.75rem;">Detail</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-secondary py-4">Belum ada data analisis risiko. Selesaikan sinkronisasi data negara & jalankan hitung risiko terlebih dahulu.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent News -->
        <div class="neo-box">
            <div class="card-header-blue">
                <h4 class="card-header-title">INTELIJEN BERITA TERBARU</h4>
            </div>
            <div class="d-flex flex-column gap-3">
                @forelse($recentNews as $news)
                <div class="p-3 bg-white d-flex align-items-center justify-content-between" style="border: 1px solid var(--border-color); box-shadow: 0 1px 3px rgba(168, 155, 133, 0.1); border-radius: 6px;">
                    <div>
                        <span class="neo-badge {{ $news->sentiment === 'Positif' ? 'neo-badge-lime' : ($news->sentiment === 'Negatif' ? 'neo-badge-pink' : 'neo-badge-blue') }} mb-2">
                            {{ $news->sentiment }}
                        </span>
                        <h6 class="fw-bold mb-1">{{ $news->title }}</h6>
                        <span class="text-secondary small">{{ $news->source }} • {{ $news->published_at ? $news->published_at->diffForHumans() : '-' }}</span>
                    </div>
                    @if($news->url)
                        <a href="{{ $news->url }}" target="_blank" class="btn-neo btn-neo-yellow py-1 px-3 small" style="font-size: 0.75rem;">Baca</a>
                    @endif
                </div>
                @empty
                <p class="text-center text-secondary py-3 mb-0">Belum ada berita ter-cache di database.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Right Column: API Health & Distribution -->
    <div class="col-md-4">
        <!-- Distribution Chart -->
        <div class="neo-box">
            <div class="card-header-yellow">
                <h4 class="card-header-title">SEBARAN TINGKAT RISIKO</h4>
            </div>
            <div style="position: relative; height: 220px; width: 100%;">
                <canvas id="riskDistributionChart"></canvas>
            </div>
        </div>

        <!-- API Health -->
        <div class="neo-box">
            <div class="card-header-purple">
                <h4 class="card-header-title">STATUS INTEGRASI API EKSTERNAL</h4>
            </div>
            <div class="d-flex flex-column gap-3">
                @forelse($apiHealth as $log)
                <div class="p-3 bg-white" style="border: 1px solid var(--border-color); box-shadow: 0 1px 3px rgba(168, 155, 133, 0.1); border-radius: 6px;">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <strong class="text-dark">{{ $log->service }}</strong>
                        <span class="neo-badge {{ $log->success ? 'neo-badge-lime' : 'neo-badge-pink' }}">
                            {{ $log->success ? 'AKTIF' : 'ERROR' }}
                        </span>
                    </div>
                    <div class="text-secondary small">Status Code: <code>{{ $log->status_code ?? 'N/A' }}</code></div>
                    <div class="text-secondary small">Waktu Respons: <code>{{ $log->response_time_ms ?? 0 }} ms</code></div>
                </div>
                @empty
                <p class="text-center text-secondary mb-0">Belum ada log pemanggilan API.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    var ctx = document.getElementById('riskDistributionChart').getContext('2d');
    var dataLevels = @json($riskLevels);

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: Object.keys(dataLevels),
            datasets: [{
                data: Object.values(dataLevels),
                backgroundColor: ['#A3E635', '#FBBF24', '#F472B6'],
                borderColor: '#000000',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#000',
                        font: {
                            family: 'Space Grotesk',
                            weight: '600'
                        }
                    }
                }
            }
        }
    });

    // Realtime AJAX refresh every 30 seconds
    setInterval(function() {
        $.ajax({
            url: "{{ route('dashboard.live') }}",
            method: 'GET',
            success: function(res) {
                if (res.success) {
                    // Update stats
                    $('.neo-box h1').eq(0).text(res.data.counts.countries);
                    $('.neo-box h1').eq(1).text(res.data.counts.ports);
                    $('.neo-box h1').eq(2).text(res.data.counts.news);
                    $('.neo-box h1').eq(3).text(res.data.counts.watchlists);
                }
            }
        });
    }, 30000);
});
</script>
@endsection
