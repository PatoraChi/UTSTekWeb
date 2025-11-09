<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // <-- Tambahkan
use Illuminate\Support\Facades\Session; // <-- Tambahkan

class AdminController extends Controller
{
    /**
     * Menampilkan halaman manajemen user.
     */
    public function listUsers()
    {
        // Ambil user yang sedang login
        $authUser = User::find(Session::get('user.id'));
        
        // Ambil semua user, KECUALI 'owner' (Owner tidak bisa dikelola)
        // Kita urutkan berdasarkan ID
        $users = User::where('role', '!=', 'owner')
                     ->orderBy('id')
                     ->get();

        return view('admin.users', compact('authUser', 'users'));
    }

    /**
     * Memperbarui role seorang user.
     */
    public function updateRole(Request $request, User $user) // $user adalah user yang akan diubah
    {
        // 1. Ambil admin yang sedang login
        $authUser = User::find(Session::get('user.id'));

        // 2. Validasi role yang baru
        $newRole = $request->input('role');
        $allowedRoles = ['user', 'admin']; // Role dasar
        
        // Jika authUser adalah owner, dia boleh mengangkat jadi super_admin
        if ($authUser->role == 'owner') {
            $allowedRoles[] = 'super_admin';
        }
        
        $request->validate([
            'role' => 'required|in:' . implode(',', $allowedRoles)
        ]);

        // 3. Cek Keamanan & Hierarki (SANGAT PENTING)
        $authRole = $authUser->role;
        $targetRole = $user->role; // Role user SEBELUM diubah

        // Larangan: Mengubah role 'owner'
        if ($targetRole == 'owner') {
            return back()->with('error', 'Role Owner tidak dapat diubah.');
        }

        // Larangan: Super Admin HANYA boleh ubah user & admin
        if ($authRole == 'super_admin' && !in_array($targetRole, ['user', 'admin'])) {
            return back()->with('error', 'Anda tidak memiliki izin untuk mengubah role user ini.');
        }
        
        // Larangan: Super Admin tidak bisa mengangkat jadi Super Admin
        if ($authRole == 'super_admin' && $newRole == 'super_admin') {
            return back()->with('error', 'Anda tidak bisa mengangkat user lain menjadi Super Admin.');
        }

        // 4. Jika semua cek lolos, update role
        $user->role = $newRole;
        $user->save();

        return back()->with('success', 'Role ' . $user->name . ' berhasil diubah menjadi ' . $newRole);
    }
/**
     * Memperbarui status ban seorang user dari Panel Admin.
     */
    public function manageBan(Request $request, User $user) // $user adalah user yang akan diubah
    {
        // 1. Ambil admin yang sedang login
        $authUser = User::find(Session::get('user.id'));

        // 2. Validasi input
        $duration = $request->input('duration');
        $request->validate([
            'duration' => 'required|string|in:unban,1_day,7_day,30_day,permanent',
        ]);

        // 3. Cek Keamanan & Hierarki
        $authRole = $authUser->role;
        $targetRole = $user->role;

        // Larangan: Mengubah diri sendiri
        if ($authUser->id == $user->id) {
            return back()->with('error', 'Anda tidak dapat mengubah status ban diri sendiri.');
        }

        // Larangan: Admin tidak bisa ban admin lain atau lebih tinggi
        if ($authRole == 'admin' && in_array($targetRole, ['admin', 'super_admin', 'owner'])) {
            return back()->with('error', 'Anda tidak memiliki izin untuk mem-ban user ini.');
        }

        // Larangan: Super Admin tidak bisa ban super_admin lain atau owner
        if ($authRole == 'super_admin' && in_array($targetRole, ['super_admin', 'owner'])) {
            return back()->with('error', 'Anda tidak memiliki izin untuk mem-ban user ini.');
        }
        
        // 4. Terapkan Logika Ban / Unban
        if ($duration == 'unban') {
            $user->is_banned = false;
            $user->banned_until = null;
            $message = $user->name . ' telah di-unban.';
        
        } elseif ($duration == 'permanent') {
            $user->is_banned = true;
            $user->banned_until = null;
            $message = $user->name . ' telah di-ban permanen.';
        
        } else {
            // Ubah '1_day' menjadi 1, '7_day' menjadi 7, dst.
            $days = (int) $duration; 
            
            $user->is_banned = true;
            $user->banned_until = Carbon::now()->addDays($days);
            $message = $user->name . ' telah di-ban selama ' . $days . ' hari.';
        }

        $user->save();

        return back()->with('success', $message);
    }
}