@extends('layouts.app')

@section('content')

<div class="feed-column">

    <div class="search-bar-container mb-4" style="position: sticky; top: 0; background: #121212; padding-top: 5px; z-index: 5;">
        <form action="{{ url('/cari') }}" method="GET">
            <div class="input-group">
                <input type="text" 
                       class="form-control bg-dark text-white border-secondary" 
                       placeholder="Cari user (cth: Budi) atau tag (cth: #laravel)" 
                       name="q" 
                       value="{{ $query ?? '' }}"
                       autofocus>
                <button class="btn btn-outline-secondary" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>

    <div class="search-results">

        @if (empty($query))
            <div class="text-center p-5 text-white-50">
                <p>Mulai mencari user atau tag.</p>
            </div>

        @elseif ($searchType == 'user')
            
            <h4 class="text-white mb-3">Hasil Pencarian User untuk "{{ $query }}"</h4>
            @forelse ($usersResult as $foundUser)
                <div class="d-flex align-items-center mb-3 p-3 bg-dark rounded">
                    <a href="{{ route('profile.show.user', $foundUser) }}">
                        <img src="{{ $notif->sender->profile_image_url }}"
                             alt="profil" class="rounded-circle me-3" width="40" height="40" style="object-fit: cover;">
                    </a>
                    <div class="flex-grow-1">
                        <a href="{{ route('profile.show.user', $foundUser) }}" class="text-decoration-none text-white">
                            <strong class="text-white">{{ $foundUser->name }}</strong>
                        </a>
                        <small class="text-muted d-block">{{ $foundUser->email }}</small>
                    </div>
                    <a href="{{ route('profile.show.user', $foundUser) }}" class="btn btn-sm btn-outline-primary ms-auto">Lihat Profil</a>
                </div>
            @empty
                <div class="text-center p-5 text-white-50">
                    <p>User dengan nama '{{ $query }}' tidak ditemukan.</p>
                </div>
            @endforelse

        @elseif ($searchType == 'tag')

            <h4 class="text-white mb-3">Hasil Postingan untuk Tag "{{ $query }}"</h4>
            @forelse ($postsResult as $post)
                @include('posts._post_card', ['post' => $post, 'user' => $user])
            
            @empty
                <div class="text-center p-5 text-white-50">
                    <p>Tidak ada postingan dengan tag '{{ $query }}' ditemukan.</p>
                </div>
            @endforelse

            <div class="d-flex justify-content-center">
                {{ $postsResult->appends(['q' => $query])->links() }}
            </div>

        @endif

    </div>

</div> 

@endsection