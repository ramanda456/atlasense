@extends('layouts.utama')

@section('title', 'Kelola Pelabuhan')
@section('page-title', 'Kelola Pelabuhan')

@section('content')
@if(session('success'))
    <div class="neo-alert neo-alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="neo-alert neo-alert-danger">{{ session('error') }}</div>
@endif

<div class="row g-4 mb-4">
    <!-- Import Ports CSV -->
    <div class="col-md-6">
        <div class="neo-box h-100">
            <div class="card-header-yellow">
                <h4 class="card-header-title">IMPOR PELABUHAN DARI DATASET CSV</h4>
            </div>
            <p class="text-secondary small">Upload file CSV World Port Index Anda untuk memproses impor data koordinat dan status pelabuhan global secara massal.</p>
            
            <form action="{{ route('admin.ports.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold">Pilih File CSV:</label>
                    <input type="file" name="csv_file" class="neo-input" accept=".csv" required>
                </div>
                <button type="submit" class="btn-neo btn-neo-yellow fw-bold">PROSES IMPOR CSV</button>
            </form>
        </div>
    </div>

    <!-- Manual Add Port -->
    <div class="col-md-6">
        <div class="neo-box h-100">
            <div class="card-header-blue">
                <h4 class="card-header-title">TAMBAH PELABUHAN SECARA MANUAL</h4>
            </div>
            
            <form action="{{ route('admin.ports.create') }}" method="POST" class="row g-2">
                @csrf
                
                <div class="col-md-6">
                    <label class="form-label fw-bold small mb-1">Nama Pelabuhan:</label>
                    <input type="text" name="name" class="neo-input py-2 mb-2" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small mb-1">Negara Induk:</label>
                    <select name="country_id" class="neo-select py-2 mb-2" required>
                        <option value="">-- Pilih Negara --</option>
                        @foreach($countries as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small mb-1">Kota/Provinsi:</label>
                    <input type="text" name="city" class="neo-input py-2 mb-2">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small mb-1">UN/LOCODE:</label>
                    <input type="text" name="unlocode" class="neo-input py-2 mb-2" placeholder="Contoh: IDJKT">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small mb-1">Latitude:</label>
                    <input type="text" name="latitude" class="neo-input py-2 mb-2" placeholder="-6.103" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small mb-1">Longitude:</label>
                    <input type="text" name="longitude" class="neo-input py-2 mb-2" placeholder="106.886" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-bold small mb-1">Fasilitas/Tipe:</label>
                    <input type="text" name="port_type" class="neo-input py-2 mb-2" value="Pelabuhan Laut" required>
                </div>
                
                <div class="col-md-12">
                    <button type="submit" class="btn-neo btn-neo-lime w-100 fw-bold py-2">SIMPAN DATA PELABUHAN</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Ports List -->
<div class="neo-box">
    <div class="card-header-purple">
        <h4 class="card-header-title">⚓ DAFTAR PELABUHAN UTAMA</h4>
    </div>
    
    <div class="neo-table-wrapper border-0 box-shadow-none m-0">
        <table class="neo-table">
            <thead>
                <tr>
                    <th>Nama Pelabuhan</th>
                    <th>UN/LOCODE</th>
                    <th>WPI Number</th>
                    <th>Kota</th>
                    <th>Negara</th>
                    <th>Koordinat</th>
                    <th>Sumber</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ports as $port)
                <tr>
                    <td><strong>🚢 {{ $port->name }}</strong></td>
                    <td><code>{{ $port->unlocode ?? '-' }}</code></td>
                    <td><code>{{ $port->wpi_number ?? '-' }}</code></td>
                    <td>{{ $port->city ?? '-' }}</td>
                    <td>{{ $port->country_name }}</td>
                    <td><small class="text-secondary">{{ $port->latitude }}, {{ $port->longitude }}</small></td>
                    <td><span class="neo-badge neo-badge-blue">{{ $port->data_source }}</span></td>
                    <td class="text-center">
                        <form action="{{ route('admin.ports.delete', $port->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pelabuhan ini?');" class="m-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm text-danger p-0 border-0 fw-bold">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center">
    {{ $ports->links('pagination::bootstrap-5') }}
</div>
@endsection
