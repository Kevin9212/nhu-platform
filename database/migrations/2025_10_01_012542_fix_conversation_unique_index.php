<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            // 🔹 先刪掉舊的 unique（只含 buyer_id, seller_id）
            $table->dropUnique('conversations_buyer_id_seller_id_unique');

            // 🔹 建立新的 unique（三欄：buyer, seller, item）
            $table->unique(['buyer_id', 'seller_id', 'idle_item_id'], 'conversations_buyer_seller_item_unique');
        });
    }

    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            // 🔹 還原：刪掉三欄 unique
            $table->dropUnique('conversations_buyer_seller_item_unique');

            // 🔹 加回原本的二欄 unique
            $table->unique(['buyer_id', 'seller_id'], 'conversations_buyer_id_seller_id_unique');
        });
    }
};
