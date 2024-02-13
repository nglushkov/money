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
        Schema::create('operations', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 8, 2)->unsigned();
            $table->tinyInteger('type'); // 0 - expense, 1 - income
            $table->foreignId('bill_id')->constrained('bills');
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('currency_id')->constrained('currencies');
            $table->foreignId('place_id')->constrained('places');
            $table->foreignId('user_id')->constrained('users');
            $table->text('notes')->nullable();
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operations');
    }
};
