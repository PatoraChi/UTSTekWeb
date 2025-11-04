<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LahIya | Home</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h3 class="text-center mb-4">LahIya</h3>
        <a href="#" class="active"><i class="bi bi-house-door"></i> Home</a>
        <a href="#"><i class="bi bi-people"></i> Topik</a>
        <a href="#"><i class="bi bi-plus-circle"></i> Buat</a>
        <a href="#"><i class="bi bi-search"></i> Cari</a>
        <a href="#"><i class="bi bi-bell"></i> Notifikasi</a>
        <a href="#"><i class="bi bi-person-circle"></i> Akun ku</a>
    </div>

    <!-- Navbar -->
    <nav class="navbar-custom d-flex justify-content-end align-items-center px-3">
        <!-- Dropdown Profil -->
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" 
               id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="https://via.placeholder.com/35" alt="profil" class="profile-img rounded-circle">
            </a>

            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="profileDropdown">
                <li><a class="dropdown-item" href="{{ url('/edit-profile') }}"><i class="bi bi-pencil-square me-2"></i>Edit Profil</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-danger" href="{{ url('/logout') }}">
                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Konten Utama -->
    <div class="main-content">
        <div class="container">
            <h2>Selamat Datang di LahIya</h2>
            <p>Ini adalah halaman utama kamu. Sidebar di kiri bisa kamu pakai untuk navigasi.</p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
