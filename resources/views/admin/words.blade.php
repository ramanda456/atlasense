@extends('layouts.app')

@section('title', 'Kamus Sentimen')
@section('page-title', 'Administrator — Kamus Sentimen')

@section('content')
@if(session('success'))
    <div class="alert alert-success py-2 mb-4" style="background: rgba(16,185,129,0.2); border: 1px solid var(--accent-green); color: white;">
        {{ session('success') }}
    </div>
@endif

<div class="row g-4 mb-4">
    <!-- Add Word form -->
    <div class="col-md-12">
        <div class="as-card">
            <div class="as-card-header">
                <h6>➕ Tambah Kata Kunci Baru Ke Kamus Sentimen</h6>
            </div>
            <div class="as-card-body">
                <form method="POST" action="{{ route('admin.words.add') }}" class="row g-3 align-items-end">
                    @csrf
                    <div class="col-md-5">
                        <label class="form-label text-secondary small">Masukkan Kata (Hanya Huruf):</label>
                        <input type="text" name="word" class="as-input" required placeholder="Contoh: congestion, recovery..." pattern="[a-zA-Z\s]+">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-secondary small">Kategori Sentimen:</label>
                        <select name="type" class="as-select" required>
                            <option value="positive">Positif (Growth, Stable, dll)</option>
                            <option value="negative">Negatif (Strike, Congestion, dll)</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-grid">
                        <button type="submit" class="btn-as-primary">Tambah Kata</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Positive Words List -->
    <div class="col-md-6">
        <div class="as-card">
            <div class="as-card-header text-success" style="border-bottom: 2px solid var(--accent-green);">
                <h6>🟢 Kamus Kata Positif</h6>
            </div>
            <div class="as-card-body p-0">
                <table class="as-table">
                    <thead>
                        <tr>
                            <th>Kata</th>
                            <th style="width: 80px; text-align: center;">Hapus</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($positive as $word)
                        <tr>
                            <td><strong class="text-success">{{ $word->word }}</strong></td>
                            <td class="text-center">
                                <form method="POST" action="{{ route('admin.words.delete', ['type' => 'positive', 'id' => $word->id]) }}" onsubmit="return confirm('Hapus kata ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-as-danger" style="font-size:0.65rem; padding: 2px 6px;">✕</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center text-muted py-3">Belum ada kata positif.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3">
            {{ $positive->appends(request()->except('pos_page'))->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <!-- Negative Words List -->
    <div class="col-md-6">
        <div class="as-card">
            <div class="as-card-header text-danger" style="border-bottom: 2px solid var(--accent-red);">
                <h6>🔴 Kamus Kata Negatif</h6>
            </div>
            <div class="as-card-body p-0">
                <table class="as-table">
                    <thead>
                        <tr>
                            <th>Kata</th>
                            <th style="width: 80px; text-align: center;">Hapus</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($negative as $word)
                        <tr>
                            <td><strong class="text-danger">{{ $word->word }}</strong></td>
                            <td class="text-center">
                                <form method="POST" action="{{ route('admin.words.delete', ['type' => 'negative', 'id' => $word->id]) }}" onsubmit="return confirm('Hapus kata ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-as-danger" style="font-size:0.65rem; padding: 2px 6px;">✕</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center text-muted py-3">Belum ada kata negatif.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3">
            {{ $negative->appends(request()->except('neg_page'))->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
