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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('idle_item_id')->constrained()->onDelete('cascade');
            $table->decimal('order_price', 10, 2);
            $table->tinyInteger('payment_status')->default(0);
            $table->string('payment_way', 32)->default('面交');
            $table->timestamp('create_time')->useCurrent()->index();
            $table->enum('order_status', ['pending', 'success', 'cancelled', 'failed'])->default('pending');
            $table->string('cancel_reason', 128)->nullable();
            $table->json('meetup_location')->nullable();
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
