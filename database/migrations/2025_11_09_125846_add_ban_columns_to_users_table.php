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
        Schema::table('users', function (Blueprint $table) {
            // Cek apakah kolom 'is_banned' BELUM ADA
            if (!Schema::hasColumn('users', 'is_banned')) {
                $table->boolean('is_banned')->default(false)->after('role');
            }

            // Cek apakah kolom 'banned_until' BELUM ADA
            if (!Schema::hasColumn('users', 'banned_until')) {
                // Pastikan kolom 'is_banned' ada (meskipun sudah ada sebelumnya)
                // agar kita bisa meletakkan 'banned_until' setelahnya.
                $table->timestamp('banned_until')->nullable()->after('is_banned');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Cek apakah kolomnya ADA sebelum menghapusnya
            if (Schema::hasColumn('users', 'is_banned')) {
                $table->dropColumn('is_banned');
            }
            if (Schema::hasColumn('users', 'banned_until')) {
                $table->dropColumn('banned_until');
            }
        });
    }
};
