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
        Schema::table('external_rates', function (Blueprint $table) {
            $table->decimal('rate', 8, 2)->nullable();
            $table->decimal('buy', 8, 2)->nullable()->change();
            $table->decimal('sell', 8, 2)->nullable()->change();
            $table->unique(['date', 'rate']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('external_rates', function (Blueprint $table) {
            $table->dropColumn('rate');
            $table->decimal('buy', 8, 2)->nullable(false)->change();
            $table->decimal('sell', 8, 2)->nullable(false)->change();
        });
    }
};
