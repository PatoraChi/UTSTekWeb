@extends('layouts.app')

@section('content')
<div class="feed-column p-0">
    
    <style>
        .profile-fixed-header {
            position: sticky; top: 0;
            background-color: #121212; z-index: 10;
            padding: 10px 15px; border-bottom: 1px solid #2c2f33;
        }
        .profile-main-header {
            padding: 20px 25px;
            /* Hapus border-bottom dari header, pindah ke tabs */
        }
        .profile-stats {
            text-align: center;
        }
        .profile-stats strong {
            display: block; font-size: 1.1rem;
        }
        .profile-stats small {
            font-size: 0.9rem; color: #aaa;
        }
        
        /* Navigasi Tab Baru */
        .profile-tabs {
            display: flex;
            border-bottom: 1px solid #2c2f33;
        }
        .profile-tab-item {
            flex: 1; /* Bagi rata lebarnya */
            text-align: center;
            padding: 12px 0;
            text-decoration: none;
            color: #aaa; /* Warna non-aktif */
            border-bottom: 2px solid transparent;
            font-weight: 500;
        }
        .profile-tab-item.active {
            color: #fff; /* Warna aktif */
            border-bottom-color: #fff;
        }
        
        /* Grid Postingan (Sudah Ada) */
        .profile-posts-grid {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 4px;
        }
        .profile-post-item {
            position: relative; width: 100%; padding-top: 100%;
            overflow: hidden; background: #222;
        }
        .profile-comment-card {
            display: flex;
            background: #1a1a1a;
            border: 1px solid #2c2f33;
            border-radius: 8px;
            text-decoration: none;
            color: white;
            margin: 0 15px 15px 15px; /* Beri padding di list */
            overflow: hidden;
        }
        .profile-comment-card:hover {
            background: #202020;
        }
        .comment-card-thumbnail {
            width: 100px;
            height: 100px;
            flex-shrink: 0;
            object-fit: cover;
            background: #333;
        }
        .comment-card-body {
            padding: 10px 15px;
        }
        .profile-post-item img, .profile-post-item video {
            position: absolute; top: 0; left: 0;
            width: 100%; height: 100%; object-fit: cover;
        }
        .profile-post-item .bi-play-fill {
            position: absolute; top: 8px; right: 8px;
            font-size: 1.5rem; color: white;
            text-shadow: 0 0 5px rgba(0,0,0,0.7);
        }
        
        /* Daftar Komentar Baru */
        .profile-comment-item {
            background: #1a1a1a;
            border: 1px solid #2c2f33;
            border-radius: 8px;
            text-decoration: none;
            display: block;
        }
        .profile-comment-item:hover {
            background: #202020;
        }
    </style>

    @if ($authUser->id != $profileUser->id)
    <div class="profile-fixed-header d-flex align-items-center">
        <a href="javascript:history.back()" class="text-white fs-4 me-3 text-decoration-none">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h5 class="mb-0 text-white fw-bold">{{ $profileUser->name }}</h5>
    </div>
    @endif

    <div class="profile-main-header text-white">
        <div class="d-flex align-items-center mb-3">
            <img src="{{ $profileUser->profile_image_url }}"
                 alt="Foto Profil" class="rounded-circle" width="90" height="90" style="object-fit: cover;">
            
            <div class="d-flex justify-content-around flex-grow-1">
                <div class="profile-stats">
                    <strong>{{ $postCount }}</strong>
                    <small>Postingan</small>
                </div>
                <div class="profile-stats">
                    <strong id="follower-count">{{ $followerCount }}</strong>
                    <small>Pengikut</small>
                </div>
                <div class="profile-stats">
                    <strong>{{ $followingCount }}</strong>
                    <small>Mengikuti</small>
                </div>
            </div>
        </div>

        <div>
            <h5 class="fw-bold mb-0">{{ $profileUser->name }}</h5>
            @if($profileUser->bio)
                <p class="mb-2">{{ $profileUser->bio }}</p>
            @else
                <p class="text-white mb-2">Belum ada bio.</p>
            @endif
        </div>
          <div class="d-flex gap-2 mt-3">
            @if ($authUser->id == $profileUser->id)
                <a href="{{ url('/edit-profile') }}" class="btn btn-outline-secondary w-100">Edit Profil</a>
            @else
                <form class="follow-form flex-grow-1" action="{{ route('follow.toggle', $profileUser) }}" method="POST">
                    @csrf
                                        <button type="submit" 
                            class="btn {{ $isFollowing ? 'btn-outline-secondary' : 'btn-primary' }} w-100"
                            data-follow-button-user-id="{{ $profileUser->id }}">
                                                <span data-follow-text-user-id="{{ $profileUser->id }}">
                            {{ $isFollowing ? 'Mengikuti' : 'Ikuti' }}
                        </span>
                    </button>
                </form>
                
                @php
                    // Tentukan hak akses admin
                    $isAdmin = in_array($authUser->role, ['admin', 'super_admin', 'owner']);
                    
                    // Tentukan hak akses target
                    $targetIsAdminOrHigher = in_array($profileUser->role, ['admin', 'super_admin', 'owner']);
                    
                    // Super_admin bisa ban admin, tapi admin tidak bisa ban admin
                    $canBan = $isAdmin;
                    if ($authUser->role == 'admin' && $targetIsAdminOrHigher) $canBan = false;
                    if ($authUser->role == 'super_admin' && in_array($profileUser->role, ['super_admin', 'owner'])) $canBan = false;

                @endphp
                
                @if ($canBan)
                <button class="btn btn-outline-secondary" title="Opsi Moderasi" 
                        data-bs-toggle="modal" data-bs-target="#banUserModal">
                    <i class="bi bi-three-dots"></i>
                </button>
                @endif
                
            @endif
        </div>
    </div>

    <nav class="profile-tabs">
        <a href="{{ url()->current() }}?tab=posts" 
           class="profile-tab-item {{ $tab == 'posts' ? 'active' : '' }}">
            <i class="bi bi-grid-3x3"></i>
        </a>
        
        <a href="{{ url()->current() }}?tab=comments" 
           class="profile-tab-item {{ $tab == 'comments' ? 'active' : '' }}">
            <i class="bi bi-chat-dots"></i>
        </a>
        
        <a href="{{ url()->current() }}?tab=liked" 
           class="profile-tab-item {{ $tab == 'liked' ? 'active' : '' }}">
            <i class="bi bi-heart"></i>
        </a>

        @if ($authUser->id == $profileUser->id)
            <a href="{{ url()->current() }}?tab=saved" 
               class="profile-tab-item {{ $tab == 'saved' ? 'active' : '' }}">
                <i class="bi bi-bookmark"></i>
            </a>
        @endif
    </nav>

    <div class="profile-content-area">
        
        @if ($tab == 'posts' || $tab == 'liked' || $tab == 'saved')
            <div class="profile-posts-grid">
                @forelse ($data as $post) <a href="{{ url('/post/' . $post->id) }}" class="profile-post-item">
                        @if ($post->media->first())
                            @if ($post->media->first()->file_type == 'image')
                                <img src="{{ asset('storage/' . $post->media->first()->file_path) }}" alt="Postingan">
                            @elseif ($post->media->first()->file_type == 'video')
                                <video muted preload="metadata">
                                    <source src="{{ asset('storage/' . $post->media->first()->file_path) }}#t=0.5">
                                </video>
                                <i class="bi bi-play-fill"></i>
                            @endif
                        @endif
                    </a>
                @empty
                    <div class="text-center text-muted p-5" style="grid-column: 1 / -1;">
                        <i class="bi bi-camera fs-1"></i>
                        <p class="mt-2">
                            @if($tab == 'posts') Belum ada postingan.
                            @elseif($tab == 'liked') Belum ada postingan yang disukai.
                            @elseif($tab == 'saved') Belum ada postingan yang disimpan.
                            @endif
                        </p>
                    </div>
                @endforelse
            </div>

        @elseif ($tab == 'comments')
            <div class="profile-comments-list pt-3">
                @forelse ($data as $comment) <a href="{{ url('/post/' . $comment->post_id) }}?highlight_comment={{ $comment->id }}" 
                       class="profile-comment-card">
                        
                        @php $media = $comment->post->media->first(); @endphp
                        @if ($media)
                            @if ($media->file_type == 'image')
                                <img src="{{ asset('storage/' . $media->file_path) }}" class="comment-card-thumbnail" alt="Post media">
                            @elseif ($media->file_type == 'video')
                                <video muted class="comment-card-thumbnail">
                                    <source src="{{ asset('storage/' . $media->file_path) }}#t=0.5" type="video/mp4">
                                </video>
                            @endif
                        @else
                            <div class="comment-card-thumbnail d-flex align-items-center justify-content-center">
                                <i class="bi bi-image-fill fs-1 text-muted"></i>
                            </div>
                        @endif
                        
                        <div class="comment-card-body">
                            <small class="text-muted">
                                Anda mengomentari postingan
                                <strong>{{ $comment->post->user->name }}</strong>
                                <span class="ms-1">· {{ $comment->created_at->diffForHumans() }}</span>
                            </small>
                            <p class="text-white fs-6 mt-1 mb-0" style="word-break: break-word;">
                                “{{ $comment->body }}”
                            </p>
                        </div>
                    </a>
                @empty
                    <div class="text-center text-muted p-5">
                        <i class="bi bi-chat-dots fs-1"></i>
                        <p class="mt-2">Belum ada komentar.</p>
                    </div>
                @endforelse
            </div>
        @endif
    </div>
@if (isset($canBan) && $canBan)
<div class="modal fade" id="banUserModal" tabindex="-1" aria-labelledby="banUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white">
            
            <form action="{{ route('moderation.ban', $profileUser) }}" method="POST">
                @csrf
                <div class="modal-header border-secondary">
                    <h5 class="modal-title" id="banUserModalLabel">Ban User: {{ $profileUser->name }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    
                    <div class="mb-3">
                        <label for="duration" class="form-label">Pilih Durasi Ban:</label>
                        <select class="form-select bg-dark-subtle text-muted" id="duration" name="duration">
                            <option value="1_day">1 Hari</option>
                            <option value="7_day">7 Hari</option>
                            <option value="30_day">30 Hari</option>
                            <option value="permanent">Permanen</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="reason" class="form-label">Alasan (Opsional):</label>
                        <textarea class="form-control bg-dark-subtle text-muted" id="reason" name="reason" rows="3"></textarea>
                    </div>
                    
                    <p class="text-danger small">
                        Perhatian: Mem-ban user akan mencegah mereka untuk login. Ini tidak akan menghapus konten mereka.
                    </p>

                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Terapkan Ban</button>
                </div>
            </form>

        </div>
    </div>
</div>
@endif
</div>

@endsection