<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 把 msg_type 放寬為 VARCHAR(32)，預設 text
        DB::statement("ALTER TABLE `messages` MODIFY `msg_type` VARCHAR(32) NOT NULL DEFAULT 'text'");
    }

    public function down(): void
    {
        // 退回較保守的 10（若你原本是 ENUM，改成對應的 ENUM）
        DB::statement("ALTER TABLE `messages` MODIFY `msg_type` VARCHAR(10) NOT NULL DEFAULT 'text'");
        // 若原本確實是 ENUM，down() 可改為：
        // DB::statement(\"ALTER TABLE `messages` MODIFY `msg_type` ENUM('text','image','system') NOT NULL DEFAULT 'text'\");
    }
};
