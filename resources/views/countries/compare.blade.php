@extends('layouts.app')

@section('title', 'Perbandingan Negara')
@section('page-title', 'Intelijen Global — Perbandingan Negara')

@section('content')
<div class="as-card mb-4">
    <div class="as-card-header">
        <h6>🔄 Pilih Dua Negara Untuk Dibandingkan</h6>
    </div>
    <div class="as-card-body">
        <form method="GET" action="{{ route('countries.compare') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label text-secondary small">Negara Pertama:</label>
                <select name="c1" class="as-select" required>
                    <option value="">-- Pilih Negara 1 --</option>
                    @foreach($countries as $c)
                        <option value="{{ $c->code }}" {{ request('c1') == $c->code ? 'selected' : '' }}>{{ $c->name }} ({{ $c->code }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label text-secondary small">Negara Kedua:</label>
                <select name="c2" class="as-select" required>
                    <option value="">-- Pilih Negara 2 --</option>
                    @foreach($countries as $c)
                        <option value="{{ $c->code }}" {{ request('c2') == $c->code ? 'selected' : '' }}>{{ $c->name }} ({{ $c->code }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-grid">
                <button type="submit" class="btn-as-primary">Bandingkan Sekarang</button>
            </div>
        </form>
    </div>
</div>

@if($country1 && $country2 && $c1Data && $c2Data)
<div class="row g-4">
    <!-- Kolom Negara 1 -->
    <div class="col-md-6">
        <div class="as-card h-100">
            <div class="as-card-header bg-dark text-white text-center">
                <h5>{{ $country1->name }}</h5>
                <span class="badge bg-secondary">{{ $country1->code }}</span>
            </div>
            <div class="as-card-body">
                <div class="text-center py-3 mb-4 rounded" style="background: var(--bg-secondary);">
                    <small class="text-muted d-block uppercase mb-1">Skor Risiko Rantai Pasok</small>
                    <div class="score-display mb-2">{{ $c1Data['risk_score'] }}</div>
                    @if($c1Data['risk_status'] === 'High Risk')
                        <span class="risk-badge bg-danger">HIGH RISK</span>
                    @elseif($c1Data['risk_status'] === 'Medium Risk')
                        <span class="risk-badge bg-warning text-dark">MEDIUM RISK</span>
                    @else
                        <span class="risk-badge bg-success">LOW RISK</span>
                    @endif
                </div>

                <div class="mb-4">
                    <h6 class="text-white small mb-3">Breakdown Risiko & Indikator:</h6>
                    
                    <div class="p-3 mb-2 rounded" style="background: var(--bg-secondary);">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-secondary">🌤️ Risiko Cuaca (30%)</small>
                            <strong class="text-info">{{ $c1Data['breakdown']['weather']['score'] }}</strong>
                        </div>
                        <small class="text-muted d-block" style="font-size: 0.75rem;">Suhu: {{ $c1Data['breakdown']['weather']['data']['temperature'] ?? 'N/A' }}°C | Angin: {{ $c1Data['breakdown']['weather']['data']['wind_speed'] ?? 'N/A' }} km/j</small>
                    </div>

                    <div class="p-3 mb-2 rounded" style="background: var(--bg-secondary);">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-secondary">📈 Risiko Inflasi (20%)</small>
                            <strong class="text-warning">{{ $c1Data['breakdown']['inflation']['score'] }}</strong>
                        </div>
                        <small class="text-muted d-block" style="font-size: 0.75rem;">Inflasi: {{ $c1Data['breakdown']['inflation']['data']['inflation'] ?? 'N/A' }}% | GDP: USD {{ isset($c1Data['breakdown']['inflation']['data']['gdp']) ? number_format($c1Data['breakdown']['inflation']['data']['gdp'] / 1e9, 2) . ' Miliar' : 'N/A' }}</small>
                    </div>

                    <div class="p-3 mb-2 rounded" style="background: var(--bg-secondary);">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-secondary">💱 Risiko Kurs (10%)</small>
                            <strong class="text-success">{{ $c1Data['breakdown']['currency']['score'] }}</strong>
                        </div>
                        <small class="text-muted d-block" style="font-size: 0.75rem;">1 USD = {{ number_format($c1Data['breakdown']['currency']['data']['rate_to_usd'], 4) }} {{ $country1->currency_code }}</small>
                    </div>

                    <div class="p-3 mb-2 rounded" style="background: var(--bg-secondary);">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-secondary">📰 Sentimen Berita (40%)</small>
                            <strong class="text-danger">{{ $c1Data['breakdown']['sentiment']['score'] }}</strong>
                        </div>
                        <small class="text-muted d-block" style="font-size: 0.75rem;">Positif: {{ $c1Data['breakdown']['sentiment']['positive_count'] }} | Negatif: {{ $c1Data['breakdown']['sentiment']['negative_count'] }} (Dari {{ $c1Data['breakdown']['sentiment']['total_news'] }} berita)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Negara 2 -->
    <div class="col-md-6">
        <div class="as-card h-100">
            <div class="as-card-header bg-dark text-white text-center">
                <h5>{{ $country2->name }}</h5>
                <span class="badge bg-secondary">{{ $country2->code }}</span>
            </div>
            <div class="as-card-body">
                <div class="text-center py-3 mb-4 rounded" style="background: var(--bg-secondary);">
                    <small class="text-muted d-block uppercase mb-1">Skor Risiko Rantai Pasok</small>
                    <div class="score-display mb-2">{{ $c2Data['risk_score'] }}</div>
                    @if($c2Data['risk_status'] === 'High Risk')
                        <span class="risk-badge bg-danger">HIGH RISK</span>
                    @elseif($c2Data['risk_status'] === 'Medium Risk')
                        <span class="risk-badge bg-warning text-dark">MEDIUM RISK</span>
                    @else
                        <span class="risk-badge bg-success">LOW RISK</span>
                    @endif
                </div>

                <div class="mb-4">
                    <h6 class="text-white small mb-3">Breakdown Risiko & Indikator:</h6>
                    
                    <div class="p-3 mb-2 rounded" style="background: var(--bg-secondary);">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-secondary">🌤️ Risiko Cuaca (30%)</small>
                            <strong class="text-info">{{ $c2Data['breakdown']['weather']['score'] }}</strong>
                        </div>
                        <small class="text-muted d-block" style="font-size: 0.75rem;">Suhu: {{ $c2Data['breakdown']['weather']['data']['temperature'] ?? 'N/A' }}°C | Angin: {{ $c2Data['breakdown']['weather']['data']['wind_speed'] ?? 'N/A' }} km/j</small>
                    </div>

                    <div class="p-3 mb-2 rounded" style="background: var(--bg-secondary);">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-secondary">📈 Risiko Inflasi (20%)</small>
                            <strong class="text-warning">{{ $c2Data['breakdown']['inflation']['score'] }}</strong>
                        </div>
                        <small class="text-muted d-block" style="font-size: 0.75rem;">Inflasi: {{ $c2Data['breakdown']['inflation']['data']['inflation'] ?? 'N/A' }}% | GDP: USD {{ isset($c2Data['breakdown']['inflation']['data']['gdp']) ? number_format($c2Data['breakdown']['inflation']['data']['gdp'] / 1e9, 2) . ' Miliar' : 'N/A' }}</small>
                    </div>

                    <div class="p-3 mb-2 rounded" style="background: var(--bg-secondary);">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-secondary">💱 Risiko Kurs (10%)</small>
                            <strong class="text-success">{{ $c2Data['breakdown']['currency']['score'] }}</strong>
                        </div>
                        <small class="text-muted d-block" style="font-size: 0.75rem;">1 USD = {{ number_format($c2Data['breakdown']['currency']['data']['rate_to_usd'], 4) }} {{ $country2->currency_code }}</small>
                    </div>

                    <div class="p-3 mb-2 rounded" style="background: var(--bg-secondary);">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-secondary">📰 Sentimen Berita (40%)</small>
                            <strong class="text-danger">{{ $c2Data['breakdown']['sentiment']['score'] }}</strong>
                        </div>
                        <small class="text-muted d-block" style="font-size: 0.75rem;">Positif: {{ $c2Data['breakdown']['sentiment']['positive_count'] }} | Negatif: {{ $c2Data['breakdown']['sentiment']['negative_count'] }} (Dari {{ $c2Data['breakdown']['sentiment']['total_news'] }} berita)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="as-card">
    <div class="as-card-body text-center py-5">
        <span style="font-size: 3rem;">🔍</span>
        <h6 class="text-white mt-3">Silakan pilih dua negara untuk dibandingkan secara detail.</h6>
        <p class="text-muted small">Sebaran risiko ekonomi, berita, cuaca, dan nilai tukar akan ditampilkan berdampingan.</p>
    </div>
</div>
@endif
@endsection
