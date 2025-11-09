@extends('layouts.app')

@section('content')
<div class="feed-column p-0">
    
    <style>
        .profile-fixed-header {
            position: sticky;
            top: 0;
            background-color: #121212; /* Warna background utama */
            z-index: 10;
            padding: 10px 15px;
            border-bottom: 1px solid #2c2f33;
        }
        .profile-main-header {
            padding: 20px 25px;
            border-bottom: 1px solid #2c2f33;
        }
        .profile-stats {
            text-align: center;
        }
        .profile-stats strong {
            display: block;
            font-size: 1.1rem;
        }
        .profile-stats small {
            font-size: 0.9rem;
            color: #aaa;
        }
        .profile-posts-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 4px;
        }
        .profile-post-item {
            position: relative;
            width: 100%;
            padding-top: 100%; /* Membuat kotak aspek 1:1 */
            overflow: hidden;
            background: #222;
        }
        .profile-post-item img,
        .profile-post-item video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .profile-post-item .bi-play-fill {
            position: absolute;
            top: 8px;
            right: 8px;
            font-size: 1.5rem;
            color: white;
            text-shadow: 0 0 5px rgba(0,0,0,0.7);
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
            <img src="{{ $profileUser->profile_image ? asset('storage/' . $profileUser->profile_image) : 'https://via.placeholder.com/90' }}" 
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
                <p class="text-muted mb-2">Belum ada bio.</p>
            @endif
        </div>

        <div class="d-grid gap-2 mt-3">
            @if ($authUser->id == $profileUser->id)
                <a href="{{ url('/edit-profile') }}" class="btn btn-outline-secondary">Edit Profil</a>
            @else
                <form class="follow-form" action="{{ route('follow.toggle', $profileUser) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn {{ $isFollowing ? 'btn-outline-secondary' : 'btn-primary' }} w-100">
                        <span id="follow-text">{{ $isFollowing ? 'Mengikuti' : 'Ikuti' }}</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="profile-posts-grid">
        @forelse ($posts as $post)
            <a href="{{ url('/post/' . $post->id) }}" class="profile-post-item">
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
                <p class="mt-2">Belum ada postingan.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection