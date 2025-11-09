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
        Schema::table('notifications', function (Blueprint $table) {
            // Ubah kolom post_id agar bisa diisi NULL (nullable)
            // .change() berarti kita memodifikasi kolom yang sudah ada
            $table->foreignId('post_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
/**
 * Reverse the migrations.
 */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Kembalikan seperti semula (tidak boleh null)
            $table->foreignId('post_id')->nullable(false)->change();
        });
    }
};
