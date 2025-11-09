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
        // GANTI BAGIAN 'up()' ANDA DENGAN INI
        Schema::create('saves', function (Blueprint $table) {
            $table->id();
            
            // INI KOLOM YANG HILANG:
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('post_id')->constrained()->onDelete('cascade');

            // Tambahkan unique juga, biar sama seperti 'likes'
            $table->unique(['user_id', 'post_id']);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saves');
    }
};