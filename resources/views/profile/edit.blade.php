<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Profil | LahIya</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">

  <div class="container py-5">
    <div class="card bg-secondary bg-opacity-25 p-4 shadow">
      <h3 class="text-center mb-4">Edit Profil</h3>

      @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="text-center mb-4">
          <img 
            src="{{ $user->profile_image_url }}"
            alt="Foto Profil" 
            class="rounded-circle mb-3" 
            width="100" height="100"
            id="previewImage">
          <div>
            <input type="file" name="profile_image" class="form-control bg-dark text-light" accept="image/*" onchange="previewFile(this)">
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Nama</label>
          <input type="text" name="name" class="form-control bg-dark text-light" value="{{ old('name', $user->name) }}" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Bio</label>
          <textarea name="bio" class="form-control bg-dark text-light" rows="3">{{ old('bio', $user->bio) }}</textarea>
        </div>

        <div class="d-flex justify-content-between">
          <a href="{{ url('/') }}" class="btn btn-outline-light">Kembali</a>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function previewFile(input) {
        const file = input.files[0];
        const preview = document.getElementById('previewImage');
        if (file) {
            const reader = new FileReader();
            reader.onload = e => preview.src = e.target.result;
            reader.readAsDataURL(file);
        }
    }
  </script>

</body>
</html>
