<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Cek apakah user sudah login
        if (!Session::has('user')) {
            return redirect('/login');
        }

        // 2. Cek apakah role-nya admin atau lebih tinggi
        $role = Session::get('user.role');
        
        if ($role === 'admin' || $role === 'super_admin' || $role === 'owner') {
            // 3. Jika ya, izinkan masuk
            return $next($request);
        }

        // 4. Jika bukan, tendang ke Halaman Utama
        return redirect('/')->with('error', 'Anda tidak memiliki hak akses.');
    }
}