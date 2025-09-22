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
        Schema::create('signals', function (Blueprint $table) {
            $table->id();
            $table->string('stock_code'); // e.g., BBCA.JK
            $table->string('company_name')->nullable();
            $table->enum('timeframe', ['1h', '1d']);

            // Core signal data
            $table->decimal('current_price', 10, 2);
            $table->decimal('entry_price', 10, 2);
            $table->decimal('target_1', 10, 2);
            $table->decimal('target_2', 10, 2);
            $table->decimal('stop_loss', 10, 2);

            // Risk management
            $table->string('risk_reward'); // e.g., "1:2 - 1:3"
            $table->enum('confidence_level', ['Conservative', 'Moderate', 'Aggressive', 'Speculative']);
            $table->tinyInteger('confidence_percentage'); // 50-85%

            // Technical analysis
            $table->text('analysis_reason'); // Technical analysis explanation
            $table->decimal('rsi', 5, 2)->nullable();
            $table->string('macd_signal')->nullable(); // Buy/Sell/Hold
            $table->bigInteger('volume')->nullable();
            $table->decimal('scalping_score', 3, 1)->nullable(); // 0.0 - 10.0

            // Signal status
            $table->enum('status', ['active', 'expired', 'hit_target_1', 'hit_target_2', 'hit_stop_loss'])->default('active');
            $table->timestamp('expires_at');
            $table->timestamp('result_achieved_at')->nullable();
            $table->decimal('highest_price_reached', 10, 2)->nullable();

            $table->timestamps();

            // Indexes for performance
            $table->index(['stock_code', 'timeframe']);
            $table->index(['confidence_level', 'status']);
            $table->index(['expires_at', 'status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signals');
    }
};
