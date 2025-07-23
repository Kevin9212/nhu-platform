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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            // 外鍵
            $table->foreignId('conversation_id')->constrained('conversations')->onDelete('cascade');
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('idle_item_id')->nullable()->constrained('idle_items')->onDelete('set null');

            // 訊息內容
            $table->enum('msg_type', ['text', 'image', 'system'])->default('text');
            $table->text('content');
            $table->boolean('is_recalled')->default(false)->comment('訊息是否已收回');

            // 時間戳
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
