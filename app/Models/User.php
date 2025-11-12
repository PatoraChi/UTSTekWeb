<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'bio',
        'profile_image',
        'role', 
        'is_banned',
        'banned_until', 
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'banned_until' => 'datetime',
        ];
    }
    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }
    public function saves(): HasMany
    {
        return $this->hasMany(Save::class);
    }
/**
     * Relasi: User ini punya banyak Postingan
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Relasi: User ini 'diikuti oleh' (Followers) banyak User lain
     */
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,     // Model tujuan
            'follows',       // Nama tabel pivot
            'following_id',  // Foreign key di pivot untuk user INI
            'follower_id'    // Foreign key di pivot untuk user LAIN
        );
    }

    /**
     * Relasi: User ini 'mengikuti' (Following) banyak User lain
     */
    public function following(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,     // Model tujuan
            'follows',       // Nama tabel pivot
            'follower_id',   // Foreign key di pivot untuk user INI
            'following_id'   // Foreign key di pivot untuk user LAIN
        );
    }
    
    /**
     * Helper: Cek apakah user yang login sedang mengikuti $user
     */
    public function isFollowing(User $user): bool
    {
        // Cek di daftar 'following' milik kita, apakah ada id $user
        return $this->following()->where('following_id', $user->id)->exists();
    }
    
/**
     * Relasi: Postingan yang di-LIKE oleh user ini.
     * Kita menggunakan tabel 'likes' sebagai pivot.
     */
    public function likedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'likes', 'user_id', 'post_id')
                    ->withTimestamps() // Untuk mengambil data created_at
                    ->latest('likes.created_at'); // Urutkan dari yang terbaru di-like
    }

    /**
     * Relasi: Postingan yang di-SAVE oleh user ini.
     * Kita menggunakan tabel 'saves' sebagai pivot.
     */
    public function savedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'saves', 'user_id', 'post_id')
                    ->withTimestamps()
                    ->latest('saves.created_at'); // Urutkan dari yang terbaru di-save
    }
    
    /**
     * Relasi: Semua komentar yang dibuat oleh user ini.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->latest();
    }
    protected function profileImageUrl(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                // $attributes['profile_image'] adalah kolom 'profile_image'
                $publicId = $attributes['profile_image'];
                
                // Jika user PUNYA foto profil (Public ID ada):
                if ($publicId) {
                    try {
                        // Menggunakan Storage::disk('cloudinary')->url()
                        return Storage::disk('cloudinary')->url($publicId);
                    } catch (\Exception $e) {
                        // Fallback jika Cloudinary API gagal (misal resource tidak ditemukan)
                        return asset('images/default_avatar.png'); 
                    }
                }
                
                // Jika user TIDAK PUNYA foto profil:
                return asset('images/default_avatar.png'); 
            }
        );
    }
}
