<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password | LahIya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
<div class="container-fluid vh-100 d-flex align-items-center justify-content-center">
    <div class="row w-100">
        <div class="col-md-6 d-none d-md-flex justify-content-center align-items-center">
            <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=600"
                 class="img-fluid rounded-3 shadow-lg" alt="illustration">
        </div>

        <div class="col-md-6 d-flex align-items-center justify-content-center">
            <div class="p-5 rounded shadow bg-secondary bg-opacity-25" style="width: 100%; max-width: 400px;">
                <h3 class="text-center mb-4 fw-bold">Lupa Password</h3>

                <form action="{{ url('/forgot-password') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Kirim Kode Verifikasi</button>
                </form>

            </div>
        </div>
    </div>
</div>
</body>
</html>
