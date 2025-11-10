@extends('layouts.app')

@section('content')

<div class="feed-column">
            
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="feed-column">
        @forelse ($posts as $post)
            
            @include('posts._post_card', ['post' => $post, 'user' => $user])
            
        @empty
            <div class="text-center p-5">
                <h3>Belum ada postingan.</h3>
                <p>Jadilah yang pertama membuat postingan!</p>
                <a href="{{ url('/post/create') }}" class="btn btn-primary">Buat Postingan</a>
            </div>
        @endforelse

    </div>

</div> 

@endsection
