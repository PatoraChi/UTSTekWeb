<div class="post-card shadow-sm" id="post-{{ $post->id }}">
    
    <div class="post-header">
        <a href="{{ route('profile.show.user', $post->user) }}" class="d-flex align-items-center text-decoration-none text-white">
            <img src="{{ $post->user->profile_image_url }}"
                 alt="profil" class="rounded-circle" width="35" height="35" style="object-fit: cover;">
            <strong class="ms-2">{{ $post->user->name }}</strong>
        </a>

        <small class="text-white ms-auto">{{ $post->created_at->diffForHumans() }}</small>

        @php
            // $user adalah user yang sedang login (diumpan dari controller)
            $isOwner = $post->user_id == $user->id;
            $isAdmin = in_array($user->role, ['admin', 'super_admin', 'owner']);
            
            // --- LOGIKA HIERARKI BARU ---
            $canDelete = false;
            if ($isOwner) {
                $canDelete = true;
            } 
            // Jika bukan pemilik, cek apakah user adalah admin
            elseif ($isAdmin) {
                $authRole = $user->role;
                $targetRole = $post->user->role; // Role pemilik postingan

                // Admin tidak bisa hapus admin lain atau lebih tinggi
                if ($authRole == 'admin' && in_array($targetRole, ['admin', 'super_admin', 'owner'])) {
                    $canDelete = false;
                }
                // Super_admin tidak bisa hapus super_admin lain atau owner
                elseif ($authRole == 'super_admin' && in_array($targetRole, ['super_admin', 'owner'])) {
                    $canDelete = false;
                }
                // Jika lolos (misal: Owner hapus Admin, atau Admin hapus User)
                else {
                    $canDelete = true;
                }
            }
            // --- AKHIR LOGIKA HIERARKI ---
        @endphp

        @if ($isOwner || $canDelete)
            <div class="dropdown ms-2">
                <a href="#" class="text-decoration-none text-white" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-three-dots fs-5"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                    
                    @if ($isOwner)
                    <li>
                        <a class="dropdown-item" href="{{ route('post.edit', $post) }}">
                            <i class="bi bi-pencil-square me-2"></i>Edit Caption
                        </a>
                    </li>
                    @endif

                    @if ($canDelete)
                    <li>
                        <form action="{{ route('post.destroy', $post) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus postingan ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-trash3 me-2"></i>Hapus Postingan
                            </button>
                        </form>
                    </li>
                    @endif
                </ul>
            </div>
        @endif
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
        
        <a href="{{ url('/post/' . $post->id) }}" style="color: inherit; text-decoration: none;">
            <i class="bi bi-chat-dots ms-3"></i>
        </a>
        <span class="fw-bold ms-2" style="font-size: 1rem;">
            {{ $post->all_comments_count }}
        </span>

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