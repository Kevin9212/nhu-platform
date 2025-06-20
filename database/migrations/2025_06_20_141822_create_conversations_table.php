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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->string('buyer_account', 64)->index();
            $table->string('seller_account', 64)->index();
            $table->timestamps(); // created_at and updated_at

            $table->unique(['buyer_account', 'seller_account']);
            $table->foreign('buyer_account')->references('account')->on('users')->onDelete('cascade');
            $table->foreign('seller_account')->references('account')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
