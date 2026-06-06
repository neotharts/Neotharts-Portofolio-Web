<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Convert `starting_price` from IDR (integer) to USD (decimal).
     */
    public function up()
    {
        // Convert values: starting_price = ROUND(starting_price / 16000, 2)
        DB::beginTransaction();
        try {
            // Change column type to decimal first (works on MySQL/MariaDB). If your platform
            // requires doctrine/dbal for Schema::table(...->change()), the raw statement is used.
            DB::statement("ALTER TABLE services MODIFY COLUMN starting_price DECIMAL(10,2) NOT NULL DEFAULT 0");

            // Update existing values from IDR to USD using exchange rate 16000
            DB::statement("UPDATE services SET starting_price = ROUND(starting_price / 16000, 2)");

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     * Convert `starting_price` back to IDR integer.
     */
    public function down()
    {
        DB::beginTransaction();
        try {
            // Convert back to IDR by multiplying and rounding
            DB::statement("UPDATE services SET starting_price = ROUND(starting_price * 16000)");

            // Change column back to bigint / integer
            DB::statement("ALTER TABLE services MODIFY COLUMN starting_price BIGINT NOT NULL DEFAULT 0");

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
};
