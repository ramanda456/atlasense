@extends('layouts.app')

@section('title', 'Manajemen Ports')
@section('page-title', 'Administrator — Manajemen Ports')

@section('content')
@if(session('success'))
    <div class="alert alert-success py-2 mb-4" style="background: rgba(16,185,129,0.2); border: 1px solid var(--accent-green); color: white;">
        {{ session('success') }}
    </div>
@endif

<div class="row g-4">
    <!-- List Ports -->
    <div class="col-md-8">
        <div class="as-card">
            <div class="as-card-header">
                <h6>⚓ Daftar Pelabuhan Logistik Terdaftar</h6>
            </div>
            <div class="as-card-body p-0">
                <div class="table-responsive">
                    <table class="as-table">
                        <thead>
                            <tr>
                                <th>Nama Pelabuhan</th>
                                <th>Kode</th>
                                <th>Negara</th>
                                <th>Lat</th>
                                <th>Lng</th>
                                <th style="width: 100px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ports as $port)
                            <tr>
                                <td><strong class="text-white">🚢 {{ $port->name }}</strong></td>
                                <td><code>{{ $port->code }}</code></td>
                                <td>{{ $port->country_name ?? $port->country_code }}</td>
                                <td><code>{{ $port->latitude }}</code></td>
                                <td><code>{{ $port->longitude }}</code></td>
                                <td class="text-center">
                                    <form method="POST" action="{{ route('admin.ports.delete', $port->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pelabuhan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-as-danger" style="font-size:0.7rem; padding:3px 8px;">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Belum ada pelabuhan terdaftar.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3">
            {{ $ports->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <!-- Create Port -->
    <div class="col-md-4">
        <div class="as-card">
            <div class="as-card-header">
                <h6>➕ Tambah Pelabuhan Baru</h6>
            </div>
            <div class="as-card-body">
                <form method="POST" action="{{ route('admin.ports.create') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label text-secondary small">Nama Pelabuhan:</label>
                        <input type="text" name="name" class="as-input" required placeholder="Contoh: Tanjung Priok">
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary small">Kode Pelabuhan:</label>
                        <input type="text" name="code" class="as-input" required placeholder="Contoh: IDTPP">
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary small">Kode Negara Pemilik (3 Huruf):</label>
                        <select name="country_code" class="as-select" required>
                            <option value="">-- Pilih Negara --</option>
                            @foreach($countries as $c)
                                <option value="{{ $c->code }}">{{ $c->name }} ({{ $c->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary small">Latitude:</label>
                        <input type="number" step="any" name="latitude" class="as-input" required placeholder="-6.1234">
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-secondary small">Longitude:</label>
                        <input type="number" step="any" name="longitude" class="as-input" required placeholder="106.1234">
                    </div>

                    <button type="submit" class="btn-as-primary w-100">Tambah Pelabuhan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
