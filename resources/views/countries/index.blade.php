@extends('layouts.app')

@section('title', 'Data Negara')
@section('page-title', 'Intelijen Global — Data Negara')

@section('content')
<div class="as-card mb-4">
    <div class="as-card-header">
        <h6>🌍 Filter & Cari Negara</h6>
    </div>
    <div class="as-card-body">
        <form method="GET" action="{{ route('countries.index') }}" class="row g-3">
            <div class="col-md-5">
                <input type="text" name="search" class="as-input" placeholder="Cari nama atau kode negara..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select name="region" class="as-select">
                    <option value="">-- Semua Region --</option>
                    @foreach($regions as $region)
                        <option value="{{ $region }}" {{ request('region') == $region ? 'selected' : '' }}>{{ $region }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-grid">
                <button type="submit" class="btn-as-primary">Cari & Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="as-card">
    <div class="as-card-header">
        <h6>📋 Daftar Negara Pantauan</h6>
    </div>
    <div class="as-card-body p-0">
        <div class="table-responsive">
            <table class="as-table">
                <thead>
                    <tr>
                        <th style="width: 80px;">Bendera</th>
                        <th>Kode</th>
                        <th>Nama Negara</th>
                        <th>Region</th>
                        <th>Ibu Kota</th>
                        <th>Mata Uang</th>
                        <th>Penduduk</th>
                        <th style="width: 120px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($countries as $country)
                    <tr>
                        <td>
                            @if($country->flag)
                                <img src="{{ $country->flag }}" alt="Flag" style="width: 38px; border-radius: 3px; border: 1px solid var(--border-light);">
                            @else
                                <span style="font-size: 1.5rem;">🏳️</span>
                            @endif
                        </td>
                        <td><strong>{{ $country->code }}</strong></td>
                        <td><span class="text-white font-weight-bold">{{ $country->name }}</span></td>
                        <td>{{ $country->region }}</td>
                        <td>{{ $country->capital ?? '-' }}</td>
                        <td><code>{{ $country->currency_code }}</code></td>
                        <td>{{ $country->population ? number_format($country->population) : '-' }}</td>
                        <td class="text-center">
                            <a href="{{ route('countries.show', $country->code) }}" class="btn-as-outline" style="font-size:0.75rem; padding: 4px 10px;">📊 Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">Data negara tidak ditemukan. Harap sinkronisasi negara terlebih dahulu.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3 d-flex justify-content-center">
    {{ $countries->links('pagination::bootstrap-5') }}
</div>
@endsection
