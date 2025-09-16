<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Database optimization migration
 * 
 * Adds indexes and optimizations for better performance
 * in production environments.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL to add indexes only if they don't exist
        try {
            // Users table indexes
            DB::statement('CREATE INDEX IF NOT EXISTS users_email_role_index ON users (email, role)');
            DB::statement('CREATE INDEX IF NOT EXISTS users_mobile_number_index ON users (mobile_number)');
            DB::statement('CREATE INDEX IF NOT EXISTS users_created_at_index ON users (created_at)');
        } catch (\Exception $e) {
            // Continue if indexes already exist
        }

        try {
            // Requests table indexes
            DB::statement('CREATE INDEX IF NOT EXISTS requests_user_created_index ON requests (user_id, created_at)');
            DB::statement('CREATE INDEX IF NOT EXISTS requests_stock_code_index ON requests (stock_code)');
            DB::statement('CREATE INDEX IF NOT EXISTS requests_email_index ON requests (email)');
            DB::statement('CREATE INDEX IF NOT EXISTS requests_timeframe_index ON requests (timeframe)');
            DB::statement('CREATE INDEX IF NOT EXISTS requests_created_stock_index ON requests (created_at, stock_code)');
            
            // Full-text search for advice (MySQL specific)
            if (Schema::getConnection()->getDriverName() === 'mysql') {
                DB::statement('CREATE FULLTEXT INDEX IF NOT EXISTS requests_advice_fulltext ON requests (advice)');
            }
        } catch (\Exception $e) {
            // Continue if indexes already exist
        }

        try {
            // Jobs table optimization (if exists)
            if (Schema::hasTable('jobs')) {
                DB::statement('CREATE INDEX IF NOT EXISTS jobs_queue_reserved_index ON jobs (queue, reserved_at)');
                DB::statement('CREATE INDEX IF NOT EXISTS jobs_created_at_index ON jobs (created_at)');
            }
        } catch (\Exception $e) {
            // Continue if indexes already exist
        }

        try {
            // Failed jobs table optimization (if exists)
            if (Schema::hasTable('failed_jobs')) {
                DB::statement('CREATE INDEX IF NOT EXISTS failed_jobs_queue_failed_index ON failed_jobs (queue, failed_at)');
            }
        } catch (\Exception $e) {
            // Continue if indexes already exist
        }

        try {
            // Cache table optimization (if exists)
            if (Schema::hasTable('cache')) {
                DB::statement('CREATE INDEX IF NOT EXISTS cache_expiration_index ON cache (expiration)');
            }
        } catch (\Exception $e) {
            // Continue if indexes already exist
        }

        try {
            // Sessions table optimization (if exists)
            if (Schema::hasTable('sessions')) {
                DB::statement('CREATE INDEX IF NOT EXISTS sessions_last_activity_index ON sessions (last_activity)');
                if (Schema::hasColumn('sessions', 'user_id')) {
                    DB::statement('CREATE INDEX IF NOT EXISTS sessions_user_id_index ON sessions (user_id)');
                }
            }
        } catch (\Exception $e) {
            // Continue if indexes already exist
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            // Drop users table indexes
            DB::statement('DROP INDEX IF EXISTS users_email_role_index ON users');
            DB::statement('DROP INDEX IF EXISTS users_mobile_number_index ON users');
            DB::statement('DROP INDEX IF EXISTS users_created_at_index ON users');
        } catch (\Exception $e) {
            // Continue if indexes don't exist
        }

        try {
            // Drop requests table indexes
            DB::statement('DROP INDEX IF EXISTS requests_user_created_index ON requests');
            DB::statement('DROP INDEX IF EXISTS requests_stock_code_index ON requests');
            DB::statement('DROP INDEX IF EXISTS requests_email_index ON requests');
            DB::statement('DROP INDEX IF EXISTS requests_timeframe_index ON requests');
            DB::statement('DROP INDEX IF EXISTS requests_created_stock_index ON requests');
            
            if (Schema::getConnection()->getDriverName() === 'mysql') {
                DB::statement('DROP INDEX IF EXISTS requests_advice_fulltext ON requests');
            }
        } catch (\Exception $e) {
            // Continue if indexes don't exist
        }

        try {
            // Drop jobs table indexes
            if (Schema::hasTable('jobs')) {
                DB::statement('DROP INDEX IF EXISTS jobs_queue_reserved_index ON jobs');
                DB::statement('DROP INDEX IF EXISTS jobs_created_at_index ON jobs');
            }
        } catch (\Exception $e) {
            // Continue if indexes don't exist
        }

        try {
            // Drop failed_jobs table indexes
            if (Schema::hasTable('failed_jobs')) {
                DB::statement('DROP INDEX IF EXISTS failed_jobs_queue_failed_index ON failed_jobs');
            }
        } catch (\Exception $e) {
            // Continue if indexes don't exist
        }

        try {
            // Drop cache table indexes
            if (Schema::hasTable('cache')) {
                DB::statement('DROP INDEX IF EXISTS cache_expiration_index ON cache');
            }
        } catch (\Exception $e) {
            // Continue if indexes don't exist
        }

        try {
            // Drop session table indexes
            if (Schema::hasTable('sessions')) {
                DB::statement('DROP INDEX IF EXISTS sessions_last_activity_index ON sessions');
                if (Schema::hasColumn('sessions', 'user_id')) {
                    DB::statement('DROP INDEX IF EXISTS sessions_user_id_index ON sessions');
                }
            }
        } catch (\Exception $e) {
            // Continue if indexes don't exist
        }
    }
};