<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — AtlaSense</title>
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
            width: 420px;
            max-width: 90%;
        }
        .login-logo {
            font-size: 3rem;
            font-weight: 800;
            text-align: center;
            margin-bottom: 30px;
            text-shadow: 3px 3px 0px var(--neo-blue);
            -webkit-text-stroke: 2px #000;
            color: #fff;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-logo">ATLASENSE</div>
        
        <div class="neo-box">
            <div class="card-header-yellow">
                <h3 class="card-header-title text-center">🔑 MASUK KE SISTEM</h3>
            </div>
            
            @if($errors->any())
                <div class="neo-alert neo-alert-danger py-2 mb-3">
                    {{ $errors->first() }}
                </div>
            @endif

            @if(session('success'))
                <div class="neo-alert neo-alert-success py-2 mb-3">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ url('/login') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Alamat Email</label>
                    <input type="email" name="email" class="neo-input" placeholder="contoh@domain.com" value="{{ old('email') }}" required autofocus>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold">Password</label>
                    <input type="password" name="password" class="neo-input" placeholder="••••••••" required>
                </div>
                
                <button type="submit" class="btn-neo btn-neo-lime w-100 py-3 fs-5 fw-bold mb-3">MASUK SEKARANG</button>
                
                <div class="text-center mt-3">
                    <span class="text-secondary small">Belum punya akun?</span>
                    <a href="{{ route('register') }}" class="text-decoration-underline fw-bold text-dark small">Daftar Akun Baru</a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
