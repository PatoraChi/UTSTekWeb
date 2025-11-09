<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postingan oleh {{ $post->user->name }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    <meta name="csrf-token" content="{{ csrf_token() }}"> 

    <style>
        /* KUNCI: Izinkan halaman ini di-scroll */
        html, body {
            overflow: auto; /* Izinkan scrollbar utama browser muncul lagi */
            height: auto;   /* Biarkan tinggi halaman otomatis */
        }

        /* Sembunyikan sidebar di halaman ini */
        .sidebar { display: none; }
        
        /* Sesuaikan main-content agar penuh */
        .main-content { 
            margin-left: 0; 
            display: block; 
            height: auto; /* Pastikan main-content tidak terpotong */
            margin-top: 0; /* Hapus margin-top dari style.css jika ada */
        }
        
        .post-card { border: none; border-radius: 0; box-shadow: none; border-bottom: 1px solid #ddd; }
        .comment-form { border-bottom: 1px solid #eee; }

        /* Ini agar warna teksnya kontras dengan background Anda */
        .comment-container, .comment-form, .comment-list p {
             color: #f8f9fa; 
        }
        .post-card {
            border-bottom: 1px solid #2c2f33 !important;
        }
        .comment-form {
            border-bottom: 1px solid #2c2f33 !important;
        }

    </style>
</head>
<body>

    <div class="main-content">
        <div class="container-fluid p-0" style="max-width: 800px; margin: 0 auto;">

            <div class.d-flex align-items-center p-3">
                <a href="{{ url('/#post-' . $post->id) }}" class="text-decoration-none fs-4">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h4 class.ms-3 mb-0>Postingan</h4>
            </div>

            @include('posts._post_card', ['post' => $post, 'user' => $user])

            <div class="comment-form p-3">
                <form action="{{ url('/post/' . $post->id . '/comment') }}" method="POST" class="d-flex align-items-start">
                    @csrf
                    <img src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : 'https://via.placeholder.com/40' }}" 
                         alt="profil" class="rounded-circle me-2" width="40" height="40" style="object-fit: cover;">
                    
                    <textarea name="body" class="form-control me-2" rows="1" placeholder="Tulis komentar..."></textarea>
                    
                    <button type="submit" class="btn btn-primary">Kirim</button>
                </form>
            </div>

            <div class="comment-list p-3">
                @forelse ($comments as $comment)
                    @include('posts._comment', ['comment' => $comment, 'post' => $post, 'user' => $user])
                @empty
                    <p class="text-center text-white">Belum ada komentar.</p>
                @endforelse
            </div>

        </div>
    </div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // --- 1. LIKE POSTINGAN (Kode dari Home.blade.php) ---
        document.querySelectorAll('.like-form').forEach(form => {
            form.addEventListener('submit', async function (event) {
                event.preventDefault(); 
                const url = this.action;
                const postId = url.split('/')[4];
                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json'},
                    });
                    const data = await response.json();
                    const icon = document.getElementById('like-icon-' + postId);
                    const count = document.getElementById('like-count-' + postId);
                    count.textContent = data.newCount;
                    if (data.isLiked) {
                        icon.className = 'bi bi-heart-fill text-danger';
                    } else {
                        icon.className = 'bi bi-heart';
                    }
                } catch (error) { console.error('Error:', error); }
            });
        });

        // --- 2. SAVE POSTINGAN (Kode dari Home.blade.php) ---
        document.querySelectorAll('.save-form').forEach(form => {
            form.addEventListener('submit', async function (event) {
                event.preventDefault(); 
                const url = this.action;
                const postId = url.split('/')[4];
                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json'},
                    });
                    const data = await response.json();
                    const icon = document.getElementById('save-icon-' + postId);
                    if (data.isSaved) {
                        icon.className = 'bi bi-bookmark-fill';
                    } else {
                        icon.className = 'bi bi-bookmark';
                    }
                } catch (error) { console.error('Error:', error); }
            });
        });

        // --- 3. LIKE KOMENTAR (Logika BARU) ---
        document.querySelectorAll('.comment-like-form').forEach(form => {
            form.addEventListener('submit', async function (event) {
                event.preventDefault(); 
                const url = this.action;
                // 'comment/123/like' -> '123'
                const commentId = url.split('/')[4]; 
                
                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json'},
                    });
                    const data = await response.json();
                    
                    const icon = document.getElementById('comment-like-icon-' + commentId);
                    const count = document.getElementById('comment-like-count-' + commentId);
                    
                    count.textContent = data.newCount; // Update angka
                    
                    if (data.isLiked) {
                        icon.className = 'bi bi-heart-fill text-danger';
                    } else {
                        icon.className = 'bi bi-heart';
                    }
                } catch (error) { console.error('Error:', error); }
            });
        });
    </script>
</body>
</html>