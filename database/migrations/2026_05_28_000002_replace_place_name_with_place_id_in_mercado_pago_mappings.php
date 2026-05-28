<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mercado_pago_mappings', function (Blueprint $table) {
            $table->foreignId('place_id')->nullable()->after('category_id')->constrained('places')->nullOnDelete();
            $table->dropColumn('place_name');
        });
    }

    public function down(): void
    {
        Schema::table('mercado_pago_mappings', function (Blueprint $table) {
            $table->string('place_name')->nullable()->after('category_id');
            $table->dropForeign(['place_id']);
            $table->dropColumn('place_id');
        });
    }
};
