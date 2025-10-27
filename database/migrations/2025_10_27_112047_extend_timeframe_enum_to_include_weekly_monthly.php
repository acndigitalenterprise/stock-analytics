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
        // Extend ENUM to include '1w' and '1m'
        DB::statement("ALTER TABLE requests MODIFY COLUMN timeframe ENUM('1h', '1d', '1w', '1m') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback to original ENUM values
        // CRITICAL: This will FAIL if any records have '1w' or '1m'
        // You must delete those records first or this will error
        DB::statement("ALTER TABLE requests MODIFY COLUMN timeframe ENUM('1h', '1d') NOT NULL");
    }
};
