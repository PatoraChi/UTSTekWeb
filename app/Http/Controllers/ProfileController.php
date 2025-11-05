<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfileController extends Controller
{
    public function show()
    {
        $sessionUser = Session::get('user');

        // ⛔ Cek apakah user ada di session
        if (!$sessionUser) {
            return redirect('/login');
        }

        // ✅ Ambil data lengkap dari database
        $user = User::find($sessionUser['id']);

        return view('profile.show', compact('user'));
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
