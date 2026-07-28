@extends('layouts.app')

@section('title', 'Manajemen Artikel Analisis')
@section('page-title', 'Administrator — Artikel Analisis')

@section('content')
@if(session('success'))
    <div class="alert alert-success py-2 mb-4" style="background: rgba(16,185,129,0.2); border: 1px solid var(--accent-green); color: white;">
        {{ session('success') }}
    </div>
@endif

<div class="row g-4">
    <!-- List Articles -->
    <div class="col-md-8">
        <div class="as-card">
            <div class="as-card-header">
                <h6>📑 Daftar Artikel Analisis Rantai Pasok</h6>
            </div>
            <div class="as-card-body p-0">
                <div class="table-responsive">
                    <table class="as-table">
                        <thead>
                            <tr>
                                <th>Judul Artikel</th>
                                <th>Terkait Negara</th>
                                <th>Penulis</th>
                                <th>Tanggal Dibuat</th>
                                <th style="width: 100px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($articles as $art)
                            <tr>
                                <td><strong class="text-white">📝 {{ $art->title }}</strong></td>
                                <td>
                                    @if($art->country_code)
                                        <span class="badge bg-secondary">{{ $art->country_code }}</span>
                                    @else
                                        <span class="text-muted small">Global</span>
                                    @endif
                                </td>
                                <td>{{ $art->user->name ?? 'Admin' }}</td>
                                <td><small class="text-muted">{{ $art->created_at->format('d M Y') }}</small></td>
                                <td class="text-center">
                                    <form method="POST" action="{{ route('admin.articles.delete', $art->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus artikel ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-as-danger" style="font-size:0.7rem; padding:3px 8px;">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Belum ada artikel analisis terdaftar.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3">
            {{ $articles->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <!-- Create Article -->
    <div class="col-md-4">
        <div class="as-card">
            <div class="as-card-header">
                <h6>➕ Tulis Artikel Analisis Baru</h6>
            </div>
            <div class="as-card-body">
                <form method="POST" action="{{ route('admin.articles.create') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label text-secondary small">Judul Artikel:</label>
                        <input type="text" name="title" class="as-input" required placeholder="Contoh: Hambatan Rute Maritim Selat Malaka">
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary small">Terkait Negara (Opsional):</label>
                        <select name="country_code" class="as-select">
                            <option value="">-- Global / Tidak Ada --</option>
                            @foreach($countries as $c)
                                <option value="{{ $c->code }}">{{ $c->name }} ({{ $c->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-secondary small">Konten / Isi Artikel:</label>
                        <textarea name="content" class="as-input" rows="8" required placeholder="Tulis analisis detail Anda mengenai risiko supply chain di sini..."></textarea>
                    </div>

                    <button type="submit" class="btn-as-primary w-100">Simpan Artikel</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
