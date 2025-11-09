<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfileController extends Controller
{
    public function show(Request $request, User $profile = null)
    {
        // 1. Dapatkan user yang sedang login
        $sessionUser = Session::get('user');
        if (!$sessionUser) {
            return redirect('/login');
        }
        $authUser = User::find($sessionUser['id']);

        // 2. Tentukan profil siapa yang mau ditampilkan
        if ($profile === null) {
            $profileUser = $authUser; // Profil sendiri
        } else {
            $profileUser = $profile; // Profil orang lain
        }

        // 3. Ambil data tab dari URL (default: 'posts')
        $tab = $request->input('tab', 'posts');
        
        // 4. Siapkan variabel data
        $data = collect();

        // 5. Ambil data berdasarkan tab yang aktif
        switch ($tab) {
            case 'posts':
                $data = $profileUser->posts()
                            ->with('media', 'likes', 'saves')
                            ->withCount('allComments')
                            ->latest()
                            ->get();
                break;
            
            case 'liked':
                $data = $profileUser->likedPosts()
                            ->with('media', 'likes', 'saves')
                            ->withCount('allComments')
                            ->get();
                break;
                
            case 'comments':
                $data = $profileUser->comments()
                            ->with(['post.media', 'post.user'])
                            ->get();
                break;

            case 'saved':
                // PRIVASI: Hanya tampilkan jika user melihat profilnya sendiri
                if ($authUser->id == $profileUser->id) {
                    $data = $profileUser->savedPosts()
                                ->with('media', 'likes', 'saves')
                                ->withCount('allComments')
                                ->get();
                } else {
                    // Jika orang lain mencoba mengintip tab 'saved',
                    // kembalikan saja ke tab 'posts'
                    $tab = 'posts';
                    $data = $profileUser->posts()
                                ->with('media')->latest()->get();
                }
                break;

            default:
                $tab = 'posts';
                $data = $profileUser->posts()
                            ->with('media')->latest()->get();
        }

        // 6. Hitung jumlah (ini untuk header, tidak berubah)
        $postCount = $profileUser->posts()->count();
        $followerCount = $profileUser->followers()->count();
        $followingCount = $profileUser->following()->count();
        
        // 7. Cek status follow
        $isFollowing = $authUser->isFollowing($profileUser);

        // 8. Kirim semua data ke view
        return view('profile.show', compact(
            'authUser',
            'profileUser',
            'data',           // Ganti '$posts' menjadi '$data'
            'tab',            // Kirim nama tab yang aktif
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
            if ($user->profile_image && Storage::disk('cloudinary')->exists($user->profile_image)) {
                Storage::disk('cloudinary')->delete($user->profile_image);
            }

            $path = $request->file('profile_image')->store('profiles', 'cloudinary');
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
