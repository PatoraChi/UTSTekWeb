<?php

namespace App\Http\Controllers;

use App\Models\Comment; // <-- Ganti
use App\Models\CommentLike; // <-- Ganti
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Notification;

class CommentLikeController extends Controller
{
    public function toggleLike(Comment $comment) // <-- Ganti
    {
        if (!Session::has('user')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $userId = Session::get('user.id');
        
        $like = CommentLike::where('user_id', $userId) // <-- Ganti
                            ->where('comment_id', $comment->id) // <-- Ganti
                            ->first();

        $isLiked = false;

        if ($like) {
            $like->delete();
            $isLiked = false;
            // Hapus notifikasi (opsional)
            Notification::where('sender_id', $userId)
                        ->where('comment_id', $comment->id)
                        ->where('type', 'comment_like')
                        ->delete();
        } else {
            CommentLike::create([ // <-- Ganti
                'user_id' => $userId,
                'comment_id' => $comment->id // <-- Ganti
            ]);
            $isLiked = true;
            // --- 2. TAMBAHKAN LOGIKA NOTIFIKASI DI SINI ---   
            // Hanya kirim notif jika kita tidak me-like komentar sendiri
            if ($userId != $comment->user_id) {
                Notification::create([
                    'user_id'   => $comment->user_id, // Penerima (Pemilik Komentar)
                    'sender_id' => $userId,         // Pelaku (Yang nge-like)
                    'type'      => 'comment_like',
                    'post_id'   => $comment->post_id, // Ambil post_id dari komentar
                    'comment_id'=> $comment->id
                ]);
            }
        }

        $newCount = $comment->likes()->count();

        return response()->json([
            'isLiked'  => $isLiked,
            'newCount' => $newCount
        ]);
    }
}