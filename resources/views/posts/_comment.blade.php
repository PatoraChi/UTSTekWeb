<div id="comment-{{ $comment->id }}" class="comment-container d-flex align-items-start mb-3">
    
    <a href="{{ route('profile.show.user', $comment->user) }}">
        <img src="{{ $comment->user->profile_image ? asset('storage/' . $comment->user->profile_image) : 'https://via.placeholder.com/40' }}" 
             alt="profil" class="rounded-circle me-2" width="40" height="40" style="object-fit: cover;">
    </a>
    
    <div class="comment-content w-100">
        <div class="comment-header d-flex align-items-center">
            <a href="{{ route('profile.show.user', $comment->user) }}" class="text-decoration-none text-white">
                <strong>{{ $comment->user->name }}</strong>
            </a>
            <small class="text-white ms-2">· {{ $comment->created_at->diffForHumans() }}</small>

            <form class="comment-like-form ms-auto" action="{{ url('/comment/' . $comment->id . '/like') }}" method="POST">
                @csrf
                @php
                    $isCommentLiked = $comment->likes->contains('user_id', $user->id);
                @endphp
                <button type="submit" style="border: none; background: none; padding: 0; color: inherit;">
                    <i id="comment-like-icon-{{ $comment->id }}" class="bi {{ $isCommentLiked ? 'bi-heart-fill text-danger' : 'bi-heart' }}"></i>
                    <span id="comment-like-count-{{ $comment->id }}" class="ms-1">{{ $comment->likes->count() }}</span>
                </button>
            </form>
        </div>

        @if ($comment->parent)
            <small class="text-white d-block">
                Membalas ke <strong>{{ $comment->parent->user->name }}</strong>
            </small>
        @endif

        <p class="mb-1">{{ $comment->body }}</p>

            <a href="#" class="text-decoration-none me-2" data-bs-toggle="collapse" 
                data-bs-target="#reply-form-{{ $comment->id }}">Balas</a>

            @if ($comment->user_id == $user->id)
                <a href="{{ route('comment.edit', $comment) }}" class="text-decoration-none text-white me-2">Edit</a>
                
                <form action="{{ route('comment.destroy', $comment) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus komentar ini? Semua balasan akan ikut terhapus.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-link text-danger p-0 m-0 align-baseline" 
                            style="text-decoration: none; font-size: inherit;">
                        Hapus
                    </button>
                </form>
            @endif
                @if ($comment->replies->count() > 0)
            <a href="#" class="text-decoration-none" data-bs-toggle="collapse" 
               data-bs-target="#replies-{{ $comment->id }}">
               Tampilkan {{ $comment->replies->count() }} balasan
            </a>
        @endif

        <div class="collapse mt-2" id="reply-form-{{ $comment->id }}">
            <form action="{{ url('/post/' . $post->id . '/comment') }}" method="POST" class="d-flex align-items-start">
                @csrf
                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                
                <textarea name="body" class="form-control me-2" rows="1" placeholder="Balas komentar..."></textarea>
                <button type="submit" class="btn btn-sm btn-primary">Balas</button>
            </form>
        </div>

        <div class="collapse mt-3" id="replies-{{ $comment->id }}">
            @foreach ($comment->replies as $reply)
                @include('posts._comment', ['comment' => $reply, 'post' => $post, 'user' => $user])
            @endforeach
        </div>
    </div>
</div>