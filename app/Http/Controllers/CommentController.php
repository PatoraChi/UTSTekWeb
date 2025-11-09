<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CommentController extends Controller
{
    /**
     * Menyimpan komentar baru (atau balasan).
     */
    public function store(Request $request, Post $post)
    {
        // ... (Isi method store() kamu biarkan saja) ...
        if (!Session::has('user')) {
            return redirect('/login');
        }

        $request->validate([
            'body' => 'required|string|max:2000',
            'parent_id' => 'nullable|exists:comments,id'
        ]);

        Comment::create([
            'user_id' => Session::get('user.id'),
            'post_id' => $post->id,
            'parent_id' => $request->parent_id,
            'body' => $request->body
        ]);

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