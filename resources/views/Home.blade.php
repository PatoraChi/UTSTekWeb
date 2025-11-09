<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LahIya | Home</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    </head>
<body>

    <div class="sidebar">
        <h3 class="text-center mb-4">LahIya</h3>
        <a href="{{ url('/') }}" class="active"><i class="bi bi-house-door"></i> Home</a>
        <a href="#"><i class="bi bi-people"></i> Topik</a>
        <a href="{{ url('/post/create') }}"><i class="bi bi-plus-circle"></i> Buat</a>
        <a href="#"><i class="bi bi-search"></i> Cari</a>
        <a href="#"><i class="bi bi-bell"></i> Notifikasi</a>
        <a href="#"><i class="bi bi-person-circle"></i> Akun ku</a>
    </div>

    <nav class="navbar-custom d-flex justify-content-end align-items-center px-3">
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-decoration-none"
               id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                
                <img 
                    src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : 'https://via.placeholder.com/35' }}" 
                    alt="profil" 
                    class="profile-img rounded-circle"
                    width="35" height="35"  
                    style="object-fit: cover;">
            </a>

            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="profileDropdown">
                <li><a class="dropdown-item" href="{{ url('/profile') }}"><i class="bi bi-person me-2"></i>Profil Saya</a></li>
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

    <div class="main-content">

        <div class="feed-column">
            
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @forelse ($posts as $post)
                <div class="post-card shadow-sm">
                    
                    <div class="post-header">
                        <img src="{{ $post->user->profile_image ? asset('storage/' . $post->user->profile_image) : 'https://via.placeholder.com/35' }}" 
                             alt="profil" class="rounded-circle" width="35" height="35" style="object-fit: cover;">
                        <strong>{{ $post->user->name }}</strong>
                        <small class="text-muted ms-auto">{{ $post->created_at->diffForHumans() }}</small>
                    </div>

                    <div class="post-media">
                        @foreach ($post->media as $media)
                            @if ($media->file_type == 'image')
                                <img src="{{ asset('storage/' . $media->file_path) }}" alt="Post media">
                            @elseif ($media->file_type == 'video')
                                <video controls style="width: 100%;">
                                    <source src="{{ asset('storage/' . $media->file_path) }}" type="video/mp4">
                                    Browser Anda tidak mendukung tag video.
                                </video>
                            @endif
                        @endforeach
                    </div>
                                        
                    <div class="post-actions">

                    @php
                        $isLiked = $post->likes->contains('user_id', $user->id);
                        $isSaved = $post->saves->contains('user_id', $user->id);
                    @endphp

                    <form class="like-form" action="{{ url('/post/' . $post->id . '/like') }}" method="POST" style="display: inline-block;">
                        @csrf
                        <button type="submit" style="border: none; background: none; padding: 0; color: inherit;">
                            <i id="like-icon-{{ $post->id }}" 
                            class="bi {{ $isLiked ? 'bi-heart-fill text-danger' : 'bi-heart' }}">
                            </i>
                        </button>
                    </form>

                    <span id="like-count-{{ $post->id }}" class="fw-bold ms-2">{{ $post->likes->count() }}</span>
                    
                    <i class="bi bi-chat-dots ms-3"></i> 

                    <form class="save-form ms-auto" action="{{ url('/post/' . $post->id . '/save') }}" method="POST">
                        @csrf
                        <button type="submit" style="border: none; background: none; padding: 0; color: inherit;">
                            <i id="save-icon-{{ $post->id }}" 
                            class="bi {{ $isSaved ? 'bi-bookmark-fill' : 'bi-bookmark' }}">
                            </i>
                        </button>
                    </form>

                </div>

                    <div class="post-caption">
                        <p><strong>{{ $post->user->name }}</strong> {{ $post->caption }}</p>
                    </div>

                </div>
            
            @empty
                <div class="text-center p-5">
                    <h3>Belum ada postingan.</h3>
                    <p>Jadilah yang pertama membuat postingan!</p>
                    <a href="{{ url('/post/create') }}" class="btn btn-primary">Buat Postingan</a>
                </div>
            @endforelse

        </div> <div class="recommendation-column">
            <h5>Rekomendasi untuk Anda</h5>
            <hr>
            
            <div class="d-flex align-items-center mb-2">
                <img src="https://via.placeholder.com/40" class="rounded-circle me-2" alt="rekomendasi">
                <div>
                    <strong>user_anime_keren</strong><br>
                    <small class="text-muted">Populer</small>
                </div>
                <a href="#" class="btn btn-sm btn-primary ms-auto">Ikuti</a>
            </div>
            
            <div class="d-flex align-items-center mb-2">
                <img src="https://via.placeholder.com/40" class="rounded-circle me-2" alt="rekomendasi">
                <div>
                    <strong>gamer_sejati</strong><br>
                    <small class="text-muted">Sering posting #game</small>
                </div>
                <a href="#" class="btn btn-sm btn-primary ms-auto">Ikuti</a>
            </div>
            
        </div> </div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Ambil CSRF token dari meta tag yang kita buat
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // --- 1. LOGIKA UNTUK LIKE ---
        // Cari SEMUA form 'like' di halaman
        document.querySelectorAll('.like-form').forEach(form => {
            // Tambahkan 'listener' untuk 'submit'
            form.addEventListener('submit', async function (event) {
                // KUNCI: Hentikan refresh halaman
                event.preventDefault(); 

                const url = this.action;
                const postId = url.split('/')[4]; // Ambil ID postingan dari URL form
                
                try {
                    // Kirim request ke server di 'background'
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json', // Minta respon JSON
                            'Content-Type': 'application/json'
                        },
                        // body tidak perlu diisi, URL sudah cukup
                    });

                    // Ambil data JSON dari controller
                    const data = await response.json();

                    // Cari elemen ikon dan angka yang spesifik
                    const icon = document.getElementById('like-icon-' + postId);
                    const count = document.getElementById('like-count-' + postId);

                    // Update angka like
                    count.textContent = data.newCount;

                    // Update ikon (ganti class-nya)
                    if (data.isLiked) {
                        icon.classList.remove('bi-heart');
                        icon.classList.add('bi-heart-fill', 'text-danger');
                    } else {
                        icon.classList.remove('bi-heart-fill', 'text-danger');
                        icon.classList.add('bi-heart');
                    }

                } catch (error) {
                    console.error('Error liking post:', error);
                }
            });
        });

        // --- 2. LOGIKA UNTUK SAVE (SAMA PERSIS) ---
        // Cari SEMUA form 'save' di halaman
        document.querySelectorAll('.save-form').forEach(form => {
            form.addEventListener('submit', async function (event) {
                // KUNCI: Hentikan refresh halaman
                event.preventDefault(); 

                const url = this.action;
                const postId = url.split('/')[4];
                
                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    });

                    const data = await response.json();

                    // Cari ikon 'save' yang spesifik
                    const icon = document.getElementById('save-icon-' + postId);

                    // Update ikon 'save'
                    if (data.isSaved) {
                        icon.classList.remove('bi-bookmark');
                        icon.classList.add('bi-bookmark-fill');
                    } else {
                        icon.classList.remove('bi-bookmark-fill');
                        icon.classList.add('bi-bookmark');
                    }

                } catch (error) {
                    console.error('Error saving post:', error);
                }
            });
        });
    </script>
</body>
</html>