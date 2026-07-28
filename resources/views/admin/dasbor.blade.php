@extends('layouts.utama')

@section('title', 'Dasbor Admin')
@section('page-title', 'Dasbor Admin')

@section('content')
@if(session('success'))
    <div class="neo-alert neo-alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="neo-alert neo-alert-danger">{{ session('error') }}</div>
@endif

<!-- Admin Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="neo-box text-center" style="background-color: var(--atlas-land-light); color: var(--text-primary);">
            <h5 class="fw-bold m-0">TOTAL PENGGUNA</h5>
            <h1 class="display-5 fw-bold my-2">{{ $stats['users'] }}</h1>
        </div>
    </div>
    <div class="col-md-4">
        <div class="neo-box text-center" style="background-color: var(--atlas-ocean-light); color: var(--text-primary);">
            <h5 class="fw-bold m-0">TOTAL PELABUHAN</h5>
            <h1 class="display-5 fw-bold my-2">{{ $stats['ports'] }}</h1>
        </div>
    </div>
    <div class="col-md-4">
        <div class="neo-box text-center" style="background-color: var(--atlas-sand-light); color: var(--text-primary);">
            <h5 class="fw-bold m-0">TOTAL ARTIKEL</h5>
            <h1 class="display-5 fw-bold my-2">{{ $stats['articles'] }}</h1>
        </div>
    </div>
    <div class="col-md-6">
        <div class="neo-box text-center" style="background-color: var(--atlas-ocean-light); color: var(--text-primary);">
            <h5 class="fw-bold m-0">KATA DI KAMUS SENTIMEN</h5>
            <h1 class="display-5 fw-bold my-2">{{ $stats['positive_words'] + $stats['negative_words'] }}</h1>
            <span class="small text-secondary fw-bold">{{ $stats['positive_words'] }} Positif / {{ $stats['negative_words'] }} Negatif</span>
        </div>
    </div>
    <div class="col-md-6">
        <div class="neo-box text-center" style="background-color: var(--atlas-danger-light); color: var(--text-primary);">
            <h5 class="fw-bold m-0">RASIO SUKSES API CALLS</h5>
            <h1 class="display-5 fw-bold my-2">{{ $stats['api_success'] }}%</h1>
            <span class="small text-secondary opacity-75">Dari total {{ $stats['api_logs'] }} log pemanggilan</span>
        </div>
    </div>
</div>

<!-- Risk Scoring Weights Form -->
<div class="neo-box">
    <div class="card-header-yellow">
        <h4 class="card-header-title text-dark">BOBOT PERHITUNGAN RISIKO RANTAI PASOK</h4>
    </div>
    <p class="text-secondary small mb-3">Tentukan persentase bobot tiap faktor untuk algoritma penilai risiko terbobot AtlaSense. <strong>Total akumulasi harus sama dengan 100%</strong>.</p>
    
    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-bold">🌤️ Bobot Cuaca (%):</label>
                <input type="number" name="risk_weather_weight" class="neo-input" min="0" max="100" value="{{ $settings['risk_weather_weight'] }}" required>
            </div>
            
            <div class="col-md-3">
                <label class="form-label fw-bold">📈 Bobot Inflasi (%):</label>
                <input type="number" name="risk_inflation_weight" class="neo-input" min="0" max="100" value="{{ $settings['risk_inflation_weight'] }}" required>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold">📰 Bobot Berita (%):</label>
                <input type="number" name="risk_news_weight" class="neo-input" min="0" max="100" value="{{ $settings['risk_news_weight'] }}" required>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold">💸 Bobot Mata Uang (%):</label>
                <input type="number" name="risk_currency_weight" class="neo-input" min="0" max="100" value="{{ $settings['risk_currency_weight'] }}" required>
            </div>
        </div>
        
        <button type="submit" class="btn-neo btn-neo-lime fw-bold mt-2">SIMPAN PERUBAHAN BOBOT</button>
    </form>
</div>
@endsection
