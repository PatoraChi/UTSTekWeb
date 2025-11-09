@extends('layouts.app')

@section('content')
<div class="feed-column p-0">

    <style>
        .post-fixed-header {
            position: sticky;
            top: 0;
            background-color: #121212;
            z-index: 10;
            padding: 10px 15px;
            border-bottom: 1px solid #2c2f33;
        }
    </style>
    <div class="post-fixed-header d-flex align-items-center">
        <a href="javascript:history.back()" class="text-white fs-4 me-3 text-decoration-none">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h5 class="mb-0 text-white fw-bold">Postingan</h5>
    </div>

    <div class="p-3">
        @include('posts._post_card', ['post' => $post, 'user' => $authUser])
    </div>
    
    <hr class="text-white-50">

    <div class="comment-section p-3">
        <h5 class="text-white mb-3">Komentar</h5>
        
        <form action="{{ url('/post/' . $post->id . '/comment') }}" method="POST" class="d-flex align-items-start mb-4">
            @csrf
            <input type="hidden" name="parent_id" value=""> 
            
            <img src="{{ $authUser->profile_image ? asset('storage/' . $authUser->profile_image) : 'https://via.placeholder.com/40' }}" 
                 alt="profil" class="rounded-circle me-2" width="40" height="40" style="object-fit: cover;">
            
            <textarea name="body" class="form-control bg-dark text-white me-2" rows="1" placeholder="Tulis komentar..." required></textarea>
            <button type="submit" class="btn btn-primary">Kirim</button>
        </form>

        <div class="comment-list">
            @forelse ($comments as $comment)
                @include('posts._comment', ['comment' => $comment, 'post' => $post, 'user' => $authUser])
            @empty
                <div class="text-center text-muted p-5">
                    <p>Belum ada komentar.</p>
                </div>
            @endforelse
        </div>

    </div>
</div>
@endsection