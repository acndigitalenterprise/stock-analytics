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
        Schema::table('requests', function (Blueprint $table) {
            // Add indexes for frequently queried columns
            $table->index('user_id');
            $table->index('timeframe');
            $table->index('created_at');
            $table->index('stock_code');
            $table->index('full_name');
            $table->index('email');
            
            // Composite index for common search combinations
            $table->index(['user_id', 'created_at']);
            $table->index(['timeframe', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            // Remove indexes
            $table->dropIndex(['user_id']);
            $table->dropIndex(['timeframe']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['stock_code']);
            $table->dropIndex(['full_name']);
            $table->dropIndex(['email']);
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex(['timeframe', 'created_at']);
        });
    }
};
