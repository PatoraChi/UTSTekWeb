<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Dikirim | LahIya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light d-flex justify-content-center align-items-center vh-100">
    <div class="text-center p-5 bg-secondary bg-opacity-25 rounded shadow">
        <h3 class="mb-3">📧 Tautan Reset Dikirim!</h3>
        <p class="mb-4">Kami telah mengirimkan tautan reset password ke email Anda (simulasi).</p>
        <a href="{{ url('/reset-password') }}" class="btn btn-primary">Buka Tautan Reset</a>
    </div>
</body>
</html>
