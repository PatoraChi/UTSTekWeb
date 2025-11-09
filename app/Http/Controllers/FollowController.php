<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Follow;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class FollowController extends Controller
{
    public function toggleFollow(Request $request, User $user)
    {
        if (!Session::has('user')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $follower = User::find(Session::get('user.id'));
        
        // Mencegah follow diri sendiri
        if ($follower->id == $user->id) {
            return response()->json(['error' => 'Tidak dapat mengikuti diri sendiri'], 422);
        }

        $isFollowing = $follower->isFollowing($user);
        
        if ($isFollowing) {
            // --- UNFOLLOW ---
            $follower->following()->detach($user->id);
            $isFollowing = false;
            
            // Hapus notifikasi follow (jika ada)
            Notification::where('sender_id', $follower->id)
                        ->where('user_id', $user->id)
                        ->where('type', 'follow')
                        ->delete();
        } else {
            // --- FOLLOW ---
            $follower->following()->attach($user->id);
            $isFollowing = true;

            // Buat notifikasi (kecuali jika user target sudah follow kita)
            // Ini opsional, tapi bagus untuk mencegah spam notif "follow-back"
            Notification::create([
                'user_id'   => $user->id,
                'sender_id' => $follower->id,
                'type'      => 'follow',
                'post_id'   => null, // Tidak terkait postingan
            ]);
        }
        
        // Ambil jumlah followers BARU dari user yang di-follow
        $newCount = $user->followers()->count();

        return response()->json([
            'isFollowing' => $isFollowing,
            'newCount' => $newCount
        ]);
    }
}