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
            $table->string('buyer_account', 64);
            $table->string('seller_account', 64);
            $table->unsignedBigInteger('idle_id');
            $table->decimal('offered_price', 10, 2);
            $table->timestamp('negotiation_time')->useCurrent();

            // 複合主鍵
            $table->primary(['buyer_account', 'seller_account', 'idle_id']);

            $table->foreign('buyer_account')->references('account')->on('users')->onDelete('cascade');
            $table->foreign('seller_account')->references('account')->on('users')->onDelete('cascade');
            $table->foreign('idle_id')->references('id')->on('idle_items')->onDelete('cascade');
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
