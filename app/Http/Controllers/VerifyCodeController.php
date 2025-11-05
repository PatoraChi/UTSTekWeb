<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VerifyCodeController extends Controller
{
    // Tampilkan halaman verifikasi OTP
    public function showForm()
    {
        $debug_code = session('debug_code'); // untuk debug tampilin kode di layar
        return view('auth.verify-token', compact('debug_code'));
    }

    // Proses verifikasi kode
    public function verify(Request $request)
    {
        $request->validate(['token' => 'required|numeric']);

        $email = session('reset_email');
        $token = $request->token;

        if (!$email) {
            return redirect('/forgot-password')->with('error', 'Sesi telah habis, silakan ulangi.');
        }

        // Ambil data OTP dari database
        $record = DB::table('password_resets')->where('email', $email)->first();

        if (!$record) {
            return back()->with('error', 'Kode tidak ditemukan, silakan kirim ulang.');
        }

        // Periksa apakah kode cocok dan belum lebih dari 10 menit
        $valid = $record->token == $token && Carbon::parse($record->created_at)->addMinutes(10)->isFuture();

        if (!$valid) {
            return back()->with('error', 'Kode verifikasi salah atau sudah kedaluwarsa.');
        }

        // Jika benar → arahkan ke halaman reset password
        session(['otp_verified' => true]);
        return redirect('/reset-password');
    }
}
