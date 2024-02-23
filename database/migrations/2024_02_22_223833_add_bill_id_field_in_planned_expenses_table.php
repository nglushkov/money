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
        Schema::table('planned_expenses', function (Blueprint $table) {
            $table->foreignId('bill_id')->nullable()->constrained('bills');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('planned_expenses', function (Blueprint $table) {
            $table->dropColumn('bill_id');
        });
    }
};
