<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->dropPrimary();
            $table->foreignId('user_id')->after('key')->constrained()->cascadeOnDelete();
            $table->primary(['user_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropPrimary();
            $table->dropColumn('user_id');
            $table->primary('key');
        });
    }
};
