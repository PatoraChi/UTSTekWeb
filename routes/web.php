<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Semua route tampilan dan logika sederhana untuk aplikasi LahIya.
| (versi simulasi tanpa database)
|
*/

// Default halaman (langsung arahkan ke login)
Route::get('/', function () {
    return redirect('/login');
});

// --------------------
//  AUTH PAGES
// --------------------

// Halaman login
Route::get('/login', fn() => view('auth.login'));

// Halaman register
Route::get('/register', fn() => view('auth.register'));

// --------------------
//  LUPA PASSWORD FLOW
// --------------------

// Halaman lupa password (GET)
Route::get('/forgot-password', fn() => view('auth.forgot'));

// Saat user kirim email (POST)
Route::post('/forgot-password', function () {
    $email = request('email');

    // Simulasi: buat kode verifikasi acak
    $code = rand(100000, 999999);

    // Simpan email dan kode ke session sementara
    session(['reset_email' => $email, 'reset_code' => $code]);

    // (Untuk debug, tampilkan kode di console)
    logger("Kode verifikasi untuk {$email}: {$code}");

    // Arahkan ke halaman verifikasi
    return redirect('/verify-token');
});

// --------------------
//  VERIFIKASI KODE
// --------------------

// Tampilkan form verifikasi
Route::get('/verify-token', fn() => view('auth.verify-token'));

// Saat user submit kode (POST)
Route::post('/verify-token', function () {
    $token = request('token');
    $correct = session('reset_code');

    if ($token == $correct) {
        // Jika benar → arahkan ke reset password
        return redirect('/reset-password');
    } else {
        // Jika salah → kembali dengan pesan error
        return back()->with('error', 'Kode verifikasi salah! Silakan coba lagi.');
    }
});

// --------------------
//  RESET PASSWORD
// --------------------

// Tampilkan form reset password
Route::get('/reset-password', fn() => view('auth.reset'));

// Proses ubah password
Route::post('/reset-password', function () {
    // Simulasi ubah password
    session()->forget(['reset_email', 'reset_code']);
    return view('auth.reset-success');
});

