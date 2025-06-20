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
            $table->unsignedBigInteger('conversation_id');
            $table->string('sender_account', 64);
            $table->unsignedBigInteger('idle_id')->nullable();
            $table->enum('msg_type', ['text', 'image', 'system'])->default('text');
            $table->text('content');
            $table->timestamp('created_at')->useCurrent();
            $table->boolean('is_recalled')->default(false);

            $table->index(['conversation_id', 'created_at']);
            $table->index(['sender_account', 'created_at']);

            $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
            $table->foreign('sender_account')->references('account')->on('users')->onDelete('cascade');
            $table->foreign('idle_id')->references('id')->on('idle_items')->onDelete('set null');
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
