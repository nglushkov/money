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
        Schema::table('bill_currency_initial', function (Blueprint $table) {
            $table->decimal('amount', 36, 18)->change();
        });
        Schema::table('exchanges', function (Blueprint $table) {
            $table->decimal('amount_from', 36, 18)->change();
            $table->decimal('amount_to', 36, 18)->change();
        });
        Schema::table('operations', function (Blueprint $table) {
            $table->decimal('amount', 36, 18)->change();
        });
        Schema::table('transfers', function (Blueprint $table) {
            $table->decimal('amount', 36, 18)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bill_currency_initial', function (Blueprint $table) {
            $table->decimal('amount', 8, 2)->change();
        });
        Schema::table('exchanges', function (Blueprint $table) {
            $table->decimal('amount_from', 10, 2)->change();
            $table->decimal('amount_to', 10, 2)->change();
        });
        Schema::table('operations', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->change();
        });
        Schema::table('transfers', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->change();
        });
    }
};
