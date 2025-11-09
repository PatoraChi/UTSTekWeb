<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            
            // 1. SIAPA PENERIMA: (User yang postingannya di-like/comment)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // 2. SIAPA PELAKU: (User yang me-like/comment)
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            
            // 3. JENIS AKSI: ('post_like', 'post_comment', 'comment_like', 'comment_reply')
            $table->string('type'); 
            
            // 4. LINK TUJUAN
            $table->foreignId('post_id')->constrained('posts')->onDelete('cascade');
            $table->foreignId('comment_id')->nullable()->constrained('comments')->onDelete('cascade');
            
            // 5. STATUS
            $table->timestamp('read_at')->nullable(); // Kapan notif dibaca
            
            $table->timestamps(); // Kapan notif dibuat
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
