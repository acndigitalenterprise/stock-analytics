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
        // SQLite doesn't support ENUM, this migration is MySQL specific
        // For SQLite, the requests table will be created with TEXT column for result
        if (DB::connection()->getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE `requests` MODIFY COLUMN `result` ENUM('MONITORING', 'WIN', 'SUPER_WIN', 'LOSS', 'TIMEOUT', 'HOLD') NULL DEFAULT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum (MySQL only)
        if (DB::connection()->getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE `requests` MODIFY COLUMN `result` ENUM('MONITORING', 'WIN', 'SUPER_WIN', 'LOSS', 'TIMEOUT') NOT NULL DEFAULT 'MONITORING'");
        }
    }
};
