<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            
            // KUNCI UNTUK REPLY:
            // Kolom ini merujuk ke ID di tabel 'comments' ini sendiri.
            // Nullable berarti jika NULL, ini adalah komentar utama.
            $table->foreignId('parent_id')->nullable()->constrained('comments')->onDelete('cascade');

            $table->text('body'); // Isi komentar
            $table->timestamps();
        });
    }
    // ...
};