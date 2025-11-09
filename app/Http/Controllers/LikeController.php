<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post; // Penting untuk Route Model Binding
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session; // Penting untuk auth Anda
use App\Models\Notification;
class LikeController extends Controller
{
    /**
     * Fungsi untuk Like atau Unlike sebuah postingan.
     * * Kita menggunakan "Route Model Binding" (Post $post)
     * Laravel akan otomatis mencari Post berdasarkan {id} di URL.
     */
    public function toggleLike(Post $post)
        {

            if (!Session::has('user')) {
                // Jika request-nya AJAX, kembalikan error JSON
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $userId = Session::get('user.id');
            $like = Like::where('user_id', $userId)
                        ->where('post_id', $post->id)
                        ->first();

            $isLiked = false; // Status default (menjadi not-liked)

            if ($like) {
                // Jika SUDAH ada -> Hapus (Unlike)
                $like->delete();
                $isLiked = false;
                Notification::where('sender_id', $userId)
                        ->where('post_id', $post->id)
                        ->where('type', 'post_like')
                        ->delete();
            } else {
                // Jika BELUM ada -> Buat (Like)
                Like::create([
                    'user_id' => $userId,
                    'post_id' => $post->id,
                ]);
                $isLiked = true; // Status menjadi liked
                // --- 2. TAMBAHKAN LOGIKA NOTIFIKASI DI SINI ---
                // Hanya kirim notif jika kita tidak me-like postingan sendiri
                if ($userId != $post->user_id) {
                    Notification::create([
                        'user_id'   => $post->user_id, // Penerima (Pemilik Post)
                        'sender_id' => $userId,         // Pelaku (Yang nge-like)
                        'type'      => 'post_like',
                        'post_id'   => $post->id,
                    ]);
                }
            }

            // Ambil jumlah like terbaru
            $newCount = $post->likes()->count();

            // Kembalikan respon JSON
            return response()->json([
                'isLiked'  => $isLiked,
                'newCount' => $newCount
            ]);
        }
}