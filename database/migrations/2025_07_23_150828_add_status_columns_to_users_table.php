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
        Schema::table('users', function (Blueprint $table) {
            // 在 'role' 欄位後面新增這兩個欄位
            $table->integer('warnings_count')->unsigned()->default(0)->after('role');
            $table->integer('suspension_count')->unsigned()->default(0)->after('warnings_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['warnings_count', 'suspension_count']);
        });
    }
};
