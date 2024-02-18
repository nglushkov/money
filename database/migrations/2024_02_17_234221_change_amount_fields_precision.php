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
        Schema::table('operations', function (Blueprint $table) {
            $table->decimal('amount', 10)->change();
        });
        Schema::table('transfers', function (Blueprint $table) {
            $table->decimal('amount', 10)->change();
        });
        Schema::table('exchanges', function (Blueprint $table) {
            $table->decimal('amount_from', 10)->change();
            $table->decimal('amount_to', 10)->change();
        });
        Schema::table('rates', function (Blueprint $table) {
            $table->decimal('rate', 10)->change();
        });
        Schema::table('external_rates', function (Blueprint $table) {
            $table->decimal('rate', 10)->change()->nullable();
            $table->decimal('buy', 10)->change()->nullable();
            $table->decimal('sell', 10)->change()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operations', function (Blueprint $table) {
            $table->decimal('amount', 8, 2)->change();
        });
        Schema::table('transfers', function (Blueprint $table) {
            $table->decimal('amount', 8, 2)->change();
        });
        Schema::table('exchanges', function (Blueprint $table) {
            $table->decimal('amount_from', 8, 2)->change();
            $table->decimal('amount_to', 8, 2)->change();
        });
        Schema::table('rates', function (Blueprint $table) {
            $table->decimal('rate', 8, 2)->change();
        });
        Schema::table('external_rates', function (Blueprint $table) {
            $table->decimal('rate', 8, 2)->change()->nullable();
            $table->decimal('buy', 8, 2)->change()->nullable();
            $table->decimal('sell', 8, 2)->change()->nullable();
        });
    }
};
