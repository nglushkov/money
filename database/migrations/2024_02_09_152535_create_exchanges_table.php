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
        Schema::create('exchanges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_currency_id')->references('id')->on('currencies');
            $table->decimal('amount_from', 8, 2)->unsigned();
            $table->foreignId('to_currency_id')->references('id')->on('currencies');
            $table->decimal('amount_to', 8, 2)->unsigned();
            $table->foreignId('bill_id')->references('id')->on('bills');
            $table->date('date');
            $table->foreignId('user_id')->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchanges');
    }
};
