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
        Schema::table('rates', function (Blueprint $table) {
            DB::statement('ALTER TABLE rates RENAME COLUMN from_currency_id TO temp_currency_id');
            DB::statement('ALTER TABLE rates RENAME COLUMN to_currency_id TO from_currency_id');
            DB::statement('ALTER TABLE rates RENAME COLUMN temp_currency_id TO to_currency_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rates', function (Blueprint $table) {
            DB::statement('ALTER TABLE rates RENAME COLUMN to_currency_id TO temp_currency_id');
            DB::statement('ALTER TABLE rates RENAME COLUMN from_currency_id TO to_currency_id');
            DB::statement('ALTER TABLE rates RENAME COLUMN temp_currency_id TO from_currency_id');
        });
    }
};
