<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post; // Penting untuk Route Model Binding
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session; // Penting untuk auth Anda

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
            } else {
                // Jika BELUM ada -> Buat (Like)
                Like::create([
                    'user_id' => $userId,
                    'post_id' => $post->id,
                ]);
                $isLiked = true; // Status menjadi liked
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