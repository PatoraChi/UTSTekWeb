<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon; // Kita butuh ini untuk menghitung tanggal

class ModerationController extends Controller
{
    /**
     * Terapkan ban ke seorang user.
     */
    public function banUser(Request $request, User $profile)
    {
        // 1. Validasi input
        $request->validate([
            'duration' => 'required|string|in:1_day,7_day,30_day,permanent',
            'reason' => 'nullable|string|max:500', // (Opsional, bisa ditambah nanti)
        ]);

        // 2. Ambil data admin yang sedang login
        $authUser = User::find(Session::get('user.id'));

        // 3. Cek Keamanan & Hierarki
        if ($authUser->id == $profile->id) {
            return back()->with('error', 'Anda tidak dapat mem-ban diri sendiri.');
        }

        $authRole = $authUser->role;
        $targetRole = $profile->role;

        // Admin tidak bisa ban admin lain, super_admin, or owner
        if ($authRole == 'admin' && ($targetRole == 'admin' || $targetRole == 'super_admin' || $targetRole == 'owner')) {
            return back()->with('error', 'Anda tidak memiliki izin untuk mem-ban user ini.');
        }
        
        // Super_admin tidak bisa ban super_admin lain or owner
        if ($authRole == 'super_admin' && ($targetRole == 'super_admin' || $targetRole == 'owner')) {
            return back()->with('error', 'Anda tidak memiliki izin untuk mem-ban user ini.');
        }

        // 4. Terapkan Logika Ban
        $duration = $request->input('duration');
        
        if ($duration == 'permanent') {
            $profile->is_banned = true;
            $profile->banned_until = null;
        } else {
            // Ubah '1_day' menjadi 1, '7_day' menjadi 7, dst.
            $days = (int) $duration; 
            
            $profile->is_banned = true;
            $profile->banned_until = Carbon::now()->addDays($days);
        }

        $profile->save();

        return back()->with('success', $profile->name . ' telah berhasil di-ban.');
    }
}