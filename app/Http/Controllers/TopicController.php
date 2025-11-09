<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;  // <-- Tambahkan
use App\Models\User; // <-- Tambahkan
use Illuminate\Support\Facades\Session; // <-- Tambahkan
use Illuminate\Support\Facades\DB; // <-- Tambahkan

class TopicController extends Controller
{
    public function index()
    {
        // 1. Cek login (Wajib untuk layout)
        if (!Session::has('user')) {
            return redirect('/login');
        }
        $user = User::find(Session::get('user.id'));
        if (!$user) {
            return redirect('/login');
        }

        // 2. Query untuk mengambil Tag populer 1 bulan terakhir
        $popularTags = Tag::select('tags.id', 'tags.name', DB::raw('count(post_tag.post_id) as posts_count'))
            ->join('post_tag', 'tags.id', '=', 'post_tag.tag_id')
            ->join('posts', 'post_tag.post_id', '=', 'posts.id')
            ->where('posts.created_at', '>=', now()->subMonth()) // Filter 1 bulan terakhir
            ->groupBy('tags.id', 'tags.name')
            ->orderBy('posts_count', 'desc') // Urutkan dari paling populer
            ->take(20) // Ambil 20 tag teratas
            ->get();

        // 3. Kirim data ke view 'topik'
        return view('topik', compact('user', 'popularTags'));
    }
}