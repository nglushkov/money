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
        Schema::create('planned_expenses', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 10, 2);
            $table->smallInteger('day');
            $table->smallInteger('month')->nullable();
            $table->enum('frequency', ['monthly', 'annually']);
            $table->foreignId('currency_id')->constrained();
            $table->foreignId('category_id')->nullable()->constrained();
            $table->foreignId('place_id')->nullable()->constrained();
            $table->foreignId('user_id')->constrained();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planned_expenses');
    }
};
