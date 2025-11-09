<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    use HasFactory;
    
    protected $fillable = ['user_id', 'post_id', 'parent_id', 'body'];

    /**
     * TAMBAHKAN SELURUH BLOK INI (Method 'booted')
     * Ini adalah "magic" untuk cascading delete pada komentar.
     */
    protected static function booted(): void
    {
        static::deleting(function (Comment $comment) {
            
            // 1. Hapus semua 'like' yang terkait dengan komentar ini
            $comment->likes()->delete();

            // 2. Hapus semua 'balasan' (replies)
            // Ini akan berjalan rekursif: 
            // Menghapus balasan akan memicu event 'deleting' untuk balasan itu,
            // yang kemudian akan menghapus like dan sub-balasannya, dst.
            $comment->replies()->delete();
        });
    }

    /**
     * Komentar ini milik satu User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Komentar ini milik satu Post.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Komentar ini punya BANYAK balasan (replies).
     */
    public function replies(): HasMany
    {
        // Muat juga relasi 'replies' di dalam 'replies' (nested)
        return $this->hasMany(Comment::class, 'parent_id')->with('user', 'likes', 'parent.user', 'replies');
    }

    /**
     * Komentar ini adalah balasan dari SATU komentar (parent).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Komentar ini punya BANYAK likes.
     */
    public function likes(): HasMany
    {
        return $this->hasMany(CommentLike::class);
    }
}