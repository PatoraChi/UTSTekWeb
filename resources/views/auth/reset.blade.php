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
        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="password" class="form-label text-light">Password Baru</label>
                <input type="password" name="password" class="form-control bg-dark text-light" required>
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label text-light">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="form-control bg-dark text-light" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Ubah Password</button>
        </form>

    </div>
</body>
</html>
