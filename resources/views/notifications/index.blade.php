@extends('layouts.app')

@section('content')

<div class="feed-column p-4">
    
    <h3 class="text-white mb-4">Notifikasi Anda</h3>

    <div class="notification-list">
        
        @forelse ($notifications as $notif)
            @php
                // Tentukan link tujuan berdasarkan tipe notifikasi
                $link = url('/post/' . $notif->post_id);
                $message = '';
                
                // Ini adalah link "highlight" yang kita diskusikan
                if ($notif->type == 'comment_reply' || $notif->type == 'comment_like') {
                    $link .= '?highlight_comment=' . $notif->comment_id;
                }

                // Tentukan pesan notifikasi
                switch ($notif->type) {
                    case 'post_like':
                        $message = 'menyukai postingan Anda.';
                        break;
                    case 'post_comment':
                        $message = 'mengomentari postingan Anda.';
                        break;
                    case 'comment_like':
                        $message = 'menyukai komentar Anda.';
                        break;
                    case 'comment_reply':
                        $message = 'membalas komentar Anda.';
                        break;
                }
            @endphp

            <a href="{{ $link }}" class="notification-item d-flex align-items-center p-3 mb-2 rounded {{ !$notif->read_at ? 'bg-primary bg-opacity-10' : 'bg-dark' }}" 
               style="text-decoration: none; border: 1px solid #3a3f44;">
                <img src="{{ $notif->sender->profile_image ? asset('storage/' . $notif->sender->profile_image) : 'https://via.placeholder.com/40' }}" 
                     alt="profil" class="rounded-circle me-3" width="40" height="40" style="object-fit: cover;">
                <div class="text-white">
                    <strong>{{ $notif->sender->name }}</strong>
                    <span class="text-white-50">{{ $message }}</span>
                    <small class="d-block text-white-50 mt-1">{{ $notif->created_at->diffForHumans() }}</small>
                </div>
            </a>

        @empty
            <div class="text-center p-5 text-white-50">
                <p>Belum ada notifikasi untuk Anda.</p>
            </div>
        @endforelse

        <div class="d-flex justify-content-center mt-4">
            {{ $notifications->links() }}
        </div>
    </div>

</div> 
@endsection