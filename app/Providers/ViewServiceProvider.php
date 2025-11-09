<?php

namespace App\Providers;

use Illuminate\Support\Facades\View; 
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Session; 
use App\Models\User; 
use App\Models\Notification;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            
            $user = null;
            $unreadCount = 0;
            $recentNotifications = collect(); // Buat koleksi kosong

            if (Session::has('user')) {
                // 1. Ambil data user
                $user = User::find(Session::get('user.id'));

                if ($user) {
                    // 2. Ambil JUMLAH notif yang belum dibaca
                    $unreadCount = Notification::where('user_id', $user->id)
                                               ->whereNull('read_at')
                                               ->count();
                    
                    // 3. Ambil 5 notif terbaru (dibaca maupun belum)
                    $recentNotifications = Notification::where('user_id', $user->id)
                                                       ->with('sender')
                                                       ->latest()
                                                       ->take(5)
                                                       ->get();
                }
            }
            
            // 4. Kirim semua data ke layout 'layouts.app'
            $view->with('user', $user)
                 ->with('unreadCount', $unreadCount)
                 ->with('recentNotifications', $recentNotifications);
        });
    }
}