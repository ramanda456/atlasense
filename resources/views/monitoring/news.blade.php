@extends('layouts.app')

@section('title', 'Monitoring Berita')
@section('page-title', 'Monitoring — Berita & Intelijen Sentimen')

@section('content')
<div class="row g-4 mb-4">
    <!-- Stat Cards -->
    <div class="col-md-3">
        <div class="stat-card" style="background: var(--bg-card);">
            <div class="stat-value text-white">{{ $stats['total'] }}</div>
            <div class="stat-label">Total Berita Diproses</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="border-left: 4px solid var(--accent-green);">
            <div class="stat-value text-success">{{ $stats['positive'] }}</div>
            <div class="stat-label">Berita Positif</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="border-left: 4px solid var(--accent-red);">
            <div class="stat-value text-danger">{{ $stats['negative'] }}</div>
            <div class="stat-label">Berita Berisiko (Negatif)</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="border-left: 4px solid var(--text-secondary);">
            <div class="stat-value text-secondary">{{ $stats['neutral'] }}</div>
            <div class="stat-label">Berita Netral</div>
        </div>
    </div>
</div>

<div class="as-card mb-4">
    <div class="as-card-header">
        <h6>📰 Filter Berita & Analisis Sentimen</h6>
    </div>
    <div class="as-card-body">
        <form method="GET" action="{{ route('monitoring.news') }}" class="row g-3">
            <div class="col-md-5">
                <input type="text" name="search" class="as-input" placeholder="Cari kata kunci berita atau kode negara..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select name="sentiment" class="as-select">
                    <option value="">-- Semua Sentimen --</option>
                    <option value="Positive" {{ request('sentiment') === 'Positive' ? 'selected' : '' }}>Positive</option>
                    <option value="Negative" {{ request('sentiment') === 'Negative' ? 'selected' : '' }}>Negative</option>
                    <option value="Neutral" {{ request('sentiment') === 'Neutral' ? 'selected' : '' }}>Neutral</option>
                </select>
            </div>
            <div class="col-md-3 d-grid">
                <button type="submit" class="btn-as-primary">Filter Berita</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4">
    @forelse($news as $item)
    <div class="col-md-6">
        <div class="as-card h-100">
            <div class="as-card-body">
                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                    <span class="badge bg-secondary">🌍 Negara: {{ $item->country_code }}</span>
                    @if($item->sentiment === 'Positive')
                        <span class="badge-positive">Positive</span>
                    @elseif($item->sentiment === 'Negative')
                        <span class="badge-negative">Negative</span>
                    @else
                        <span class="badge-neutral">Neutral</span>
                    @endif
                </div>
                <h6 class="text-white mb-2">{{ $item->title }}</h6>
                <p class="text-secondary small mb-3">{{ Str::limit($item->description, 200) }}</p>
                <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top" style="border-top-color: var(--border-color) !important;">
                    <small class="text-muted" style="font-size: 0.72rem;">🕒 Diambil: {{ $item->fetched_at ? $item->fetched_at->diffForHumans() : '-' }}</small>
                    <a href="{{ route('countries.show', $item->country_code) }}" class="text-info small" style="font-size: 0.72rem;">Analisis Negara →</a>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-md-12">
        <div class="as-card text-center py-5">
            <span style="font-size: 3rem;">📰</span>
            <h6 class="text-white mt-3">Tidak ada berita yang cocok dengan filter pencarian.</h6>
            <p class="text-muted small">Harap sinkronisasi berita menggunakan command `php artisan atlasense:sync-news`.</p>
        </div>
    </div>
    @endforelse
</div>

<div class="mt-4 d-flex justify-content-center">
    {{ $news->links('pagination::bootstrap-5') }}
</div>
@endsection
