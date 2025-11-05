<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    // Tampilkan form lupa password
    public function showForm()
    {
        return view('auth.forgot');
    }

    // Kirim kode OTP ke email
    public function sendOTP(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $email = $request->email;

        // Cek apakah email terdaftar
        $user = DB::table('users')->where('email', $email)->first();
        if (!$user) {
            return back()->with('error', 'Email tidak ditemukan!');
        }

        // Buat kode OTP 6 digit
        $otp = rand(100000, 999999);

        // Simpan di tabel password_resets
        DB::table('password_resets')->updateOrInsert(
            ['email' => $email],
            [
                'token' => $otp,
                'created_at' => Carbon::now(),
            ]
        );

        // Kirim email berisi OTP
        Mail::raw("Kode verifikasi kamu adalah: {$otp}", function ($message) use ($email) {
            $message->to($email)->subject('Kode Reset Password - LahIya');
        });

        // Simpan ke session
        session(['reset_email' => $email]);

        // Debug (buat testing)
        session(['debug_code' => $otp]);

        return redirect('/verify-token')->with('success', 'Kode OTP telah dikirim ke email kamu.');
    }
}
