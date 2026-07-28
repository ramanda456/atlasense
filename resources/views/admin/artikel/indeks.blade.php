@extends('layouts.utama')

@section('title', 'Kelola Artikel')
@section('page-title', 'Kelola Artikel')

@section('content')
@if(session('success'))
    <div class="neo-alert neo-alert-success">{{ session('success') }}</div>
@endif

<div class="row g-4">
    <!-- Add Article Form -->
    <div class="col-md-5">
        <div class="neo-box">
            <div class="card-header-yellow">
                <h4 class="card-header-title">TULIS ARTIKEL BARU</h4>
            </div>
            
            <form action="{{ route('admin.articles.create') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Judul Artikel:</label>
                    <input type="text" name="title" class="neo-input" placeholder="Masukkan judul..." required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Ringkasan (Excerpt):</label>
                    <textarea name="excerpt" class="neo-textarea" rows="2" placeholder="Masukkan ringkasan singkat..." required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Konten Artikel:</label>
                    <textarea name="content" class="neo-textarea" rows="6" placeholder="Tulis konten lengkap di sini..." required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Status Artikel:</label>
                    <select name="status" class="neo-select" required>
                        <option value="published">Langsung Terbitkan (Published)</option>
                        <option value="draft">Simpan sebagai Draft</option>
                    </select>
                </div>

                <button type="submit" class="btn-neo btn-neo-lime w-100 fw-bold py-2 mt-2">TERBITKAN ARTIKEL</button>
            </form>
        </div>
    </div>

    <!-- Articles List -->
    <div class="col-md-7">
        <div class="neo-box">
            <div class="card-header-blue">
                <h4 class="card-header-title">📋 DAFTAR ARTIKEL ANALISIS</h4>
            </div>
            
            <div class="d-flex flex-column gap-3">
                @forelse($articles as $art)
                <div class="border border-dark border-2 p-3 bg-white" style="box-shadow: 2px 2px 0px #000; border-radius: 6px;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="neo-badge {{ $art->status === 'published' ? 'neo-badge-lime' : 'neo-badge-yellow' }}">
                            {{ strtoupper($art->status) }}
                        </span>
                        <small class="text-secondary">{{ $art->created_at ? $art->created_at->diffForHumans() : '-' }}</small>
                    </div>
                    
                    <h5 class="fw-bold mb-1 text-dark">{{ $art->title }}</h5>
                    <p class="text-secondary small mb-3">{{ $art->excerpt }}</p>
                    
                    <div class="border-top border-dark pt-3 d-flex align-items-center justify-content-between">
                        <span class="small text-secondary">Penulis: <strong>{{ $art->user?->name ?? 'Admin' }}</strong></span>
                        <div class="d-flex gap-2">
                            <form action="{{ route('admin.articles.delete', $art->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus artikel ini?');" class="m-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-neo btn-neo-pink text-white py-1 px-2 small" style="font-size: 0.7rem;">HAPUS</button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-center text-secondary mb-0 py-4">Belum ada artikel analisis yang diterbitkan.</p>
                @endforelse
            </div>
        </div>

        <div class="d-flex justify-content-center">
            {{ $articles->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
