<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Identitas dasar
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');

            // Tambahan untuk profil
            $table->string('profile_image')->nullable(); // simpan path file gambar
            $table->text('bio')->nullable();

            // Tambahan sistem manajemen user
            $table->enum('role', ['user', 'admin', 'super_admin', 'owner'])->default('user');
            $table->boolean('is_banned')->default(false);
            $table->timestamp('banned_until')->nullable();

            // Standar Laravel
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        // Jika kamu masih butuh untuk login session manual
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // Reset password (opsional)
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('users');
    }
};
