<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
            $table->string('account', 64)->primary()->comment('登入帳號(學號+email)');
            $table->string('user_password');
            $table->string('nickname', 32);
            $table->string('avatar', 256)->nullable();
            $table->string('user_phone', 16)->index();
            $table->timestamp('register_in_time')->useCurrent();
            $table->timestamp('last_login_time')->nullable();
            $table->enum('user_status', ['active', 'banned'])->default('active');
            $table->enum('role', ['user', 'admin'])->default('user');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
