<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Diubah | LahIya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light d-flex justify-content-center align-items-center vh-100">
    <div class="text-center p-5 bg-secondary bg-opacity-25 rounded shadow">
        <h3 class="mb-3 text-success">✅ Password Berhasil Diubah!</h3>
        <p class="mb-4">Silakan login kembali dengan password baru Anda.</p>
        <a href="{{ url('/login') }}" class="btn btn-primary">Kembali ke Login</a>
    </div>
</body>
</html>
