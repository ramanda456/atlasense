@extends('layouts.utama')

@section('title', 'Analisis Risiko')
@section('page-title', 'Analisis Risiko')

@section('content')
@if(session('success'))
    <div class="neo-alert neo-alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="neo-alert neo-alert-danger">{{ session('error') }}</div>
@endif

<!-- Search Widget -->
<div class="neo-box">
    <div class="card-header-yellow">
        <h4 class="card-header-title text-dark">CARI NEGARA UNTUK ANALISIS RISIKO</h4>
    </div>
    <form method="GET" action="{{ route('analysis.risk') }}" class="row g-3">
        <div class="col-md-9">
            <input type="text" name="search" class="neo-input m-0" placeholder="Masukkan nama atau kode negara..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn-neo btn-neo-lime w-100 py-3 fw-bold">CARI NEGARA</button>
        </div>
    </form>
</div>

<!-- Risk Cards Grid -->
<div class="row g-4 mb-4">
    @forelse($countries as $c)
    <div class="col-md-4">
        <div class="neo-box neo-box-hover h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="neo-badge neo-badge-purple fw-bold">{{ $c->code }}</span>
                    @if($c->flag_url)
                        <img src="{{ $c->flag_url }}" alt="Flag" style="width: 32px; border: 1px solid #000; border-radius: 4px;">
                    @endif
                </div>

                <h4 class="fw-bold mb-3 text-dark">{{ $c->name }}</h4>

                @if($c->latestRisk)
                    <div class="border border-dark border-2 p-3 bg-light text-center" style="box-shadow: 2px 2px 0px #000; border-radius: 6px;">
                        <span class="text-secondary small text-uppercase fw-bold">Skor Terbobot</span>
                        <h2 class="fw-bold my-1 display-6">{{ $c->latestRisk->total_score }}</h2>
                        <span class="neo-badge {{ $c->latestRisk->risk_level === 'Tinggi' ? 'neo-badge-pink' : ($c->latestRisk->risk_level === 'Sedang' ? 'neo-badge-yellow' : 'neo-badge-lime') }} py-1 px-3">
                            {{ strtoupper($c->latestRisk->risk_level) }} RISK
                        </span>
                    </div>
                @else
                    <div class="border border-dark border-2 p-3 bg-white text-center text-secondary small" style="box-shadow: 2px 2px 0px #000; border-radius: 6px; border-style: dashed !important;">
                        <p class="mb-0">Risiko belum dihitung untuk negara ini.</p>
                    </div>
                @endif
            </div>

            <div class="mt-4 pt-3 border-top border-dark d-flex gap-2">
                <form action="{{ route('analysis.calculate', $c->code) }}" method="POST" class="w-50 m-0">
                    @csrf
                    <button type="submit" class="btn-neo btn-neo-lime w-100 py-2 small fw-bold" style="font-size: 0.75rem;">HITUNG RISIKO</button>
                </form>
                <a href="{{ route('countries.show', $c->code) }}" class="btn-neo btn-neo-blue w-50 py-2 small fw-bold" style="font-size: 0.75rem;">DETAIL</a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center">
        <div class="neo-box py-5">
            <h4 class="text-secondary mb-0">Negara tidak ditemukan.</h4>
        </div>
    </div>
    @endforelse
</div>

<!-- Pagination -->
<div class="d-flex justify-content-center">
    {{ $countries->appends(request()->query())->links('pagination::bootstrap-5') }}
</div>
@endsection
