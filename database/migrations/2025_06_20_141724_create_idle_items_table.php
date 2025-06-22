<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('idle_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('current_buyer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('idle_name', 128);
            $table->text('idle_details');
            $table->decimal('idle_price', 10, 2)->index();
            $table->timestamp('release_time')->useCurrent();
            $table->tinyInteger('idle_status')->default(1);

            // 租屋專屬
            $table->boolean('is_rental')->default(false);
            $table->string('room_type', 32)->nullable();
            $table->boolean('pets_allowed')->nullable();
            $table->boolean('cooking_allowed')->nullable();
            $table->text('rental_rules')->nullable();
            $table->text('equipment')->nullable();
            $table->json('meetup_location')->nullable();

            // 索引
            $table->index(['category_id', 'idle_status'], 'idx_label_status');
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
