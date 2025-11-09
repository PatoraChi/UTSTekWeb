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
use App\Http\Controllers\PostController;
use App\Models\Post;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\SaveController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CommentLikeController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\ModerationController;
use App\Http\Controllers\AdminController;
/*
|--------------------------------------------------------------------------
| Halaman Utama
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (!Session::has('user')) {
        return redirect('/login');
    }

    $user = User::find(Session::get('user.id'));

    if (!$user) {
        Session::forget('user');
        return redirect('/login');
    }

    // AMBIL SEMUA POSTINGAN DARI DATABASE
    // Kita pakai 'with' (Eager Loading) agar lebih efisien
    // Kita ambil juga data 'user' (pemilik post) dan 'media' (file-filenya)
    // Diurutkan dari yang paling baru (latest)

    $posts = Post::with(['user', 'media', 'likes', 'saves'])
                ->withCount('allComments') 
                ->latest()
                ->get();

    // KIRIM DATA $user dan $posts KE VIEW
    return view('home', compact('user', 'posts'));
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

    if ($user->is_banned) {
        // Cek apakah ban-nya permanen (banned_until == NULL)
        if (is_null($user->banned_until)) {
            return back()->with('error', 'Akun Anda telah diblokir secara permanen.');
        }
        
        // Cek apakah ban-nya temporer dan masih aktif
        // Kita butuh 'use Carbon\Carbon;' di atas file, tapi 'now()' juga bisa
        if (now()->lessThan($user->banned_until)) {
            // Menggunakan ->diffForHumans() untuk 'dalam 3 hari lagi'
            return back()->with('error', 'Akun Anda ditangguhkan. Sisa waktu: ' . now()->diffForHumans($user->banned_until, true));
        }
        
        // Jika ban-nya temporer dan sudah lewat, un-ban user-nya
        $user->is_banned = false;
        $user->banned_until = null;
        $user->save();
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
})->name('verify.check');


// Tampilkan form reset password
Route::get('/reset-password', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');

// Proses ubah password
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');


/*
|--------------------------------------------------------------------------
| Profil (Show & Edit)
|--------------------------------------------------------------------------
*/
// HAPUS Route::group lama kamu dan GANTI DENGAN INI:

// 2. Route untuk melihat profil SENDIRI ( /profile )
Route::get('/profile', [ProfileController::class, 'show'])
    ->name('profile.show');

// 3. Route untuk melihat profil ORANG LAIN ( /profile/5 )
Route::get('/profile/{profile}', [ProfileController::class, 'show']) // <-- UBAH DI SINI
    ->name('profile.show.user');

// 4. Route untuk Halaman Edit Profil ( /edit-profile )
Route::get('/edit-profile', [ProfileController::class, 'edit'])
    ->name('profile.edit');
Route::post('/edit-profile', [ProfileController::class, 'update']) // Kamu sudah punya ini
    ->name('profile.update');

/*
|--------------------------------------------------------------------------
| Fitur Follow
|--------------------------------------------------------------------------
*/
// 5. Route untuk Aksi Follow/Unfollow (AJAX)
Route::post('/follow/{profile}', [FollowController::class, 'toggleFollow'])
    ->name('follow.toggle');

/*
|--------------------------------------------------------------------------
| Fitur Postingan (Buat & Simpan)
|--------------------------------------------------------------------------
*/

// Tampilkan halaman form 'Buat'
Route::get('/post/create', [PostController::class, 'create']);

// Proses simpan data dari form 'Buat'
Route::post('/post/store', [PostController::class, 'store']);

// --- TAMBAHKAN 3 ROUTE BARU DI BAWAH INI ---

// 1. Menampilkan halaman form edit
Route::get('/post/{post}/edit', [PostController::class, 'edit'])
    ->name('post.edit'); // Kita beri nama agar mudah dipanggil

// 2. Menyimpan perubahan dari form edit (Update)
Route::put('/post/{post}', [PostController::class, 'update'])
    ->name('post.update');

// 3. Menghapus postingan (Delete)
Route::delete('/post/{post}', [PostController::class, 'destroy'])
    ->name('post.destroy');

/*
|--------------------------------------------------------------------------
| Fitur Interaksi (Like)
|--------------------------------------------------------------------------
*/

// {post} akan otomatis di-binding ke parameter (Post $post) di controller
Route::post('/post/{post}/like', [LikeController::class, 'toggleLike']);
/*
|--------------------------------------------------------------------------
| Fitur Interaksi (Save)
|--------------------------------------------------------------------------
*/

// {post} akan otomatis di-binding ke parameter (Post $post) di controller
Route::post('/post/{post}/save', [SaveController::class, 'toggleSave']);
/*
|--------------------------------------------------------------------------
| Fitur Komentar
|--------------------------------------------------------------------------
*/

// HALAMAN BARU: Menampilkan 1 postingan & semua komentarnya
Route::get('/post/{post}', [PostController::class, 'show']);

// Menyimpan komentar (baik komentar utama maupun balasan)
Route::post('/post/{post}/comment', [CommentController::class, 'store']);

// AJAX: Like/Unlike sebuah komentar
Route::post('/comment/{comment}/like', [CommentLikeController::class, 'toggleLike']);
// 1. Menampilkan halaman form edit komentar
Route::get('/comment/{comment}/edit', [CommentController::class, 'edit'])
    ->name('comment.edit');

// 2. Menyimpan perubahan dari form edit (Update)
Route::put('/comment/{comment}', [CommentController::class, 'update'])
    ->name('comment.update');

// 3. Menghapus komentar (Delete)
Route::delete('/comment/{comment}', [CommentController::class, 'destroy'])
    ->name('comment.destroy');


/*
|--------------------------------------------------------------------------
| Fitur Topik & Cari
|--------------------------------------------------------------------------
*/

Route::get('/topik', [TopicController::class, 'index']);
Route::get('/cari', [SearchController::class, 'index']);
/*
|--------------------------------------------------------------------------
| Fitur Notifikasi
|--------------------------------------------------------------------------
*/
Route::get('/notifikasi', [NotificationController::class, 'index']);

/*
|--------------------------------------------------------------------------
| Fitur Moderasi (Khusus Admin)
|--------------------------------------------------------------------------
*/
// Gunakan middleware 'admin' yang sudah kita daftarkan
Route::middleware('admin')->group(function () {
    
    // Route untuk memproses ban user
    Route::post('/moderation/ban/{profile}', [ModerationController::class, 'banUser'])
        ->name('moderation.ban');
    
    // 1. Menampilkan halaman "Manajemen User"
    Route::get('/admin/users', [AdminController::class, 'listUsers'])
        ->name('admin.users.list');

    // 2. Memproses perubahan role
    Route::post('/admin/users/{user}/update-role', [AdminController::class, 'updateRole'])
        ->name('admin.users.update_role');

    Route::post('/admin/users/{user}/manage-ban', [AdminController::class, 'manageBan'])
        ->name('admin.users.manage_ban');

});