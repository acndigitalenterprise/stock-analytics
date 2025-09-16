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
            $table->enum('result', ['MONITORING', 'WIN', 'SUPER_WIN', 'LOSS', 'TIMEOUT'])->default('MONITORING')->after('advice');
            $table->decimal('entry_price', 10, 2)->nullable()->after('result');
            $table->decimal('target_1', 10, 2)->nullable()->after('entry_price');
            $table->decimal('target_2', 10, 2)->nullable()->after('target_1');
            $table->decimal('stop_loss', 10, 2)->nullable()->after('target_2');
            $table->timestamp('monitoring_until')->nullable()->after('stop_loss');
            $table->decimal('highest_price_reached', 10, 2)->nullable()->after('monitoring_until');
            $table->timestamp('result_achieved_at')->nullable()->after('highest_price_reached');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn([
                'result', 
                'entry_price', 
                'target_1', 
                'target_2', 
                'stop_loss', 
                'monitoring_until',
                'highest_price_reached',
                'result_achieved_at'
            ]);
        });
    }
};
