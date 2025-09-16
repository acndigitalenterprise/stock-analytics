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
        // Add foreign key constraint with CASCADE DELETE for performance
        Schema::table('requests', function (Blueprint $table) {
            // Add foreign key constraint to user_id column
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade') // Automatic cascade delete for performance
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
    }
};