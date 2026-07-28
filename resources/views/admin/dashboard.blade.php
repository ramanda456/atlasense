@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Administrator — Panel Utama')

@section('content')
<div class="row g-4 mb-4">
    <!-- Stat cards -->
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(139, 92, 246, 0.15); color: var(--accent-purple);">👥</div>
            <div class="stat-value text-white">{{ $stats['users'] }}</div>
            <div class="stat-label">Jumlah Pengguna (Users)</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(6, 182, 212, 0.15); color: var(--accent-cyan);">⚓</div>
            <div class="stat-value text-white">{{ $stats['ports'] }}</div>
            <div class="stat-label">Jumlah Pelabuhan</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(16, 185, 129, 0.15); color: var(--accent-green);">📖</div>
            <div class="stat-value text-white">{{ $stats['positive_words'] + $stats['negative_words'] }}</div>
            <div class="stat-label">Kamus Kata Sentimen</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="border-left: 4px solid var(--accent-cyan);">
            <div class="stat-value text-info">{{ $stats['api_success'] }}%</div>
            <div class="stat-label">Persentase Sukses API</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- API Integration Status -->
    <div class="col-md-8">
        <div class="as-card">
            <div class="as-card-header">
                <h6>📊 Ringkasan Panggilan API Eksternal (6 API)</h6>
            </div>
            <div class="as-card-body p-0">
                <div class="table-responsive">
                    <table class="as-table">
                        <thead>
                            <tr>
                                <th>Nama Service API</th>
                                <th>Total Panggilan</th>
                                <th>Sukses</th>
                                <th>Gagal</th>
                                <th>Persentase Sukses</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logsSummary as $log)
                            <tr>
                                <td><strong class="text-white">{{ $log->service }}</strong></td>
                                <td>{{ $log->total }}</td>
                                <td class="text-success">{{ $log->successes }}</td>
                                <td class="text-danger">{{ $log->total - $log->successes }}</td>
                                <td>
                                    <strong>{{ round(($log->successes / $log->total) * 100, 1) }}%</strong>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Belum ada data logs API eksternal yang terekam.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="col-md-4">
        <div class="as-card">
            <div class="as-card-header">
                <h6>⚡ Navigasi Cepat Admin</h6>
            </div>
            <div class="as-card-body d-flex flex-column gap-2">
                <a href="{{ route('admin.users') }}" class="btn-as-primary text-center">Manage User Accounts</a>
                <a href="{{ route('admin.ports') }}" class="btn-as-outline text-center">Manage Port Locations</a>
                <a href="{{ route('admin.words') }}" class="btn-as-outline text-center">Manage Sentimen Dictionary</a>
                <a href="{{ route('admin.api-logs') }}" class="btn-as-outline text-center">Inspect System API Logs</a>
            </div>
        </div>
    </div>
</div>
@endsection
