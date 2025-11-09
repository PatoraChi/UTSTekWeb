<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'LahIya' }}</title> 
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

    <div class="sidebar">
        <h3 class="text-center mb-4">LahIya</h3>
        <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}"><i class="bi bi-house-door"></i> Home</a>
        <a href="{{ url('/topik') }}" class="{{ request()->is('topik') ? 'active' : '' }}"><i class="bi bi-people"></i> Topik</a>
        <a href="{{ url('/post/create') }}" class="{{ request()->is('post/create') ? 'active' : '' }}"><i class="bi bi-plus-circle"></i> Buat</a>
        <a href="{{ url('/cari') }}" class="{{ request()->is('cari') ? 'active' : '' }}"><i class="bi bi-search"></i> Cari</a>
        <a href="{{ url('/notifikasi') }}"><i class="bi bi-bell"></i> Notifikasi</a>
        <a href="{{ url('/profile') }}" class="{{ request()->is('profile*') ? 'active' : '' }}"><i class="bi bi-person-circle"></i> Akun ku</a>
    </div>

    <div class="main-content">

        @yield('content')
        
        <div>
            <div>
                <nav class="navbar-custom d-flex justify-content-end align-items-center px-3">

                    <div class="dropdown me-2">
                        <a href="#" class="text-white fs-4" data-bs-toggle="dropdown" aria-expanded="false" style="position: relative;">
                            <i class="bi bi-bell"></i>
                            @if ($unreadCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                                    <span class="visually-hidden">Notifikasi baru</span>
                                </span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow" style="width: 350px;">
                            <li><h6 class="dropdown-header">Notifikasi Terbaru</h6></li>
                            
                            @forelse ($recentNotifications as $notif)
                                @php
                                    // Logika yang sama seperti di halaman penuh
                                    $link = url('/post/' . $notif->post_id);
                                    $message = '';
                                    if ($notif->type == 'comment_reply' || $notif->type == 'comment_like') {
                                        $link .= '?highlight_comment=' . $notif->comment_id;
                                    }
                                    switch ($notif->type) {
                                        case 'post_like': $message = 'menyukai postingan Anda.'; break;
                                        case 'post_comment': $message = 'mengomentari postingan Anda.'; break;
                                        case 'comment_like': $message = 'menyukai komentar Anda.'; break;
                                        case 'comment_reply': $message = 'membalas komentar Anda.'; break;
                                    }
                                @endphp
                            <li>
                                <div class="dropdown-item d-flex align-items-start py-2">
                                <a href="{{ route('profile.show.user', $notif->sender) }}">
                                    <img src="{{ $notif->sender->profile_image_url }}" 
                                        alt="profil" class="rounded-circle me-2" width="35" height="35" style="object-fit: cover;">
                                </a>
                                    <div style="white-space: normal; line-height: 1.3;">
                                        <a href="{{ route('profile.show.user', $notif->sender) }}" class="text-decoration-none text-white">
                                            <strong>{{ $notif->sender->name }}</strong>
                                        </a>
                                        <a href="{{ $link }}" class="text-decoration-none">
                                            <small class="text-white-50">{{ $message }}</small>
                                            <small class="d-block text-white-50 mt-1">{{ $notif->created_at->diffForHumans() }}</small>
                                        </a>
                                    </div>
                                </div>
                            </li>
                            @empty
                                <li><p class="dropdown-item text-center text-white-50 py-3">Tidak ada notifikasi.</p></li>
                            @endforelse

                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-center" href="{{ url('/notifikasi') }}">
                                    Lihat Semua Notifikasi
                                </a>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none text-white"
                        id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="me-2"><strong>{{ $user->name }}</strong></span>
                            <img 
                                src="{{ $user->profile_image_url }}"
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
            </div>
            <div class="recommendation-column">
                <h5>Rekomendasi untuk Anda</h5>
                <hr>
                
                @forelse ($recommendedUsers as $recUser)
                    <div class="d-flex align-items-center mb-3">
                        <a href="{{ route('profile.show.user', $recUser) }}">
                            <img src="{{ $recUser->profile_image_url }}"
                                 alt="profil" class="rounded-circle me-2" width="40" height="40" style="object-fit: cover;">
                        </a>
                        
                        <div class="flex-grow-1">
                            <a href="{{ route('profile.show.user', $recUser) }}" class="text-white text-decoration-none">
                                <strong>{{ $recUser->name }}</strong>
                            </a>
                            <small class="text-white-50 d-block">Populer</small>
                        </div>

                        @if ($followingIds->contains($recUser->id))
                            <a href="{{ route('profile.show.user', $recUser) }}" class="btn btn-sm btn-outline-secondary ms-auto">
                                Mengikuti
                            </a>
                        @else
                            <a href="{{ route('profile.show.user', $recUser) }}" class="btn btn-sm btn-primary ms-auto">
                                Ikuti
                            </a>
                        @endif
                    </div>
                @empty
                    <p class="text-white-50">Tidak ada rekomendasi user.</p>
                @endforelse
                
            </div> 
        </div> </div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
      

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

// Logika untuk "Scroll & Highlight" Komentar
        document.addEventListener('DOMContentLoaded', (event) => {
            // 1. Cek apakah ada 'highlight_comment' di URL
            const urlParams = new URLSearchParams(window.location.search);
            const commentId = urlParams.get('highlight_comment');

            if (commentId) {
                // 2. Cari elemen komentar
                const commentElement = document.getElementById('comment-' + commentId);
                
                if (commentElement) {
                    // 3. Scroll ke elemen tersebut
                    commentElement.scrollIntoView({ behavior: 'smooth', block: 'center' });

                    // 4. Beri efek highlight (flash)
                    commentElement.style.transition = 'background-color 0.5s ease';
                    commentElement.style.backgroundColor = '#4e4b2d'; // Warna highlight kuning gelap
                    
                    // 5. Kembalikan ke warna normal
                    setTimeout(() => {
                        commentElement.style.backgroundColor = ''; 
                    }, 2000); // Hilang setelah 2 detik
                }
            }
        });

// --- 3. LOGIKA UNTUK FOLLOW ---
        document.querySelectorAll('.follow-form').forEach(form => {
            form.addEventListener('submit', async function (event) {
                event.preventDefault(); // Hentikan refresh
                
                const url = this.action;
                
                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    });

                    // Cek jika respons-nya tidak OK
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();

                    // Cari tombol & text di dalamnya
                    const followText = document.getElementById('follow-text');
                    const followButton = followText.parentElement; // Ini adalah <button>
                    
                    // Cari angka follower
                    const followerCount = document.getElementById('follower-count');

                    // Update angka
                    followerCount.textContent = data.newCount; // Ini bagian penting

                    // Update tombol
                    if (data.isFollowing) {
                        followText.textContent = 'Mengikuti';
                        followButton.classList.remove('btn-primary');
                        followButton.classList.add('btn-outline-secondary');
                    } else {
                        followText.textContent = 'Ikuti';
                        followButton.classList.remove('btn-outline-secondary');
                        followButton.classList.add('btn-primary');
                    }

                } catch (error) {
                    console.error('Error following user:', error);
                }
            });
        });
    </script>
</body>
</html>