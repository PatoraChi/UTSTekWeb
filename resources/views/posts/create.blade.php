@extends('layouts.app')

@section('content')

<div class="feed-column p-4">
    
    <h2 class="mb-4 text-white">Buat Postingan Baru</h2>
            
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ url('/post/store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="caption" class="form-label">Keterangan</label>
            <textarea class="form-control bg-dark text-white" id="caption" name="caption" rows="4" 
                      placeholder="Tulis sesuatu... (contoh: #videolucu #keren)">{{ old('caption') }}</textarea>
        </div>

        <div class="mb-3">
            <label for="media_files" class="form-label">Upload Gambar/Video</label>
            <input class="form-control bg-dark text-white" type="file" id="media_files" name="media_files[]" multiple 
                   accept="image/*,video/*">
            <div class="form-text">Anda bisa memilih lebih dari satu file.</div>
        </div>

        <button type="submit" class="btn btn-primary">Posting</button>
        <a href="{{ url('/') }}" class="btn btn-secondary">Batal</a>
    </form>

</div>

@endsection