<?php

namespace App\Http\Controllers;

use App\Models\Comment; // <-- Ganti
use App\Models\CommentLike; // <-- Ganti
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

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
        } else {
            CommentLike::create([ // <-- Ganti
                'user_id' => $userId,
                'comment_id' => $comment->id // <-- Ganti
            ]);
            $isLiked = true;
        }

        $newCount = $comment->likes()->count();

        return response()->json([
            'isLiked'  => $isLiked,
            'newCount' => $newCount
        ]);
    }
}