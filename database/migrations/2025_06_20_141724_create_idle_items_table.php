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
            $table->string('idle_name', 128);
            $table->text('idle_details');
            $table->decimal('idle_price', 10, 2)->index();
            $table->unsignedBigInteger('idle_label');
            $table->timestamp('release_time')->useCurrent();
            $table->tinyInteger('idle_status')->default(1);
            $table->string('user_account', 64);
            $table->string('current_buyer_account', 64)->nullable();

            // 租屋專屬
            $table->boolean('is_rental')->default(false);
            $table->string('room_type', 32)->nullable();
            $table->boolean('pets_allowed')->nullable();
            $table->boolean('cooking_allowed')->nullable();
            $table->text('rental_rules')->nullable();
            $table->text('equipment')->nullable();
            $table->json('meetup_location')->nullable();

            // 索引
            $table->index(['idle_label', 'idle_status'], 'idx_label_status');

            // 外鍵
            $table->foreign('user_account')->references('account')->on('users')->onDelete('cascade');
            $table->foreign('idle_label')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('current_buyer_account')->references('account')->on('users')->onDelete('set null');
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
