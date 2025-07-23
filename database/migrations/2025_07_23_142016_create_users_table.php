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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nickname', 32);
            $table->string('account', 64)->unique()->comment('學號帳號');
            $table->string('email')->unique()->comment('電子郵件，用於登入與密碼重設');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->comment('Laravel標準密碼欄位');
            $table->rememberToken();
            $table->string('avatar', 256)->nullable();
            $table->string('user_phone', 16)->nullable()->index();
            $table->timestamp('last_login_time')->nullable();
            $table->enum('user_status', ['active', 'banned'])->default('active');
            $table->enum('role', ['user', 'admin'])->default('user');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
