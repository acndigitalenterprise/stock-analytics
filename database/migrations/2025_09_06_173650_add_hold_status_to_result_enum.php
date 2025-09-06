<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL to modify ENUM as Laravel doesn't handle ENUM changes well
        DB::statement("ALTER TABLE `requests` MODIFY COLUMN `result` ENUM('MONITORING', 'WIN', 'SUPER_WIN', 'LOSS', 'TIMEOUT', 'HOLD') NULL DEFAULT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum
        DB::statement("ALTER TABLE `requests` MODIFY COLUMN `result` ENUM('MONITORING', 'WIN', 'SUPER_WIN', 'LOSS', 'TIMEOUT') NOT NULL DEFAULT 'MONITORING'");
    }
};
