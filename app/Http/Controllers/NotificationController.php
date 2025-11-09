<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification; // <-- Tambahkan
use App\Models\User;         // <-- Tambahkan
use Illuminate\Support\Facades\Session; // <-- Tambahkan

class NotificationController extends Controller
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

        // 2. Tandai semua notifikasi yang belum dibaca sebagai "dibaca"
        // Kita lakukan ini saat user membuka halaman notifikasi
        Notification::where('user_id', $user->id)
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);

        // 3. Ambil semua notifikasi untuk user ini, urutkan dari terbaru
        // Kita pakai "with" (Eager Loading) agar efisien
        $notifications = Notification::where('user_id', $user->id)
                                    ->with('sender', 'post') // Ambil data pengirim & post
                                    ->latest() // Urutkan dari terbaru
                                    ->paginate(20); // Bagi per 20 notif per halaman

        // 4. Kirim data ke view
        return view('notifications.index', compact('user', 'notifications'));
    }
}