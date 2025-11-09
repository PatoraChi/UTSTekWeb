<?php

namespace App\Http\Controllers;

use App\Models\Save;
use App\Models\Post; // Penting untuk Route Model Binding
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session; // Penting untuk auth Anda

class SaveController extends Controller
{
    /**
     * Fungsi untuk Save atau Unsave sebuah postingan.
     * * Kita menggunakan "Route Model Binding" (Post $post)
     * Laravel akan otomatis mencari Post berdasarkan {id} di URL.
     */
    public function toggleSave(Post $post) // Asumsi nama fungsinya
        {
            if (!Session::has('user')) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $userId = Session::get('user.id');
            
            // Ganti Model 'Like' menjadi 'Save'
            $save = Save::where('user_id', $userId)
                        ->where('post_id', $post->id)
                        ->first();

            $isSaved = false;

            if ($save) {
                $save->delete();
                $isSaved = false;
            } else {
                Save::create([ // Ganti Model 'Like' menjadi 'Save'
                    'user_id' => $userId,
                    'post_id' => $post->id,
                ]);
                $isSaved = true;
            }

            // Kembalikan status tersimpan (bukan jumlah)
            return response()->json([
                'isSaved' => $isSaved
                // Kita tidak perlu 'newCount' untuk 'save'
            ]);
        }
}