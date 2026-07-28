@extends('layouts.app')

@section('title', 'Manajemen User')
@section('page-title', 'Administrator — Manajemen User')

@section('content')
@if(session('success'))
    <div class="alert alert-success py-2 mb-4" style="background: rgba(16,185,129,0.2); border: 1px solid var(--accent-green); color: white;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger py-2 mb-4" style="background: rgba(239,68,68,0.2); border: 1px solid var(--accent-red); color: white;">
        {{ session('error') }}
    </div>
@endif

<div class="row g-4">
    <!-- List Users -->
    <div class="col-md-8">
        <div class="as-card">
            <div class="as-card-header">
                <h6>👥 Daftar Pengguna Sistem</h6>
            </div>
            <div class="as-card-body p-0">
                <div class="table-responsive">
                    <table class="as-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Dibuat</th>
                                <th style="width: 100px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td><strong class="text-white">{{ $user->name }}</strong></td>
                                <td><code>{{ $user->email }}</code></td>
                                <td>
                                    @if($user->role === 'admin')
                                        <span class="badge bg-danger">Administrator</span>
                                    @else
                                        <span class="badge bg-primary">User</span>
                                    @endif
                                </td>
                                <td><small class="text-muted">{{ $user->created_at->format('d M Y') }}</small></td>
                                <td class="text-center">
                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.delete', $user->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-as-danger" style="font-size:0.7rem; padding:3px 8px;">Hapus</button>
                                        </form>
                                    @else
                                        <span class="text-muted small">Active</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3">
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <!-- Create User -->
    <div class="col-md-4">
        <div class="as-card">
            <div class="as-card-header">
                <h6>➕ Tambah User Baru</h6>
            </div>
            <div class="as-card-body">
                <form method="POST" action="{{ route('admin.users.create') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label text-secondary small">Nama Lengkap:</label>
                        <input type="text" name="name" class="as-input" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary small">Email:</label>
                        <input type="email" name="email" class="as-input" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary small">Password:</label>
                        <input type="password" name="password" class="as-input" required placeholder="Minimal 6 karakter">
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-secondary small">Role Akses:</label>
                        <select name="role" class="as-select" required>
                            <option value="user">User Biasa</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-as-primary w-100">Tambah User</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
