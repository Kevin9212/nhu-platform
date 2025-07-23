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
        Schema::create('idle_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->comment('賣家ID')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->comment('分類ID')->constrained('categories')->onDelete('cascade');
            $table->foreignId('current_buyer_id')->nullable()->comment('已成交買家ID')->constrained('users')->onDelete('set null');
            $table->string('idle_name', 128);
            $table->text('idle_details');
            $table->decimal('idle_price', 10, 2)->index();
            $table->tinyInteger('idle_status')->default(1)->comment('1:上架 2:議價中 3:交易中 4:完成 0:刪除');

            // 租屋專屬
            $table->boolean('is_rental')->default(false);
            $table->string('room_type', 32)->nullable();
            $table->boolean('pets_allowed')->nullable();
            $table->boolean('cooking_allowed')->nullable();
            $table->text('rental_rules')->nullable();
            $table->text('equipment')->nullable();
            $table->json('meetup_location')->nullable();

            $table->index(['category_id', 'idle_status']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('idle_items');
    }
};
