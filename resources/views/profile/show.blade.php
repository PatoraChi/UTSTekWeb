<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil Saya | LahIya</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">

  <div class="container py-5">
    <div class="card bg-secondary bg-opacity-25 p-4 shadow text-center">
      <img 
        src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : 'https://via.placeholder.com/120' }}" 
        alt="Foto Profil" 
        class="rounded-circle mb-3" 
        width="120" height="120">

      <h3 class="fw-bold">{{ $user->name }}</h3>
      <p class="text-secondary mb-2">{{ $user->email }}</p>

      @if($user->bio)
        <p class="fst-italic">“{{ $user->bio }}”</p>
      @else
        <p class="text-muted">Belum menambahkan bio.</p>
      @endif

      <div class="mt-3">
        <a href="{{ url('/edit-profile') }}" class="btn btn-outline-light btn-sm"><i class="bi bi-pencil"></i> Edit Profil</a>
        <a href="{{ url('/') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-house"></i> Kembali</a>
      </div>
    </div>
  </div>

</body>
</html>
