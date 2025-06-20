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
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->string('user_account', 64);
            $table->unsignedBigInteger('idle_id');
            $table->timestamp('create_time')->useCurrent();

            $table->unique(['user_account', 'idle_id'], 'uniq_user_item');
            $table->foreign('user_account')->references('account')->on('users')->onDelete('cascade');
            $table->foreign('idle_id')->references('id')->on('idle_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
