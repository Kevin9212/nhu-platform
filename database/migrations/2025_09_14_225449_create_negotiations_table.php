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
        Schema::create('negotiations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idle_item_id')->constrained('idle_items')->onDelete('cascade'); // 商品
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->integer('proposed_price');  // 出價金額
            $table->enum('status', ['open', 'agreed', 'rejected'])->default('open');
            $table->timestamps();
        });
        Schema::table('messages', function (Blueprint $table) {
            $table->boolean('is_system')->default(false)->after('content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('negotiations');
    }
};
