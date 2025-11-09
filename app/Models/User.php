<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;class User extends Authenticatable
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
    

}
