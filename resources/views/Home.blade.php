<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LahIya | Home</title>

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
                        <i class="bi bi-heart"></i>
                        <i class="bi bi-chat-dots"></i>
                        <i class="bi bi-bookmark ms-auto"></i> 
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
</body>
</html>