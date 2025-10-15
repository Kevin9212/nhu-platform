<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 先新增欄位 (允許 NULL 以避免舊資料阻擋) 並加外鍵
        Schema::table('conversations', function (Blueprint $table) {
            if (!Schema::hasColumn('conversations', 'idle_item_id')) {
                $table->foreignId('idle_item_id')
                      ->nullable()
                      ->after('seller_id')
                      ->constrained('idle_items')
                      ->onDelete('cascade');
            }
        });

        // 再新增複合唯一索引
        Schema::table('conversations', function (Blueprint $table) {
            $table->unique(
                ['buyer_id', 'seller_id', 'idle_item_id'],
                'conversations_buyer_id_seller_id_idle_item_id_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            // 先移除唯一索引（用名稱最保險）
            $table->dropUnique('conversations_buyer_id_seller_id_idle_item_id_unique');
        });

        Schema::table('conversations', function (Blueprint $table) {
            // 再移除外鍵與欄位（若存在）
            if (Schema::hasColumn('conversations', 'idle_item_id')) {
                $table->dropConstrainedForeignId('idle_item_id');
                // 舊版可用：
                // $table->dropForeign(['idle_item_id']);
                // $table->dropColumn('idle_item_id');
            }
        });
    }
};
