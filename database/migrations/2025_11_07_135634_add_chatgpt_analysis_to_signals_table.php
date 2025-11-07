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
        Schema::table('signals', function (Blueprint $table) {
            // Add ChatGPT analysis columns after analysis_reason
            $table->text('chatgpt_reason')->nullable()->after('analysis_reason');
            $table->integer('chatgpt_confidence_percentage')->nullable()->after('chatgpt_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('signals', function (Blueprint $table) {
            $table->dropColumn(['chatgpt_reason', 'chatgpt_confidence_percentage']);
        });
    }
};
