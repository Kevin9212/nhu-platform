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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->string('rater_id', 64);
            $table->string('rated_id', 64);
            $table->unsignedBigInteger('order_id');
            $table->tinyInteger('score');
            $table->text('comment')->nullable();
            $table->timestamp('rating_time')->useCurrent();

            $table->foreign('rater_id')->references('account')->on('users')->onDelete('cascade');
            $table->foreign('rated_id')->references('account')->on('users')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
