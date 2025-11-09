<?php
// ...
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_media', function (Blueprint $table) {
            $table->id();
            // Foreign key ke tabel posts
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->string('file_path'); // Path ke file di server
            $table->string('file_type', 20)->default('image'); // 'image' atau 'video'
            $table->timestamps();
        });
    }
    // ...
};