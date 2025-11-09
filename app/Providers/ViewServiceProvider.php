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
     */public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            
            $globalUser = null;
            $unreadCount = 0;
            $recentNotifications = collect();
            $recommendedUsers = collect(); 
            $followingIds = collect();
            if (Session::has('user')) {
                // 1. Ambil data user
                $globalUser = User::find(Session::get('user.id'));

                if ($globalUser) {
// 2. Ambil data Notifikasi (Kode lamamu)
                    $unreadCount = Notification::where('user_id', $globalUser->id)
                                               ->whereNull('read_at')
                                               ->count();
                    
                    $recentNotifications = Notification::where('user_id', $globalUser->id)
                                                       ->with('sender')
                                                       ->latest()
                                                       ->take(5)
                                                       ->get();

                    // 3. Ambil Rekomendasi (Kode lamamu)
                    $recommendedUsers = User::withCount('followers') 
                                        ->where('id', '!=', $globalUser->id) 
                                        ->orderBy('followers_count', 'desc') 
                                        ->take(6) 
                                        ->get();
                                        
                    // --- 4. LOGIKA BARU: Ambil ID user yang kita ikuti ---
                    $followingIds = $globalUser->following()->pluck('id');
                    // --------------------------------------------------
                }
            }
            
        $view->with('user', $globalUser)
                 ->with('unreadCount', $unreadCount)
                 ->with('recentNotifications', $recentNotifications)
                 ->with('recommendedUsers', $recommendedUsers) // Data rekomendasi
                 ->with('followingIds', $followingIds); // Data user yg kita follow
        });
    }
}