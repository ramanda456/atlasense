@extends('layouts.utama')

@section('title', 'Lokasi Pelabuhan')
@section('page-title', 'Lokasi Pelabuhan')

@section('content')
<!-- Map Widget -->
<div class="neo-box">
    <div class="card-header-blue">
        <h4 class="card-header-title text-dark">PETA LOKASI PELABUHAN UTAMA DUNIA</h4>
    </div>
    <div id="portsMap" style="height: 380px; border-radius: 6px; border: 1px solid var(--border-color); box-shadow: 0 1px 3px rgba(168,155,133,0.1);"></div>
</div>

<!-- Search Widget -->
<div class="neo-box">
    <div class="card-header-yellow">
        <h4 class="card-header-title">CARI DATA PELABUHAN</h4>
    </div>
    <form method="GET" action="{{ route('monitoring.ports') }}" class="row g-3">
        <div class="col-md-9">
            <input type="text" name="search" class="neo-input m-0" placeholder="Masukkan nama pelabuhan, kota, negara, atau kode UN/LOCODE..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn-neo btn-neo-lime w-100 py-3 fw-bold">CARI PELABUHAN</button>
        </div>
    </form>
</div>

<!-- Ports Table -->
<div class="neo-table-wrapper">
    <table class="neo-table">
        <thead>
            <tr>
                <th>Nama Pelabuhan</th>
                <th>UN/LOCODE</th>
                <th>WPI Number</th>
                <th>Kota</th>
                <th>Negara</th>
                <th>Koordinat</th>
                <th>Tipe Fasilitas</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ports as $port)
            <tr>
                <td><strong>🚢 {{ $port->name }}</strong></td>
                <td><code>{{ $port->unlocode ?? '-' }}</code></td>
                <td><code>{{ $port->wpi_number ?? '-' }}</code></td>
                <td>{{ $port->city ?? '-' }}</td>
                <td>
                    @if($port->negara?->flag_url)
                        <img src="{{ $port->negara->flag_url }}" alt="Flag" style="width: 20px; border: 1px solid #000; margin-right: 6px;">
                    @endif
                    {{ $port->country_name }}
                </td>
                <td><small class="text-secondary">{{ $port->latitude }}, {{ $port->longitude }}</small></td>
                <td><span class="neo-badge neo-badge-purple">{{ $port->port_type }}</span></td>
                <td class="text-center">
                    @if($port->negara)
                        <a href="{{ route('countries.show', $port->negara->code) }}" class="btn-neo btn-neo-yellow py-1 px-2 small" style="font-size: 0.75rem;">Detail Negara</a>
                    @else
                        -
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center text-secondary py-4">Data pelabuhan belum tersedia.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="d-flex justify-content-center">
    {{ $ports->appends(request()->query())->links('pagination::bootstrap-5') }}
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    var map = L.map('portsMap').setView([15, 20], 2);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var portsList = @json($allPorts);
    
    portsList.forEach(function(port) {
        if (port.latitude && port.longitude) {
            var portIcon = L.divIcon({
                className: 'port-marker',
                html: '<div style="background-color:#38BDF8; width:12px; height:12px; border-radius:3px; border:2px solid #000; box-shadow: 2px 2px 0px #000;"></div>',
                iconSize: [12, 12], iconAnchor: [6, 6]
            });

            L.marker([port.latitude, port.longitude], {icon: portIcon}).addTo(map)
                .bindPopup(
                    '🚢 <strong>' + port.name + '</strong><br>' +
                    'Kode UN/LOCODE: <code>' + (port.unlocode || '-') + '</code><br>' +
                    'Kota: ' + (port.city || '-') + '<br>' +
                    'Negara: ' + port.country_name + '<br>' +
                    'Tipe: ' + port.port_type
                );
        }
    });
});
</script>
@endsection
