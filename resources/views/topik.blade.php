@extends('layouts.app')

@section('content')

<div class="feed-column">
    
    <h3 class="text-white mb-3">Topik Populer (1 Bulan Terakhir)</h3>

    <style>
        .tag-grid-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr); /* 2 kolom */
            gap: 15px; /* Jarak antar kotak */
        }
        .tag-box {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #2c2f33; /* Warna dark-grey */
            border: 1px solid #3a3f44;
            border-radius: 8px;
            padding: 2.5rem 1rem; /* Bikin kotak jadi tinggi */
            text-decoration: none;
            color: #ffffff;
            font-size: 1.2rem;
            font-weight: bold;
            transition: background-color 0.2s;
        }
        .tag-box:hover {
            background-color: #3a3f44; /* Efek hover */
            color: #ffffff;
        }
    </style>

    <div class="tag-grid-container">
        @forelse ($popularTags as $tag)
            <a href="{{ url('/cari?q=' . urlencode('#' . $tag->name)) }}" class="tag-box">
                #{{ $tag->name }}
            </a>
        @empty
            <div class="text-center p-5 text-white">
                <p>Belum ada topik populer bulan ini.</p>
            </div>
        @endforelse
    </div>

</div> 

@endsection