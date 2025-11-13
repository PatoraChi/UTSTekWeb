<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // <-- Tambahkan
use Illuminate\Support\Facades\Session; // <-- Tambahkan
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;

class AdminController extends Controller
{
    
    /**
     * Menampilkan halaman manajemen user.
     */
    public function listUsers()
    {
        $sessionUser = Session::get('user');
        if (!$sessionUser) {
            return redirect('/login');
        }

        $currentUserId = $sessionUser['id'];
        $currentUserRole = $sessionUser['role'];
        // Ambil user yang sedang login
        $authUser = User::find(Session::get('user.id'));
        
        // Ambil semua user, KECUALI 'owner' (Owner tidak bisa dikelola)
        // Kita urutkan berdasarkan ID
        $users = User::where('id', '!=', $currentUserId)
                     ->orderBy('id')
                     ->get();

        return view('admin.users', compact('authUser', 'users'));
    }

    /**
     * Menampilkan form untuk membuat user baru.
     */
    public function create()
    {
        $authUser = User::find(Session::get('user.id'));
        return view('admin.create_user', compact('authUser'));
    }

    /**
     * Menyimpan user baru ke database.
     */
    public function store(Request $request)
    {
        // 1. Ambil admin yang sedang login
        $authUser = User::find(Session::get('user.id'));

        // 2. Tentukan role yang diizinkan berdasarkan hierarki
        $allowedRoles = ['user'];
        if (in_array($authUser->role, ['super_admin', 'owner'])) {
            $allowedRoles[] = 'admin';
        }
        if ($authUser->role == 'owner') {
            $allowedRoles[] = 'super_admin';
        }

        // 3. Validasi
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:' . implode(',', $allowedRoles)
        ], [
            'role.in' => 'Anda tidak memiliki izin untuk membuat user dengan role tersebut.'
        ]);

        // 4. Buat user
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.users.list')->with('success', 'User baru berhasil dibuat.');
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
/**
     * Menghapus user dari database.
     */
    public function destroy(User $user) // $user adalah user yang akan dihapus
    {
        // 1. Ambil admin yang sedang login
        $authUser = User::find(Session::get('user.id'));

        // 2. Cek Keamanan & Hierarki
        $authRole = $authUser->role;
        $targetRole = $user->role;

        // Larangan: Menghapus diri sendiri
        if ($authUser->id == $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Larangan: Admin tidak bisa hapus admin lain atau lebih tinggi
        if ($authRole == 'admin' && in_array($targetRole, ['admin', 'super_admin', 'owner'])) {
            return back()->with('error', 'Anda tidak memiliki izin untuk menghapus user ini.');
        }

        // Larangan: Super Admin tidak bisa hapus super_admin lain atau owner
        if ($authRole == 'super_admin' && in_array($targetRole, ['super_admin', 'owner'])) {
            return back()->with('error', 'Anda tidak memiliki izin untuk menghapus user ini.');
        }
        
        // 3. Proses Hapus (SANGAT PENTING: Hapus konten dulu)
        // Kita harus me-looping untuk memicu Model Event 'deleting' pada Post
        // agar media di Cloudinary ikut terhapus.
        foreach ($user->posts as $post) {
            $post->delete();
        }
        
        // Hapus relasi lain (ini aman, tidak ada file)
        $user->comments()->delete();
        $user->likes()->delete();
        $user->saves()->delete();
        $user->followers()->detach();
        $user->following()->detach();
        // Hapus notifikasi yang DIKIRIM atau DITERIMA user
        Notification::where('user_id', $user->id)->orWhere('sender_id', $user->id)->delete();

        // 4. Hapus user
        $userName = $user->name;
        $user->delete();

        return back()->with('success', 'User "' . $userName . '" dan semua konten miliknya telah berhasil dihapus.');
    }
    public function edit(User $user) // $user adalah user yang akan diedit
    {
        // 1. Ambil admin yang sedang login
        $authUser = User::find(Session::get('user.id'));

        // 2. Cek Keamanan & Hierarki (Sama seperti 'destroy')
        $authRole = $authUser->role;
        $targetRole = $user->role;

        // Larangan: Mengedit diri sendiri (harus lewat halaman profil biasa)
        if ($authUser->id == $user->id) {
            return redirect()->route('admin.users.list')->with('error', 'Gunakan halaman "Edit Profil" biasa untuk mengubah data Anda.');
        }

        // Larangan: Admin tidak bisa edit admin lain atau lebih tinggi
        if ($authRole == 'admin' && in_array($targetRole, ['admin', 'super_admin', 'owner'])) {
            return redirect()->route('admin.users.list')->with('error', 'Anda tidak memiliki izin untuk mengedit user ini.');
        }

        // Larangan: Super Admin tidak bisa edit super_admin lain atau owner
        if ($authRole == 'super_admin' && in_array($targetRole, ['super_admin', 'owner'])) {
            return redirect()->route('admin.users.list')->with('error', 'Anda tidak memiliki izin untuk mengedit user ini.');
        }

        // 3. Tampilkan view
        return view('admin.edit_user', compact('authUser', 'user'));
    }

    /**
     * Memperbarui data user dari form admin.
     */
    public function update(Request $request, User $user) // $user adalah user yang akan diupdate
    {
        // 1. Ambil admin yang sedang login
        $authUser = User::find(Session::get('user.id'));

        // 2. Cek Keamanan & Hierarki (Sama seperti 'edit' dan 'destroy')
        $authRole = $authUser->role;
        $targetRole = $user->role;

        if ($authUser->id == $user->id) {
            return back()->with('error', 'Gunakan halaman "Edit Profil" biasa untuk mengubah data Anda.')->withInput();
        }
        if ($authRole == 'admin' && in_array($targetRole, ['admin', 'super_admin', 'owner'])) {
            return back()->with('error', 'Anda tidak memiliki izin untuk mengedit user ini.')->withInput();
        }
        if ($authRole == 'super_admin' && in_array($targetRole, ['super_admin', 'owner'])) {
            return back()->with('error', 'Anda tidak memiliki izin untuk mengedit user ini.')->withInput();
        }

        // 3. Validasi
        $request->validate([
            'name' => 'required|string|max:255',
            // Gunakan Rule::unique untuk mengabaikan email user ini sendiri saat pengecekan
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            // Foto profil opsional
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            // Password opsional, tapi jika diisi, harus dikonfirmasi
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // 4. Proses Update Data
        $user->name = $request->name;
        $user->email = $request->email;

        // 5. Proses Update Password (HANYA JIKA DIISI)
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('profile_image')) {
                    
            // --- LOGIKA HAPUS CLOUDINARY LAMA ---
            // Pastikan kolom profile_image tidak null dan file ada di Cloudinary
            if ($user->profile_image && Storage::disk('cloudinary')->exists($user->profile_image)) {
                // Hapus gambar lama dari Cloudinary
                Storage::disk('cloudinary')->delete($user->profile_image);
            }
            // --- AKHIR LOGIKA HAPUS CLOUDINARY LAMA ---


            // Simpan gambar baru ke Cloudinary menggunakan Storage Adapter
            // PENTING: Gunakan 'profiles' atau 'profile_pictures' sebagai folder
            $folderName = 'profile_pictures'; // Sesuaikan jika Anda menggunakan nama folder lain
            $path = $request->file('profile_image')->store($folderName, 'cloudinary');
            
            // Simpan Public ID baru ke database
            // $path sudah berisi Public ID (e.g., 'profile_pictures/abcde12345')
            $user->profile_image = $path; 
        }

        // 7. Simpan perubahan
        $user->save();

        return redirect()->route('admin.users.list')->with('success', 'Data untuk ' . $user->name . ' berhasil diperbarui.');
    }
/**
     * Helper untuk mendapatkan data user yang sudah disensor, digunakan oleh export.
     * @param string $currentUserRole Role dari user yang sedang login
     * @return array
     */
    protected function getExportData(string $currentUserRole): array
    {
        $users = User::all();
        $records = [];
        $currentUserLevel = $this->roleLevel[$currentUserRole] ?? 0;
        
        foreach ($users as $user) {
            $viewedUserLevel = $this->roleLevel[$user->role] ?? 0;
            // $isSensitive berlaku jika user yang dilihat memiliki level > user yang login
            $isSensitive = ($viewedUserLevel > $currentUserLevel); 
            
            $email = $user->email;
            $password = $user->password;
            
            if ($isSensitive) {
                $email = '*** DISENSORED ***';
                $password = '*** DISENSORED ***';
            } 

            $records[] = [
                'ID' => $user->id,
                'Name' => $user->name,
                'Email' => $email,
                'Password (Hashed)' => $password,
                'Bio' => $user->bio ?? 'N/A',
                'Profile Image Public ID' => $user->profile_image ?? 'N/A',
                'Role' => $user->role,
                'Is Banned' => $user->is_banned ? 'Yes' : 'No',
                'Banned Until' => $user->banned_until ? $user->banned_until->toDateTimeString() : 'N/A',
                'Created At' => $user->created_at->toDateTimeString(),
            ];
        }

        return $records;
    }

    /**
     * Export daftar user ke Excel, CSV, atau PDF.
     * Menggunakan Spatie/SimpleExcel untuk Excel/CSV dan DomPDF untuk PDF.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\RedirectResponse
     */

}