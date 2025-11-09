<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\PostMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session; // Penting untuk session custom Anda
use Illuminate\Support\Facades\Storage; // Penting untuk file
use Illuminate\Support\Str; // Penting untuk cek tipe file
use App\Models\Tag;
class PostController extends Controller
{
    /**
     * Menampilkan halaman form buat postingan.
     */
    public function create()
    {
        // Cek apakah user sudah login (mengikuti pola Anda)
        if (!Session::has('user')) {
            return redirect('/login');
        }

        return view('posts.create');
    }

    /**
     * Menyimpan postingan baru ke database.
     */
    public function store(Request $request)
    {
        // Cek apakah user sudah login
        if (!Session::has('user')) {
            return redirect('/login');
        }

        // 1. Validasi input
        $request->validate([
            'caption' => 'nullable|string|max:2000',
            // 'media_files' harus ada minimal 1 file, maks 10 file
            'media_files' => 'required|array|min:1|max:10', 
            // Cek setiap file di dalam array
            'media_files.*' => 'file|mimes:jpg,jpeg,png,gif,mp4,mov,avi,mkv|max:20480', // Maks 20MB per file
        ], [
            'media_files.required' => 'Anda harus mengupload setidaknya 1 gambar/video.',
            'media_files.*.mimes' => 'Format file tidak didukung (hanya: jpg, png, mp4, mov).',
            'media_files.*.max' => 'Ukuran file maksimal 20MB.',
        ]);

        // 2. Simpan Postingan Utama (caption & user_id)
        $post = Post::create([
            'user_id' => Session::get('user.id'), // Ambil ID user dari Session
            'caption' => $request->caption,
        ]);

        // 3. Simpan File Media (Looping)
        if ($request->hasFile('media_files')) {
            foreach ($request->file('media_files') as $file) {
                
                // Tentukan tipe file
                $fileType = Str::startsWith($file->getMimeType(), 'video') ? 'video' : 'image';
                
                // Simpan file ke 'storage/app/public/posts'
                // Nama file akan di-hash (random) agar unik
                $path = $file->store('posts', 'public');

                // Simpan info file ke tabel post_media
                PostMedia::create([
                    'post_id'   => $post->id,
                    'file_path' => $path,
                    'file_type' => $fileType,
                ]);
            }
        }
        
        // (Langkah 4 untuk Hashtag akan ada di bawah)
        // 4. Proses dan Simpan Hashtag
        // Regex untuk menemukan #hashtag (hanya huruf dan angka)
        preg_match_all('/#([a-zA-Z0-9_]+)/', $request->caption, $matches);

        $tagIds = [];
        if (!empty($matches[1])) {
            // $matches[1] berisi array nama tag (tanpa #)
            $tagNames = $matches[1];

            foreach ($tagNames as $tagName) {
                // Cari tag, atau buat baru jika tidak ada
                $tag = Tag::firstOrCreate(['name' => strtolower($tagName)]);
                $tagIds[] = $tag->id;
            }
        }

        // Hubungkan postingan ini dengan semua tag yang ditemukan
        if (!empty($tagIds)) {
            $post->tags()->sync($tagIds);
        }
        // 5. Kembali ke Halaman Utama
        return redirect('/')->with('success', 'Postingan berhasil dibuat!');
    }
    public function show(Post $post)
        {
            if (!Session::has('user')) {
                return redirect('/login');
            }

            // Ambil data user yang sedang login
            $user = User::find(Session::get('user.id'));

            // Load relasi post (seperti di Home)
            $post->load(['user', 'media', 'likes', 'saves']);
            
            // Load komentar (hanya top-level) dan relasi nested-nya
            $comments = $post->comments()
                            ->with('user', 'likes', 'parent.user', 'replies')
                            ->latest() // Tampilkan komentar terbaru di atas
                            ->get();

            return view('posts.show', compact('user', 'post', 'comments'));
        }
        /**
         * Menampilkan form untuk mengedit postingan.
         */
        public function edit(Post $post)
        {
            if (!Session::has('user')) {
                return redirect('/login');
            }

            // PENTING: Cek Otorisasi
            // Apakah user yang login adalah pemilik post ini?
            if ($post->user_id !== Session::get('user.id')) {
                abort(403, 'ANDA TIDAK PUNYA HAK AKSES');
            }

            // Kirim data post ke view 'posts.edit'
            return view('posts.edit', compact('post'));
        }
        /**
         * Menyimpan perubahan (update) caption postingan.
         */
        public function update(Request $request, Post $post)
        {
            if (!Session::has('user')) {
                return redirect('/login');
            }

            // PENTING: Cek Otorisasi
            if ($post->user_id !== Session::get('user.id')) {
                abort(403, 'ANDA TIDAK PUNYA HAK AKSES');
            }

            // 1. Validasi (hanya caption)
            $request->validate([
                'caption' => 'nullable|string|max:2000',
            ]);

            // 2. Update caption di database
            $post->update([
                'caption' => $request->caption,
            ]);

            // 3. Proses dan Update Hashtag (Sama seperti di method store)
            preg_match_all('/#([a-zA-Z0-9_]+)/', $request->caption, $matches);
            
            $tagIds = [];
            if (!empty($matches[1])) {
                $tagNames = $matches[1];
                foreach ($tagNames as $tagName) {
                    $tag = Tag::firstOrCreate(['name' => strtolower($tagName)]);
                    $tagIds[] = $tag->id;
                }
            }

            // 'sync' akan otomatis menambah/menghapus tag yang berubah
            $post->tags()->sync($tagIds);

            // 4. Kembali ke halaman home
            return redirect('/')->with('success', 'Caption berhasil diperbarui!');
        }

        /**
         * Menghapus postingan.
         */
        public function destroy(Post $post)
        {
            if (!Session::has('user')) {
                return redirect('/login');
            }

            // PENTING: Cek Otorisasi
            if ($post->user_id !== Session::get('user.id')) {
                abort(403, 'ANDA TIDAK PUNYA HAK AKSES');
            }

            // Panggil delete.
            // Sisanya (hapus file, like, comment, dll) akan diurus
            // oleh 'deleting' event di Model Post.php.
            $post->delete();

            // 4. Kembali ke halaman home
            return redirect('/')->with('success', 'Postingan telah dihapus.');
        }
}