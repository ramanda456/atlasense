@extends('layouts.app')

@section('title', 'API System Logs')
@section('page-title', 'Administrator — API Logs Monitoring')

@section('content')
<div class="as-card mb-4">
    <div class="as-card-header">
        <h6>🔍 Filter API Logs</h6>
    </div>
    <div class="as-card-body">
        <form method="GET" action="{{ route('admin.api-logs') }}" class="row g-3">
            <div class="col-md-5">
                <select name="service" class="as-select">
                    <option value="">-- Semua Service --</option>
                    @foreach($services as $srv)
                        <option value="{{ $srv }}" {{ request('service') === $srv ? 'selected' : '' }}>{{ $srv }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <select name="status" class="as-select">
                    <option value="">-- Semua Status --</option>
                    <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Sukses (Success)</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Gagal (Failed)</option>
                </select>
            </div>
            <div class="col-md-3 d-grid">
                <button type="submit" class="btn-as-primary">Filter Logs</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-12">
        <div class="as-card">
            <div class="as-card-header">
                <h6>📋 Histori Panggilan API Terakhir</h6>
            </div>
            <div class="as-card-body p-0">
                <div class="table-responsive">
                    <table class="as-table" style="font-family: monospace;">
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>Service</th>
                                <th>Method</th>
                                <th>Endpoint</th>
                                <th>HTTP Code</th>
                                <th>Latency</th>
                                <th>Status</th>
                                <th>Error Message</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                            <tr style="color: {{ $log->success ? 'var(--text-primary)' : '#ef4444' }};">
                                <td><small>{{ $log->created_at->format('d-m-Y H:i:s') }}</small></td>
                                <td><strong>{{ $log->service }}</strong></td>
                                <td><code>{{ $log->method }}</code></td>
                                <td title="{{ $log->endpoint }}"><small>{{ Str::limit($log->endpoint, 45) }}</small></td>
                                <td><code>{{ $log->status_code ?? '-' }}</code></td>
                                <td>{{ $log->response_time_ms ? $log->response_time_ms . ' ms' : '-' }}</td>
                                <td>
                                    @if($log->success)
                                        <span class="badge bg-success" style="font-size: 0.65rem;">SUCCESS</span>
                                    @else
                                        <span class="badge bg-danger" style="font-size: 0.65rem;">FAILED</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-danger" title="{{ $log->error_message }}">{{ Str::limit($log->error_message, 40) }}</small>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Logs tidak ditemukan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-4 d-flex justify-content-center">
    {{ $logs->links('pagination::bootstrap-5') }}
</div>
@endsection
