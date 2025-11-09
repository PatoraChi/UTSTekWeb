<?php
// ...
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            // Foreign key ke tabel users
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('caption')->nullable(); // Keterangan postingan
            $table->timestamps(); // kapan dibuat (created_at)
        });
    }
    // ...
};