@extends('layouts.app')

@section('title', 'Monitoring Pelabuhan')
@section('page-title', 'Monitoring — Pelabuhan Logistik')

@section('content')
<div class="row g-4 mb-4">
    <!-- Peta Pelabuhan Dunia -->
    <div class="col-md-12">
        <div class="as-card">
            <div class="as-card-header">
                <h6>🗺️ Peta Geospatial Pelabuhan Utama Dunia</h6>
            </div>
            <div class="as-card-body p-2">
                <div id="portsMap" style="height: 380px;"></div>
            </div>
        </div>
    </div>
</div>

<div class="as-card mb-4">
    <div class="as-card-header">
        <h6>⚓ Cari Pelabuhan</h6>
    </div>
    <div class="as-card-body">
        <form method="GET" action="{{ route('monitoring.ports') }}" class="row g-3">
            <div class="col-md-9">
                <input type="text" name="search" class="as-input" placeholder="Cari nama pelabuhan, kode, atau kode negara..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3 d-grid">
                <button type="submit" class="btn-as-primary">Cari Pelabuhan</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-12">
        <div class="as-card">
            <div class="as-card-header">
                <h6>📋 Daftar Pelabuhan Logistik Dunia</h6>
            </div>
            <div class="as-card-body p-0">
                <div class="table-responsive">
                    <table class="as-table">
                        <thead>
                            <tr>
                                <th>Nama Pelabuhan</th>
                                <th>Kode Pelabuhan</th>
                                <th>Negara</th>
                                <th>Latitude</th>
                                <th>Longitude</th>
                                <th style="width: 100px; text-align: center;">Detail Negara</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ports as $port)
                            <tr>
                                <td><strong class="text-white">🚢 {{ $port->name }}</strong></td>
                                <td><code>{{ $port->code }}</code></td>
                                <td>
                                    @if($port->country)
                                        @if($port->country->flag)
                                            <img src="{{ $port->country->flag }}" alt="Flag" style="width: 20px; margin-right: 6px; border-radius: 2px;">
                                        @endif
                                        <span>{{ $port->country->name }} ({{ $port->country_code }})</span>
                                    @else
                                        <span>{{ $port->country_name ?? $port->country_code }}</span>
                                        <span class="text-warning small" title="Belum terhubung ke country_id. Harap jalankan backfill command.">⚠️</span>
                                    @endif
                                </td>
                                <td><code>{{ $port->latitude }}</code></td>
                                <td><code>{{ $port->longitude }}</code></td>
                                <td class="text-center">
                                    @if($port->country)
                                        <a href="{{ route('countries.show', $port->country->code) }}" class="btn-as-outline" style="font-size:0.7rem; padding: 3px 8px;">Detail</a>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Data pelabuhan tidak ditemukan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-3 d-flex justify-content-center">
    {{ $ports->links('pagination::bootstrap-5') }}
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    var map = L.map('portsMap').setView([15, 20], 2);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 18,
    }).addTo(map);

    var portsList = @json($allPorts);
    
    portsList.forEach(function(port) {
        if (port.latitude && port.longitude) {
            var portIcon = L.divIcon({
                className: 'port-map-marker',
                html: '<div style="background-color:#0ea5e9; width:12px; height:12px; border-radius:3px; border:2px solid white; box-shadow: 0 0 5px rgba(0,0,0,0.5);"></div>',
                iconSize: [12, 12], iconAnchor: [6, 6]
            });

            L.marker([port.latitude, port.longitude], {icon: portIcon}).addTo(map)
                .bindPopup(
                    '🚢 <strong>' + port.name + '</strong><br>' +
                    'Kode Pelabuhan: <code>' + port.code + '</code><br>' +
                    'Negara: ' + (port.country_name || port.country_code) + '<br>' +
                    'Posisi: ' + port.latitude + ', ' + port.longitude
                );
        }
    });
});
</script>
@endsection
