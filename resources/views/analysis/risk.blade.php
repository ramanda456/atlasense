@extends('layouts.app')

@section('title', 'Analisis Gangguan Transportasi')
@section('page-title', 'Intelijen Global — Analisis Gangguan Transportasi')

@section('content')
<div class="as-card mb-4">
    <div class="as-card-header">
        <h6>🕵️‍♂️ Cari Analisis Gangguan</h6>
    </div>
    <div class="as-card-body">
        <form method="GET" action="{{ route('analysis.risk') }}" class="row g-3">
            <div class="col-md-9">
                <input type="text" name="search" class="as-input" placeholder="Cari berdasarkan nama atau kode negara..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3 d-grid">
                <button type="submit" class="btn-as-primary">Cari Analisis</button>
            </div>
        </form>
    </div>
</div>

<div class="as-card">
    <div class="as-card-header">
        <h6>📊 Indeks Potensi Gangguan Logistik & Transportasi Global</h6>
    </div>
    <div class="as-card-body p-0">
        <div class="table-responsive">
            <table class="as-table">
                <thead>
                    <tr>
                        <th>Negara</th>
                        <th>Skor Gangguan</th>
                        <th>Level Risiko</th>
                        <th>Faktor Dominan</th>
                        <th>Pelabuhan</th>
                        <th>Berita Diproses</th>
                        <th>Rekomendasi Operasional</th>
                        <th style="width: 100px; text-align: center;">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($analysisResults as $result)
                    <tr>
                        <td>
                            @if($result['country']->flag)
                                <img src="{{ $result['country']->flag }}" alt="Flag" style="width: 24px; margin-right: 8px; border-radius: 2px;">
                            @endif
                            <strong class="text-white">{{ $result['country']->name }}</strong>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="bar-container" style="width: 70px; height: 8px; background: var(--bg-input); border-radius: 4px; overflow: hidden;">
                                    <div class="bar-fill" style="width: {{ $result['analysis']['score'] }}%; height: 100%; background: {{ $result['analysis']['score'] >= 65 ? 'var(--accent-red)' : ($result['analysis']['score'] >= 35 ? 'var(--accent-yellow)' : 'var(--accent-green)') }};"></div>
                                </div>
                                <span>{{ $result['analysis']['score'] }}</span>
                            </div>
                        </td>
                        <td>
                            @if($result['analysis']['level'] === 'High')
                                <span class="badge bg-danger">HIGH</span>
                            @elseif($result['analysis']['level'] === 'Medium')
                                <span class="badge bg-warning text-dark">MEDIUM</span>
                            @else
                                <span class="badge bg-success">LOW</span>
                            @endif
                        </td>
                        <td>
                            @if($result['analysis']['dominant_component'] === 'weather')
                                <span class="text-info">🌤️ Cuaca</span>
                            @elseif($result['analysis']['dominant_component'] === 'news')
                                <span class="text-danger">📰 Sentimen Berita</span>
                            @else
                                <span class="text-primary">🚢 Pelabuhan</span>
                            @endif
                        </td>
                        <td>{{ $result['analysis']['port_count'] }}</td>
                        <td>{{ $result['analysis']['news_count'] }}</td>
                        <td><small class="text-secondary">{{ $result['analysis']['recommendation'] }}</small></td>
                        <td class="text-center">
                            <a href="{{ route('countries.show', $result['country']->code) }}" class="btn-as-outline" style="font-size: 0.7rem; padding: 3px 8px;">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">Data analisis tidak ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
