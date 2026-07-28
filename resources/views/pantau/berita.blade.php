@extends('layouts.utama')

@section('title', 'Intelijen Berita')
@section('page-title', 'Intelijen Berita')

@section('content')
<!-- Search & Filter -->
<div class="neo-box">
    <div class="card-header-yellow">
        <h4 class="card-header-title">CARI & FILTER BERITA SUPPLY CHAIN</h4>
    </div>
    <form method="GET" action="{{ route('monitoring.news') }}">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">Kata Kunci:</label>
                <input type="text" name="search" class="neo-input" placeholder="Masukkan judul, konten, atau negara..." value="{{ request('search') }}">
            </div>
            
            <div class="col-md-4">
                <label class="form-label fw-bold">Sentimen:</label>
                <select name="sentiment" class="neo-select">
                    <option value="">-- Semua Sentimen --</option>
                    <option value="Positif" {{ request('sentiment') === 'Positif' ? 'selected' : '' }}>Positif</option>
                    <option value="Negatif" {{ request('sentiment') === 'Negatif' ? 'selected' : '' }}>Negatif</option>
                    <option value="Netral" {{ request('sentiment') === 'Netral' ? 'selected' : '' }}>Netral</option>
                </select>
            </div>
            
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn-neo btn-neo-lime w-100 py-3 mb-3 fw-bold">SINKRON</button>
            </div>
        </div>
    </form>
</div>

<!-- News Grid -->
<div class="row g-4 mb-4">
    @forelse($newsData as $news)
    <div class="col-md-6">
        <div class="neo-box h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="neo-badge {{ $news->sentiment === 'Positif' ? 'neo-badge-lime' : ($news->sentiment === 'Negatif' ? 'neo-badge-pink' : 'neo-badge-blue') }} fw-bold">
                        SENTIMEN: {{ strtoupper($news->sentiment) }}
                    </span>
                    <span class="small text-secondary fw-bold">{{ $news->negara?->name ?? 'Global' }}</span>
                </div>
                
                <h5 class="fw-bold text-dark mb-2">{{ $news->title }}</h5>
                <p class="text-secondary small mb-3">{{ Str::limit($news->description, 180) }}</p>
                
                <!-- Lexicon Details -->
                @if($news->analysis)
                    <div class="border-top border-dark border-1 pt-3 mb-3">
                        <span class="small fw-bold text-secondary d-block mb-2">🔍 KATA KUNCI TERDETEKSI (LEXICON):</span>
                        <div class="d-flex flex-wrap gap-1">
                            @if(is_array($news->analysis->matched_positive) && count($news->analysis->matched_positive) > 0)
                                @foreach($news->analysis->matched_positive as $word)
                                    <span class="badge bg-success border border-dark text-white text-lowercase">{{ $word }}</span>
                                @endforeach
                            @endif
                            @if(is_array($news->analysis->matched_negative) && count($news->analysis->matched_negative) > 0)
                                @foreach($news->analysis->matched_negative as $word)
                                    <span class="badge bg-danger border border-dark text-white text-lowercase">{{ $word }}</span>
                                @endforeach
                            @endif
                            @if((!is_array($news->analysis->matched_positive) || count($news->analysis->matched_positive) === 0) && (!is_array($news->analysis->matched_negative) || count($news->analysis->matched_negative) === 0))
                                <span class="text-muted small">Tidak ada kata positif/negatif spesifik terdeteksi.</span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <div class="d-flex align-items-center justify-content-between border-top border-dark pt-3 mt-3">
                <span class="text-secondary small fw-bold">{{ $news->source }} • {{ $news->published_at ? $news->published_at->diffForHumans() : '-' }}</span>
                @if($news->url)
                    <a href="{{ $news->url }}" target="_blank" class="btn-neo btn-neo-yellow py-1 px-3 small" style="font-size: 0.75rem;">BACA SUMBER</a>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center">
        <div class="neo-box py-5">
            <h4 class="text-secondary mb-0">Berita tidak ditemukan.</h4>
        </div>
    </div>
    @endforelse
</div>

<!-- Pagination -->
<div class="d-flex justify-content-center">
    {{ $newsData->appends(request()->query())->links('pagination::bootstrap-5') }}
</div>
@endsection
