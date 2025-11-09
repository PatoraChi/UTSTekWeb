<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Komentar | LahIya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

    <div class="sidebar">
        <h3 class="text-center mb-4">LahIya</h3>
        <a href="{{ url('/') }}"><i class="bi bi-house-door"></i> Home</a>
        <a href="#"><i class="bi bi-people"></i> Topik</a>
        <a href="{{ url('/post/create') }}"><i class="bi bi-plus-circle"></i> Buat</a>
        <a href="#"><i class="bi bi-search"></i> Cari</a>
        <a href="#"><i class="bi bi-bell"></i> Notifikasi</a>
        <a href="#"><i class="bi bi-person-circle"></i> Akun ku</a>
    </div>

    <div class="main-content">
        <div class="feed-column" style="max-width: 700px; margin: 0 auto;">
            
            <h3><i class="bi bi-pencil-square me-2"></i>Edit Komentar</h3>
            <hr>

            <form action="{{ route('comment.update', $comment) }}" method="POST">
                @csrf @method('PUT') <div class="mb-3">
                    <label for="body" class="form-label">Komentar Anda</label>
                    <textarea class="form-control bg-dark text-white" 
                              id="body" 
                              name="body" 
                              rows="5"
                              autofocus>{{ old('body', $comment->body) }}</textarea>
                    
                    @error('body')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ url('/post/' . $comment->post_id) }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>