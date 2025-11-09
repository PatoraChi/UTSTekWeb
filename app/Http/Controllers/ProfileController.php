<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfileController extends Controller
{
    public function show(User $user = null)
    {
        // 1. Dapatkan user yang sedang login
        $sessionUser = Session::get('user');
        if (!$sessionUser) {
            return redirect('/login');
        }
        // $authUser = User yang sedang login
        $authUser = User::find($sessionUser['id']);

        // 2. Tentukan profil siapa yang mau ditampilkan
        if ($user === null) {
            // Jika URL-nya /profile (kosong), tampilkan profil sendiri
            $profileUser = $authUser;
        } else {
            // Jika URL-nya /profile/5, tampilkan profil user 5
            $profileUser = $user;
        }

        // 3. Ambil data untuk profil $profileUser
        
        // Ambil postingan milik $profileUser
        $posts = $profileUser->posts()->with('media', 'likes', 'saves')->withCount('allComments')->latest()->get();
        
        // Hitung jumlah
        $postCount = $posts->count();
        $followerCount = $profileUser->followers()->count();
        $followingCount = $profileUser->following()->count();
        
        // Cek status follow (apakah $authUser mengikuti $profileUser)
        $isFollowing = $authUser->isFollowing($profileUser);

        // 4. Kirim semua data ke view
        return view('profile.show', compact(
            'authUser',     // User yang login
            'profileUser',  // User yang profilnya dilihat
            'posts',
            'postCount',
            'followerCount',
            'followingCount',
            'isFollowing'
        ));
    }
    public function edit()
    {
        $sessionUser = Session::get('user');

        if (!$sessionUser) {
            return redirect('/login');
        }

        $user = User::find($sessionUser['id']);

        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $sessionUser = Session::get('user');

        if (!$sessionUser) {
            return redirect('/login');
        }

        $user = User::find($sessionUser['id']); // ✅ Pastikan ini model User

        $request->validate([
            'name' => 'required|string|max:100',
            'bio' => 'nullable|string|max:500',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // ✅ Upload foto profil
        if ($request->hasFile('profile_image')) {
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }

            $path = $request->file('profile_image')->store('profiles', 'public');
            $user->profile_image = $path;
        }

        // ✅ Update nama & bio
        $user->name = $request->name;
        $user->bio = $request->bio;
        $user->save();

        // ✅ Perbarui session juga agar nama di navbar ikut berubah
        Session::put('user', [
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->role,
        ]);

        return redirect('/')->with('success', 'Profil berhasil diperbarui!');
    }
}
