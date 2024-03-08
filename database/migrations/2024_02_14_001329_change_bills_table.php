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
        Schema::table('bills', function (Blueprint $table) {
            $table->dropUnique('bills_name_unique'); // Remove unique name constraint
            $table->unique(['name', 'user_id']); // Add unique name constraint for each user
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->dropUnique('bills_name_user_id_unique');
            $table->unique('name');
        });
    }
};
