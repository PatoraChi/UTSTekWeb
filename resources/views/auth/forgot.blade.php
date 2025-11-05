<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LahIya | Lupa Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">

<div class="container d-flex align-items-center justify-content-center vh-100">
    <div class="card bg-secondary text-light p-4 shadow-lg" style="width: 400px;">
        <h4 class="text-center mb-3">Lupa Password</h4>
        <p class="text-center text-light small mb-4">
            Masukkan email kamu untuk menerima kode verifikasi (OTP).
        </p>

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="/forgot-password" method="POST">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Alamat Email</label>
                <input type="email" name="email" id="email" class="form-control bg-dark text-light border-secondary" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Kirim Kode</button>
        </form>

        <div class="text-center mt-3">
            <a href="/login" class="text-info text-decoration-none">
                <i class="bi bi-arrow-left"></i> Kembali ke Login
            </a>
        </div>
    </div>
</div>

</body>
</html>
