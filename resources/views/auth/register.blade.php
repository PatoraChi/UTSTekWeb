<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LahIya | Buat Akun</title>

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
                 alt="register illustration" class="img-fluid rounded-3 shadow-lg" style="max-height: 80%;">
        </div>

        <!-- Form Register di kanan -->
        <div class="col-md-6 d-flex align-items-center justify-content-center">
            <div class="login-box p-5 rounded shadow">
                <h3 class="text-center mb-4 fw-bold text-light">Buat Akun LahIya</h3>

                <!-- Form Register -->
                <form action="#" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="username" class="form-label text-light">Username</label>
                        <input type="text" id="username" name="username" class="form-control bg-dark text-light border-secondary" placeholder="Masukkan username" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label text-light">Email</label>
                        <input type="email" id="email" name="email" class="form-control bg-dark text-light border-secondary" placeholder="Masukkan email aktif" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label text-light">Kata Sandi</label>
                        <input type="password" id="password" name="password" class="form-control bg-dark text-light border-secondary" placeholder="Masukkan kata sandi" required>
                    </div>

                    <div class="mb-3">
                        <label for="confirm_password" class="form-label text-light">Ulangi Kata Sandi</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control bg-dark text-light border-secondary" placeholder="Ketik ulang kata sandi" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3">Daftar</button>

                    <div class="text-center">
                        <p class="mb-0 text-light">Sudah punya akun?
                            <a href="{{ url('/login') }}" class="text-decoration-none fw-semibold text-info">Masuk</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>
