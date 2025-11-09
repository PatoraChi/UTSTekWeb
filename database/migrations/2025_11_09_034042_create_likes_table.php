<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            
            // Foreign key ke tabel users
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Foreign key ke tabel posts
            $table->foreignId('post_id')->constrained()->onDelete('cascade');

            // Mencegah user_id dan post_id yang sama didaftarkan 2x
            $table->unique(['user_id', 'post_id']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
};