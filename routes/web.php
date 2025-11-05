<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\OtpMail;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\VerifyCodeController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Halaman Utama
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (!Session::has('user')) {
        return redirect('/login');
    }

    // Ambil data user LENGKAP dari database
    $user = User::find(Session::get('user.id'));

    // Jika user tidak ditemukan (mungkin session 'basi'), logout paksa
    if (!$user) {
        Session::forget('user');
        return redirect('/login');
    }

    // Kirim data user ke view 'home'
    return view('home', compact('user'));
});


/*
|--------------------------------------------------------------------------
| Login & Logout
|--------------------------------------------------------------------------
*/

// Halaman login
Route::get('/login', function () {
    if (Session::has('user')) {
        return redirect('/');
    }
    return view('auth.login');
})->name('login');

// Proses login
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

    // Simpan session user (tanpa sistem Auth Laravel)
    Session::put('user', [
        'id' => $user->id,
        'name' => $user->name,
        'role' => $user->role,
    ]);

    return redirect('/');
});

// Logout
Route::get('/logout', function () {
    Session::forget('user');
    return redirect('/login');
});


/*
|--------------------------------------------------------------------------
| Register
|--------------------------------------------------------------------------
*/

Route::get('/register', fn() => view('auth.register'));
Route::post('/register', function (Request $request) {
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6|confirmed',
    ]);

    User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => 'user',
    ]);

    return redirect('/login')->with('success', 'Akun berhasil dibuat! Silakan login.');
});


/*
|--------------------------------------------------------------------------
| Lupa Password, OTP, dan Reset Password
|--------------------------------------------------------------------------
*/

// Halaman lupa password
Route::get('/forgot-password', fn() => view('auth.forgot'));

// Kirim kode OTP ke email
Route::post('/forgot-password', function () {
    $email = request('email');
    $code = rand(100000, 999999);

    session(['reset_email' => $email, 'reset_code' => $code]);
    logger("Kode verifikasi untuk {$email}: {$code}");

    Mail::to($email)->send(new OtpMail($code));

    return redirect('/verify-token');
});

// Form verifikasi OTP
Route::get('/verify-token', function () {
    $code = session('reset_code');
    $debug_code = config('app.debug') ? $code : null;

    logger("DEBUG: Session reset_code = " . $code);

    return view('auth.verify-token', ['debug_code' => $debug_code]);
});

// Proses verifikasi OTP
Route::post('/verify-token', function () {
    $token = request('token');
    $correct = session('reset_code');

    logger("User input token = {$token}, Session code = {$correct}");

    if ($token == $correct) {
        return redirect('/reset-password');
    } else {
        return back()->with('error', 'Kode verifikasi salah! Silakan coba lagi.');
    }
});

// Tampilkan form reset password
Route::get('/reset-password', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');

// Proses ubah password
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');


/*
|--------------------------------------------------------------------------
| Profil (Show & Edit)
|--------------------------------------------------------------------------
|
| Menggunakan session buatan sendiri, bukan middleware "auth" bawaan Laravel.
| Jadi kita buat middleware manual agar cek Session::has('user')
|
*/

// routes/web.php (KODE BARU YANG BENAR)
Route::group(['prefix' => '', 'middleware' => 'web'], function () {

    // Cek login di controller, tapi bisa juga tambahkan middleware buatan sendiri nanti
    // Untuk sekarang, controller-mu sudah handle cek session

    // Arahkan ke ProfileController@show
    Route::get('/profile', [ProfileController::class, 'show'])
         ->name('profile.show');

    // Arahkan ke ProfileController@edit
    Route::get('/edit-profile', [ProfileController::class, 'edit'])
         ->name('profile.edit');

    // Arahkan ke ProfileController@update
    Route::post('/edit-profile', [ProfileController::class, 'update'])
         ->name('profile.update');
});
