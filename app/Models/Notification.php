<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;



    /**
     * PENTING 2: Inilah solusi error-mu.
     * Daftar kolom yang BOLEH diisi menggunakan Notification::create()
     */
    protected $fillable = [
        'user_id',    // Siapa penerima
        'sender_id',  // Siapa pelaku
        'type',       // Jenis notif
        'post_id',    // Link ke post
        'comment_id', // Link ke comment (jika ada)
        'read_at',    // Status dibaca
    ];

    /**
     * Relasi untuk mengambil data 'pelaku' notifikasi.
     * Kita akan butuh ini untuk menampilkan "Prabowo menyukai..."
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Relasi untuk mengambil data postingan yang terkait.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
    
    /**
     * Relasi untuk mengambil data komentar yang terkait.
     */
    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }

    /**
     * Relasi untuk mengambil data 'penerima' notifikasi.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}