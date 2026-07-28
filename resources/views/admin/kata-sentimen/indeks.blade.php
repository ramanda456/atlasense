@extends('layouts.utama')

@section('title', 'Kamus Sentimen')
@section('page-title', 'Kamus Sentimen')

@section('content')
@if(session('success'))
    <div class="neo-alert neo-alert-success">{{ session('success') }}</div>
@endif

<div class="row g-4 mb-4">
    <!-- Add Word Form -->
    <div class="col-md-12">
        <div class="neo-box">
            <div class="card-header-yellow">
                <h4 class="card-header-title">TAMBAH KATA BARU KE KAMUS SENTIMEN</h4>
            </div>
            
            <form action="{{ route('admin.words.add') }}" method="POST" class="row g-3">
                @csrf
                
                <div class="col-md-6">
                    <label class="form-label fw-bold">Kata (Bahasa Inggris / Indonesia):</label>
                    <input type="text" name="word" class="neo-input m-0" placeholder="Masukkan kata tunggal (contoh: crisis, kelangkaan)..." required>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Kelompok Sentimen:</label>
                    <select name="type" class="neo-select m-0" required>
                        <option value="positive">Kategori Positif (Positive Word)</option>
                        <option value="negative">Kategori Negatif (Negative Word)</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn-neo btn-neo-lime w-100 py-3 fw-bold">TAMBAH KATA</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Positive Words Table -->
    <div class="col-md-6">
        <div class="neo-box">
            <div class="card-header-blue">
                <h4 class="card-header-title">DAFTAR KATA SENTIMEN POSITIF</h4>
            </div>
            
            <div class="neo-table-wrapper border-0 box-shadow-none m-0">
                <table class="neo-table">
                    <thead>
                        <tr>
                            <th>Kata Terdaftar</th>
                            <th class="text-center" style="width: 20%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($positives as $pos)
                        <tr>
                            <td><strong class="text-success fs-5">{{ $pos->word }}</strong></td>
                            <td class="text-center">
                                <form action="{{ route('admin.words.delete', ['positive', $pos->id]) }}" method="POST" onsubmit="return confirm('Hapus kata ini?');" class="m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm text-danger p-0 border-0 fw-bold">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center text-secondary py-3">Kamus kata positif kosong.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-3">
                {{ $positives->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    <!-- Negative Words Table -->
    <div class="col-md-6">
        <div class="neo-box">
            <div class="card-header-pink">
                <h4 class="card-header-title text-dark">DAFTAR KATA SENTIMEN NEGATIF</h4>
            </div>
            
            <div class="neo-table-wrapper border-0 box-shadow-none m-0">
                <table class="neo-table">
                    <thead>
                        <tr>
                            <th>Kata Terdaftar</th>
                            <th class="text-center" style="width: 20%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($negatives as $neg)
                        <tr>
                            <td><strong class="text-danger fs-5">{{ $neg->word }}</strong></td>
                            <td class="text-center">
                                <form action="{{ route('admin.words.delete', ['negative', $neg->id]) }}" method="POST" onsubmit="return confirm('Hapus kata ini?');" class="m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm text-danger p-0 border-0 fw-bold">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center text-secondary py-3">Kamus kata negatif kosong.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-3">
                {{ $negatives->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
