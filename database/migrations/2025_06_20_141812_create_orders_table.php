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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 32);
            $table->string('user_account', 64)->index();
            $table->unsignedBigInteger('idle_id')->index();
            $table->decimal('order_price', 10, 2);
            $table->tinyInteger('payment_status')->default(0);
            $table->string('payment_way', 32)->default('面交');
            $table->timestamp('create_time')->useCurrent()->index();
            $table->enum('order_status', ['pending', 'success', 'cancelled', 'failed'])->default('pending');
            $table->string('cancel_reason', 128)->nullable();
            $table->json('meetup_location')->nullable();

            $table->foreign('user_account')->references('account')->on('users')->onDelete('cascade');
            $table->foreign('idle_id')->references('id')->on('idle_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
