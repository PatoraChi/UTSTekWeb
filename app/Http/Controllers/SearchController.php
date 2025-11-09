<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;     // <-- Tambahkan
use App\Models\Post;     // <-- Tambahkan
use App\Models\Tag;      // <-- Tambahkan
use Illuminate\Support\Str;  // <-- Tambahkan
use Illuminate\Support\Facades\Session; // <-- Tambahkan

class SearchController extends Controller
{
    public function index(Request $request)
    {
        // 1. Cek login (Wajib untuk layout)
        if (!Session::has('user')) {
            return redirect('/login');
        }
        $user = User::find(Session::get('user.id'));
        if (!$user) {
            return redirect('/login');
        }

        // 2. Ambil query pencarian dari URL (cth: /cari?q=...)
        $query = $request->input('q', '');

        // 3. Siapkan variabel hasil
        $usersResult = collect(); // Hasil pencarian User
        $postsResult = collect(); // Hasil pencarian Post
        $searchType = ''; // 'user' atau 'tag'

        // 4. Logika Pencarian
        if (!empty($query)) {
            if (Str::startsWith($query, '#')) {
                // --- PENCARIAN TAG ---
                $searchType = 'tag';
                $tagName = substr($query, 1); // Hilangkan '#'

                $postsResult = Post::whereHas('tags', function ($q) use ($tagName) {
                    $q->where('name', $tagName);
                })
                ->with(['user', 'media', 'likes', 'saves']) // Eager load relasi
                ->withCount('allComments')
                ->latest()
                ->paginate(15); // Kita pakai paginate di sini

            } else {
                // --- PENCARIAN USER ---
                $searchType = 'user';
                $usersResult = User::where('name', 'LIKE', '%' . $query . '%')
                    ->take(20)
                    ->get();
            }
        }

        // 5. Kirim semua data ke view 'cari'
        return view('cari', compact('user', 'query', 'searchType', 'usersResult', 'postsResult'));
    }
}