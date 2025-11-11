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
            // ✅ KEMBALI KE CLOUDINARY
            if ($media->file_path) {
                Storage::disk('cloudinary')->delete($media->file_path);
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
            // ✅ KEMBALI KE CLOUDINARY
            // Ini akan menghasilkan URL https://res.cloudinary.com/...
            get: fn () => Storage::disk('cloudinary')->url($this->file_path),
        );
    }
}