<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\VerifyCodeController;
use App\Http\Controllers\ResetPasswordController;

// 🏠 Halaman utama (dashboard setelah login)
Route::get('/', function () {
    if (!Session::has('user')) {
        return redirect('/login');
    }
    return view('home'); // ini file home.blade.php kamu
});

// 🔐 Halaman login (GET)
Route::get('/login', function () {
    // Kalau sudah login, langsung ke home
    if (Session::has('user')) {
        return redirect('/');
    }
    return view('auth.login');
})->name('login');

// 🔐 Proses login (POST)
Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return back()->with('error', 'Email tidak ditemukan.');
    }

    if (!Hash::check($request->password, $user->password)) {
        return back()->with('error', 'Password salah.');
    }

    if ($user->is_banned ?? false) {
        return back()->with('error', 'Akun Anda diblokir.');
    }

    // Simpan session user
    Session::put('user', [
        'id' => $user->id,
        'name' => $user->name,
        'role' => $user->role,
    ]);

    return redirect('/'); // langsung ke home
});

// 🧾 Logout
Route::get('/logout', function () {
    Session::forget('user');
    return redirect('/login');
});

// 📝 Register (GET)
Route::get('/register', function () {
    return view('auth.register');
});

// 📝 Register (POST)
Route::post('/register', function (Illuminate\Http\Request $request) {
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6|confirmed',
    ]);

    \App\Models\User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        'role' => 'user', // default user
    ]);

    return redirect('/login')->with('success', 'Akun berhasil dibuat! Silakan login.');
});



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
    logger("Kode verifikasi untuk {$email}: {$code}");
    // (Untuk debug, tampilkan kode di console)
    Mail::to($email)->send(new OtpMail($code));

    // Arahkan ke halaman verifikasi
    return redirect('/verify-token');
});

// --------------------
//  VERIFIKASI KODE
// --------------------

// Tampilkan form verifikasi
Route::get('/verify-token', function () {
    $code = session('reset_code');
    $debug_code = config('app.debug') ? $code : null;

    // Tambahkan log untuk memastikan session tersimpan
    logger("DEBUG: Session reset_code sekarang = " . $code);

    return view('auth.verify-token', ['debug_code' => $debug_code]);
});


// Saat user submit kode (POST)
Route::post('/verify-token', function () {
    $token = request('token');
    $correct = session('reset_code'); // <-- HARUS sama key-nya

    logger("User input token = {$token}, Session code = {$correct}");

    if ($token == $correct) {
        return redirect('/reset-password');
    } else {
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
// Halaman reset password
Route::get('/reset-password', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');

// Proses ubah password
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');



Route::get('/forgot-password', [ForgotPasswordController::class, 'showForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendOTP'])->name('password.send');


// Verifikasi OTP
Route::get('/verify-token', [VerifyCodeController::class, 'showForm'])->name('verify.form');
Route::post('/verify-token', [VerifyCodeController::class, 'verify'])->name('verify.check');
