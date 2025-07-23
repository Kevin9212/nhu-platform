<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 32)->unique();
            $table->foreignId('user_id')->comment('買家ID')->constrained('users')->onDelete('cascade');
            $table->foreignId('idle_item_id')->constrained('idle_items')->onDelete('cascade');
            $table->decimal('order_price', 10, 2);
            $table->boolean('payment_status')->default(false)->comment('0:未付 1:已付');
            $table->string('payment_way', 32)->default('面交');
            $table->enum('order_status', ['pending', 'success', 'cancelled', 'failed'])->default('pending');
            $table->string('cancel_reason', 128)->nullable();
            $table->json('meetup_location')->nullable()->comment('面交地點備份');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('orders');
    }
};
