@extends('layouts.utama')

@section('title', 'Perbandingan Negara')
@section('page-title', 'Perbandingan Negara')

@section('content')
<div class="neo-box">
    <div class="card-header-yellow">
        <h4 class="card-header-title">PILIH NEGARA UNTUK DIBANDINGKAN</h4>
    </div>
    <form method="GET" action="{{ route('countries.compare') }}">
        <div class="row g-3">
            <div class="col-md-5">
                <label class="form-label fw-bold">Negara A:</label>
                <select name="country_a" class="neo-select" required>
                    <option value="">-- Pilih Negara A --</option>
                    @foreach($countries as $c)
                        <option value="{{ $c->id }}" {{ request('country_a') == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->code }})</option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-5">
                <label class="form-label fw-bold">Negara B:</label>
                <select name="country_b" class="neo-select" required>
                    <option value="">-- Pilih Negara B --</option>
                    @foreach($countries as $c)
                        <option value="{{ $c->id }}" {{ request('country_b') == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->code }})</option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn-neo btn-neo-lime w-100 py-3 mb-3 fw-bold">BANDINGKAN</button>
            </div>
        </div>
    </form>
</div>

@if($comparison)
<div class="row g-4 mb-4">
    <!-- Country A -->
    <div class="col-md-6">
        <div class="neo-box text-center" style="background-color: #ffffff;">
            <div class="card-header-blue">
                <h4 class="card-header-title text-dark">NEGARA A: {{ $negaraA->name }}</h4>
            </div>
            
            @if($negaraA->flag_url)
                <img src="{{ $negaraA->flag_url }}" alt="Flag" style="width: 100px; border: 3px solid #000; box-shadow: 2px 2px 0px #000; border-radius: 4px;" class="my-3">
            @endif
            
            <h3 class="fw-bold mt-2">{{ $negaraA->name }}</h3>
            
            <div class="border border-dark border-2 p-3 my-3 bg-light" style="box-shadow: 2px 2px 0px #000; border-radius: 6px;">
                <span class="text-secondary small fw-bold">Skor Risiko Rantai Pasok</span>
                <h2 class="fw-bold my-1">{{ $comparison['risk_a'] }} / 100</h2>
                <span class="neo-badge {{ $negaraA->latestRisk?->risk_level === 'Tinggi' ? 'neo-badge-pink' : ($negaraA->latestRisk?->risk_level === 'Sedang' ? 'neo-badge-yellow' : 'neo-badge-lime') }}">
                    RISIKO {{ strtoupper($negaraA->latestRisk?->risk_level ?? 'Rendah') }}
                </span>
            </div>
        </div>
    </div>
    
    <!-- Country B -->
    <div class="col-md-6">
        <div class="neo-box text-center" style="background-color: #ffffff;">
            <div class="card-header-pink">
                <h4 class="card-header-title text-dark">NEGARA B: {{ $negaraB->name }}</h4>
            </div>
            
            @if($negaraB->flag_url)
                <img src="{{ $negaraB->flag_url }}" alt="Flag" style="width: 100px; border: 3px solid #000; box-shadow: 2px 2px 0px #000; border-radius: 4px;" class="my-3">
            @endif
            
            <h3 class="fw-bold mt-2">{{ $negaraB->name }}</h3>
            
            <div class="border border-dark border-2 p-3 my-3 bg-light" style="box-shadow: 2px 2px 0px #000; border-radius: 6px;">
                <span class="text-secondary small fw-bold">Skor Risiko Rantai Pasok</span>
                <h2 class="fw-bold my-1">{{ $comparison['risk_b'] }} / 100</h2>
                <span class="neo-badge {{ $negaraB->latestRisk?->risk_level === 'Tinggi' ? 'neo-badge-pink' : ($negaraB->latestRisk?->risk_level === 'Sedang' ? 'neo-badge-yellow' : 'neo-badge-lime') }}">
                    RISIKO {{ strtoupper($negaraB->latestRisk?->risk_level ?? 'Rendah') }}
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Comparison Table -->
<div class="neo-table-wrapper">
    <table class="neo-table">
        <thead>
            <tr>
                <th style="width: 30%;">Parameter Perbandingan</th>
                <th style="width: 35%;" class="text-center">{{ $negaraA->name }}</th>
                <th style="width: 35%;" class="text-center">{{ $negaraB->name }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Ibu Kota</strong></td>
                <td class="text-center">{{ $negaraA->capital ?? '-' }}</td>
                <td class="text-center">{{ $negaraB->capital ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Wilayah / Benua</strong></td>
                <td class="text-center">{{ $negaraA->region }} ({{ $negaraA->subregion ?? '-' }})</td>
                <td class="text-center">{{ $negaraB->region }} ({{ $negaraB->subregion ?? '-' }})</td>
            </tr>
            <tr>
                <td><strong>Mata Uang</strong></td>
                <td class="text-center">{{ $negaraA->currency_code }} ({{ $negaraA->currency_name ?? '-' }})</td>
                <td class="text-center">{{ $negaraB->currency_code }} ({{ $negaraB->currency_name ?? '-' }})</td>
            </tr>
            <tr>
                <td><strong>Populasi Penduduk</strong></td>
                <td class="text-center">{{ number_format($comparison['population_a']) }} jiwa</td>
                <td class="text-center">{{ number_format($comparison['population_b']) }} jiwa</td>
            </tr>
            <tr>
                <td><strong>Produk Domestik Bruto (GDP)</strong></td>
                <td class="text-center font-weight-bold">
                    {{ $comparison['gdp_a'] ? 'USD ' . number_format($comparison['gdp_a']) : 'N/A' }}
                </td>
                <td class="text-center font-weight-bold">
                    {{ $comparison['gdp_b'] ? 'USD ' . number_format($comparison['gdp_b']) : 'N/A' }}
                </td>
            </tr>
            <tr>
                <td><strong>Tingkat Inflasi Tahunan</strong></td>
                <td class="text-center">{{ $comparison['inflation_a'] ? number_format($comparison['inflation_a'], 2) . '%' : 'N/A' }}</td>
                <td class="text-center">{{ $comparison['inflation_b'] ? number_format($comparison['inflation_b'], 2) . '%' : 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Temperatur Udara</strong></td>
                <td class="text-center">{{ $comparison['temp_a'] ?? 'N/A' }} °C</td>
                <td class="text-center">{{ $comparison['temp_b'] ?? 'N/A' }} °C</td>
            </tr>
            <tr>
                <td><strong>Kecepatan Angin</strong></td>
                <td class="text-center">{{ $comparison['wind_a'] ?? 'N/A' }} km/jam</td>
                <td class="text-center">{{ $comparison['wind_b'] ?? 'N/A' }} km/jam</td>
            </tr>
        </tbody>
    </table>
</div>
@endif
@endsection
