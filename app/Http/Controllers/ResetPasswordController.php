<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    // Tampilkan halaman reset password
    public function showResetForm()
    {
        return view('auth.reset');
    }

    // Proses ubah password
    public function reset(Request $request)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        // Ambil email dari session (diset saat OTP)
        $email = session('reset_email');

        if (!$email) {
            return redirect('/forgot-password')->with('error', 'Sesi reset password berakhir. Silakan mulai lagi.');
        }

        // Cari user berdasarkan email
        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect('/forgot-password')->with('error', 'Email tidak ditemukan.');
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        // Hapus session OTP/email
        session()->forget(['reset_email', 'reset_code']);

        return redirect('/login')->with('success', 'Password berhasil diubah! Silakan login kembali.');
    }
}
