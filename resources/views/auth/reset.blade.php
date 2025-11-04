<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | LahIya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light d-flex justify-content-center align-items-center vh-100">
    <div class="p-5 bg-secondary bg-opacity-25 rounded shadow" style="width: 100%; max-width: 400px;">
        <h3 class="text-center mb-4 fw-bold">Reset Password</h3>

        <form action="{{ url('/reset-password') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="password" class="form-label">Password Baru</label>
                <input type="password" id="password" name="password" class="form-control bg-dark text-light border-secondary" required>
            </div>

            <div class="mb-3">
                <label for="confirm" class="form-label">Ulangi Password Baru</label>
                <input type="password" id="confirm" name="confirm" class="form-control bg-dark text-light border-secondary" required>
            </div>

            <button type="submit" class="btn btn-success w-100">Simpan Password</button>

            <div class="text-center mt-3">
                <a href="{{ url('/login') }}" class="text-info text-decoration-none"><i class="bi bi-arrow-left"></i> Kembali ke Login</a>
            </div>
        </form>
    </div>
</body>
</html>
