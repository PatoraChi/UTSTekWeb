<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // 1. Tambahkan ini
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // 2. Tambahkan ini

class PostMedia extends Model
{
    use HasFactory; // 3. Tambahkan ini

    /**
     * 4. INI PERBAIKAN UTAMANYA:
     * Tentukan kolom yang boleh diisi untuk PostMedia::create()
     * Kita perlu 'post_id' (sesuai error), dan juga dua lainnya yang kita isi.
     */
    protected $fillable = ['post_id', 'file_path', 'file_type'];


    /**
     * Relasi: Media ini milik satu Postingan.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}