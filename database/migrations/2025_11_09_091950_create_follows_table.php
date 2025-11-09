<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('follows', function (Blueprint $table) {
            // Kolom 'follower_id' adalah user yang 'mengikuti'
            $table->foreignId('follower_id')->constrained('users')->onDelete('cascade');
            
            // Kolom 'following_id' adalah user yang 'diikuti'
            $table->foreignId('following_id')->constrained('users')->onDelete('cascade');
            
            // Ini mencegah user mengikuti orang yang sama > 1 kali
            $table->primary(['follower_id', 'following_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follows');
    }
};
