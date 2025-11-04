<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LahIya | Login</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="dark-mode">

<div class="container-fluid login-page">
    <div class="row vh-100">
        <!-- Gambar di kiri -->
        <div class="col-md-6 d-none d-md-flex align-items-center justify-content-center bg-dark">
            <img src="https://images.unsplash.com/photo-1525182008055-f88b95ff7980?w=600"
                 alt="login illustration" class="img-fluid rounded-3 shadow-lg" style="max-height: 80%;">
        </div>

        <!-- Form Login di kanan -->
        <div class="col-md-6 d-flex align-items-center justify-content-center">
            <div class="login-box p-5 rounded shadow">
                <h3 class="text-center mb-4 fw-bold text-light">LahIya</h3>
                    @if (session('error'))
                    <div class="alert alert-danger text-center">
                        {{ session('error') }}
                    </div>
                    @endif
                <!-- Form Login -->
                <form action="{{ url('/login') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label text-light">Email atau Username</label>
                        <input type="text" id="email" name="email" class="form-control bg-dark text-light border-secondary" placeholder="Masukkan email atau username" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label text-light">Kata Sandi</label>
                        <input type="password" id="password" name="password" class="form-control bg-dark text-light border-secondary" placeholder="Masukkan kata sandi" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3">Masuk</button>

                    <div class="text-center">
                        <a href="{{ url('/forgot-password') }}" class="text-decoration-none small text-info">Lupa kata sandi?</a>
                    </div>

                    <hr class="my-4 text-secondary">

                    <div class="text-center">
                        <p class="mb-0 text-light">Belum punya akun?
                            <a href="{{ url('/register') }}" class="text-decoration-none fw-semibold text-info">Buat akun</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>
