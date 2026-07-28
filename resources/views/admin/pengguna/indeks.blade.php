@extends('layouts.utama')

@section('title', 'Kelola Pengguna')
@section('page-title', 'Kelola Pengguna')

@section('content')
@if(session('success'))
    <div class="neo-alert neo-alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="neo-alert neo-alert-danger">{{ session('error') }}</div>
@endif

<div class="row g-4">
    <!-- Add User Form -->
    <div class="col-md-4">
        <div class="neo-box">
            <div class="card-header-yellow">
                <h4 class="card-header-title">TAMBAH PENGGUNA</h4>
            </div>
            
            <form action="{{ route('admin.users.create') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Lengkap:</label>
                    <input type="text" name="name" class="neo-input" placeholder="Masukkan nama..." required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Email:</label>
                    <input type="email" name="email" class="neo-input" placeholder="Masukkan email..." required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Password:</label>
                    <input type="password" name="password" class="neo-input" placeholder="Minimal 6 karakter..." required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Role Hak Akses:</label>
                    <select name="role" class="neo-select" required>
                        <option value="user">Pengguna Biasa (User)</option>
                        <option value="admin">Administrator (Admin)</option>
                    </select>
                </div>

                <button type="submit" class="btn-neo btn-neo-lime w-100 fw-bold py-2 mt-2">SIMPAN PENGGUNA</button>
            </form>
        </div>
    </div>

    <!-- Users Table List -->
    <div class="col-md-8">
        <div class="neo-box">
            <div class="card-header-blue">
                <h4 class="card-header-title">👥 DAFTAR PENGGUNA SISTEM</h4>
            </div>
            
            <div class="neo-table-wrapper border-0 box-shadow-none m-0">
                <table class="neo-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td><strong>{{ $user->name }}</strong></td>
                            <td><code>{{ $user->email }}</code></td>
                            <td>
                                <span class="neo-badge {{ $user->role === 'admin' ? 'neo-badge-pink' : 'neo-badge-blue' }}">
                                    {{ strtoupper($user->role) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-success border border-dark text-dark fw-bold">AKTIF</span>
                            </td>
                            <td class="text-center">
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');" class="m-0 d-inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm text-danger p-0 border-0 fw-bold">Hapus</button>
                                    </form>
                                @else
                                    <span class="text-secondary small font-italic">Sedang Aktif</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-center">
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
