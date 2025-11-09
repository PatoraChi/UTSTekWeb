<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Notification;

class CommentController extends Controller
{
    /**
     * Menyimpan komentar baru (atau balasan).
     */
    public function store(Request $request, Post $post)
    {
        if (!Session::has('user')) {
            return redirect('/login');
        }

        $request->validate([
            'body' => 'required|string|max:2000',
            'parent_id' => 'nullable|exists:comments,id'
        ]);

        $senderId = Session::get('user.id');

        // ----- 2. UBAH BAGIAN INI -----
        // Kita simpan komentarnya ke variabel $comment
        $comment = Comment::create([
            'user_id'   => $senderId,
            'post_id'   => $post->id,
            'parent_id' => $request->parent_id,
            'body'      => $request->body
        ]);

        // ----- 3. TAMBAHKAN LOGIKA NOTIFIKASI INI -----
        if ($request->parent_id) {
            // INI ADALAH BALASAN (REPLY)
            // Cari pemilik komentar yang dibalas
            $parentComment = Comment::find($request->parent_id);
            
            // Kirim notif ke pemilik komentar (jika bukan membalas diri sendiri)
            if ($parentComment && $senderId != $parentComment->user_id) {
                Notification::create([
                    'user_id'   => $parentComment->user_id, // Penerima
                    'sender_id' => $senderId,              // Pelaku
                    'type'      => 'comment_reply',
                    'post_id'   => $post->id,
                    'comment_id'=> $comment->id // Komentar balasan baru
                ]);
            }

        } else {
            // INI ADALAH KOMENTAR BARU
            // Kirim notif ke pemilik postingan (jika bukan komentar di post sendiri)
            if ($senderId != $post->user_id) {
                Notification::create([
                    'user_id'   => $post->user_id, // Penerima
                    'sender_id' => $senderId,      // Pelaku
                    'type'      => 'post_comment',
                    'post_id'   => $post->id,
                    'comment_id'=> $comment->id // Komentar baru
                ]);
            }
        }
        // ----- AKHIR BLOK NOTIFIKASI -----

        return back()->with('success', 'Komentar Anda diposting!');
    }


    // --- TAMBAHKAN 3 METHOD BARU DI BAWAH INI ---

    /**
     * Menampilkan form untuk mengedit komentar.
     */
    public function edit(Comment $comment)
    {
        if (!Session::has('user')) {
            return redirect('/login');
        }

        // PENTING: Cek Otorisasi
        if ($comment->user_id !== Session::get('user.id')) {
            abort(403, 'ANDA TIDAK PUNYA HAK AKSES');
        }

        // Kirim data komentar ke view 'comments.edit'
        return view('comments.edit', compact('comment'));
    }

    /**
     * Menyimpan perubahan (update) isi komentar.
     */
    public function update(Request $request, Comment $comment)
    {
        if (!Session::has('user')) {
            return redirect('/login');
        }

        // PENTING: Cek Otorisasi
        if ($comment->user_id !== Session::get('user.id')) {
            abort(403, 'ANDA TIDAK PUNYA HAK AKSES');
        }

        // 1. Validasi (hanya body)
        $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        // 2. Update body di database
        $comment->update([
            'body' => $request->body,
        ]);

        // 3. Kembali ke halaman postingan
        return redirect('/post/' . $comment->post_id)->with('success', 'Komentar berhasil diperbarui!');
    }

    /**
     * Menghapus komentar.
     */
    public function destroy(Comment $comment)
    {
        if (!Session::has('user')) {
            return redirect('/login');
        }

        // PENTING: Cek Otorisasi
        if ($comment->user_id !== Session::get('user.id')) {
            abort(403, 'ANDA TIDAK PUNYA HAK AKSES');
        }

        // Simpan post_id untuk redirect SEBELUM dihapus
        $postId = $comment->post_id;

        // Panggil delete.
        // Sisanya (hapus like & balasan) akan diurus
        // oleh 'deleting' event di Model Comment.php.
        $comment->delete();

        // 4. Kembali ke halaman postingan
        return redirect('/post/' . $postId)->with('success', 'Komentar telah dihapus.');
    }

} // <-- Ini bracket penutup Class