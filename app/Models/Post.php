<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // 1. Tambahkan ini
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // 2. Tambahkan ini
use Illuminate\Database\Eloquent\Relations\HasMany; // 3. Tambahkan ini
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Post extends Model
{
    use HasFactory; // 4. Tambahkan ini

    /**
     * 5. INI PERBAIKAN UTAMANYA:
     * Tentukan kolom mana yang boleh diisi saat menggunakan Post::create()
     */
    protected $fillable = ['user_id', 'caption'];


    /**
     * Relasi: Postingan ini milik satu User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

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
}