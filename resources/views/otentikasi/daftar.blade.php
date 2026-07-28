<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun — AtlaSense</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Atlas CSS -->
    <link rel="stylesheet" href="{{ asset('css/final.css') }}">
    <style>
        body {
            background-color: var(--bg-page);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            width: 440px;
            max-width: 90%;
        }
        .login-logo {
            font-size: 3rem;
            font-weight: 800;
            text-align: center;
            margin-bottom: 30px;
            text-shadow: 3px 3px 0px var(--neo-pink);
            -webkit-text-stroke: 2px #000;
            color: #fff;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-logo">ATLASENSE</div>
        
        <div class="neo-box">
            <div class="card-header-pink">
                <h3 class="card-header-title text-center text-white">📝 DAFTAR AKUN BARU</h3>
            </div>
            
            @if($errors->any())
                <div class="neo-alert neo-alert-danger py-2 mb-3">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ url('/register') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Lengkap</label>
                    <input type="text" name="name" class="neo-input" placeholder="Nama Lengkap Anda" value="{{ old('name') }}" required autofocus>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Alamat Email</label>
                    <input type="email" name="email" class="neo-input" placeholder="contoh@domain.com" value="{{ old('email') }}" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Password</label>
                    <input type="password" name="password" class="neo-input" placeholder="Minimal 6 karakter" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="neo-input" placeholder="Ketik ulang password" required>
                </div>
                
                <button type="submit" class="btn-neo btn-neo-pink text-white w-100 py-3 fs-5 fw-bold mb-3">DAFTAR SEKARANG</button>
                
                <div class="text-center mt-3">
                    <span class="text-secondary small">Sudah punya akun?</span>
                    <a href="{{ route('login') }}" class="text-decoration-underline fw-bold text-dark small">Login di sini</a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
