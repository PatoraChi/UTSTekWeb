<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute; // TAMBAH INI
use Illuminate\Support\Facades\Storage; // TAMBAH INI

class PostMedia extends Model
{
    use HasFactory;

    protected $fillable = ['post_id', 'file_path', 'file_type'];

    // Menghapus file dari storage saat record PostMedia dihapus
    protected static function booted(): void
    {
        static::deleting(function (PostMedia $media) {
            // ✅ PERUBAHAN DI SINI
            // Hapus file dari disk 'public'
            if ($media->file_path) {
                Storage::disk('public')->delete($media->file_path);
            }
        });
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Accessor untuk mendapatkan URL media dari Cloudinary.
     * Dapat dipanggil sebagai $media->url
     */
    protected function url(): Attribute
    {
        return Attribute::make(
            // ✅ PERUBAHAN DI SINI
            // Arahkan ke URL public storage
            // Ini akan menghasilkan: http://127.0.0.1:8000/storage/path/ke/file.jpg
            get: fn () => asset('storage/' . $this->file_path),
        );
    }
}