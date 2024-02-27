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
            $table->enum('type2', ['Expense', 'Income'])->nullable();
        });
        DB::statement("UPDATE operations SET type2 = 'Expense' WHERE type = 0");
        DB::statement("UPDATE operations SET type2 = 'Income' WHERE type = 1");
        Schema::table('operations', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->renameColumn('type2', 'type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operations', function (Blueprint $table) {
            $table->tinyInteger('type2')->nullable();
        });
        DB::statement("UPDATE operations SET type2 = 0 WHERE type = 'Expense'");
        DB::statement("UPDATE operations SET type2 = 1 WHERE type = 'Income'");
        Schema::table('operations', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->renameColumn('type2', 'type');
        });
    }
};
