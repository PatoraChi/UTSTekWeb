@extends('layouts.app')

@section('content')

<div class="feed-column p-4" style="max-width: 700px; margin: 0 auto;">
    
    <h3 class="text-white"><i class="bi bi-pencil-square me-2"></i>Edit Postingan</h3>
    <hr class="text-white">

    <div class="post-media mb-3">
        @foreach ($post->media as $media)
            @if ($media->file_type == 'image')
                <img src="{{ $post->media->first()->url }}" alt="Post media" class="img-fluid rounded mb-2" style="max-height: 400px; object-fit: contain;">
            @elseif ($media->file_type == 'video')
                <video controls style="width: 100%;" class="rounded mb-2">
                    <source src="{{ $post->media->first()->url }}#t=0.5">
                </video>
            @endif
        @endforeach
    </div>

    <form action="{{ route('post.update', $post) }}" method="POST">
        @csrf @method('PUT') <div class="mb-3">
            <label for="caption" class="form-label">Caption</label>
            <textarea class="form-control bg-dark text-white" 
                      id="caption" 
                      name="caption" 
                      rows="6"
                      placeholder="Tulis captionmu...">{{ old('caption', $post->caption) }}</textarea>
            
            @error('caption')
                <div class="text-danger mt-2">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex justify-content-end">
            <a href="{{ url('/') }}" class="btn btn-secondary me-2">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>

</div>

@endsection