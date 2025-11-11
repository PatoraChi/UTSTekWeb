<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    use HasFactory; 

    protected $fillable = ['user_id', 'caption'];

    /**
     * TAMBAHKAN SELURUH BLOK INI (Method 'booted')
     * Ini adalah "magic" untuk cascading delete.
     */
protected static function booted(): void
    {
        static::deleting(function (Post $post) {
            
            // 1. Hapus semua file media dari storage
            foreach ($post->media as $media) {
                // ✅ PERUBAHAN DI SINI
                // Pastikan file_path ada dan hapus dari disk 'public'
                if ($media->file_path) {
                    Storage::disk('public')->delete($media->file_path);
                }
            }

            // 2. Hapus relasi database
            $post->media()->delete();
            $post->likes()->delete();
            $post->saves()->delete();
            $post->allComments()->delete();
            $post->tags()->detach(); 
        });
    }

    /**
     * Relasi: Postingan ini milik satu User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ... (sisa relasi media, tags, likes, saves, comments, allComments) ...
    // ... (biarkan seperti sedia kala) ...
    
    /**
     * Relasi: Postingan ini punya banyak Media.
     */
    public function media(): HasMany
    {
        return $this->hasMany(PostMedia::class);
    }

    /**
     * Relasi: Postingan ini punya banyak Tag.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }
    /**
 * Relasi: Postingan ini punya banyak Likes.
 */
    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }
    public function saves(): HasMany
    {
        return $this->hasMany(Save::class);
    }
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id');
    }
    /**
     * Relasi: Postingan ini punya banyak Komentar (TERMASUK BALASAN).
     * Ini khusus untuk menghitung total.
     */
    public function allComments(): HasMany
    {
        // Tidak pakai whereNull('parent_id')
        return $this->hasMany(Comment::class);
    }
}