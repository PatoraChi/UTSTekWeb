<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Postingan Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    
    <div class="sidebar">
        <h3 class="text-center mb-4">LahIya</h3>
        <a href="{{ url('/') }}"><i class="bi bi-house-door"></i> Home</a>
        <a href="#"><i class="bi bi-people"></i> Topik</a>
        <a href="{{ url('/post/create') }}" class="active"><i class="bi bi-plus-circle"></i> Buat</a>
        <a href="#"><i class="bi bi-search"></i> Cari</a>
        <a href="#"><i class="bi bi-bell"></i> Notifikasi</a>
        <a href="#"><i class="bi bi-person-circle"></i> Akun ku</a>
    </div>

    <div class="main-content">
        <div class="container">
            <h2 class="mb-4">Buat Postingan Baru</h2>
            
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ url('/post/store') }}" method="POST" enctype="multipart/form-data">
                @csrf <div class="mb-3">
                    <label for="caption" class="form-label">Keterangan</label>
                    <textarea class="form-control" id="caption" name="caption" rows="4" 
                              placeholder="Tulis sesuatu... (contoh: #videolucu #keren)">{{ old('caption') }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="media_files" class="form-label">Upload Gambar/Video</label>
                    <input class="form-control" type="file" id="media_files" name="media_files[]" multiple 
                           accept="image/*,video/*">
                    <div class="form-text">Anda bisa memilih lebih dari satu file.</div>
                </div>

                <button type="submit" class="btn btn-primary">Posting</button>
                <a href="{{ url('/') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>