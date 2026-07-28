@extends('layouts.utama')

@section('title', 'Log API')
@section('page-title', 'Log Panggilan API')

@section('content')
@if(session('success'))
    <div class="neo-alert neo-alert-success">{{ session('success') }}</div>
@endif

<!-- Filters & Actions -->
<div class="neo-box">
    <div class="card-header-yellow">
        <h4 class="card-header-title">FILTER LOG API</h4>
    </div>
    
    <div class="d-flex flex-wrap align-items-end justify-content-between gap-3">
        <form method="GET" action="{{ route('admin.api-logs') }}" class="d-flex flex-wrap gap-3 m-0 align-items-end">
            <div style="min-width: 200px;">
                <label class="form-label fw-bold small">Pilih Layanan:</label>
                <select name="service" class="neo-select m-0 py-2">
                    <option value="">-- Semua Layanan --</option>
                    @foreach($services as $srv)
                        <option value="{{ $srv }}" {{ request('service') === $srv ? 'selected' : '' }}>{{ $srv }}</option>
                    @endforeach
                </select>
            </div>
            
            <div style="min-width: 160px;">
                <label class="form-label fw-bold small">Status Panggilan:</label>
                <select name="status" class="neo-select m-0 py-2">
                    <option value="">-- Semua Status --</option>
                    <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Sukses (Success)</option>
                    <option value="error" {{ request('status') === 'error' ? 'selected' : '' }}>Gagal (Error)</option>
                </select>
            </div>
            
            <button type="submit" class="btn-neo btn-neo-lime py-2 px-4 fw-bold">TERAPKAN FILTER</button>
        </form>

        <form action="{{ route('admin.api-logs.clear') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus seluruh log pemanggilan API? Tindakan ini permanen.');" class="m-0">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-neo btn-neo-pink text-white py-2 px-4 fw-bold">⚠️ BERSIHKAN SEMUA LOG</button>
        </form>
    </div>
</div>

<!-- Logs Table -->
<div class="neo-table-wrapper">
    <table class="neo-table">
        <thead>
            <tr>
                <th>Layanan</th>
                <th>Method</th>
                <th>Endpoint URL</th>
                <th>Status Code</th>
                <th>Waktu Respons</th>
                <th>Rasio Keberhasilan</th>
                <th>Pesan Error</th>
                <th>Waktu Request</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
            <tr>
                <td><strong>{{ $log->service }}</strong></td>
                <td><code>{{ $log->method }}</code></td>
                <td><code class="text-secondary small" style="word-break: break-all;">{{ $log->endpoint }}</code></td>
                <td>
                    <span class="badge {{ $log->status_code >= 200 && $log->status_code < 300 ? 'bg-success' : 'bg-danger' }} border border-dark text-dark fw-bold">
                        {{ $log->status_code ?? 'N/A' }}
                    </span>
                </td>
                <td><code>{{ $log->response_time_ms ?? 0 }} ms</code></td>
                <td>
                    <span class="neo-badge {{ $log->success ? 'neo-badge-lime' : 'neo-badge-pink' }} py-0 px-2 small">
                        {{ $log->success ? 'SUKSES' : 'GAGAL' }}
                    </span>
                </td>
                <td>
                    @if($log->message)
                        <span class="text-danger small" title="{{ $log->message }}">{{ Str::limit($log->message, 40) }}</span>
                    @else
                        <span class="text-muted small">-</span>
                    @endif
                </td>
                <td><small class="text-secondary">{{ $log->requested_at ? $log->requested_at->translatedFormat('d M, H:i:s') : '-' }}</small></td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center text-secondary py-4">Belum ada log pemanggilan API eksternal terdaftar.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="d-flex justify-content-center">
    {{ $logs->appends(request()->query())->links('pagination::bootstrap-5') }}
</div>
@endsection
