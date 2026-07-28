@extends('layouts.utama')

@section('title', 'Daftar Negara')
@section('page-title', 'Daftar Negara')

@section('content')
<!-- Filter Box -->
<div class="neo-box">
    <div class="card-header-yellow">
        <h4 class="card-header-title">FILTER & PENCARIAN NEGARA</h4>
    </div>
    <form method="GET" action="{{ route('countries.index') }}">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">Pencarian:</label>
                <input type="text" name="search" class="neo-input" placeholder="Masukkan nama, wilayah, atau kode negara..." value="{{ request('search') }}">
            </div>
            
            <div class="col-md-4">
                <label class="form-label fw-bold">Wilayah (Region):</label>
                <select name="region" class="neo-select">
                    <option value="">-- Semua Wilayah --</option>
                    @foreach($regions as $reg)
                        <option value="{{ $reg }}" {{ request('region') === $reg ? 'selected' : '' }}>{{ $reg }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn-neo btn-neo-lime w-100 py-3 mb-3 fw-bold">CARI</button>
            </div>
        </div>
    </form>
</div>

<!-- Countries Grid -->
<div class="row g-4 mb-4">
    @forelse($countries as $c)
    <div class="col-md-4">
        <div class="neo-box neo-box-hover h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="neo-badge neo-badge-purple fw-bold">{{ $c->code }} ({{ $c->cca3 ?? '-' }})</span>
                    @if($c->flag_url)
                        <img src="{{ $c->flag_url }}" alt="Flag" style="width: 40px; border: 2px solid #000; border-radius: 4px; box-shadow: 1px 1px 0px #000;">
                    @endif
                </div>
                
                <h4 class="fw-bold mb-1 text-dark">{{ $c->name }}</h4>
                <p class="text-secondary small mb-3">Capital: <span class="fw-bold">{{ $c->capital ?? '-' }}</span></p>
                
                <div class="border-top border-dark pt-3 d-flex flex-column gap-1 small">
                    <div>🌐 Wilayah: <span class="fw-bold">{{ $c->region ?? '-' }}</span></div>
                    <div>💵 Mata Uang: <span class="fw-bold">{{ $c->currency_code ?? '-' }} - {{ $c->currency_name ?? '-' }}</span></div>
                    <div>👥 Populasi: <span class="fw-bold">{{ number_format($c->population ?? 0) }}</span></div>
                </div>
            </div>
            
            <div class="mt-4">
                <a href="{{ route('countries.show', $c->code) }}" class="btn-neo btn-neo-blue w-100 fw-bold">ANALISIS DETAIL</a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center">
        <div class="neo-box py-5">
            <h4 class="text-secondary mb-0">Negara tidak ditemukan. Pastikan seeder database telah dijalankan.</h4>
        </div>
    </div>
    @endforelse
</div>

<!-- Pagination -->
<div class="d-flex justify-content-center">
    {{ $countries->appends(request()->query())->links('pagination::bootstrap-5') }}
</div>
@endsection
